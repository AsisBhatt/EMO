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
		$apps = Yii::app()->apps;
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('datetimepicker/js/bootstrap-datetimepicker.min.js')));
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('campaigns.js')));
		$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('dashboard.js')));
        $this->getData('pageStyles')->add(array('src' => AssetsUrl::css('wizard.css')));
		$this->getData('pageScripts')->mergeWith(array(
            array('src' => $apps->getBaseUrl('assets/js/flot/jquery.flot.min.js')),
            array('src' => $apps->getBaseUrl('assets/js/flot/jquery.flot.resize.min.js')),
            array('src' => $apps->getBaseUrl('assets/js/flot/jquery.flot.categories.min.js')),
        ));
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
                Yii::t('dashboard', 'emaildashboard') => $this->createUrl('dashboard/emaildashboard'),
                Yii::t('app', 'management')
            )
        ));

        $this->render('management');
	}
	
	public function actionReports(){
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('email', 'Reports'),
            'pageHeading'       => Yii::t('email', 'Reports'),
            'pageBreadcrumbs'   => array(
                //Yii::t('dashboard', 'emaildashboard') => $this->createUrl('dashboard/emaildashboard'),
                Yii::t('app', 'Reports')
            )
        ));

        $this->render('reports');
	}

	public function actionFinancial(){
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('email', 'Financial'),
            'pageHeading'       => Yii::t('email', 'Financial'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'emaildashboard') => $this->createUrl('dashboard/emaildashboard'),
                Yii::t('app', 'Financial')
            )
        ));

        $this->render('financial');
	}

	public function actionEzemail(){
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('email', 'Ez Email'),
            'pageHeading'       => Yii::t('email', 'Ez Email'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'emaildashboard') => $this->createUrl('dashboard/emaildashboard'),
                Yii::t('app', 'Ez Email')
            )
        ));

        $this->render('ez_email');
	}	
}