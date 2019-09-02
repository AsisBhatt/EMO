<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CustomerGroupOptionSending
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.3
 */
 
class CustomerGroupOptionSmsSending extends OptionCustomerSmsSending
{
    public function behaviors()
    {
        $behaviors = array(
            'handler' => array(
                'class'         => 'backend.components.behaviors.CustomerGroupModelHandlerBehavior',
                'categoryName'  => $this->_categoryName,
            ),
        );
        return CMap::mergeArray($behaviors, parent::behaviors());
    }

    public function save()
    {
        return $this->asa('handler')->save();
    }
    
    /*public function getGroupsList()
    {
        $groups = parent::getGroupsList();
        if ($group = $this->asa('handler')->getGroup()) {
            foreach ($groups as $groupId => $name) {
                if ($groupId == $group->group_id) {
                    unset($groups[$groupId]);
                    break;
                }
            }    
        }
        return $groups;
    }*/
}
