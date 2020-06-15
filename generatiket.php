<?php
	include("main.php"); 
	$rsUsuario=mysql_db_query($base,"SELECT * FROM usuarios");
while($Usuario=mysql_fetch_array($rsUsuario)){
	$array_usuario[$Usuario['cve']]=$Usuario['usuario'];
	$array_nomusuario[$Usuario['cve']]=$Usuario['nombre'];
}

$rsUsuario=mysql_db_query($base,"SELECT * FROM preciotickets");
while($Usuario=mysql_fetch_array($rsUsuario)){
	$array_precios[$Usuario['cve']]=$Usuario['nombre'];
}

	$_POST['reg']=$_GET['reg'];
	$res=mysql_db_query($base,"SELECT * FROM plaza");
	$row=mysql_fetch_array($res);
	$nomplaza=$row['nombre'];
	$cveplaza=$row['clave'];

	$res=mysql_db_query($base,"SELECT * FROM preciotickets WHERE cve='".$_POST['reg']."'");
	$row=mysql_fetch_array($res);
	
	$sql="INSERT tickets SET taq='".$cveplaza."',cveprecio='".$_POST['reg']."',fecha='".fechaLocal()."',hora='".horaLocal()."',monto='".$row['precio']."',
	usuario='".$array_usuario[$_SESSION['CveUsuario']]."'";
	mysql_db_query($base,$sql) or die(mysql_error());
	
	$folio=mysql_insert_id();
	
	//system("copy BRUAS.TMB lpt1");
	//$fp =fopen( "LPT1:", "w+"); // for parallel output
	//$fp = fopen ("a".$folio.".txt","w+");
	/*fwrite($fp,chr(29)."h".chr(80));
	fwrite($fp,chr(29)."H".chr(2));
	fwrite($fp,chr(29)."f".chr(0));
	fwrite($fp,chr(29)."k".chr(2));
	fwrite($fp,"099000000001".chr(0));*/
	$fechaL=fechaLocal();
	$horaL=horaLocal();
	/*fwrite($fp,chr(27).'!'.chr(6)."    ALIANZA AUTOTRANSPORTISTA DE ZUMPANGO\n");
	fwrite($fp,chr(27).'!'.chr(6)."           R.F.C.:AAZ6803283V5\n\n");
	fwrite($fp,chr(27).'!'.chr(10)."Costo: ".$row['precio']."\n");
	fwrite($fp,chr(27).'!'.chr(10)."Taquilla: ".$nomplaza."\n");
	fwrite($fp,chr(27).'!'.chr(10)."Usuario: ".$array_usuario[$_SESSION['CveUsuario']]."\n");
	fwrite($fp,chr(29)."h".chr(80).chr(29)."H".chr(2).chr(29)."k".chr(2)."1".sprintf("%02s",(intval($row['cve']))).sprintf("%02s",$cveplaza).sprintf("%07s",$folio).chr(0));
	//fwrite($fp,sprintf("%'04s",(intval($_GET['precio']*10))).$_GET['folio']);
	fwrite($fp,chr(27).'!'.chr(10)." \n");
	fwrite($fp,chr(27).'!'.chr(40)."Folio: ".$folio."\n");
	fwrite($fp,chr(27).'!'.chr(40)."".$row['nombre']."\n\n");
	fwrite($fp,chr(27).'!'.chr(10).$fechaL." ".$horaL."            OPERADOR\n");
	fwrite($fp,chr(29).chr(86).chr(66).chr(0));
	//fclose($fp);
	//system("copy a".$folio.".txt lpt1: >null:");
	//system("copy BRUAS.TMB lpt1");
	//$fp1 = fopen ("b".$folio.".txt","w+");
	fwrite($fp,chr(27).'!'.chr(6)."    ALIANZA AUTOTRANSPORTISTA DE ZUMPANGO\n");
	fwrite($fp,chr(27).'!'.chr(6)."           R.F.C.:AAZ6803283V5\n\n");
	fwrite($fp,chr(27).'!'.chr(10)."PASAJERO   $ ".$row['precio']." (".$row['nombre'].")\n");
	fwrite($fp,chr(27).'!'.chr(10)."Usuario: ".$array_usuario[$_SESSION['CveUsuario']]."\n");
	fwrite($fp,chr(27).'!'.chr(10)."Taquilla: ".$nomplaza."\n");
	fwrite($fp,chr(27).'!'.chr(10)."Este boleto ampara el seguro de viajero en la fecha y hora se".chr(164)."alada\n\n");
	fwrite($fp,chr(27).'!'.chr(10).$fechaL." ".$horaL."  FOLIO: ".$folio."\n");
	fwrite($fp,chr(29).chr(86).chr(66).chr(0));
	fclose($fp);*/
	//system("copy a".$folio.".txt lpt1: >null:");
	//$impresion='<iframe src="http://localhost/impticketaaz.php?nomprecio='.$row['nombre'].'&cveprecio='.$row['cve'].'&precio='.$row['precio'].'&fechadia='.fechaLocal().'&horadia='.horaLocal().'&folio='.$folio.'&usuario='.$array_usuario[$_SESSION['CveUsuario']].'" width=200 height=200></iframe>';
	$strtiket="";
	$strtiket.="zxcx27+".ord('!')."+6xcxz    ALIANZA AUTOTRANSPORTISTA DE ZUMPANGOzxcx10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+6xcxz           R.F.C.:AAZ6803283V5zxcx10+13+10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+10xcxzCosto: ".$row['precio']."zxcx10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+10xcxzTaquilla: ".$nomplaza."zxcx10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+10xcxzUsuario: ".$array_usuario[$_SESSION['CveUsuario']]."zxcx10+13xcxz";
	$strtiket.="zxcx29+".ord('h')."+80+29+".ord('H')."+2+29+".ord('k')."+2+".ord('1')."xcxz".sprintf("%02s",(intval($row['cve']))).sprintf("%02s",$cveplaza).sprintf("%07s",$folio)."zxcx0xcxz";
	$strtiket.="zxcx27+".ord('!')."+10+10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+40xcxzFolio: ".$folio."zxcx10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+40xcxz".$row['nombre']."zxcx10+13+10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+10xcxz".$fechaL." ".$horaL."            OPERADORzxcx10+13xcxz";
	$strtiket.="zxcx29+86+66+0xcxz";
	$strtiket.="zxcx27+".ord('!')."+6xcxz    ALIANZA AUTOTRANSPORTISTA DE ZUMPANGOzxcx10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+6xcxz           R.F.C.:AAZ6803283V5zxcx10+13+10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+10xcxzPASAJERO   $ ".$row['precio']." (".$row['nombre'].")zxcx10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+10xcxzUsuario: ".$array_usuario[$_SESSION['CveUsuario']]."zxcx10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+10xcxzTaquilla: ".$nomplaza."zxcx10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+10xcxzEste boleto ampara el seguro de viajero en la fecha y hora sezxcx164xcxzaladazxcx10+13+10+13xcxz";
	$strtiket.="zxcx27+".ord('!')."+10xcxz".$fechaL." ".$horaL."  FOLIO: ".$folio."zxcx10+13xcxz";
	$strtiket.="zxcx29+86+66+0xcxz";
	echo '<html>
<body bgcolor="#ddd" onload="window.parent.habilitabotones();">

</body>
<applet code="writeFile.class" archive="writeFile.jar"  width="100%" height="100%">
<param name="imprimir" value="'.$strtiket.'"><param name="Puerto" value="LPT1"></applet>
</script>
</html>
';
?>
