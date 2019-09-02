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
         
		 
                <div class="clearfix"><!-- --></div>
				<!--<br>-->
				<div class="">

				
				


<?php
   // From the APIs console
    $client_id = '486869175061-1mc3ujrcsn5iv9sbrbefaeurnd8bsrgj.apps.googleusercontent.com';
    // From the APIs console
    $client_secret = 'fCMAKkpDAGPfO_Fok-Lvw2mt';
     // Url to your this page, must match the one in the APIs console
    $redirect_uri = 'http://socialstark.com/login/customer/index.php/analytic/analyticsreportgraph';
    // Analytics account id like, 'ga:xxxxxxx'
    $account_id = 'ga:100600522'; 
    
    session_start();
    //include('../GoogleAnalyticsAPI.class.php');
    include('GoogleAnalyticsAPI.class.php');

    $ga = new GoogleAnalyticsAPI(); 
    $ga->auth->setClientId($client_id);
    $ga->auth->setClientSecret($client_secret);
    $ga->auth->setRedirectUri($redirect_uri);
	
    if (isset($_GET['force_oauth'])) {
        $_SESSION['oauth_access_token'] = null;
    }

    /*
     *  Step 1: Check if we have an oAuth access token in our session
     *          If we've got $_GET['code'], move to the next step
     */
    if (!isset($_SESSION['oauth_access_token']) && !isset($_GET['code'])) {
        // Go get the url of the authentication page, redirect the client and go get that token!
        $url = $ga->auth->buildAuthUrl();
		header("Location: ".$url);
    } 

    /*
     *  Step 2: Returning from the Google oAuth page, the access token should be in $_GET['code']
     */
    if (!isset($_SESSION['oauth_access_token']) && isset($_GET['code'])) {
        $auth = $ga->auth->getAccessToken($_GET['code']);

			if ($auth['http_code'] == 200) {
            $accessToken    = $auth['access_token'];
            $refreshToken   = $auth['refresh_token'];
            $tokenExpires   = $auth['expires_in'];
            $tokenCreated   = time();
            
            // For simplicity of the example we only store the accessToken
            // If it expires use the refreshToken to get a fresh one
            $_SESSION['oauth_access_token'] = $accessToken;
        } else {
            die("Sorry, something wend wrong retrieving the oAuth tokens");
        }
    }
	


    
    /*
     *  Step 3: Do real stuff!
     *          If we're here, we sure we've got an access token
     */
    $ga->setAccessToken($_SESSION['oauth_access_token']);
	//$ga->setAccountId($account_id);
   	
	//die('ok'); 
	
    $profiles = $ga->getProfiles();;
	//print_r($profiles);
	//print_r($profiles['username'] ); 
	//print_r($profiles['totalResults'] ); 
	//print_r($profiles['items'] ); 
	//$acc_id = @$profiles['items'][0]['id'];
	 //$acc_id = 'ga:'.@$profiles['items'][0]['id'];
	 
	//unset($_SESSION['accountId']);
	if (isset($_SESSION['accountId'])) {
		$acc_id = 'ga:'.@$_SESSION['accountId'];;
		$acc_id_numeric= @$_SESSION['accountId'];;
    } else {
		$acc_id = 'ga:'.@$profiles['items'][0]['id'];
		$acc_id_numeric= @$profiles['items'][0]['id'];
    } 
	$ga->setAccountId($acc_id);

	$selecthtml='';$selecthtml='';
	foreach($profiles['items'] as $pro){
		if($pro['id']==$acc_id_numeric){
			$selecthtml .='<option value="'.$pro['id'].'" selected>'. $pro['webPropertyId'].' ('.$pro['id'].')'.' </option>';		
		}else{
			$selecthtml .='<option value="'.$pro['id'].'">'. $pro['webPropertyId'].' ('.$pro['id'].')'.' </option>';
		}
	}
	
	
?>	 



<div class="form-group col-lg-12">
	<div class="col-md-12">
	<label for="Customer_mobile">SELECT PROFILE ACCOUNT ID</label>  
	</div>
	<div class="col-md-4">
	<select name="accountId" class="form-control" id="accountId" required > 
		<?php echo $selecthtml;?>
		<!--<option>carza UA-61689909-1 (100600522)</option>-->
		
	</select>
	</div>
	<div class="col-md-2">
	<button type="submit" class="btn btn-primary btn-submit" data-loading-text="Please wait, processing...">Submit</button>
	</div>
	
	<div class="col-md-4">
	<a href="http://socialstark.com/login/customer/index.php/analytic/analyticsreport"><button type="button" class="btn btn-primary btn-submit" >Switch to Text Report</button></a>
	</div>	
</div>	

	
	
	
	
<!--1. Audience Overview (Date Time wise). -->
<?php	
	$defaults = array(
        'start-date' => date('Y-m-d', strtotime('-1 month')),
        'end-date'   => date('Y-m-d'),
    );
    $ga->setDefaultQueryParams($defaults);

    $params = array();
    $params = array(
        'metrics'    => 'ga:visits,ga:pageviews',
        'dimensions' => 'ga:date',
    );
    $result1 = $ga->query($params);
	//print_r(@$result1['rows']);
	 

	$defaults = array(
        'start-date' => date('Y-m-d', strtotime('-1 day')),
        'end-date'   => date('Y-m-d'),
    );
    $ga->setDefaultQueryParams($defaults);
    $params = array();
    $params = array(
        'metrics'    => 'ga:visits,ga:pageviews',
        'dimensions' => 'ga:dateHour',
    );
    $result1a = $ga->query($params);
	//print_r(@$result1a['rows']);

?>


<div class="col-md-12">
	<h4 style="color: red;">1. Audience Overview (Date wise).</h4>
	<?php
	$audiencedatehtml="['Date', 'Pageview', 'Visits', ],";
	foreach($result1['rows'] as $res):
		$audiencedatehtml .= "['".$res[0]."',". $res[1].", ".$res[2]."],";
	endforeach
	?>
	<div id="chart_div_audience"  style="width: 100%; height: 400px;"></div>				
</div>

<div class=" col-md-12"><hr><hr></div>
				
				

<!--2.Visitor Source.-->	
<?php	
	$defaults = array(
        'start-date' => date('Y-m-d', strtotime('-1 year')),
        'end-date'   => date('Y-m-d'),
    );
    $ga->setDefaultQueryParams($defaults);
    $params = array();
    $params = array(
        'metrics'    => 'ga:visits,ga:pageviews',
		'dimensions' => 'ga:source',
	);
    $result2 = $ga->query($params);
	//print_r($result2['rows']);
?>
<div class="col-md-12">
	<h4 style="color: red;">2. Visitor Source.</h4>
	<?php
	$visitorsourcehtml="['Source', 'Pageview', 'Visits', ],";
	foreach($result2['rows'] as $res):
		$visitorsourcehtml .= "['".$res[0]."',". $res[1].", ".$res[2]."],";
	endforeach
	?>
	<div id="chart_div_source"  style="width: 100%; height: 400px;"></div>	
</div>				
<div class=" col-md-12"><hr><hr></div>
	

<!--3. New vs Returning visitor.-->
<?php
	$defaults = array(
        'start-date' => date('Y-m-d', strtotime('-1 year')),
        'end-date'   => date('Y-m-d'),
    );
    $ga->setDefaultQueryParams($defaults);
    $params = array();
    $params = array(
        //'metrics'    => 'ga:visits,ga:pageviews',
        'metrics'    => 'ga:users',
		'dimensions' => 'ga:userType',  
    );
    $result3 = $ga->query($params);
	//print_r($result3['rows']);
	//print_r($result3);
	
	
?>
<div class="col-md-12">
	<h4 style="color: red;">3. New vs Returning visitor.</h4>
	<?php
	$userhtml="['User', 'Hits' ],";
	foreach($result3['rows'] as $res):
		$userhtml .= "['".$res[0]."',". $res[1]."],";
	endforeach
	?>
	<div id="chart_div_user"  style="width: 100%; height: 400px;"></div>	
</div>				
<div class=" col-md-12"><hr><hr></div>
	

<!--4.Keyword wise Audience-->
<?php
	$defaults = array(
        'start-date' => date('Y-m-d', strtotime('-1 year')),
        'end-date'   => date('Y-m-d'),
    );
    $ga->setDefaultQueryParams($defaults);
    $params = array();
    $params = array(
        'metrics'    => 'ga:visits,ga:pageviews',
		'dimensions' => 'ga:keyword',  
    );
    $result4 = $ga->query($params);
	//print_r($result4['rows']);
	
?>
<div class="col-md-12">
	<h4 style="color: red;">4.Keyword wise Audience</h4>
	<?php
	$visitorsourcehtml="['Keyword', 'Pageview', 'Visits', ],";
	foreach($result4['rows'] as $res):
		$visitorsourcehtml .= "['".$res[0]."',". $res[1].", ".$res[2]."],";
	endforeach
	?>
	<div id="chart_div_keyword"  style="width: 100%; height: 400px;"></div>	
	
</div>				
<div class=" col-md-12"><hr><hr></div>
	
<!--5. Users Flow (Top page hit)-->
<!--
<?php
	$defaults = array(
        'start-date' => date('Y-m-d', strtotime('-1 year')),
        'end-date'   => date('Y-m-d'),
    );
    $ga->setDefaultQueryParams($defaults);
    $params = array();
    $params = array(
        'metrics'    => 'ga:pageviews',
		'dimensions' => 'ga:pagePath', 
        'sort' => '-ga:pageviews',
		//'max-results' => 5,
		
    );
    $result5 = $ga->query($params);
	//print_r($result5['rows']);
?>
<div class="col-md-12">
	<h4 style="color: red;">5. Users Flow (Top page hit)</h4>
	<?php
	$pagehitshtml="['Page', 'Hits'],";
	foreach($result5['rows'] as $res):
		$pagehitshtml .= "['".$res[0]."',". $res[1]."],";
	endforeach
	?>
	<?php
	 //echo $pagehitshtml;
	?>
	
	<div id="chart_div_pagehits"  style="width: 100%; height: 400px;"></div>	
	
</div>				
<div class=" col-md-12"><hr><hr></div>
-->	

<!--6. Country -->
<?php
	$defaults = array(
        'start-date' => date('Y-m-d', strtotime('-1 year')),
        'end-date'   => date('Y-m-d'),
    );
    $ga->setDefaultQueryParams($defaults);
    $params = array();
	$params = array(
        'metrics'    => 'ga:visits,ga:pageviews',
		'dimensions' => 'ga:country', 
    );
    $result6 = $ga->query($params);
	//print_r($result6['rows']);

?>
<div class="col-md-12">
	<h4 style="color: red;">6. Country.</h4>
	<?php
	$countryhtml="['Country', 'Pageview', 'Visits', ],";
	foreach($result6['rows'] as $res):
		$countryhtml .= "['".$res[0]."',". $res[1].", ".$res[2]."],";
	 endforeach
	?>
	<div id="chart_div_country"  style="width: 100%; height: 400px;"></div>	
	
</div>				
<div class=" col-md-12"><hr><hr></div>


<!--7. Location.-->
<?php
	$defaults = array(
        'start-date' => date('Y-m-d', strtotime('-1 year')),
        'end-date'   => date('Y-m-d'),
    );
    $ga->setDefaultQueryParams($defaults);
    $params = array();
	$params = array(
        'metrics'    => 'ga:visits,ga:pageviews',
		'dimensions' => 'ga:city', 
		//'filters'=> 'ga:countryOnPage%3D%3Dindia',
		'filters'=>'ga:country==India'
    );
    $result7 = $ga->query($params);
	//print_r($result7['rows']); 
?>

<div class="col-md-12">
	<h4 style="color: red;">7. Location.</h4>
	<?php
	$cityhtml="['City', 'Pageview', 'Visits', ],";
	foreach($result7['rows'] as $res):
		$cityhtml .= "['".$res[0]."',". $res[1].", ".$res[2]."],";
	 endforeach
	?>
	<div id="chart_div_city"  style="width: 100%; height: 400px;"></div>	

</div>				
<div class=" col-md-12"><hr><hr></div>


<!--8. Browser.-->
<?php	
	$defaults = array(
        'start-date' => date('Y-m-d', strtotime('-1 year')),
        'end-date'   => date('Y-m-d'),
    );
    $ga->setDefaultQueryParams($defaults);
	$params = array();
    $params = array(
        'metrics'    => 'ga:visits,ga:pageviews',
		'dimensions' => 'ga:browser',  
    );
    $result8 = $ga->query($params);
	//print_r($result8['rows']);
?>

<div class="col-md-12">
	<h4 style="color: red;">8. Browser.</h4>
	<?php
	$browserhtml="['Browser', 'Pageview' ],";
	foreach($result8['rows'] as $res):
		$browserhtml .= "['".$res[0]."',". $res[1]."],";
	endforeach
	?>
	<div id="chart_div_browser"  style="width: 100%; height: 400px;"></div>
	
	
	<?php //echo $browserhtml; ?>
	<?php //echo $userhtml; ?>
</div>				
<div class=" col-md-12"><hr><hr></div>








<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>  
<script>
google.charts.load('current', {'packages':['corechart','bar']});
//google.charts.load('current', {'packages':['bar']});
google.charts.setOnLoadCallback(drawChart);

function drawChart() {
        var data = google.visualization.arrayToDataTable([
         <?php echo $audiencedatehtml; ?>
        ]);

        var options = {
          chart: {
            title: 'Page View and Visits',
           // subtitle: 'Test',
          }
        };

        var chart = new google.charts.Bar(document.getElementById('chart_div_audience'));
        chart.draw(data, options);
		
		
		
		var data2 = google.visualization.arrayToDataTable([
         <?php echo $visitorsourcehtml; ?>
        ]);

        var options2 = {
          chart: {
            title: 'Page View and Visits',
           // subtitle: 'Test',
		   // width: 900,
			
          }
        };

        var chart2 = new google.charts.Bar(document.getElementById('chart_div_source'));
        chart2.draw(data2, options2);
		
		
		var data3 = google.visualization.arrayToDataTable([
          <?php echo $userhtml; ?>
        ]);

        var options3 = {
          title: 'Users'
        };

        var chart3 = new google.visualization.PieChart(document.getElementById('chart_div_user'));
        chart3.draw(data3, options3);
		
		
		var data4 = google.visualization.arrayToDataTable([
         <?php echo $visitorsourcehtml; ?>
        ]);

        var options4 = {
          chart: {
            title: 'Page View and Visits',
           // subtitle: 'Test',
		   // width: 900,
			
          }
        };

        var chart4 = new google.charts.Bar(document.getElementById('chart_div_keyword'));
        chart4.draw(data4, options4);  
		
		
		/*
		var data5 = google.visualization.arrayToDataTable([
         <?php echo $pagehitshtml; ?>
        ]);

		
		var options5 = {
          chart: {
            title: 'Hits',
          }
        };

        var chart5 = new google.charts.Bar(document.getElementById('chart_div_pagehits'));
        chart5.draw(data5, options5);
		*/
		
		var data6 = google.visualization.arrayToDataTable([
         <?php echo $countryhtml; ?>
        ]);

		
		var options6 = {
          chart: {
            title: 'Page View and Visits',
          }
        };

        var chart6 = new google.charts.Bar(document.getElementById('chart_div_country'));
        chart6.draw(data6, options6);
		
		
		var data7 = google.visualization.arrayToDataTable([
         <?php echo $cityhtml; ?>
        ]);

		
		var options7 = {
          chart: {
            title: 'Page View and Visits',
          }
        };

        var chart7 = new google.charts.Bar(document.getElementById('chart_div_city'));
        chart7.draw(data7, options7);
		
		
		
		var data8 = google.visualization.arrayToDataTable([
          <?php echo $browserhtml; ?>
        ]);

        var options8 = {
          title: 'Browser'
        };

        var chart8 = new google.visualization.PieChart(document.getElementById('chart_div_browser'));
        chart8.draw(data8, options8);
		
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