<?php defined('MW_INSTALLER_PATH') || exit('No direct script access allowed');

/**
 * LicenseController
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class LicenseController extends Controller
{
    public function actionIndex()
    {
        $this->data['breadcrumbs'] = array(
            'License' => 'index.php?route=license',
        );
        
        $license = null;
        if (is_file($file = MW_ROOT_PATH . '/license.txt')) {
            $license = file_get_contents($file);
        }
        
        $this->render('license', compact('license'));
    }
    
}