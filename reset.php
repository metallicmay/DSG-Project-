<?php
		include_once("database.php");
		$query= mysql_query("SELECT * FROM users") or die(mysql_error());
        $result=mysql_fetch_array($query);
		$sql="UPDATE users 
		      SET LIQUIDCASH= DEFAULT(LIQUIDCASH)";
		$quer=mysql_query($sql);
        if(!$quer)
         echo "Failed" .mysql_error();
?>
