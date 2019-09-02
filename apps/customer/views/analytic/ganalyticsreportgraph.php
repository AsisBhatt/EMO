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

/**
 * This hook gives a chance to prepend content or to replace the default view content with a custom content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * In case the content is replaced, make sure to set {@CAttributeCollection $collection->renderContent} to false 
 * in order to stop rendering the default content.
 * @since 1.3.3.1
 */
$hooks->doAction('before_view_file_content', $viewCollection = new CAttributeCollection(array(
    'controller'    => $this,
    'renderContent' => true,
)));

// and render if allowed
if ($viewCollection->renderContent) { ?>
    <div class="tabs-container">
    <?php 
    //echo $this->renderTabs();
    /**
     * This hook gives a chance to prepend content before the active form or to replace the default active form entirely.
     * Please note that from inside the action callback you can access all the controller view variables 
     * via {@CAttributeCollection $collection->controller->data}
     * In case the form is replaced, make sure to set {@CAttributeCollection $collection->renderForm} to false 
     * in order to stop rendering the default content.
     * @since 1.3.3.1
     */    
    $hooks->doAction('before_active_form', $collection = new CAttributeCollection(array(
        'controller'    => $this,
        'renderForm'    => true,
    )));
    
    // and render only if allowed
    if ($collection->renderForm) {
        $form = $this->beginWidget('CActiveForm', array(
            'htmlOptions' => array('enctype' => 'multipart/form-data')
        )); 
        ?>
        <div class="box box-primary no-top-border">
            <div class="box-body">
         
				<h3><a href="http://email.unikainfocom.net/customer/index.php/account/ganalyticsreport?pid=<?php echo trim($ga['profile_id']); ?> " >
				 <button type="button" class="btn btn-primary btn-submit">Switch To Normal Mode</button></a></h3>		

				 
                <div class="clearfix"><!-- --></div>
				<!--<br>-->
				<div class="">

				
 
 
<?php 
require 'gapi.class.php' ;
define('ga_profile_id',trim($ga['profile_id']));
//define('ga_profile_id','100600522');
$ga = new gapi("486869175061-compute@developer.gserviceaccount.com", "uploads/key.p12");	
?>
<?php
$ga->requestReportData(ga_profile_id,array('browser'),array('pageviews','visits'));
$browserhtml="['Browser', 'Pageview', 'Visits', ],";
foreach($ga->getResults() as $result):
	$browserhtml .= "['".$result."',". $result->getPageviews().", ".$result->getVisits()."],";
endforeach
?>
<div class="col-md-12">
	<h4 style="color: red;">BROWSER </h4>
	<div id="chart_div_browser"  style="width: 100%; height: 400px;"></div>				
</div>

<div class="col-md-12"> </br><hr><hr></br></div>

<?php
$ga->requestReportData(ga_profile_id,array('country'),array('pageviews','visits'));
$countryhtml="['Country', 'Pageview', 'Visits', ],";
foreach($ga->getResults() as $result):
	$countryhtml .= "['".$result."',". $result->getPageviews().", ".$result->getVisits()."],";
endforeach
?>
<div class="col-md-12">
	<h4 style="color: red;">COUNTRY </h4>
	<div id="chart_div_country"  style="width: 100%; height: 400px;"></div>				
</div>

<div class="col-md-12"> </br><hr><hr></br></div>

<?php
$filter = 'country == India';
$ga->requestReportData(ga_profile_id,array('city'),array('pageviews','visits'),'-visits',$filter);

$cityhtml="['City', 'Pageview', 'Visits', ],";
foreach($ga->getResults() as $result):
	$cityhtml .= "['".$result."',". $result->getPageviews().", ".$result->getVisits()."],";
endforeach
?>
<div class="col-md-12">
	<h4 style="color: red;">CITY </h4>
	<div id="chart_div_city"  style="width: 100%; height: 400px;"></div>				
</div>



<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>  
<script>
google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
        var data = google.visualization.arrayToDataTable([
         <?php echo $browserhtml; ?>
        ]);

        var options = {
          chart: {
            title: 'Page View and Visits',
           // subtitle: 'Test',
          }
        };

        var chart = new google.charts.Bar(document.getElementById('chart_div_browser'));

        chart.draw(data, options);
		
		
		var data2 = google.visualization.arrayToDataTable([
         <?php echo $countryhtml; ?>
        ]);

        var options2 = {
          chart: {
            title: 'Page View and Visits',
           // subtitle: 'Test',
		   // width: 900,
			
          }
        };

        var chart2 = new google.charts.Bar(document.getElementById('chart_div_country'));
        chart2.draw(data2, options2);
		
		
		var data3 = google.visualization.arrayToDataTable([
         <?php echo $cityhtml; ?>
        ]);

        var options3 = {
          chart: {
            title: 'Page View and Visits',
           // subtitle: 'Test',
          }
        };

        var chart3 = new google.charts.Bar(document.getElementById('chart_div_city'));
        chart3.draw(data3, options3);
		
}
</script>




				</div> 
				<br>
                <div class="clearfix"><!-- --></div>
     
            </div>
            <div class="box-footer">
                <div class="pull-right">
                    
					</div>
                <div class="clearfix"><!-- --></div>
            </div>
        </div>
        <?php 
        $this->endWidget(); 
    } 
    /**
     * This hook gives a chance to append content after the active form.
     * Please note that from inside the action callback you can access all the controller view variables 
     * via {@CAttributeCollection $collection->controller->data}
     * @since 1.3.3.1
     */
    $hooks->doAction('after_active_form', new CAttributeCollection(array(
        'controller'      => $this,
        'renderedForm'    => $collection->renderForm,
    )));
    ?>
    </div>
<?php 
}
/**
 * This hook gives a chance to append content after the view file default content.
 * Please note that from inside the action callback you can access all the controller view
 * variables via {@CAttributeCollection $collection->controller->data}
 * @since 1.3.3.1
 */
$hooks->doAction('after_view_file_content', new CAttributeCollection(array(
    'controller'        => $this,
    'renderedContent'   => $viewCollection->renderContent,
)));