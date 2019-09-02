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

class ManagementController extends Controller
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

    /**
     * List available campaigns
     */
    public function actionIndex()
    {
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('management', 'Management'),
            'pageHeading'       => Yii::t('management', 'Our Services'),
            'pageBreadcrumbs'   => array(
                //Yii::t('campaigns', 'Sms') => $this->createUrl('sms/index'),
                Yii::t('app', 'Management')
            )
        ));

        $this->render('index');
    }
	
	public function actionPopup(){
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('management', 'Guidance For Wordpress Subscriber Popup'),
            'pageHeading'       => Yii::t('management', 'Guidance For Wordpress Subscriber Popup'),
            'pageBreadcrumbs'   => array(
                //Yii::t('campaigns', 'Sms') => $this->createUrl('sms/index'),
                Yii::t('app', 'Guidance For Wordpress Subscriber Popup')
            )
        ));

        $this->render('popup');
	}
	
}
