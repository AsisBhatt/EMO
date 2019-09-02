<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CampaignsController
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

class EmailController extends Controller
{
    public function init()
    {
        $this->getData('pageStyles')->add(array('src' => AssetsUrl::js('datetimepicker/css/bootstrap-datetimepicker.min.css')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('datetimepicker/js/bootstrap-datetimepicker.min.js')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('campaigns.js')));
		$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('dashboard.js')));
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
	
	public function actionManagement(){
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('email', 'Management'),
            'pageHeading'       => Yii::t('email', 'Management'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'EmailDashboard') => $this->createUrl('dashboard/emaildashboard'),
                Yii::t('app', 'Management')
            )
        ));

        $this->render('management');
	}
	
	public function actionFinancial(){
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('email', 'Financial'),
            'pageHeading'       => Yii::t('email', 'Financial'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'EmailDashboard') => $this->createUrl('dashboard/smsdashboard'),
                Yii::t('app', 'Financial')
            )
        ));

        $this->render('financial');
	}

	public function actionReports(){
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('email', 'Reports'),
            'pageHeading'       => Yii::t('email', 'Reports'),
            'pageBreadcrumbs'   => array(
                //Yii::t('dashboard', 'SocialmediaDashboard') => $this->createUrl('dashboard/socialdashboard'),
                Yii::t('app', 'Reports')
            )
        ));

        $this->render('reports');
	}	
   
}
