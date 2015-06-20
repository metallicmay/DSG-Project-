<?php

include_once("database.php");
$uid = $_GET['uid'];
$sym = $_GET['sym'];
$qty = $_GET['qty'];
$price = $_GET['price'];
$type = $_GET['type'];
$query = "INSERT INTO transaction (uid,sym,qty,price,type,date_time)
			VALUES ('$uid','$sym','$qty','$price','$type',NOW())";
$result = mysql_query($query);
if(!$result)
         echo "Failed" .mysql_error();
 
?>
