<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CustomerGroupModelHandlerBehavior
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */
 
class CustomerGroupModelHandlerBehavior extends CBehavior
{
    private $_group;

    public $categoryName;
    
    public $exceptAttributes = array();
    
    public function save()
    {
        if (!$this->getOwner()->validate() || !$this->getGroup() || !$this->getGroup()->group_id) {
            return false;
        }

        try {
            
            foreach ($this->getAttributesList() as $attributeName => $attributeValue) {
                $code = $this->categoryName . '.' . $attributeName;
                $option = CustomerGroupOption::model()->findByAttributes(array(
                    'group_id'  => $this->getGroup()->group_id,
                    'code'      => $code,
                ));
                if (empty($option)) {
                    $option = new CustomerGroupOption();
                    $option->group_id = $this->getGroup()->group_id;
                    $option->code = $code;
                }
                $option->value = $attributeValue;
                if (!$option->save()) {
                    throw new Exception(CHtml::errorSummary($option));
                }
            }
            
        } catch (Exception $e) {
            return false;
        }
        
        return true;   
    }
    
    public function setGroup(CustomerGroup $group)
    {
        $this->_group = $group;
        if (!empty($this->_group->group_id)) {
            $codes = array();
            foreach ($this->getAttributesList() as $key => $value) {
                $codes[] = $this->categoryName . '.' . $key;
            }
            $criteria = new CDbCriteria();
            $criteria->compare('group_id', (int)$this->_group->group_id);
            $criteria->addInCondition('code', $codes);
            $options = CustomerGroupOption::model()->findAll($criteria);
            foreach ($options as $option) {
                $attributeName = explode('.', $option->code);
                $attributeName = end($attributeName);
                $this->getOwner()->$attributeName = $option->value;
            }
        }
        return $this;
    }
    
    public function getGroup()
    {
        return $this->_group;
    }
    
    public function getAttributesList()
    {
        $attributes = $this->getOwner()->getAttributes();
        foreach ($attributes as $key => $value) {
            if (in_array($key, $this->exceptAttributes)) {
                unset($attributes[$key]);
            }
        }
        return $attributes;
    }
}