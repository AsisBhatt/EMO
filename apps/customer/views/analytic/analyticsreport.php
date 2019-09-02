<?php defined('MW_PATH') || exit('No direct script access allowed');

$ip = $_SERVER['REMOTE_ADDR'];
$details = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}"));
$filtercountry = @$details->geoplugin_countryName; // -> "India"
//print_r( $details);//die('ok ji');

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
    $redirect_uri = 'http://socialstark.com/login/customer/index.php/analytic/analyticsreport';
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
	
	
	<div class="col-md-3">
	<a href="http://socialstark.com/login/customer/index.php/analytic/analyticsreportgraph"><button type="button" class="btn btn-primary btn-submit" >Switch to Graph Report</button></a>
	</div>	
	
	<div class="col-md-2">
	<a href="http://socialstark.com/login/customer/index.php/analytic/analyticsexport"><button type="button" class="btn btn-primary" >Export</button></a>
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
	<table class="table table-bordered table-hover">
	<tr>
	  <th>Date </th>
	  <th>Pageviews</th>  
	  <th>Visits</th>
	</tr>
	</tr>
	<?php
	foreach($result1['rows'] as $res):
	?>
	<tr>
	  <td><?php echo $res[0] ?></td>
	  <td><?php echo $res[1] ?></td>
	  <td><?php echo $res[2] ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
		<tr>
		  <th>Total Pageviews:</th>
		  <td><?php echo $result1['totalsForAllResults']['ga:pageviews'] ; ?>
		</tr>
		<tr>
		  <th>Total Visits:</th>
		  <td><?php echo $result1['totalsForAllResults']['ga:visits']; ?></td>
		</tr>
	</table>
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
	<table class="table table-bordered table-hover">
	<tr>
	  <th>Source </th>
	  <th>Pageviews</th>
	  <th>Visits</th>
	</tr>
	</tr>
	<?php
	foreach($result2['rows'] as $res):
	?>
	<tr>
	  <td><?php echo $res[0] ?></td>
	  <td><?php echo $res[1] ?></td>
	  <td><?php echo $res[2] ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
		<tr>
		  <th>Total Pageviews:</th>
		  <td><?php echo $result2['totalsForAllResults']['ga:pageviews'] ; ?>
		</tr>
		<tr>
		  <th>Total Visits:</th>
		  <td><?php echo $result2['totalsForAllResults']['ga:visits']; ?></td>
		</tr>
	</table>
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
	<table class="table table-bordered table-hover">
	<tr>
	  <th>User </th>
	  <th>Hits</th>
	</tr>
	</tr>
	<?php
	foreach($result3['rows'] as $res):
	?>
	<tr>
	  <td><?php echo $res[0] ?></td>
	  <td><?php echo $res[1] ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
		<tr>
		  <th>Total Hits:</th>
		  <td><?php echo $result3['totalsForAllResults']['ga:users']; ?></td>
		</tr>
	</table>
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
	<table class="table table-bordered table-hover">
	<tr>
	  <th>Keyword </th>
	  <th>Pageviews</th>
	  <th>Visits</th>
	</tr>
	</tr>
	<?php
	foreach($result4['rows'] as $res):
	?>
	<tr>
	  <td><?php echo $res[0] ?></td>
	  <td><?php echo $res[1] ?></td>
	  <td><?php echo $res[2] ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
		<tr>
		  <th>Total Pageviews:</th>
		  <td><?php echo $result4['totalsForAllResults']['ga:pageviews'] ; ?>
		</tr>
		<tr>
		  <th>Total Visits:</th>
		  <td><?php echo $result4['totalsForAllResults']['ga:visits']; ?></td>
		</tr>
	</table>
</div>				
<div class=" col-md-12"><hr><hr></div>
	
<!--5. Users Flow (Top page hit)-->
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
	<table class="table table-bordered table-hover">
	<tr>
	  <th>Pages </th>
	  <th>Pageviews</th>
	</tr>
	</tr>
	<?php
	foreach($result5['rows'] as $res):
	?>
	<tr>
	  <td><?php echo $res[0] ?></td>
	  <td><?php echo $res[1] ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
		<tr>
		  <th>Total Pageviews:</th>
		  <td><?php echo $result5['totalsForAllResults']['ga:pageviews'] ; ?>
		</tr>
		
	</table>
</div>				
<div class=" col-md-12"><hr><hr></div>
	

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
	<table class="table table-bordered table-hover">
	<tr>
	  <th>Country </th>
	  <th>Pageviews</th>
	  <th>Visits</th>
	</tr>
	</tr>
	<?php
	foreach($result6['rows'] as $res):
	?>
	<tr>
	  <td><?php echo $res[0] ?></td>
	  <td><?php echo $res[1] ?></td>
	  <td><?php echo $res[2] ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
		<tr>
		  <th>Total Pageviews:</th>
		  <td><?php echo $result6['totalsForAllResults']['ga:pageviews'] ; ?>
		</tr>
		<tr>
		  <th>Total Visits:</th>
		  <td><?php echo $result6['totalsForAllResults']['ga:visits']; ?></td>
		</tr>
	</table>
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
		//'filters'=>'ga:country==India'
		'filters'=>'ga:country=='.$filtercountry.''
    );
    $result7 = $ga->query($params);
	//print_r($result7['rows']); 
?>

<div class="col-md-12">
	<h4 style="color: red;">7. Location.</h4>
	<table class="table table-bordered table-hover">
	<tr>
	  <th>City </th>
	  <th>Pageviews</th>
	  <th>Visits</th>
	</tr>
	</tr>
	<?php
	foreach($result7['rows'] as $res):
	?>
	<tr>
	  <td><?php echo $res[0] ?></td>
	  <td><?php echo $res[1] ?></td>
	  <td><?php echo $res[2] ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
		<tr>
		  <th>Total Pageviews:</th>
		  <td><?php echo $result7['totalsForAllResults']['ga:pageviews'] ; ?>
		</tr>
		<tr>
		  <th>Total Visits:</th>
		  <td><?php echo $result7['totalsForAllResults']['ga:visits']; ?></td>
		</tr>
	</table>
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
	<table class="table table-bordered table-hover">
	<tr>
	  <th>Browser </th>
	  <th>Pageviews</th>
	  <th>Visits</th>
	</tr>
	</tr>
	<?php
	foreach($result8['rows'] as $res):
	?>
	<tr>
	  <td><?php echo $res[0] ?></td>
	  <td><?php echo $res[1] ?></td>
	  <td><?php echo $res[2] ?></td>
	</tr>
	<?php
	endforeach
	?>
	</table>

	<table>
		<tr>
		  <th>Total Pageviews:</th>
		  <td><?php echo $result8['totalsForAllResults']['ga:pageviews'] ; ?>
		</tr>
		<tr>
		  <th>Total Visits:</th>
		  <td><?php echo $result8['totalsForAllResults']['ga:visits']; ?></td>
		</tr>
	</table>
</div>				
<div class=" col-md-12"><hr><hr></div>





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