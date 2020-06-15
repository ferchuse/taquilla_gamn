<?php
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
global $base,$PHP_SELF;
$base="gamn_local";
/*Validamos solicitud de login a este sitio*/
if (!isset($_SESSION)) {
  session_start();
}
if(!$_SESSION['CveUsuario'] && !$_SESSION['NomUsuario']) {
	header("Location: index2.php");
}
$archivo=explode("/",$_SERVER["PHP_SELF"]);
global $archivo,$reg_sistema;

$array_meses=array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
$array_dias=array("Domingo","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado");
//Si existen las variables POST  usuario y password viene de login
if (isset($_POST['loginUser']) && isset($_POST['loginPassword'])) {
	mysql_db_query($base,"SET GLOBAL time_zone = SYSTEM");
	//Como se supone venimos de ventana de login o sesion expirada, eliminamos cualquier rastro de sesion anterior
	// Unset all of the session variables.
	$_SESSION = array();
	// Finally, destroy the session.
	session_destroy();
	$loginUsername=$_POST['loginUser'];
	$password=$_POST['loginPassword'];
	$res = mysql_db_query($base,"SELECT * FROM taquilla");
	$row = mysql_fetch_array($res);
	if($row['cve']==0){
		$redirectLoginSuccess = "configurar_taquilla.php";
	}
	else{
		$redirectLoginSuccess = "tickets.php";
	}
	$redirectLoginFailed = "index2.php?ErrLogUs=true";
	//Hacemos uso de la funcion GetSQLValueString para evitar la inyeccion de SQL
	//$LoginRS_query = sprintf("SELECT * FROM usuarios WHERE usuario LIKE BINARY %s AND password LIKE BINARY %s", Se le quito validacion de distincion de mayusculas y minusculas
	$LoginRS_query = sprintf("SELECT * FROM usuarios WHERE usuario = %s AND password = %s AND tipo_taquilla>0 AND estatus='A'",
			  GetSQLValueString($loginUsername, "text"), GetSQLValueString($password, "text")); 
	//echo $LoginRS_query;
	//exit();
	$LoginRS = mysql_db_query($base,$LoginRS_query) or die(mysql_error());
	$loginFoundUser = mysql_num_rows($LoginRS);
	if ($loginFoundUser) {
		mysql_db_query($base,"UPDATE guia SET fecha_fin = fecha, hora_fin = '23:59:59', usuario_fin=1 WHERE fecha<CURDATE() AND fecha_fin = '0000-00-00'");
		$Usuario=mysql_fetch_array($LoginRS);
		//Creamos la sesion
		session_start();		
		//Creamos las variables de sesion del usuario en cuestion
		$_SESSION['CveUsuario'] = $Usuario['cve'];
		$_SESSION['NomUsuario'] = $Usuario['nombre'];
		$_SESSION['NickUsuario'] = $Usuario['usuario'];
		$_SESSION['TipoUsuario'] = $Usuario['tipo_taquilla'];
		header("Location: " . $redirectLoginSuccess );
	} else {
		header("Location: " . $redirectLoginFailed);
	}
}



function top($SESSION, $no_menu=false) {
	global $base,$PHP_SELF;
	$url=split("/",$_SERVER["PHP_SELF"]);
	$url=array_reverse($url);
	echo '
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>:: NEXTLALPAN ::</title>
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="calendar/dhtmlgoodies_calendar.css" />
	<style>.colorrojo { color: #FF0000 } </style>
	<script src="js/rutinas.js"></script>
	<script src="js/jquery-1.2.6.min.js" type="text/javascript"></script>
	<script src="calendar/dhtmlgoodies_calendar.js"></script>
	<script>
	/*var fecha = "'.fechaLocal().' '.horaLocal().'";
	var	momentoActual = new Date(fecha);
	var	hora = momentoActual.getHours();
	var	minuto = momentoActual.getMinutes();
	var	segundo = momentoActual.getSeconds();
	var	dia = momentoActual.getDate();
	var	mes = momentoActual.getMonth()+1;
	var	anio = momentoActual.getFullYear();*/
	/*var	horas = parseInt("'.substr(horaLocal(),0,2).'");
	var	minuto = parseInt("'.substr(horaLocal(),3,2).'");
	var	segundo = parseInt("'.substr(horaLocal(),6,2).'");
	var	anio = parseInt("'.substr(fechaLocal(),0,4).'");
	var	mes = parseInt("'.substr(fechaLocal(),5,2).'");
	var	dia = parseInt("'.substr(fechaLocal(),8,2).'");*/
	function mueveReloj(){
		cadena=document.getElementById("idreloj").innerHTML;
		if(cadena.substr(11,1)=="0")
			var	horas = parseInt(cadena.substr(12,1));
		else
			var	horas = parseInt(cadena.substr(11,2));
		if(cadena.substr(14,1)=="0")
			var	minuto = parseInt(cadena.substr(15,1));
		else
			var	minuto = parseInt(cadena.substr(14,2));
		if(cadena.substr(17,1)=="0")
			var	segundo = parseInt(cadena.substr(18,1));
		else
			var	segundo = parseInt(cadena.substr(17,2));
		var	anio = parseInt(cadena.substr(0,4));
		if(cadena.substr(5,1)=="0")
			var	mes = parseInt(cadena.substr(6,1));
		else
			var	mes = parseInt(cadena.substr(5,2));
		if(cadena.substr(8,1)=="0")
			var	dia = parseInt(cadena.substr(9,1));
		else
			var	dia = parseInt(cadena.substr(8,2));
		segundo++;
		if (segundo==60) {
			segundo=0;
			minuto++;
			if (minuto==60) {
				minuto=0;
				horas++;
				if (horas==24) {
					horas=0;
					dia++;
					if((dia==31 && (mes==4 || mes==6 || mes==9 || mes==11)) || (dia==32 && (mes==1 || mes==3 || mes==5 || mes==7 || mes==8 || mes==10 || mes==12)) || (dia==29 && mes==2 && (anio%4)!=0) || (dia==30 && mes==2 && (anio%4)==0)){
						dia=1;
						mes++;
					}
					if(mes==13){
						mes=1;
						anio++;
					}
				}
			}
		}
		if(horas<10) horas="0"+parseInt(horas);
		if(minuto<10) minuto="0"+parseInt(minuto);
		if(segundo<10) segundo="0"+parseInt(segundo);
		if(dia<10) dia="0"+parseInt(dia);
		if(mes<10) mes="0"+parseInt(mes);
		horaImprimible = anio+"-"+mes+"-"+dia+" "+horas+":"+minuto+ ":"+segundo;
		document.getElementById("idreloj").innerHTML = horaImprimible;
		setTimeout("mueveReloj()",1000)
	}
	</script>
	
	</head>



	<form name="forma" id="forma" method="POST" enctype="multipart/form-data">
	<!-- Definicion de variables ocultas -->
		<input type="hidden" name="cmd" id="cmd">
		<input type="hidden" name="cmdreferer" id="cmdreferer">
		<input type="hidden" name="reg" id="reg">
		<input type="hidden" name="numeroPagina" id="numeroPagina" value="0">
	<body>';
	if(!$no_menu) echo '<table align="center" style="font-size:20px;"><tr><td><a href="#" onClick="atcr(\'inicio.php\',\'\',\'0\',\'0\');">Menu</td></tr></table><br><br>';
	$resTaq = mysql_db_query($base,"SELECT * FROM taquilla");
	$rowTaq = mysql_fetch_array($resTaq);
	echo '<div align="center"><h1>'.$rowTaq['nombre'].'</h1>';

}

function bottom() {



	echo '
	</div>
	</body>
	</form>

	</html>

	';

}


function menuppal2($SESSION) {
	global $base,$array_modulos,$PHP_SELF;
	$url=split("/",$_SERVER["PHP_SELF"]);
	$url=array_reverse($url);
	echo '<br><br>
	<table border="0" cellspacing="0" cellpadding="3" style="font-size:40px;">
		<tr><td height="20" bgcolor="#3399FF"><span class="style1">Menu</span></td></tr>
		<tr><td><a href="#" onClick="atcr(\'tickets.php\',\'\',\'\',\'\')">-Venta de Boletos</a></td></tr>';
		echo '<tr><td><a href="#" onClick="atcr(\'cortes.php\',\'\',\'\',\'\')">-Cortes</a></td></tr>';
		if($_SESSION['TipoUsuario']==1){
			//echo '<tr><td><a href="#" onClick="atcr(\'exportar.php\',\'\',\'\',\'\')">-Exportar</a></td></tr>';
			
			echo '<tr><td><a href="#" onClick="atcr(\'inicio.php\',\'\',\'99\',\'\')">-Reparar Tablas</a></td></tr>';
		}
		echo '<tr><td><a href="#" onClick="atcr(\'logout.php\',\'\',\'\',\'\')">-Cerrar Session</a></td></tr>';
		
	echo '</table>';
}

function menunavegacion() {



	global $totalRegistros, $eTotalPaginas, $eNumeroPagina, $primerRegistro, $eAnteriorPagina, $eSiguientePagina, $eNumeroPagina;



	echo '



	<table width="100%" height="20" border="0" cellpadding="0" cellspacing="0">

	<tr>

	<td width="20%" class="">'.$totalRegistros.'</font> Registro(s)</td>';

	if ($eTotalPaginas>0) {

		echo '

		<td width="60%" class="" align="right">P&aacute;gina <font class="fntN10B">';print $eNumeroPagina+1; echo'</font> de <font class="fntN10B">'; print $eTotalPaginas+1; echo'</font> </td>';

		if ($primerRegistro>0) {

			echo '

			<td width="12" align="center" class="sanLR10"><a href="JavaScript:moverPagina(0);"><img src="images/mover-primero.gif" width="10" height="12" border="0" align="absmiddle" title="Inicio"></a> </td>';

		} else {

			echo '

			<td width="12" align="center" class="sanLR10"><img src="images/mover-primero-d.gif" width="10" height="12" border="0" align="absmiddle"></td>';

		}



		if ($eAnteriorPagina>=0) {

			echo '

			<td width="12" align="center" class="sanLR10"><a href="JavaScript:moverPagina('.$eAnteriorPagina.');"><img src="images/mover-anterior.gif" width="7" height="12" border="0" align="absmiddle" title="Anterior"></a></td>';

		} else {

			echo '

			<td width="12" align="center" class="sanLR10"><img src="images/mover-anterior-d.gif" width="7" height="12" border="0" align="absmiddle"></td>';

		}



		if ($eSiguientePagina<=$eTotalPaginas) {

			echo '

			<td width="12" align="center" class="sanLR10"><a href="JavaScript:moverPagina('.$eSiguientePagina.');"><img src="images/mover-siguiente.gif" width="7" height="12" border="0" align="absmiddle" title="Siguiente"></a></td>';

		} else {

			echo '

			<td width="12" align="center" class="sanLR10"><img src="images/mover-siguiente-d.gif" width="7" height="12" border="0" align="absmiddle"></td>';

		}



		if ($eNumeroPagina<$eTotalPaginas) {

			echo '

			<td width="12" align="center" class="sanLR10"> <a href="JavaScript:moverPagina('.$eTotalPaginas.');"><img src="images/mover-ultimo.gif" width="10" height="12" border="0" align="absmiddle" title="Fin"></a></td>';

		} else {

			echo '

			<td width="12" align="center" class="sanLR10"><img src="images/mover-ultimo-d.gif" width="10" height="12" border="0" align="absmiddle"></td>';

		}



	}

	echo '

	</tr>

	</table>';

	

}





function menu() {

echo '';

}



	// Renglon en fondo Blanco

	function rowc() {

		echo '<tr bgcolor="#ffffff" onmouseover="sc(this, 1, 0);" onmouseout="sc(this, 0, 0);" onmousedown="sc(this, 2, 0);">';

	}



	// Renglones que cambian el color de fondo

	function rowb() {

		static $rc;

		if ($rc) {

			echo '<tr bgcolor="#d5d5d5" onmouseover="sc(this, 1, 1);" onmouseout="sc(this, 0, 1);" onmousedown="sc(this, 2, 1);">';

			$rc=FALSE;

		}

		else {

			echo '<tr bgcolor="#e5e5e5" onmouseover="sc(this, 1, 2);" onmouseout="sc(this, 0, 2);" onmousedown="sc(this, 2, 2);">';

			$rc=TRUE;

		}

	}





	function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 

	{

		$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;



		$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);



		switch ($theType) {

		case "text":

		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";

		  break;    

		case "long":

		case "int":

		  $theValue = ($theValue != "") ? intval($theValue) : "NULL";

		  break;

		case "double":

		  $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";

		  break;

		case "date":

		  $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";

		  break;

		case "defined":

		  $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;

		  break;

		}

		return $theValue;

	}



	

		function diaSemana($fecha) {

			$weekDay=array('DOMINGO','LUNES','MARTES','MIERCOLES','JUEVES','VIERNES','SABADO');

			$ano=substr($fecha,0,4);

			$mes=substr($fecha,5,2);

			$dia=substr($fecha,8,2);

			$numDia=jddayofweek ( cal_to_jd(CAL_GREGORIAN, date($mes),date($dia), date($ano)) , 0 );

			$result=$weekDay[$numDia];

			return $result;

		}



	function horaLocal() {
		global $base;
		$differencetolocaltime=1;

		$new_U=date("U")+$differencetolocaltime*3600;

		//$fulllocaldatetime= date("d-m-Y h:i:s A", $new_U);

		$hora= date("H:i:s", $new_U);
		
		$res=mysql_db_query($base,"SELECT NOW()");
		$row=mysql_fetch_array($res);
		
		$hora=date( "Y-m-d H:i:s" , strtotime ( "0 hour" , strtotime($row[0]) ) );
		
		$hora=date( "H:i:s" , strtotime ( "0 minute" , strtotime($hora) ) );

		return $hora;

		//Regards. Mohammed Ahmad. MSN: m@maaking.com

	}
	
	function fechaLocal(){
		global $base;
		$differencetolocaltime=1;

		$new_U=date("U")+$differencetolocaltime*3600;

		//$fulllocaldatetime= date("d-m-Y h:i:s A", $new_U);

		//$fecha= date("Y-m-d", $new_U);
		
		$res=mysql_db_query($base,"SELECT NOW()");
		$row=mysql_fetch_array($res);
		
		$fecha=date( "Y-m-d H:i:s" , strtotime ( "0 hour" , strtotime($row[0]) ) );
		
		$fecha=date( "Y-m-d" , strtotime ( "0 minute" , strtotime($fecha) ) );

		return $fecha;
	}
	
	function fechahoraLocal(){
		global $base;
		$differencetolocaltime=1;

		$new_U=date("U")+$differencetolocaltime*3600;

		//$fulllocaldatetime= date("d-m-Y h:i:s A", $new_U);

		//fechahora= date("Y-m-d H:i:s", $new_U);
		
		$res=mysql_db_query($base,"SELECT NOW()");
		$row=mysql_fetch_array($res);
		
		$fechahora=date( "Y-m-d H:i:s" , strtotime ( "0 hour" , strtotime($row[0]) ) );
		
		$fechahora=date( "Y-m-d H:i:s" , strtotime ( "0 minute" , strtotime($fechahora) ) );

		return $fechahora;
	}

	function fecha_letra($fecha){
		$fecven=split("-",$fecha);
		$fecha_letra=$fecven[2]." de ";;
		switch($fecven[1]){
			case "01":$fecha_letra.="Enero";break;
			case "02":$fecha_letra.="Febrero";break;
			case "03":$fecha_letra.="Marzo";break;
			case "04":$fecha_letra.="Abril";break;
			case "05":$fecha_letra.="Mayo";break;
			case "06":$fecha_letra.="Junio";break;
			case "07":$fecha_letra.="Julio";break;
			case "08":$fecha_letra.="Agosto";break;
			case "09":$fecha_letra.="Septiembre";break;
			case "10":$fecha_letra.="Octubre";break;
			case "11":$fecha_letra.="Noviembre";break;
			case "12":$fecha_letra.="Diciembre";break;
		}
		$fecha_letra.=" del ".$fecven[0]."";
		return $fecha_letra;
	}

	function edad($rfc){
		$anio=intval("19".substr($rfc,4,2));
		$mes=intval(substr($rfc,6,2));
		$dia=intval(substr($rfc,8,2));
		
		$anio2=intval(substr(fechaLocal(),0,4));
		$mes2=intval(substr(fechaLocal(),5,2));
		$dia2=intval(substr(fechaLocal(),8,2));
		
		$edad=$anio2-$anio;
		if($mes2<$mes){
			$edad--;
		}
		elseif($mes2==$mes){
			if($dia2<$dia){
				$edad--;
			}
		}
		return $edad;
	}
	
	function fechaNormal($fecha){
		$arrFecha=explode("-",$fecha);
		return $arrFecha[2].'/'.$arrFecha[1].'/'.$arrFecha[0];
	}

	function calculaCondonacion($unidad,$fecha_ini,$fecha_fin){
		global $base;
		$fecha=$fecha_ini;
		$totcondonacion=0;
		while($fecha<=$fecha_fin){
			$res=mysql_db_query($base,"SELECT SUM(importe) FROM parque_condonacion WHERE unidad='$unidad' AND fecha_ini<='$fecha' AND fecha_fin>='$fecha' AND sta='0'");
			$row=mysql_fetch_array($res);
			$totcondonacion+=$row[0];
			$fecha=date( "Y-m-d" , strtotime ( "+ 1 day" , strtotime($fecha) ) );
		}
		return $totcondonacion;
	}

?>