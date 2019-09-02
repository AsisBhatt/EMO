$(document).ready(function(){
	var ctx = $("#searching_data");

	var barGraph = new Chart(ctx, {
		type: 'bar',
	});
	
	var chartdata = {};
});

function create_chart(data){
	alert(barGraph);
	$.ajax({
	url: 'smsbarchart',
	method: "GET",
	dataType: 'json',
	cache: true,
		success: function(data) {
			var date = [];
			var smscount = [];
			
			for(var i in data){
				var date_cut = data[i].date.split(' ')[0]
				date.push(date_cut);
				smscount.push(data[i].sms_count);
			}

			var chartdata = {
				labels: date,
				datasets : [
					{
						label: 'Sms Count Date Wise',
						backgroundColor: 'rgba(224, 160, 199, 0.75)',
						borderColor: 'rgba(224, 160, 199, 0.75)',
						hoverBackgroundColor: 'rgba(224, 160, 199, 1)',
						hoverBorderColor: 'rgba(224, 160, 199, 1)',
						data: smscount
					}
				]
			};
			
			barGraph.data.chartdata;
		},
	});
};