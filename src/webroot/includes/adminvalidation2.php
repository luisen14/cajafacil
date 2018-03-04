<?
//si el usuario tiene permiso para la pantalla, si no avisa y va a la inicial
if (trim(permiso($pPantalla))=="")
{
	aviso("No tiene permiso para ingresar a esta pantalla. Debe solicitarlo al administrador del sistema."); 
	echo "<script>window.close();</script>";
}
?>