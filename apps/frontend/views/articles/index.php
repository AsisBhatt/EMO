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

<div class="col-lg-12 list-articles">
    <h1 class="page-heading">
        <?php echo Yii::t('articles', 'Articles');?> <small><?php echo Yii::t('articles', 'List of helpful articles');?></small>
    </h1>
    <hr />
    <?php if (!empty($articles)) { foreach ($articles as $article) { ?>
    <div class="article">
        <div class="title"><?php echo CHtml::link($article->title, Yii::app()->createUrl('articles/view', array('slug' => $article->slug)), array('title' => $article->title)); ?></div>
        <div class="excerpt"><?php echo $article->getExcerpt(500); ?></div>
        <div class="categories pull-right">
        <?php 
        $this->widget('frontend.components.web.widgets.article.ArticleCategoriesWidget', array(
            'article' => $article,
        ));
        ?>
        </div>
        <div class="clearfix"><!-- --></div>
    </div>
    <?php } ?>
    <hr />
    <div class="pull-right">
    <?php $this->widget('CLinkPager', array(
        'pages'         => $pages,
        'htmlOptions'   => array('class' => 'pagination'),
        'header'        => false,
        'cssFile'       => false                
    )); ?>
    </div>
    <div class="clearfix"><!-- --></div>
    
    <?php } else { ?>
    <h4><?php echo Yii::t('articles', 'We\'re sorry, but for now there is no published article!');?></h4>
    <?php } ?>
    
</div>