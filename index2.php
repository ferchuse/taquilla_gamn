<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: NEXTLALPAN ::</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
</head>

<body>
<p>&nbsp;</p>
<form id="forma" name="forma" method="POST" action="tickets.php">
  <table width="530" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#F1F3F5" class="loginTable">
    <tr>
      <td height="50" colspan="2" background="images/bannertop-bg.png">&nbsp;</td>
    </tr>
    <tr>
      <td width="50%"><div align="center">
          <p>&nbsp;</p>
        <table width="70%" border="0">
            <tr>
              <td><div align="center"><img src="images/logingif.gif?<?php echo date("Y-m-d");?>" width="69" height="69" /></div></td>
            </tr>
            <tr>
              <td class="bodyText"><div align="center">&iexcl; Bienvenido a NEXTLALPAN ! </div></td>
            </tr>
            <tr>
              <td class="bodyText"><div align="center"></div></td>
            </tr>
            <tr>
              <td class="bodyText"><div align="center">Introduzca su usuario y password </div></td>
            </tr>
          </table>
        <p>&nbsp;</p>
      </div></td>
      <td width="50%"><div align="center">
          <table width="90%" border="0" class="loginInnerTable">
            <tr>
              <td colspan="5"><img src="images/login.gif" width="80" height="36" /></td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td class="bodyTextBold">Usuario</td>
              <td><input name="loginUser" type="text" class="textField" id="loginUser" /></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td class="bodyTextBold">Password</td>
              <td><input name="loginPassword" type="password" class="textField" id="loginPassword" /></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td><div align="center">
                  <input name="Submit" type="submit" class="appDefButton" value="Login" />
              </div></td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
<?php
		if($_GET['ErrLogUs']) {
			echo '<tr><th colspan="5"><font color="RED">Usuario y/o Password Incorrectos !</font></th></tr>';
		}

?>
            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
            </tr>
          </table>
	  </div></td>
    </tr>
  </table>
</form>
</body>
</html>