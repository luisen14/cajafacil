<?
	$server="localhost";
	$user="wwwcajaf_web";
	$pass="gDva%af@t(kG";
    $dblink = mysql_connect ($server, $user, $pass) or die ("No se ha podido conectar a la base de datos. Detalles: " . mysql_error());
    $database="wwwcajaf_local";	
    mysql_select_db ($database);
    $color="1";
    $icono=5;
   if (strlen($_SESSION["AdminUser"])!=0) {
	 	$SQL_query = "SELECT fkEmpresa FROM usuarios WHERE ID='".$_SESSION["AdminUser"]."'  ";
		//print "<P>".$SQL_query;		
		$xCatalogRS = mysql_query($SQL_query) ;					
		if ($xCatalogRow = mysql_fetch_array($xCatalogRS)) {
			$database1="wwwcajaf_".$xCatalogRow["fkEmpresa"];
			$file="dbconnect_".$xCatalogRow["fkEmpresa"].".php";
			if (file_exists($mainroot."includes/".$file)) {
				$database=$database1;
				mysql_select_db ($database);
				//echo $database;				
			}
			//tomar el color de la empresa para cambiar los estilos
				$SQL_query2 = "SELECT fkColor,fkIcono FROM empresa WHERE ID='1'  ";
				//print "<P>".$SQL_query2;
				$xCatalogRS2 = mysql_query($SQL_query2) ;					
				if ($xCatalogRow2 = mysql_fetch_array($xCatalogRS2)) {			
					$color=$xCatalogRow2["fkColor"];	
					$icono=$xCatalogRow2["fkIcono"];	
				}
		}	
	}    
    mysql_set_charset("latin1_spanish_ci",$dblink); 
    mysql_query("SET NAMES 'iso-8859-1'");	
    date_default_timezone_set("America/Mexico_City");
?>
