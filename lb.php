<?php
 include_once("database.php") 
 ?> 
<html>
<title>LEADERBOARD</title>
<style type='text/css'>
#logout
{ position:absolute;
  right:50px;  
  top:0px;
  }
  
#ticker
{ position:absolute;
  right:50px;  
  top:50px;
  outline:1px solid black;
}
</style>
<div id="ticker">
        <marquee direction="up">
          <table>
            <thead>
            <tr>
              <td>Symbol</td>
              <td>Price</td>
            </tr>
            </thead>
            <tbody>          <tr>
            <td>AAPL</td>
            <td>111.81</td>
          </tr>          <tr>
            <td>YHOO</td>
            <td>50.90</td>
          </tr>          <tr>
            <td>MSFT</td>
            <td>48.85</td>
          </tr>          <tr>
            <td>TWTR</td>
            <td>43.515</td>
          </tr>          <tr>
            <td>CSCO</td>
            <td>24.91</td>
          </tr>          <tr>
            <td>F</td>
            <td>14.71</td>
          </tr>          <tr>
            <td>ZNGA</td>
            <td>2.81</td>
          </tr>          <tr>
            <td>WMT</td>
            <td>80.94</td>
          </tr>            </tbody>
          </table>
         </marquee>
       </div>
</html>
<?php

/* INCLUSION OF LIBRARY FILEs*/
	require_once( 'lib/Facebook/FacebookSession.php');
	require_once( 'lib/Facebook/FacebookRequest.php' );
	require_once( 'lib/Facebook/FacebookResponse.php' );
	require_once( 'lib/Facebook/FacebookSDKException.php' );
	require_once( 'lib/Facebook/FacebookRequestException.php' );
	require_once( 'lib/Facebook/FacebookRedirectLoginHelper.php');
	require_once( 'lib/Facebook/FacebookAuthorizationException.php' );
	require_once( 'lib/Facebook/GraphObject.php' );
	require_once( 'lib/Facebook/GraphUser.php' );
	require_once( 'lib/Facebook/GraphSessionInfo.php' );
	require_once( 'lib/Facebook/Entities/AccessToken.php');
	require_once( 'lib/Facebook/HttpClients/FacebookCurl.php' );
	require_once( 'lib/Facebook/HttpClients/FacebookHttpable.php');
	require_once( 'lib/Facebook/HttpClients/FacebookCurlHttpClient.php');
/* USE NAMESPACES */
	
	use Facebook\FacebookSession;
	use Facebook\FacebookRedirectLoginHelper;
	use Facebook\FacebookRequest;
	use Facebook\FacebookResponse;
	use Facebook\FacebookSDKException;
	use Facebook\FacebookRequestException;
	use Facebook\FacebookAuthorizationException;
	use Facebook\GraphObject;
	use Facebook\GraphUser;
	use Facebook\GraphSessionInfo;
	use Facebook\FacebookHttpable;
	use Facebook\FacebookCurlHttpClient;
	use Facebook\FacebookCurl;
/*PROCESS*/
	
	//1.Start Session
	 session_start();
	   //check if users wants to logout
	 if(isset($_REQUEST['logout'])){
	 	unset($_SESSION['fb_token']);
	 }
	//2.Use app id,secret and redirect url
	 $app_id = '754225167991120';
	 $app_secret = '1c86383191c399a92d6ff0d1be14a89d';
	 $redirect_url='http://localhost:8080/dsg/';
	 
	 //3.Initialize application, create helper object and get fb sess
	 FacebookSession::setDefaultApplication($app_id,$app_secret);
	 $helper = new FacebookRedirectLoginHelper($redirect_url);
	 $sess = $helper->getSessionFromRedirect();
	 if(isset($_SESSION['fb_token'])){
	 	$sess = new FacebookSession($_SESSION['fb_token']);
	}
	//logout
	$logout = 'http://localhost:8080/dsg/index.php?logout=true';
	if(isset($sess)){
		//store the token in the php session
	 		$_SESSION['fb_token']=$sess->getToken();
	 		//create request object,execute and capture response
			 $sess = new FacebookSession($sess->getToken());
	         // create a session using saved token or the new one we generated at login
		$request = new FacebookRequest($sess, 'GET', '/me');
		// from response get graph object
		$response = $request->execute();
		$graph = $response->getGraphObject(GraphUser::className());
		// use graph object methods to get user details
		$name= $graph->getName();
		$id = $graph->getId();
		$query= mysql_query("SELECT * FROM users WHERE NAME ='".$name."'") or die(mysql_error());
        $result=mysql_fetch_array($query);
		$liqcash=$result['LIQUIDCASH'];
		echo "Hi $name";
		echo "<div align=right><a href='".$logout."'>Logout</a></div>";
		echo"<div align=center><a href=home.php> Homepage &nbsp;</a> <a href=user.php> Userpage &nbsp;</a> <a href=lb.php> &nbsp;  Leaderboard  </a></div>";
		echo "<div align=center>Liquid Cash: $liqcash <br></div>";
		echo "<div align=center><br>LEADERBOARD</div>";
		}
		else{
		//else echo login
		echo '<a href='.$helper->getLoginUrl().'>Login with facebook</a>';
	}
	?>
