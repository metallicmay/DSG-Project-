<?php
 include_once("database.php") 
 ?> 
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
</head>
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
	 $check=true;
	 //3.Initialize application, create helper object and get fb sess
	 FacebookSession::setDefaultApplication($app_id,$app_secret);
	 $helper = new FacebookRedirectLoginHelper($redirect_url);
	 $sess = $helper->getSessionFromRedirect();
	 //check if facebook session exists
	if(isset($_SESSION['fb_token'])){
	 	$sess = new FacebookSession($_SESSION['fb_token']);
	}
	//logout
	$logout = 'http://localhost:8080/dsg/index.php?logout=true';
	
	//4. if fb sess exists echo name 
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
         if($result)
         { 	 
           if($id==$result['UID']);
	        {
			$check=false;
            }   
		 }
		$liqcash=$result['LIQUIDCASH'];
		echo "Hi $name";
		echo "<div align=right><a href='".$logout."'>Logout</a></div>";
		echo"<div align=center><a href=home.php> Homepage &nbsp;</a> <a href=user.php> Userpage &nbsp;</a> <a href=lb.php> &nbsp;  Leaderboard  </a></div>";
		echo "<div align=center>Liquid Cash: $liqcash <br></div>";
		if($check)
		{
		$sql="INSERT INTO users (NAME,UID)
            VALUES ('$name','$id')";
		$query=mysql_query($sql);
        if(!$query)
         echo "Failed" .mysql_error();
		 }

	}else{
		//else echo login
		echo '<a href='.$helper->getLoginUrl().'>Login with facebook</a>';
	}
	
	include_once('class.yahoostock.php');
		 
		$objYahooStock = new YahooStock; //creating object
		$objYahooStock->addFormat("sl1hg"); //adding format/parameters to be fetched
		
        //adding company stock code to be fetched
		$objYahooStock->addStock("msft"); 
		$objYahooStock->addStock("yhoo");
		$objYahooStock->addStock("goog"); 
		$objYahooStock->addStock("aapl"); 
	?>
 	
 <body>
 <div id="ticker">
        <marquee direction="up">
          <table>
            <thead>
			<tr>
              <th>Symbol</th>
              <th>Price</th>
            </tr>
            </thead>
            <?php foreach( $objYahooStock->getQuotes() as $code => $stock)
			{
		    ?>	
			<tbody>          
			<tr>
            <td><?php echo $stock[0]; ?></td>
            <td><?php echo $stock[1]; ?></td>
           </tr>
		   </tbody>
		   <?php
			}
		   ?>
          </table>
         </marquee>
       </div>
 </body>
  </html>
