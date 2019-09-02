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
jQuery(document).ready(function($){
    
    $('ul.list-forms-nav li a').on('click', function(){
        var $lis = $('ul.list-forms-nav li');
        var $li = $(this).closest('li');
        if (!$li.is('.active')) {
            $lis.removeClass('active');
            $li.addClass('active');
            $('.form-container').hide();
            $($(this).attr('href')).show();
        }
        return false;
    });
});