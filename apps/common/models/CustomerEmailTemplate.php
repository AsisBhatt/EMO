<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * CustomerEmailTemplate
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

/**
 * This is the model class for table "customer_email_template".
 *
 * The followings are the available columns in table 'customer_email_template':
 * @property integer $template_id
 * @property string $template_uid
 * @property integer $customer_id
 * @property string $name
 * @property string $content
 * @property string $content_hash
 * @property string $create_screenshot
 * @property string $screenshot
 * @property string $inline_css
 * @property string $minify
 * @property integer $sort_order
 * @property string $date_added
 * @property string $last_updated
 *
 * The followings are the available model relations:
 * @property Customer $customer
 */
class CustomerEmailTemplate extends ActiveRecord
{
    public $archive;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{customer_email_template}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        $mimes = null;
        if (CommonHelper::functionExists('finfo_open')) {
            $mimes = Yii::app()->extensionMimes->get('zip')->toArray();
        }

        $rules =  array(
            array('name, content, inline_css, minify', 'required', 'on' => 'insert, update'),
            array('archive, inline_css, minify', 'required', 'on' => 'upload'),
            array('name, content', 'unsafe', 'on' => 'upload'),

            array('name', 'length', 'max'=>255),
            array('inline_css', 'in', 'range' => array_keys($this->getInlineCssArray())),
            array('minify', 'in', 'range' => array_keys($this->getYesNoOptions())),
            array('content', 'safe'),
            array('archive', 'file', 'types' => array('zip'), 'mimeTypes' => $mimes, 'allowEmpty' => true),
            array('sort_order', 'numerical', 'integerOnly' => true),
        );

        return CMap::mergeArray($rules, parent::rules());
    }

    public function behaviors()
    {
        $behaviors = array(
            // will handle the upload but also the afterDelete event to delete uploaded files.
            'uploader' => array(
                'class' => 'common.components.db.behaviors.EmailTemplateUploadBehavior',
            ),
        );

        return CMap::mergeArray($behaviors, parent::behaviors());
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        $relations = array(
            'customer' => array(self::BELONGS_TO, 'Customer', 'customer_id'),
        );

        return CMap::mergeArray($relations, parent::relations());
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels =  array(
            'template_id'   => Yii::t('email_templates', 'Template'),
            'template_uid'  => Yii::t('email_templates', 'Template uid'),
            'customer_id'   => Yii::t('email_templates', 'Customer'),
            'name'          => Yii::t('email_templates', 'Name'),
            'content'       => Yii::t('email_templates', 'Content'),
            'content_hash'  => Yii::t('email_templates', 'Content hash'),
            'create_screenshot' => Yii::t('email_templates', 'Create screenshot'),
            'screenshot'    => Yii::t('email_templates', 'Screenshot'),
            'inline_css'    => Yii::t('email_templates', 'Inline css'),
            'minify'        => Yii::t('email_templates', 'Minify'),
            'archive'       => Yii::t('email_templates', 'Archive file'),
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
        $criteria=new CDbCriteria;
        $criteria->compare('customer_id', (int)$this->customer_id);

        return new CActiveDataProvider(get_class($this), array(
            'criteria'      => $criteria,
            'pagination'    => array(
                'pageSize'  => (int)Yii::app()->request->getQuery('pageSize', 20),
                'pageVar'   => 'page',
            ),
            'sort'  => array(
                'defaultOrder' => array(
                    'sort_order'   => CSort::SORT_ASC,
                ),
            ),
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CustomerEmailTemplate the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function beforeSave()
    {
        if (empty($this->template_uid)) {
            $this->template_uid = $this->generateUid();
        }

        if (empty($this->name)) {
            $this->name = 'Untitled';
        }

        if ($this->content_hash != sha1($this->content)) {
            $this->create_screenshot = self::TEXT_YES;
        }

        $this->content_hash = sha1($this->content);

        return parent::beforeSave();
    }

    protected function afterDelete()
    {
        // clean template files, if any.
        $storagePath = Yii::getPathOfAlias('root.frontend.assets.gallery');
        $templateFiles = $storagePath.'/'.$this->template_uid;
        if (file_exists($templateFiles) && is_dir($templateFiles)) {
            FileSystemHelper::deleteDirectoryContents($templateFiles, true, 1);
        }

        parent::afterDelete();
    }

    public function findByUid($template_uid)
    {
        return $this->findByAttributes(array(
            'template_uid' => $template_uid,
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

    public function getInlineCssArray()
    {
        return $this->getYesNoOptions();
    }

    public function attributeHelpTexts()
    {
        $texts = array(
            'name'       => Yii::t('email_templates', 'The name of the template, used for you to make the difference if having to many templates.'),
            'inline_css' => Yii::t('email_templates', 'Whether the parser should extract the css from the head of the document and inline it for each matching attribute found in the document body.'),
            'minify'     => Yii::t('email_templates', 'Whether the parser should minify the template to reduce size.'),
        );

        return CMap::mergeArray($texts, parent::attributeHelpTexts());
    }

    public function copy()
    {
        if ($this->isNewRecord) {
            return false;
        }

        $storagePath = Yii::getPathOfAlias('root.frontend.assets.gallery');
        $filesPath   = $storagePath.'/'.$this->template_uid;

        $templateUid  = $this->generateUid();
        $newFilesPath = $storagePath.'/'.$templateUid;

        if (file_exists($filesPath) && is_dir($filesPath) && mkdir($newFilesPath, 0777, true)) {
            if (!FileSystemHelper::copyOnlyDirectoryContents($filesPath, $newFilesPath)) {
                return false;
            }
        }

        $template = clone $this;
        $template->isNewRecord  = true;
        $template->template_id  = null;
        $template->template_uid = $templateUid;
        $template->content      = str_replace($this->template_uid, $templateUid, $this->content);
        $template->content_hash = null;
        $template->screenshot   = preg_replace('#' . $this->template_uid . '#', $templateUid, $this->screenshot, 1);
        $template->date_added   = null;
        $template->last_updated = null;

        if (!$template->save(false)) {
            if (file_exists($newFilesPath) && is_dir($newFilesPath)) {
                FileSystemHelper::deleteDirectoryContents($newFilesPath, true, 1);
            }
            return false;
        }

        return $template;
    }

    public function getScreenshotSrc($width = 160, $height = 160)
    {
        if (!empty($this->screenshot)) {
            try {
                if ($image = @ImageHelper::resize($this->screenshot, $width, $height)) {
                    return $image;
                }
            } catch (Exception $e) {}
        }
        return ImageHelper::resize('/frontend/assets/files/no-image-160x160.gif', $width, $height);
    }

    public function getShortName($length = 20)
    {
        return StringHelper::truncateLength($this->name, (int)$length);
    }

    public function getUid()
    {
        return $this->template_uid;
    }
}
