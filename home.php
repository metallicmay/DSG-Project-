<?php
 include_once("database.php") 
 ?> 
<html>
<title> HOMEPAGE </title>
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
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
</style>
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
		$query= mysql_query("SELECT * FROM users WHERE NAME ='".$name."'") or die(mysql_error());
        $result=mysql_fetch_array($query);
		//fetch liquid cash value
		$liqcash=$result['LIQUIDCASH'];
		$ex_stock=$result['STOCKS'];
		$liqnew="";
		$tot="";
		$stocks = "";
		echo "Hi $name";
		echo "<div align=right><a href='".$logout."'>Logout</a></div>";
		echo"<div align=center><a href=home.php> Homepage &nbsp;</a> <a href=user.php> Userpage &nbsp;</a> <a href=lb.php> &nbsp;  Leaderboard  </a></div>";
		echo "<div align=center>Liquid Cash: $liqcash <br></div>";
		echo "<div align=center><br>HOMEPAGE</div>";
	     //calculate new values after buy/sell button is clicked and update in database
		if (isset($_GET['prices'])) 
		 {
		 $prices = explode(",", $_GET["prices"]);
		 $liqnew = $prices[0];
		 $stocks = $prices[1];
		 $tot=$liqnew+$stocks+$ex_stock;
		 $check=true;
		 }
		if($check)
		{
		
		$sql="UPDATE users 
		      SET LIQUIDCASH=$liqnew, STOCKS = STOCKS + $stocks, TOTAL=$tot
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
        function buy(price)
        { 
		 var qty; 
		 do
		 {
		 qty = prompt("Enter quantity: ", "100");
		 } while(isNaN(qty));
         var liq = "<?php echo $liqcash;?>";
		 var fin;
		 var tprice;
		 tprice = price*qty;
		 fin=liq-tprice;
		 window.location = "?prices=" + fin + "," + tprice;
        }
        function sell(price)
         {
		 var qty; 
		 do
		 {
		 qty = prompt("Enter quantity: ", "100");
		 } while(isNaN(qty));
         var liq = "<?php echo $liqcash;?>";
		 var fin;
		 var tprice;
		 tprice = (-price)*qty;
		 fin=liq-tprice;
		 window.location = "?prices=" + fin + "," + tprice;
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
			<td><input type="button" value="Buy" onclick="buy('<?php echo $stock[1]?>')"></td>
			<td><input type="button" value="Sell" onclick="sell('<?php echo $stock[1]?>')"> <br /> </td>
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
	   	
