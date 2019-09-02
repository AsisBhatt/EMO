<?php defined('MW_INSTALLER_PATH') || exit('No direct script access allowed');

/**
 * FinishController
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class FinishController extends Controller
{
    public function actionIndex()
    {
        if (!getSession('cron') || !getSession('license_data')) {
            redirect('index.php?route=cron');
        }
        
        $this->data['pageHeading'] = 'Finish';
        $this->data['breadcrumbs'] = array(
            'Finish' => 'index.php?route=finish',
        );
        
        $this->render('finish');
    }
    
}