$( document ).ready(function() {
    $("li.start.active .home_selected").append("<span class='selected'></span>");
	$(".nav-item.start.active .nav-toggle .arrow").addClass("open");
	$(".nav-item .sub-menu li").addClass("nav-item");
	$(".nav-item .sub-menu li a").addClass("nav-link");
	$(".page-sidebar-menu li").eq(1).addClass("blue");
	$(".page-sidebar-menu li").eq(6).addClass("orange");
	$(".page-sidebar-menu li").eq(63).addClass("gray");
});

jQuery(document).ready(function($){
	
	ajaxData = {};
	if ($('meta[name=csrf-token-name]').length && $('meta[name=csrf-token-value]').length) {
			var csrfTokenName = $('meta[name=csrf-token-name]').attr('content');
			var csrfTokenValue = $('meta[name=csrf-token-value]').attr('content');
			ajaxData[csrfTokenName] = csrfTokenValue;
	}
	
	// input/select/textarea fields help text
	$('.has-help-text').popover();
	$(document).on('blur', '.has-help-text', function(e) {
		if ($(this).data('bs.popover')) {
			// this really doesn't want to behave correct unless forced this way!
			$(this).data('bs.popover').destroy();
			$('.popover').remove();
			$(this).popover();
		}
	});
	
	// buttons with loading state
	$('button.btn-submit').button().on('click', function(){
		$(this).button('loading');
	});
    $('.left-side').on('mouseenter', function(){
        $('.timeinfo').stop().fadeIn();
    }).on('mouseleave', function(){
        $('.timeinfo').stop().fadeOut();
    });
});