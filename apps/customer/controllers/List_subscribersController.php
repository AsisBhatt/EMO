<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * List_subscribersController
 *
 * Handles the actions for list subscribers related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class List_subscribersController extends Controller
{
    public function init()
    {
        $this->getData('pageScripts')->add(array('src' => AssetsUrl::js('subscribers.js')));
        parent::init();
    }

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
        return CMap::mergeArray(array(
            'postOnly + delete, subscribe, unsubscribe, disable, bulk_action',
        ), parent::filters());
    }

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
     * List available subscribers for a list
     */
    public function actionIndex($list_uid)
    {
        $list       = $this->loadListModel($list_uid);
        $request    = Yii::app()->request;
        $postFilter = (array)$request->getPost('filter', array());
        $subscriber = new ListSubscriber();

        $subscriberStatusesList = $subscriber->getFilterStatusesList();
        
        // since 1.3.6.2
        // filters
        $getFilterSet = false;
        $getFilter = array(
            'campaigns' => array(
                'campaign' => null,
                'action'   => null,
                'atu'      => null, // action time unit
                'atuc'     => null, // action time unit count
            )
        );
        if ($request->getQuery('filter') && is_array($request->getQuery('filter'))) {
            $getFilter = CMap::mergeArray($getFilter, $request->getQuery('filter'));
            $getFilterSet = true;
        }
        
        // list campaigns for filters
        $criteria = new CDbCriteria();
        $criteria->select = 'campaign_id, name';
        $criteria->compare('list_id', $list->list_id);
        $criteria->addInCondition('status', array(Campaign::STATUS_SENT, Campaign::STATUS_SENDING));
        $criteria->order = 'campaign_id DESC';
        $campaigns = Campaign::model()->findAll($criteria);
        
        $listCampaigns = array();
        foreach ($campaigns as $campaign) {
            $listCampaigns[$campaign->campaign_id] = $campaign->name;
        }
        //
        
        /**
         * NOTE:
         * Following criteria will use filesort and create a temp table because of the group by condition.
         * So far, beside subqueries this is the only optimal way i have found to work fine.
         * Needs optimization in the future if will cause problems.
         */
        $criteria = new CDbCriteria();
        $criteria->select = 'COUNT(DISTINCT t.subscriber_id) as counter';
        $criteria->compare('t.list_id', $list->list_id);
        $criteria->order = 't.subscriber_id DESC';
        
        // since 1.3.6.2
        if (!empty($getFilter['campaigns']['action'])) {
            $action      = $getFilter['campaigns']['action'];
            $campaignId  = !empty($getFilter['campaigns']['campaign']) ? (int)$getFilter['campaigns']['campaign'] : 0;
            $campaignIds = empty($campaignId) ? array_keys($listCampaigns) : array($campaignId);
            $campaignIds = array_map('intval', $campaignIds);
            $atu  = $subscriber->getFilterTimeUnitValueForDb(!empty($getFilter['campaigns']['atu']) ? (int)$getFilter['campaigns']['atu'] : 0);
            $atuc = !empty($getFilter['campaigns']['atuc']) ? (int)$getFilter['campaigns']['atuc'] : 0;
            $atuc = $atuc > 1024 ? 1024 : $atuc;
            $atuc = $atuc < 0 ? 0 : $atuc;
            
            if (in_array($action, array(ListSubscriber::CAMPAIGN_FILTER_ACTION_DID_OPEN, ListSubscriber::CAMPAIGN_FILTER_ACTION_DID_NOT_OPEN))) {
                $rel = array(
                    'select'   => false,
                    'together' => true,
                );
                
                if ($action == ListSubscriber::CAMPAIGN_FILTER_ACTION_DID_OPEN) {
                    $rel['joinType']  = 'INNER JOIN';
                    $rel['condition'] = 'trackOpens.campaign_id IN (' . implode(',', $campaignIds) . ')';
                    if (!empty($atuc)) {
                        $rel['condition'] .= sprintf(' AND trackOpens.date_added >= DATE_SUB(NOW(), INTERVAL %d %s)', $atuc, $atu);
                    }
                } else {
                    $rel['on']        = 'trackOpens.campaign_id IN (' . implode(',', $campaignIds) . ')';
                    $rel['joinType']  = 'LEFT OUTER JOIN';
                    $rel['condition'] = 'trackOpens.subscriber_id IS NULL';
                    if (!empty($atuc)) {
                        $rel['condition'] .= sprintf(' OR (trackOpens.subscriber_id IS NOT NULL AND (SELECT date_added FROM {{campaign_track_open}} WHERE subscriber_id = trackOpens.subscriber_id ORDER BY date_added DESC LIMIT 1) <= DATE_SUB(NOW(), INTERVAL %d %s))', $atuc, $atu);
                    }
                }
                
                $criteria->with['trackOpens'] = $rel;
            }
            
            if (in_array($action, array(ListSubscriber::CAMPAIGN_FILTER_ACTION_DID_CLICK, ListSubscriber::CAMPAIGN_FILTER_ACTION_DID_NOT_CLICK))) {
                
                $ucriteria = new CDbCriteria();
                $ucriteria->select = 'url_id';
                $ucriteria->addInCondition('campaign_id', $campaignIds);
                $models = CampaignUrl::model()->findAll($ucriteria);
                $urlIds = array();
                foreach ($models as $model) {
                    $urlIds[] = $model->url_id;
                }

                if (empty($urlIds)) {
                    $urlIds = array(0);
                }
                
                $rel = array(
                    'select'   => false,
                    'together' => true,
                );

                if ($action == ListSubscriber::CAMPAIGN_FILTER_ACTION_DID_CLICK) {
                    $rel['joinType']  = 'INNER JOIN';
                    $rel['condition'] = 'trackUrls.url_id IN (' . implode(',', $urlIds) . ')';
                    if (!empty($atuc)) {
                        $rel['condition'] .= sprintf(' AND trackUrls.date_added >= DATE_SUB(NOW(), INTERVAL %d %s)', $atuc, $atu);
                    }
                } else {
                    $rel['on']        = 'trackUrls.url_id IN (' . implode(',', $urlIds) . ')';
                    $rel['joinType']  = 'LEFT OUTER JOIN';
                    $rel['condition'] = 'trackUrls.subscriber_id IS NULL';
                    if (!empty($atuc)) {
                        $rel['condition'] .= sprintf(' OR (trackUrls.subscriber_id IS NOT NULL AND (SELECT date_added FROM {{campaign_track_url}} WHERE subscriber_id = trackUrls.subscriber_id ORDER BY date_added DESC LIMIT 1) <= DATE_SUB(NOW(), INTERVAL %d %s))', $atuc, $atu);
                    }
                }

                $criteria->with['trackUrls'] = $rel;
            }
        }
        //
        
        foreach ($postFilter as $field_id => $value) {
            if (empty($value)) {
                unset($postFilter[$field_id]);
                continue;
            }

            if (is_numeric($field_id)) {
                $model = ListField::model()->findByAttributes(array(
                    'field_id'  => $field_id,
                    'list_id'   => $list->list_id,
                ));
                if (empty($model)) {
                    unset($postFilter[$field_id]);
                }
            }
        }

        if (!empty($postFilter['status']) && in_array($postFilter['status'], array_keys($subscriberStatusesList))) {
            $criteria->compare('status', $postFilter['status']);
        }

        if (!empty($postFilter['uid']) && strlen($postFilter['uid']) == 13) {
            $criteria->compare('subscriber_uid', $postFilter['uid']);
        }

        if (!empty($postFilter)) {

            $with = array();
            foreach ($postFilter as $field_id => $value) {
                if (!is_numeric($field_id)) {
                    continue;
                }

                $i = (int)$field_id;
                $with['fieldValues'.$i] = array(
                    'select'    => false,
                    'together'  => true,
                    'joinType'  => 'INNER JOIN',
                    'condition' => '`fieldValues'.$i.'`.`field_id` = :field_id'.$i.' AND `fieldValues'.$i.'`.`value` LIKE :value'.$i,
                    'params'    => array(
                        ':field_id'.$i  => (int)$field_id,
                        ':value'.$i     => '%'.$value.'%',
                    ),
                );
            }

            $md = $subscriber->getMetaData();
            foreach ($postFilter as $field_id => $value) {
                if (!is_numeric($field_id)) {
                    continue;
                }
                if ($md->hasRelation('fieldValues'.$field_id)) {
                    continue;
                }
                $md->addRelation('fieldValues'.$field_id, array(ListSubscriber::HAS_MANY, 'ListFieldValue', 'subscriber_id'));
            }

            if (!empty($with)) {
                $criteria->with = $with;
            }
        }

        // count all confirmed subscribers of this list
        $count = $subscriber->count($criteria);

        // instantiate the pagination and apply the limit statement to the query
        $pages = new CPagination($count);
        $pages->pageSize = (int)$subscriber->paginationOptions->getPageSize();
        $pages->applyLimit($criteria);

        // load the required models
        $criteria->select = 't.list_id, t.subscriber_id, t.subscriber_uid, t.ip_address, t.status, t.date_added';
        $criteria->group = 't.subscriber_id';
        $subscribers = $subscriber->findAll($criteria);

        // now, we need to know what columns this list has, that is, all the tags available for this list.
        $columns = array();
        $rows = array();

        $criteria = new CDbCriteria();
        $criteria->compare('t.list_id', $list->list_id);
        $criteria->order = 't.sort_order ASC';

        $fields = ListField::model()->findAll($criteria);

        $columns[] = array(
            'label'     => null,
            'field_type'=> 'checkbox',
            'field_id'  => 'bulk_select',
            'value'     => null,
            'checked'   => false,
            'htmlOptions'   => array(),
        );

        $columns[] = array(
            'label'         => Yii::t('app', 'Options'),
            'field_type'    => null,
            'field_id'      => null,
            'value'         => null,
            'htmlOptions'   => array('class' => 'empty-options-header'),
        );

        $columns[] = array(
            'label'     => Yii::t('list_subscribers', 'Unique ID'),
            'field_type'=> 'text',
            'field_id'  => 'uid',
            'value'     => isset($postFilter['uid']) ? CHtml::encode($postFilter['uid']) : null,
        );
        
        $columns[] = array(
            'label'         => Yii::t('app', 'Date added'),
            'field_type'    => null,
            'field_id'      => 'date_added',
            'value'         => null,
            'htmlOptions'   => array('class' => 'subscriber-date-added'),
        );

        $columns[] = array(
            'label'         => Yii::t('app', 'Ip address'),
            'field_type'    => null,
            'field_id'      => 'ip_address',
            'value'         => null,
            'htmlOptions'   => array('class' => 'subscriber-date-added'),
        );

        $columns[] = array(
            'label'     => Yii::t('app', 'Status'),
            'field_type'=> 'select',
            'field_id'  => 'status',
            'value'     => isset($postFilter['status']) ? CHtml::encode($postFilter['status']) : null,
            'options'   => CMap::mergeArray(array('' => Yii::t('app', 'Choose')), $subscriberStatusesList),
        );

        foreach ($fields as $field) {
            $columns[] = array(
                'label'     => $field->label,
                'field_type'=> 'text',
                'field_id'  => $field->field_id,
                'value'     => isset($postFilter[$field->field_id]) ? CHtml::encode($postFilter[$field->field_id]) : null,
            );
        }

        foreach ($subscribers as $index => $subscriber) {
            $subscriberRow = array('columns' => array());

            // checkbox
            $subscriberRow['columns'][] = ($list->customer_id != 0 ? CHtml::checkBox('bulk_select[]', false, array('value' => $subscriber->subscriber_id, 'class' => 'bulk-select')) : '');
			
            $actions = array();
			if($list->customer_id != 0){
				$actions[] = CHtml::link(' <span class="glyphicon glyphicon-envelope"><!-- --></span> ', array('list_subscribers/campaigns', 'list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid), array('title' => Yii::t('app', 'Campaigns sent to this subscriber')));
			
				$actions[] = CHtml::link(' <span class="glyphicon glyphicon-pencil"><!-- --></span> ', array('list_subscribers/update', 'list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid), array('title' => Yii::t('app', 'Update')));
			}
            if ($subscriber->getCanBeUnsubscribed() && $subscriber->isConfirmed && $list->customer_id != 0) {
                $actions[] = CHtml::link(' <span class="glyphicon glyphicon-log-out"><!-- --></span> ', array('list_subscribers/unsubscribe', 'list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid), array('class' => 'unsubscribe', 'title' => Yii::t('app', 'Unsubscribe'), 'data-message' => Yii::t('list_subscribers', 'Are you sure you want to unsubscribe this subscriber?')));
            } elseif ($subscriber->getCanBeConfirmed() && $subscriber->isUnconfirmed) {
                $actions[] = CHtml::link(' <span class="glyphicon glyphicon-log-in"><!-- --></span> ', array('list_subscribers/subscribe', 'list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid), array('class' => 'subscribe', 'title' => Yii::t('list_subscribers', 'Subscribe back'), 'data-message' => Yii::t('list_subscribers', 'Are you sure you want to subscribe back this unsubscriber?')));
            } elseif ($subscriber->getCanBeConfirmed() && $subscriber->isUnsubscribed) {
                $actions[] = CHtml::link(' <span class="glyphicon glyphicon-log-in"><!-- --></span> ', array('list_subscribers/subscribe', 'list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid), array('class' => 'subscribe', 'title' => Yii::t('list_subscribers', 'Confirm subscriber'), 'data-message' => Yii::t('list_subscribers', 'Are you sure you want to confirm this subscriber?')));
            } elseif ($subscriber->getCanBeConfirmed() && $subscriber->isUnapproved) {
                $actions[] = CHtml::link(' <span class="glyphicon glyphicon-log-in"><!-- --></span> ', array('list_subscribers/subscribe', 'list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid), array('class' => 'subscribe', 'title' => Yii::t('list_subscribers', 'Approve subscriber'), 'data-message' => Yii::t('list_subscribers', 'Are you sure you want to approve this subscriber?')));
            } elseif ($subscriber->getCanBeConfirmed() && $subscriber->isDisabled) {
                $actions[] = CHtml::link(' <span class="glyphicon glyphicon-log-in"><!-- --></span> ', array('list_subscribers/subscribe', 'list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid), array('class' => 'subscribe', 'title' => Yii::t('list_subscribers', 'Enable subscriber'), 'data-message' => Yii::t('list_subscribers', 'This subscriber has been disabled, are you sure you want to enable it back?')));
            }

            if ($subscriber->getCanBeDisabled() && $list->customer_id != 0) {
                $actions[] = CHtml::link(' <span class="glyphicon glyphicon-remove"><!-- --></span> ', array('list_subscribers/disable', 'list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid), array('class' => 'unsubscribe', 'title' => Yii::t('list_subscribers', 'Disable subscriber'), 'data-message' => Yii::t('list_subscribers', 'Are you sure you want to disable this subscriber?')));
            }

            if ($subscriber->getCanBeDeleted() && $list->customer_id != 0) {
                $actions[] = CHtml::link(' <span class="glyphicon glyphicon-remove-circle"><!-- --></span> ', array('list_subscribers/delete', 'list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid), array('class' => 'delete', 'title' => Yii::t('app', 'Delete'), 'data-message' => Yii::t('app', 'Are you sure you want to delete this item? There is no coming back after you do it.')));
            }

            $subscriberRow['columns'][] = implode(str_repeat('&nbsp;', 3), $actions);
            $subscriberRow['columns'][] = CHtml::link($subscriber->subscriber_uid, Yii::app()->createUrl('list_subscribers/update', array('list_uid' => $list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid)));
            $subscriberRow['columns'][] = $subscriber->dateAdded;
            $subscriberRow['columns'][] = $subscriber->ip_address;
            $subscriberRow['columns'][] = $subscriber->getGridViewHtmlStatus();

            foreach ($fields as $field) {
                $criteria = new CDbCriteria();
                $criteria->select = 't.value';
                $criteria->compare('field_id', $field->field_id);
                $criteria->compare('subscriber_id', $subscriber->subscriber_id);
                $values = ListFieldValue::model()->findAll($criteria);

                $value = array();
                foreach ($values as $val) {
                    $value[] = $val->value;
                }

                $subscriberRow['columns'][] = CHtml::encode(implode(', ', $value));
            }

            if (count($subscriberRow['columns']) == count($columns)) {
                $rows[] = $subscriberRow;
            }
        }

        if ($request->isPostRequest && $request->isAjaxRequest) {
            return $this->renderPartial('_list', compact('list', 'subscriber', 'columns', 'rows', 'pages', 'count'));
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('list_subscribers', 'Your mail list subscribers'),
            'pageHeading'       => Yii::t('list_subscribers', 'List subscribers'),
            'pageBreadcrumbs'   => array(
                Yii::t('lists', 'Lists') => $this->createUrl('lists/index'),
                $list->name => $this->createUrl('lists/overview', array('list_uid' => $list->list_uid)),
                Yii::t('list_subscribers', 'Subscribers') => $this->createUrl('list_subscribers/index', array('list_uid' => $list->list_uid)),
                Yii::t('app', 'View all')
            )
        ));

        $subBulkFromSource = new ListSubscriberBulkFromSource();
        $subBulkFromSource->list_id = $list->list_id;

        $this->render('index', compact('list', 'subscriber', 'columns', 'rows', 'pages', 'count', 'subBulkFromSource', 'getFilter', 'getFilterSet', 'listCampaigns'));
    }

    /**
     * Create / Add a new subscriber in a list
     */
    public function actionCreate($list_uid)
    {
        $list       = $this->loadListModel($list_uid);
        $request    = Yii::app()->request;
        $hooks      = Yii::app()->hooks;

        $listFields = ListField::model()->findAll(array(
            'condition' => 'list_id = :lid',
            'params'    => array(':lid' => $list->list_id),
            'order'     => 'sort_order ASC'
        ));

        if (empty($listFields)) {
            throw new CHttpException(404, Yii::t('list_fields', 'Your mail list does not have any field defined.'));
        }

        $usedTypes = array();
        foreach ($listFields as $field) {
            $usedTypes[] = $field->type->type_id;
        }
		
        $criteria = new CDbCriteria();
        $criteria->addInCondition('type_id', $usedTypes);
        $types = ListFieldType::model()->findAll($criteria);

        $subscriber = new ListSubscriber();
        $subscriber->list_id = $list->list_id;

        $instances = array();

        foreach ($types as $type) {

            if (empty($type->identifier) || !is_file(Yii::getPathOfAlias($type->class_alias).'.php')) {
                continue;
            }

            $component = Yii::app()->getWidgetFactory()->createWidget($this, $type->class_alias, array(
                'fieldType'     => $type,
                'list'          => $list,
                'subscriber'    => $subscriber,
            ));

            if (!($component instanceof FieldBuilderType)) {
                continue;
            }

            // run the component to hook into next events
            $component->run();

            $instances[] = $component;
        }

        $fields = array();

        // if the fields are saved
        if ($request->isPostRequest) {
            $transaction = Yii::app()->db->beginTransaction();

            try {

                $customer                = $list->customer;
                $maxSubscribersPerList   = (int)$customer->getGroupOption('lists.max_subscribers_per_list', -1);
                $maxSubscribers          = (int)$customer->getGroupOption('lists.max_subscribers', -1);

                if ($maxSubscribers > -1 || $maxSubscribersPerList > -1) {
                    $criteria = new CDbCriteria();
                    $criteria->select = 'COUNT(DISTINCT(t.email)) as counter';

                    if ($maxSubscribers > -1 && ($listsIds = $customer->getAllListsIds())) {
                        $criteria->addInCondition('t.list_id', $listsIds);
                        $totalSubscribersCount = ListSubscriber::model()->count($criteria);
                        if ($totalSubscribersCount >= $maxSubscribers) {
                            throw new Exception(Yii::t('lists', 'You have reached the maximum number of allowed subscribers.'));
                        }
                    }

                    if ($maxSubscribersPerList > -1) {
                        $criteria->compare('t.list_id', (int)$list->list_id);
                        $listSubscribersCount = ListSubscriber::model()->count($criteria);
                        if ($listSubscribersCount >= $maxSubscribersPerList) {
                            throw new Exception(Yii::t('lists', 'You have reached the maximum number of allowed subscribers into this list.'));
                        }
                    }
                }

                $attributes = (array)$request->getPost($subscriber->modelName, array());
                if (empty($subscriber->ip_address)) {
                    $subscriber->ip_address = Yii::app()->request->getUserHostAddress();
                }
                if (isset($attributes['status']) && in_array($attributes['status'], array_keys($subscriber->getStatusesList()))) {
                    $subscriber->status = $attributes['status'];
                } else {
                    $subscriber->status = ListSubscriber::STATUS_UNCONFIRMED;
                }

                if (!$subscriber->save()) {
                    throw new Exception(Yii::t('app', 'Temporary error, please contact us if this happens too often!'));
                }
				
				CommonHelper::setActivityLogs('List Subscriber Create '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$subscriber->subscriber_id,$subscriber->tableName(),'List Subscriber Create',(int)Yii::app()->customer->getId());

                // raise event
                $this->callbacks->onSubscriberSave(new CEvent($this->callbacks, array(
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
                $this->callbacks->onSubscriberSaveSuccess(new CEvent($this->callbacks, array(
                    'instances'     => $instances,
                    'subscriber'    => $subscriber,
                    'list'          => $list,
                )));

                $transaction->commit();

            } catch (Exception $e) {

                $transaction->rollBack();
                Yii::app()->notify->addError($e->getMessage());

                // bind default save error event handler
                $this->callbacks->onSubscriberSaveError = array($this->callbacks, '_collectAndShowErrorMessages');

                // raise event
                $this->callbacks->onSubscriberSaveError(new CEvent($this->callbacks, array(
                    'instances'     => $instances,
                    'subscriber'    => $subscriber,
                    'list'          => $list
                )));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'   => $this,
                'success'      => Yii::app()->notify->hasSuccess,
                'subscriber'   => $subscriber,
            )));

            if ($collection->success) {
                if ($request->getPost('next_action') && $request->getPost('next_action') == 'create-new') {
                    $this->redirect(array('list_subscribers/create', 'list_uid' => $subscriber->list->list_uid));
                }
                $this->redirect(array('list_subscribers/update', 'list_uid' => $subscriber->list->list_uid, 'subscriber_uid' => $subscriber->subscriber_uid));
            }
        }

        // raise event. simply the fields are shown
        $this->callbacks->onSubscriberFieldsDisplay(new CEvent($this->callbacks, array(
            'fields' => &$fields,
        )));

        // add the default sorting of fields actions and raise the event
        $this->callbacks->onSubscriberFieldsSorting = array($this->callbacks, '_orderFields');
        $this->callbacks->onSubscriberFieldsSorting(new CEvent($this->callbacks, array(
            'fields' => &$fields,
        )));

        // and build the html for the fields.
        $fieldsHtml = '';
        foreach ($fields as $type => $field) {
            $fieldsHtml .= $field['field_html'];
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('list_subscribers', 'Add a new subscriber to your list.'),
            'pageHeading'       => Yii::t('list_subscribers', 'Add a new subscriber to your list.'),
            'pageBreadcrumbs'   => array(
                Yii::t('lists', 'Lists') => $this->createUrl('lists/index'),
                $list->name => $this->createUrl('lists/overview', array('list_uid' => $list->list_uid)),
                Yii::t('list_subscribers', 'Subscribers') => $this->createUrl('list_subscribers/index', array('list_uid' => $list->list_uid)),
                Yii::t('app', 'Create new')
            )
        ));
		//print_r($fieldsHtml);exit;
        $this->render('form', compact('fieldsHtml', 'list', 'subscriber'));
    }

    /**
     * Update existing list subscriber
     */
    public function actionUpdate($list_uid, $subscriber_uid)
    {
        $list       = $this->loadListModel($list_uid);
        $subscriber = $this->loadSubscriberModel($list->list_id, $subscriber_uid);

        $request = Yii::app()->request;
        $hooks = Yii::app()->hooks;

        $listFields = ListField::model()->findAll(array(
            'condition' => 'list_id = :lid',
            'params'    => array(':lid' => $list->list_id),
            'order'     => 'sort_order ASC'
        ));

        if (empty($listFields)) {
            throw new CHttpException(404, Yii::t('list', 'Your mail list does not have any field defined.'));
        }

        $usedTypes = array();
        foreach ($listFields as $field) {
            $usedTypes[] = $field->type->type_id;
        }
        $criteria = new CDbCriteria();
        $criteria->addInCondition('type_id', $usedTypes);
        $types = ListFieldType::model()->findAll($criteria);

        $instances = array();

        foreach ($types as $type) {

            if (empty($type->identifier) || !is_file(Yii::getPathOfAlias($type->class_alias).'.php')) {
                continue;
            }

            $component = Yii::app()->getWidgetFactory()->createWidget($this, $type->class_alias, array(
                'fieldType'     => $type,
                'list'          => $list,
                'subscriber'    => $subscriber,
            ));

            if (!($component instanceof FieldBuilderType)) {
                continue;
            }

            // run the component to hook into next events
            $component->run();

            $instances[] = $component;
        }

        $fields = array();

        // if the fields are saved
        if ($request->isPostRequest) {

            $transaction = Yii::app()->db->beginTransaction();

            try {

                $attributes = (array)$request->getPost($subscriber->modelName, array());
                if (empty($subscriber->ip_address)) {
                    $subscriber->ip_address = Yii::app()->request->getUserHostAddress();
                }
                if (isset($attributes['status']) && in_array($attributes['status'], array_keys($subscriber->getStatusesList()))) {
                    $subscriber->status = $attributes['status'];
                } else {
                    $subscriber->status = ListSubscriber::STATUS_UNCONFIRMED;
                }

                // since 1.3.5
                if ($subscriber->status == ListSubscriber::STATUS_CONFIRMED) {
                    if (Yii::app()->customer->getModel()->getGroupOption('lists.can_mark_blacklisted_as_confirmed', 'yes') === 'yes') {
                        $subscriber->removeFromBlacklistByEmail();
                    }
                }

                if (!$subscriber->save()) {
                    throw new Exception(Yii::t('app', 'Temporary error, please contact us if this happens too often!'));
                }
				
				CommonHelper::setActivityLogs('List Subscriber Update '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$subscriber->subscriber_id,$subscriber->tableName(),'List Subscriber Update',(int)Yii::app()->customer->getId());

                // raise event
                $this->callbacks->onSubscriberSave(new CEvent($this->callbacks, array(
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
                $this->callbacks->onSubscriberSaveSuccess(new CEvent($this->callbacks, array(
                    'instances'     => $instances,
                    'subscriber'    => $subscriber,
                    'list'          => $list,
                )));

                $transaction->commit();

            } catch (Exception $e) {

                $transaction->rollBack();
                Yii::app()->notify->addError($e->getMessage());

                // bind default save error event handler
                $this->callbacks->onSubscriberSaveError = array($this->callbacks, '_collectAndShowErrorMessages');

                // raise event
                $this->callbacks->onSubscriberSaveError(new CEvent($this->callbacks, array(
                    'instances'     => $instances,
                    'subscriber'    => $subscriber,
                    'list'          => $list
                )));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'   => $this,
                'success'      => Yii::app()->notify->hasSuccess,
                'subscriber'   => $subscriber,
            )));

            if ($collection->success) {
                if ($request->getPost('next_action') && $request->getPost('next_action') == 'create-new') {
                    $this->redirect(array('list_subscribers/create', 'list_uid' => $subscriber->list->list_uid));
                }
            }
        }

        // raise event. simply the fields are shown
        $this->callbacks->onSubscriberFieldsDisplay(new CEvent($this->callbacks, array(
            'fields' => &$fields,
        )));

        // add the default sorting of fields actions and raise the event
        $this->callbacks->onSubscriberFieldsSorting = array($this->callbacks, '_orderFields');
        $this->callbacks->onSubscriberFieldsSorting(new CEvent($this->callbacks, array(
            'fields' => &$fields,
        )));

        // and build the html for the fields.
        $fieldsHtml = '';
        foreach ($fields as $type => $field) {
            $fieldsHtml .= $field['field_html'];
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | ' . Yii::t('list_subscribers', 'Update existing list subscriber.'),
            'pageHeading'       => Yii::t('list_subscribers', 'Update existing list subscriber.'),
            'pageBreadcrumbs'   => array(
                Yii::t('lists', 'Lists') => $this->createUrl('lists/index'),
                $list->name => $this->createUrl('lists/overview', array('list_uid' => $list->list_uid)),
                Yii::t('list_subscribers', 'Subscribers') => $this->createUrl('list_subscribers/index', array('list_uid' => $list->list_uid)),
                Yii::t('app', 'Update')
            )
        ));

        $this->render('form', compact('fieldsHtml', 'list', 'subscriber'));
    }

    /**
     * Campaigns sent to this subscriber
     */
    public function actionCampaigns($list_uid, $subscriber_uid)
    {
        $list       = $this->loadListModel($list_uid);
        $subscriber = $this->loadSubscriberModel($list->list_id, $subscriber_uid);
        $request    = Yii::app()->request;

        $model = new CampaignDeliveryLog('search');
        $model->campaign_id   = -1;
        $model->subscriber_id = (int)$subscriber->subscriber_id;
        $model->status        = null;

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('list_subscribers', 'Subscriber campaigns'),
            'pageHeading'     => Yii::t('list_subscribers', 'Subscriber campaigns'),
            'pageBreadcrumbs' => array(
                Yii::t('lists', 'Lists') => $this->createUrl('lists/index'),
                $list->name => $this->createUrl('lists/overview', array('list_uid' => $list->list_uid)),
                Yii::t('list_subscribers', 'Subscribers') => $this->createUrl('list_subscribers/index', array('list_uid' => $list->list_uid)),
                Yii::t('list_subscribers', 'Campaigns') => $this->createUrl('list_subscribers/campaigns', array('list_uid' => $list_uid, 'subscriber_uid' => $subscriber_uid)),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('campaigns', compact('model', 'list', 'subscriber'));
    }

    /**
     * Delete existing list subscriber
     */
    public function actionDelete($list_uid, $subscriber_uid)
    {
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $list       = $this->loadListModel($list_uid);
        $subscriber = $this->loadSubscriberModel($list->list_id, $subscriber_uid);

        if ($subscriber->canBeDeleted) {
			
			CommonHelper::setActivityLogs('List Subscriber Delete '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$subscriber->subscriber_id,$subscriber->tableName(),'List Subscriber Delete',(int)Yii::app()->customer->getId());
			
            $subscriber->delete();
            if ($logAction = Yii::app()->customer->getModel()->asa('logAction')) {
                $logAction->subscriberDeleted($subscriber);
            }
        }

        $redirect = null;
        if (!$request->isAjaxRequest) {
            $notify->addSuccess(Yii::t('list_subscribers', 'Your list subscriber was successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('list_subscribers/index', 'list_uid' => $list->list_uid));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'list'       => $list,
            'subscriber' => $subscriber,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }

    /**
     * Disable existing list subscriber
     */
    public function actionDisable($list_uid, $subscriber_uid)
    {
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $list       = $this->loadListModel($list_uid);
        $subscriber = $this->loadSubscriberModel($list->list_id, $subscriber_uid);

        if ($subscriber->getCanBeDisabled()) {
			
			CommonHelper::setActivityLogs('List Subscriber Disable '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$subscriber->subscriber_id,$subscriber->tableName(),'List Subscriber Disable',(int)Yii::app()->customer->getId());
            
			$subscriber->saveStatus(ListSubscriber::STATUS_DISABLED);
        }

        if (!$request->isAjaxRequest) {
            $notify->addSuccess(Yii::t('list_subscribers', 'Your list subscriber was successfully disabled!'));
            $this->redirect($request->getPost('returnUrl', array('list_subscribers/index', 'list_uid' => $list->list_uid)));
        }
    }
    
    /**
     * Unsubscribe existing list subscriber
     */
    public function actionUnsubscribe($list_uid, $subscriber_uid)
    {
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $list       = $this->loadListModel($list_uid);
        $subscriber = $this->loadSubscriberModel($list->list_id, $subscriber_uid);

        if ($subscriber->getCanBeUnsubscribed()) {
			
			CommonHelper::setActivityLogs('List Subscriber Unsubscribe '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$subscriber->subscriber_id,$subscriber->tableName(),'List Subscriber Unsubscribe',(int)Yii::app()->customer->getId());
			
            $subscriber->saveStatus(ListSubscriber::STATUS_UNSUBSCRIBED);
        }

        if (!$request->isAjaxRequest) {
            $notify->addSuccess(Yii::t('list_subscribers', 'Your list subscriber was successfully unsubscribed!'));
            $this->redirect($request->getPost('returnUrl', array('list_subscribers/index', 'list_uid' => $list->list_uid)));
        }
    }

    /**
     * Subscribe existing list subscriber
     */
    public function actionSubscribe($list_uid, $subscriber_uid)
    {
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $list       = $this->loadListModel($list_uid);
        $subscriber = $this->loadSubscriberModel($list->list_id, $subscriber_uid);
        $oldStatus  = $subscriber->status;

        if ($subscriber->getCanBeApproved()) {
			
			CommonHelper::setActivityLogs('List Subscriber Subscribe '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$subscriber->subscriber_id,$subscriber->tableName(),'List Subscriber Subscribe',(int)Yii::app()->customer->getId());
			
            $subscriber->saveStatus(ListSubscriber::STATUS_CONFIRMED);
            $subscriber->handleApprove(true)->handleWelcome(true);
        } elseif ($subscriber->getCanBeConfirmed()) {
			
			CommonHelper::setActivityLogs('List Subscriber Subscribe '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$subscriber->subscriber_id,$subscriber->tableName(),'List Subscriber Subscribe',(int)Yii::app()->customer->getId());
			
            $subscriber->saveStatus(ListSubscriber::STATUS_CONFIRMED);
        }

        if (!$request->isAjaxRequest) {
            if ($oldStatus == ListSubscriber::STATUS_UNSUBSCRIBED) {
                $notify->addSuccess(Yii::t('list_subscribers', 'Your list unsubscriber was successfully subscribed back!'));
            } elseif ($oldStatus == ListSubscriber::STATUS_UNAPPROVED) {
                $notify->addSuccess(Yii::t('list_subscribers', 'Your list subscriber has been approved and notified!'));
            } else {
                $notify->addSuccess(Yii::t('list_subscribers', 'Your list subscriber has been confirmed!'));
            }
            $this->redirect($request->getPost('returnUrl', array('list_subscribers/index', 'list_uid' => $list->list_uid)));
        }
    }
    
    /**
     * Bulk actions
     */
    public function actionBulk_action($list_uid)
    {
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $list       = $this->loadListModel($list_uid);
        $subscriber = new ListSubscriber();
        $action     = $request->getPost('action');

        if (!in_array($action, array_keys($subscriber->getBulkActionsList()))) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        set_time_limit(0);

        $customer = Yii::app()->customer->getModel();

        $selectedSubscribers = (array)$request->getPost('bulk_select', array());
        $selectedSubscribers = array_values($selectedSubscribers);
        $selectedSubscribers = array_map('intval', $selectedSubscribers);

        // since 1.3.5.9
        $redirect = null;
        if (!$request->isAjaxRequest) {
            $redirect = $request->getPost('returnUrl', array('list_subscribers/index', 'list_uid' => $list->list_uid));
        }
        Yii::app()->hooks->doAction('controller_action_bulk_action', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'redirect'   => $redirect,
            'list'       => $list,
            'action'     => $action,
            'data'       => $selectedSubscribers,
        )));
        $selectedSubscribers = $collection->data;
        //

        if (!empty($selectedSubscribers)) {
            $criteria = new CDbCriteria();
            $criteria->compare('list_id', (int)$list->list_id);
            $criteria->addInCondition('subscriber_id', $selectedSubscribers);
            
            if ($action == ListSubscriber::BULK_SUBSCRIBE) {

                $statusNotIn          = array(ListSubscriber::STATUS_CONFIRMED, ListSubscriber::STATUS_MOVED);
                $canMarkBlAsConfirmed = $customer->getGroupOption('lists.can_mark_blacklisted_as_confirmed', 'no') === 'yes';
                if (!$canMarkBlAsConfirmed) {
                    $statusNotIn[] = ListSubscriber::STATUS_BLACKLISTED;
                }
                $criteria->addNotInCondition('status', $statusNotIn);
                $subscribers = ListSubscriber::model()->findAll($criteria);
                
                foreach ($subscribers as $subscriber) {
                    // save the flag here
                    $approve    = $subscriber->getIsUnapproved();
                    $initStatus = $subscriber->status;
                    
                    // confirm the subscriber
                    $subscriber->saveStatus(ListSubscriber::STATUS_CONFIRMED);
					
					CommonHelper::setActivityLogs('List Subscriber Bulk_action Subscribe '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$subscriber->subscriber_id,$subscriber->tableName(),'List Subscriber Bulk_action Subscribe',(int)Yii::app()->customer->getId());
                    
                    // and if the above flag is bool, proceed with approval stuff
                    if ($approve) {
                        $subscriber->handleApprove(true)->handleWelcome(true);
                    }
                    
                    // finally remove from blacklist
                    if ($canMarkBlAsConfirmed && $initStatus == ListSubscriber::STATUS_BLACKLISTED) {
                        $subscriber->removeFromBlacklistByEmail();
                    }
                    
                }

            } elseif ($action == ListSubscriber::BULK_UNSUBSCRIBE) {

                $criteria->addNotInCondition('status', array(ListSubscriber::STATUS_BLACKLISTED, ListSubscriber::STATUS_MOVED));
				$unsub_cnt = 0;
				foreach($criteria->params as $sub_key){
					
					if($unsub_cnt > 0 && is_numeric($sub_key)){
						CommonHelper::setActivityLogs('List Subscriber Bulk_action UnSubscribe '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$sub_key,$subscriber->tableName(),'List Subscriber Bulk_action UnSubscribe',(int)Yii::app()->customer->getId());
					}
					$unsub_cnt++;
				}
				
                $list_subscribers = ListSubscriber::model()->updateAll(array(
                    'status'        => ListSubscriber::STATUS_UNSUBSCRIBED,
                    'last_updated'  => new CDbExpression('NOW()'),
                ), $criteria);

            } elseif ($action == ListSubscriber::BULK_DISABLE) {
                
                $criteria->addInCondition('status', array(ListSubscriber::STATUS_CONFIRMED));
				$dib_cnt = 0;
				foreach($criteria->params as $sub_key){
					if($dib_cnt > 0 && is_numeric($sub_key)){
						CommonHelper::setActivityLogs('List Subscriber Bulk_action Disable '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$sub_key,$subscriber->tableName(),'List Subscriber Bulk_action Disable',(int)Yii::app()->customer->getId());
					}
					$dib_cnt++;
				}

                ListSubscriber::model()->updateAll(array(
                    'status'        => ListSubscriber::STATUS_DISABLED,
                    'last_updated'  => new CDbExpression('NOW()'),
                ), $criteria);
                
            } elseif ($action == ListSubscriber::BULK_DELETE) {
				$del_cnt = 0;
				foreach($criteria->params as $sub_key){
					if($del_cnt > 0){
						CommonHelper::setActivityLogs('List Subscriber Bulk_action Delete '.str_replace(array('{{','}}'),'',$subscriber->tableName()),$sub_key,$subscriber->tableName(),'List Subscriber Bulk_action Delete',(int)Yii::app()->customer->getId());
					}
					$del_cnt++;
				}

               ListSubscriber::model()->deleteAll($criteria);

            }
        }

        if (!$request->isAjaxRequest) {
            $notify->addSuccess(Yii::t('app', 'Bulk action completed successfully!'));
        }

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }

    public function actionBulk_from_source($list_uid)
    {
        set_time_limit(0);

        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $ioFilter   = Yii::app()->ioFilter;
        $list       = $this->loadListModel($list_uid);
        $model      = new ListSubscriberBulkFromSource();
        $redirect   = array('list_subscribers/index', 'list_uid' => $list_uid);

        $emailAddresses    = array();
        $model->attributes = (array)$request->getPost($model->modelName, array());

        if (!in_array($model->status, array_keys($model->getBulkActionsList()))) {
            $this->redirect($redirect);
        }

        if (!empty($model->bulk_from_text)) {
            $lines = explode("\n", $model->bulk_from_text);
            foreach ($lines as $line) {
                $emails = explode(',', $line);
                $emails = array_map('trim', $emails);
                foreach ($emails as $email) {
                    if (FilterVarHelper::email($email)) {
                        $emailAddresses[] = $email;
                    }
                }
            }
        }
        $emailAddresses = array_unique($emailAddresses);

        $model->bulk_from_file = CUploadedFile::getInstance($model, 'bulk_from_file');
        if (!empty($model->bulk_from_file)) {
            if (!$model->validate()) {
                $notify->addError($model->shortErrors->getAllAsString());
            } else {
                $file = new SplFileObject($model->bulk_from_file->tempName);
                $file->setFlags(SplFileObject::READ_CSV | SplFileObject::SKIP_EMPTY | SplFileObject::DROP_NEW_LINE | SplFileObject::READ_AHEAD);
                while (!$file->eof()) {
                    $row = $file->fgetcsv();
                    if (empty($row)) {
                        continue;
                    }
                    $row = $ioFilter->stripTags($ioFilter->xssClean($row));
                    foreach ($row as $value) {
                        if (empty($value)) {
                            continue;
                        }
                        $emails = explode(',', $value);
                        $emails = array_map('trim', $emails);
                        foreach ($emails as $email) {
                            if (FilterVarHelper::email($email)) {
                                $emailAddresses[] = $email;
                            }
                        }
                    }
                }
            }
        }
        $emailAddresses = array_unique($emailAddresses);

        $total = 0;
        while (!empty($emailAddresses)) {
            $emails = array_splice($emailAddresses, 0, 10);

            $criteria = new CDbCriteria();
            $criteria->compare('list_id', (int)$list->list_id);
            $criteria->addInCondition('email', $emails);

            if ($model->status == ListSubscriber::BULK_SUBSCRIBE) {

                $criteria->addNotInCondition('status', array(ListSubscriber::STATUS_BLACKLISTED));

                ListSubscriber::model()->updateAll(array(
                    'status'        => ListSubscriber::STATUS_CONFIRMED,
                    'last_updated'  => new CDbExpression('NOW()'),
                ), $criteria);

            } elseif ($model->status == ListSubscriber::BULK_UNSUBSCRIBE) {

                $criteria->addNotInCondition('status', array(ListSubscriber::STATUS_BLACKLISTED));

                ListSubscriber::model()->updateAll(array(
                    'status'        => ListSubscriber::STATUS_UNSUBSCRIBED,
                    'last_updated'  => new CDbExpression('NOW()'),
                ), $criteria);

            } elseif ($model->status == ListSubscriber::BULK_DISABLE) {

                $criteria->addInCondition('status', array(ListSubscriber::STATUS_CONFIRMED));

                ListSubscriber::model()->updateAll(array(
                    'status'        => ListSubscriber::STATUS_DISABLED,
                    'last_updated'  => new CDbExpression('NOW()'),
                ), $criteria);

            } elseif ($model->status == ListSubscriber::BULK_DELETE) {

               ListSubscriber::model()->deleteAll($criteria);

            }

            $total += count($emails);
        }
        $notify->addSuccess(Yii::t('list_subscribers', 'Action completed, {count} subscribers were affected!', array(
            '{count}'   => $total,
        )));

        $this->redirect($redirect);
    }

    /**
     * Helper method to load the list AR model
     */
    public function loadListModel($list_uid)
    {
        $model = Lists::model()->findByAttributes(array(
            'list_uid'      => $list_uid,
            'customer_id'   => array((int)Yii::app()->customer->getId(),"0"),
        ));

        if ($model === null) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        return $model;
    }

    /**
     * Helper method to load the list subscriber AR model
     */
    public function loadSubscriberModel($list_id, $subscriber_uid)
    {
        $model = ListSubscriber::model()->findByAttributes(array(
            'subscriber_uid'    => $subscriber_uid,
            'list_id'           => (int)$list_id,
        ));

        if ($model === null) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        return $model;
    }
}
