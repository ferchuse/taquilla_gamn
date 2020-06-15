<?php
	set_time_limit(0);
	require_once("nusoap/nusoap.php");
	if (!$MySQL=@mysql_connect('localhost', 'root', 'loscocos')) {
		$t=time();
		while (time()<$t+5) {}
		if (!$MySQL=@mysql_connect('localhost', 'root', 'loscocos')) {
			$t=time();
			while (time()<$t+10) {}
			if (!$MySQL=@mysql_connect('localhost', 'root', 'loscocos')) {
			echo '<br><br><br><h3 align=center">Hay problemas de comunicaci&oacute;n con la Base de datos.</h3>';
			echo '<h4>Por favor intente mas tarde.-</h4>';
			exit();
			}
		}
	}
	$base="gamn_local";
	$dias=$_POST['dias'];
	$arango=array(1=>'1 Dia',2=>'2 Dias',3=>'3 Dias',4=>'4 Dias',5=>'5 Dias',10=>'10 Dias',15=>'15 Dias',30=>'30 Dias');
	if($dias==0)
		$dias=1;

	$segundos=$_POST['segundos'];
	$asincroniza=array(1=>'1 Minuto',5=>'5 Minutos',10=>'10 Minutos',30=>'30 Minutos',60=>'1 Hora',180=>'3 Horas',300=>'5 Horas');
	if($segundos==0)
		$segundos=1;
	echo '<html><head><title>Sincronizar Boletos</title></head>
	<script language="javascript">
		function cargar(){
			setTimeout("sincronizar()",'.($segundos*60000).');	
		}
		function sincronizar(){
			//alert("Se sincronizaran los Boletos");
			forma.submit();
		}
	</script>
	<body leftmargin=0 marginwidth=0 topmargin=0 marginheight=0 onload="cargar();">';
	//Consumir el WS
	$oSoapClient = new nusoap_client("http://gamn.checame.net/wsSincronizar.php?wsdl", true);			
	$err = $oSoapClient->getError();
	if($err!="")
		echo "error1:".$err;
	else{
		//Tomar los Boletos de los ultimos 5 dias
		$res = mysql_db_query($base,"SELECT cve FROM taquilla");
		$row=mysql_fetch_array($res);
		$cvetaq=$row[0];
		$fini=date('Y-m-d H:i:s',time()-($dias*24*60*60));
		$ffin=date('Y-m-d H:i:s');
		$tickets="";
		$totreg=0;
		$query= " SELECT * FROM boletos WHERE fecha BETWEEN '$fini' And '$ffin'";
		$rs = mysql_db_query($base,$query) or die("Error en query $query ".mysql_error());
		while($row=mysql_fetch_array($rs)){
			$taquilla=$row['taq'];
			$totreg++;
			$tickets.="INSERT into boletos SET taquilla='{$cvetaq}',folio='{$row['cve']}',guia='{$row['guia']}',fecha='{$row['fecha']}',hora='{$row['hora']}',monto='{$row['monto']}',unidad='".$row['unidad']."',usuario='".$row['usuario']."',no_eco='".$row['no_eco']."',costo='".$row['costo']."',estatus='0';\n";
		}
		$query= " SELECT * FROM guia WHERE fecha_fin between '$fini' And '$ffin'";
		$rs = mysql_db_query($base,$query) or die("Error en query $query ".mysql_error());
		while($row=mysql_fetch_array($rs)){
			$taquilla=$row['taq'];
			$tickets.="INSERT into guia SET taquilla='{$cvetaq}',folio='{$row['cve']}',unidad='{$row['unidad']}',fecha='{$row['fecha']}',hora='{$row['hora']}',no_eco='{$row['no_eco']}',fecha_fin='".$row['fecha_fin']."',usuario='".$row['usuario']."',hora_fin='".$row['hora_fin']."',usuario_fin='".$row['usuario_fin']."';\n";
		}
		$tickets=base64_encode($tickets);
		//Enviar la informacion
		$parametros=array ('taquilla'=>$taquilla,'fechainicial'=>$fini,'fechafinal'=>$ffin,'tickets'=>$tickets);
		$respuesta = $oSoapClient->call("UpdateTickets", $parametros);
		$err = $oSoapClient->getError();
		if (!$err){
			if($respuesta['resultado'])
				echo "OK Boletos($totreg Registros Actualizados)<br/>";
			else
				echo "res".$respuesta['mensaje'];
		}
		else
			echo $oSoapClient->response;
	}
	echo '<form name="forma" enctype="multipart/form-data" method="POST" action="sincronizarboletos.php">';
	echo 'Tomar informacion de<select name="dias">';
	foreach($arango as $key=>$val){
		echo '<option value='.$key;
		if($key==$dias)
			echo ' selected';
		echo '>'.$val,'</option>';
	}
	echo '</select> a la Fecha actual<br/>';
	echo 'Intervalo de Sincronizacion:<select name="segundos">';
	foreach($asincroniza as $key=>$val){
		echo '<option value='.$key;
		if($key==$segundos)
			echo ' selected';
		echo '>'.$val,'</option>';
	}
	echo '</select><input type="submit" value="Sincronizar Ahora"></form></body></html>';
?>