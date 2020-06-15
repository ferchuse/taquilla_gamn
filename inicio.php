<?php
	include("main.php");
	
	if($_POST['cmd']==99)
	{
		mysql_db_query($base,"REPAIR TABLE boletos") or die(mysql_error());
		mysql_db_query($base,"REPAIR TABLE costo_boletos") or die(mysql_error());
		mysql_db_query($base,"REPAIR TABLE guia") or die(mysql_error());
		mysql_db_query($base,"REPAIR TABLE usuarios") or die(mysql_error());
		mysql_db_query($base,"REPAIR TABLE unidades") or die(mysql_error());
		mysql_db_query($base,"REPAIR TABLE taquilla") or die(mysql_error());
	}

	top($_SESSION);
	menuppal2($_SESSION);
	bottom();
?>