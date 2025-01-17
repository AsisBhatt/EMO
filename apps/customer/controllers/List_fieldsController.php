<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * List_fieldsController
 * 
 * Handles the actions for list fields related tasks
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class List_fieldsController extends Controller
{
    /**
     * List of behaviors attached to this controller
     * The behaviors are merged with the one from parent implementation
     */
    public function behaviors()
    {
        return CMap::mergeArray(array(
            'callbacks' => array(
                'class' => 'customer.components.behaviors.ListFieldsControllerCallbacksBehavior',
            ),
        ), parent::behaviors());
    }
    
    /**
     * Handle the CRUD actions for list fields
     */
    public function actionIndex($list_uid)
    {
        $list = $this->loadListModel($list_uid);

        // get a reference to all available field types.
        $fieldTypes = ListFieldType::model()->findAll();
        if (empty($fieldTypes)) {
            throw new CHttpException(400, Yii::t('list_fields', 'There is no field type defined yet, please contact the administrator.'));
        }

        $types = ListFieldType::model()->findAll();
        $instances = array();
        
        foreach ($types as $type) {
            
            if (empty($type->identifier) || !is_file(Yii::getPathOfAlias($type->class_alias).'.php')) {
                continue;
            }
            
            $component = Yii::app()->getWidgetFactory()->createWidget($this, $type->class_alias, array(
                'fieldType' => $type,
                'list'      => $list,
            ));
            
            if (!($component instanceof FieldBuilderType)) {
                continue;
            }
            
            // run the component to hook into next events
            $component->run();
            
            $instances[] = $component;
        }
        
        $hooks   = Yii::app()->hooks;
        $request = Yii::app()->request;
        $fields  = array();
        
        // if the fields are saved
        if ($request->isPostRequest) {
            
            $transaction = Yii::app()->db->beginTransaction();
            
            try {
                
                // raise event
                $this->callbacks->onListFieldsSave(new CEvent($this->callbacks, array(
                    'fields' => &$fields,
                )));
                
                // if no error thrown but still there are errors in any of the instances, stop.
                foreach ($instances as $instance) {
                    if (!empty($instance->errors)) {
                        throw new Exception(Yii::t('app', 'Your form has a few errors. Please fix them and try again!'));
                    }
                }
                
                // add the default success message
                Yii::app()->notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
                
                // raise event. at this point everything seems to be fine.
                $this->callbacks->onListFieldsSaveSuccess(new CEvent($this->callbacks, array(
                    'instances' => $instances,
                )));

                $transaction->commit();
                
            } catch (Exception $e) {
                
                $transaction->rollBack();
                Yii::app()->notify->addError($e->getMessage());
                
                // bind default save error event handler
                $this->callbacks->onSubscriberSaveError = array($this->callbacks, '_collectAndShowErrorMessages');
                
                // raise event
                $this->callbacks->onListFieldsSaveError(new CEvent($this->callbacks, array(
                    'instances' => $instances,
                )));
            }
        }
        
        // raise event. simply the fields are shown
        $this->callbacks->onListFieldsDisplay(new CEvent($this->callbacks, array(
            'fields' => &$fields,
        ))); 
        
        // add the default sorting of fields actions and raise the event
        $this->callbacks->onListFieldsSorting = array($this->callbacks, '_orderFields');
        $this->callbacks->onListFieldsSorting(new CEvent($this->callbacks, array(
            'fields' => &$fields,
        )));
        
        // and build the html for the fields.
        $fieldsHtml = '';
        foreach ($fields as $type => $field) {
            $fieldsHtml .= $field['field_html'];
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('list_fields', 'Your mail lists custom fields'),
            'pageHeading'       => Yii::t('list_fields', 'List custom fields'), 
            'pageBreadcrumbs'   => array(
                Yii::t('lists', 'Lists')    => $this->createUrl('lists/index'),
                $list->name                 => $this->createUrl('lists/overview', array('list_uid' => $list->list_uid)),
                Yii::t('list_fields', 'Custom fields')
            )
        ));
        
        $this->render('index', compact('fieldsHtml', 'list'));
    }
    
    /**
     * Helper method to load the list AR model
     */
    public function loadListModel($list_uid)
    {
        $model = Lists::model()->findByAttributes(array(
            'list_uid'      => $list_uid,
            'customer_id'   => (int)Yii::app()->customer->getId(),
        ));
        
        if ($model === null) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
        
        return $model;
    }
}