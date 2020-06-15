<?php

include("main.php"); 
$rsUsuario=mysql_db_query($base,"SELECT * FROM usuarios");
while($Usuario=mysql_fetch_array($rsUsuario)){
	$array_usuario[$Usuario['cve']]=$Usuario['usuario'];
	$array_nomusuario[$Usuario['cve']]=$Usuario['nombre'];
}

$rsUsuario=mysql_db_query($base,"SELECT * FROM costo_boletos");
while($Usuario=mysql_fetch_array($rsUsuario)){
	$array_precios[$Usuario['cve']]=$Usuario['nombre'];
}



function insertar_ceros($num){
	$numero="";
	for($i=0;$i<7-strlen(strval($num));$i++)
		$numero.="0";
	return $numero.$num;
}

$resTaq=mysql_db_query($base,"SELECT * FROM taquilla LIMIT 1");
$rowTaq=mysql_fetch_array($resTaq);
$cveTaq=$rowTaq['cve'];
$nomTaq=$rowTaq['nombre'];
$tipoimp=$rowTaq['tipo_impresora'];
$nombimp=$rowTaq['nombre_impresora'];

top($_SESSION);

if($_POST['cmd']==50){
	$fp = fopen ("a.txt","w+");
	
	fwrite($fp,chr(27).'!'.chr(6)." NEXTLALPAN \n\n");
	//fwrite($fp,chr(27).'!'.chr(6)."           R.F.C.:AAZ6803283V5\n\n");
	fwrite($fp,chr(27).'!'.chr(10)."Costo: 99\n");
	fwrite($fp,chr(27).'!'.chr(10)."Taquilla: ".$nomTaq."\n");
	fwrite($fp,chr(27).'!'.chr(10)."Usuario: Prueba\n");
	//fwrite($fp,chr(29)."h".chr(80).chr(29)."H".chr(2).chr(29)."k".chr(2)."1".sprintf("%02s",(intval(9))).sprintf("%09s","999999").chr(0));
	fwrite($fp,chr(27).'!'.chr(10)." \n");
	fwrite($fp,chr(27).'!'.chr(40)."Folio: 999999\n");
	fwrite($fp,chr(27).'!'.chr(40)."Prueba\n\n");
	fwrite($fp,chr(27).'!'.chr(10)."9999-99-99 99:99:99            OPERADOR\n");
	fwrite($fp,chr(29).chr(86).chr(66).chr(0));
	fclose($fp);
	if($tipoimp==0)
		system("copy a.txt ".$nombimp.": >null:");
	else
		exec('copy a.txt "\\\\caja\\EPSON TM-T20II Receipt"');

	$fp1 = fopen ("b.txt","w+");
	fwrite($fp1,chr(27).'!'.chr(6)."    NEXTLALPAN\n\n");
	//fwrite($fp1,chr(27).'!'.chr(6)."           R.F.C.:AAZ6803283V5\n\n");
	fwrite($fp1,chr(27).'!'.chr(10)."PASAJERO   $ 99 (Prueba)\n");
	fwrite($fp1,chr(27).'!'.chr(10)."Usuario: Prueba\n");
	fwrite($fp1,chr(27).'!'.chr(10)."Taquilla: ".$nomTaq."\n");
	fwrite($fp1,chr(27).'!'.chr(10)."Este boleto ampara el seguro de viajero en la fecha y hora se".chr(164)."alada\n\n");
	fwrite($fp1,chr(27).'!'.chr(10)."9999-99-99 99:99:99   FOLIO: 999999\n");
	fwrite($fp1,chr(29).chr(86).chr(66).chr(0));
	fclose($fp1);
	if($tipoimp==0)
		system("copy b.txt ".$nombimp.": >null:");
	else
		exec('copy b.txt "\\\\caja\\EPSON TM-T20II Receipt"');
}

if($_POST['cmd']==4){
	$res = mysql_db_query($base,"SELECT * FROM guia WHERE fecha=CURDATE() AND fecha_fin='0000-00-00' ORDER BY cve DESC LIMIT 1");
	$row = mysql_fetch_array($res);
	mysql_db_query($base,"UPDATE guia SET fecha_fin=CURDATE(), hora_fin=CURTIME(), usuario_fin='".$_POST['cveusuario']."' WHERE cve='".$row['cve']."'");
	if($row['cve'] > 0){
		$res = mysql_db_query($base,"SELECT * FROM guia WHERE cve='".$row['cve']."'");
		$row = mysql_fetch_array($res);
		$fp = fopen ("c.txt","w");
		//system("copy TMTEPE.TMB lpt1");
		fwrite($fp,chr(27).'!'.chr(6)."   NEXTLALPAN\n");
		fwrite($fp,chr(27).'!'.chr(40)."  GUIA: ".$row['cve']."\n");
		fwrite($fp,chr(27).'!'.chr(40)."   NUM ECO: ".$row['no_eco']."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   FECHA: ".$row['fecha']."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   HORA INICIO: ".$row['hora']."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   HORA CIERRE: ".$row['hora_fin']."\n");
		$res = mysql_db_query($base, "SELECT b.nombre, a.monto, COUNT(a.cve), SUM(a.monto) FROM 
			boletos a LEFT JOIN costo_boletos b ON b.cve = a.costo WHERE a.guia = '".$row['cve']."' 
			GROUP BY a.costo ORDER BY b.nombre");
		$tot1=$tot2=0;
		while($row = mysql_fetch_array($res)){
			$tot1+=$row[2];
			$tot2+=$row[3];
			fwrite($fp,chr(27).'!'.chr(6)."   TOTAL BOL ".$row[1]." ".substr($row[0],0,10).":   ".$row[2]."\n");
			fwrite($fp,chr(27).'!'.chr(6)."   IMPORTE BOL ".$row[1]." ".substr($row[0],0,10).":  $  ".$row[3]."\n\n");
		}

		fwrite($fp,chr(27).'!'.chr(6)."   TOTAL BOLETOS:   ".$tot1."\n");
		fwrite($fp,chr(27).'!'.chr(6)."   IMPORTE BOLETOS:  $  ".$tot2."\n\n");
		fwrite($fp,chr(29).chr(86).chr(66).chr(0));
		fclose($fp);

		
		if($tipoimp==0){
			system("copy LOGO.TMB ".$nombimp."");
			system("copy c.txt ".$nombimp.": >null:");
		}
		else{
			exec('copy LOGO.TMB "\\\\caja\\EPSON TM-T20II Receipt"');
			exec('copy c.txt "\\\\caja\\EPSON TM-T20II Receipt"');
		}
	}

	$_POST['cmd']=0;
}

if($_POST['cmd']==2){
	$select=" SELECT * FROM unidades WHERE no_eco='".trim($_POST['unidad'])."'";
	$resuni=mysql_db_query($base,$select);
	$rowuni=mysql_fetch_array($resuni);
	if($rowuni['cve']>0){
		if($rowuni['estatus']==1){
			$res = mysql_db_query($base,"SELECT * FROM guia WHERE fecha=CURDATE() AND fecha_fin='0000-00-00' ORDER BY cve DESC LIMIT 1");
			$row = mysql_fetch_array($res);
			if($row['unidad'] == $rowuni['cve']){
				$guia = $row['cve'];
			}
			else{
				mysql_db_query($base,"UPDATE guia SET fecha_fin=CURDATE(), hora_fin=CURTIME(), usuario_fin='".$_POST['cveusuario']."' WHERE cve='".$row['cve']."'");
				if($row['cve'] > 0){
					$res = mysql_db_query($base,"SELECT * FROM guia WHERE cve='".$row['cve']."'");
					$row = mysql_fetch_array($res);
					$fp = fopen ("c.txt","w");
					//system("copy TMTEPE.TMB lpt1");
					fwrite($fp,chr(27).'!'.chr(6)."   NEXTLALPAN\n");
					fwrite($fp,chr(27).'!'.chr(40)."  GUIA: ".$row['cve']."\n");
					fwrite($fp,chr(27).'!'.chr(40)."   NUM ECO: ".$row['no_eco']."\n");
					fwrite($fp,chr(27).'!'.chr(6)."   FECHA: ".$row['fecha']."\n");
					fwrite($fp,chr(27).'!'.chr(6)."   HORA INICIO: ".$row['hora']."\n");
					fwrite($fp,chr(27).'!'.chr(6)."   HORA CIERRE: ".$row['hora_fin']."\n\n");

					$res = mysql_db_query($base, "SELECT b.nombre, a.monto, COUNT(a.cve), SUM(a.monto) FROM 
						boletos a LEFT JOIN costo_boletos b ON b.cve = a.costo WHERE a.guia = '".$row['cve']."' 
						GROUP BY a.costo ORDER BY b.nombre");
					$tot1=$tot2=0;
					while($row = mysql_fetch_array($res)){
						$tot1+=$row[2];
						$tot2+=$row[3];
						fwrite($fp,chr(27).'!'.chr(6)."   TOTAL BOL ".$row[1]." ".substr($row[0],0,10).":   ".$row[2]."\n");
						fwrite($fp,chr(27).'!'.chr(6)."   IMPORTE BOL ".$row[1]." ".substr($row[0],0,10).":  $  ".$row[3]."\n\n");
					}

					fwrite($fp,chr(27).'!'.chr(6)."   TOTAL BOLETOS:   ".$tot1."\n");
					fwrite($fp,chr(27).'!'.chr(6)."   IMPORTE BOLETOS:  $  ".$tot2."\n\n");
					fwrite($fp,chr(29).chr(86).chr(66).chr(0));
					fclose($fp);

					
					if($tipoimp==0){
						system("copy LOGO.TMB ".$nombimp."");
						system("copy c.txt ".$nombimp.": >null:");
					}
					else{
						exec('copy LOGO.TMB "\\\\caja\\EPSON TM-T20II Receipt"');
						exec('copy c.txt "\\\\caja\\EPSON TM-T20II Receipt"');
					}
				}
				mysql_db_query($base,"INSERT guia SET fecha=CURDATE(), hora = CURTIME(), unidad='".$rowuni['cve']."', no_eco='".$rowuni['no_eco']."',usuario='".$_POST['cveusuario']."'");
				$guia = mysql_insert_id();				
			}
			$res=mysql_db_query($base,"SELECT * FROM costo_boletos WHERE cve='".$_POST['costo']."'");
			$row=mysql_fetch_array($res);
			$afolios=array();
			for($i=1;$i<=$_POST['cantidad'];$i++){
				$sql="INSERT boletos SET fecha=CURDATE(),hora=CURTIME(),guia='".$guia."',unidad='".$rowuni['cve']."',no_eco='".$rowuni['no_eco']."',
				usuario='".$_POST['cveusuario']."',costo='".$row['cve']."',monto='".$row['costo']."',estatus=0";
				mysql_db_query($base,$sql) or die(mysql_error());
				
				$folio=mysql_insert_id();
				$afolios[$i]=$folio;
			}
			
			$fp = fopen ("a.txt","w+");
			$fechaL=fechaLocal();
			$horaL=horaLocal();
			$fechaV=date( "Y-m-d" , strtotime ( "+ 30 day" , strtotime($fechaL) ) );
			for($i=1;$i<=$_POST['cantidad'];$i++){
				fwrite($fp,chr(27)."@");
				fwrite($fp,chr(27).'!'.chr(50)." NEXTLALPAN\n");
				fwrite($fp,chr(27).'!'.chr(10).sprintf("% 40s",fechaNormal($fechaL)." ".$horaL)."\n");
				//fwrite($fp,chr(27).'!'.chr(30).$row['nombre']."\n");
				fwrite($fp,chr(27).'!'.chr(10)."Unidad:   ".$rowuni['no_eco']."\n");
				fwrite($fp,chr(27).'!'.chr(10)."Costo:    ".$row['costo']."\n");
				fwrite($fp,chr(27).'!'.chr(10)."Taquilla: ".$nomTaq."\n");
				fwrite($fp,chr(27).'!'.chr(10)."Vendedor: ".$array_usuario[$_POST['cveusuario']]."\n");
				
				//fwrite($fp,chr(27).'!'.chr(30)."VIGENCIA HASTA : ".fechaNormal($fechaV)."\n");
				//fwrite($fp,chr(29)."h".chr(80).chr(29)."H".chr(2).chr(29)."k".chr(5).sprintf("%02s",$cveTaq).sprintf("%04s",(intval($row['cve']))).sprintf("%09s",$afolios[$i]).sprintf("%03s",$_POST['unidad']).chr(0));
				fwrite($fp,chr(27).'!'.chr(10)." \n");
				fwrite($fp,chr(27).'!'.chr(40)."".$row['nombre']."\n");
				fwrite($fp,chr(27).'!'.chr(40)."Folio: ".$afolios[$i]."\n");
				fwrite($fp,chr(27).'!'.chr(10).sprintf("% 40s","PASAJERO\n"));
				fwrite($fp,chr(29).chr(86).chr(66).chr(0));
				fwrite($fp,chr(27).'!'.chr(10).sprintf("% 40s",fechaNormal($fechaL)." ".$horaL)."\n");
				fwrite($fp,chr(27).'!'.chr(10)."Precio:   ".$row['costo']."(".$row['nombre'].")\n");
				fwrite($fp,chr(27).'!'.chr(10)."Vendedor: ".$array_usuario[$_POST['cveusuario']]."\n");
				fwrite($fp,chr(27).'!'.chr(10)."ESTE BOLETO AMPARA EL SEGURO DE VIAJERO\n");
				//fwrite($fp,chr(29)."h".chr(80).chr(29)."H".chr(2).chr(29)."k".chr(5).sprintf("%02s",$cveTaq).sprintf("%04s",(intval($row['cve']))).sprintf("%09s",$afolios[$i]).sprintf("%03s",$_POST['unidad']).chr(0));
				fwrite($fp,chr(27).'!'.chr(40)."Folio: ".$afolios[$i]."\n");
				fwrite($fp,chr(27).'!'.chr(10).sprintf("% 40s","OPERADOR\n"));
				fwrite($fp,chr(29).chr(86).chr(66).chr(0));
			}
			fclose($fp);
			if($tipoimp==0){
				system("copy a.txt ".$nombimp.": >null:");
			}
			else{
				exec('copy a.txt "\\\\caja\\EPSON TM-T20II Receipt"');
			}
			//unlink("a.txt");
		}
		else{
			echo '<script>alert("La unidad no esta dada de alta");</script>';
			$_POST['unidad']="";
		}
	}
	else{
		echo '<script>alert("El no economico no existe");</script>';
		$_POST['unidad']="";
	}
}


echo '<input type="hidden" name="cveusuario" value="'.$_SESSION['CveUsuario'].'">';

		echo '<br><br><br>';
echo '<table>';
echo '<tr><th align="left">Taquillero</th><td>'.$array_nomusuario[$_SESSION['CveUsuario']].'</td><td id="idreloj">'.fechaLocal().' '.horaLocal().'</td></tr>';
echo '</table>';
echo '<br><table>';
echo '<tr><td align="left" style="font-size:40px">No Economico</td><td><input type="text" name="unidad" id="unidad" style="font-size:40px" class="textField" value="'.@$_POST['unidad'].'"></td></tr>';

echo '<tr><td align="left" style="font-size:40px">Costo</td><td><select style="font-size:40px" name="costo" id="costo" onChange="calcular()">
<option value="0" costo="0">Seleccione</option>';
$res1=mysql_db_query($base,"SELECT * FROM costo_boletos WHERE estatus=0 ORDER BY nombre");
while($row1=mysql_fetch_array($res1)){
	echo '<option value="'.$row1['cve'].'" costo="'.$row1['costo'].'">'.$row1['costo'].' '.$row1['nombre'].'</option>';
}
echo '</select></td></tr>';

echo '<tr><td align="left" style="font-size:40px">Cantidad</td><td><select style="font-size:40px" onChange="calcular()" name="cantidad" id="cantidad" onChange="calcular()">
<option value="0" costo="0">0</option>';
for($i=1;$i<=9;$i++) echo '<option value="'.$i.'">'.$i.'</option>';
echo '</select></td></tr>';
echo '<tr><td align="left" style="font-size:40px">Total</td><td><input type="text" name="total" id="total" style="font-size:40px" class="readOnly" value="" readOnly></td></tr>';
echo '</table>';
echo '<input type="button" class="botones" style="width:200px;height:50px;font-size:40px;" value="Vender" 
			onclick="
					if(document.forma.unidad.value==\'\'){ 
						alert(\'No ha puesto la unidad\');
					} 
					else if(document.forma.unidad.value!=\'0000\' && document.forma.costo.value==\'0\'){ 
						alert(\'No ha seleccionado el costo\');
					} 
					else if(document.forma.unidad.value!=\'0000\' && document.forma.cantidad.value==\'0\'){ 
						alert(\'No ha seleccionado la cantidad\');
					} 
					else{ 
						$(\'.botones\').attr(\'disabled\',\'-1\');
						if(document.forma.unidad.value==\'0000\')
							atcr(\'tickets.php\',\'\',4,0);
						else
							atcr(\'tickets.php\',\'\',2,0);
					}" 
					disabled>';
echo '&nbsp;&nbsp;<input type="button" class="botones" style="width:200px;height:50px;font-size:40px;" value="Guia" 
			onclick="atcr(\'tickets.php\',\'\',4,0);" disabled>';
echo '<script>
		window.setTimeout("$(\'.botones\').removeAttr(\'disabled\');",2000);
		function calcular(){
			total = $("#costo").find("option:selected").attr("costo")*document.forma.cantidad.value;
			document.forma.total.value=total.toFixed(2);
		}

';
if($_POST['cmd']==2){
	echo 'atcr(\'tickets.php\',\'\',0,0);';
}
echo '
</script>';

bottom();
?>