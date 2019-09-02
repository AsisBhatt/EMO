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
    var $sendAt = $('#SmsCampaign_send_at'), 
        $displaySendAt = $('#SmsCampaign_sendAt'),
        $fakeSendAt = $('#fake_send_at');
    if ($sendAt.length && $displaySendAt.length && $fakeSendAt.length) {
        $fakeSendAt.datetimepicker({
			inline:true,
			minDateTime: 0,
			startDate: new Date(),
			format: $sendAt.data('date-format') || 'yyyy-mm-dd hh:ii:ss',
			autoclose: true,
            language: $sendAt.data('language') || 'en',
            showMeridian: true
		}).on('changeDate', function(e) {
            syncDateTime();
		}).on('blur', function(){
            syncDateTime();
		});
        
        $displaySendAt.on('focus', function(){
            $('#fake_send_at').datetimepicker('show');
        });
        
        function syncDateTime() {
            var date = $fakeSendAt.val();
            if (!date) {
                return;
            }
            $displaySendAt.val('').addClass('spinner');
            $.get($fakeSendAt.data('syncurl'), {date: date}, function(json){
                $displaySendAt.removeClass('spinner');
                $displaySendAt.val(json.localeDateTime);
                $sendAt.val(json.utcDateTime);
            }, 'json');
        }
        syncDateTime();
	}
	
	/*var $sendAt = $('#SmsCampaign_send_at'), 
        $displaySendAt = $('#SmsCampaign_sendAt'),
        $Sms_fakeSendAt = $('#fake_send_at');
	
    if ($sendAt.length && $displaySendAt.length && $Sms_fakeSendAt.length) {
		alert($sendAt.val());
		alert($displaySendAt.val());
        alert($Sms_fakeSendAt.val());
		$Sms_fakeSendAt.datetimepicker({
			format: $Sms_fakeSendAt.data('date-format') || 'yyyy-mm-dd hh:ii:ss',
			autoclose: true,
            language: $Sms_fakeSendAt.data('language') || 'en',
            showMeridian: true
		}).on('changeDate', function(e) {
            smssyncDateTime();
		}).on('blur', function(){
            smssyncDateTime();
		});
        
        $displaySendAt.on('focus', function(){
            $('#fake_send_at').datetimepicker('show');
        });
        
        function smssyncDateTime() {
            var smsdate = $Sms_fakeSendAt.val();
            if (!smsdate) {
                return;
            }
            $displaySendAt.val('').addClass('spinner');
            $.get($Sms_fakeSendAt.data('syncurl'), {date: smsdate}, function(json){
                $displaySendAt.removeClass('spinner');
                $displaySendAt.val(json.utcDateTime);
                $sendAt.val(json.localeDateTime);
            }, 'json');
        }
        smssyncDateTime();
	}*/
});