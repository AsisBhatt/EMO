<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * GridViewBulkAction
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5.4
 */
 
class GridViewBulkAction extends CWidget
{
    public $model;
    
    public $formAction;
    
    public function init()
    {
        parent::init();
        Yii::app()->clientScript->registerScriptFile(Yii::app()->apps->getBaseUrl('assets/js/grid-view-bulk-action.js'));
    }
    
    public function run()
    {
        $this->render('grid-view-bulk-action', array(
            'model'       => $this->model,
            'bulkActions' => $this->model->getBulkActionsList(),
            'formAction'  => $this->formAction,
        ));
    }
}