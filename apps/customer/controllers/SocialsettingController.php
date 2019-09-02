<?php defined('MW_PATH') || exit('No direct script access allowed');

/**
 * SocialsettingController
 *
 * Handles the actions for social api related tasks
 *
 * @package Unika DMS
 * @author Serban George Cristian <business@unikainfocom.in>
 * @link http://www.unikainfocom.in/
 * @copyright 2013-2017 Unika DMS (http://www.unikainfocom.in/)
 * @license http://www.unikainfocom.in/support
 * @since 1.3.4.9
 */

class SocialsettingController extends Controller
{
    public function init()  
    {
        $this->onBeforeAction = array($this, '_registerJuiBs');
        parent::init();
    } 

    /**
     * Define the filters for various controller actions
     * Merge the filters with the ones from parent implementation
     */
	 
    public function filters()
    {
        $filters = array(
           // 'postOnly + delete',
        );

        return CMap::mergeArray($filters, parent::filters());
    }

    /**
     * List Api details
     */
    public function actionIndex()
    {
        $request = Yii::app()->request;
        $message = new Socialsetting('search');

		//print_r($message);
        $message->unsetAttributes();
        $message->attributes = (array)$request->getQuery($message->modelName, array());

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Social API Setting'),
            'pageHeading'     => Yii::t('messages', 'Social API Setting'),
            'pageBreadcrumbs' => array(
                Yii::t('settings', 'Settings') => $this->createUrl('settings/index'),
				Yii::t('smssetting', 'Social setting')   => $this->createUrl('socialsetting/index'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('message'));
    }

    /**
     * Create a new message
     */
    public function actionCreate()
    {
        $message = new Smssetting();
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($message->modelName, array()))) {
            $message->attributes = $attributes;
            //$message->message    = Yii::app()->ioFilter->purify(Yii::app()->params['POST'][$message->modelName]['message']);

            if (!$message->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'model'     => $message,
            )));

            if ($collection->success) {
                $this->redirect(array('smssetting/index'));
            }
        }

       // $message->fieldDecorator->onHtmlOptionsSetup = array($this, '_setEditorOptions');

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Create new Sms API Setting'),
            'pageHeading'     => Yii::t('messages', 'Create new Sms API Setting'),
            'pageBreadcrumbs' => array(
				Yii::t('settings', 'Settings') => $this->createUrl('settings/index'), 
				Yii::t('smssetting', 'Sms setting')   => $this->createUrl('smssetting/index'),
                Yii::t('app', 'Create new'),
            )
        ));

        $this->render('form', compact('message'));
    }

    /**
     * Update existing message
     */
    public function actionUpdate($id)
    {
		
        $message = Socialsetting::model()->findByPk((int)$id);

        if (empty($message)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }
      
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($message->modelName, array()))) {
            $message->attributes = $attributes;
            //$message->message    = Yii::app()->ioFilter->purify(Yii::app()->params['POST'][$message->modelName]['message']);

            if (!$message->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your form has been successfully saved!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'model'     => $message,
            )));

            if ($collection->success) {
                $this->redirect(array('socialsetting/index'));
            }
        }

        //$message->fieldDecorator->onHtmlOptionsSetup = array($this, '_setEditorOptions');

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Update Sms API Setting'),
            'pageHeading'     => Yii::t('messages', 'Update Sms API Setting'),
            'pageBreadcrumbs' => array(
                Yii::t('settings', 'Settings') => $this->createUrl('settings/index'),
                Yii::t('smssetting', 'Sms setting')   => $this->createUrl('smssetting/index'),
                Yii::t('app', 'Update'),
            )
        ));

        $this->render('form', compact('message'));
    }

    /**
     * View message
     */
    public function actionView($id)
    {
        $message = Socialsetting::model()->findByPk((int)$id);

        if (empty($message)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'View Social API Setting'),
            'pageHeading'     => Yii::t('messages', 'View Social API Setting'),
            'pageBreadcrumbs' => array(
                Yii::t('settings', 'Settings') => $this->createUrl('settings/index'),
                Yii::t('socialsetting', 'Social API setting')   => $this->createUrl('socialsetting/index'),
                Yii::t('app', 'View'),
            )
        ));

        $this->render('view', compact('message'));
    }

    


    /**
     * Callback to register Jquery ui bootstrap only for certain actions
     */
    public function _registerJuiBs($event)
    {
        if (in_array($event->params['action']->id, array('create', 'update'))) {
            $this->getData('pageStyles')->mergeWith(array(
                array('src' => Yii::app()->apps->getBaseUrl('assets/css/jui-bs/jquery-ui-1.10.3.custom.css'), 'priority' => -1001),
            ));
        }
    }
	
	
	
	
	
	
	
	
	/****----Twitter in api function----****/
	public function actionTwitterauthorise()
    {
		require_once('oauth/config.php');
		require_once('oauth/twitteroauth.php');

		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	 
		$request_token = $connection->getRequestToken(OAUTH_CALLBACK);
		
		$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
		$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];


		switch ($connection->http_code) {
		  case 200:
			$url = $connection->getAuthorizeURL($token);
			header('Location: ' . $url); 
			break;
		  default:
			//echo 'Could not connect to Twitter. Refresh the page or try again later.';
			$notify  = Yii::app()->notify;
			$notify->addError(Yii::t('app', 'Could not connect to Twitter. Refresh the page or try again later.!'));
			$this->redirect(array('socialsetting/connect'));
			
		}
		exit;
	
    }
	
	public function actionTwittercallback()
    {
        
			$notify  = Yii::app()->notify;
			
			require_once('oauth/config.php');
			require_once('oauth/twitteroauth.php');

			$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

			$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
			//save new access tocken array in session
			$_SESSION['access_token'] = $access_token;

			unset($_SESSION['oauth_token']);
			unset($_SESSION['oauth_token_secret']);

			//print_r($_SESSION['access_token']); die('yes');
			if (200 == $connection->http_code) {
			   $_SESSION['status'] = 'verified';
			   
			   
			   
				$profile_results = $connection->get('account/verify_credentials', $parameters);
				$_SESSION['t_profile_details']= $profile_results ;
				

			  //header('Location: ./connect');
			  $notify->addSuccess(Yii::t('app', 'Your are connected, You can now post status!'));
			  $this->redirect(array('socialsetting/connect')); 
			} else {
			  //header('Location: ./connect');
			  $notify->addError(Yii::t('app', 'Something went wrong, please try again!'));
			  $this->redirect(array('socialsetting/connect'));
			 
			} 
			
    }
	
	
	
	
	/****----Linkedin in api function----****/
	public function actionLinkedinauthorise()
    {
	
		include_once 'linkedin2/config.php';
		include_once 'linkedin2/linkedinoAuth.php';

		//    echo date('l jS \of F Y h:i:s A',"1352203709");
		# First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
		$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['base_url'] . '/index.php?login=true' );
		$linkedin->debug = true;

		//print_r($linkedin); //die('ok');
		# Now we retrieve a request token. It will be set as $linkedin->request_token
		$linkedin->getRequestToken();

		//print_r($linkedin->request_token); die('ok');
		$_SESSION['requestToken'] = serialize($linkedin->request_token);

		# With a request token in hand, we can generate an authorization URL, which we'll direct the user to
		## echo "Authorization URL: " . $linkedin->generateAuthorizeUrl() . "\n\n";
		header("Location: " . $linkedin->generateAuthorizeUrl());

		exit;
	
    }
	
	public function actionLinkedincallback()
    {
			
			$notify  = Yii::app()->notify;
			
			require_once('linkedin2/config.php');
			require_once('linkedin2/linkedinoAuth.php');
			require_once('linkedin2/class.linkedClass.php');
			
		
			$linkedClass   =   new linkedClass();
			# First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
			$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret']);
			//$linkedin->debug = true;
			 

			if (isset($_REQUEST['oauth_verifier']))
			{
				$_SESSION['oauth_verifier']     = $_REQUEST['oauth_verifier'];

				$linkedin->request_token    =   unserialize($_SESSION['requestToken']);
				$linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
				$linkedin->getAccessToken($_REQUEST['oauth_verifier']);
				$_SESSION['oauth_access_token'] = serialize($linkedin->access_token);
				
				
				
	
				$company_results= $linkedClass->linkedInGetCompany($_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token']);
				//print_r($profile_results);
				//print_r(json_decode($profile_results));
				$l_all_companies = json_decode($company_results);
				$_SESSION['l_all_companies']= $l_all_companies ;
				
				
				$profile_results= $linkedClass->linkedinGetUserInfo($_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token']);
				$l_profile_details = json_decode($profile_results);
				$_SESSION['l_profile_details']= $l_profile_details ;

			
				//header('Location: ./connect');
				$notify->addSuccess(Yii::t('app', 'Your are connected, You can now post status!'));
				$this->redirect(array('socialsetting/connect')); 
			}
			else
			{
				$linkedin->request_token    =   unserialize($_SESSION['requestToken']);
				$linkedin->oauth_verifier   =   $_SESSION['oauth_verifier'];
				$linkedin->access_token     =   unserialize($_SESSION['oauth_access_token']);
				
				//header('Location: ./connect');
			    $notify->addError(Yii::t('app', 'Something went wrong, please try again!'));
			    $this->redirect(array('socialsetting/connect'));
			}
	
		 
			
			
    }
	
	public function actionLinkedinpagesave()
    {
			
		$notify  = Yii::app()->notify;
			
		$_SESSION['l_page_id']= $_REQUEST['page_id'];	
		//header('Location: ./connect');
		$notify->addSuccess(Yii::t('app', 'Selected Company has been saved, You can now post status!'));
		$this->redirect(array('socialsetting/connect')); 
	
		
    }
	
	
	
	
	
	
	/****----Facebook in api function----****/
	public function actionFacebookauthorise()
    {
	
		include_once 'linkedin/config.php';
		include_once 'linkedin/linkedinoAuth.php';

		//    echo date('l jS \of F Y h:i:s A',"1352203709");
		# First step is to initialize with your consumer key and secret. We'll use an out-of-band oauth_callback
		$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret'], $config['base_url'] . '/index.php?login=true' );
		$linkedin->debug = true;

		//print_r($linkedin); //die('ok');
		# Now we retrieve a request token. It will be set as $linkedin->request_token
		$linkedin->getRequestToken();

		//print_r($linkedin->request_token); die('ok');
		$_SESSION['requestToken'] = serialize($linkedin->request_token);

		# With a request token in hand, we can generate an authorization URL, which we'll direct the user to
		## echo "Authorization URL: " . $linkedin->generateAuthorizeUrl() . "\n\n";
		header("Location: " . $linkedin->generateAuthorizeUrl());

		exit;
	
    }
	
	public function actionFacebookcallback()
    {
		//session_start();
		require_once 'Facebook/autoload.php';
		//include_once 'facebook/src/facebook.php';
		$message = Socialsetting::model()->findByPk('1');
		
		$facebook = new Facebook\Facebook(array(
		  'app_id' => $message->facebook_app_id,
		  'app_secret' => $message->facebook_app_secret,
		  'default_graph_version' => 'v2.10',
		));
		
		$helper = $facebook->getRedirectLoginHelper();
		$accessToken = $helper->getAccessToken();
		
		//$user_id = $helper->getUserId();
		$_SESSION['facebook_access_token'] = (string)$accessToken;
		
		/*$user_detail = $facebook->get('/me/accounts', $_SESSION['facebook_access_token']);
		$graphEdge = $user_detail->getGraphEdge();*/
		$user_detail = $facebook->get('/me/accounts?fields=picture,id,category,name,access_token',$_SESSION['facebook_access_token']);
		$graphEdge = $user_detail->getGraphEdge();

		
		foreach ($graphEdge as $graphNode => $graphValue) 
		{

			$_SESSION['pages'][$graphValue['id']] = array('page_access_token' => $graphValue['access_token'], 'page_category' => $graphValue['category'], 'page_name' => $graphValue['name'], 'picture_url' => $graphValue['picture']['url']);
		}
		
		/*if($_GET['fbTrue'] == 'true11' )
		{
			$token_url = "https://graph.facebook.com/oauth/access_token?"
			. "client_id=".$config['App_ID']."&redirect_uri=" . urlencode($config['callback_url'])
			. "11&client_secret=".$config['App_Secret']."&code=" . $_GET['code'];
		}
		else
		{
			$token_url = "https://graph.facebook.com/oauth/access_token?"
			. "client_id=".$config['App_ID']."&redirect_uri=" . urlencode($config['callback_url'])
			. "&client_secret=".$config['App_Secret']."&code=" . $_GET['code'];
		}*/
		
		/*$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$token_url);
		$result=curl_exec($ch);
		curl_close($ch);
		$response = json_decode($result, true);*/
		//$response = json_decode(file_get_contents($token_url));
		//var_dump($response);exit;
		//$params = null;
		//parse_str($response, $params);

		//$graph_url = "https://graph.facebook.com/v2.1/me?access_token=". $response['access_token'];
		
			
		/*$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$graph_url);
		$usr_result=curl_exec($ch);
		curl_close($ch);
		$usr_response = json_decode($usr_result, true);*/

		/*if($_GET['fbTrue'] == 'true' )
		{
			$publish = $facebook->api('/'.$usr_response['id'].'/EData Media:publish_actions&feed', 'post',
				array(
					'access_token' => $response['access_token'],
					'message'=>'This Messsage published by PHPGang.com Demo.',
					'from' => $config['App_ID'],
				)
			);
				print_r($publish);exit;
			
			
			$publish = $facebook->api('/'.$group_id.'/feed', 'post',
				array(
					'access_token' => $_SESSION['token'],'message'=>$_POST['status'] .'   via PHPGang.com Demo',
					'from' => $config['App_ID']
				)
			);
			print_r($publish);exit;
			$message = 'Default status updated.<br>';
		}*/
		
		$notify  = Yii::app()->notify;		
		//$_SESSION['fToken'] = '85187344b0da322bc7eb5ebf1c299354';
		//header('Location: ./connect');
		$notify->addSuccess(Yii::t('app', 'Your are connected, You can now post status!'));
		$this->redirect(array('socialsetting/connect')); 
    }
	
	
	public function actionConnect()
    {
     
		//require 'facebook/src/config.php';
		//require 'facebook/src/facebook.php';

		require_once 'Facebook/autoload.php';
		$message = Socialsetting::model()->findByPk('1');
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;
		
		$fb = new Facebook\Facebook([
		  'app_id' => $message->facebook_app_id, // Replace {app-id} with your app id
		  'app_secret' => $message->facebook_app_secret,
		  'default_graph_version' => 'v2.10',
		]);
		
		$helper = $fb->getRedirectLoginHelper();

		$permissions = ['email', 'publish_actions','user_photos', 'manage_pages', 'publish_pages', 'pages_show_list']; // Optional permissions
		$loginUrl = $helper->getLoginUrl('https://new2managevip.beelift.com/customer/index.php/socialsetting/facebookcallback?fbTrue=true', $permissions);

        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Connect'),
            'pageHeading'     => Yii::t('messages', 'Connect to Social Network'),
            'pageBreadcrumbs' => array(
				Yii::t('settings', 'Social Marketing') => $this->createUrl('socialsetting/connect'), 
				Yii::t('socialsetting', ' Connect to Social Network')   => $this->createUrl('socialsetting/connect'),
                Yii::t('app', 'Connect'),
            )
        ));
 
        $this->render('connect', compact('message','loginUrl'));
    }
	
	
	public function actionPosting()
    {
		$request = Yii::app()->request;
        $notify  = Yii::app()->notify;
		$customer   = Yii::app()->customer->getModel();
		$cid =Yii::app()->customer->getId();
		
		$api_message = Socialsetting::model()->findByPk('1');
		$message = new Socialpost();
        
		require_once 'Facebook/autoload.php';
		//require_once('oauth/config.php');
		//require_once('oauth/twitteroauth.php');
		//require_once('facebook/src/config.php');
		//require_once('facebook/src/facebook.php');
		
		//$access_token = $_SESSION['access_token'];
		//$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		$fb = new Facebook\Facebook([
		  'app_id' => $api_message->facebook_app_id,
		  'app_secret' => $api_message->facebook_app_secret,
		  'fileUpload' => true,
		  'default_graph_version' => 'v2.10',
		]);
			
		

        if ($request->isPostRequest && ($attributes = (array)$request->getPost($message->modelName, array()))) 
		{
			$base_url = 'https://'.Yii::app()->getRequest()->serverName;
			$target_dir = "../myuploads/";
			$response = '';
			
			if(empty($_FILES["Socialpost"]["name"]['imagename']) && empty($_FILES["Socialpost"]["name"]['videoname'])&& !empty($attributes['text'])){
				$data = [
				  'message' => $attributes['text'],
				];
				$response = $fb->post('/'.$_SESSION['facebook_page_id'].'/feed', $data, $_SESSION['page_access_token']);
			}else if($_FILES["Socialpost"]["name"]['imagename'] != ''){
				
				//facebook image upload code .
				$uploaded=0;
				$target_file = $target_dir . basename($_FILES["Socialpost"]["name"]['imagename']);
				$target_file = $target_dir . $_FILES["Socialpost"]["name"]['imagename'];
				if (copy($_FILES["Socialpost"]["tmp_name"]['imagename'], $target_file)) {
					$uploaded=1;
					$filename = $_FILES["Socialpost"]["name"]['imagename'];
				}
				$data = [
				  'url' => $base_url.'/myuploads/'.$filename,
				  'caption' => $attributes['text'],
				];
				$response = $fb->post('/'.$_SESSION['facebook_page_id'].'/photos', $data, $_SESSION['page_access_token']);
				
			}elseif($_FILES["Socialpost"]["name"]['videoname'] != ''){
				
				//facebook video upload code .
				$uploaded_video=0;
				$target_video = $target_dir. basename($_FILES["Socialpost"]["name"]['videoname']);
				$target_video = $target_dir. $_FILES["Socialpost"]["name"]['videoname'];
				if(copy($_FILES["Socialpost"]["tmp_name"]['videoname'],$target_video)){
					$uploaded_video=1;
					$video_filename = $_FILES["Socialpost"]["name"]['videoname'];
				}
				
				$data = [
					'title' => 'Edata App',
					'description' => $attributes['text'],
					'file_url' => $base_url.'/myuploads/'.$video_filename,
				];
				
				$response = $fb->post('/'.$_SESSION['facebook_page_id'].'/videos', $data, $_SESSION['page_access_token']);
			}
			$response_array = $response->getDecodedBody();
			
			if(is_array($response_array)){
				$message->customer_id = $customer->customer_id;
				$message->text = $attributes['text'];
				if(!empty($filename)){
					$message->image_url = $filename;
				}elseif(!empty($video_filename)){
					$message->video_url = $video_filename;
				}
				$message->facebook_postid = $response_array['id'];
				$message->save();
			}

			if (empty($response_array['id'])) {
                $notify->addError(Yii::t('app', 'Your Messsage not Post Yet! Please Try Again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your Messsage Post Successfully!'));
            }
			
			/*---posting---*/			
			//require_once('oauth/config.php');
			//require_once('oauth/twitteroauth.php');
			
			/*$access_token = $_SESSION['access_token'];
			$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
			
			if(strlen($status)>=130)
			{
				$status = substr($status,0,130);
			}		
			//$results = $connection->post('statuses/update', array('status' => $status));
	
			if($uploaded==1){
				$site_url='http://socialstark.com/login';
				$mediaContent = file_get_contents($site_url."/myuploads/".$filename);
				$params = array(
					'media' => base64_encode($mediaContent),
				);
				$media1 = $connection->uploadn('https://upload.twitter.com/1.1/media/upload.json', $params);
				//print_r($media1);
				
				$parameters = [
					'status' => $status ,
					'media_ids' => implode(',', [$media1->media_id_string]),
				];
						
			}else{
				$parameters = [
					'status' => $status 
				];
				
			}
			$results = $connection->post('statuses/update', $parameters);
			$twitter_postid= @$results->id;
			//print_r($results); die('ok');
			
			//$results = $connection->get('statuses/user_timeline', array());
			//$results = $connection->get('statuses/show/829820022751113217', array());
			//print_r($results); die('ok');
			
			
			/*--linkedin post----*/
			//require_once('linkedin/config.php');
			//require_once('linkedin/linkedinoAuth.php');
			//require_once('linkedin/class.linkedClass.php');*/

			/*$linkedClass   =   new linkedClass();
			$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret']);*/
			//print_r($linkedin);
			

			//$title = "Test post "; 
			/*$title = substr($status,'0,15');  
			$targetUrl = "http://unikainfocom.in";
			$description= $status ;
					
			$xml = "<share>";
			$xml .= "<content>
			<title>$title</title>
			<description>$description</description>";
			if(!empty($targetUrl)){
				$xml .= "<submitted-url>$targetUrl</submitted-url>";
			}
			if($uploaded==1){
				$site_url='http://socialstark.com/login';
				$imgUrl = $site_url."/myuploads/".$filename;
				$xml .= "<submitted-image-url>$imgUrl</submitted-image-url>";
			}
			
				
			$xml .= "</content>
			  <visibility>
				<code>anyone</code>
			  </visibility>
			</share>";
	
	
			//$results= $linkedClass->linkedInStatus($status, $_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token']);
			$results= $linkedClass->linkedInshareStatus($xml, $_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token']);
			//print_r($results);
			$json_result = json_decode($results);
			$linkedin_postid= $json_result->updateKey;
			//print_r(json_decode($results));			
			//die('ok');
			
		
			
			$text = $attributes['text'];
			$message->text = $text;
			//$message->twitter_postid = '829820022751113217';
			$message->twitter_postid = $twitter_postid;
			$message->linkedin_postid = $linkedin_postid;
			$message->customer_id = $customer->customer_id;
			$message->save();

			$message->attributes = $attributes;
            if (!$message->save()) {
                $notify->addError(Yii::t('app', 'Your form has a few errors, please fix them and try again!'));
            } else {
                $notify->addSuccess(Yii::t('app', 'Your status has been posted!'));
            }

            Yii::app()->hooks->doAction('controller_action_save_data', $collection = new CAttributeCollection(array(
                'controller'=> $this,
                'success'   => $notify->hasSuccess,
                'model'     => $message,
            )));

            if ($collection->success) {
                $this->redirect(array('socialsetting/posting'));
            }*/
        }

   
        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Post Status'),
            'pageHeading'     => Yii::t('messages', 'Post Status'),
            'pageBreadcrumbs' => array(
				Yii::t('settings', 'Social Marketing') => $this->createUrl('socialsetting/connect'), 
				Yii::t('socialsetting', 'Post status')   => $this->createUrl('socialsetting/posting'),
                Yii::t('app', 'Post'),
            )
        ));

        $this->render('form', compact('message'));
    }
	
	
	public function actionPostlist()
    {
        $request = Yii::app()->request;
        //$link = new Socialpost('search');
        $link = new Socialpost('search');
        $link->unsetAttributes();

        $link->attributes  = (array)$request->getQuery($link->modelName, array());
        $link->customer_id = (int)Yii::app()->customer->getId();
		
        $this->setData(array(
            'pageMetaTitle'     => $this->data->pageMetaTitle . ' | '. Yii::t('links', 'Posted Status'),
            'pageHeading'       => Yii::t('links', 'Posted Status'),
            'pageBreadcrumbs'   => array(
                Yii::t('links', 'Posted Status') => $this->createUrl('socialsetting/postlist'),
                Yii::t('app', 'View all')
            )
        ));

        $this->render('list', compact('link'));
    }
	

	
	public function actionFacebookstatus()
    {
     
		$message = new Socialpost();
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

		require_once('oauth/config.php');
		require_once('oauth/twitteroauth.php');
		
		$access_token = $_SESSION['access_token'];
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		
		//$results = $connection->get('statuses/user_timeline', array());
		$results = $connection->get('statuses/show/829820022751113217', array());
		//print_r($results); die('ok');
		$message->report= $result;
		
			
        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Connect'),
            'pageHeading'     => Yii::t('messages', 'Connect to Social Network'),
            'pageBreadcrumbs' => array(
				Yii::t('settings', 'Social Marketing') => $this->createUrl('socialsetting/connect'), 
				Yii::t('socialsetting', ' Connect to Social Network')   => $this->createUrl('socialsetting/connect'),
                Yii::t('app', 'Connect'),
            )
        ));
 
        $this->render('connect', compact('message'));
    }
	
	public function actionTwitterstatus($id)
    {
     
		//print_r($id);
		$post = Socialpost::model()->findByPk((int)$id);
		$postid= @$post->twitter_postid;
		//print_r($post->twitter_postid);
		
		$message = new Socialpost();
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

		require_once('oauth/config.php');
		require_once('oauth/twitteroauth.php');
		
		$access_token = $_SESSION['access_token'];
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		
		//$res = $connection->get('statuses/user_timeline', array());
		//print_r($res); die('end'); 
		
		
		//$results = $connection->get('statuses/show/829820022751113217', array());
		$results = $connection->get('statuses/show/'.$postid, array());
		//print_r($results); die('ok');
		$report= $results;
		
			
        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Twitter Status report'),
            'pageHeading'     => Yii::t('messages', 'Twitter Status report'),
            'pageBreadcrumbs' => array(
				Yii::t('settings', 'Social Marketing') => $this->createUrl('socialsetting/connect'), 
				Yii::t('socialsetting', ' Connect to Social Network')   => $this->createUrl('socialsetting/connect'),
                Yii::t('app', 'Report'),
            )
        ));
 
        $this->render('twitterstatus', compact('message','report'));
    }
	
	public function actionLinkedinstatus($id=null)
    {
     
		
		//print_r($id);
		$post = Socialpost::model()->findByPk((int)$id);
		$postid= @$post->linkedin_postid;
		//print_r($post);
		//echo $postid; die();
		
		$message = new Socialpost();
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

		require_once('linkedin2/config.php');
		require_once('linkedin2/linkedinoAuth.php');
		require_once('linkedin2/class.linkedClass.php');
	
		$linkedClass   =   new linkedClass();
		$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret']);
		

		//$company_results= $linkedClass->linkedInGetCompany($_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token']);
		$results= $linkedClass->linkedInGetReport($_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token'],$postid);
		$json_results= json_decode($results);
		//print_r($json_results->updateComments); ;
		//print_r(json_decode($results));   
		//die('okji');
		$report= $json_results;
		
			
        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Linkedin Status report'),
            'pageHeading'     => Yii::t('messages', 'Linkedin Status report'),
            'pageBreadcrumbs' => array(
				Yii::t('settings', 'Social Marketing') => $this->createUrl('socialsetting/connect'), 
				Yii::t('socialsetting', ' Connect to Social Network')   => $this->createUrl('socialsetting/connect'),
                Yii::t('app', 'Report'),
            )
        ));
 
        $this->render('linkedinstatus', compact('message','report'));

    }
	
	
	
	
	
	/**
     * Delete an existing link
     */
    public function actionDelete($id)
    {
        //die('ok');
		$message = Socialpost::model()->findByPk((int)$id);

        if (empty($message)) {
            throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
        }

        $message->delete();

        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

        $redirect = null;
        if (!$request->getQuery('ajax')) {
            $notify->addSuccess(Yii::t('app', 'The item has been successfully deleted!'));
            $redirect = $request->getPost('returnUrl', array('socialsetting/postlist'));
        }

        // since 1.3.5.9
        Yii::app()->hooks->doAction('controller_action_delete_data', $collection = new CAttributeCollection(array(
            'controller' => $this,
            'model'      => $message,
            'redirect'   => $redirect,
        )));

        if ($collection->redirect) {
            $this->redirect($collection->redirect);
        }
    }
	
	
	
	
	public function actionTest()
    {
		
			require_once('linkedin2/config.php');
			require_once('linkedin2/linkedinoAuth.php');
			require_once('linkedin2/class.linkedClass.php');
		
			$linkedClass   =   new linkedClass();
			$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret']);
			

			//$company_results= $linkedClass->linkedInGetCompany($_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token']);
			$company_results= $linkedClass->linkedInGetReport($_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token']);
			print_r($company_results); die('okji');
			//print_r(json_decode($profile_results));
			//$l_all_companies = json_decode($company_results);
		
			
			 //http://api.linkedin.com/v1/companies/13248997/updates/key={update-key}/update-comments 
			 
			 
			 
			 
	}
	
	public function actionTwitterdashboard()
    {

		$message = new Socialpost();
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;

		require_once('oauth/config.php');
		require_once('oauth/twitteroauth.php');
		
		$access_token = $_SESSION['access_token'];
		$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
		
		$results = $connection->get('statuses/user_timeline', array());
		//print_r($results); die('end'); 
		$reports =$results; 
		
	
        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Twitter'),
            'pageHeading'     => Yii::t('messages', 'Twitter'),
            'pageBreadcrumbs' => array(
				Yii::t('settings', 'Social Marketing') => $this->createUrl('socialsetting/connect'), 
				Yii::t('socialsetting', ' Connect to Social Network')   => $this->createUrl('socialsetting/connect'),
                Yii::t('app', 'Twitter'),
            )
        ));
 
        $this->render('twitterdashboard', compact('message','reports'));
    }
	
	
	
	
	
	
	
	
	public function actionLinkedindashboard()
    {

		$message = new Socialpost();
        $request = Yii::app()->request;
        $notify  = Yii::app()->notify;
		
		require_once('linkedin2/config.php');
		require_once('linkedin2/linkedinoAuth.php');
		require_once('linkedin2/class.linkedClass.php');
	
		$linkedClass   =   new linkedClass();
		$linkedin = new LinkedIn($config['linkedin_access'], $config['linkedin_secret']);
		

		$results= $linkedClass->linkedInGetTimeline($_SESSION['requestToken'], $_SESSION['oauth_verifier'], $_SESSION['oauth_access_token'],$_SESSION['l_page_id']);
		//print_r(json_decode($profile_results));
		//print_r($results); die('end'); 
		$reports =$results; 
		$reports =json_decode($reports); 
		
	
        $this->setData(array(
            'pageMetaTitle'   => $this->data->pageMetaTitle . ' | '. Yii::t('messages', 'Linkedin'),
            'pageHeading'     => Yii::t('messages', 'Linkedin'),
            'pageBreadcrumbs' => array(
				Yii::t('settings', 'Social Marketing') => $this->createUrl('socialsetting/connect'), 
				Yii::t('socialsetting', ' Connect to Social Network')   => $this->createUrl('socialsetting/connect'),
                Yii::t('app', 'Linkedin'),
            )
        ));
 
        $this->render('linkedindashboard', compact('message','reports'));
    }
	
	
}
