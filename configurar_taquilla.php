<?php
	include("main.php");

	if($_POST['cmd']==2){
		require_once("nusoap/nusoap.php");
		$oSoapClient = new nusoap_client("http://road.checame.net/wsSincronizar.php?wsdl", true);	
		$err = $oSoapClient->getError();
		if($err!="")
			echo "error1:".$err;
		else{
			//Taquilleros
			$parametros=array ('tabla' => 'taquillas', 'taquilla'=> $_POST['taquilla']);
			$respuesta = $oSoapClient->call("ReadTable", $parametros);
			$err = $oSoapClient->getError();
			print_r($err);
			if (!$err){
				if($respuesta['resultado']){
					//Eliminar la informacion Actual
					mysql_db_query($base,"DELETE FROM taquilla");
					//Tomar la informacion de Retorno
					$strData=base64_decode($respuesta['mensaje']);
					$vecData=explode("\n", $strData);
					$totreg=0;
					foreach($vecData As $query)
						if($query!=''){
							$totreg++;
							mysql_db_query($base,"INSERT into taquilla SET $query");
						}
				}
				else
					echo $respuesta['mensaje'];
			}
		}
		$res = mysql_db_query($base, "SELECT * FROM taquilla");
		$row = mysql_fetch_array($res);
		if($row['cve']>0){
			header("Location: tickets.php");
		}
	}	

	top($_SESSION, true);
	echo '<table><tr><th>Numero Taquilla</th><td><input type="text" name="taquilla" id="taquilla" value=""></td></tr>
	<tr style="display:none;"><th>Quita enter</th><td><input type="text" name="quitaenter" id="quitaenter" value=""></td></tr>
	<tr><td colspan="2"><input type="button" value="Guardar" onClick="atcr(\'configurar_taquilla.php\',\'\',\'2\',\'0\');"></td></tr>';
	
	bottom();
?>