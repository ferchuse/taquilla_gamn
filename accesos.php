<?php 

include ("main.php"); 
		
/*** ELIMINAR REGISTRO  **************************************************/

if ($_POST['cmd']==3) {
	$delete= "UPDATE usuarios SET estatus='I' WHERE cve='".$_POST['reg']."' ";
	$ejecutar=mysql_db_query($base,$delete);
	header("Location: accesos.php");
}

/*** ACTUALIZAR REGISTRO  **************************************************/

if ($_POST['cmd']==2) {
	$nombre=$_POST['nombre'];
	$usuario=$_POST['usuario'];
	$pass=$_POST['pass'];
	$tipo=$_POST['tipo'];
	if($_POST['reg']) {
			//Actualizar el Registro
			$update = " UPDATE usuarios 
						SET 
						  nombre='$nombre',
						  usuario='$usuario',
						  pass='$pass',
						  tipo='$tipo'
						WHERE cve='".$_POST['reg']."' " ;
			$ejecutar = mysql_db_query($base,$update);
	} else {
			//Insertar el Registro
			$insert = " INSERT INTO usuarios 
						( nombre,
						  usuario,
						  pass,
						  tipo)
						VALUES 
						( '$nombre',
						  '$usuario',
						  '$pass',
						  '$tipo')
						";
			$ejecutar = mysql_db_query($base,$insert) or die(mysql_error());
	}
	header("Location: accesos.php");
	
}


/*** CONSULTA AJAX  **************************************************/

if($_POST['ajax']==1) {
	
		//Listado de tecnicos y administradores
		$select= " SELECT * FROM usuarios WHERE 1 ";
		if ($_POST['nom']!="") { $select.=" AND nombre LIKE '%".$_POST['nom']."%' "; }
		$rsusuarios=mysql_db_query($base,$select);
		$totalRegistros = mysql_num_rows($rsusuarios);
		$select .= " ORDER BY nombre";
		$rsusuarios=mysql_db_query($base,$select);
		
		if(mysql_num_rows($rsusuarios)>0) 
		{
			echo '<table width="100%" border="0" cellpadding="4" cellspacing="1" class="">';
		echo '<tr><td bgcolor="#E9F2F8" colspan="3">'.mysql_num_rows($rsusuarios).' Registro(s)</td></tr>';
			echo '<tr bgcolor="#E9F2F8"><th>Accesos</th><th>Nombre</th></tr>';
			$x=0;
			while($Usuario=mysql_fetch_array($rsusuarios)) {
				rowb();
				if($Usuario['cve']==1 && $_SESSION['CveUsuario']!=1)
					echo '<td align="center" width="40" nowrap>&nbsp;</td>';
				else
					echo '<td align="center" width="40" nowrap><a href="#" onClick="atcr(\'\',\'\',\'1\','.$Usuario['cve'].')"><img src="images/key.png" border="0" title="Editar '.$Usuario['nombre'].'"></a></td>';
				$extra="";
				if($Usuario['estatus']=="I")
					$extra=" (INACTIVO)";
				echo '<td>'.htmlentities($Usuario['nombre']).$extra.'</td>';
				/*if($Usuario['cve']==1)
					echo '<td align="center" width="40" nowrap>&nbsp;</td>';
				else
					echo '<td align="center" width="40" nowrap><a href="#" onClick="borrar('.$Usuario['cve'].')"><img src="images/basura.gif" border="0" title="Borrar '.$Usuario['nombre'].'"></a></td>';
				*/
				echo '</tr>';
				$x++;
			}
			echo '	
				<tr>
				<td colspan="3" bgcolor="#E9F2F8">'.$x.' Registro(s)</td>
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

	if ($_POST['cmd']==1) {
		
		$select=" SELECT * FROM usuarios WHERE cve='".$_POST['reg']."' ";
		$rssuario=mysql_db_query($base,$select);
		$Usuario=mysql_fetch_array($rssuario);
		//Menu
		echo '<table>';
		echo '
			<tr>';
		echo '<td><a href="#" onClick="atcr(\'accesos.php\',\'\',\'2\',\''.$Usuario['cve'].'\');"><img src="images/guardar.gif" border="0">&nbsp;Guardar</a></td><td>&nbsp;</td>';
		echo '
			<td><a href="#" onClick="atcr(\'accesos.php\',\'\',\'0\',\'0\');"><img src="images/flecha-izquierda.gif" border="0">&nbsp;Volver</a></td><td>&nbsp;</td>
			</tr>';
		echo '</table>';
		echo '<br>';
		
		//Formulario 
		echo '<table>';
		echo '<tr><td class="tableEnc">Edicion Usuarios</td></tr>';
		echo '</table>';

		//Formulario 
		echo '<table>';
		echo '<tr><th>Nombre</th><td><input type="text" name="nombre" id="nombre" value="'.$Usuario['nombre'].'" size="40" class="textField"></td></tr>';
		echo '<tr><th>Usuario</th><td><input type="text" name="usuario" id="usuario" value="'.$Usuario['usuario'].'" class="textField"></td></tr>';
		echo '<tr><th>Password</th><td><input type="password" name="pass" id="pass" value="'.$Usuario['pass'].'" class="textField"></td></tr>';
		echo '<tr><th>Supervisor</th><td><input type="checkbox" name="tipo" value="1"';
		if($Usuario['tipo']=='1') echo ' checked';
		echo '></td></tr>';
		echo '</table>';
		
	}

/*** PAGINA PRINCIPAL **************************************************/

	if ($_POST['cmd']<1) {
		
		//Busqueda
		echo '<table>';
		echo '<tr>
				<td><a href="#" onclick="buscarRegistros();"><img src="images/buscar.gif" border="0"></a>&nbsp;&nbsp;Buscar</td><td>&nbsp;</td>
				<td><a href="#" onClick="atcr(\'accesos.php\',\'\',\'1\',\'0\');"><img src="images/nuevo.gif" border="0"></a>&nbsp;Nuevo</td><td>&nbsp;</td>
			 </tr>';
		echo '</table>';
		
		echo '<table>';
		echo '<tr><td>Nombre</td><td><input type="text" name="nom" id="nom" size="20" class="textField"></td></tr>';		
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
			objeto.open("POST","accesos.php",true);
			objeto.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
			objeto.send("ajax=1&nom="+document.getElementById("nom").value+"&numeroPagina="+document.getElementById("numeroPagina").value);
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
	}	
	
	window.onload = function () {
	    buscarRegistros(); //Realizar consulta de todos los registros al iniciar la forma.
	}
	</Script>
';

?>

