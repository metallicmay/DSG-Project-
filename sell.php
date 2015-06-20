<?php
include_once("database.php");
$sym = $_GET['sym'];
$qty = $_GET['qty'];
$price = $_GET['price'];
$ans = "";
$q = mysql_query("SELECT QTY FROM STOCKS WHERE SYM = '$sym'") or die(mysql_error());
$res=mysql_fetch_array($q);
if($res) {
$val = $res['QTY'];
  if($qty<=$val)
  {
	$query = "UPDATE stocks SET QTY = QTY - $qty
			   WHERE SYM = '$sym' ";
	$result = mysql_query($query);
	$ans = "Yes";
  }
  else {
  $ans = "NoQ";
  }
}
else {
 $ans = "No";
 }

echo($ans);


?>
