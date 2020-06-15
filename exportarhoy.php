<?php
include ("main.php"); 
$array_estatuspre=array("Activo","Inactivo");
$array_tipopre=array("Normal","Promocion");
/*** ACTUALIZAR REGISTRO  **************************************************/
	$fecha=date('Y-m-d');
//$fecha='2010-04-28';
	$cadena="";
	$select= " SELECT * FROM tickets WHERE fecha='$fecha'";
	$rspuesto = mysql_db_query($base,$select);
	while($row=mysql_fetch_array($rspuesto)){
		$cadena.="INSERT tickets2 SET taq='".$row['taq']."',cve='".$row['cve']."',cveprecio='".$row['cveprecio']."',fecha='".$row['fecha']."',hora='".$row['hora']."',monto='".$row['monto']."',usuario='".$row['usuario']."'".chr(13).chr(10);
	}
	
	header("Content-type: TXT");
	//header("Content-Length: $len");
	header("Content-Disposition: attachment; filename=venta_$fecha.txt");
	print $cadena;
	exit();
		
?>