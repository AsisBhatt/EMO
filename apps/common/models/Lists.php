<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * Lists
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

/**
 * This is the model class for table "list".
 *
 * The followings are the available columns in table 'list':
 * @property integer $list_id
 * @property integer $customer_id
 * @property string $list_uid
 * @property string $name
 * @property string $display_name
 * @property string $description
 * @property string $visibility
 * @property string $opt_in
 * @property string $opt_out
 * @property string $merged
 * @property string $welcome_email
 * @property string $removable
 * @property string $subscriber_require_approval
 * @property string $subscriber_404_redirect
 * @property string $subscriber_exists_redirect
 * @property string $meta_data
 * @property string $status
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property Campaign[] $campaigns
 * @property Campaign[] $campaignsCount
 * @property CampaignOpenActionListField[] $campaignOpenActionListFields
 * @property CampaignOpenActionSubscriber[] $campaignOpenActionSubscribers
 * @property CampaignTemplateUrlActionListField[] $campaignTemplateUrlActionListFields
 * @property CampaignTemplateUrlActionSubscriber[] $campaignTemplateUrlActionSubscribers
 * @property Customer $customer
 * @property ListCompany $company
 * @property ListCustomerNotification $customerNotification
 * @property ListDefault $default
 * @property ListField[] $fields
 * @property ListField[] $fieldsCount
 * @property ListPageType[] $pageTypes
 * @property ListPageType[] $pageTypesCount
 * @property ListSegment[] $segments
 * @property ListSegment[] $segmentsCount
 * @property ListSubscriber[] $subscribers
 * @property ListSubscriber[] $subscribersCount
 * @property ListSubscriber[] $confirmedSubscribers
 * @property ListSubscriber[] $confirmedSubscribersCount
 * @property ListSubscriberAction[] $subscriberSourceActions
 * @property ListSubscriberAction[] $subscriberTargetActions
 */
class Lists extends ActiveRecord
{
    const VISIBILITY_PUBLIC = 'public';

    const VISIBILITY_PRIVATE = 'private';

    const OPT_IN_SINGLE = 'single';

    const OPT_IN_DOUBLE = 'double';

    const OPT_OUT_SINGLE = 'single';

    const OPT_OUT_DOUBLE = 'double';

    const STATUS_PENDING_DELETE = 'pending-delete';

    public $copyListFieldsMap = array();

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{list}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $rules = array(
            array('name, description, opt_in, opt_out', 'required'),
            
            array('name, display_name, description', 'length', 'min' => 2, 'max' => 255),
            array('visibility', 'in', 'range' => array(self::VISIBILITY_PUBLIC, self::VISIBILITY_PRIVATE)),
            array('opt_in', 'in', 'range' => array_keys($this->getOptInArray())),
            array('opt_out', 'in', 'range' => array_keys($this->getOptOutArray())),
            array('merged, welcome_email, subscriber_require_approval', 'in', 'range' => array_keys($this->getYesNoOptions())),
            array('subscriber_404_redirect, subscriber_exists_redirect', 'url'),

            array('list_uid, name, display_name, opt_in, opt_out, status', 'safe', 'on' => 'search'),
        );

        return CMap::mergeArray($rules, parent::rules());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations = array(
            'campaigns' => array(self::HAS_MANY, 'Campaign', 'list_id'),
            'campaignsCount' => array(self::STAT, 'Campaign', 'list_id'),
            'campaignOpenActionListFields' => array(self::HAS_MANY, 'CampaignOpenActionListField', 'list_id'),
            'campaignOpenActionSubscribers' => array(self::HAS_MANY, 'CampaignOpenActionSubscriber', 'list_id'),
            'campaignTemplateUrlActionListFields' => array(self::HAS_MANY, 'CampaignTemplateUrlActionListField', 'list_id'),
            'campaignTemplateUrlActionSubscribers' => array(self::HAS_MANY, 'CampaignTemplateUrlActionSubscriber', 'list_id'),
            'customer' => array(self::BELONGS_TO, 'Customer', 'customer_id'),
            'company' => array(self::HAS_ONE, 'ListCompany', 'list_id'),
            'customerNotification' => array(self::HAS_ONE, 'ListCustomerNotification', 'list_id'),
            'default' => array(self::HAS_ONE, 'ListDefault', 'list_id'),
            'fields' => array(self::HAS_MANY, 'ListField', 'list_id', 'order' => 'sort_order ASC'),
            'fieldsCount' => array(self::STAT, 'ListField', 'list_id'),
            'pageTypes' => array(self::MANY_MANY, 'ListPageType', '{{list_page}}(list_id, type_id)'),
            'pageTypesCount' => array(self::STAT, 'ListPageType', '{{list_page}}(list_id, type_id)'),
            'segments' => array(self::HAS_MANY, 'ListSegment', 'list_id'),
            'segmentsCount' => array(self::STAT, 'ListSegment', 'list_id'),
            'subscribers' => array(self::HAS_MANY, 'ListSubscriber', 'list_id'),
            'subscribersCount' => array(self::STAT, 'ListSubscriber', 'list_id'),

            'confirmedSubscribers' => array(self::HAS_MANY, 'ListSubscriber', 'list_id', 'condition' => 't.status = :s', 'params' => array(':s' => ListSubscriber::STATUS_CONFIRMED)),
            'confirmedSubscribersCount' => array(self::STAT, 'ListSubscriber', 'list_id', 'condition' => 't.status = :s', 'params' => array(':s' => ListSubscriber::STATUS_CONFIRMED)),

            'subscriberSourceActions' => array(self::HAS_MANY, 'ListSubscriberAction', 'source_list_id'),
            'subscriberTargetActions' => array(self::HAS_MANY, 'ListSubscriberAction', 'target_list_id'),
        );

        return CMap::mergeArray($relations, parent::relations());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = array(
            'list_id'       => Yii::t('lists', 'List'),
            'customer_id'   => Yii::t('lists', 'Customer'),
            'list_uid'      => Yii::t('lists', 'Unique ID'),
            'name'          => Yii::t('lists', 'Name'),
            'display_name'  => Yii::t('lists', 'Display name'),
            'description'   => Yii::t('lists', 'Description'),
            'visibility'    => Yii::t('lists', 'Visibility'),
            'opt_in'        => Yii::t('lists', 'Opt in'),
            'opt_out'       => Yii::t('lists', 'Opt out'),
            'merged'        => Yii::t('lists', 'Merged'),
            'welcome_email' => Yii::t('lists', 'Welcome email'),
            'removable'     => Yii::t('lists', 'Removable'),
            'subscriber_require_approval' => Yii::t('lists', 'Sub. require approval'),
            'subscribers_count'           => Yii::t('lists', 'Subscribers count'),
            'subscriber_404_redirect'     => Yii::t('lists', 'Sub. not found redirect'),
            'subscriber_exists_redirect'  => Yii::t('lists', 'Sub. exists redirect'),
            'meta_data'                   => Yii::t('lists', 'Meta data'),
        );

        return CMap::mergeArray($labels, parent::attributeLabels());
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;
        //$criteria->compare('t.customer_id', (int)$this->customer_id);
		//$criteria->addInCondition('t.customer_id', array((int)$this->customer_id));
		$criteria->addInCondition('t.customer_id', array((int)$this->customer_id,"0"));
        $criteria->compare('t.list_uid', $this->list_uid);
        $criteria->compare('t.name', $this->name, true);
        $criteria->compare('t.display_name', $this->display_name, true);
        $criteria->compare('t.opt_in', $this->opt_in);
        $criteria->compare('t.opt_out', $this->opt_out);
        $criteria->compare('t.merged', $this->merged);

        if (empty($this->status)) {
            $criteria->compare('t.status', '<>' . self::STATUS_PENDING_DELETE);
        } else {
            $criteria->compare('t.status', $this->status);
        }

        $criteria->order = 't.list_id DESC';

        return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize'  => $this->paginationOptions->getPageSize(),
                'pageVar'   => 'page',
            ),
            'sort'  => array(
                'defaultOrder'  => array(
                    't.list_id'   => CSort::SORT_DESC,
                ),
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return Lists the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeSave()
    {
        if ($this->isNewRecord && empty($this->list_uid)) {
            $this->list_uid = $this->generateUid();
        }
        if (empty($this->display_name)) {
            $this->display_name = $this->name;
        }
        return parent::beforeSave();
    }

    // since 1.3.5
    protected function beforeDelete()
    {
        if ($this->removable == self::TEXT_NO) {
            return false;
        }
        if (!$this->getPendingDelete()) {
            $this->status = self::STATUS_PENDING_DELETE;
            $this->save(false);
            // the campaigns
            $campaigns = Campaign::model()->findAllByAttributes(array(
                'list_id' => $this->list_id
            ));
            foreach ($campaigns as $campaign) {
                $campaign->status = Campaign::STATUS_PENDING_DELETE;
                $campaign->save(false);
            }
            return false;
        }
        return parent::beforeDelete();
    }

    public function findByUid($list_uid)
    {
        return $this->findByAttributes(array(
            'list_uid' => $list_uid,
        ));
    }

    public function generateUid()
    {
        $unique = StringHelper::uniqid();
        $exists = $this->findByUid($unique);

        if (!empty($exists)) {
            return $this->generateUid();
        }

        return $unique;
    }

    public function getUid()
    {
        return $this->list_uid;
    }

    public function getStatusesList()
    {
        return array(
            self::STATUS_ACTIVE         => ucfirst(Yii::t('lists', self::STATUS_ACTIVE)),
            //self::STATUS_PENDING_DELETE => ucfirst(Yii::t('lists', self::STATUS_PENDING_DELETE)),
        );
    }

    public function getVisibilityOptions()
    {
        return array(
            ''                          => Yii::t('app', 'Choose'),
            self::VISIBILITY_PUBLIC     => Yii::t('app', 'Public'),
            self::VISIBILITY_PRIVATE    => Yii::t('app', 'Private'),
        );
    }

    public function getOptInArray()
    {
        return array(
            self::OPT_IN_DOUBLE => Yii::t('lists', 'Double opt-in'),
            self::OPT_IN_SINGLE => Yii::t('lists', 'Single opt-in'),
        );
    }

    public function getOptOutArray()
    {
        return array(
            self::OPT_OUT_DOUBLE => Yii::t('lists', 'Double opt-out'),
            self::OPT_OUT_SINGLE => Yii::t('lists', 'Single opt-out'),
        );
    }

    public function getCanBeDeleted()
    {
        return $this->getIsRemovable();
    }

    public function getIsRemovable()
    {
        if ($this->getPendingDelete()) {
            return false;
        }
        if ($this->removable == self::TEXT_NO) {
            return false;
        }
        $removable = true;
        if (!empty($this->customer_id) && !empty($this->customer)) {
            $removable = $this->customer->getGroupOption('lists.can_delete_own_lists', 'yes') == 'yes';
        }
        return $removable;
    }

    public function getEditable()
    {
        return in_array($this->status, array(self::STATUS_ACTIVE));
    }

    public function getPendingDelete()
    {
        return $this->status == self::STATUS_PENDING_DELETE;
    }

    public function attributeHelpTexts()
    {
        $texts = array(
            'name'                       => Yii::t('lists', 'Your mail list verbose name. It will be shown in your customer area sections.'),
            'display_name'               => Yii::t('lists', 'Your mail list display name. This name will be used in subscription forms and template tags parsing for campaigns.'),
            'description'                => Yii::t('lists', 'Please use an accurate list description, but keep it brief.'),
            'visibility'                 => Yii::t('lists', 'Public lists are shown on the website landing page, providing a way of getting new subscribers easily.'),
            'opt_in'                     => Yii::t('lists', 'Double opt-in will send a confirmation email while single opt-in will not.'),
            'opt_out'                    => Yii::t('lists', 'Double opt-out will send a confirmation email while single opt-out will not.'),
            'welcome_email'              => Yii::t('lists', 'Whether the subscriber should receive a welcome email as defined in your list pages.'),
            'subscriber_require_approval'=> Yii::t('lists', 'Whether the subscriber must be manually approved in the list.'),
            'subscriber_404_redirect'    => Yii::t('lists', 'Optionally, a url to redirect the visitor if the subscriber hasn\'t been found in the list or he isn\'t valid anymore.'),
            'subscriber_exists_redirect' => Yii::t('lists', 'Optionally, a url to redirect the visitor at subscription time if the subscriber email already exists in the list.'),
        );
        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }

    public function attributePlaceholders()
    {
        $placeholders = array(
            'name'                       => Yii::t('lists', 'List name, i.e: Newsletter subscribers.'),
            'description'                => Yii::t('lists', 'List detailed description, something your subscribers will easily recognize.'),
            'subscriber_404_redirect'    => 'http://domain.com/subscriber-not-found',
            'subscriber_exists_redirect' => 'http://domain.com/subscriber-exists',
        );
        return CMap::mergeArray($placeholders, parent::attributePlaceholders());
    }

    public function copy()
    {
        $copied = false;

        if ($this->isNewRecord) {
            return $copied;
        }

        $transaction = Yii::app()->db->beginTransaction();

        try {

            $list = clone $this;
            $list->isNewRecord  = true;
            $list->list_id      = null;
            $list->list_uid     = $this->generateUid();
            $list->merged       = self::TEXT_NO;
            $list->removable    = self::TEXT_YES;
            $list->date_added   = new CDbExpression('NOW()');
            $list->last_updated = new CDbExpression('NOW()');

            if (preg_match('/\#(\d+)$/', $list->name, $matches)) {
                $counter = (int)$matches[1];
                $counter++;
                $list->name = preg_replace('/\#(\d+)$/', '#' . $counter, $list->name);
            } else {
                $list->name .= ' #1';
            }

            if (!$list->save(false)) {
                throw new CException($list->shortErrors->getAllAsString());
            }

            $listDefault = !empty($this->default) ? clone $this->default : new ListDefault();
            $listDefault->isNewRecord = true;
            $listDefault->list_id     = $list->list_id;
            $listDefault->save(false);

            $listCompany = !empty($this->company) ? clone $this->company : new ListCompany();
            $listCompany->isNewRecord = true;
            $listCompany->list_id     = $list->list_id;
            $listCompany->save(false);

            $listCustomerNotification = !empty($this->customerNotification) ? clone $this->customerNotification : new ListCustomerNotification();
            $listCustomerNotification->isNewRecord = true;
            $listCustomerNotification->list_id = $list->list_id;
            $listCustomerNotification->save(false);

            $fields = !empty($this->fields) ? $this->fields : array();
            foreach ($fields as $field) {
                $oldFieldId = $field->field_id;

                $fieldOptions = !empty($field->options) ? $field->options : array();
                $field = clone $field;
                $field->isNewRecord  = true;
                $field->field_id     = null;
                $field->list_id      = $list->list_id;
                $field->date_added   = new CDbExpression('NOW()');
                $field->last_updated = new CDbExpression('NOW()');
                if (!$field->save(false)) {
                    continue;
                }

                $newFieldId = $field->field_id;
                $this->copyListFieldsMap[$oldFieldId] = $newFieldId;

                foreach ($fieldOptions as $option) {
                    $option = clone $option;
                    $option->isNewRecord  = true;
                    $option->option_id    = null;
                    $option->field_id     = $field->field_id;
                    $option->date_added   = new CDbExpression('NOW()');
                    $option->last_updated = new CDbExpression('NOW()');
                    $option->save(false);
                }
            }

            $pages = ListPage::model()->findAllByAttributes(array('list_id' => $this->list_id));
            foreach ($pages as $page) {
                $page = clone $page;
                $page->isNewRecord  = true;
                $page->list_id      = $list->list_id;
                $page->date_added   = new CDbExpression('NOW()');
                $page->last_updated = new CDbExpression('NOW()');
                $page->save(false);
            }

            $segments = !empty($this->segments) ? $this->segments : array();
            foreach ($segments as $_segment) {
                $segment = clone $_segment;
                $segment->isNewRecord  = true;
                $segment->list_id      = $list->list_id;
                $segment->segment_id   = null;
                $segment->segment_uid  = null;
                $segment->date_added   = new CDbExpression('NOW()');
                $segment->last_updated = new CDbExpression('NOW()');
                if (!$segment->save(false)) {
                    continue;
                }
                $conditions = !empty($_segment->segmentConditions) ? $_segment->segmentConditions : array();
                foreach ($conditions as $_condition) {
                    if (!isset($this->copyListFieldsMap[$_condition->field_id])) {
                        continue;
                    }
                    $condition = clone $_condition;
                    $condition->isNewRecord  = true;
                    $condition->condition_id = null;
                    $condition->segment_id   = $segment->segment_id;
                    $condition->field_id     = $this->copyListFieldsMap[$_condition->field_id];
                    $condition->date_added   = new CDbExpression('NOW()');
                    $condition->last_updated = new CDbExpression('NOW()');
                    $condition->save(false);
                }
            }

            $transaction->commit();
            $copied = $list;
            $copied->copyListFieldsMap = $this->copyListFieldsMap;
        } catch (Exception $e) {
            $transaction->rollBack();
            $this->copyListFieldsMap = array();
        }

        return Yii::app()->hooks->applyFilters('models_lists_after_copy_list', $copied);
    }

    public function getSubscriber404Redirect()
    {
        return !empty($this->subscriber_404_redirect) ? $this->subscriber_404_redirect : null;
    }

    public function getSubscriberExistsRedirect()
    {
        return !empty($this->subscriber_exists_redirect) ? $this->subscriber_exists_redirect : null;
    }

    public function findAllForSubscriberActions()
    {
        static $subscriberActionLists;
        if ($subscriberActionLists !== null) {
            return $subscriberActionLists;
        }
        $subscriberActionLists = array();

        $criteria = new CDbCriteria();
        $criteria->select = 'list_id, name';
        $criteria->compare('customer_id', (int)$this->customer_id);
        $criteria->addNotInCondition('list_id', array((int)$this->list_id));
        $criteria->addNotInCondition('status', array(self::STATUS_PENDING_DELETE));
        $_subscriberActionLists = self::model()->findAll($criteria);

        foreach ($_subscriberActionLists as $listModel) {
            $subscriberActionLists[$listModel->list_id] = $listModel->name;
        }

        return $subscriberActionLists;
    }

    public static function getListIdsByCustomerId($customerId)
    {
        return array_keys(self::getCustomerListsForDropdown($customerId));
    }

    public static function getCustomerListsForDropdown($customerId)
    {
        static $lists = array();

        if (isset($lists[$customerId])) {
            return $lists[$customerId];
        }
        $lists[$customerId] = array();

        $criteria = new CDbCriteria();
        $criteria->select = 'list_id, name';
        $criteria->compare('customer_id', (int)$customerId);
        $criteria->addNotInCondition('status', array(self::STATUS_PENDING_DELETE));

        $models = self::model()->findAll($criteria);
        foreach ($models as $model) {
            $lists[$customerId][$model->list_id] = $model->name;
        }
        unset($models);

        return $lists[$customerId];
    }
	
	public function getUniqueSubscriber($list_id, $customer_id, $field_value)
	{
		$criteria = new CDbCriteria();
		$criteria->select = 'count(t.subscriber_id)';
		$criteria->join="INNER JOIN uic_list_subscriber as uls ON(uls.subscriber_id=t.subscriber_id) INNER JOIN uic_list as ul ON(uls.list_id=ul.list_id)";
		$criteria->addCondition('uls.list_id="'.$list_id.'"');
		$criteria->addCondition('t.value ="'.$field_value.'"');
		$criteria->addCondition('ul.customer_id ="'.$customer_id.'"');
		$mobile_count = ListFieldValue::model()->count($criteria);
		return $mobile_count;
	}
}
