<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * FieldBuilderTypeTextCrud
 * 
 * The save action is running inside an active transaction.
 * For fatal errors, an exception must be thrown, otherwise the errors array must be populated.
 * If an exception is thrown, or the errors array is populated, the transaction is rolled back.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class FieldBuilderTypeTextCrud extends CBehavior
{
    public function _saveFields(CEvent $event)
    {
        $hooks      = Yii::app()->hooks;
        $fieldType  = $this->owner->getFieldType();
        $list       = $this->owner->getList();
        $typeName   = $fieldType->identifier;
        
        if (!isset($event->params['fields'][$typeName]) || !is_array($event->params['fields'][$typeName])) {
            $event->params['fields'][$typeName] = array();
        }
        
        $postModels = Yii::app()->request->getPost('ListField', array());
        if (!isset($postModels[$typeName]) || !is_array($postModels[$typeName])) {
            $postModels[$typeName] = array();
        }
        
        $models = array();
        
        foreach ($postModels[$typeName] as $index => $attributes) {
            $model = null;
            if (!empty($attributes['field_id'])) {
                $model = ListField::model()->findByAttributes(array(
                    'field_id'  => (int)$attributes['field_id'],
                    'type_id'   => (int)$fieldType->type_id,
                    'list_id'   => (int)$list->list_id,
                ));
            }
            
            if (isset($attributes['field_id'])) {
                unset($attributes['field_id']);
            }
            
            if (empty($model)) {
                $model = new ListField();
            }
                
            $model->attributes = $attributes;
            $model->type_id = $fieldType->type_id;
            $model->list_id = $list->list_id;

            $models[] = $model;
        }
        
        $hasEmailTag = false;
        foreach ($models as $model) {
            if ($model->tag === 'EMAIL') {
                $model->required = 'yes';
                $model->visibility = 'visible';
                $hasEmailTag = true;
                break;
            }
        }
        
        if (!$hasEmailTag) {
            $this->owner->errors[] = array(
                'show'      => true,
                'message'   => Yii::t('list_fields', 'Missing the EMAIL tag, this is not acceptable!'),
            );
        } else {
            $modelsToKeep = array();
            foreach ($models as $model) {
                if (!$model->save()) {
                    $this->owner->errors[] = array(
                        'show'      => false, 
                        'message'   => $model->shortErrors->getAllAsString()
                    );
                } else {
                    $modelsToKeep[] = $model->field_id;
                }
            }
            
            if (empty($this->owner->errors)) {
                $criteria = new CDbCriteria();
                $criteria->compare('list_id', $list->list_id);
                $criteria->compare('type_id', $fieldType->type_id);
                $criteria->addNotInCondition('field_id', $modelsToKeep);    
                ListField::model()->deleteAll($criteria);    
            }
        }
        
        $fields = array();
        foreach ($models as $model) {
            $fields[] = $this->buildFieldArray($model);
        }

        $event->params['fields'][$typeName] = $fields;
    }
    
    public function _displayFields(CEvent $event)
    {
        $hooks      = Yii::app()->hooks;
        $fieldType  = $this->owner->getFieldType();
        $list       = $this->owner->getList();
        $typeName   = $fieldType->identifier;
        
        // register the add button.
        $hooks->addAction('customer_controller_list_fields_render_buttons', array($this, '_renderAddButton'));
        
        // register the javascript template
        $hooks->addAction('customer_controller_list_fields_after_form', array($this, '_registerJavascriptTemplate'));
        
        // register the assets
        $assetsUrl = Yii::app()->assetManager->publish(realpath(dirname(__FILE__) . '/../assets/'), false, -1, MW_DEBUG);
        
        // push the file into the queue.
        Yii::app()->clientScript->registerScriptFile($assetsUrl . '/field.js');
        
        // fields created in the save action.
        if (isset($event->params['fields'][$typeName]) && is_array($event->params['fields'][$typeName])) {
            return;
        }
        
        if (!isset($event->params['fields'][$typeName]) || !is_array($event->params['fields'][$typeName])) {
            $event->params['fields'][$typeName] = array();
        }

        $models = ListField::model()->findAllByAttributes(array(
            'type_id' => (int)$fieldType->type_id,
            'list_id' => (int)$list->list_id,
        ));
        
        $fields = array();
        foreach ($models as $model) {
            $fields[] = $this->buildFieldArray($model);
        }

        $event->params['fields'][$typeName] = $fields;
    }

    protected function buildFieldArray($model)
    {
        $hooks      = Yii::app()->hooks;
        $fieldType  = $this->owner->getFieldType();
        $list       = $this->owner->getList();
        $typeName   = $fieldType->identifier;
        
        // so that it increments properly!
        $index = $this->owner->getIndex();
        
        $viewFile = realpath(dirname(__FILE__) . '/../views/field-tpl.php');
        $model->fieldDecorator->onHtmlOptionsSetup = array($this->owner, '_addInputErrorClass');
        $model->fieldDecorator->onHtmlOptionsSetup = array($this, '_addReadOnlyAttributes');
        
        return array(
            'sort_order' => (int)$model->sort_order,
            'field_html' => $this->owner->renderInternal($viewFile, compact('model', 'index', 'fieldType', 'list'), true),
        );
    }
    
    public function _renderAddButton()
    {
        // default view file
        $viewFile = realpath(dirname(__FILE__) . '/../views/add-button.php');

        // and render
        $this->owner->renderInternal($viewFile); 
    }
    
    public function _registerJavascriptTemplate()
    {
        $model      = new ListField();
        $fieldType  = $this->owner->getFieldType();
        $list       = $this->owner->getList();
        
        // default view file
        $viewFile = realpath(dirname(__FILE__) . '/../views/field-tpl-js.php');

        // and render
        $this->owner->renderInternal($viewFile, compact('model', 'fieldType', 'list')); 
    }

    public function _addReadOnlyAttributes(CEvent $event)
    {
        if ($event->sender->owner->tag === 'EMAIL') {
            if (in_array($event->params['attribute'], array('tag', 'required', 'visibility'))) {
                $event->params['htmlOptions']['readonly'] = 'readonly';    
            }
        }
    }
    
}