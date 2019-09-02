/**
 * This file is part of the MailWizz EMA application.
 * 
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in> 
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.5
 */
jQuery(document).ready(function($){
	
	var ajaxData = {};
	if ($('meta[name=csrf-token-name]').length && $('meta[name=csrf-token-value]').length) {
			var csrfTokenName = $('meta[name=csrf-token-name]').attr('content');
			var csrfTokenValue = $('meta[name=csrf-token-value]').attr('content');
			ajaxData[csrfTokenName] = csrfTokenValue;
	}
    
    $('a.allow-all').on('click', function(){
        $(this).closest('div.box-body').find('select').val('allow');
        return false;
    });
    $('a.deny-all').on('click', function(){
        $(this).closest('div.box-body').find('select').val('deny');
        return false;
    });
    $('.btn-save-route-access').on('click', function(){
        var $this = $(this), $form = $this.closest('form');
        $.post('', $form.serialize(), function(){
            $this.text($this.data('init-text')).removeClass('disabled').removeAttr('disabled');
        });
        return false;
    });
});