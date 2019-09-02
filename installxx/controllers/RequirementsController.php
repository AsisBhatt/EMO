<?php defined('MW_INSTALLER_PATH') || exit('No direct script access allowed');

/**
 * RequirementsController
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
class RequirementsController extends Controller
{
    public function actionIndex()
    {
        if (!getSession('welcome') || !getSession('license_data')) {
            redirect('index.php?route=welcome');
        }

        $this->data['requirements'] = require dirname(__FILE__) . '/../inc/requirements.php';
        $result = 1;  // 1: all pass, 0: fail, -1: pass with warnings
        
        foreach($this->data['requirements'] as $i => $requirement) {
            
            if($requirement[1] && !$requirement[2]) {
                $result = 0;
            } elseif($result > 0 && !$requirement[1] && !$requirement[2]) {
                $result = -1;
            }
            
            if($requirement[4] === '') {
                $requirements[$i][4]='&nbsp;';
            }
        }
        
        if (setSession('requirements', (int)(getPost('result', 0) != 0 && $result != 0))) {
            redirect('index.php?route=filesystem');
        }
        
        $this->data['result'] = $result;
        
        $this->data['pageHeading'] = 'Requirements';
        $this->data['breadcrumbs'] = array(
            'Requirements' => 'index.php?route=requirements',
        );
        
        $this->render('requirements');
    }
    
}