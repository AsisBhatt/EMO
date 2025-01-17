<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Sending_domainsController
 *
 * Handles the actions for sending domains related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.7
 */

class Sending_domainsController extends Controller
{
    // init method
    public function init()
    {
        parent::init();

        $customer = Yii::app()->customer->getModel();
        if ($customer->getGroupOption('sending_domains.can_manage_sending_domains', 'no') != 'yes') {
            $this->redirect(array('dashboard/index'));
        }
    }

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
        $filters = array(
            'postOnly + delete',
        );

        return CMap::mergeArray($filters, parent::filters());
    }

    /**
     * List all available sending domains
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;
        $domain  = new SendingDomain('search');
        $domain->unsetAttributes();

        $domain->attributes = (array)$request->getQuery($domain->modelName, array());
        $domain->customer_id = Yii::app()->customer->getId();
        
        if ($domain->getRequirementsErrors()) {
            $this->redirect('dashboard/index');
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sending_domains', 'View sending domains'),
            'pageHeading'       => Yii::t('sending_domains', 'View sending domains'),
            'pageBreadcrumbs'   => array(
                Yii::t('sending_domains', 'Sending domains') => $this->createUrl('sending_domains/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('domain'));
    }

    /**
     * Create a new sending domain
     */
    public function actionCreate()
    {
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;
        $domain  = new SendingDomain();

        if ($domain->getRequirementsErrors()) {
            $this->redirect('dashboard/index');
        }

        $customer = Yii::app()->customer->getModel();
        if (($limit = (int)$customer->getGroupOption('sending_domains.max_sending_domains', -1)) > -1) {
            $count = SendingDomain::model()->countByAttributes(array('customer_id' => (int)$customer->customer_id));
            if ($count >= $limit) {
                $notify->addWarning(Yii::t('sending_domains', 'You have reached the maximum number of allowed sending domains!'));
                $this->redirect(array('sending_domains/index'));
            }
        }

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($domain->modelName, array()))) {
            $domain->attributes  = $attributes;
            $domain->customer_id = Yii::app()->customer->getId();
            if (!$domain->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
				
				CommonHelper::setActivityLogs('Sending Domains Create '.str_replace(array('{{','}}'),'',$domain->tableName()),$domain->domain_id,$domain->tableName(),'Sending Domains Create',(int)Yii::app()->customer->getId());
                
				$notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'domain'    => $domain,
            )));

            if ($collection->success) {
                $this->redirect(array('sending_domains/update', 'id' => $domain->domain_id));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sending_domains', 'Create new sending domain'),
            'pageHeading'       => Yii::t('sending_domains', 'Create new sending domain'),
            'pageBreadcrumbs'   => array(
                Yii::t('sending_domains', 'Sending domains') => $this->createUrl('sending_domains/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('domain'));
    }

    /**
     * Update existing sending domain
     */
    public function actionUpdate($id)
    {
        $domain = SendingDomain::model()->findByAttributes(array(
            'domain_id'     => (int)$id,
            'customer_id'   => (int)Yii::app()->customer->getId()
        ));

        if (empty($domain)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        if ($domain->getRequirementsErrors()) {
            $this->redirect('dashboard/index');
        }

        if ($domain->getIsLocked()) {
            $notify->addWarning(Yii::t('servers', 'This domain is locked, you cannot change or delete it!'));
            $this->redirect(array('sending_domains/index'));
        }

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($domain->modelName, array()))) {
            $domain->attributes  = $attributes;
            $domain->customer_id = Yii::app()->customer->getId();
            if (!$domain->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
				
				CommonHelper::setActivityLogs('Sending Domains Update '.str_replace(array('{{','}}'),'',$domain->tableName()),$id,$domain->tableName(),'Sending Domains Update',(int)Yii::app()->customer->getId());
				
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'domain'    => $domain,
            )));

            if ($collection->success) {
                $this->redirect(array('sending_domains/update', 'id' => $domain->domain_id));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('sending_domains', 'Update sending domain'),
            'pageHeading'       => Yii::t('sending_domains', 'Update sending domain'),
            'pageBreadcrumbs'   => array(
                Yii::t('sending_domains', 'Sending domains') => $this->createUrl('sending_domains/index'),
                Yii::t('app', 'Update'),
            )
        ));

        $this->render('form', compact('domain'));
    }

    /**
     * Verify sending domain
     */
    public function actionVerify($id)
    {
        $notify = Yii::app()->notify;
        $domain = SendingDomain::model()->findByAttributes(array(
            'domain_id'     => (int)$id,
            'customer_id'   => (int)Yii::app()->customer->getId()
        ));
        if (empty($domain)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
        $dnsRecords = dns_get_record(SendingDomain::getDkimFullSelector().'.'.$domain->name, DNS_TXT);
        if (empty($dnsRecords)) {
            $notify->addError(Yii::t('sending_domains', 'Unable to retrieve the TXT records for your domain name.'));
            $this->redirect(array('sending_domains/update', 'id' => $id));
        }
        $found = false;
        $publicKey = $domain->getCleanPublicKey();
        $publicKey = preg_replace('/[^a-z0-9]/six', '', $publicKey);
        foreach ($dnsRecords as $info) {
            if (!empty($info['txt']) && strpos(preg_replace('/[^a-z0-9]/six', '', $info['txt']), $publicKey) !== false) {
                $found = true;
                break;
            }
        }

        if (!$found) {
            $notify->addError(Yii::t('sending_domains', 'Unable to find proper TXT record for your domain name, if you just added the records please wait for them to propagate.'));
            $this->redirect(array('sending_domains/update', 'id' => $id));
        }

        $domain->verified = SendingDomain::TEXT_YES;
        $domain->save(false);
		
		CommonHelper::setActivityLogs('Sending Domains Verify '.str_replace(array('{{','}}'),'',$domain->tableName()),$domain->domain_id,$domain->tableName(),'Sending Domains Verify',(int)Yii::app()->customer->getId());

        $notify->addSuccess(Yii::t('sending_domains', 'Your domain has been successfully verified.'));
        $this->redirect(array('sending_domains/update', 'id' => $id));
    }

    /**
     * Delete existing sending domain
     */
    public function actionDelete($id)
    {
        $domain = SendingDomain::model()->findByAttributes(array(
            'domain_id'     => (int)$id,
            'customer_id'   => (int)Yii::app()->customer->getId()
        ));

        if (empty($domain)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        if (!$domain->getIsLocked()) {
			
			CommonHelper::setActivityLogs('Sending Domains Delete '.str_replace(array('{{','}}'),'',$domain->tableName()),$id,$domain->tableName(),'Sending Domains Delete',(int)Yii::app()->customer->getId());
            
			$domain->delete();
        }

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('sending_domains/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $domain,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }
}
