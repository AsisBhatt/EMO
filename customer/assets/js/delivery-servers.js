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

    var $headersTemplate    = $('#headers-template'), 
        headersCounter      = $headersTemplate.data('count');
    
    $('a.btn-add-header').on('click', function(){
        var $html = $($headersTemplate.html().replace(/__#__/g, headersCounter));
        $('#headers-list').append($html);
        $html.find('input').removeAttr('disabled');
        headersCounter++;
        return false;
    });
    
    $(document).on('click', 'a.remove-header', function(){
        $(this).closest('.form-group').remove();
        return false;
    });
    
    var $policiesTemplate    = $('#policies-template'), 
        policiesCounter      = $policiesTemplate.data('count');
    
    $('a.btn-add-policy').on('click', function(){
        var $html = $($policiesTemplate.html().replace(/__#__/g, policiesCounter));
        $('#policies-list').append($html);
        $html.find('input, select').removeAttr('disabled');
        policiesCounter++;
        return false;
    });
    
    $(document).on('click', 'a.remove-policy', function(){
        $(this).closest('.form-group').remove();
        return false;
    });
    
    $(document).on('click', 'a.copy-server, a.enable-server, a.disable-server', function() {
		$.post($(this).attr('href'), ajaxData, function(){
			window.location.reload();
		});
		return false;
	});
});