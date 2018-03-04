<?
if (strlen($_SESSION["AdminUser"])==0) {
	redirect($mainroot."index.php");
}

//si el usuario tiene permiso para la pantalla, si no avisa y va a la inicial
if (trim(permiso($pPantalla))=="")
{
	die(aviso_accion("No tiene permiso para ingresar a esta pantalla. Debe solicitarlo al administrador del sistema. ",$mainroot."index.php")); 
}
?>