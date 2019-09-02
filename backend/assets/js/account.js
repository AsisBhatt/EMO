jQuery(document).ready(function($){
	// company start
	var obj_country = $('select#CustomerCompany_country_id');
	
	$('select#CustomerCompany_country_id').on('change', function() {
		
		var url = $(this).data('zones-by-country-url'), 
			countryId = $(this).val(),
			$zones = $('select#CustomerCompany_zone_id');
		
		if (url) {
			var formData = {
				country_id: countryId
			}
			
			$.get(url, formData, function(json){
				$zones.html('');
				if (typeof json.zones == 'object' && json.zones.length > 0) {
					for (var i in json.zones) {
						$zones.append($('<option/>').val(json.zones[i].zone_id).html(json.zones[i].name));
					}	
				}
			}, 'json');
			
		}
	});
	if(obj_country.val() == '223'){
		get_zones();
	}
	
	
	
	// company end
	
});
function get_zones(){

	var obj = $('select#CustomerCompany_country_id');
	var url = $(obj).data('zones-by-country-url'), 
		countryId = $(obj).val(),
		$zones = $('select#CustomerCompany_zone_id');
	
	if (url) {
		var formData = {
			country_id: countryId
		}
		
		$.get(url, formData, function(json){
			$zones.html('');
			if (typeof json.zones == 'object' && json.zones.length > 0) {
				for (var i in json.zones) {
					$zones.append($('<option/>').val(json.zones[i].zone_id).html(json.zones[i].name));
				}	
			}
		}, 'json');
		
	}
}