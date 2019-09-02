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
	
	var ajaxData = {};
	if ($('meta[name=csrf-token-name]').length && $('meta[name=csrf-token-value]').length) {
			var csrfTokenName = $('meta[name=csrf-token-name]').attr('content');
			var csrfTokenValue = $('meta[name=csrf-token-value]').attr('content');
			ajaxData[csrfTokenName] = csrfTokenValue;
	}
	
	$('a.btn-delete-template').on('click', function(){
		var $this = $(this);
		var confirmText = $this.data('confirm-text');
		if (!confirm(confirmText)) {
			return false;
		}
		
		$.post($this.attr('href'), ajaxData, function(){
			$this.closest('.panel-template-box').fadeOut('slow', function(){
				$(this).remove();
			});
		});
		return false;
	});
    
    if ($.fn.sortable && $(".sortable-box").length) {
        $(".sortable-box").height($(".sortable-box").height()).find('.box-header, .box-title').css({cursor: 'move'});
        $(".sortable-box").sortable({
            connectWith: ".sortable-box",
            update: function(event, ui) {
                var templates = [];
                $(".sortable-box .panel-template-box").each(function(){
                    templates.push({
                        template_id: $(this).data('id'),
                        sort_order: $(this).index()
                    });
                });
                var postData = $.extend(ajaxData, {templates: templates});
                $.post(ui.item.data('url'), postData, function(json){}, 'json');
            }
        });    
    }
    
});