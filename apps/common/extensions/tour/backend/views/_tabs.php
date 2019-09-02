<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */

$controller = $this->getId();
$action     = $this->getAction()->getId();
?>
<ul class="nav nav-tabs" style="border-bottom: 0px;">
    <li class="<?php echo $controller == 'ext_tour_settings' ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('ext_tour_settings/index')?>">
            <?php echo $this->extension->t('Common');?>
        </a>
    </li>
    <li class="<?php echo stripos($controller, 'ext_tour_slideshow') === 0 ? 'active' : 'inactive';?>">
        <a href="<?php echo $this->createUrl('ext_tour_slideshows/index')?>">
            <?php echo $this->extension->t('Slideshows');?>
        </a>
    </li>
</ul>
