<?php defined('MW_PATH') || exit('No direct script access allowed'); 

$ip = $_SERVER['REMOTE_ADDR'];
$details = json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip={$ip}"));
$filtercountry = @$details->geoplugin_countryName; // -> "India"
//print_r( $details);//die('ok ji');



   // From the APIs console
    $client_id = '486869175061-1mc3ujrcsn5iv9sbrbefaeurnd8bsrgj.apps.googleusercontent.com';
    // From the APIs console
    $client_secret = 'fCMAKkpDAGPfO_Fok-Lvw2mt';
     // Url to your this page, must match the one in the APIs console
    $redirect_uri = 'http://email.unikainfocom.net/customer/index.php/account/analyticsreport';
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

?>
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


<?php
		$delim =  ',' ;
		$csv_output ='';

		
		$csv_output .= "1. Audience Overview (Date Time wise)".$delim;
		$csv_output .= "\n"; 		
		$csv_output .= "Date".$delim;
		$csv_output .= "Pageviews".$delim;
		$csv_output .= "Visits".$delim;
		$csv_output .= "\n";
		foreach($result1['rows'] as $res){
		
				$CSV_SEPARATOR = ",";
				$CSV_NEWLINE = "\r\n";

				$csv_output .=  $res[0].$delim;
				$csv_output .=  $res[1].$delim;
				$csv_output .=  $res[2].$delim;
				$csv_output .= "\n"; 			

		}
		
		
		$csv_output .= "\n";
		$csv_output .= "\n";
		$csv_output .= "2. Visitor Source".$delim;
		$csv_output .= "\n"; 		
		$csv_output .= "Source".$delim;
		$csv_output .= "Pageviews".$delim;
		$csv_output .= "Visits".$delim;
		$csv_output .= "\n";
		foreach($result2['rows'] as $res){
		
				$CSV_SEPARATOR = ",";
				$CSV_NEWLINE = "\r\n";

				$csv_output .=  $res[0].$delim;
				$csv_output .=  $res[1].$delim;
				$csv_output .=  $res[2].$delim;
				$csv_output .= "\n"; 			

		}	

		
		$csv_output .= "\n";
		$csv_output .= "\n";
		$csv_output .= "3. New vs Returning visitor".$delim;
		$csv_output .= "\n"; 		
		$csv_output .= "User".$delim;
		$csv_output .= "Hits".$delim;
		$csv_output .= "\n";
		foreach($result3['rows'] as $res){
		
				$CSV_SEPARATOR = ",";
				$CSV_NEWLINE = "\r\n";

				$csv_output .=  $res[0].$delim;
				$csv_output .=  $res[1].$delim;
				$csv_output .= "\n"; 			

		}
		
		
		$csv_output .= "\n";
		$csv_output .= "\n";
		$csv_output .= "4.Keyword wise Audience".$delim;
		$csv_output .= "\n"; 		
		$csv_output .= "Keyword".$delim;
		$csv_output .= "Pageviews".$delim;
		$csv_output .= "Visits".$delim;
		$csv_output .= "\n";
		foreach($result4['rows'] as $res){
		
				$CSV_SEPARATOR = ",";
				$CSV_NEWLINE = "\r\n";

				$csv_output .=  $res[0].$delim;
				$csv_output .=  $res[1].$delim;
				$csv_output .=  $res[2].$delim;
				$csv_output .= "\n"; 			

		}	
		
		
		
		$csv_output .= "\n";
		$csv_output .= "\n";
		$csv_output .= "5. Users Flow (Top page hit)".$delim;
		$csv_output .= "\n"; 		
		$csv_output .= "Pages".$delim;
		$csv_output .= "Pageviews".$delim;
		$csv_output .= "\n";
		foreach($result5['rows'] as $res){
		
				$CSV_SEPARATOR = ",";
				$CSV_NEWLINE = "\r\n";

				$csv_output .=  $res[0].$delim;
				$csv_output .=  $res[1].$delim;
				$csv_output .= "\n"; 			

		}
		
		
		$csv_output .= "\n";
		$csv_output .= "\n";
		$csv_output .= "6. Country".$delim;
		$csv_output .= "\n"; 		
		$csv_output .= "Country".$delim;
		$csv_output .= "Pageviews".$delim;
		$csv_output .= "Visits".$delim;
		$csv_output .= "\n";
		foreach($result6['rows'] as $res){
		
				$CSV_SEPARATOR = ",";
				$CSV_NEWLINE = "\r\n";

				$csv_output .=  $res[0].$delim;
				$csv_output .=  $res[1].$delim;
				$csv_output .=  $res[2].$delim;
				$csv_output .= "\n"; 			

		}
		
		
		$csv_output .= "\n";
		$csv_output .= "\n";
		$csv_output .= "7. Location ".$delim;
		$csv_output .= "\n"; 		
		$csv_output .= "City".$delim;
		$csv_output .= "Pageviews".$delim;
		$csv_output .= "Visits".$delim;
		$csv_output .= "\n";
		foreach($result7['rows'] as $res){
		
				$CSV_SEPARATOR = ",";
				$CSV_NEWLINE = "\r\n";

				$csv_output .=  $res[0].$delim;
				$csv_output .=  $res[1].$delim;
				$csv_output .=  $res[2].$delim;
				$csv_output .= "\n"; 			

		}
		
		
		$csv_output .= "\n";
		$csv_output .= "\n";
		$csv_output .= "8. Browser ".$delim;
		$csv_output .= "\n"; 		
		$csv_output .= "Browser".$delim;
		$csv_output .= "Pageviews".$delim;
		$csv_output .= "Visits".$delim;
		$csv_output .= "\n";
		foreach($result7['rows'] as $res){
		
				$CSV_SEPARATOR = ",";
				$CSV_NEWLINE = "\r\n";

				$csv_output .=  $res[0].$delim;
				$csv_output .=  $res[1].$delim;
				$csv_output .=  $res[2].$delim;
				$csv_output .= "\n"; 			

		}
		
	
		// while loop main first
		header("Content-Type: application/force-download\n");
		header("Cache-Control: cache, must-revalidate");   
		header("Pragma: public");
		header("Content-Disposition: attachment; filename=exports_analytics_" .date("Ymd") . ".csv");
		print $csv_output;
		exit;
		
		
?>


