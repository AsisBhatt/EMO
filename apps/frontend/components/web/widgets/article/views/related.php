<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.0
 */
 
?>

<div class="col-lg-12 related-articles">
    <h4><?php echo Yii::t('articles', 'Related articles');?></h4>
    <?php foreach ($columns as $index => $articles) { ?>
    <div class="column <?php echo $this->columnsCssClass;?>">
        <?php foreach ($articles as $article) { ?>
            <div class="article">
                <div class="title"><?php echo CHtml::link(StringHelper::truncateLength($article->title, 30), Yii::app()->createUrl('articles/view', array('slug' => $article->slug)), array('title' => $article->title)); ?></div>
                <div class="excerpt"><?php echo $article->getExcerpt((int)$this->excerptLength); ?></div>
            </div>
        <?php } ?>    
    </div>
    <?php } ?>
</div>