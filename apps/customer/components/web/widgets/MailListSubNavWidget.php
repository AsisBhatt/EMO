<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * MailListSubNavWidget
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class MailListSubNavWidget extends CWidget
{
    public $list;
    
    public function run()
    {
        if ($this->list->isNewRecord) {
            return;
        }
        
        $this->render('mail-list-sub-nav');
    }
    
    public function getNavItems()
    {
        $items = array(
            array(
                'label'     => Yii::t('lists', 'All lists'),
                'url'       => $this->controller->createUrl('lists/index'),
            ),
            array(
                'label' => Yii::t('lists', 'List overview'),
                'url'   => $this->controller->createUrl('lists/overview', array('list_uid' => $this->list->list_uid)),
            ),
            array(
                'label' => Yii::t('list_subscribers', 'List subscribers'),
                'url'   => $this->controller->createUrl('list_subscribers/index', array('list_uid' => $this->list->list_uid)),
            ),
            array(
                'label' => Yii::t('list_fields', 'List custom fields'),
                'url'   => $this->controller->createUrl('list_fields/index', array('list_uid' => $this->list->list_uid)),
            ),
            array(
                'label' => Yii::t('list_pages', 'List pages'),
                'url'   => $this->controller->createUrl('list_page/index', array('list_uid' => $this->list->list_uid, 'type' => 'subscribe-form')),
            ),
            array(
                'label' => Yii::t('list_forms', 'List embed forms'),
                'url'   => $this->controller->createUrl('list_forms/index', array('list_uid' => $this->list->list_uid)),
            ),
            array(
                'label' => Yii::t('list_segments', 'List segments'),
                'url'   => $this->controller->createUrl('list_segments/index', array('list_uid' => $this->list->list_uid)),
            ),
            array(
                'label' => Yii::t('lists', 'Update list'),
                'url'   => $this->controller->createUrl('lists/update', array('list_uid' => $this->list->list_uid)),
            )
        );
        
        if (!(Yii::app()->customer->getModel()->getGroupOption('lists.can_segment_lists', 'yes') == 'yes')) {
            unset($items[6]);
        }

        return $items;
    }
}