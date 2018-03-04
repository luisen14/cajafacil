<?
#=== VERIFICA SI ESTA MAQUINA TIENE PRICER INSTALADO
function estaPricerInstalado() {
    $nFKCaja = intval($_SESSION["fkCaja"]);
    $SQL_query = "SELECT 1 FROM configcaja WHERE fkCaja='".$nFKCaja."' AND etiquetas=1";
    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    return (mysql_num_rows($xCatalogRS)==0?false:true);
}
#=== VERIFICA SI ESTA MAQUINA ES LA CONEXION CON BASCULAS
function estaBASCULA() {
    $nFKCaja = intval($_SESSION["fkCaja"]);
    $SQL_query = "SELECT 1 FROM configcaja WHERE fkCaja='".$nFKCaja."' AND sincbasculas=1";
    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    return (mysql_num_rows($xCatalogRS)==0?false:true);
}
#=== VERIFICA SI ESTA MAQUINA ES LA de tickets de basculas
function estaTicketsE() {
    $nFKCaja = intval($_SESSION["fkCaja"]);
    $SQL_query = "SELECT 1 FROM configcaja WHERE fkCaja='".$nFKCaja."' AND ticketsbasculas=1";
    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    return (mysql_num_rows($xCatalogRS)==0?false:true);
}
#=== VERIFICA SI ESTA MAQUINA imprime pedidos 
function estaPedidos() {
    $nFKCaja = intval($_SESSION["fkCaja"]);
    $SQL_query = "SELECT 1 FROM configcaja WHERE fkCaja='".$nFKCaja."' AND pedidosimprime=1";
    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    return (mysql_num_rows($xCatalogRS)==0?false:true);
}

#=== VERIFICA SI ESTA MAQUINA descagra facturas 
function estaFacturas() {
    $nFKCaja = intval($_SESSION["fkCaja"]);
    $SQL_query = "SELECT 1 FROM configcaja WHERE fkCaja='".$nFKCaja."' AND facturas=1";
    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    return (mysql_num_rows($xCatalogRS)==0?false:true);
}
#=== VERIFICA que pac utiliza 
function PAC() {
	 $sfkPAC="";
	 //toma la sucurasl de la caja
	 $sfkSucursal=$_SESSION["FkSucursalCaja"];
	 if(trim($sfkSucursal)=="")//si noe s cja toma la sucursal del usuario
	 	$sfkSucursal=$_SESSION["Sucursal"];
	 if(trim($sfkSucursal)=="" || trim($sfkSucursal)=="0")	//si el usuario tiene asignada la sucursal todas, toma la priemra sucursal
	 	$sfkSucursal="1";
		
   $SQL_query = "SELECT ifnull(fkPAC,0) fkPAC FROM sucursales where ID='".$sfkSucursal."' limit 0,1";
    //print "<P>".$SQL_query;
    $xCatalogRS = mysql_query($SQL_query) ;
    if ($xCatalogRow = mysql_fetch_array($xCatalogRS)) {	
	  $sfkPAC=$xCatalogRow["fkPAC"];
	}
	return $sfkPAC;
}
#=== VERIFICA SI ES caja
function esCaja() {
    return (intval($_SESSION["fkCaja"])==0?false:true);
}

#=== INSERTA NUEVO TICKET Y DEVUELVE ID Y NUMERO
function PV_generaTicket() {
    global $nFKCliente;
    global $nFKCaja;
    global $nUsuarioID;
        
    #--- info de último ticket...
	//SOFIA: [NUMERO] Cambio a tomar el folio de la caja correspondiente y actualizarlo
        $nTicketNum=0;
     
	/*$SQL_query = "SELECT FolioInicial as NumeroMax FROM cajas WHERE ID='".$nFKCaja."'";
        //print "<P>".$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
        if ($xCatalogRow = mysql_fetch_array($xCatalogRS)) {
           $nTicketNum=intval($xCatalogRow["NumeroMax"]);
        }*/
    	
    #--- Inserta nuevo ticket...
        $SQL_query = "INSERT INTO tickets (Numero, Abierto, Fecha, fkUsuario, fkCaja, Hora, fkCliente) "
                . "VALUES ('', 1, CURDATE(), '".$nUsuarioID."','".$nFKCaja."', CURTIME(), '".$nFKCliente."')";
        #print "<P>".$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
        $nTicketID=  mysql_insert_id();
       
	    $nTicketNum++;
 /* #--- actualiza numero de ticket en caja..
        $SQL_query = "UPDATE cajas SET FolioInicial='".$nTicketNum."' WHERE ID='".$nFKCaja."'";
        #print "<P>".$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error()); */  
        
    #--- Regresa ID y Número de ticket...
    return array(intval($nTicketID),intval($nTicketNum));
}


?>