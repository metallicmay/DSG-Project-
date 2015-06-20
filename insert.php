<?php
 include_once("database.php");
$uid = $_GET['uid'];
$sym = $_GET['sym'];
$qty = $_GET['qty'];
$price = $_GET['price'];

$query = "INSERT INTO stocks (uid,sym,qty,price)
			VALUES ('$uid','$sym','$qty','$price')
			ON DUPLICATE KEY 
			 UPDATE QTY = QTY + $qty, PRICE = $price";
$result = mysql_query($query);
if(!$result)
         echo "Failed" .mysql_error();
 ?>
