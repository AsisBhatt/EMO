<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * ListDefaultFieldsBehavior
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class ListDefaultFieldsBehavior extends CActiveRecordBehavior 
{
    public function afterSave($event)
    {
        $type = ListFieldType::model()->findByAttributes(array(
            'identifier' => 'text',
        ));
        
        if (empty($type)) {
            return;
        }
		
        $model = new ListField();
        $model->type_id     = $type->type_id;
        $model->list_id     = $this->owner->list_id;
        $model->label       = 'Email';
        $model->tag         = 'EMAIL';
        $model->required    = 'no';
        $model->visibility  = 'visible';
        $model->sort_order  = 0;
        $model->save(false);
        
        $model = new ListField();
        $model->type_id     = $type->type_id;
        $model->list_id     = $this->owner->list_id;
        $model->label       = 'First name';
        $model->tag         = 'FNAME';
        $model->required    = 'no';
        $model->visibility  = 'visible';
        $model->sort_order  = 1;
        $model->save(false);
        
        $model = new ListField();
        $model->type_id     = $type->type_id;
        $model->list_id     = $this->owner->list_id;
        $model->label       = 'Last name';
        $model->tag         = 'LNAME';
        $model->required    = 'no';
        $model->visibility  = 'visible';
        $model->sort_order  = 2;
        $model->save(false);
		
		$model = new ListField();
        $model->type_id     = $type->type_id;
        $model->list_id     = $this->owner->list_id;
        $model->label       = 'Mobile';
        $model->tag         = 'MOBILE';
        $model->required    = 'no';
        $model->visibility  = 'visible';
        $model->sort_order  = 3;
        $model->save(false);
    }
}