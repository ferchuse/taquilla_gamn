<?php 

include ("main.php"); 


/*** ELIMINAR REGISTRO  **************************************************/

/*if ($_POST['cmd']==3) {
	$delete= "UPDATE productos SET borrado='SI' WHERE cve='".$_POST['reg']."' ";
	$ejecutar=mysql_db_query($base,$delete);
	header("Location: plazas.php");
}*/
$array_estatuspre=array("Activo","Inactivo");
$array_tipopre=array("Normal","Promocion");
/*** ACTUALIZAR REGISTRO  **************************************************/

if ($_POST['cmd']==1) {
	$cadena="";
	$select= " SELECT * FROM tickets WHERE fecha>='".$_POST['fecha_ini']."' AND fecha<='".$_POST['fecha_fin']."'";
	$rspuesto = mysql_db_query($base,$select);
	while($row=mysql_fetch_array($rspuesto)){
		$cadena.="INSERT tickets2 SET taq='".$row['taq']."',cve='".$row['cve']."',cveprecio='".$row['cveprecio']."',fecha='".$row['fecha']."',hora='".$row['hora']."',monto='".$row['monto']."',unidad='".$row['unidad']."',usuario='".$row['usuario']."',empresa='".$row['empresa']."',tipopago='".$row['tipopago']."',revendedor='".$row['revendedor']."'\n";
	}
	
	header("Content-type: TXT");
	header("Content-Length: $len");
	header("Content-Disposition: attachment; filename=venta_".$_POST['fecha_ini']."_a_".$_POST["fecha_fin"].".txt");
	print $cadena;
	exit();
		
}

/*** CONSULTA AJAX  **************************************************/

if($_POST['ajax']==1) {
		//Listado de plazas
		$select= " SELECT * FROM tickets WHERE fecha>='".$_POST['fecha_ini']."' AND fecha<='".$_POST['fecha_fin']."'";
		$rspuesto = mysql_db_query($base,$select);
		/*if($totalRegistros / $eRegistrosPagina > 1) 
		{
			$eTotalPaginas = $totalRegistros / $eRegistrosPagina;
			if(is_int($eTotalPaginas))
			{$eTotalPaginas--;}
			else
			{$eTotalPaginas = floor($eTotalPaginas);}
		}
		$select .= " ORDER BY nombre LIMIT ".$primerRegistro.",".$eRegistrosPagina;
		$rspuesto=mysql_db_query($base,$select);*/
		
		if(mysql_num_rows($rspuesto)>0) 
		{
			echo '<table width="100%" border="0" cellpadding="4" cellspacing="1" class="">';
			$x=$suma=0;
			echo '<tr bgcolor="#E9F2F8"><th>Folio</th><th>Fecha</th><th>Hora</th><th>Precio</th><th>Tipo</th><th>Usuario</th></tr>';//<th>P.Costo</th><th>P.Venta</th>
			while($Puesto=mysql_fetch_array($rspuesto)) {
				rowb();
				echo '<td align="center">'.$Puesto['cve'].'</td>';
				echo '<td align="center">'.$Puesto['fecha'].'</td>';
				echo '<td align="center">'.$Puesto['hora'].'</td>';
				echo '<td align="right">'.$Puesto['monto'].'</td>';
				$res=mysql_db_query($base,"SELECT * FROM preciotickets WHERE cve='".$Puesto['cveprecio']."'");
				$row=mysql_fetch_array($res);
				echo '<td>'.$row['nombre'].'</td>';
				echo '<td>'.htmlentities($Puesto['usuario']).'</td>';
				echo '</tr>';
				$x++;
				$suma+=$Puesto['monto'];
			}
			echo '	
				<tr>
				<td colspan="3" bgcolor="#E9F2F8">'.$x.' Registro(s)</td>
				<td align="right" bgcolor="#E9F2F8">'.number_format($suma,2).'</td>
				<td colspan="2" bgcolor="#E9F2F8">&nbsp;</td>
				</tr>
			</table>';
			
		} else {
			echo '
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td class="sanLR10"><font class="fntN10B"> No se encontraron registros</font></td>
			</tr>	  
			</table>';
		}
		exit();	
}	


top($_SESSION);

/*** EDICION  **************************************************/



/*** PAGINA PRINCIPAL **************************************************/

	if ($_POST['cmd']<1) {
		//Busqueda
		echo '<table>';
		echo '<tr>';
		echo '<td><a href="#" onClick="atcr(\'exportar.php\',\'\',\'1\',\'0\');"><img src="images/guardar.gif" border="0"></a>&nbsp;Exportar</td><td>&nbsp;</td>
				<td><a href="#" onClick="buscarRegistros();"><img src="images/buscar.gif" border="0"></a>&nbsp;Buscar</td><td>&nbsp;</td>
				</tr></table>';
		echo '<table>';
		echo '<tr><td>Fecha Inicial</td><td><input type="text" name="fecha_ini" id="fecha_ini" class="readOnly" size="12" value="'.fechaLocal().'" readonly>&nbsp;<a href="#" onClick="displayCalendar(document.forms[0].fecha_ini,\'yyyy-mm-dd\',this,true)"><img src="images/calendario.gif" border="0"></a></td></tr>';
		echo '<tr><td>Fecha Final</td><td><input type="text" name="fecha_fin" id="fecha_fin" class="readOnly" size="12" value="'.fechaLocal().'" readonly>&nbsp;<a href="#" onClick="displayCalendar(document.forms[0].fecha_fin,\'yyyy-mm-dd\',this,true)"><img src="images/calendario.gif" border="0"></a></td></tr>';
		echo '</table>';
		echo '<br>';

		//Listado
		echo '<div id="Resultados">';
		echo '</div>';
	}
	
bottom();



/*** RUTINAS JS **************************************************/
echo '
<Script language="javascript">

	function buscarRegistros()
	{
		document.getElementById("Resultados").innerHTML = "<img src=\'images/ajaxtrabajando.gif\' border=\'0\' align=\'absmiddle\'> Espere un momento, buscando registros...";
		objeto=crearObjeto();
		if (objeto.readyState != 0) {
			alert("Error: El Navegador no soporta AJAX");
		} else {
			objeto.open("POST","exportar.php",true);
			objeto.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			objeto.send("ajax=1&fecha_ini="+document.getElementById("fecha_ini").value+"&fecha_fin="+document.getElementById("fecha_fin").value);
			objeto.onreadystatechange = function()
			{
				if (objeto.readyState==4)
				{document.getElementById("Resultados").innerHTML = objeto.responseText;}
			}
		}
		document.getElementById("numeroPagina").value = "0"; //Se reestablece la variable para que las busquedas por criterio no se afecten.
	}
	
	//Funcion para navegacion de Registros. 20 por pagina.
	function moverPagina(x) {
		document.getElementById("numeroPagina").value = x;
		buscarRegistros();
	}';	
	if($_POST['cmd']<1){
	echo '
	window.onload = function () {
			buscarRegistros(); //Realizar consulta de todos los registros al iniciar la forma.
	}';
	}
	echo '
	
	</Script>
';

?>

