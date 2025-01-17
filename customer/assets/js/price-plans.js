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
	
    $('.btn-do-order').on('click', function(){  
        $('form#payment-options-form #plan_uid').val($(this).data('plan-uid'));
    });	
    
    if ($('#PricePlanOrderNote_note').length) {
        $('form').on('submit', function(){
            var $this = $(this);
            if (!$this.find('#PricePlanOrderNote_note_fake').length) {
                $this.append('<input type="hidden" name="PricePlanOrderNote[note]" id="PricePlanOrderNote_note_fake"/>');
            }
            $this.find('#PricePlanOrderNote_note_fake').val($('#PricePlanOrderNote_note').val());
        });
    }
});