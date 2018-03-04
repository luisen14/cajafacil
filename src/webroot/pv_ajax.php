<? session_start(); 
//id de atributo de la pantalla para checar permiso en adminvalidation
$pPantalla=1200;
 include("includes/dbconnect.php");  
 include("includes/util.php"); 
 include_once("clases/producto.php"); 
 include("includes/adminvalidation.php"); 

$sEntidad=$_POST["Entidad"];
$nTicketID = intval($_POST["TicketID"]);

if ($sEntidad=="CancelarVenta") {
	#--- limpia información del ticket....
	$SQL_query = "DELETE FROM pagos WHERE fkTicket='".$nTicketID."'";
   // $sError=$SQL_query;
    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    $SQL_query = "DELETE FROM tickets WHERE ID='".$nTicketID."'";
    //$sError=$SQL_query;    
	$xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
	
	 #--- actualiza numero de ticket en caja..
        $SQL_query = "UPDATE cajas SET FolioInicial=FolioInicial-1 WHERE ID='".$nFKCaja."'";
        #print "<P>".$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());	
    #=== SE OLVIDA DEL TICKET ACTUAL
        $_SESSION["TicketID"]='';
        $_SESSION["TicketNum"]='';    
        $_SESSION["fkCliente"]='';
		$_SESSION["Comentario"]='';
  $aResultado["Error"] = "";
    print json_encode($aResultado);
}

if ($sEntidad=="ProductoAumenta") {
    $nfkCodigo = intval($_POST["fkCodigo"]);
    $nFKCliente = intval($_SESSION["fkCliente"]);   // Público General
    $nFKCliente = ($nFKCliente==0?1:$nFKCliente);
	// MULTIPLE: LEE SI ES CODIGO DE BARRAS MULTIPLE, AUMENTA la cantidad del multiplicador cada vez
	$SQL_query1 = "SELECT Multiplicador FROM codigosbarra WHERE Tipo='Multiple' and codigosbarra.ID='".$nfkCodigo."'";
    #print "<P>".$SQL_query;
    $xProdsRS1 = mysql_query($SQL_query1) or die (mysql_error());
    if ($xProdsRow1 = mysql_fetch_array($xProdsRS1))  
        $nMultiplicador=floatval($xProdsRow1["Multiplicador"]);
	  else$nMultiplicador=1;
    mysql_free_result($xProdsRS1);
	
	// FORZAR: Si en configuracion pdv esta seleccionado forzar a renglon nuevo, inserta cada vez
	 $SQL_query2 = "SELECT ifnull(ForceRowPerEqualProduct,0) ForceRowPerEqualProduct FROM configpdv WHERE fkCaja='".$_SESSION["fkCaja"]."'";
    #print "<P>".$SQL_query;
    $xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
    if ($xProdsRow2 = mysql_fetch_array($xProdsRS2)) { 
	$sForceRowPerEqualProduct=$xProdsRow2["ForceRowPerEqualProduct"];
	}
    
    if ($sForceRowPerEqualProduct=="0" ) { 
    $SQL_query = "UPDATE productosticket "
            . " SET Cantidad=Cantidad+1*'".$nMultiplicador."' "
            . " WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$nfkCodigo."'";
	}else{
		// FORZAR:SI esta seleccionado forzar renglon inserta de nuevo
		$SQL_query2 = "SELECT * FROM productosticket WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$nfkCodigo."'";
   		 #print "<P>".$SQL_query;
   		$xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
    	if ($xProdsRow2 = mysql_fetch_array($xProdsRS2) ) { 
		
		$SQL_query = "INSERT INTO productosticket (fkTicket, fkProducto, fkCodigo, PrecioUnitario, Cantidad, 
                   PorcIVA, montoSImp, montoTotal, montoIVA, 
                   PorcImp2, montoIMP2, 
                   PorcImp3, montoIMP3, 
                   PorcImp4, montoIMP4, 
                   PorcImp5, montoIMP5, 
                   fkListaVenta, precioLista1, listaVenta,
                   costoMax, costoUltimo, costoPromedio,
                   hora) 
                 VALUES ('".$nTicketID."', '".$xProdsRow2["fkProducto"]."', '".$xProdsRow2["fkCodigo"]."', '".$xProdsRow2["PrecioUnitario"]."', '1', 
                 '".$xProdsRow2["PorcIVA"]."', '".$xProdsRow2["montoSImp"]."', '".$xProdsRow2["montoTotal"]."', '".$xProdsRow2["montoIVA"]."',
                 '".$xProdsRow2["PorcImp2"]."', '".$xProdsRow2["montoIMP2"]."',
                 '".$xProdsRow2["PorcImp3"]."', '".$xProdsRow2["montoIMP3"]."',
                 '".$xProdsRow2["PorcImp4"]."', '".$xProdsRow2["montoIMP4"]."',
                 '".$xProdsRow2["PorcImp5"]."', '".$xProdsRow2["montoIMP5"]."',
                 '".$xProdsRow2[" fkListaVenta"]."', '".$xProdsRow2["precioLista1"]."', '".$xProdsRow2["listaVenta"]."' ,
                 '".$xProdsRow2["costoMax"]."', '".$xProdsRow2["costoUltimo"]."', '".$xProdsRow2["costoPromedio"]."' ,
                 CURRENT_TIMESTAMP()) ";
		}
	 }
    #print "<P>".$SQL_query;
    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    
    #--- lee la nueva cantidad...
    $SQL_query = "SELECT Cantidad FROM productosticket WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$nfkCodigo."'";
    #print "<P>".$SQL_query;
    $xProdsRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xProdsRow = mysql_fetch_array($xProdsRS)) { 
        $nCantidad=floatval($xProdsRow["Cantidad"]);
    }
    mysql_free_result($xProdsRS);
    $xProd = new Producto();
    $xProd->leeProductoPorCampoForaneo("codigosbarra","ID", $nfkCodigo);    
 	// Si ya el adminitrador cambio el precio, ya no lo actualiza
		$SQL_query2 = "SELECT costoPromIMP2 FROM productosticket WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$nfkCodigo."'";
   		 #print "<P>".$SQL_query;
   		$xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
    	if ($xProdsRow2 = mysql_fetch_array($xProdsRS2) ) { 
			$scostoPromIMP2=$xProdsRow2["costoPromIMP2"];
		}
		if(intval($scostoPromIMP2)!=1)
		    $sError=$xProd->actualizaPrecio($nFKCliente, $nCantidad);
    
    recalculaImpuestos();
    $sEntidad="listaProductos";
}

if ($sEntidad=="ProductoDisminuye") {
    $nfkCodigo = intval($_POST["fkCodigo"]);
    $nFKCliente = intval($_SESSION["fkCliente"]);   // Público General
    $nFKCliente = ($nFKCliente==0?1:$nFKCliente);
    // MULTIPLE:LEE SI ES CODIGO DE BARRAS MULTIPLE, AUMENTA la cantidad del multiplicador cada vez
	$SQL_query1 = "SELECT Multiplicador FROM codigosbarra WHERE Tipo='Multiple' and codigosbarra.ID='".$nfkCodigo."'";
    #print "<P>".$SQL_query;
    $xProdsRS1 = mysql_query($SQL_query1) or die (mysql_error());
    if ($xProdsRow1 = mysql_fetch_array($xProdsRS1)) { 
        $nMultiplicador=floatval($xProdsRow1["Multiplicador"]);
    }else{$nMultiplicador=1;}
    mysql_free_result($xProdsRS1);
	
    $SQL_query = "SELECT Cantidad FROM productosticket WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$nfkCodigo."'";
    #print "<P>".$SQL_query;
    $xProdsRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xProdsRow = mysql_fetch_array($xProdsRS)) {         
        if ($xProdsRow["Cantidad"]<=1) {
            $SQL_query = "DELETE FROM productosticket WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$nfkCodigo."' Limit 1; ";
            #print "<P>".$SQL_query;
            $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());            
        } else {
            $SQL_query = "UPDATE productosticket "
            . " SET Cantidad=Cantidad-1 *'".$nMultiplicador."' "
            . " WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$nfkCodigo."'";
            #print "<P>".$SQL_query;
            $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
            
            $xProd = new Producto();
            $xProd->leeProductoPorCampoForaneo("codigosbarra","ID", $nfkCodigo);
			// Si ya el adminitrador cambio el precio, ya no lo actualiza
		$SQL_query2 = "SELECT costoPromIMP2 FROM productosticket WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$nfkCodigo."'";
   		 #print "<P>".$SQL_query;
   		$xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
    	if ($xProdsRow2 = mysql_fetch_array($xProdsRS2) ) { 
			$scostoPromIMP2=$xProdsRow2["costoPromIMP2"];
		}
		if(intval($scostoPromIMP2)!=1)    
            $xProd->actualizaPrecio($nFKCliente, $xProdsRow["Cantidad"]-1);            
            recalculaImpuestos();            
        }
    }    	
    $sEntidad="listaProductos";
}

if ($sEntidad=="ProductoCambia") {
    $nRenSeleccionado = intval($_POST["nRenSeleccionado"]);
    $nFKCliente = intval($_SESSION["fkCliente"]);   // Público General
    $nFKCliente = ($nFKCliente==0?1:$nFKCliente);
    $nCantidad  =floatval($_POST["Cantidad"]);   
        if ($nCantidad<=0) {
            $SQL_query = "DELETE FROM productosticket WHERE fkTicket='".$nTicketID."' AND ID='".$nRenSeleccionado."' Limit 1; ";
            #print "<P>".$SQL_query;
            $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());            
        } else {
            $SQL_query = "UPDATE productosticket "
            . " SET Cantidad='".$nCantidad."' "
            . " WHERE fkTicket='".$nTicketID."' AND ID='".$nRenSeleccionado."'";
           // print "<P>".$SQL_query;
            $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
            $SQL_queryb = "SELECT fkCodigo FROM productosticket WHERE fkTicket='".$nTicketID."' AND ID='".$nRenSeleccionado."'";
   			// print "<P>".$SQL_queryb;
   			$xProdsRSb = mysql_query($SQL_queryb) or die (mysql_error());
	    	if ($xProdsRowb = mysql_fetch_array($xProdsRSb) )  
				$nfkCodigo=$xProdsRowb["fkCodigo"];
            $xProd = new Producto();
            $xProd->leeProductoPorCampoForaneo("codigosbarra","ID", $nfkCodigo); 
			//Si ya el adminitrador cambio el precio, ya no lo actualiza
		$SQL_query2 = "SELECT costoPromIMP2 FROM productosticket WHERE fkTicket='".$nTicketID."' AND ID='".$nRenSeleccionado."'";
   		//print "<P>".$SQL_query2;
   		$xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
    	if ($xProdsRow2 = mysql_fetch_array($xProdsRS2) ) { 
			$scostoPromIMP2=$xProdsRow2["costoPromIMP2"];
		}
		if(intval($scostoPromIMP2)!=1)   
              $sError=$xProd->actualizaPrecio($nFKCliente,  $nCantidad);            
            recalculaImpuestos();            
        }
    $sEntidad="listaProductos";
}

if ($sEntidad=="ProductoElimina") {
    $nRenSeleccionado = intval($_POST["renglon"]);
	$sError="";
    $SQL_query = "DELETE FROM productosticket WHERE  fkTicket='".$nTicketID."' and ID='".$nRenSeleccionado."'";
   // $sError=$SQL_query;
    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    $aResultado["Error"] = $sError;
    print json_encode($aResultado);
}

if ($sEntidad=="BorrarTicketE") {
 $nTicketID = intval($_POST["TicketID"]);
      #--- toma el numero de ticket electronico usado , si lo hay lo borra...
    $SQL_query = "select distinct(fkTicketE) fkTicketE FROM productosticket WHERE fkTicket='".$nTicketID."'";
    $sError=$SQL_query;
    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
	while ( $xCatalogRow = mysql_fetch_array( $xCatalogRS)) { 
		//borrar archivos temporales
		$nFKSucursal = intval($_SESSION["FkSucursalCaja"]);
		$nfkTicketE=$xCatalogRow["fkTicketE"];
		if(is_file('ticketbas/'.$nFKSucursal."_".$nfkTicketE))
			unlink('ticketbas/'.$nFKSucursal."_".$nfkTicketE); // delete file
		else if(is_file('ticketbas/'.$nfkTicketE))
			unlink('ticketbas/'.$nFKSucursal."_".$nfkTicketE); // delete file		
		if(is_file('ticketbas/'.$nfkTicketE))
			unlink('ticketbas/'.$nfkTicketE); // delete file	
		else if	(is_file('ticketbas/'.$nFKSucursal."_".$nfkTicketE))
			unlink('ticketbas/'.$nFKSucursal."_".$nfkTicketE); // delete file
	}
	 $aResultado["Error"] = $sError;
    print json_encode($aResultado);
}
if ($sEntidad=="BorrarTicketE2") {
 $nTicketID =$_POST["TicketID"];
 $sError=$nTicketID;
 		//borrar archivos temporales
		if(is_file($nTicketID ))
			unlink($nTicketID ); // delete file	
	 $aResultado["Error"] = $sError;
    print json_encode($aResultado);
}
if ($sEntidad=="ProductoObtenPorCodigo") {
    $nProdCodigoID = intval($_POST["ProdCodigoID"]);    
    if ($nProdCodigoID==0) {
        $aResultado["Error"] = "";
        $aResultado["NombreCorto"]="";
    } else {
	 //obtiene la sucursal de la caja, y solo muestra los productos de dicha sucursal	 
	$SQL_query1 = "SELECT FkSucursal FROM cajas WHERE ID='".$_SESSION["fkCaja"]."'";
    #print "<P>".$SQL_query;
    $xProdsRS1 = mysql_query($SQL_query1) or die (mysql_error());
    if ($xProdsRow1 = mysql_fetch_array($xProdsRS1)) { 
      if($xProdsRow1["FkSucursal"]!="0")
  			$ssucursal=" and (productos.FkSucursal='".$xProdsRow1["FkSucursal"]."' or  productos.FkSucursal='0')";
	  else
			$ssucursal="";  
    }else $ssucursal=""; 
    mysql_free_result($xProdsRS1);
$ssucursal="";
        $SQL_query = "SELECT NombreCorto FROM productos p INNER JOIN codigosbarra cb ON (p.ID=cb.fkProducto) WHERE cb.ID='".$nProdCodigoID."' ".$ssucursal." ";
        #print "<P>".$SQL_query;
        $xProdRS = mysql_query($SQL_query) or die (mysql_error());
        if ($xProdRow = mysql_fetch_array($xProdRS)) { 
            $aResultado["NombreCorto"] = $xProdRow["NombreCorto"];
        }
        mysql_free_result($xProdRS);        
    }
    print json_encode($aResultado);   
}

if ($sEntidad=="ProductoGuardaPrecio") {
    $sError="";
    $nfkCodigo = intval($_POST["fkCodigo"]);
    $nPrecioUnitario = floatval($_POST["PrecioUnitario"]);
    if ($nfkCodigo==0) exit;

    $sError=Costo($nfkCodigo, $nPrecioUnitario);    
    if (trim($sError)=="") {
        $SQL_query = "UPDATE productosticket "
                . " SET hora=CURRENT_TIMESTAMP(), PrecioUnitario='".$nPrecioUnitario."', costoPromImp2=1"
                . " WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$nfkCodigo."'";
        #print "<P>".$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());        
        recalculaImpuestos();
    }       
    $aResultado["Error"] = $sError;
    print json_encode($aResultado);
}

function recalculaImpuestos() {
    $nTicketID=intval($_SESSION["TicketID"]);
    $SQL_query = "SELECT pt.ID, pt.PrecioUnitario, pt.Cantidad, pt.PorcIVA, pt.PorcImp2, pt.PorcImp3, pt.PorcImp4, pt.PorcImp5, p.exentoiva
             FROM productosticket pt 
             INNER JOIN productos p ON (pt.fkProducto=p.ID) 
             WHERE fkTicket='".$nTicketID."' ORDER BY hora ASC";
    #print "<P>".$SQL_query;
    $xProdsRS = mysql_query($SQL_query) or die (mysql_error());
    while ($xProdsRow = mysql_fetch_array($xProdsRS)) {        
        #--- Calcula impuestos:
            $nPorcIVA=$xProdsRow["PorcIVA"];
            $nMonto = floatval($xProdsRow["PrecioUnitario"]*$xProdsRow["Cantidad"]);
            $nMontoSinImpuestos = $nMonto / (1 + floatval($xProdsRow["PorcIVA"])/100 + floatval($xProdsRow["PorcImp2"])/100 + floatval($xProdsRow["PorcImp3"])/100 + floatval($xProdsRow["PorcImp4"])/100 + floatval($xProdsRow["PorcImp5"])/100);
            $sDebug.="\n nMontoSinImpuestos: ".$nMontoSinImpuestos;
            $nMontoIVA = $nMontoSinImpuestos * floatval($xProdsRow["PorcIVA"])/100;
            $nMontoIMP2= $nMontoSinImpuestos * floatval($xProdsRow["PorcImp2"])/100;
            $nMontoIMP3= $nMontoSinImpuestos * floatval($xProdsRow["PorcImp3"])/100;
            $nMontoIMP4= $nMontoSinImpuestos * floatval($xProdsRow["PorcImp4"])/100;
            $nMontoIMP5= $nMontoSinImpuestos * floatval($xProdsRow["PorcImp5"])/100;
            if ($xProdsRow["exentoiva"]==1) {
                $nMontoIVA=0;
                $nPorcIVA=0;
            }
        // IMPUESTOS: Agregar monto sin impuestos
        $SQL_query = "UPDATE productosticket SET
                 PorcIVA='".$nPorcIVA."', 
				 montoTotal='".floatval($nMonto)."',
				 montoSImp='".floatval($nMontoSinImpuestos)."',
                 montoIVA='".floatval($nMontoIVA)."', 
                 montoIMP2='".floatval($nMontoIMP2)."', 
                 montoIMP3='".floatval($nMontoIMP3)."', 
                 montoIMP4='".floatval($nMontoIMP4)."', 
                 montoIMP5='".floatval($nMontoIMP5)."'
                 WHERE fkTicket='".$nTicketID."' AND ID='".$xProdsRow["ID"]."'";
     //   $sDebug.="\n ".$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    }
    mysql_free_result($xProdsRS);       
    return $sDebug;    
}

if ($sEntidad=="ProductoObtenFoto") {
    $nfkCodigo = intval($_POST["fkCodigo"]);

    $SQL_query = "SELECT archivo FROM fotos f INNER JOIN codigosbarra cb ON (f.fkProducto=cb.fkProducto) WHERE cb.ID='".$nfkCodigo."' ";
    #print "<P>".$SQL_query;
    $xProdRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xProdRow = mysql_fetch_array($xProdRS)) { 
        $aResultado["archivo"] = $xProdRow["archivo"];
    }
    mysql_free_result($xProdRS);
    
    if ((file_exists("fotos/".$aResultado["archivo"])) && (is_file("fotos/".$aResultado["archivo"]))) {
        $aResultado["archivo"]="fotos/".$aResultado["archivo"];
    } else {
        $aResultado["archivo"]="images/shim.gif";
    }
        
    $aResultado["Error"] = "";
    print json_encode($aResultado);
}

if ($sEntidad=="ClienteAsigna") {
    $nFKCliente = intval($_POST["FKCliente"]);
    $_SESSION["fkCliente"]=$nFKCliente;
	$sError="";
    $SQL_query = "SELECT Nombre, fkListaPrecios, limiteCredito, diasPlazo FROM clientes WHERE ID='".$nFKCliente."' ";
    #print "<P>".$SQL_query;
    $xClienteRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xClienteRow = mysql_fetch_array($xClienteRS)) { 
        $aResultado["Nombre"] = convTexto2($xClienteRow["Nombre"]);
        $aResultado["fkListaPrecios"] = $xClienteRow["fkListaPrecios"];
        $aResultado["CreditoDisponible"] = floatval($xClienteRow["limiteCredito"]);
        $aResultado["DiasPlazo"] = intval($xClienteRow["diasPlazo"]);
        
        #--- Verifica el crédito que actualmente está usando...
        $SQL_query = "SELECT SUM(Saldo) as CreditoUsado FROM tickets WHERE fkCliente='".$nFKCliente."'";
        #print "<P>".$SQL_query;
        $xCreditoRS = mysql_query($SQL_query) or die (mysql_error());
        if ($xCreditoRow = mysql_fetch_array($xCreditoRS)) {
            $aResultado["CreditoDisponible"]=$aResultado["CreditoDisponible"]-floatval($xCreditoRow["CreditoUsado"]);
        }
        mysql_free_result($xCreditoRS);
        
        #--- Asigna el ticket al cliente
        $SQL_query = "UPDATE tickets SET fkCliente='".$nFKCliente."' WHERE ID='".$nTicketID."'";
        #print "<P>".$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    }
    mysql_free_result($xClienteRS);    
    
    #--- Publico general no tiene credito...
    if ($nFKCliente==1) $aResultado["CreditoDisponible"]=0;
    
    #--- Actualiza los precios de los productos
    $SQL_query = "SELECT * FROM productosticket pt 
             INNER JOIN codigosbarra cb ON (pt.fkCodigo=cb.ID) 
             INNER JOIN productos p ON (cb.fkProducto=p.ID) 
             WHERE fkTicket='".$nTicketID."' ORDER BY hora ASC";
   // $sError=$SQL_query;
    $xProdsRS = mysql_query($SQL_query) or die (mysql_error());
    while ($xProdsRow = mysql_fetch_array($xProdsRS)) { 
	        $xProd = new Producto();
    	    $xProd->leeProducto($xProdsRow["fkProducto"]);	
			if(intval($xProdsRow["costoPromIMP2"])!=1)
        		$xProd->actualizaPrecio($nFKCliente, $xProdsRow["Cantidad"]);     
    }
    mysql_free_result($xProdsRS);	
	recalculaImpuestos();	
	$aResultado["PromocionNombre"] = $xProd->PromocionNombre;
    print json_encode($aResultado);    
}

if ($sEntidad=="ProductoAgrega") {
    $sError="";
    $sDebug="";
    $bPidePrecio=0;
	$sWarning="";
    $sProductoCodigo = textoSimple(urldecode($_POST["Codigo"]));	
    $nCodigoBarraID = intval($_POST["CodigoBarraID"]);
    $nFKListaPrecios = $_POST["FKListaPrecios"];
    $nFKCliente = intval($_SESSION["fkCliente"]); 
    $nProductoCantidad = floatval($_POST["Cantidad"]);
    $xProd = new Producto();
    // LECTURA ETIQUETAS DE BASCULAS
		$bEtiquetas=0;
		$sticketsbasculas="0";
		 $SQL_querya = "SELECT ifnull(ClaveProdPesado,0) ClaveProdPesado, LongitudTotalClave, IndicadorProdPesado, LongitudIDVendedor, LongitudClaveProd, LongitudPeso, DecimalesPeso, ifnull(ticketsbasculas,0) ticketsbasculas FROM parametros where ID=1; ";
       // $sError=$SQL_querya;
        $xCodBarraRSa = mysql_query($SQL_querya) or die (mysql_error());
        if ($xCodBarraRowa = mysql_fetch_array($xCodBarraRSa)) {
			$bEtiquetas=$xCodBarraRowa["ClaveProdPesado"];
			$sIndicadorProdPesado=$xCodBarraRowa["IndicadorProdPesado"];
			$nLongitudIDVendedor=intval($xCodBarraRowa["LongitudIDVendedor"]);
			$nLongitudClaveProd=intval($xCodBarraRowa["LongitudClaveProd"]);
			$nLongitudPeso=intval($xCodBarraRowa["LongitudPeso"]);
			$nDecimalesPeso=intval($xCodBarraRowa["DecimalesPeso"]);
			$sticketsbasculas=$xCodBarraRowa["ticketsbasculas"];	
			$sLongitudTotalClave=$xCodBarraRowa["LongitudTotalClave"];
			if($sticketsbasculas=="1")
			{//si estan activos los tickets electronicos
				 $i=0;
				 $SQL_queryb = "SELECT ifnull(codigo,0) codigo,ifnull(precioForzado,0) precioForzado, ifnull(hasVendorOnFileName,0) hasVendorOnFileName, vendorStart, vendorLength, separador,ticketStart from elayout where Tipo='eTicket'; ";
       			//$sError.=" ".$SQL_queryb;
		        $xCodBarraRSb = mysql_query($SQL_queryb) or die (mysql_error());
        		while ($xCodBarraRowb = mysql_fetch_array($xCodBarraRSb)) {
					$acodigo[$i]=$xCodBarraRowb["codigo"];	
					$aprecioForzado[$i]=$xCodBarraRowb["precioForzado"];	
					$ahasVendorOnFileName[$i]=$xCodBarraRowb["hasVendorOnFileName"];
					$avendorStart[$i]=$xCodBarraRowb["vendorStart"];
					$avendorLength[$i]=$xCodBarraRowb["vendorLength"];
					$aseparador[$i]=$xCodBarraRowb["separador"];
					$aticketStart[$i]=$xCodBarraRowb["ticketStart"];										
					$i=$i+1;	
				}	
				//el codigo del producto trae el prefijo de algun ticket electronico?			
				$pos=-1;
				$pos=array_search(substr($sProductoCodigo, 0, 2), $acodigo);
				if(trim($pos)=="")
					$pos=-1;
			}						
		}
		$sVendor1="0";
		$_SESSION["Vendor"]=$sVendor1;
		$_SESSION["Etiquetas"]="0";
		$_SESSION["ticketse"]="0";			
		//Si tiene activado la interpretacion de codigos de barra de basculas, y los 2 primeros digitos del codigo son iguales al indicador (25 o lo capturado)
		if($bEtiquetas=="1" && (substr($sProductoCodigo, 0, 2) === $sIndicadorProdPesado) && (strlen($sProductoCodigo)>=($sLongitudTotalClave-1) ))
		{
			//los siguientes digitos son del vendedor		
			$sVendor=substr($sProductoCodigo, 2,$nLongitudIDVendedor);
			$bEtiquetas="2";
			//toma el id del vendedor
			$SQL_queryb= "SELECT ifnull(ID,0) ID FROM usuarios where UsrVendor='".$sVendor."'; ";
	        #print "<P>".$SQL_query;
        	$xCodBarraRSb = mysql_query($SQL_queryb);
        	if ($xCodBarraRowb = mysql_fetch_array($xCodBarraRSb)) {
				$sVendor1=$xCodBarraRowb["ID"];
			}			
			$_SESSION["Etiquetas"]=$bEtiquetas;
			//los siguientes del codigo de producto
			$ninicio=2+$nLongitudIDVendedor;
			$nfin=$nLongitudClaveProd;
			$sProductoCodigo2=ltrim(substr($sProductoCodigo, $ninicio, $nfin),'0');
			$xProd->leeProductoPorPLU($sProductoCodigo2);	
			//los siguientes de la cantidad (separo enteros de decimales)
			$ninicio=$ninicio+$nfin;
			$ninicio2=$ninicio;
			$nfin=$nLongitudPeso-$nDecimalesPeso;
			$nProductoCantidad=ltrim(substr($sProductoCodigo, $ninicio,$nfin),0);
			$ninicio=$ninicio+$nfin;
             if($xProd->Pesado=="1")   	
			{                         
			$nfin=$nDecimalesPeso;
			//para convertir a decimales los siguientes numeros
			$nentre=1;
			for($i=1; $i<=$nfin; $i++)
				$nentre=$nentre."0";
			$nentre=intval($nentre);
			$nProductoCantidad=$nProductoCantidad+(intval(substr($sProductoCodigo, $ninicio,$nfin))/$nentre);
			}else{
				$ninicio=$ninicio2;
				$nfin=$nLongitudPeso;
				$nProductoCantidad=ltrim(substr($sProductoCodigo, $ninicio,$nfin),0);
			}			
			$sProductoCodigo=$sProductoCodigo2;
		// LECTURA DE TICKETS ELETRONICOS DE BASCULAS, si estan activados, si viene el codigo de inciio y si es mas largo de 5 caracteres
		}else if($sticketsbasculas=="1" && $pos>=0 && (strlen($sProductoCodigo)>=12)) 
			{		
				//si trae vendedor lo toma y guarda en una session
				if($ahasVendorOnFileName[$pos]=="1")	
				{	
					$sVendor=substr($sProductoCodigo, $avendorStart[$pos]-1,$avendorLength[$pos]);
					$sDebug.=$sVendor;
					//toma el id del vendedor
					$SQL_queryb= "SELECT ifnull(ID,0) ID FROM usuarios where UsrVendor='".$sVendor."'; ";
	    		   // $sError.=" q:".$SQL_queryb;
					$xCodBarraRSb = mysql_query($SQL_queryb);
        			if ($xCodBarraRowb = mysql_fetch_array($xCodBarraRSb)) {
						$sVendor1=$xCodBarraRowb["ID"];
						//$_SESSION["Vendor"]=$sVendor1;
						$_SESSION["Etiquetas"]="2";
					}					
				}
			$sticketsbasculas="2";
			//los siguientes del numero de ticket
			$nfin=12-$aticketStart[$pos];
			$sticket2=trim(substr($sProductoCodigo, $aticketStart[$pos]-1, $nfin+1));	
			$directorio="ticketbas/";
			$ficheros1  = scandir($directorio);
			$snombre_archivo="";
			
			foreach($ficheros1 as $fichero)
			{				
				$pos2=strpos($fichero,$sticket2);			
				if($pos2===false)
				{}else{
					 $nFKSucursal = intval($_SESSION["FkSucursalCaja"]);	
					$snombre_archivo=$fichero;
				}	
			}
			if($snombre_archivo!="")
			{
			//abrir archivo de ticket
			$handle = fopen($directorio.$snombre_archivo, "r");
			if ($handle) {
				$extension = end(explode('.', $snombre_archivo));
				$i=1;
				//si no se ha agregado el ticket
				$SQL_query2 = "SELECT 1 FROM productosticket WHERE fkTicketE='".$sticket2.".".$extension."' and fkTicket='".$_SESSION["TicketID"]."';";
   				//$sError= "<P>".$SQL_query2;
				$xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
				if ($xProdsRow2 = mysql_fetch_array($xProdsRS2)) { 
					$sError.="El ticket ya ha sido agregado";
				}else{
				$r=0;
			    while (($line = fgets($handle)) !== false) {
				//separar los datos por comas					    
						$sdato=explode($aseparador[$pos],$line);
						
						if($acodigo[$pos]=="63")
						{	
						if($i>1)//si no es la primera linea
						{					
							if($sdato[0]=='"61"' && $sdato[11]!=1)//si la linea trae producto
							{	
                            	$r++;						
						        // toma el codigo del producto y le quita los ceros de la izquierda
								$sProductoCodigo2=ltrim($sdato[2],'0');
								//los siguientes de la cantidad (separo enteros de decimales)
								$nProductoCantidad=ltrim($sdato[8],'0');
								$nPrecio=ltrim($sdato[9],'0')/100;
								//para convertir a decimales los siguientes numeros
								$nProductoCantidad=($nProductoCantidad/1000);
								$sticket2b=$sticket2.".".$extension;								
								$sWarning.=ProductoAumenta($sProductoCodigo2,$nProductoCantidad,$sticket2b ,$aprecioForzado[$pos],$nPrecio, $sVendor1);
							}	
						}									
						}else{
						 $r++;						
					        // toma el codigo del producto y le quita los ceros de la izquierda
							$sProductoCodigo2=ltrim($sdato[0],'0');
							$nProductoCantidad=ltrim($sdato[2],'0');
							$nPrecio=ltrim($sdato[1],'0');
							if($sdato[4]!="1" && $sdato[4]!=1)
								$nProductoCantidad=$nProductoCantidad/1000;
								$sticket2=trim(substr($sProductoCodigo, $aticketStart[$pos]-1, $nfin+2));
							$sticket2b="0".$sticket2.".".$extension;
							if(trim($sProductoCodigo2)!="")
								$sWarning.=ProductoAumenta($sProductoCodigo2,$nProductoCantidad,$sticket2b,$aprecioForzado[$pos],$nPrecio,$sVendor1);
						}
					$i=$i+1;
				}
				//si todos estan cancelados avisar
				if($r==0)
					$sWarning.=" Todos los productos del ticket estan cancelados";
				}
    		}else{
				$sError.="Ticket no encontrado: No existe o ya fue utilizado.";
			}
		    fclose($handle);
			}//if archivo
			else{
				$sError.="Ticket no encontrado: No existe o ya fue utilizado.";
			}
		}else//si no es ticket e
		{
	 if ($nCodigoBarraID>0) {        
        #-- Busca producto, primero por codigo de barras
        $xProd->leeProductoPorCodigoBarrasID($nCodigoBarraID);        
    } else {
        #--- Determina si trae multiplicador al inicio.
        #--- dado que los caracteres 'x' y '*' no son permitidos en los códigos de barras
        #--- asumimos que de encontrarlos, son símbolos de multiplicación
        $sProductoCodigo = str_replace("x","*",$sProductoCodigo);
        $aCodigoToken = explode('*',$sProductoCodigo);
        if (count($aCodigoToken)==2) {
            if ($aCodigoToken[0]>0) {
                $nProductoCantidad=floatval($aCodigoToken[0]);
                $sProductoCodigo=$aCodigoToken[1];
            }
        }
        #-- Busca producto, primero por codigo de barras
        $xProd->leeProductoPorCodigoBarras($sProductoCodigo);
		
        #-- Busca producto, por nombre corto (en caso de no haberlo encontrado por codigo de barras)
        if ($xProd->ID=="") {
            $xProd->leeProductoPorCampo("NombreCorto",$sProductoCodigo);
			$xProd->multiplicador=1;
        }
    }
		}//else etiquetas=="1"		
	//si no viene de ticket electronico
	if($sticketsbasculas!="2"){	
    #--- obtiene el codigo, temporal porque puede cambiar cuando se cambie la cantidad de items
    $xProd->Precio=$xProd->precio($nFKCliente, $nProductoCantidad);
	$sError=$xProd->actualizaprecio($nFKCliente, $nProductoCantidad);
	$nFkCodigoTemp = $xProd->fkCodigo;
   
    #--- multiplica cantidad por multiplicador del codigo de barras (o uno si el multiplicador no ha sido especificado)
    $xProd->multiplicador=($xProd->multiplicador<1?1:$xProd->multiplicador);
    $nProductoCantidad*=$xProd->multiplicador;
   
    if ($nTicketID<=0) $sError.= "No se ha identificado el ticket.\n";
    if ($xProd->ID<=0) $sError.= "No se ha identificado el producto: ".$sProductoCodigo.".\n";
    
	// Si en configuracion pdv esta seleccionado forzar a renglon nuevo, inserta cada vez en lugar de aumentar
	 $SQL_query2 = "SELECT ifnull(ForceRowPerEqualProduct,0) ForceRowPerEqualProduct FROM configpdv WHERE fkCaja='".$_SESSION["fkCaja"]."'";
    #print "<P>".$SQL_query;
    $xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
    if ($xProdsRow2 = mysql_fetch_array($xProdsRS2)) { 
		$sForceRowPerEqualProduct=$xProdsRow2["ForceRowPerEqualProduct"];
	}
    // FRACCIONADA: TOMAR si el producto permite venta fraccionada, si  no y trae decimales pasar error y redondear
	 $SQL_query2 = "SELECT ventafraccionada FROM productos WHERE ID='".$xProd->ID."';";
    //$sError= "<P>".$SQL_query2;
    $xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
    if ($xProdsRow2 = mysql_fetch_array($xProdsRS2)) { 
		$bventafraccionada=$xProdsRow2["ventafraccionada"];
	}
	// FRACCIONADA: si el producto permite venta fraccionada
	if($bventafraccionada=="0" && fmod($nProductoCantidad, 1) != 0)
	{
		$nProductoCantidad=intval($nProductoCantidad);	
		$sError="Este producto no permite venta fraccionada";
	}

    $SQL_query = "SELECT * FROM productosticket left join productos on productos.ID=productosticket.fkProducto WHERE fkTicket='".$nTicketID."' AND fkProducto='".$xProd->ID."' AND fkCodigo='".$nFkCodigoTemp."' AND IFNULL(Vendor,'')='".$sVendor1."'";
   // $sError.=$SQL_query;
    $xProdsRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xProdsRow = mysql_fetch_array($xProdsRS)) { 
	      if($sForceRowPerEqualProduct=="0") {
	        #---la cantidad es ahora los items existentes más los nuevos
            $nProductoCantidad += floatval($xProdsRow["Cantidad"]);        

            #--- Busca el precio del producto, por la lista actual y en base a la cantidad de productos.
            $xProd->Precio=$xProd->precio($nFKCliente, $nProductoCantidad);
			$sError=$xProd->actualizaprecio($nFKCliente, $nProductoCantidad);
			$sWarning.=Costo($xProd->fkCodigo, $xProd->Precio);
           //if(trim($sError)==""){
            // GUARDAR MONTO SIN IMPUESTOS
            $nMonto = floatval($xProd->Precio*$nProductoCantidad);
            $nMontoSinImpuestos = $nMonto / (1 + floatval($xProd->porIVA)/100 + floatval($xProd->porIEPS)/100 + floatval($xProd->porSunt)/100 + floatval($xProd->porIMP4)/100 + floatval($xProd->porIMP5)/100);
            $SQL_query = "UPDATE productosticket 
                 SET Cantidad='".$nProductoCantidad."', hora=CURRENT_TIMESTAMP(), PrecioUnitario='".$xProd->Precio."',
                 PorcIVA='".$xProd->porIVA."', 
                 PorcImp2='".$xProd->porIEPS."', 
                 PorcImp3='".$xProd->porSunt."', 
                 PorcImp4='".$xProd->porIMP4."', 
                 PorcImp5='".$xProd->porIMP5."', 
				 montoTotal='".floatval($nMonto)."',
				 montoSImp='".floatval($nMontoSinImpuestos)."',
                 montoIVA='".floatval($nProductoCantidad*$xProd->montoIVA)."', 
                 montoIMP2='".floatval($nProductoCantidad*$xProd->montoIMP2)."', 
                 montoIMP3='".floatval($nProductoCantidad*$xProd->montoIMP3)."', 
                 montoIMP4='".floatval($nProductoCantidad*$xProd->montoIMP4)."', 
                 montoIMP5='".floatval($nProductoCantidad*$xProd->montoIMP5)."',                 
                    fkListaVenta='".$xProd->FkLista."', 
                    precioLista1='".$xProd->PreciLista1."', 
                    listaVenta='".$xProd->ListaVenta."', 
                        
                 fkCodigo='".$xProd->fkCodigo."' 
                 WHERE fkTicket='".$nTicketID."' AND fkProducto='".$xProd->ID."' AND fkCodigo='".$nFkCodigoTemp."'";
              //  $sError.=$SQL_query;
                $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
	} else {
			if($xProd->ID!=0 && $xProd->ID!="")
			{
            $nMonto = floatval($xProd->Precio*$nProductoCantidad);
            $nMontoSinImpuestos = $nMonto / (1 + floatval($xProd->porIVA)/100 + floatval($xProd->porIEPS)/100 + floatval($xProd->porSunt)/100 + floatval($xProd->porIMP4)/100 + floatval($xProd->porIMP5)/100);      
            $xProd->Precio=$xProd->precio($nFKCliente, $nProductoCantidad);
			$sError=$xProd->actualizaprecio($nFKCliente, $nProductoCantidad);
			$sWarning.=Costo($xProd->fkCodigo, $xProd->Precio);
			//  if(trim($sError)==""){           
	         $sDebug.="|cliente1:".$nFKCliente."|";
            $sDebug.="|precio:".$xProd->Precio."|";
            $sDebug.="|montoIVA:".$xProd->montoIVA."|";
            $SQL_query = "INSERT INTO productosticket (fkTicket, fkProducto, fkCodigo, PrecioUnitario, Cantidad, 
                   PorcIVA, montoSImp, montoTotal, montoIVA, 
                   PorcImp2, montoIMP2, 
                   PorcImp3, montoIMP3, 
                   PorcImp4, montoIMP4, 
                   PorcImp5, montoIMP5, 
                   fkListaVenta, precioLista1, listaVenta,
                   costoMax, costoUltimo, costoPromedio,
                   hora, Vendor) 
                 VALUES ('".$nTicketID."', '".$xProd->ID."', '".$xProd->fkCodigo."', '".$xProd->Precio."', '".$nProductoCantidad."', 
                 '".$xProd->porIVA."', '".floatval($nMontoSinImpuestos)."', '".floatval($nMonto)."', '".floatval($xProd->montoIVA)."',
                 '".$xProd->porIEPS."', '".floatval($xProd->montoIMP2)."',
                 '".$xProd->porSunt."', '".floatval($xProd->montoIMP3)."',
                 '".$xProd->porIMP4."', '".floatval($xProd->montoIMP4)."',
                 '".$xProd->porIMP5."', '".floatval($xProd->montoIMP5)."',
                 '".$xProd->FkLista."', '".$xProd->PreciLista1."', '".$xProd->ListaVenta."' ,
                 '".$xProd->CostoMax."', '".$xProd->CostoUltimo."', '".$xProd->CostoPromedio."' ,
                 CURRENT_TIMESTAMP(),'".$sVendor1."' ) ";
           // $sError.=$SQL_query;
            $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
			}
        }//forcerow
    } else {
		if($xProd->ID!=0 && $xProd->ID!="")
		{
        $nMonto = floatval($xProd->Precio*$nProductoCantidad);
        $nMontoSinImpuestos = $nMonto / (1 + floatval($xProd->porIVA)/100 + floatval($xProd->porIEPS)/100 + floatval($xProd->porSunt)/100 + floatval($xProd->porIMP4)/100 + floatval($xProd->porIMP5)/100);      
        $xProd->Precio=$xProd->precio($nFKCliente, $nProductoCantidad);
		$sError=$xProd->actualizaprecio($nFKCliente, $nProductoCantidad);
		//$sError=$xProd->Precio;
		$sWarning.=Costo($xProd->fkCodigo, $xProd->Precio);
		//  if(trim($sError)==""){           
        $sDebug.="|cliente2:".$nFKCliente."|";
        $sDebug.="|precio:".$xProd->Precio."|";
        $sDebug.="|montoIVA:".$xProd->montoIVA."|";
        $SQL_query = "INSERT INTO productosticket (fkTicket, fkProducto, fkCodigo, PrecioUnitario, Cantidad, 
                   PorcIVA, montoSImp, montoIVA, 
                   PorcImp2, montoIMP2, 
                   PorcImp3, montoIMP3, 
                   PorcImp4, montoIMP4, 
                   PorcImp5, montoIMP5, 
                   fkListaVenta, precioLista1, listaVenta,
                   costoMax, costoUltimo, costoPromedio,
                   hora, Vendor) 
                 VALUES ('".$nTicketID."', '".$xProd->ID."', '".$xProd->fkCodigo."', '".$xProd->Precio."', '".$nProductoCantidad."', 
                 '".$xProd->porIVA."', '".floatval($nMontoSinImpuestos)."', '".floatval($xProd->montoIVA)."',
                 '".$xProd->porIEPS."', '".floatval($xProd->montoIMP2)."',
                 '".$xProd->porSunt."', '".floatval($xProd->montoIMP3)."',
                 '".$xProd->porIMP4."', '".floatval($xProd->montoIMP4)."',
                 '".$xProd->porIMP5."', '".floatval($xProd->montoIMP5)."',
                 '".$xProd->FkLista."', '".$xProd->PreciLista1."', '".$xProd->ListaVenta."' ,
                 '".$xProd->CostoMax."', '".$xProd->CostoUltimo."', '".$xProd->CostoPromedio."' ,
                 CURRENT_TIMESTAMP(),'".$sVendor1."') ";
        //$sError.=$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
		}
    }
  
    #-- si al final no hay precio, prende bandera para pedirlo...
    #--- en dicho caso, los montos de impuestos serán cero y deben ser recalculados!
    if ($xProd->Precio<=0) $bPidePrecio=1;
	}
	 
 	$aResultado["Warning"] = $sWarning;
	$aResultado["Error"] = $sError;
    $aResultado["Debug"] = $sDebug;
    $aResultado["PidePrecio"] = $bPidePrecio;
    $aResultado["fkCodigo"] = $xProd->fkCodigo;
	$aResultado["PromocionNombre"] = $xProd->PromocionNombre;
    print json_encode($aResultado);
}
function Costo($sfkCodigo, $nPrecioUnitario) {
mysql_query("SET NAMES 'utf8'");
	$sError="";
	// COSTO:checar configuracion de pdv, si checamos costo y cual
	$SQL_query1 = "SELECT KindLimitPrice FROM configpdv WHERE fkCaja='".$_SESSION["fkCaja"]."' ";
    // $sError=$SQL_query1;
    $xProdRS1 = mysql_query($SQL_query1) or die (mysql_error());
    if ($xProdRow1 = mysql_fetch_array($xProdRS1)) { 
        $sKindLimitPrice = $xProdRow1["KindLimitPrice"];
    }
    mysql_free_result($xProdRS1);
	
	 $SQL_query = "SELECT CostoUltimo, CostoMax, CostoPromedio, cb.Codigo, substring(p.NombreLargo,1,51) NombreLargo FROM productos p INNER JOIN codigosbarra cb ON (p.ID=cb.fkProducto) WHERE cb.ID='".$sfkCodigo."' ";
   //  $sError=$SQL_query;
    $xProdRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xProdRow = mysql_fetch_array($xProdRS)) { 
        $nCostoUltimo = $xProdRow["CostoUltimo"];
		$nCostoMaximo = $xProdRow["CostoMax"];
		$nCostoPromedio = $xProdRow["CostoPromedio"];
		$sCodigo=$xProdRow["Codigo"];
		$sProducto=$xProdRow["NombreLargo"];
    }
    mysql_free_result($xProdRS);	
	
	switch($sKindLimitPrice){
	case "None": $nChecar=0;
			break;	
	case "Ult": $nChecar=1;
				$nCosto=floatval($nCostoUltimo);
			break;	
	case "Max":$nChecar=1;
				$nCosto=floatval($nCostoMaximo);
			break;	
	case "Prom":$nChecar=1;
				$nCosto=floatval($nCostoPromedio);
			break;	
	}  

    if ($nChecar==1 && $nPrecioUnitario<$nCosto && $nPrecioUnitario!=0) {
        $sError="El precio unitario no puede ser menor que el costo ($".$nCosto."). Producto: ".$sCodigo." / ".$sProducto ;
    }
	return $sError;
}
//para leer varios productos con el ticket eletronico
function ProductoAumenta($sProductoCodigo, $nProductoCantidad, $sticket2,$bprecioForzado,$nPrecio, $sVendor1) {
    $sWarning="";
    $sDebug="";
    $bPidePrecio=0;
    $nFKCliente = intval($_SESSION["fkCliente"]); 
    $xProd = new Producto();
   
	$xProd->leeProductoPorPLU($sProductoCodigo);	
	if ($xProd->ID<=0)				
		$xProd->leeProductoPorCodigoBarras($sProductoCodigo);
	
    #--- obtiene el codigo, temporal porque puede cambiar cuando se cambie la cantidad de items
    $xProd->Precio=$xProd->precio($nFKCliente, $nProductoCantidad);
    $nFkCodigoTemp = $xProd->fkCodigo;
   //si el ticket trae forzado al precio de la bascula lo cambia
    if($bprecioForzado=="1")
		$xProd->Precio=$nPrecio;
	
    #--- multiplica cantidad por multiplicador del codigo de barras (o uno si el multiplicador no ha sido especificado)
    $xProd->multiplicador=($xProd->multiplicador<1?1:$xProd->multiplicador);
    $nProductoCantidad*=$xProd->multiplicador;
    
    $nTicketID=intval($_SESSION["TicketID"]);
    if ($nTicketID<=0) $sWarning.= "No se ha identificado el ticket.\n";
    if ($xProd->ID<=0) $sWarning.= "No se ha identificado el producto: ".$sProductoCodigo.".\n";
   // Si en configuracion pdv esta seleccionado forzar a renglon nuevo, inserta cada vez en lugar de aumentar
	 $SQL_query2 = "SELECT ifnull(ForceRowPerEqualProduct,0) ForceRowPerEqualProduct FROM configpdv WHERE fkCaja='".$_SESSION["fkCaja"]."'";
    #print "<P>".$SQL_query;
    $xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
    if ($xProdsRow2 = mysql_fetch_array($xProdsRS2)) { 
	$sForceRowPerEqualProduct=$xProdsRow2["ForceRowPerEqualProduct"];
	}
	
    // FRACCIONADA: TOMAR si el producto permite venta fraccionada, si  no y trae decimales pasar error y redondear
	 $SQL_query2 = "SELECT ventafraccionada FROM productos WHERE ID='".$xProd->ID."';";
    //$sError= "<P>".$SQL_query2;
    $xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
    if ($xProdsRow2 = mysql_fetch_array($xProdsRS2)) { 
		$bventafraccionada=$xProdsRow2["ventafraccionada"];
	}
	// FRACCIONADA: si el producto permite venta fraccionada
	if($bventafraccionada=="0" && fmod($nProductoCantidad, 1) != 0)
	{
		$nProductoCantidad=intval($nProductoCantidad);	
		$sWarning.="Este producto no permite venta fraccionada: ".$sProductoCodigo.".\n";
	}

    $SQL_query = "SELECT * FROM productosticket left join productos on productos.ID=productosticket.fkProducto WHERE fkTicket='".$nTicketID."' AND fkProducto='".$xProd->ID."' AND fkCodigo='".$nFkCodigoTemp."' and IFNULL(Vendor,'')='".$sVendor1."'";
    #print "<P>".$SQL_query;
    $xProdsRS = mysql_query($SQL_query) or die (mysql_error());
  if ($xProdsRow = mysql_fetch_array($xProdsRS)) { 	
		//si no es producto pesado, trae la cantidad diferente del ticket
		if($xProdsRow["Pesado"]=="0")
			 $nProductoCantidad= $nProductoCantidad*1000;
		  if($sForceRowPerEqualProduct=="0") {			
            #---la cantidad es ahora los items existentes más los nuevos
            $nProductoCantidad += floatval($xProdsRow["Cantidad"]);        

            #--- Busca el precio del producto, por la lista actual y en base a la cantidad de productos.
            $xProd->Precio=$xProd->precio($nFKCliente, $nProductoCantidad);
			//si el ticket trae forzado al precio de la bascula lo cambia
    		if($bprecioForzado=="1")
				$xProd->Precio=$nPrecio;
			$sWarning.=Costo($xProd->fkCodigo, $xProd->Precio);
			if(trim(Costo($xProd->fkCodigo, $xProd->Precio))!="") $sWarning.=": ".$sProductoCodigo.".\n";
            // GUARDAR MONTO SIN IMPUESTOS
            $nMonto = floatval($xProd->Precio*$nProductoCantidad);
            $nMontoSinImpuestos = $nMonto / (1 + floatval($xProd->porIVA)/100 + floatval($xProd->porIEPS)/100 + floatval($xProd->porSunt)/100 + floatval($xProd->porIMP4)/100 + floatval($xProd->porIMP5)/100);
            $SQL_query = "UPDATE productosticket 
                 SET Cantidad='".$nProductoCantidad."', hora=CURRENT_TIMESTAMP(), PrecioUnitario='".$xProd->Precio."',
                 PorcIVA='".$xProd->porIVA."', 
                 PorcImp2='".$xProd->porIEPS."', 
                 PorcImp3='".$xProd->porSunt."', 
                 PorcImp4='".$xProd->porIMP4."', 
                 PorcImp5='".$xProd->porIMP5."', 
				 montoTotal='".floatval($nMonto)."',
				 montoSImp='".floatval($nMontoSinImpuestos)."',
                 montoIVA='".floatval($nProductoCantidad*$xProd->montoIVA)."', 
                 montoIMP2='".floatval($nProductoCantidad*$xProd->montoIMP2)."', 
                 montoIMP3='".floatval($nProductoCantidad*$xProd->montoIMP3)."', 
                 montoIMP4='".floatval($nProductoCantidad*$xProd->montoIMP4)."', 
                 montoIMP5='".floatval($nProductoCantidad*$xProd->montoIMP5)."', 
                 Vendor='".$sVendor1."',   
                    fkListaVenta='".$xProd->FkLista."', 
                    precioLista1='".$xProd->PreciLista1."', 
                    listaVenta='".$xProd->ListaVenta."',                         
                 fkCodigo='".$xProd->fkCodigo."', fkTicketE='".$sticket2."'
                 WHERE fkTicket='".$nTicketID."' AND fkProducto='".$xProd->ID."' AND fkCodigo='".$nFkCodigoTemp."'";
               // $sWarning.=$SQL_query;
                $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
		   //}
		} else {		
		  	if($xProd->ID!=0 && $xProd->ID!="")
			{
			 #--- Busca el precio del producto, por la lista actual y en base a la cantidad de productos.
            $xProd->Precio=$xProd->precio($nFKCliente, $nProductoCantidad);
			//si el ticket trae forzado al precio de la bascula lo cambia
    		if($bprecioForzado=="1")
				$xProd->Precio=$nPrecio;
			$sWarning.=Costo($xProd->fkCodigo, $xProd->Precio);
			if(trim(Costo($xProd->fkCodigo, $xProd->Precio))!="") $sWarning.=": ".$sProductoCodigo.".\n";
            // GUARDAR MONTO SIN IMPUESTOS
            $nMonto = floatval($xProd->Precio*$nProductoCantidad);
            $nMontoSinImpuestos = $nMonto / (1 + floatval($xProd->porIVA)/100 + floatval($xProd->porIEPS)/100 + floatval($xProd->porSunt)/100 + floatval($xProd->porIMP4)/100 + floatval($xProd->porIMP5)/100);
	       $SQL_query = "INSERT INTO productosticket (fkTicket, fkProducto, fkCodigo, PrecioUnitario, Cantidad, 
                   PorcIVA, montoSImp, montoTotal, montoIVA, 
                   PorcImp2, montoIMP2, 
                   PorcImp3, montoIMP3, 
                   PorcImp4, montoIMP4, 
                   PorcImp5, montoIMP5, 
                   fkListaVenta, precioLista1, listaVenta,
                   costoMax, costoUltimo, costoPromedio,
                   hora, fkTicketE, Vendor) 
                 VALUES ('".$nTicketID."', '".$xProd->ID."', '".$xProd->fkCodigo."', '".$xProd->Precio."', '".$nProductoCantidad."', 
                 '".$xProd->porIVA."', '".floatval($nMontoSinImpuestos)."', '".floatval($nMonto)."', '".floatval($xProd->montoIVA)."',
                 '".$xProd->porIEPS."', '".floatval($xProd->montoIMP2)."',
                 '".$xProd->porSunt."', '".floatval($xProd->montoIMP3)."',
                 '".$xProd->porIMP4."', '".floatval($xProd->montoIMP4)."',
                 '".$xProd->porIMP5."', '".floatval($xProd->montoIMP5)."',
                 '".$xProd->FkLista."', '".$xProd->PreciLista1."', '".$xProd->ListaVenta."' ,
                 '".$xProd->CostoMax."', '".$xProd->CostoUltimo."', '".$xProd->CostoPromedio."' ,
                 CURRENT_TIMESTAMP(),'".$sticket2."','".$sVendor1."')";
				//  $sWarning.=$SQL_query;
            $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());		
			}
    	}
    } else {	
		if($xProd->ID!=0 && $xProd->ID!="")
		{
		  $SQL_query1 = "SELECT Pesado FROM productos  WHERE ID='".$xProd->ID."'";
	    // $sError=$SQL_query1;
	     $xProdsRS1 = mysql_query($SQL_query1) or die (mysql_error());
    	  if ($xProdsRow1 = mysql_fetch_array($xProdsRS1)) { 	
			//si no es producto pesado, trae la cantidad diferente del ticket
			if($xProdsRow1["Pesado"]=="0")
				 $nProductoCantidad= $nProductoCantidad*1000;
			}
        $nMonto = floatval($xProd->Precio*$nProductoCantidad);
        $nMontoSinImpuestos = $nMonto / (1 + floatval($xProd->porIVA)/100 + floatval($xProd->porIEPS)/100 + floatval($xProd->porSunt)/100 + floatval($xProd->porIMP4)/100 + floatval($xProd->porIMP5)/100);      
        $xProd->Precio=$xProd->precio($nFKCliente, $nProductoCantidad);
		//si el ticket trae forzado al precio de la bascula lo cambia
	    if($bprecioForzado=="1")
			$xProd->Precio=$nPrecio;
		$sWarning.=Costo($xProd->fkCodigo, $xProd->Precio);
		if(trim(Costo($xProd->fkCodigo, $xProd->Precio))!="") $sWarning.=": ".$sProductoCodigo.".\n";
         //  if(trim($sError)==""){
        $sDebug.="|cliente2:".$nFKCliente."|";
        $sDebug.="|precio:".$xProd->Precio."|";
        $sDebug.="|montoIVA:".$xProd->montoIVA."|";
        $SQL_query = "INSERT INTO productosticket (fkTicket, fkProducto, fkCodigo, PrecioUnitario, Cantidad, 
                   PorcIVA, montoSImp, montoIVA, 
                   PorcImp2, montoIMP2, 
                   PorcImp3, montoIMP3, 
                   PorcImp4, montoIMP4, 
                   PorcImp5, montoIMP5, 
                   fkListaVenta, precioLista1, listaVenta,
                   costoMax, costoUltimo, costoPromedio,
                   hora, fkTicketE, Vendor) 
                 VALUES ('".$nTicketID."', '".$xProd->ID."', '".$xProd->fkCodigo."', '".$xProd->Precio."', '".$nProductoCantidad."', 
                 '".$xProd->porIVA."', '".floatval($nMontoSinImpuestos)."', '".floatval($xProd->montoIVA)."',
                 '".$xProd->porIEPS."', '".floatval($xProd->montoIMP2)."',
                 '".$xProd->porSunt."', '".floatval($xProd->montoIMP3)."',
                 '".$xProd->porIMP4."', '".floatval($xProd->montoIMP4)."',
                 '".$xProd->porIMP5."', '".floatval($xProd->montoIMP5)."',
                 '".$xProd->FkLista."', '".$xProd->PreciLista1."', '".$xProd->ListaVenta."' ,
                 '".$xProd->CostoMax."', '".$xProd->CostoUltimo."', '".$xProd->CostoPromedio."' ,
                 CURRENT_TIMESTAMP(),'".$sticket2."', '".$sVendor1."') ";
       // $sWarning.=$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
		 //  }
		}
    }  
    
    #-- si al final no hay precio,pone el del ticket electronico...
    #--- en dicho caso, los montos de impuestos serán cero y deben ser recalculados!
	if ($xProd->Precio<=0) $xProd->Precio=$nPrecio;
    if ($xProd->Precio<=0) $bPidePrecio=1;
	$aResultado["PromocionNombre"] = $xProd->PromocionNombre;
    return $sWarning;
}//function producto aumenta

if ($sEntidad=="PagoRealiza") {
    $nTotalPagos=0;
    $nSaldoCredito=0;
    $dHoy = date("Y-m-d");
    $sTipoTransaccion=$_POST["TipoTransaccion"];
	$sfkUsuario1=$_POST["Vendedor"];
    $nCambio=$_POST["Cambio"];
	$sError="";
    #==== RECEPCION DE PARAMETROS
        $nClienteDiasPlazo=intval($_POST["ClienteDiasPlazo"]);
        $nUsuarioAutoriza=intval($_POST["UsuarioAutoriza"]);
        $nUsuarioAutoriza=($nUsuarioAutoriza==0?"No Requirio":$nUsuarioAutoriza);
        $aParamTmp = explode("&",urldecode($_POST["data"]));
        foreach ($aParamTmp as $sParam) {
           $aKey=explode("=", $sParam);
           $key = $aKey[0];
           $value = $aKey[1];
           if (substr($key,0,8)=="SubTotal"){
               $nFkTipoPago=str_replace("SubTotal[","",$key);
               $nFkTipoPago=str_replace("]","",$nFkTipoPago);
               $aPagoPorTipo[$nFkTipoPago]=$value;
           }
           if ($key=="NumCheque") $sNumCheque=$value;
           if ($key=="Banco") $sBanco=$value;
           if ($key=="TicketTotal") $nTicketTotal=floatval(limpiaCantidad($value));
           if ($key=="TicketSaldo") $nTicketSaldo=floatval(-1*limpiaCantidad($value));
        }
                
    #=== POR CADA TIPO DE PAGO, CALCULA CONVERSIONES E INSERTA...
    foreach ($aPagoPorTipo as $fkTipoPago => $nPago) {
        $SQL_query = "SELECT * FROM tipospago WHERE ID='".$fkTipoPago."'";
        $xTipoPagoRS = mysql_query($SQL_query) or die (mysql_error());
        if ($xTipoPagoRow = mysql_fetch_array($xTipoPagoRS)) { 
            #--- Solo insertamos pagos con monto..
            if ($nPago>0) {
                $nPagoReal = floatval($nPago * $xTipoPagoRow["TipoCambio"]);                
                $nTicketTotal2 = floatval($nTicketTotal / $xTipoPagoRow["TipoCambio"]);
                $sBancoSQL="";
                $sNumChequeSQL="";
                if ($xTipoPagoRow["Nombre"]=="Cheque") {
                    $sBancoSQL=$sBanco;
                    $sNumChequeSQL=$sNumCheque;
                }                
                #--- Guarda la cantidad por tipo de pago referenciando al ticket...				  
				//si no es de cobranza en ruta inserta le pago, si no solo abono
				 if ($sTipoTransaccion!="TicketCxC")
				 {				 
					 //asegurarnos de que el pago no sea mayor que el total, parche temporal
					  $SQL_query2 = "SELECT sum(PrecioUnitario*Cantidad) Total FROM productosticket WHERE fkTicket='".$nTicketID."'";
					//   $debug.=$SQL_query2;
					  $xCatalogRS2 = mysql_query($SQL_query2) or die (mysql_error());
		        		if ($xCatalogRow2 = mysql_fetch_array($xCatalogRS2)) {
				           $nTotal=$xCatalogRow2["Total"];
						   $nTotal1=number_format($nTotal,2);
						   $nTicketTotal2=number_format($nTicketTotal2,2);
						   	if($nTotal<$nTicketTotal2)
								$nTicketTotal2=$nTotal1;
							$ValorReal=number_format($nPago-$nCambio,2);							
                       	    if($nTotal1<$ValorReal)
							{
								$nPago=$nTotal;
								$nCambio=0;
							}
        				}			    
        			$SQL_query = "INSERT INTO pagos (Valor, fkTipoPago, fkTicket, TipoCambio, PagoReal, NumCheque, Banco, Cambio) "
                        . " select '".$nTicketTotal2."', '".$fkTipoPago."', '".$nTicketID."', '".$xTipoPagoRow["TipoCambio"]."', '".$nPago."', '".$sNumChequeSQL."', '".$sBancoSQL."','0' from tickets where ID='".$nTicketID."' and ID NOT IN (select fkTicket from pagos where fkTipoPago= '".$fkTipoPago."'and fkTicket='".$nTicketID."') limit 0,1";						 
				}	
             //  $debug.=$SQL_query;
			    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
          //  $debug.=$nPago." ".$nCambio;
				//GUARDAR EL CAMBIO en algun tipo de pago usado que si acepte cambio			
        if ($xTipoPagoRow["ExactPay"]=="2" and $nCambio!=0) {
			$SQL_query2 = "update pagos set cambio='".$nCambio."' where fkTicket='".$nTicketID."' and fkTipoPago='".$fkTipoPago."'";
			$xCatalogRS2 = mysql_query($SQL_query2) or die (mysql_error());			
			$nCambio=0;
		}                
                #--- actualiza el contenido a caja, agregando el valor del tipo pagado...
                $SQL_query = "INSERT INTO contenidocaja (fkCaja, fkTipoPago, Valor) "
                        . " VALUES ('".intval($_SESSION["fkCaja"])."', '".$fkTipoPago."', '".$nPago."') "
                        . " ON DUPLICATE KEY UPDATE Valor=Valor+".$nPago;
                #print "\n3: ".$SQL_query;
                $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());                
                #--- va sumando los diferentes tipos de pago...
                $nTotalPagos+=$nPagoReal;
            }                        
            #--- crédito lo usaremos para saldo en tabla tickets.
            if ($fkTipoPago==6)  
                $nSaldoCredito=$nPago;
        }
        mysql_free_result($xTipoPagoRS);
    }   
    #--- regresa cambio
        if (floatval($nTicketSaldo)>0) {
            $SQL_query = "INSERT INTO contenidocaja (fkCaja, fkTipoPago, Valor) "
                    . " VALUES ('".intval($_SESSION["fkCaja"])."', '1', '".floatval(0-$nTicketSaldo)."') "
                    . " ON DUPLICATE KEY UPDATE Valor=Valor-".$nTicketSaldo;
            #print "\n4: ".$SQL_query;
            $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());                
        }                
    #=== ACTUALIZA LOS DATOS DEL TICKET
        $dFechaVence=date('Y-m-d', strtotime($dHoy.' + '.$nClienteDiasPlazo.' days'));
        if ($sTipoTransaccion=="Ticket") { 
			$SQL_query1 = "SELECT FolioInicial as NumeroMax FROM cajas WHERE ID='".$_SESSION["fkCaja"]."'";
     		 //  $sError.=$SQL_query1;
		        $xCatalogRS1 = mysql_query($SQL_query1) or die (mysql_error());
        		if ($xCatalogRow1 = mysql_fetch_array($xCatalogRS1)) {
		           $nTicketNum=intval($xCatalogRow1["NumeroMax"]);
        		}
				 $nTicketNum2= $nTicketNum+1;
				 #--- actualiza numero de ticket en caja..
        $SQL_query2 = "UPDATE cajas SET FolioInicial='".$nTicketNum2."' WHERE ID='".$_SESSION["fkCaja"]."'";
      // $sError.=$SQL_query2;
        $xCatalogRS2 = mysql_query($SQL_query2) or die (mysql_error());   
            $SQL_query = "UPDATE tickets SET Pagado=1, Abierto=0, Total='".$nTicketTotal."', Saldo='".$nSaldoCredito."', Autorizo='".$nUsuarioAutoriza."' ";
                $SQL_query.= " , fechaVence='".$dFechaVence."', Numero='".$nTicketNum."' ";                
                $SQL_query.= " WHERE ID='".$nTicketID."'";
        } else if ($sTipoTransaccion=="TicketCxC") { 
            $nTotalPagos=floatval($nTotalPagos);
            $SQL_query = "UPDATE tickets 
                    SET Saldo = IF (Saldo-".$nTotalPagos.">0,Saldo-".$nTotalPagos.",0) ";
                $SQL_query.= " WHERE ID='".$nTicketID."'";
        }
        #print "\n5: ".$SQL_query;
        $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
    
    #==== AFECTA INVENTARIOS Y MOVIMIENTOS
        #---solo para tickets normales, no para ticketscxc
        if ($sTipoTransaccion=="Ticket") { 
            $nFKAlmacen=0;
            $SQL_query = "SELECT fkAlmacen FROM cajas WHERE ID='".$_SESSION["fkCaja"]."'";
            #print "\n6: ".$SQL_query;
            $xCajaRS = mysql_query($SQL_query) or die (mysql_error());
            if ($xCajaRow = mysql_fetch_array($xCajaRS))  
                $nFKAlmacen=intval($xCajaRow["fkAlmacen"]);
            mysql_free_result($xCajaRS);

            #--- inserta encabezado de afectainventarios (un registro por dia)        
                $nFKAfectaInventarios=0;
                $SQL_query = "SELECT ID FROM afectacioninventarios WHERE Referencia='Ventas del dia ".fechahumana($dHoy)."' AND fkAlmacen='".$nFKAlmacen."'";
                #print "\n7: ".$SQL_query;
                $xAfectaInventariosRS = mysql_query($SQL_query) or die (mysql_error());
                if ($xAfectaInventariosRow = mysql_fetch_array($xAfectaInventariosRS)) { 
                    $nFKAfectaInventarios=$xAfectaInventariosRow["ID"];
                } else {
                    $SQL_query = "INSERT INTO afectacioninventarios (Fecha, fkAlmacen, Referencia, FkUsuario, Tipo)
                         VALUES (CURDATE(),'".$nFKAlmacen."','Ventas del dia ".fechahumana($dHoy)."', '".$_SESSION["AdminUser"]."','Salida')";
                    #print "\n8:".$SQL_query;
                    $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
                    $nFKAfectaInventarios=intval(mysql_insert_id());            
                }
                mysql_free_result($xAfectaInventariosRS);
              //si el cliente es otra sucursal agregar la entrada aL almacen a dicha sucursal
               $nFKAfectaInventarios1=0;
                $SQL_query1 = "SELECT ifnull(essucursal,0) essucursal,almacenes.ID Almacen FROM clientes, almacenes WHERE clientes.ID='".$_SESSION["fkCliente"]."' and almacenes.FkSucursal=ifnull(essucursal,0) order by almacenes.ID limit 0,1";
                //$debug=$SQL_query1;
                $xAfectaInventariosRS1 = mysql_query($SQL_query1) or die (mysql_error());
                if ($xAfectaInventariosRow1 = mysql_fetch_array($xAfectaInventariosRS1)) { 
                    if($xAfectaInventariosRow1["essucursal"]!="0")
                    {
                    $SQL_query1 = "INSERT INTO afectacioninventarios (Fecha, fkAlmacen, Referencia, FkUsuario, Tipo, autorizada)
                         VALUES (CURDATE(),'".$xAfectaInventariosRow1["Almacen"]."','Entrada Automatica por compra a Sucursal', '".$_SESSION["AdminUser"]."','Entrada','".$nFKAfectaInventarios."')";
                    //$debug.=$SQL_query;
                    $xCatalogRS = mysql_query($SQL_query1) or die (mysql_error());
                    $nFKAfectaInventarios1=intval(mysql_insert_id());               
                    }
                }
                mysql_free_result($xAfectaInventariosRS1);
	
				//COMISIONES: Tomar como se manejan las comisiones de conficaja	
				$SQL_query2 = "SELECT ifnull(BehaveVendor,0) BehaveVendor, fkUsuario FROM configcaja WHERE fkCaja='".$_SESSION["fkCaja"]."'";
			    #print "<P>".$SQL_query;
			    $xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
			    if ($xProdsRow2 = mysql_fetch_array($xProdsRS2)) { 
					$sBehaveVendor=$xProdsRow2["BehaveVendor"];
					$sfkVendor=$xProdsRow2["fkUsuario"];
				}	
				switch($sBehaveVendor)
				{
					case "Segun Usuario": 							
						$sfkUsuario=$_SESSION["AdminUser"];
					break;
					case "Preseleccionado":
						$sfkUsuario=$sfkVendor;
					break;
					case "Forzar Seleccion":
						$sfkUsuario=$sfkUsuario1;
					break;
				}				
		
            #--- inserta cada producto a afectainventarios y actualiza existencias
            $SQL_query = "SELECT *, pt.ID IDROW FROM productosticket pt 
                INNER JOIN productos p ON (pt.fkProducto=p.ID) 
                INNER JOIN codigosbarra cb ON (pt.fkCodigo=cb.ID) 
                WHERE fkTicket='".$nTicketID."'";
            #print "\n9:".$SQL_query;
            $xProdsRS = mysql_query($SQL_query) or die (mysql_error());
            while ($xProdsRow = mysql_fetch_array($xProdsRS)) { 
                $nExistencia=0;
				$nCostoUltimo = $xProdsRow["CostoUltimo"];
				$nCostoMaximo = $xProdsRow["CostoMax"];
				$nCostoPromedio = $xProdsRow["CostoPromedio"];
				$nComision2=$xProdsRow["Comision"];//comision por producto
				$nmontoSImp=$xProdsRow["montoSImp"];
				//LECTURA ETIQUETAS DE BASCULAS: si es una etiqueta o ticket de bascula , poner comision al vededor de la etiqueta
				if(trim($xProdsRow["Vendor"])!="0" && trim($xProdsRow["Vendor"])!=""  )
					$sfkUsuario2="";
				else
					$sfkUsuario2=", Vendor='".$sfkUsuario."' ";
				$SQL_query2 = "SELECT Comision, tipoComision FROM usuarios WHERE ID='".$sfkUsuario."'";
				#print "<P>".$SQL_query;
			    $xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
			    if ($xProdsRow2 = mysql_fetch_array($xProdsRS2)) { 
					$stipoComision=$xProdsRow2["tipoComision"];
					$nComision1=floatval($xProdsRow2["Comision"]);//comision por usuario
				}								
                #--- la cantidad de productos en el ticket ya fue afectada por el multiplicador del código
                $nCantidadAfectaExistencia=floatval($xProdsRow["Cantidad"]);

                #--- modifica la cantidad según el rendimiento especificado en productos...
                $nCantidadAfectaExistencia=floatval($xProdsRow["Cantidad"])*(100/floatval($xProdsRow["Rendimiento"]));
				// SI ES PRODUCTO COMPUESTO Y YA NO HAY EXISTENCIA DEL COMPUESTO, RESTA LOS INDIVIDUALES
				  $SQL_query2 = "SELECT Existencia, Compuesto, DetID , cantidad, codigosbarra.ID IDC FROM  existencias left join productos on productos.ID=existencias.fkProducto left join prodcompxproddetalle on prodcompxproddetalle.compId=productos.ID left join codigosbarra on codigosbarra.fkProducto=DetID and codigosbarra.Tipo='Principal'   WHERE existencias.fkProducto='".$xProdsRow["fkProducto"]."' AND existencias.fkAlmacen='".$nFKAlmacen."'";
            	#print "\n9:".$SQL_query2;
           		$xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
				$i=0;
            	while($xProdsRow2 = mysql_fetch_array($xProdsRS2)) { 		
					if($xProdsRow2["Compuesto"]=="1" )
					{
						$nExistencia=$xProdsRow2["Existencia"];
						$nCantidadAfectaExistencia2=($nCantidadAfectaExistencia*$xProdsRow2["cantidad"]);
						$nExistencia2=floatval($nExistencia)+floatval($nCantidadAfectaExistencia2);
						if($nExistencia<=0)
						{						
							 $SQL_query = "UPDATE existencias SET Existencia=Existencia-".$nCantidadAfectaExistencia2."
    	                	 WHERE fkProducto='".$xProdsRow2["DetID"]."' AND fkAlmacen='".$nFKAlmacen."'";
							  $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
 							#--- obtiene nueva existencia
             			  $SQL_query = "SELECT Existencia FROM existencias WHERE fkProducto='".$xProdsRow2["DetID"]."' AND fkAlmacen='".$nFKAlmacen."'";
			                #print "\n11: ".$SQL_query;
            			   $xExistenciasRS = mysql_query($SQL_query) or die (mysql_error());
			                while ($xExistenciasRow = mysql_fetch_array($xExistenciasRS)) { 
            			        $nExistencian=floatval($xExistenciasRow["Existencia"]);
			                }//inserta movimiento de almacen de armado de compuesto
							if($i==0)//si es el primer producto, resta la existencia del producto compuesto
							{
								 $SQL_query = "INSERT INTO afectacioninventarios (Fecha, fkAlmacen, Referencia, FkUsuario, Tipo)
    	                    	 VALUES (CURDATE(),'".$nFKAlmacen."','Armar compuestos', '".$_SESSION["AdminUser"]."','Salida');";
        	           			// print "\n8:".$SQL_query;
				               $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
            			       $nFKAfectaInventarios2=intval(mysql_insert_id());   
							   
							    $SQL_query = "INSERT INTO afectacioninventarios (Fecha, fkAlmacen, Referencia, FkUsuario, Tipo)
    	                    	 VALUES (CURDATE(),'".$nFKAlmacen."','Armar compuestos', '".$_SESSION["AdminUser"]."','Entrada');";
        	           			// print "\n8:".$SQL_query;
				               $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
            			       $nFKAfectaInventarios3=intval(mysql_insert_id());            
							   //inserta la entrada del producto compuesto
							   	$SQL_query = "INSERT INTO productosafectainventario (Fecha, Tipo, FKAfectaInventarios, FKProducto, Cantidad, Costo, Codigo, Existencias)
                             VALUES (NOW(),'Entrada','".$nFKAfectaInventarios3."','".$xProdsRow["fkProducto"]."', '".$nCantidadAfectaExistencia2."', '".$nCostoUltimo."', '".$xProdsRow["fkCodigo"]."', '".$nExistencia2."');";
                       		//print "\n14: ".$SQL_query;
                       		 $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
							}
							//inseta la salida del rpoducto sencillo
							$SQL_query = "INSERT INTO productosafectainventario (Fecha, Tipo, FKAfectaInventarios, FKProducto, Cantidad, Costo, Codigo, Existencias)
                             VALUES (NOW(),'Salida','".$nFKAfectaInventarios2."','".$xProdsRow2["DetID"]."', '".$nCantidadAfectaExistencia2."', '".$nCostoUltimo."', '".$xProdsRow["IDC"]."', '".$nExistencian."');";
                       		//print "\n14: ".$SQL_query;
                       		 $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
						
						}else if($nExistencia<$nCantidadAfectaExistencia){
							if($i==0)//si es el primer producto, resta la existencia del producto compuesto
							{
								$SQL_query = "UPDATE existencias SET Existencia=0
		                     WHERE fkProducto='".$xProdsRow["fkProducto"]."' AND fkAlmacen='".$nFKAlmacen."'";	
							  $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
							  	
									#--- obtiene nueva existencia
             			  $SQL_query = "SELECT Existencia FROM existencias WHERE fkProducto='".$xProdsRow2["DetID"]."' AND fkAlmacen='".$nFKAlmacen."'";
			                #print "\n11: ".$SQL_query;
            			   $xExistenciasRS = mysql_query($SQL_query) or die (mysql_error());
			                while ($xExistenciasRow = mysql_fetch_array($xExistenciasRS)) { 
            			        $nExistencian=floatval($xExistenciasRow["Existencia"]);
			                }//inserta movimiento de almacen de armado de compuesto
							
								 $SQL_query = "INSERT INTO afectacioninventarios (Fecha, fkAlmacen, Referencia, FkUsuario, Tipo)
    	                    	 VALUES (CURDATE(),'".$nFKAlmacen."','Armar compuestos', '".$_SESSION["AdminUser"]."','Salida');";
        	           			// print "\n8:".$SQL_query;
				               $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
            			       $nFKAfectaInventarios2=intval(mysql_insert_id());   
							   
							    $SQL_query = "INSERT INTO afectacioninventarios (Fecha, fkAlmacen, Referencia, FkUsuario, Tipo)
    	                    	 VALUES (CURDATE(),'".$nFKAlmacen."','Armar compuestos', '".$_SESSION["AdminUser"]."','Entrada');";
        	           			// print "\n8:".$SQL_query;
				               $xCatalogRS = mysql_query($SQL_query) or die (mysql_error());
            			       $nFKAfectaInventarios3=intval(mysql_insert_id());            
							   //inserta la entrada del producto compuesto
							   	$SQL_query = "INSERT INTO productosafectainventario (Fecha, Tipo, FKAfectaInventarios, FKProducto, Cantidad, Costo, Codigo, Existencias)
                             VALUES (NOW(),'Entrada','".$nFKAfectaInventarios3."','".$xProdsRow["fkProducto"]."', '".($nCantidadAfectaExistencia2-($nExistencia*$xProdsRow2["cantidad"]))."', '".$nCostoUltimo."', '".$xProdsRow["fkCodigo"]."', '".($nExistencia+($nCantidadAfectaExistencia2-($nExistencia*$xProdsRow2["cantidad"])))."');";
                       		//print "\n14: ".$SQL_query;
                       		 $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
							}//lo demas al detalle
							//inseta la salida del rpoducto sencillo
							$SQL_query = "INSERT INTO productosafectainventario (Fecha, Tipo, FKAfectaInventarios, FKProducto, Cantidad, Costo, Codigo, Existencias)
                             VALUES (NOW(),'Salida','".$nFKAfectaInventarios2."','".$xProdsRow2["DetID"]."', '".($nCantidadAfectaExistencia2-($nExistencia*$xProdsRow2["cantidad"]))."', '".$nCostoUltimo."', '".$xProdsRow["IDC"]."', '".($nExistencian-($nCantidadAfectaExistencia2-($nExistencia*$xProdsRow2["cantidad"])) )."');";
                       		//print "\n14: ".$SQL_query;
                       		 $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
							 $SQL_query = "UPDATE existencias SET Existencia=Existencia-".($nCantidadAfectaExistencia2-($nExistencia*$xProdsRow2["cantidad"]))."
    	                	 WHERE fkProducto='".$xProdsRow2["DetID"]."' AND fkAlmacen='".$nFKAlmacen."'";
							  $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
						}else{		
							if($i==0)//si es el primer producto, resta la existencia del producto compuesto
							{					
						 		$SQL_query = "UPDATE existencias SET Existencia=Existencia-".$nCantidadAfectaExistencia."
		    	                 WHERE fkProducto='".$xProdsRow["fkProducto"]."' AND fkAlmacen='".$nFKAlmacen."'";	
								  $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
							}
						}
					}else{
					 $SQL_query = "UPDATE existencias SET Existencia=Existencia-".$nCantidadAfectaExistencia."
                     WHERE fkProducto='".$xProdsRow["fkProducto"]."' AND fkAlmacen='".$nFKAlmacen."'";	
					  $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
					}
					$i=$i+1;				
				}              
                #print "\n10: ".$SQL_query;
               
                #--- obtiene nueva existencia
                $SQL_query = "SELECT Existencia FROM existencias WHERE fkProducto='".$xProdsRow["fkProducto"]."' AND fkAlmacen='".$nFKAlmacen."'";
                #print "\n11: ".$SQL_query;
                $xExistenciasRS = mysql_query($SQL_query) or die (mysql_error());
                while ($xExistenciasRow = mysql_fetch_array($xExistenciasRS)) { 
                    $nExistencia=floatval($xExistenciasRow["Existencia"]);
                }
                mysql_free_result($xExistenciasRS);

				 // checar configuracion de pdv CUAL costo se guarda
				$SQL_query1 = "SELECT KindLimitPrice FROM configpdv WHERE fkCaja='".$_SESSION["fkCaja"]."' ";
			    #print "<P>".$SQL_query;
		    	$xProdRS1 = mysql_query($SQL_query1) or die (mysql_error());
			    if ($xProdRow1 = mysql_fetch_array($xProdRS1)) { 
    	    		$sKindLimitPrice = $xProdRow1["KindLimitPrice"];
			    }
		    	mysql_free_result($xProdRS1);
	
				switch($sKindLimitPrice){			
					case "Ult": $nCosto=floatval($nCostoUltimo);
					break;	
					case "Max": $nCosto=floatval($nCostoMaximo);
					break;	
					case "Prom": $nCosto=floatval($nCostoPromedio);
					break;	
					default:  $nCosto=floatval($nCostoUltimo);
					break;
				}	  
			// para guardar costo sin impuesto en afectacion de inventarios y los montos de los impuestos no los porcentajes: 17/08/2015
			 $nCostoSinImpuestos = floatval($nCosto) / (1 + floatval($xProdsRow["PorcIVA"])/100 + floatval($xProdsRow["PorcImp2"])/100 + floatval($xProdsRow["PorcImp3"])/100 + floatval($xProdsRow["PorcImp4"])/100 + floatval($xProdsRow["PorcImp5"])/100);
            $nMontoIVA = $nCostoSinImpuestos * floatval($xProdsRow["PorcIVA"])/100;
            $nMontoIMP2= $nCostoSinImpuestos * floatval($xProdsRow["PorcImp2"])/100;
            $nMontoIMP3= $nCostoSinImpuestos * floatval($xProdsRow["PorcImp3"])/100;
            $nMontoIMP4= $nCostoSinImpuestos * floatval($xProdsRow["PorcImp4"])/100;
            $nMontoIMP5= $nCostoSinImpuestos * floatval($xProdsRow["PorcImp5"])/100;
			
			// COMISION:
			switch($stipoComision)
			{
				case "Comision por Producto": $nComision=$nComision2;
				break;
				case "Comision Fija":$nComision=$nComision1;
				break;
				default:$nComision=0;
				break;
			}
				$nTotalComision=($nComision/100)*$nmontoSImp;
				  $SQL_query3 = "UPDATE productosticket 
                            SET comision='".$nTotalComision."' ".$sfkUsuario2."
                               WHERE ID='".$xProdsRow["IDROW"]."'";
                 //$debug.=$SQL_query3;
                 $xResultsRS3 = mysql_query($SQL_query3) or die (mysql_error());
						
                #--- inserta renglón de productosafectainventarios (un registro por dia por producto)        
                    $SQL_query = "SELECT ID FROM productosafectainventario WHERE date(Fecha)='".$dHoy."' AND fkProducto='".$xProdsRow["fkProducto"]."' AND FKAfectaInventarios='".$nFKAfectaInventarios."' AND fkAfectaInventarios='".$nFKAfectaInventarios."'";
                    #print "\n12: ".$SQL_query;
                    $xProdAfectaInventariosRS = mysql_query($SQL_query) or die (mysql_error());
                    if ($xProdAfectaInventariosRow = mysql_fetch_array($xProdAfectaInventariosRS)) { 
                        $SQL_query = "UPDATE productosafectainventario 
                            SET Costo='".floatval($nCosto)."', 
                                Tipo='Salida',
                                Cantidad=Cantidad+".floatval($nCantidadAfectaExistencia).", 
                                Existencias='".floatval($nExistencia)."', 
                                CostoSImp='".$nCostoSinImpuestos."',
								Fecha=NOW() WHERE ID='".$xProdAfectaInventariosRow["ID"]."'";
                        #print "\n13: ".$SQL_query;
                        $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
                    } else {
                        $SQL_query = "INSERT INTO productosafectainventario (Fecha, Tipo, FKAfectaInventarios, FKProducto, Cantidad, Costo, Codigo, Existencias, CostoSImp, costoMax, costoUltimo, iiva, iieps, iimp3, iimp4, iimp5)
                             VALUES (NOW(),'Salida','".$nFKAfectaInventarios."','".$xProdsRow["fkProducto"]."', '".$nCantidadAfectaExistencia."', '".$nCosto."', '".$xProdsRow["Codigo"]."', '".$nExistencia."', '".$nCostoSinImpuestos."', '".$xProdsRow["CostoMax"]."', '".$xProdsRow["CostoUltimo"]."', '".$nMontoIVA."', '".$nMontoIMP2."', '".$nMontoIMP3."', '".$nMontoIMP4."', '".$nMontoIMP5."')";
                        #print "\n14: ".$SQL_query;
                        $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
                    }
                    mysql_free_result($xProdAfectaInventariosRS);
             //si fue venta a otra sucursal, inserta la entrada a la otra sucursal
            if($nFKAfectaInventarios1!="0")
            {
            $SQL_query = "INSERT INTO productosafectainventario (Fecha, Tipo, FKAfectaInventarios, FKProducto, Cantidad, Costo, Codigo, Existencias, CostoSImp, costoMax, costoUltimo, iiva, iieps, iimp3, iimp4, iimp5)
                             VALUES (NOW(),'Entrada','".$nFKAfectaInventarios1."','".$xProdsRow["fkProducto"]."', '".$nCantidadAfectaExistencia."', '".$nCosto."', '".$xProdsRow["Codigo"]."', '".$nExistencia."', '".$nCostoSinImpuestos."', '".$xProdsRow["CostoMax"]."', '".$xProdsRow["CostoUltimo"]."', '".$nMontoIVA."', '".$nMontoIMP2."', '".$nMontoIMP3."', '".$nMontoIMP4."', '".$nMontoIMP5."')";
                       // $debug.=$SQL_query;
                        $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
             }  
            }
            mysql_free_result($xProdsRS);
            
        }  // if ($sTipoTransaccion=="Ticket") {         
    #=== SE OLVIDA DEL TICKET ACTUAL
        $_SESSION["TicketID"]='';
        $_SESSION["TicketNum"]='';
        $_SESSION["fkCliente"]='';
		$_SESSION["Comentario"]='';
}
# LIMITES: TOMAR EL CONTENIDO DE CAJA en efectivo Y REGRESARLO
if ($sEntidad=="Limites") {
	 $SQL_query2 = "SELECT Valor FROM contenidocaja WHERE fkTipoPago!='6' and fkCaja='".$_SESSION["fkCaja"]."'; ";
       // $sError="1 ".$SQL_query2;
        #print "\n--> nPago: ".$nPago;
        $xTipoPagoRS2 = mysql_query($SQL_query2) or die (mysql_error());
        if ($xTipoPagoRow2 = mysql_fetch_array($xTipoPagoRS2)) { 
			 print $xTipoPagoRow2["Valor"];
		}
	 $aResultado["Error"] = $sError;
    $aResultado["Autoriza"] = $nAutorizaID;
    print json_encode($aResultado);	
}
#--- si se requiere autorizacion del administrador para credito, aqui se hace
if ($sEntidad=="CreditoLoginVerifica") {
    $nAutorizaID=0;
    $sError = "Login";
    $sLogin = $_POST["login"];
    $sPassword = $_POST["password"];
    $pFKAtributo = $_POST["FKAtributo"];
    
    //encripta el password					
    $pass_encriptada1 = md5 ($sPassword); //Encriptacion nivel 1
    $pass_encriptada2 = crc32($pass_encriptada1); //Encriptacion nivel 1
    $pass_encriptada3 = crypt($pass_encriptada2, "xtemp"); //Encriptacion nivel 2
    $pass_encriptada4 = sha1("xtemp".$pass_encriptada3); //Encriptacion nivel 3

    //echo $pass_encriptada4;
    $SQL_query = "SELECT ID FROM usuarios WHERE Login='".$sLogin."' and Pass='".$pass_encriptada4 ."' and activo=1;";
    //print "<P>".$SQL_query;
    $xUsuarioRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xUsuarioRow = mysql_fetch_array($xUsuarioRS)) {
        $SQL_query = "SELECT 1 FROM atributosusuarios WHERE fkUsuario='".$xUsuarioRow["ID"]."' and fkAtributo='".$pFKAtributo."'";
        #print "<P>".$SQL_query;
        $xPermisoRS = mysql_query($SQL_query) or die (mysql_error());
        if ($xPermisoRow = mysql_fetch_array($xPermisoRS)) {
            $sError="";
            $nAutorizaID=$xUsuarioRow["ID"];
        } else {
            $sError="Permiso";
        }
        mysql_free_result($xPermisoRS);
    }
    mysql_free_result($xUsuarioRS);

    $aResultado["Error"] = $sError;
    $aResultado["Autoriza"] = $nAutorizaID;
    print json_encode($aResultado);
}
#--- si se requiere autorizacion del administrador para credito, aqui se hace
if ($sEntidad=="PublicidadLoginVerifica") {
    $nAutorizaID=0;
    $sError = "Login";
    $sPassword = $_POST["password"];
	$sPass=date("Y")+date("m")+date("d");
	$sPass="rycsa_".$sPass;
    
    if ($sPassword==$sPass) {
	   $sError = "";
    }
    $aResultado["Error"] = $sError;
    $aResultado["Autoriza"] = $nAutorizaID;
    print json_encode($aResultado);
}

if ($sEntidad=="SuspendeVenta") {
    $SQL_query = "SELECT max(Suspendido) as maxSuspendido FROM tickets";
    #print "\n".$SQL_query;
    $xTSuspRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xTSuspRow = mysql_fetch_array($xTSuspRS)) { 
        $nMax=$xTSuspRow["maxSuspendido"];
    }
    mysql_free_result($xTSuspRS);
    $nMax=intval($nMax)+1;

    
    $SQL_query = "UPDATE tickets SET Suspendido='".$nMax."' 
             WHERE ID='".$nTicketID."'";
    #print "<P>".$SQL_query;
    $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
    
    #=== SE OLVIDA DEL TICKET ACTUAL
        $_SESSION["TicketID"]='';
        $_SESSION["TicketNum"]='';    
        $_SESSION["fkCliente"]='';
		$_SESSION["Comentario"]='';
    $aResultado["Error"] = "";
    print json_encode($aResultado);
}
// SUSPENDER
if ($sEntidad=="TicketsSuspenderTexto") {
    $sComentario=$_POST["Comentario"];
    $SQL_query = "UPDATE tickets SET Comentario='".$sComentario."' 
             WHERE ID='".$nTicketID."'";
    #print "<P>".$SQL_query;
    $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
    
    $aResultado["Error"] = "";
    print json_encode($aResultado);
}

if ($sEntidad=="TicketSuspendidoReanuda") {
    $nTicketReanudar = intval($_POST["TicketReanudar"]);
    $bFusionar=($_POST["Fusionar"]=="true"?true:false);
    $sError="";
    $nTicketNum=0;
	$sPrecios1 =$_POST["Precios1"];
    $SQL_query = "SELECT * FROM tickets
             WHERE ID='".$nTicketReanudar."' AND Suspendido>0 ORDER BY ID DESC";
  //  $sError.=$SQL_query;
    $xTSuspRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xTSuspRow = mysql_fetch_array($xTSuspRS)) { 
        $sComentario=$xTSuspRow["Comentario"];
        #--- si no fusiona, borra los productos que hay
        if (!$bFusionar) {
		 $SQL_query = "DELETE FROM productosticket WHERE fkTicket='".$nTicketID."' ";
		 $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
         mysql_free_result($xResultsRS);
		}
            $SQL_query = "UPDATE tickets
                SET Suspendido=0, fkCliente='".$xTSuspRow["fkCliente"]."', Mensaje='Reanudado' , Total='".$xTSuspRow["Total"]."', IVA='".$xTSuspRow["IVA"]."', 
				Imp2='".$xTSuspRow["Imp2"]."',Imp3='".$xTSuspRow["Imp3"]."',Imp4='".$xTSuspRow["Imp4"]."',Imp5='".$xTSuspRow["Imp5"]."',Comentario='".$sComentario."'
                WHERE ID='".$nTicketID."'";            
            $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
            
            $SQL_query = "UPDATE tickets
                SET Suspendido=0 , Mensaje='Suspendido'
                WHERE ID='".$nTicketReanudar."'";
            #print "\n".$SQL_query;
            $xResultsRS = mysql_query($SQL_query) or die (mysql_error());            
            $SQL_query = "SELECT * FROM productosticket 
                     WHERE fkTicket='".$nTicketReanudar."' ORDER BY ID ASC";
            //$sError.=$SQL_query;
            $xProdSuspRS = mysql_query($SQL_query) or die (mysql_error());
            while ($xProdSuspRow = mysql_fetch_array($xProdSuspRS)) { 

                $SQL_query = "SELECT * FROM productosticket 
                 WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$xProdSuspRow["fkCodigo"]."'";
               //$sError.=$SQL_query;
                $xProdRS = mysql_query($SQL_query) or die (mysql_error());
                if ($xProdRow = mysql_fetch_array($xProdRS)) { 
                    $SQL_query = "UPDATE productosticket 
                        SET Cantidad=Cantidad+'".$xProdSuspRow["Cantidad"]."'
                        WHERE fkTicket='".$nTicketID."' AND fkCodigo='".$xProdSuspRow["fkCodigo"]."'";
                    #print "\n".$SQL_query;
                    $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
                } else {
                    $SQL_query = "INSERT INTO productosticket (fkTicket, fkProducto, fkCodigo, PrecioUnitario, Cantidad, PorcIVA, hora, precioLista1, Vendor) "
                        . " VALUES ('".$nTicketID."', '".$xProdSuspRow["fkProducto"]."', '".$xProdSuspRow["fkCodigo"]."', '".$xProdSuspRow["PrecioUnitario"]."', '".$xProdSuspRow["Cantidad"]."', '".$xProdSuspRow["PorIVA"]."', CURRENT_TIMESTAMP(), '".$xProdSuspRow["precioLista1"]."', '".$xProdSuspRow["Vendor"]."') ";
                    //$sError.=$SQL_query;
                    $xResultsRS = mysql_query($SQL_query) or die (mysql_error());
                }
                mysql_free_result($xProdRS);
            }
            mysql_free_result($xProdSuspRow);
    } else 
        $sError="NO_SUSPENDIDO";
    if($sPrecios1=="1")
	{
		#--- Actualiza los precios de los productos
		$SQL_query = "SELECT * FROM productosticket pt 
             INNER JOIN codigosbarra cb ON (pt.fkCodigo=cb.ID) 
             INNER JOIN productos p ON (cb.fkProducto=p.ID) 
             WHERE fkTicket='".$nTicketID."' ORDER BY hora ASC";
		//$sError.=$SQL_query;
		$xProdsRS = mysql_query($SQL_query) or die (mysql_error());
		while ($xProdsRow = mysql_fetch_array($xProdsRS)) { 
			$xProd = new Producto();
			$xProd->leeProducto($xProdsRow["fkProducto"]);
			$xProd->actualizaPrecio($nFKCliente, $xProdsRow["Cantidad"]);        
		}
		mysql_free_result($xProdsRS);
	}else{
		$SQL_query2 = "UPDATE tickets
			SET Mensaje=concat(Mensaje,' No Precios') WHERE ID='".$nTicketID."'";                 
		$xProdsRS2 = mysql_query($SQL_query2) or die (mysql_error());
	}
   $_SESSION["Comentario"]=$sComentario;
   $aResultado["Error"] = $sError;
   $aResultado["TicketID"] = $nTicketReanudar;
   print json_encode($aResultado);
}

if ($sEntidad=="listaTicketsSuspendidos") {
	header('Content-Type: text/html; charset=utf8');
mysql_query("SET NAMES 'utf8'");
    $nPrimerTicket=0;
    $iTicket=0;
    $sRow="";
    $SQL_query = "SELECT tickets.ID, fkCliente, Fecha, cajas.Nombre Caja, Numero, usuarios.Nombre Usuario, cajas.Clave, ifnull(Comentario,'') Comentario, ifnull(Pedido,'') Pedido, ifnull(clientes.Nombre,'') Nombre  FROM tickets
        LEFT JOIN cajas on cajas.ID=tickets.fkCaja left join usuarios on usuarios.ID=tickets.fkUsuario left join clientes on clientes.ID=tickets.fkCliente
             WHERE Suspendido>0 ORDER BY ID DESC";
    //$sError.=$SQL_query;
    $xTSuspRS = mysql_query($SQL_query) or die (mysql_error());
    while ($xTSuspRow = mysql_fetch_array($xTSuspRS)) { 
        $iTicket++;
        $sCliente='';
		$sComentario=trim($xTSuspRow["Comentario"]);
        $sCliente=trim($xTSuspRow["Nombre"]);
    	// SUSPENDIDOS: Si al suspender el ticket escribio un texto, poner este texto si no el cliente
        if($sComentario!="")
			$sCliente=$sComentario;
		
		 $sRow.='<tr class="TicketSuspendidoRow" RowID="'.$xTSuspRow["ID"].'" id="TicketSuspendidoLista_'.$iTicket.'" rowNum="'.$iTicket.'">
             <td>'.fechahumana($xTSuspRow["Fecha"]).'</td>
             <td>'.($iTicket+1).'</td>
             <td>'.$xTSuspRow["ID"].'</td>
             <td>'.$sCliente.'</td>
             <td>'.$xTSuspRow["Caja"].'</td>
             <td>'.$xTSuspRow["Usuario"].'</td>
			 <td>'.$xTSuspRow["Pedido"].'</td>
         </tr>';		 
    }
    mysql_free_result($xTSuspRS);
    mysql_free_result($xClienteRS);
    $aResultado["Error"] = $sError;
    $aResultado["Lista"] = $sRow;
    $aResultado["Tickets"] = $iTicket;
    $aResultado["PrimerTicket"] = $nPrimerTicket;
    print json_encode($aResultado);
}

if ($sEntidad=="listaTickets") {
    $nPrimerTicket=0;
    $iTicket=0;
    $sRow="";
    $SQL_query = "SELECT tickets.*, cajas.Clave FROM tickets left join cajas on cajas.ID=tickets.fkCaja
             WHERE Abierto=0 AND Suspendido=0 AND (Cancelado=0 OR Cancelado IS NULL) ORDER BY ID DESC";
    #print "<P>".$SQL_query;
    $xTicketsRS = mysql_query($SQL_query) or die (mysql_error());
    while ($xTicketsRow = mysql_fetch_array($xTicketsRS)) { 
        $iTicket++;
        $sCliente='';
        $SQL_query = "SELECT Nombre FROM clientes WHERE ID='".$xTicketsRow["fkCliente"]."'";
        #print "<P>".$SQL_query;
        $xClienteRS = mysql_query($SQL_query) or die (mysql_error());
        while ($xClienteRow = mysql_fetch_array($xClienteRS)) { 
            $sCliente=convTexto2($xClienteRow["Nombre"]);
        }
        mysql_free_result($xClienteRS);
        
        if ($nPrimerTicket==0) $nPrimerTicket=$xTicketsRow["ID"];
        
        $sRow.='<tr class="TicketRow" RowID="'.$xTicketsRow["ID"].'"  id="TicketLista_'.$iTicket.'"  rowNum="'.$iTicket.'">
             <td>'.$xTicketsRow["Clave"].'-'.$xTicketsRow["Numero"].'</td>
             <td>'.$sCliente.'</td>
             <td>'.fechahumana($xTicketsRow["Fecha"]).'</td>
         </tr>';
    }
    mysql_free_result($xTicketsRS);
    
    $aResultado["Error"] = "";
    $aResultado["Lista"] = $sRow;
    $aResultado["Tickets"] = $iTicket;
    $aResultado["PrimerTicket"] = $nPrimerTicket;
    print json_encode($aResultado);
}
if ($sEntidad=="listaTicketsCancelar") {
	header('Content-Type: text/html; charset=utf8');
mysql_query("SET NAMES 'utf8'");
    $nPrimerTicket=0;
    $iTicket=0;
    $sRow="";
	$Buscar=$_POST["Buscar"];
	$SQL_query1 = "SELECT ifnull(max(fkTicketFinal),0) fkTicketFinal from cortez;";
	$xTicketsRS1 = mysql_query($SQL_query1) or die (mysql_error());
    if ($xTicketsRow1 = mysql_fetch_array($xTicketsRS1)) { 
		$sfkTicketFinal=$xTicketsRow1["fkTicketFinal"];
	}
    $SQL_query = "SELECT tickets.*, cajas.Clave, clientes.Nombre FROM tickets left join cajas on cajas.ID=tickets.fkCaja LEFT JOIN clientes on clientes.ID=tickets.fkCliente
             WHERE Abierto=0 AND Suspendido=0 AND (Cancelado=0 OR Cancelado IS NULL) AND tickets.ID>'".$sfkTicketFinal."' and tickets.ID  not in (select FkTicket from ticketscxc where bloqueado=1)";
			 if($Buscar!="")
			 {
				$SQL_query .=" AND (tickets.Numero like '%".$Buscar."%' OR clientes.Nombre like '%".$Buscar."%' or tickets.Fecha like '".$Buscar."')"; 
				 
			 }
			  $SQL_query .=" ORDER BY ID DESC";
    #print "<P>".$SQL_query;
    $xTicketsRS = mysql_query($SQL_query) or die (mysql_error());
    while ($xTicketsRow = mysql_fetch_array($xTicketsRS)) { 
        $iTicket++;
        if ($nPrimerTicket==0) $nPrimerTicket=$xTicketsRow["ID"];
        
        $sRow.='<tr class="TicketRow" RowID="'.$xTicketsRow["ID"].'"  id="TicketLista_'.$iTicket.'"  rowNum="'.$iTicket.'">
             <td>'.$xTicketsRow["Clave"].'-'.$xTicketsRow["Numero"].'</td>
             <td>'.$xClienteRow["Nombre"].'</td>
             <td>'.fechahumana($xTicketsRow["Fecha"]).'</td>
         </tr>';
    }
    mysql_free_result($xTicketsRS);
    
    $aResultado["Error"] = "";
    $aResultado["Lista"] = $sRow;
    $aResultado["Tickets"] = $iTicket;
    $aResultado["PrimerTicket"] = $nPrimerTicket;
    print json_encode($aResultado);
}

if ($sEntidad=="listaProductos") {
	mysql_query("SET NAMES 'utf8'");
    $bBloqOnExistZero=(intval($_POST["bBloqOnExistZero"])==1?true:false);
	$sBloqCosto=($_POST["sBloqCosto"]);
    $iProd=0;
    $nArticulos=0;
    $bExistenciaSuficiente=1;
	$bPrecioSuficiente=1;
    
    #--- obtiene el amacen afectado por el ticket
    $nFKAlmacen=0;
    $SQL_query = "SELECT fkAlmacen FROM cajas WHERE ID='".$_SESSION["fkCaja"]."'";
    #print "<P>".$SQL_query;
    $xCajaRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xCajaRow = mysql_fetch_array($xCajaRS)) {
        $nFKAlmacen=intval($xCajaRow["fkAlmacen"]);
    }
    mysql_free_result($xCajaRS);    
    
    $SQL_query = "SELECT *, pt.ID IDP FROM productosticket pt 
             INNER JOIN codigosbarra cb ON (pt.fkCodigo=cb.ID) 
             INNER JOIN productos p ON (cb.fkProducto=p.ID) 
             WHERE fkTicket='".$nTicketID."' ORDER BY hora ASC";
    $sDebug.="\n".$SQL_query;
    $xProdsRS = mysql_query($SQL_query) or die (mysql_error());
    while ($xProdsRow = mysql_fetch_array($xProdsRS)) {         
        #--- resetea valores
            $sClaseExistenciaEstatus='';
            $nExistencia=0;
        
        #--- Contabiliza...
            $nArticulos+=$xProdsRow["Cantidad"];
            $sDebug.="\n ---- LISTADO DE PRODUCTOS: ----";
            $sDebug.="\n excento: (".$xProdsRow["exentoiva"].")";
            $sDebug.="\n cantidad: (".$xProdsRow["Cantidad"].")";
            $sDebug.="\n PU: (".$xProdsRow["PrecioUnitario"].")";
            $sDebug.="\n porcIVA: (".$xProdsRow["PorcIVA"].")";
            
            $nSubtotalLinea = floatval($xProdsRow["Cantidad"] * $xProdsRow["PrecioUnitario"]);
            $nSubtotalLinea-= floatval($xProdsRow["montoIVA"]);
            $nSubtotalLinea-= floatval($xProdsRow["montoIMP2"]);
            $nSubtotalLinea-= floatval($xProdsRow["montoIMP3"]);
            $nSubtotalLinea-= floatval($xProdsRow["montoIMP4"]);
            $nSubtotalLinea-= floatval($xProdsRow["montoIMP5"]);
        
		    $sDebug.="\n nSubtotalLinea: (".$nSubtotalLinea.")";
            $nIVA+=floatval($xProdsRow["montoIVA"]);
            $nOtrosImpuestos+=floatval($xProdsRow["montoIMP2"])+floatval($xProdsRow["montoIMP3"])+floatval($xProdsRow["montoIMP4"])+floatval($xProdsRow["montoIMP5"]);
            $sDebug.="\n montoIVA: (".$xProdsRow["montoIVA"].")";
            $sDebug.="\n montoIVA total: (".$nIVA.")";
            $nSubtotal+=$nSubtotalLinea;            

        #--- Verifica inventarios
            $SQL_query = "SELECT Existencia FROM existencias 
                     WHERE fkAlmacen='".$nFKAlmacen."' AND fkProducto='".$xProdsRow["ID"]."'";
            #print "<P>".$SQL_query;
            $xInventarioRS = mysql_query($SQL_query) or die (mysql_error());
            if ($xInventarioRow = mysql_fetch_array($xInventarioRS)) { 
                $nExistencia=intval($xInventarioRow["Existencia"]);
            }
            mysql_free_result($xInventarioRS);
            
            #--- Si la caja está configurada para evitar venta sin existencia, las verifica y marca en rojo esos casos...
            if ($bBloqOnExistZero) {
                if ($nExistencia<$xProdsRow["Cantidad"]) {
                    $bExistenciaSuficiente=0;
                    $sClaseExistenciaEstatus="pv_ProductoExistenciaInsuficiente";
                }
            }
              
              #--- Si la caja está configurada para evitar venta de producto abajo del costo...
		     if ($sBloqCosto!="None") {		
			 switch($sBloqCosto){
				case "Ult": $sBloqCosto1="CostoUltimo";
				break;	
				case "Max":$sBloqCosto1="CostoMax";
				break;	
				case "Prom":$sBloqCosto1="CostoPromedio";
				break;	
			 }
                if ($xProdsRow["PrecioUnitario"]<$xProdsRow[$sBloqCosto1]) {
                    $bPrecioSuficiente=0;
                    $sClaseExistenciaEstatus="pv_ProductoExistenciaInsuficiente";
                }
            }
        // SECUNDARIO
        if($xProdsRow["Tipo"]=="Secundaria" && trim($xProdsRow["descripcion"])!="")
			$sproducto=$xProdsRow["descripcion"];
		else if($xProdsRow["Tipo"]=="Multiple" && trim($xProdsRow["descripcion"])!="" )
			$sproducto=$xProdsRow["descripcion"];
		else
		 	$sproducto=$xProdsRow["NombreLargo"];
		 
        $sRow.='<tr class="pv_tablaproductos_tr ListaRow '.$sClaseExistenciaEstatus.'" rowID="'.$xProdsRow["fkCodigo"].'" id="ProdLista_'.$iProd.'" rowNum="'.$iProd.'" row2="'.$xProdsRow["IDP"].'">
            <td align="left"></td>
            <td align="left">'.$sproducto.'<BR>'.$xProdsRow["Codigo"].'</td>
            <td align="left"></td>
            <td align="right"><input type="number" id="Cantidad_'.$xProdsRow["IDP"].'" name="Cantidad_'.$xProdsRow["IDP"].'" value="'.round($xProdsRow["Cantidad"],3).'" step="any" onchange="javascript: cambiarcantidad('.$xProdsRow["IDP"].');" ></td>
            <td align="left"></td>
            <td align="right"><DIV ID="Precio1d_'.$xProdsRow["fkCodigo"].'" >$<a href="javascript: cambiarPrecio1('.$xProdsRow["fkCodigo"].');">'.number_format($xProdsRow["PrecioUnitario"],2).'</a></div><div id="Precio2d_'.$xProdsRow["fkCodigo"].'" style="display: none;">$<input type="number" id="Precio2_'.$xProdsRow["fkCodigo"].'" name="Precio2_'.$xProdsRow["fkCodigo"].'" value="'.number_format($xProdsRow["PrecioUnitario"],2).'" step="any"  style = " width: 5em"; onchange="javascript: cambiarPrecio2('.$xProdsRow["fkCodigo"].');" /></td>
            <td align="left"></td>
            <td align="right">$'.number_format(floatval($xProdsRow["Cantidad"] * ($xProdsRow["PrecioUnitario"])),2).'</td>
          </tr>';
        $iProd++;
    }
    mysql_free_result($xProdsRS);
    
    $sRow.='<!--ULTIMO RENGLON PERMITE DEFINIR LOS MISMOS TAMANOS QUE EL ENCABEZADO --->
                     <tr height="100%">
                       <td widtd="2%"></td>
                       <td widtd="56%"></td>
                       <td widtd="2%"></td>
                       <td widtd="14%"></td>
                       <td widtd="2%"></td>
                       <td widtd="14%"></td>
                       <td widtd="2%"></td>
                       <td widtd="8%"></td>
                     </tr>';    
    $nTotal=floatval($nSubtotal) + floatval($nIVA) + $nOtrosImpuestos;
    $aResultado["Debug"] = $sDebug;
    $aResultado["Error"] = $sError;
    $aResultado["Lista"] = $sRow;
    $aResultado["Articulos"] = $nArticulos;
    $aResultado["LineasProductos"] = $iProd;
    $aResultado["Subtotal"] = number_format($nSubtotal,2);
    $aResultado["IVA"] = number_format($nIVA,2);
    $aResultado["Total"] = number_format($nTotal,2);
    $aResultado["ExistenciasSuficientes"] = intval($bExistenciaSuficiente);
    $aResultado["PrecioSuficiente"] = intval($bPrecioSuficiente);     
    print json_encode($aResultado);    
}
if ($sEntidad=="CambiarPrecio") {
	 $nfkCodigo = intval($_POST["fkCodigo"]);
	    $sProductoCodigo = urldecode($_POST["Codigo"]);
    $nFKCliente = intval($_SESSION["fkCliente"]);   // Público General
    $nFKCliente = ($nFKCliente==0?1:$nFKCliente);
    $nCantidad  =floatval(1);   
        
	$xProd = new Producto();
    $xProd->leeProductoPorCampoForaneo("codigosbarra","ID", $nfkCodigo);    
    $xProd->actualizaPrecio($nFKCliente, $nCantidad);
            
    recalculaImpuestos();        
    $sEntidad="listaProductos";
}

?>