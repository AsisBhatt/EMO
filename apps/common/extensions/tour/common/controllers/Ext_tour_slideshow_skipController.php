<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 */

class Ext_tour_slideshow_skipController extends Controller
{
    // the extension instance
    public $extension;

    /**
     * Index 
     */
    public function actionIndex()
    {
        $appName = Yii::app()->apps->getCurrentAppName();
        $id      = null;

        if ($appName == TourSlideshow::APPLICATION_BACKEND) {
            $id = Yii::app()->user->getId();
        } elseif ($appName == TourSlideshow::APPLICATION_CUSTOMER) {
            $id = Yii::app()->customer->getId();
        }
        
        if (empty($id)) {
            return $this->renderJson(array());
        }

        $criteria = new CDbCriteria();
        $criteria->compare('slideshow_id', (int)Yii::app()->request->getPost('slideshow'));
        $criteria->compare('application', $appName);
        $criteria->compare('status', TourSlideshow::STATUS_ACTIVE);
        $slideshow = TourSlideshow::model()->find($criteria);

        if (empty($slideshow)) {
            return $this->renderJson(array());
        }
        
        $this->extension->setOption('views.' . $appName . '.' . $id . '.viewed', $slideshow->slideshow_id);

        return $this->renderJson(array());
    }
}
