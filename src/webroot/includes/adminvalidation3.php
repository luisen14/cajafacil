<?
if (strlen($_SESSION["AdminUser"])==0) {
	redirect($mainroot."index.php");
}

//si el usuario tiene permiso para la pantalla, si no avisa y va a la inicial
if (trim(permiso($pPantalla))=="")
{
	redirect($mainroot."index.php"); 
}
?>