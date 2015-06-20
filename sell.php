<?php
include_once("database.php");
$uid = $_GET['uid'];
$sym = $_GET['sym'];
$qty = $_GET['qty'];
$price = $_GET['price'];
$ans = "";
$q = mysql_query("SELECT QTY FROM STOCKS WHERE SYM = '$sym'  AND UID = $uid ") or die(mysql_error());
$res=mysql_fetch_array($q);
if($res) {
$val = $res['QTY'];
  if($qty<=$val)
  {
	$query = "UPDATE stocks SET QTY = QTY - $qty
			   WHERE SYM = '$sym'  AND UID = $uid ";
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
