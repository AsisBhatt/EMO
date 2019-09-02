<?php defined('MW_INSTALLER_PATH') || exit('No direct script access allowed');

/**
 * CronController
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class CronController extends Controller
{
    public function actionIndex()
    {
        if (!getSession('admin') || !getSession('license_data')) {
            redirect('index.php?route=admin');
        }
        
        if (getPost('next')) {
            setSession('cron', 1);
            redirect('index.php?route=finish');
        }
        
        $this->data['pageHeading'] = 'Cron jobs';
        $this->data['breadcrumbs'] = array(
            'Cron jobs' => 'index.php?route=cron',
        );

        $this->render('cron');
    }
    
    public function getCliPath()
    {
        return CommonHelper::findPhpCliPath();
    }
    
}