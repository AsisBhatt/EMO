<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * DashboardController
 *
 * Handles the actions for dashboard related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class Text_to_payController extends Controller
{
	public $enableCsrfValidation = false;
	
    public function init()
    {
        $apps = Yii::app()->apps;
        $this->getData('pageScripts')->mergeWith(array(
            array('src' => AssetsUrl::js('dashboard.js'))
        ));
        parent::init();
		
    }

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
        return CMap::mergeArray(array(
            'postOnly + delete_log, delete_logs',
        ), parent::filters());
    }
    /**
     * Display dashboard informations
     */
    public function actionIndex()
    {
		$text_reply = new TextReply('search');

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('text_reply', 'Text 2 Reply'),
            'pageHeading'       => Yii::t('text_reply', 'Text 2 Reply'),
            'pageBreadcrumbs'   => array(
                Yii::t('text_reply', 'Text 2 Reply'),
            ),
        ));
		
        $this->render('index', compact('text_reply'));
    }
	
	public function actionSocialdashboard(){
		$options = Yii::app()->options;
        $notify  = Yii::app()->notify;
		
		$this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('dashboard', 'Social Media Dashboard'),
			'pageHeading'       => Yii::t('dashboard', 'Social Media Dashboard'),
            'pageBreadcrumbs'   => array(
                Yii::t('dashboard', 'Social Media Dashboard'),
            ),
        ));
		
		$this->render('social_dashboard', compact());
	}

}
