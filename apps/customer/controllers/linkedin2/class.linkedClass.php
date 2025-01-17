<?php
class linkedClass {
    private $config                         =   array();

    public function __construct()
    {
        include_once "config.php";
        global $config;
        
        $this->config     =  $config;
		
		
		$this->config['linkedin_access'] = '814ngq494psr20';
		$this->config['linkedin_secret'] = 'VkCfq1M3IZOh7MFn';
       
    }

    public function linkedinGetUserInfo( $requestToken='', $oauthVerifier='', $accessToken=''){
	
	    include_once 'linkedinoAuth.php';

        $linkedin = new LinkedIn($this->config['linkedin_access'], $this->config['linkedin_secret']);
        $linkedin->request_token    =   unserialize($requestToken); //as data is passed here serialized form
        $linkedin->oauth_verifier   =   $oauthVerifier;
        $linkedin->access_token     =   unserialize($accessToken);

        try{
            $xml_response = $linkedin->getProfile("~:(id,first-name,last-name,interests,publications,patents,languages,skills,date-of-birth,email-address,phone-numbers,im-accounts,main-address,twitter-accounts,headline,picture-url,public-profile-url)");
        }
        catch (Exception $o){
            print_r($o);
        }
        return $xml_response;
    }
    public function linkedInStatus( $status,$requestToken='', $oauthVerifier='', $accessToken=''){
        include_once 'linkedinoAuth.php';

        $linkedin = new LinkedIn($this->config['linkedin_access'], $this->config['linkedin_secret']);
        $linkedin->request_token    =   unserialize($requestToken); //as data is passed here serialized form
        $linkedin->oauth_verifier   =   $oauthVerifier;
        $linkedin->access_token     =   unserialize($accessToken);

        try{
            $xml_response = $linkedin->setStatus($status);
        }
        catch (Exception $o){
            print_r($o);
        }
        return $xml_response;
    }
	
	
	
	
	/*-------custom-------*/
	public function linkedInGetTimeline($requestToken='', $oauthVerifier='', $accessToken='',$companyid=''){
        include_once 'linkedinoAuth.php';

        $linkedin = new LinkedIn($this->config['linkedin_access'], $this->config['linkedin_secret']);
        $linkedin->request_token    =   unserialize($requestToken); //as data is passed here serialized form
        $linkedin->oauth_verifier   =   $oauthVerifier;
        $linkedin->access_token     =   unserialize($accessToken);

        try{
            $xml_response = $linkedin->getTimeline($companyid);
        }
        catch (Exception $o){
            print_r($o);
        }
        return $xml_response;
    }
	
	
	
	
	public function linkedInGetReport($requestToken='', $oauthVerifier='', $accessToken='',$postid=''){
        include_once 'linkedinoAuth.php';

        $linkedin = new LinkedIn($this->config['linkedin_access'], $this->config['linkedin_secret']);
        $linkedin->request_token    =   unserialize($requestToken); //as data is passed here serialized form
        $linkedin->oauth_verifier   =   $oauthVerifier;
        $linkedin->access_token     =   unserialize($accessToken);

        try{
            $xml_response = $linkedin->getReport($postid);
        }
        catch (Exception $o){
            print_r($o);
        }
        return $xml_response;
    }
	
	
	
	public function linkedInGetCompany($requestToken='', $oauthVerifier='', $accessToken=''){
        include_once 'linkedinoAuth.php';

        $linkedin = new LinkedIn($this->config['linkedin_access'], $this->config['linkedin_secret']);
        $linkedin->request_token    =   unserialize($requestToken); //as data is passed here serialized form
        $linkedin->oauth_verifier   =   $oauthVerifier;
        $linkedin->access_token     =   unserialize($accessToken);

        try{
            $xml_response = $linkedin->getCompanies();
        }
        catch (Exception $o){
            print_r($o);
        }
        return $xml_response;
    }
	
	 public function linkedInshareStatus( $xml,$requestToken='', $oauthVerifier='', $accessToken=''){

        include_once 'linkedinoAuth.php';

        $linkedin = new LinkedIn($this->config['linkedin_access'], $this->config['linkedin_secret']);
        $linkedin->request_token    =   unserialize($requestToken); //as data is passed here serialized form
        $linkedin->oauth_verifier   =   $oauthVerifier;
        $linkedin->access_token     =   unserialize($accessToken);

        try{
            $xml_response = $linkedin->postStatus($xml);
        }
        catch (Exception $o){
            print_r($o);
        }
        return $xml_response;
    }
	
}
?>
