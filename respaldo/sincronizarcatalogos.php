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
			exit;
			}
		}
	}
	$base="gamn_local";
	$segundos=$_POST['segundos'];
	$resTaq=mysql_db_query($base,"SELECT * FROM taquilla LIMIT 1");
	$rowTaq=mysql_fetch_array($resTaq);
	$cveTaq=$rowTaq['cve'];
	$asincroniza=array(1=>'1 Hora',3=>'3 Horas',5=>'5 Horas');
	echo "Segundos:$segundos";
	if($segundos==0)
		$segundos=1;
	echo '<html><head><title>Sincronizar Catalogos</title></head>
	<script language="javascript">
		function cargar(){
			setTimeout("sincronizar()",'.($segundos*3600000).');	
		}
		function sincronizar(){
			//alert("Se sincronizaran los catalogos");
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
		$parametros=array ('tabla' => 'taquillas','taquilla'=>$cveTaq);
		$respuesta = $oSoapClient->call("ReadTable", $parametros);
		$err = $oSoapClient->getError();
		print_r($err);
		if (!$err){
			if($respuesta['resultado']){
				//Eliminar la informacion Actual
				//mysql_db_query($base,"DELETE FROM taquilla ");
				//Tomar la informacion de Retorno
				$strData=base64_decode($respuesta['mensaje']);
				$vecData=explode("\n", $strData);
				$totreg=0;
				foreach($vecData As $query)
					if($query!=''){
						$totreg++;
						mysql_db_query($base,"UPDATE taquilla SET $query") or die(mysql_error());
					}
				echo "OK Taquilla($totreg Registros Actualizados)<br/>";
			}
			else
				echo $respuesta['mensaje'];
		}
		//Taquilleros
		$parametros=array ('tabla' => 'costo_boletos','taquilla'=>$cveTaq);
		$respuesta = $oSoapClient->call("ReadTable", $parametros);
		$err = $oSoapClient->getError();
		print_r($err);
		if (!$err){
			if($respuesta['resultado']){
				//Eliminar la informacion Actual
				mysql_db_query($base,"DELETE FROM costo_boletos ");
				//Tomar la informacion de Retorno
				$strData=base64_decode($respuesta['mensaje']);
				$vecData=explode("\n", $strData);
				$totreg=0;
				foreach($vecData As $query)
					if($query!=''){
						$totreg++;
						mysql_db_query($base,"INSERT into costo_boletos SET $query");
					}
				echo "OK Costos de Boletos($totreg Registros Actualizados)<br/>";
			}
			else
				echo $respuesta['mensaje'];
		}
		//Parque

		$parametros=array ('tabla' => 'parque','taquilla'=>$cveTaq);
		$respuesta = $oSoapClient->call("ReadTable", $parametros);
		$err = $oSoapClient->getError();
		if (!$err){
			if($respuesta['resultado']){
				echo $respuesta['mensaje'];
				//Eliminar la informacion Actual
				mysql_db_query($base,"DELETE FROM unidades");
				//Tomar la informacion de Retorno
				$strData=base64_decode($respuesta['mensaje']);
				$vecData=explode("\n", $strData);
				$totreg=0;
				foreach($vecData As $query)
					if($query!=''){
						$totreg++;
						mysql_db_query($base,"INSERT unidades SET $query");
					}
				echo "OK Unidades($totreg Registros Actualizados)<br/>";
			}
			else
				echo $respuesta['mensaje'];
		}

		$parametros=array ('tabla' => 'usuarios','taquilla'=>$cveTaq);
		$respuesta = $oSoapClient->call("ReadTable", $parametros);
		$err = $oSoapClient->getError();
		if (!$err){
			if($respuesta['resultado']){
				//Eliminar la informacion Actual
				mysql_db_query($base,"DELETE FROM usuarios");
				//Tomar la informacion de Retorno
				$strData=base64_decode($respuesta['mensaje']);
				$vecData=explode("\n", $strData);
				$totreg=0;
				foreach($vecData As $query)
					if($query!=''){
						$totreg++;
						mysql_db_query($base,"INSERT usuarios SET $query");
					}
				echo "OK Usuarios($totreg Registros Actualizados)<br/>";
			}
			else
				echo $respuesta['mensaje'];
		}
	}
	echo '<form name="forma" enctype="multipart/form-data" method="POST" action="sincronizarcatalogos.php">Intervalo de Sincronizacion:';
	echo '<select name="segundos">';
	foreach($asincroniza as $key=>$val){
		echo '<option value='.$key;
		if($key==$segundos)
			echo ' selected';
		echo '>'.$val,'</option>';
	}
	echo '</select><input type="submit" value="Sincronizar Ahora"></form></body></html>';
?>