<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * AnalyticController
 *
 * Handles the actions for campaigns related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class AnalyticController extends Controller
{
    public function init()
    {
        $this->getData('pageStyles')->add(array('src' => AssetsUrl::js('datetimepicker/css/bootstrap-datetimepicker.min.css')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('datetimepicker/js/bootstrap-datetimepicker.min.js')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('campaigns.js')));
        $this->getData('pageStyles')->add(array('src' => AssetsUrl::css('wizard.css')));

        parent::init();
    }

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
		$filters = array(
            //'postOnly + delete, slug',
        );
        return CMap::mergeArray($filters, parent::filters());
    }

    public function actionInstructionganalytics()
    {
		$customer = Yii::app()->customer->getModel();
 
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Google Analytics Setting Instructions'),
            'pageHeading'       => Yii::t('customers', 'Google Analytics Setting Instructions'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/instructionganalytics'),
                Yii::t('app', 'Google Analytics Setting Instructions')
            )
        ));
        
        $this->render('instructionganalytics', compact('customer'));
    }
	
	public function actionGanalytics()
    {
		//die($profileid);
		$customer = Yii::app()->customer->getModel();
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Google Analytics Report'),
            'pageHeading'       => Yii::t('customers', 'Google Analytics Report'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/ganalytics'),
                Yii::t('app', 'Google Analytics Report')
            )
        ));
        
        $this->render('ganalytics', compact('customer'));
    }
	
	public function actionGanalyticsreport($profileid=null)
    {
		$profile_id=$_GET['pid']; 
		//print_r($_GET['pid']); 
		 
		$request = Yii::app()->request;
		//print_r($request); 
		//die($profileid);
		
       	$ga['profile_id'] = $profile_id;
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Google Analytics Report'),
            'pageHeading'       => Yii::t('customers', 'Google Analytics Report'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/ganalytics'),
                Yii::t('app', 'Google Analytics Report')
            )
        ));
        
        $this->render('ganalyticsreport', compact('ga'));
    }
	public function actionGanalyticsreportgraph($profileid=null)
    {
		$profile_id=$_GET['pid']; 
		//print_r($_GET['pid']); 
		 
		$request = Yii::app()->request;
		//print_r($request); 
		//die($profileid);
		
       	$ga['profile_id'] = $profile_id;
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Google Analytics Graph Report'),
            'pageHeading'       => Yii::t('customers', 'Google Analytics Graph Report'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/ganalytics'),
                Yii::t('app', 'Google Analytics Graph Report')
            )
        ));
        
        $this->render('ganalyticsreportgraph', compact('ga'));
    }
	
	
	
	
	public function actionAnalyticsinit()
    {
		 
		$anlyticsInput =array();
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Google Analytics'),
            'pageHeading'       => Yii::t('customers', 'Google Analytics '),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/analyticsinit'),
                Yii::t('app', 'Google Analytics')
            )
        ));
        
        $this->render('analyticsinit', compact('analyticsInput'));
    }
	public function actionAnalyticsreport()
    {
		
		if (Yii::app()->request->isPostRequest ) {
			$accountId= $_POST['accountId']; 
			//print_r($_POST['accountId']); 
			$_SESSION['accountId'] = $accountId;
        }
		
		$analyticsInput =array();
		$analyticsInput['accountId']= @$accountId;
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Google Analytics Report'),
            'pageHeading'       => Yii::t('customers', 'Google Analytics Report'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/analyticsreport'),
                Yii::t('app', 'Google Analytics Report')
            )
        ));
        
        $this->render('analyticsreport', compact('analyticsInput'));;
    }
	
	
	public function actionAnalyticsreportgraph()
    {
		
		if (Yii::app()->request->isPostRequest ) {
			$accountId= $_POST['accountId']; 
			//print_r($_POST['accountId']); 
			$_SESSION['accountId'] = $accountId;
        }
		
		$analyticsInput =array();
		$analyticsInput['accountId']= @$accountId;
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Google Analytics Report'),
            'pageHeading'       => Yii::t('customers', 'Google Analytics Report'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/analyticsreport'),
                Yii::t('app', 'Google Analytics Report')
            )
        ));
        
        $this->render('analyticsreportgraph', compact('analyticsInput'));;
    }
	
	
	public function actionAnalyticsexport()
    {
		
		if (Yii::app()->request->isPostRequest ) {
			$accountId= $_POST['accountId']; 
			//print_r($_POST['accountId']); 
			$_SESSION['accountId'] = $accountId;
        }
		
		$analyticsInput =array();
		$analyticsInput['accountId']= @$accountId;
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('customers', 'Google Analytics Report'),
            'pageHeading'       => Yii::t('customers', 'Google Analytics Report'),
            'pageBreadcrumbs'   => array(
                Yii::t('customers', 'Account') => $this->createUrl('account/analyticsreport'),
                Yii::t('app', 'Google Analytics Report')
            )
        ));
        
        $this->render('analyticsexport', compact('analyticsInput'));;
    }
	
	
	

   
}
