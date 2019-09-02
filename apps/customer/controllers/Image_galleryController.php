<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * LinkController
 *
 * Handles the actions for campaigns related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

class Image_galleryController extends Controller
{
    public function init()
    {
		
		//array('src' => AssetsUrl::css('style.css'), 'priority' => -1000),
		//$this->getData('pageScripts')->add(array('src' => AssetsUrl::css('lightbox.min.css')),array('type' => 'text/css','rel' => 'stylesheet'));
		
		//$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('jquery_upload/jquery.fileupload.js')));
		// $baseUrl = Yii::app()->baseUrl; 
		// $cs = Yii::app()->getClientScript();
		// $cs->registerCssFile($baseUrl.'/assets/css/lightbox.min.css');
		// $cs->registerScriptFile($baseUrl.'/assets/js/lightbox-plus-jquery.min.js');
		$this->getData('pageScripts')->add(array('src' => AssetsUrl::js('templates.js')));
        parent::init();
    }

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
    public function filters()
    {
        $filters = array(
            //'postOnly + delete, slug',
        );
        return CMap::mergeArray($filters, parent::filters());
    }

    /**
     * Image Gallery 
     */
    public function actionIndex()
    {
		$request = Yii::app()->request;
        $image_gallery = new ImageGallery;
        // $image_gallery->unsetAttributes();
        // $image_gallery->attributes  = (array)$request->getQuery($image_gallery->modelName, array());
        // $image_gallery->customer_id = (int)Yii::app()->customer->getId();
		$criteria = new CDbCriteria();
		$criteria->compare('t.customer_id', (int)Yii::app()->customer->getId());
		$imagegallery = ImageGallery::model()->findAll($criteria);
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('image_gallery', 'Image Gallery'),
            'pageHeading'       => Yii::t('image_gallery', 'Image Gallery'),
            'pageBreadcrumbs'   => array(
                //Yii::t('dashboard', 'Dashboard') => $this->createUrl('dashboard/index'),
                Yii::t('app', 'Image Gallery')
            )
        ));

        $this->render('index', compact('image_gallery','imagegallery'));
    }



    /**
     * Create a new link
     */
    public function actionCreate()
    {
		//$image_gallery  = new ImageGallery();
        $request    = Yii::app()->request;
        $notify     = Yii::app()->notify;
        $customer   = Yii::app()->customer->getModel();
		$image_types = array('image/jpg','image/jpeg','image/bmp','image/png','image/gif');
		$base_url = 'https://'.Yii::app()->getRequest()->serverName;
		$target_dir = "../myuploads/";
       
        if ($request->isPostRequest) {
			$uploaded_images = array();
			
			foreach($_FILES['ImageGallery']['name']['filename'] as $file_key => $file_val){
				
				$media_size =  round($_FILES['ImageGallery']['size']['filename'][$file_key]/1072);
				$file_type = $_FILES['ImageGallery']['type']['filename'][$file_key];	
				
				$upload_filename = $_FILES["ImageGallery"]["name"]['filename'][$file_key];
				$upload_filename = str_replace(" ","",$upload_filename);
				
				$uploaded=0;
				$target_file = $target_dir . basename($upload_filename);
				$target_file = $target_dir . $upload_filename;
				
				if (copy($_FILES["ImageGallery"]["tmp_name"]['filename'][$file_key], $target_file)) {
					$uploaded=1;
					$filename = $_FILES["ImageGallery"]["name"]['filename'][$file_key];
					$filename = str_replace(" ","",$filename);
				}
				
				if(in_array($file_type,$image_types)){
				
					if($media_size > 650){
						Yii::import('customer.extensions.image.Image');
						$image = new Image($target_file);
						$image->quality(70);
						$image->save();
						$upload_image_size = filesize($image->file);
						$image_size = $this->sizeFilter($upload_image_size);
					}
					
					$imagegallery  = new ImageGallery();
					$imagegallery->customer_id = $customer->customer_id;
					$imagegallery->filename = $filename;
					$imagegallery->created_at = date('Y-m-d H:i:s');
					$imagegallery->save();
					
					$notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
				}else{
					$notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
				}
				
				CommonHelper::setActivityLogs('Image Gallery Upload Images '.str_replace(array('{{','}}'),'',$imagegallery->tableName()),$imagegallery->image_id,$imagegallery->tableName(),'Image Gallery Upload Images',(int)Yii::app()->customer->getId());
			}

            // if ($count_success > 0) {
                // $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            // } else {
                // $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            // }


            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'imagegallery'   => $imagegallery,
            )));

            if ($collection->success) {
                $this->redirect(array('image_gallery/index'));
            }
        }

        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('image_gallery', 'Image Gallery'),
            'pageHeading'       => Yii::t('image_gallery', 'Image Gallery'),
            'pageBreadcrumbs'   => array(
                Yii::t('app', 'Image Gallery'),
            )
        ));

        $this->render('index', compact('imagegallery'));
    }

    /**
     * Delete an existing Image
     */
    public function actionDelete($image_id)
    {
		$image_gallery = ImageGallery::model()->findByPk((int)$image_id);

        if (empty($image_gallery)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $image_gallery->delete();
		
		CommonHelper::setActivityLogs('Image Gallery Delete '.str_replace(array('{{','}}'),'',$image_gallery->tableName()),$image_id,$image_gallery->tableName(),'Image Gallery Delete',(int)Yii::app()->customer->getId());

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('image_gallery/index'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $image_gallery,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }

    /**
     * Generate the slug for an link based on the link title
     */
    public function actionSlug()
    {
        $request = Yii::app()->request;

        if (!$request->isAjaxRequest) {
            $this->redirect(array('link/index'));
        }

        $image_gallery = new Link();
        $image_gallery->link_id = (int)$request->getPost('link_id');
        $image_gallery->slug = $request->getPost('string');

        $category = new ArticleCategory();
        $category->slug = $image_gallery->slug;

        $image_gallery->slug = $category->generateSlug();
        $image_gallery->slug = $image_gallery->generateSlug();

        return $this->renderJson(array('result' => 'success', 'slug' => $image_gallery->slug));
    }
	
	protected function sizeFilter( $bytes )
	{
		$label = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
		for( $i = 0; $bytes >= 1024 && $i < ( count( $label ) -1 ); $bytes /= 1024, $i++ );
		return round($bytes, 2);
	}
   
}
