<?php
	include("main.php");
	$rsUsuario=mysql_db_query($base,"SELECT * FROM usuarios");
	while($Usuario=mysql_fetch_array($rsUsuario)){
		$array_usuario[$Usuario['cve']]=$Usuario['usuario'];
		$array_nomusuario[$Usuario['cve']]=$Usuario['nombre'];
	}

	top($_SESSION);
	if($_POST['cmd']==10){
		//mysql_db_query($base,"INSERT folios_cortes SET fecha='".fechaLocal()." ".horaLocal()."',usuario='".$_SESSION['NickUsuario']."',usucorte='".$_POST['usuario']."',feccorte='".$_POST['fecha']."'");
		//$foliocorte=mysql_insert_id();
		$res=mysql_db_query($base,"SELECT * FROM taquilla");
		$row=mysql_fetch_array($res);
		$nomplaza=$row['nombre'];
		$tipoimp=$row['tipo_impresora'];
		$nombimp=$row['nombre_impresora'];
		$filtro="";
		if($_POST['usuario']!="") $filtro.=" AND usuario='".$_POST['usuario']."'";
		$res=mysql_db_query($base,"SELECT max(hora),min(hora),MAX(cve),MIN(cve) FROM boletos where fecha='".$_POST['fecha']."' $filtro LIMIT 1");
		$row=mysql_fetch_array($res);
		
		$horainicio=$row[1];
		$horacierre=$row[0];
		$folioinicio=$row[3];
		$foliocierre=$row[2];
		
		$sSQL="SELECT costo,COUNT(cve),monto,SUM(monto)
				FROM boletos
				WHERE fecha='".$_POST['fecha']."' $filtro 
				GROUP BY costo"; 
		$res=mysql_db_query($base,$sSQL) or die(mysql_error());
		$boletos=0;
		$total=0;
		$cuerpo="";
		while($row=mysql_fetch_array($res)){
			$res1=mysql_db_query($base,"SELECT * FROM boletos WHERE fecha='".$_POST['fecha']."' $filtro AND costo='".$row[0]."' ORDER BY cve") or die(mysql_error());

			$cuerpo.="   TOTAL BOL ".$row[2]." ".substr($array_precios[$row1['costo']],0,10).":   ".$row[1]."|";
			$cuerpo.="   IMPORTE BOL ".$row[2]." ".substr($array_precios[$row1['costo']],0,10).":  $  ".$row[3]."||";
			$boletos+=$row[1];
			$total+=$row[3];
		}
		
		//system("copy c.txt lpt1");
		$fp = fopen ("c.txt","w");
		//system("copy BRUAS.TMB lpt1");
		//if(!copy("BRUAS.TMB","lpt1"))
		//	echo "error al imprimir el tiket";
		fwrite($fp,chr(27).'!'.chr(6)."   TAQUILLA: ".strtoupper($nomplaza)."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   CORTE DE CAJA DEL DIA: ".$_POST['fecha']."\n");
		//fwrite($fp,chr(27).'!'.chr(6)."   FOLIO: ".$foliocorte."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   USUARIO: ".$_POST['usuario']."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   HORA INICIO: ".$horainicio."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   HORA CIERRE: ".$horacierre."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   FOLIO INICIO: ".$folioinicio."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   FOLIO CIERRE: ".$foliocierre."\n");
		$datos=explode("|",$cuerpo);
		for($i=0;$i<(count($datos)-1);$i++){
			fwrite($fp,chr(27).'!'.chr(6)."   ".$datos[$i]."\n");
		}
		fwrite($fp,chr(27).'!'.chr(6)."   ------------------------------------\n");
		fwrite($fp,chr(27).'!'.chr(6)."   TOTAL BOLETOS:   ".$boletos."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   IMPORTE BOLETOS:  $  ".$total."\n\n");
		
		fwrite($fp,chr(29).chr(86).chr(66).chr(0));
		fclose($fp);

		
		//system("copy c.txt lpt1: >null:");
		if($tipoimp==0){
			system("copy c.txt ".$nombimp.": >null:");
		}
		else{
			exec('copy c.txt "\\\\caja\\EPSON TM-T20II Receipt"');
		}
	}
	
	echo '<table style="font-size:20px;">
			<tr>
				<td>
					<a href="#" onClick="atcr(\'cortes.php\',\'\',\'10\',\'0\');"><img src="images/b_print.png" border="0">&nbsp;Imprimir Corte</a>
				</td>
			</tr>
		</table>';
		
	echo '<table style="font-size:20px;"><tr><td>Usuario</td><td><select name="usuario" style="font-size:20px;"><option value="">Todos</option>';
	$res=mysql_db_query($base,"SELECT usuario FROM boletos GROUP BY usuario");
	while($row=mysql_fetch_array($res)){
		echo '<option value="'.$row[0].'">'.$array_usuario[$row[0]].'</option>';
	}
	echo '</select></td></tr>';
	if($_POST['fecha']=="") $_POST['fecha']=fechaLocal();
	echo '<tr><td>Fecha</td><td><input type="text" name="fecha" id="fecha" style="font-size:20px;" class="readOnly" size="15" value="'.$_POST['fecha'].'" readonly>&nbsp;<a href="#" onClick="displayCalendar(document.forms[0].fecha,\'yyyy-mm-dd\',this,true)"><img src="images/calendario.gif" border="0"></a></td></tr>';
	echo '</table>';
	
	bottom();
?>