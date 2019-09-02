<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CustomersController
 *
 * Handles the actions for customers related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class CustomersController extends Controller
{
    public function init()
    {
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('customers.js')));
		$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('account.js')));
        parent::init();
    }

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
        $filters = array(
            'postOnly + delete, reset_sending_quota',
        );

        return CMap::mergeArray($filters, parent::filters());
    }

    /**
     * List all available customers
     */
    public function actionIndex()
    {
		//Yii:: app () ->cache->flush();exit;
        $request    = Yii::app()->request;
        $customer   = new Customer('search');
        $customer->unsetAttributes();

        $customer->attributes = (array)$request->getQuery($customer->modelName, array());

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Merchant Customer'),
            'pageHeading'       => Yii::t('customers', 'Merchant Customer'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Merchant Customer') => $this->createUrl('customers/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('customer'));
    }

    /**
     * Create a new customer
     */
    public function actionCreate()
    {
        $customer   = new Customer();
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($customer->modelName, array()))) {
            $customer->attributes = $attributes;
            if (!$customer->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'customer'  => $customer,
            )));

            if ($collection->success) {
                $this->redirect(array('customers/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Create New Merchant Customer'),
            'pageHeading'       => Yii::t('customers', 'Create New Merchant Customer'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Customers') => $this->createUrl('customers/index'),
                Yii::t('app', 'Create New Merchant Customer'),
            )
        ));

        $this->render('form', compact('customer'));
    }

    /**
     * Update existing customer
     */
    public function actionUpdate($id)
    {
        $customer = Customer::model()->findByPk((int)$id);

        if (empty($customer)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;

        $this->setData('initCustomerStatus', $customer->status);
        $customer->onAfterSave = array($this, '_sendEmailNotification');

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($customer->modelName, array()))) {
            $customer->attributes = $attributes;
            if (!$customer->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'customer'  => $customer,
            )));

            if ($collection->success) {
                $this->redirect(array('customers/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Update Merchant Customer'),
            'pageHeading'       => Yii::t('customers', 'Update Merchant Customer'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Customers') => $this->createUrl('customers/index'),
                Yii::t('app', 'Update Merchant Customer'),
            )
        ));

        $this->render('form', compact('customer'));
    }

    /**
     * Delete existing customer
     */
    public function actionDelete($id)
    {
        $customer = Customer::model()->findByPk((int)$id);

        if (empty($customer)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        if ($customer->removable == Customer::TEXT_YES) {
            $customer->delete();
        }

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('customers/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $customer,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }
	
	public function actionCompany($id = NULL)
    {
		if($id == NULL){
			$customer = Yii::app()->customer->getModel();
		}else{
			$customer = Customer::model()->findByPk((int)$id);
		}
		
        if($id == NULL || empty($customer->company)) {
            $customer->company = new CustomerCompany();
        }
        
        $company = $customer->company;
        $request = Yii::app()->request;
        
        if ($request->isPostRequest && $attributes = $request->getPost($company->modelName)) {
			//print_r($_POST);exit;
			$check_beelift_number = Yii::app()->db->createCommand("Select * FROM uic_beelift_number WHERE number ='".$attributes['flowroute_sms_num']."' and customer_id != '".$customer->customer_id."'")->queryAll();
			
			/*$get_previous_number = Yii::app()->db->createCommand("SELECT * FROM uic_beelift_number WHERE customer_id ='".$customer->customer_id."'")->queryAll();
			echo '<pre>';
			print_r($get_previous_number);
			echo '</pre>';
			exit;*/
			
			
			//print_r($company->attributes);exit;
			$company->attributes = $attributes;
            $company->customer_id = $customer->customer_id;
			$company->save();
            
			if(!count($check_beelift_number)){
				$beelift_number = new BeeliftNumber();
				$beelift_number->customer_id = $customer->customer_id;
				$beelift_number->number = $attributes['flowroute_sms_num'];
				$beelift_number->status = 'ACTIVE';
				$beelift_number->created_at = date('Y-m-d H:m:s');
				$beelift_number->save();
			}
			
            if ($company->save()) {
                Yii::app()->notify->addSuccess(Yii::t('customers', 'Company info successfully updated!'));
            }
            
            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => Yii::app()->notify->hasSuccess,
                'customer'  => $customer,
                'company'   => $company,
            )));
        }
        
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Update Merchant Customer'),
            'pageHeading'       => Yii::t('customers', 'Update Merchant Customer'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Customers') => $this->createUrl('customers/index'),
                Yii::t('app', 'Update Merchant Customer'),
            )
        ));
        $this->render('company', compact('company'));
    }

    /**
     * Impersonate (login as) this customer
     */
    public function actionImpersonate($id)
    {
        $customer = Customer::model()->findByPk((int)$id);

        if (empty($customer)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request = Yii::app()->request;
        $notify = Yii::app()->notify;

        Yii::import('customer.components.web.auth.*');
        $identity = new CustomerIdentity($customer->email, null);
        $identity->impersonate = true;

        if (!$identity->authenticate() || !Yii::app()->customer->login($identity)) {
            $notify->addError(Yii::t('app', 'Unable to impersonate the customer!'));
            $this->redirect(array('customers/index'));
        }

        Yii::app()->customer->setState('__customer_impersonate', true);
        $notify->clearAll()->addSuccess(Yii::t('app', 'You are using the customer account for {customerName}!', array(
            '{customerName}' => $customer->fullName ? $customer->fullName : $customer->email,
        )));

        $this->redirect(Yii::app()->apps->getAppUrl('customer', 'dashboard/index', true));
    }

    /**
     * Reset sending quota
     */
    public function actionReset_sending_quota($id)
    {
        $customer = Customer::model()->findByPk((int)$id);

        if (empty($customer)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $customer->resetSendingQuota();

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $notify->addSuccess(Yii::t('customers', 'The sending quota has been successfully reseted!'));

        if (!$request->isAjaxRequest) {
            $this->redirect($request->getPost('returnUrl', array('customers/index')));
        }
    }

    /**
     * Autocompletre for search
     */
    public function actionAutocomplete($term)
    {
        $request = Yii::app()->request;
        if (!$request->isAjaxRequest) {
            $this->redirect(array('customers/index'));
        }

        $criteria = new CDbCriteria();
        $criteria->select = 'customer_id, first_name, last_name, email';
        $criteria->compare(new CDbExpression('CONCAT(first_name, " ", last_name)'), $term, true);
        $criteria->compare('email', $term, true, 'OR');
        $criteria->limit = 10;

        $models = Customer::model()->findAll($criteria);
        $results = array();

        foreach ($models as $model) {
            $results[] = array(
                'customer_id' => $model->customer_id,
                'value'       => $model->getFullName() ? $model->getFullName() : $model->email,
            );
        }

        return $this->renderJson($results);
    }

    public function _sendEmailNotification(CEvent $event)
    {
        if ($this->getData('initCustomerStatus') != Customer::STATUS_PENDING_ACTIVE) {
            return;
        }

        $customer = $event->sender;
        if ($customer->status != Customer::STATUS_ACTIVE) {
            return;
        }

        $options = Yii::app()->options;
        $notify  = Yii::app()->notify;

        $emailTemplate = $options->get('system.email_templates.common');
        $emailBody     = $this->renderPartial('_email-approve', compact('customer'), true);
        $emailTemplate = str_replace('[CONTENT]', $emailBody, $emailTemplate);

        $email = new TransactionalEmail();
        $email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
        $email->to_name      = $customer->getFullName();
        $email->to_email     = $customer->email;
        $email->from_name    = $options->get('system.common.site_name', 'Marketing website');
        $email->subject      = Yii::t('customers', 'Your account has been approved!');
        $email->body         = $emailTemplate;
        $email->save();

        // send welcome email if needed
        $sendWelcome        = $options->get('system.customer_registration.welcome_email', 'no') == 'yes';
        $sendWelcomeSubject = $options->get('system.customer_registration.welcome_email_subject', '');
        $sendWelcomeContent = $options->get('system.customer_registration.welcome_email_content', '');
        if (!empty($sendWelcome) && !empty($sendWelcomeSubject) && !empty($sendWelcomeContent)) {
            $searchReplace = array(
                '[FIRST_NAME]' => $customer->first_name,
                '[LAST_NAME]'  => $customer->last_name,
                '[FULL_NAME]'  => $customer->fullName,
                '[EMAIL]'      => $customer->email,
            );
            $sendWelcomeSubject = str_replace(array_keys($searchReplace), array_values($searchReplace), $sendWelcomeSubject);
            $sendWelcomeContent = str_replace(array_keys($searchReplace), array_values($searchReplace), $sendWelcomeContent);
            $emailTemplate = $options->get('system.email_templates.common');
            $emailTemplate = str_replace('[CONTENT]', $sendWelcomeContent, $emailTemplate);

            $email = new TransactionalEmail();
            $email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
            $email->to_name      = $customer->getFullName();
            $email->to_email     = $customer->email;
            $email->from_name    = $options->get('system.common.site_name', 'Marketing website');
            $email->subject      = $sendWelcomeSubject;
            $email->body         = $emailTemplate;
            $email->save();
        }

        $notify->addSuccess(Yii::t('customers', 'A notification email has been sent for this customer!'));
    }
	
	public function renderTabs()
    {
        $route = Yii::app()->getController()->getRoute();
		$id = (isset($_GET['id']) && $_GET['id'] != '' ? $_GET['id'] : '');
        $priority = 0;
        $tabs = array();
        if($route == 'customers/update' || $route == 'customers/company' && $id != ''){
			$tabs[] = array(
				'label'     => '<span class="glyphicon glyphicon-list"></span> '.Yii::t('customers', 'Profile'), 
				'url'       => array('customers/update','id' => $id), 
				'active'    => strpos('customers/update', $route) === 0,
				'priority'  => (++$priority),
			);
			
			$tabs[] = array(
				'label'     => '<span class="glyphicon glyphicon-briefcase"></span> '.Yii::t('customers', 'Company'), 
				'url'       => array('customers/company', 'id' => $id), 
				'active'    => strpos('customers/company', $route) === 0,
				'priority'  => (++$priority),
			);
		}else if($route == 'customers/create' || $route == 'customers/company'){
			$tabs[] = array(
				'label'     => '<span class="glyphicon glyphicon-list"></span> '.Yii::t('customers', 'Profile'), 
				'url'       => array('customers/create'), 
				'active'    => strpos('customers/create', $route) === 0,
				'priority'  => (++$priority),
			);
			
			$tabs[] = array(
				'label'     => '<span class="glyphicon glyphicon-briefcase"></span> '.Yii::t('customers', 'Company'), 
				'url'       => array('customers/company'), 
				'active'    => strpos('customers/company', $route) === 0,
				'priority'  => (++$priority),
			);
		}
        
        
        // since 1.3.6.2
        $tabs = Yii::app()->hooks->applyFilters('customer_account_edit_render_tabs', $tabs);

        $sort = array();
        foreach ($tabs as $index => $tab) {
            if (!isset($tab['label'], $tab['url'], $tab['active'])) {
                unset($tabs[$index]);
                continue;
            }
            
            $sort[] = isset($tab['priority']) ? (int)$tab['priority'] : (++$priority);
            
            if (isset($tabs['priority'])) {
                unset($tabs['priority']);
            }
            
            if (isset($tabs['items'])) {
                unset($tabs['items']);
            }
        }
        
        if (empty($tabs) || !is_array($tabs)) {
            return;
        }
        
        array_multisort($sort, $tabs);
        
        return $this->widget('zii.widgets.CMenu', array(
            'htmlOptions'   => array('class' => 'nav nav-tabs'),
            'items'         => $tabs,
            'encodeLabel'   => false,
        ), true);
    }
	
	public function actionBeeliftnumber()
	{
		$request = Yii::app()->request;
		
		$beeliftnumber = new BeeliftNumber('search');
		$beeliftnumber->unsetAttributes();
		
		$beeliftnumber->attributes  = (array)$request->getQuery($beeliftnumber->modelName, array());
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'BeeliftNumber Merchant Customer'),
            'pageHeading'       => Yii::t('customers', 'BeeliftNumber Merchant Customer'),
            'pageBreadcrumbs'   => array(
                //Yii::t('customers', 'Customers') => $this->createUrl('customers/index'),
                Yii::t('app', 'BeeliftNumber Merchant Customer'),
            )
        ));
        $this->render('beelift_number', compact('beeliftnumber'));
	}
	
	/**
     * Display country zones
     */
    public function actionZones_by_country()
    {
		$c_id = (int) Yii::app()->request->getQuery('country_id');
		$country_id = (isset($c_id) && $c_id != '' ? $c_id : '223');
		
        $criteria = new CDbCriteria();
        $criteria->select = 'zone_id, name';
        $criteria->compare('country_id', $country_id);
        $models = Zone::model()->findAll($criteria);
        
        $zones = array(
            array('zone_id' => '', 'name' => Yii::t('app', 'Please select'))
        );
        foreach ($models as $model) {
            $zones[] = array(
                'zone_id'    => $model->zone_id, 
                'name'        => $model->name
            );
        }
		
        return $this->renderJson(array('zones' => $zones));
    }
	
	public function actionReport($id){
		$request = Yii::app()->request;
		$notify  = Yii::app()->notify;
		$customer_company = CustomerCompany::model()->findByAttributes(array('customer_id' => $id));

		$smsrply = new SmsRply('search');
		$smsrply->unsetAttributes();
		
		$smsrply->attributes = (array)$request->getQuery($smsrply->modelName, array());
		$smsrply->sms_rply_to_number  = $customer_company->flowroute_sms_num;
		$smsrply->customer_id  = $id;
		$smsrply->sms_rply_body = 'STOP';
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sms', 'SMS Stop Count Report'),
            'pageHeading'       => Yii::t('sms', 'SMS Stop Count Report'),
            'pageBreadcrumbs'   => array(
                //Yii::t('dashboard', 'SmsDashboard') => $this->createUrl('dashboard/smsdashboard'),
                Yii::t('app', 'SMS Stop Count Report')
            )
        ));

        $this->render('stop_report',compact('smsrply'));		
		
	}
	
	public function actionCronstop(){
		$options = Yii::app()->options;
		$customers_array = Customer::model()->findByAttributes(array('status' => 'active'))->findAll();
		
		foreach($customers_array as $customers_key => $customers){
			
			$email_array = TransactionalEmail::model()->findByAttributes(array('to_email' => $customers->email,'subject' => 'Received STOP Requests - Beelift'));
			
			$customer_company = CustomerCompany::model()->findByAttributes(array('customer_id' => $customers->customer_id));
			if(!is_array($email_array) && !count($email_array)){
		
				if(!empty($customer_company)){
					$sms_rply_stop_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM uic_sms_rply WHERE customer_id='".$customers->customer_id."' AND sms_rply_to_number='".$customer_company->flowroute_sms_num."' AND sms_rply_body LIKE '%STOP%'")->queryScalar();
				
					if($sms_rply_stop_count > 50){
						$emailTemplate  = $options->get('system.email_templates.common');
						$emailBody      = $this->renderPartial('_sms-stop-counter', compact('customers','customer_company'), true);
						$emailTemplate  = str_replace('[CONTENT]', $emailBody, $emailTemplate);
						
						$email = new TransactionalEmail();
						$email->sendDirectly = (bool)($options->get('system.customer_registration.send_email_method', 'transactional') == 'direct');
						$email->to_name      = $customers->getFullName();
						$email->to_email     = $customers->email;
						$email->from_name    = $options->get('system.common.site_name', 'Marketing website');
						$email->subject      = 'Received STOP Requests - Beelift';
						$email->body         = $emailTemplate;
						$email->save();
					}
				}
			}
		}
	}

}
