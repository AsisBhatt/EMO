<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * GuestController
 *
 * Handles the actions for guest related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class GuestController extends Controller
{
    public $layout = 'guest';

    public function init()
    {
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('guest.js')));
        parent::init();
    }

    /**
     * Display the login form
     */
    public function actionIndex()
    {
        $model   = new CustomerLogin();
        $request = Yii::app()->request;
        $options = Yii::app()->options;

        if (GuestFailAttempt::model()->setBaseInfo()->hasTooManyFailures) {
            throw new CHttpException(403, Yii::t('app', 'Your access to this resource is forbidden.'));
        }

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($model->modelName, array()))) {
            $model->attributes = $attributes;

            // since 1.3.6.2
            Yii::app()->hooks->doAction('customer_controller_guest_form_submit_start', $collection = new CAttributeCollection(array(
                'model'      => $model,
                'controller' => $this,
                'error'      => null,
            )));
            if ($collection->error) {
                Yii::app()->notify->addError($collection->error);
            }
            // 
            
            if (!$collection->error && $model->validate()) {
                // since 1.3.6.2
                CustomerLoginLog::addNew(Yii::app()->customer->getModel());
                
                $this->redirect(Yii::app()->customer->returnUrl);
            }
            GuestFailAttempt::registerByPlace('Customer login');
        }

        $registrationEnabled = $options->get('system.customer_registration.enabled', 'no') == 'yes';
        $facebookEnabled     = $options->get('system.customer_registration.facebook_enabled', 'no') == 'yes';
        $twitterEnabled      = $options->get('system.customer_registration.twitter_enabled', 'no') == 'yes';
        
        // twitter library requires php >= 5.5
        if ($twitterEnabled && version_compare(PHP_VERSION, '5.5.0', '<')) {
            $twitterEnabled = false;
        }
        
        $this->setData(array(
            'pageMetaTitle' => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Please login'),
            'pageHeading'   => Yii::t('customers', 'Please login'),
        ));

        $this->render('login', compact('model', 'registrationEnabled', 'facebookEnabled', 'twitterEnabled'));
    }

    /**
     * Display the registration form
     */
    public function actionRegister()
    {
        $options = Yii::app()->options;
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;
        $model   = new Customer('register');
        $company = new CustomerCompany('register');

        if ($options->get('system.customer_registration.enabled', 'no') != 'yes') {
            $this->redirect(array('guest/index'));
        }

        if (GuestFailAttempt::model()->setBaseInfo()->hasTooManyFailures) {
            throw new CHttpException(403, Yii::t('app', 'Your access to this resource is forbidden.'));
        }

        $facebookEnabled    = $options->get('system.customer_registration.facebook_enabled', 'no') == 'yes';
        $twitterEnabled     = $options->get('system.customer_registration.twitter_enabled', 'no') == 'yes';
        $companyRequired    = $options->get('system.customer_registration.company_required', 'no') == 'yes';
        
        // 1.3.6.3
        $mustConfirmEmail   = $options->get('system.customer_registration.require_email_confirmation', 'yes') == 'yes';
        $requireApproval    = $options->get('system.customer_registration.require_approval', 'no') == 'yes';
        $defaultCountry     = $options->get('system.customer_registration.default_country', '');
        $defaultTimezone    = $options->get('system.customer_registration.default_timezone', '');
        
        if (!empty($defaultCountry)) {
            $company->country_id = $defaultCountry;
        }
        
        if (!empty($defaultTimezone)) {
            $model->timezone = $defaultTimezone;
        }
        // 
        
        if ($request->isPostRequest && ($attributes = (array)$request->getPost($model->modelName, array()))) {
            $model->attributes = $attributes;
            $model->status     = Customer::STATUS_PENDING_CONFIRM;

            // since 1.3.6.2
            Yii::app()->hooks->addAction('customer_controller_guest_form_submit_start', array($this, '_checkEmailDomainForbidden'), 100);
            Yii::app()->hooks->doAction('customer_controller_guest_form_submit_start', $collection = new CAttributeCollection(array(
                'model'      => $model,
                'controller' => $this,
                'error'      => null,
            )));
            if ($collection->error) {
                $notify->addError($collection->error);
            }
            //
            
            if (!$collection->error) {
                $transaction = Yii::app()->getDb()->beginTransaction();

                try {
                    if ($model->hasErrors() || !$model->save()) {
                        throw new Exception(CHtml::errorSummary($model));
                    }
                    if ($companyRequired) {
                        $company->attributes  = (array)$request->getPost($company->modelName, array());
                        $company->customer_id = $model->customer_id;
                        if (!$company->save()) {
                            throw new Exception(CHtml::errorSummary($company));
                        }
                    }
                    
                    // 1.3.6.3
					//print_r($mustConfirmEmail);exit;
                    if ($mustConfirmEmail) {
                        $this->_sendRegistrationConfirmationEmail($model, $company);
                    }
                    
                    $this->_sendNewCustomerNotifications($model, $company);
                    
                    // 1.3.7
                    $this->_subscribeToEmailList($model);
                    
                    // 1.3.6.3
                    if (!$mustConfirmEmail) {
                        $transaction->commit();
                        $this->redirect(array('guest/confirm_registration', 'key' => $model->confirmation_key));
                    }
                    
                    if ($notify->isEmpty) {
                        if ($mustConfirmEmail) {
                            $notify->addSuccess(Yii::t('customers', 'Congratulations, your account has been created, please check your email address for confirmation!'));
                        } else {
                            $notify->addSuccess(Yii::t('customers', 'Congratulations, your account has been created, you can login now!'));
                        }
                    }
                    $transaction->commit();
                    $this->redirect(array('guest/index'));
                } catch (Exception $e) {
                    $transaction->rollBack();
                    GuestFailAttempt::registerByPlace('Customer register');
                }    
            }
        }

        $this->setData(array(
            'pageMetaTitle' => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Please register'),
            'pageHeading'   => Yii::t('customers', 'Please register'),
        ));

        $this->render('register', compact('model', 'company', 'companyRequired', 'facebookEnabled', 'twitterEnabled'));
    }

    public function actionConfirm_registration($key)
    {
        $options = Yii::app()->options;
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $model = Customer::model()->findByAttributes(array(
            'confirmation_key' => $key,
            'status'           => Customer::STATUS_PENDING_CONFIRM,
        ));
		
		/*if(empty($model->company)){
			$model->company = new CustomerCompany();
		}
		$company = $model->company;*/

        if (empty($model)) {
            $this->redirect(array('guest/index'));
        }

        if (($defaultGroup = (int)$options->get('system.customer_registration.default_group')) > 0) {
            $group = CustomerGroup::model()->findByPk((int)$defaultGroup);
            if (!empty($group)) {
                $model->group_id = $group->group_id;
            }
        }
		
		if (($defaultPlan = (int)$options->get('system.customer_registration.default_plan')) > 0) {
            $plan = PricePlan::model()->findByPk((int)$defaultPlan);
            if (!empty($plan)) {
                $model->plan_id = $plan->plan_id;
            }
        }
		
		if($model->customer_id != ''){
			$command = Yii::app()->db->createCommand();
			$command->insert('uic_customer_company', array(
				'customer_id' => $model->customer_id,
				'flowroute_sms_num' => '18882642564',
			));
			
			$list = new Lists();
			$list->customer_id = $model->customer_id;
			
			$listDefault = new ListDefault();
			$listCompany = new ListCompany();
			$listCustomerNotification = new ListCustomerNotification();
			
			// since 1.3.5 - this should be expanded in future
			$listSubscriberAction      = new ListSubscriberAction();
			$subscriberActionLists     = $list->findAllForSubscriberActions();
			$selectedSubscriberActions = array(ListSubscriberAction::ACTION_SUBSCRIBE => array(), ListSubscriberAction::ACTION_UNSUBSCRIBE => array());
			
			// to create the default mail list fields.
			$list->attachBehavior('listDefaultFields', array(
				'class' => 'customer.components.db.behaviors.ListDefaultFieldsBehavior',
			));
			
			if (!empty($model->company)) {
				$listCompany->mergeWithCustomerCompany($model->company);
			}
			
			$listDefault->mergeWithCustomerInfo($model);
			
			$list_models = array($list, $listCompany, $listCustomerNotification, $listDefault);
			
			foreach ($list_models as $list_model) {
				if($list_model->modelName == 'Lists'){
					$list_model->name = 'Default SMS List';
					$list_model->display_name = 'Default SMS List';
					$list_model->description = 'Default SMS List';
					$list_model->opt_in = 'double';
					$list_model->opt_out = 'single';
					$list_model->welcome_email = 'no';
					$list_model->subscriber_require_approval = 'no';
				}elseif($list_model->modelName == 'ListCompany'){
					$list_model->name = 'Trial User';
					$list_model->country_id = 1;
					$list_model->address_1 = 'address_1';
					$list_model->city = 'city';
					$list_model->zip_code = '123456';
					$list_model->address_format = '[COMPANY_NAME][COMPANY_ADDRESS_1] [COMPANY_ADDRESS_2][COMPANY_CITY] [COMPANY_ZONE] [COMPANY_ZIP][COMPANY_COUNTRY][COMPANY_WEBSITE]';
				}elseif($list_model->modelName == 'ListCustomerNotification'){
					$list_model->subscribe = 'no';
					$list_model->unsubscribe = 'no';
				}elseif($list_model->modelName == 'ListDefault'){
					$list_model->from_name = $model->getFullName();
					$list_model->from_email = $model->email;
					$list_model->reply_to = $model->email;
				}
				
            }
			foreach ($list_models as $list_model) {
				if (!($list_model instanceof Lists)) {
					$list_model->list_id = $list->list_id;
				}
				$list_model->save(false);
            }

			/*if ($logAction = Yii::app()->customer->getModel()->asa('logAction')) {
				$logAction->listCreated($list);
			}*/
				
		}
		
        $requireApproval = $options->get('system.customer_registration.require_approval', 'no') == 'yes';
        $model->status   = !$requireApproval ? Customer::STATUS_ACTIVE : Customer::STATUS_PENDING_ACTIVE;

        if (!$model->save(false)) {
            $this->redirect(array('guest/index'));
        }

        if ($requireApproval) {
            $notify->addSuccess(Yii::t('customers', 'Congratulations, you have successfully confirmed your account.'));
            $notify->addSuccess(Yii::t('customers', 'You will be able to login once an administrator will approve it.'));
            $this->redirect(array('guest/index'));
        }

        // send welcome email if needed
        $sendWelcome        = $options->get('system.customer_registration.welcome_email', 'no') == 'yes';
        $sendWelcomeSubject = $options->get('system.customer_registration.welcome_email_subject', '');
        $sendWelcomeContent = $options->get('system.customer_registration.welcome_email_content', '');
        if (!empty($sendWelcome) && !empty($sendWelcomeSubject) && !empty($sendWelcomeContent)) {
            $searchReplace = array(
                '[FIRST_NAME]' => $model->first_name,
                '[LAST_NAME]'  => $model->last_name,
                '[FULL_NAME]'  => $model->fullName,
                '[EMAIL]'      => $model->email,
            );
            $sendWelcomeSubject = str_replace(array_keys($searchReplace), array_values($searchReplace), $sendWelcomeSubject);
            $sendWelcomeContent = str_replace(array_keys($searchReplace), array_values($searchReplace), $sendWelcomeContent);
            $emailTemplate = $options->get('system.email_templates.common');
            $emailTemplate = str_replace('[CONTENT]', $sendWelcomeContent, $emailTemplate);

            $email = new TransactionalEmail();
            $email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
            $email->to_name      = $model->getFullName();
            $email->to_email     = $model->email;
            $email->from_name    = $options->get('system.common.site_name', 'Marketing website');
            $email->subject      = $sendWelcomeSubject;
            $email->body         = $emailTemplate;
            $email->save();
        }

        $identity = new CustomerIdentity($model->email, $model->password);
        $identity->setId($model->customer_id)->setAutoLoginToken($model);

        if (!Yii::app()->customer->login($identity, 3600 * 24 * 30)) {
            $this->redirect(array('guest/index'));
        }

        $notify->addSuccess(Yii::t('customers', 'Congratulations, your account is now ready to use.'));
        $notify->addSuccess(Yii::t('customers', 'Please start by filling your account and company info.'));
        $this->redirect(array('account/index'));
    }

    /**
     * Display the "Forgot password" form
     */
    public function actionForgot_password()
    {
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;
        $model   = new CustomerPasswordReset();

        if (GuestFailAttempt::model()->setBaseInfo()->hasTooManyFailures) {
            throw new CHttpException(403, Yii::t('app', 'Your access to this resource is forbidden.'));
        }

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($model->modelName, array()))) {
            $model->attributes = $attributes;
            
            // since 1.3.6.2
            Yii::app()->hooks->doAction('customer_controller_guest_form_submit_start', $collection = new CAttributeCollection(array(
                'model'      => $model,
                'controller' => $this,
                'error'      => null,
            )));
            
            if ($collection->error || !$model->validate()) {
                $notify->addError(Yii::t('app', 'Please fix your form errors!'));
                if ($collection->error) {
                    $notify->addError($collection->error);
                }
                GuestFailAttempt::registerByPlace('Customer forgot password');
            } else {
                $options = Yii::app()->options;
                $customer = Customer::model()->findByAttributes(array('email' => $model->email));
                $model->customer_id = $customer->customer_id;
                $model->save(false);

                $emailTemplate    = $options->get('system.email_templates.common');
                $emailBody        = $this->renderPartial('_email-reset-key', compact('model', 'customer'), true);
                $emailTemplate    = str_replace('[CONTENT]', $emailBody, $emailTemplate);

                $email = new TransactionalEmail();
                $email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
                $email->to_name      = $customer->getFullName();
                $email->to_email     = $customer->email;
                $email->from_name    = $options->get('system.common.site_name', 'Marketing website');
                $email->subject      = Yii::t('customers', 'Password reset request!');
                $email->body         = $emailTemplate;
                $email->save();

                $notify->addSuccess(Yii::t('app', 'Please check your email address.'));
                $model->unsetAttributes();
                $model->email = null;
            }
        }

        $this->setData(array(
            'pageMetaTitle' => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Retrieve a new password for your account.'),
            'pageHeading'   => Yii::t('customers', 'Forgot password?'),
        ));

        $this->render('forgot_password', compact('model'));
    }

    /**
     * Reached from email, will reset the password for given user and send a new one via email.
     */
    public function actionReset_password($reset_key)
    {
        $model = CustomerPasswordReset::model()->findByAttributes(array(
            'reset_key' => $reset_key,
            'status'    => CustomerPasswordReset::STATUS_ACTIVE,
        ));

        if (empty($model)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $randPassword = StringHelper::random();
        $hashedPassword = Yii::app()->passwordHasher->hash($randPassword);

        Customer::model()->updateByPk((int)$model->customer_id, array('password' => $hashedPassword));
        $model->status = CustomerPasswordReset::STATUS_USED;
        $model->save();

        $options    = Yii::app()->options;
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $customer   = Customer::model()->findByPk($model->customer_id);
        $currentPassword = $customer->password;

        $emailTemplate  = $options->get('system.email_templates.common');
        $emailBody      = $this->renderPartial('_email-new-login', compact('model', 'customer', 'randPassword'), true);
        $emailTemplate  = str_replace('[CONTENT]', $emailBody, $emailTemplate);

        $email = new TransactionalEmail();
        $email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
        $email->to_name      = $customer->getFullName();
        $email->to_email     = $customer->email;
        $email->from_name    = $options->get('system.common.site_name', 'Marketing website');
        $email->subject      = Yii::t('app', 'Your new login info!');
        $email->body         = $emailTemplate;
        $email->save();

        $notify->addSuccess(Yii::t('app', 'Your new login has been successfully sent to your email address.'));
        $this->redirect(array('guest/index'));
    }

    public function actionFacebook()
    {
        Yii::import('common.vendors.Facebook.*');

        $options = Yii::app()->options;

        if ($options->get('system.customer_registration.enabled', 'no') != 'yes') {
            $this->redirect(array('guest/index'));
        }

        if ($options->get('system.customer_registration.facebook_enabled', 'no') != 'yes') {
            $this->redirect(array('guest/index'));
        }

        $appID     = $options->get('system.customer_registration.facebook_app_id');
        $appSecret = $options->get('system.customer_registration.facebook_app_secret');

        if (strlen($appID) < 15 || strlen($appSecret) < 32) {
            $this->redirect(array('guest/index'));
        }

        $notify   = Yii::app()->notify;
        $request  = Yii::app()->request;
        $facebook = new Facebook(array(
            'appId'  => $appID,
            'secret' => $appSecret,
        ));

        // let's see if the user is logged into facebook and he has approved our app.
        try {
            $uid  = $facebook->getUser();
            $user = $facebook->api('/me');
        }
        catch(Exception $e){}

        // the user needs to approve our application.
        if(empty($user)) {
            $this->redirect($facebook->getLoginUrl(array(
                'scope'         => 'email',
                'redirect_uri'  => $this->createAbsoluteUrl('guest/facebook'),
                'display'       => 'page',
            )));
        }

        // if we are here, means the customer approved the app
        // create the default attributes.
        $attributes = array(
            'oauth_uid'     => !empty($user['id']) ? $user['id'] : null,
            'oauth_provider'=> 'facebook',
            'first_name'    => !empty($user['first_name']) ? $user['first_name'] : null,
            'last_name'     => !empty($user['last_name']) ? $user['last_name'] : null,
            'email'         => !empty($user['email']) ? $user['email'] : null,
        );
        $attributes = Yii::app()->ioFilter->stripClean($attributes);

        // DO NOT $customer->attributes = $attributes because most of them will not be assigned
        $customer = new Customer();
        foreach($attributes AS $key => $value) {
            if (empty($value)) {
                $notify->addError(Yii::t('customers', 'Unable to retrieve all your account data!'));
                $this->redirect(array('guest/index'));
            }
            $customer->setAttribute($key, $value);
        }

        $exists = Customer::model()->findByAttributes(array(
            'oauth_uid'         => $customer->oauth_uid,
            'oauth_provider'    => 'facebook',
        ));

        if(!empty($exists)) {
            if ($exists->status == Customer::STATUS_ACTIVE) {
                $identity = new CustomerIdentity($exists->email, $exists->password);
                $identity->setId($exists->customer_id)->setAutoLoginToken($exists);
                Yii::app()->customer->login($identity, 3600 * 24 * 30);
                $this->redirect(array('dashboard/index'));
            }
            $notify->addError(Yii::t('customers', 'Your account is not active!'));
            $this->redirect(array('guest/index'));
        }

        // if another customer with same email address, do nothing
        $exists = Customer::model()->findByAttributes(array('email' => $customer->email));
        if (!empty($exists)) {
            $notify->addError(Yii::t('customers', 'There is another account using this email address, please fill in the form to recover your password!'));
            $this->redirect(array('guest/forgot_password'));
        }

        $requireApproval         = $options->get('system.customer_registration.require_approval', 'no') == 'yes';
        $randPassword            = StringHelper::random(8);
        $customer->fake_password = $randPassword;
        $customer->status        = !$requireApproval ? Customer::STATUS_ACTIVE : Customer::STATUS_PENDING_ACTIVE;
        $customer->avatar        = $this->fetchCustomerRemoteImage('https://graph.facebook.com/'.$customer->oauth_uid.'/picture?height=400&type=large&width=400');

        if (($defaultGroup = (int)$options->get('system.customer_registration.default_group')) > 0) {
            $group = CustomerGroup::model()->findByPk((int)$defaultGroup);
            if (!empty($group)) {
                $customer->group_id = $group->group_id;
            }
        }

        // finally try to save the customer.
        if(!$customer->save(false)) {
            $notify->addError(Yii::t('customers', 'Unable to save your account, please contact us if this error persists!'));
            $this->redirect(array('guest/index'));
        }

        // create the email for customer
        $emailTemplate  = $options->get('system.email_templates.common');
        $emailBody      = $this->renderPartial('_email-new-login', compact('customer', 'randPassword'), true);
        $emailTemplate  = str_replace('[CONTENT]', $emailBody, $emailTemplate);

        $email = new TransactionalEmail();
        $email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
        $email->to_name      = $customer->getFullName();
        $email->to_email     = $customer->email;
        $email->from_name    = $options->get('system.common.site_name', 'Marketing website');
        $email->subject      = Yii::t('app', 'Your new login info!');
        $email->body         = $emailTemplate;
        $email->save();

        // notify admins
        $this->_sendNewCustomerNotifications($customer, new CustomerCompany());

        // 1.3.7
        $this->_subscribeToEmailList($customer);
        
        if ($requireApproval) {
            $notify->addSuccess(Yii::t('customers', 'Congratulations, your account has been successfully created.'));
            $notify->addSuccess(Yii::t('customers', 'You will be able to login once an administrator will approve it.'));
            $this->redirect(array('guest/index'));
        }

        // the customer has been saved, we need to log him in, should work okay...
        $identity = new CustomerIdentity($customer->email, $customer->password);
        $identity->setId($customer->customer_id)->setAutoLoginToken($customer);
        Yii::app()->customer->login($identity, 3600 * 24 * 30);
        $this->redirect(array('dashboard/index'));
    }

    public function actionTwitter()
    {
        // the twitter library works only with php >= 5.5
        if (version_compare(PHP_VERSION, '5.5', '<')) {
            $this->redirect(array('guest/index'));
        }
        
        $options = Yii::app()->options;
        
        // avoid parsing errors on php < 5.3
        $twitterLibClassName = '\Abraham\TwitterOAuth\TwitterOAuth';
        
        if ($options->get('system.customer_registration.enabled', 'no') != 'yes') {
            $this->redirect(array('guest/index'));
        }

        if ($options->get('system.customer_registration.twitter_enabled', 'no') != 'yes') {
            $this->redirect(array('guest/index'));
        }

        $appConsumerKey     = $options->get('system.customer_registration.twitter_app_consumer_key');
        $appConsumerSecret  = $options->get('system.customer_registration.twitter_app_consumer_secret');
        $requireApproval    = $options->get('system.customer_registration.require_approval', 'no') == 'yes';

        if (strlen($appConsumerKey) < 20 || strlen($appConsumerSecret) < 40) {
            $this->redirect(array('guest/index'));
        }

        $notify   = Yii::app()->notify;
        $request  = Yii::app()->request;
        $session  = Yii::app()->session;
        
        // only if not done already.
        if (!isset($session['access_token'])) {
            // when the app is not approved.
            if ($request->getQuery('do') != 'get-request-token') {
                $twitterOauth = new $twitterLibClassName($appConsumerKey, $appConsumerSecret);
                $requestToken = $twitterOauth->oauth('oauth/request_token', array('oauth_callback' => $this->createAbsoluteUrl('guest/twitter',array('do'=>'get-request-token'))));

                if(empty($requestToken)) {
                    $this->redirect(array('guest/index'));
                }

                $session['oauth_token']        = $requestToken['oauth_token'];
                $session['oauth_token_secret'] = $requestToken['oauth_token_secret'];

                $this->redirect($twitterOauth->url('oauth/authorize', array('oauth_token' => $requestToken['oauth_token'])));
            }
            
            //when the request is made...
            if (!$request->getQuery('oauth_verifier') || empty($session['oauth_token']) || empty($session['oauth_token_secret'])) {
                $this->redirect(array('guest/index'));
            }

            $twitterOauth = new $twitterLibClassName($appConsumerKey, $appConsumerSecret, $session['oauth_token'], $session['oauth_token_secret']);
            $accessToken  = $twitterOauth->oauth("oauth/access_token", array("oauth_verifier" => $request->getQuery('oauth_verifier')));

            if (empty($accessToken)) {
                $this->redirect(array('guest/index'));
            }

            $session['access_token'] = $accessToken;
        }

        $accessToken = $session['access_token'];
        $twitterOauth = new $twitterLibClassName($appConsumerKey, $appConsumerSecret, $accessToken['oauth_token'], $accessToken['oauth_token_secret']);
        $_user = $twitterOauth->get('account/verify_credentials');
        
        if (empty($_user) || !empty($_user->errors)) {
            $this->redirect(array('guest/index'));
        }
        
        $firstName = $lastName = trim($_user->name);
        if (strpos($_user->name, ' ') !== false) {
            $names = explode(' ', $_user->name);
            if (count($names) >= 2) {
                $firstName = array_shift($names);
                $lastName  = implode(' ', $names);
            }
        }

        $attributes = array(
            'oauth_uid'      => !empty($_user->id) ? $_user->id : null,
            'oauth_provider' => 'twitter',
            'first_name'     => $firstName,
            'last_name'      => $lastName,
        );
        $attributes = Yii::app()->ioFilter->stripClean($attributes);

        $customer = new Customer();
        foreach($attributes AS $key => $value) {
            if (empty($value)) {
                $notify->addError(Yii::t('customers', 'Unable to retrieve all your account data!'));
                $this->redirect(array('guest/index'));
            }
            $customer->setAttribute($key, $value);
        }

        $exists = Customer::model()->findByAttributes(array(
            'oauth_uid'         => $customer->oauth_uid,
            'oauth_provider'    => 'twitter',
        ));

        if(!empty($exists)) {
            if ($exists->status == Customer::STATUS_ACTIVE) {
                $identity = new CustomerIdentity($exists->email, $exists->password);
                $identity->setId($exists->customer_id)->setAutoLoginToken($exists);
                Yii::app()->customer->login($identity, 3600 * 24 * 30);
                $this->redirect(array('dashboard/index'));
            }
            $notify->addError(Yii::t('customers', 'Your account is not active!'));
            $this->redirect(array('guest/index'));
        }

        if (!$request->isPostRequest) {
            $this->setData('pageHeading', Yii::t('customers', 'Enter your email address'));
            return $this->render('twitter-email', compact('customer'));
        }

        if (($attributes = $request->getPost($customer->modelName, array()))) {
            $customer->email = isset($attributes['email']) ? $attributes['email'] : null;
        }

        if (!FilterVarHelper::email($customer->email)) {
            $notify->addError(Yii::t('customers', 'Invalid email address provided!'));
            $this->setData('pageHeading', Yii::t('customers', 'Enter your email address'));
            return $this->render('twitter-email', compact('customer'));
        }

        // if another customer with same email address, do nothing
        $exists = Customer::model()->findByAttributes(array('email' => $customer->email));
        if (!empty($exists)) {
            $notify->addError(Yii::t('customers', 'There is another account using this email address, please fill in the form to recover your password!'));
            $this->redirect(array('guest/forgot_password'));
        }

        // create a random 8 chars password for the customer, and assign the active status.
        $randPassword            = StringHelper::random(8);
        $customer->fake_password = $randPassword;
        $customer->status        = !$requireApproval ? Customer::STATUS_ACTIVE : Customer::STATUS_PENDING_ACTIVE;
        $customer->avatar        = $this->fetchCustomerRemoteImage($_user->profile_image_url);

        if (($defaultGroup = (int)$options->get('system.customer_registration.default_group')) > 0) {
            $group = CustomerGroup::model()->findByPk((int)$defaultGroup);
            if (!empty($group)) {
                $customer->group_id = $group->group_id;
            }
        }

        // finally try to save the customer.
        if(!$customer->save(false)) {
            $notify->addError(Yii::t('customers', 'Unable to save your account, please contact us if this error persists!'));
            $this->redirect(array('guest/index'));
        }

        // create the email for customer
        $emailTemplate  = $options->get('system.email_templates.common');
        $emailBody      = $this->renderPartial('_email-new-login', compact('customer', 'randPassword'), true);
        $emailTemplate  = str_replace('[CONTENT]', $emailBody, $emailTemplate);

        $email = new TransactionalEmail();
        $email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
        $email->to_name      = $customer->getFullName();
        $email->to_email     = $customer->email;
        $email->from_name    = $options->get('system.common.site_name', 'Marketing website');
        $email->subject      = Yii::t('app', 'Your new login info!');
        $email->body         = $emailTemplate;
        $email->save();

        // notify admins
        $this->_sendNewCustomerNotifications($customer, new CustomerCompany());

        // 1.3.7
        $this->_subscribeToEmailList($customer);
        
        if ($requireApproval) {
            $notify->addSuccess(Yii::t('customers', 'Congratulations, your account has been successfully created.'));
            $notify->addSuccess(Yii::t('customers', 'You will be able to login once an administrator will approve it.'));
            $this->redirect(array('guest/index'));
        }

        // the customer has been saved, we need to log him in, should work okay...
        $identity = new CustomerIdentity($customer->email, $customer->password);
        $identity->setId($customer->customer_id)->setAutoLoginToken($customer);
        Yii::app()->customer->login($identity, 3600 * 24 * 30);
        $this->redirect(array('dashboard/index'));
    }

    /**
     * Display country zones
     */
    public function actionZones_by_country()
    {
        $request = Yii::app()->request;
        if (!$request->isAjaxRequest) {
            $this->redirect(array('guest/index'));
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'zone_id, name';
        $criteria->compare('country_id', (int)$request->getQuery('country_id'));
        $models = Zone::model()->findAll($criteria);

        $zones = array();
        foreach ($models as $model) {
            $zones[] = array(
                'zone_id'  => $model->zone_id,
                'name'     => $model->name
            );
        }
        return $this->renderJson(array('zones' => $zones));
    }

    /**
     * Callback for customer_controller_guest_form_submit_start action hook
     * @param $collection
     */
    public function _checkEmailDomainForbidden($collection)
    {
        if ($collection->error) {
            return;
        }
        
        $email = $collection->model->email;
        if (empty($email) || !FilterVarHelper::email($email)) {
            return;
        }
        
        $options = Yii::app()->options;
        if (!($domains = $options->get('system.customer_registration.forbidden_domains', ''))) {
            return;
        }
        
        $domains = explode(',', $domains);
        $domains = array_map('strtolower', array_map('trim', $domains));
        $domains = array_unique($domains);
        
        $emailDomain = explode('@', $email);
        $emailDomain = strtolower($emailDomain[1]);
        
        foreach ($domains as $domain) {
            if (strpos($emailDomain, $domain) === 0) {
                $collection->error = Yii::t('customers', 'We\'re sorry, but we don\'t accept registrations from {domain}. <br />Please use another email address, preferably not from a free service!', array(
                    '{domain}' => $emailDomain,
                ));
                break;
            }
        }
    }

    /**
     * Callback after success registration to send the confirmation email
     */
    protected function _sendRegistrationConfirmationEmail(Customer $customer, CustomerCompany $company)
    {
        $options  = Yii::app()->options;
        $notify   = Yii::app()->notify;

        if ($options->get('system.customer_registration.company_required', 'no') == 'yes' && $company->isNewRecord) {
            return;
        }

        $emailTemplate  = $options->get('system.email_templates.common');
        $emailBody      = $this->renderPartial('_email-registration-key', compact('customer'), true);
        $emailTemplate  = str_replace('[CONTENT]', $emailBody, $emailTemplate);

        $email = new TransactionalEmail();
        $email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
        $email->to_name      = $customer->getFullName();
        $email->to_email     = $customer->email;
        $email->from_name    = $options->get('system.common.site_name', 'Marketing website');
        $email->subject      = Yii::t('customers', 'Please confirm your account!');
        $email->body         = $emailTemplate;
        $email->save();
    }

    /**
     * Callback after success registration to send the notification emails to admin users
     */
    protected function _sendNewCustomerNotifications(Customer $customer, CustomerCompany $company)
    {
        $options    = Yii::app()->options;
        $notify     = Yii::app()->notify;
        $recipients = $options->get('system.customer_registration.new_customer_registration_notification_to');

        if (empty($recipients)) {
            return;
        }

        $recipients = explode(',', $recipients);
        $recipients = array_map('trim', $recipients);

        $emailTemplate  = $options->get('system.email_templates.common');
        $emailBody      = $this->renderPartial('_email-new-customer-notification', compact('customer', 'options'), true);
        $emailTemplate  = str_replace('[CONTENT]', $emailBody, $emailTemplate);

        foreach ($recipients as $recipient) {
            if (!FilterVarHelper::email($recipient)) {
                continue;
            }
            $email = new TransactionalEmail();
            $email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
            $email->to_name      = $recipient;
            $email->to_email     = $recipient;
            $email->from_name    = $options->get('system.common.site_name', 'Marketing website');
            $email->subject      = Yii::t('customers', 'New customer registration!');
            $email->body         = $emailTemplate;
            $email->save();
        }
    }

    protected function fetchCustomerRemoteImage($url)
    {
        if (empty($url)) {
            return null;
        }

        $imageRequest = AppInitHelper::simpleCurlGet($url);
        if ($imageRequest['status'] != 'success' || empty($imageRequest['message'])) {
            return null;
        }

        $storagePath = Yii::getPathOfAlias('root.frontend.assets.files.avatars');
        if (!file_exists($storagePath) || !is_dir($storagePath)) {
            mkdir($storagePath, 0777, true);
        }

        if (!file_exists($storagePath) || !is_dir($storagePath)) {
            return null;
        }

        $tempDir = FileSystemHelper::getTmpDirectory();
        $name    = StringHelper::random(20);

        if (!file_exists($tempDir) || !is_dir($tempDir)) {
            return null;
        }

        if (!file_put_contents($tempDir . '/' . $name, $imageRequest['message'])) {
            return null;
        }

        if (($info = getimagesize($tempDir . '/' . $name)) === false) {
            unlink($tempDir . '/' . $name);
            return null;
        }

        if (empty($info[0]) || empty($info[1]) || empty($info['mime'])) {
            unlink($tempDir . '/' . $name);
            return null;
        }

        $mimes = array();
        $mimes['jpg'] = Yii::app()->extensionMimes->get('jpg')->toArray();
        $mimes['png'] = Yii::app()->extensionMimes->get('png')->toArray();
        $mimes['gif'] = Yii::app()->extensionMimes->get('gif')->toArray();

        $extension = null;
        foreach ($mimes as $_extension => $_mimes) {
            if (in_array($info['mime'], $_mimes)) {
                $extension = $_extension;
                break;
            }
        }

        if ($extension === null) {
            unlink($tempDir . '/' . $name);
            return null;
        }

        if (!copy($tempDir . '/' . $name, $storagePath . '/' . $name . '.' . $extension)) {
            unlink($tempDir . '/' . $name);
            return null;
        }

        return '/frontend/assets/files/avatars/' . $name . '.' . $extension;
    }

    /**
     * @since 1.3.7
     * @param Customer $customer
     */
    protected function _subscribeToEmailList(Customer $customer)
    {
        $apiUrl     = Yii::app()->options->get('system.customer_registration.api_url');
        $privateKey = Yii::app()->options->get('system.customer_registration.api_private_key');
        $publicKey  = Yii::app()->options->get('system.customer_registration.api_public_key');
        $listUids   = Yii::app()->options->get('system.customer_registration.api_list_uid');

        if (empty($apiUrl) || empty($privateKey) || empty($publicKey) || empty($listUids)) {
            return;
        }

        require_once Yii::getPathOfAlias('common.vendors.MailWizzApi.Autoloader') . '.php';
        Yii::registerAutoloader(array('MailWizzApi_Autoloader', 'autoloader'));
        
        MailWizzApi_Base::setConfig(new MailWizzApi_Config(array(
            'apiUrl'        => $apiUrl,
            'publicKey'     => $publicKey,
            'privateKey'    => $privateKey,
        )));
        
        $lists    = CommonHelper::getArrayFromString($listUids, ',');
        $endpoint = new MailWizzApi_Endpoint_ListSubscribers();
        
        foreach ($lists as $list) {
            $endpoint->create($list, array(
                'EMAIL'    => $customer->email,
                'FNAME'    => $customer->first_name,
                'LNAME'    => $customer->last_name,
            ));
        }
    }

    /**
     * Called when the application is offline
     */
    public function actionOffline()
    {
        if (Yii::app()->options->get('system.common.site_status') !== 'offline') {
            $this->redirect(array('dashboard/index'));
        }

        throw new CHttpException(503, Yii::app()->options->get('system.common.site_offline_message'));
    }

    /**
     * The error handler
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo CHtml::encode($error['message']);
            } else {
                $this->setData(array(
                    'pageMetaTitle' => Yii::t('app', 'Error {code}!', array('{code}' => (int)$error['code'])),
                ));
                $this->render('error', $error) ;
            }
        }
    }
}
