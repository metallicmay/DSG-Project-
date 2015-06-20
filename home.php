<?php
 include_once("database.php") 
 ?> 
<html>
<title> HOMEPAGE </title>
<head>
<link rel="stylesheet" type="text/css" href="style.css">

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
	 $check=false;
	 
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
				
		//select user from database
		$query= mysql_query("SELECT * FROM users WHERE NAME ='".$name."'") or die(mysql_error());  //user id is unique
        $result=mysql_fetch_array($query);
		//fetch liquid cash value and user id from user table
		$liqcash=$result['LIQUIDCASH'];
		$uid = $result['UID'];
		$liqnew="";
		
		echo "Hi $name";
		echo "<div align=right><a href='".$logout."'>Logout</a></div>";
		echo"<div align=center><a href=home.php> Homepage &nbsp;</a> <a href=user.php> Userpage &nbsp;</a> <a href=lb.php> &nbsp;  Leaderboard  </a></div>";
		echo "<div align=center>Liquid Cash: $liqcash <br></div>";
		echo "<div align=center><br>HOMEPAGE</div>";
	    
		//calculate new values after buy/sell button is clicked and update in database
		if (isset($_GET['liq'])) 
		 {
		 $liqnew = $_GET['liq'];
		 $check=true;
		  }
		 if($check)
		{
		
		$sql="UPDATE users 
		      SET LIQUIDCASH=$liqnew
		      WHERE name='".$name."'";
		$quer=mysql_query($sql);
        if(!$quer)
         echo "Failed" .mysql_error();
		 $check = false;
		 }
		
		}
		else{
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

<script type="text/javascript">
function buy(sym,price)
        { 
		 var qty; 
		 do
		 {
		 qty = prompt("Enter quantity: ", "100");
		   
		 } while(isNaN(qty)||qty<=0);
         	var liq = '<?php echo $liqcash?>';
		 var uid = '<?php echo $uid?>';
		 var fin;
		 var tprice;
		 tprice = price*qty;
		 if(tprice>liq)
		 { alert('Cannot buy');
		 }
		 else
		 {
		 fin=liq-tprice;
		 window.location = "?liq=" + fin;
		 var xmlhttp;
		 var xhr;
		 var type = "Buy";
		if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  xhr=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  xhr=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			
			}
		  }
		  xhr.onreadystatechange=function()
		  {
		  if (xhr.readyState==4 && xhr.status==200)
			{
			
			}
		  }
	  xmlhttp.open("GET","insert.php?uid=" + uid + "&sym=" + sym + "&qty=" + qty + "&price=" + price,true);
          xmlhttp.send();
          xhr.open("GET","logs.php?uid=" + uid + "&sym=" + sym + "&qty=" + qty + "&price=" + price + "&type=" + type,true);
          xhr.send();
		 }
        }
   
	function sell(sym,price)
         {
		 var qty; 
		 do
		 {
		 qty = prompt("Enter quantity: ", "100");
		 } while(isNaN(qty)||qty<=0);
         var liq = '<?php echo $liqcash?>';
		 var uid = '<?php echo $uid?>';
		 var fin;		 
		 var tprice;
		 tprice = price*qty;
		 var xmlhttp;
		 if (window.XMLHttpRequest)
		  {// code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		  }
		else
		  {// code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		  }
		  xmlhttp.onreadystatechange=function()
		  {
		  if (xmlhttp.readyState==4 && xmlhttp.status==200)
			{
			
			}
		  }
		  xmlhttp.open("GET","sell.php?uid=" + uid + "&sym=" + sym + "&qty=" + qty + "&price=" + price,false);
          	  xmlhttp.send();
		  if(xmlhttp.responseText.trim()=="No") {
		   alert('User does not own this stock');
		   } 
		   else if(xmlhttp.responseText.trim()=="NoQ") {
		  alert('Quantity too large');
		  }
		  else if(xmlhttp.responseText.trim()=="Yes") {
		  fin=liq+tprice;
		  window.location = "?liq=" + fin;
		  var type = "Sell";
		  var xhr;
			 if (window.XMLHttpRequest)
			  {// code for IE7+, Firefox, Chrome, Opera, Safari
			  xhr=new XMLHttpRequest();
			  }
			else
			  {// code for IE6, IE5
			  xhr=new ActiveXObject("Microsoft.XMLHTTP");
			  }
			  xhr.onreadystatechange=function()
			  {
			  if (xhr.readyState==4 && xhr.status==200)
				{
				
				}
			  }
		  xhr.open("GET","logs.php?uid=" + uid + "&sym=" + sym + "&qty=" + qty + "&price=" + price + "&type=" + type,true);
          	  xhr.send();
		  }
         }    		 
    function reset()
		 {
		 var xmlhttp;
				if (window.XMLHttpRequest)
				  {// code for IE7+, Firefox, Chrome, Opera, Safari
				  xmlhttp=new XMLHttpRequest();
				  }
					else
				  {// code for IE6, IE5
				  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
				  }
				xmlhttp.onreadystatechange=function()
				  {
				  if (xmlhttp.readyState==4 && xmlhttp.status==200)
					{
					document.getElementById("res").innerHTML=xmlhttp.responseText;
					}
				  }
				xmlhttp.open("POST","reset.php",true);
				xmlhttp.send();
		 }
</script>
	</head>
  	<body>
	   <form method="post">
	    <table>            
            <tr>
              <th>Symbol</th>
              <th>Price</th>
			  <th>High</th>
			  <th>Low</th>
			  <th>Buy</th>
			  <th>Sell</th>
            </tr>
        <?php foreach( $objYahooStock->getQuotes() as $code => $stock)
			{
		?>			
			<tr>
            <td> <?php echo $stock[0]; ?> </td>
			<td> <?php echo $stock[1]; ?> </td>
			<td> <?php echo $stock[2]; ?> </td>
			<td> <?php echo $stock[3]; ?> </td> 
			<td><input type="button" value="Buy" onclick="buy('<?php echo str_replace("\"","", $stock[0]); ?>','<?php echo $stock[1]; ?>')"></td>
			<td><input type="button" value="Sell" onclick="sell('<?php echo str_replace("\"","", $stock[0]); ?>','<?php echo $stock[1];?>')"> <br /> </td>
          </tr>         
        <?php
			}
		?>
        </table>
	   </form>
	   <button id = "res" onclick = "reset()";> Reset </button>
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
	   	
