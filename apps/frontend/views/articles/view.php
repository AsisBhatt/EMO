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

<div class="col-lg-12">
    <h1 class="page-heading">
        <?php echo $article->title;?>
    </h1>
    <hr />
    <?php echo $article->content;?>
    <hr />
    <?php 
    $this->widget('frontend.components.web.widgets.article.ArticleCategoriesWidget', array(
        'article' => $article,
    ));
    $this->widget('frontend.components.web.widgets.article.ArticleRelatedArticlesWidget', array(
        'article' => $article,
    ));
    ?>
</div>