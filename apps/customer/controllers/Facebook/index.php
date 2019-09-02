<?php
session_start();
require 'src/config.php';
require 'src/facebook.php';
// Create our Application instance (replace this with your appId and secret).
$facebook = new Facebook(array(
  'appId'  => $config['App_ID'],
  'secret' => $config['App_Secret'],
  'cookie' => true
));

ini_set("display_errors",1);
if(isset($_GET['logout']))
{
    $url = 'https://www.facebook.com/logout.php?next=' . urlencode('http://demo.phpgang.com/post-status-facebook/') .
      '&access_token='.$_GET['tocken'];
    session_destroy();
    header('Location: '.$url);
}
if(isset($_POST['status']))
{
        $publish = $facebook->api('/me/feed', 'post',
        array('access_token' => $_SESSION['token'],'message'=>$_POST['status'] .'   via PHPGang.com Demo',
        'from' => $config['App_ID']
        ));
        $message = 'Status updated.<br>';
        $extra = "<a href='index.php?logout=1&tocken=".$params['access_token']."'>Logout</a><br>";
    $content = '
    <style>
    #status
    {
        width: 357px;
        height: 28px;
        font-size: 15px;
    }
    </style>
    '.$message.'
    <form action="index.php" method="post">
    <input type="text" name="status" id="status" placeholder="Write a comment...." /><input type="submit" value="Post On My Wall!" style="padding: 5px;" />
    <form>';
}
elseif(isset($_GET['fbTrue']))
{
    if($_GET['fbTrue'] == 'true11' )
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
    }
    
    $response = file_get_contents($token_url);
    $params = null;
    parse_str($response, $params);

    $graph_url = "https://graph.facebook.com/me?access_token=" 
        . $params['access_token'];
        $_SESSION['token'] = $params['access_token'];
    $user = json_decode(file_get_contents($graph_url));
    if($_GET['fbTrue'] == 'true11' )
    {
        $publish = $facebook->api('/me/feed', 'post',
        array('access_token' => $params['access_token'],'message'=>'This Messsage published by PHPGang.com Demo.',
        'from' => $config['App_ID']
        ));
        $message = 'Default status updated.<br>';
    }
    $extra = "<a href='index.php?logout=1&tocken=".$params['access_token']."'>Logout</a><br>";
    $content = '
    <style>
    #status
    {
        width: 357px;
        height: 28px;
        font-size: 15px;
    }
    </style>
    '.$message.'
    <form action="index.php" method="post">
    <input type="text" name="status" id="status" placeholder="Write a comment...." /><input type="submit" value="Post On My Wall!" style="padding: 5px;" />
    <form>';    
}
else
{
    $content = 'Connect only &nbsp;&nbsp;<a href="https://www.facebook.com/dialog/oauth?client_id='.$config['App_ID'].'&redirect_uri='.$config['callback_url'].'&scope=email,user_likes,publish_stream"><img src="./images/login-button.png" alt="Sign in with Facebook"/></a>';
    
	$content .= '<br><br>Connect and post Status &nbsp;&nbsp;<a href="https://www.facebook.com/dialog/oauth?client_id='.$config['App_ID'].'&redirect_uri='.$config['callback_url'].'11&scope=email,user_likes,publish_stream"><img src="./images/login-button.png" alt="Sign in with Facebook"/></a>';
}


$title = "How to post status on Facebook with Graph API"; 
$heading = "How to post status on Facebook with Graph API example.";
//include('html.inc');
?>

<h2><?php echo $heading;?></h2>
  
<p>
	<?php if(!isset($pre)) {?>
  <pre>
	<?php print_r($content); ?>
  </pre>
  <?php }else{ ?>
   <?php print_r($content); ?>
  <?php } ?>
</p>