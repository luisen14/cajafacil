<?php
// Include the main TCPDF library (search for installation path).
require_once('includes/tcpdf_include.php');
require_once('includes/dbconnect.php');
mysql_query("SET NAMES 'utf8'");
require_once('includes/util.php');

$nTicketID = intval($_GET["TicketID"]);
if ($nTicketID == 0)
    $nTicketID = intval($_POST["TicketID"]);
  $nFolio = intval($_POST["Folio"]);
if (isset($_POST["Reimpresion"]))
    $bReimpresion = (intval($_POST["Reimpresion"]) == 1 ? true : false);
else
    $bReimpresion = (intval($_GET["Reimpresion"]) == 1 ? true : false);

if (isset($_POST["Suspendido"]))
    $bSuspendido = (intval($_POST["Suspendido"]) > 0 ? true : false);
else
    $bSuspendido = (intval($_GET["Suspendido"]) > 0 ? true : false);

function strafter($string, $substring) {
    $pos = strpos($string, $substring);
    if ($pos === false)
        return $string;
    else
        return(substr($string, $pos + strlen($substring)));
}

#-- Inicialización de variables
$cMaxLong=40;
$sTicketTexto = "";
$sNombreFiscal = "";
$sRFC = "";
$sDomicilio = "";
$sTels = "";
$sCaja = "";
$sFolio = "";
$sVendedor = "";
$nFkCliente = 0;
$sClienteNombre = "";

#--- Datos Ticket        
$dFecha = date("d/m/Y h:i:sa");
$SQL_query = "SELECT concat(cajas.Clave,'-',Numero) Ticket, fkCaja, fkUsuario, fkCliente, tickets.Vendor, usuarios.Nombre Cajero, case when ifnull(Cancelado,0)=1 then 'CANCELADO' ELSE '' end Cancelado FROM tickets 
    left join cajas on cajas.ID=tickets.fkCaja
    left join usuarios on usuarios.ID=tickets.fkUsuario WHERE tickets.ID='" . $nTicketID . "' ";
if ($bSuspendido) {
    $SQL_query.= "AND Abierto=1 ";
} else {
    $SQL_query.= "AND Abierto=0 ";
}
//$SQL_query.= " AND (Cancelado IS NULL OR Cancelado=0) ";
#print "<P>".$SQL_query;
$xTicketRS = mysql_query($SQL_query) or die(mysql_error());
if ($xTicketRow = mysql_fetch_array($xTicketRS)) {
    $sFolio = $xTicketRow["Ticket"];
    $nFKCaja = intval($xTicketRow["fkCaja"]);
    $sCajero = $xTicketRow["Cajero"];	
    $nFKCliente = intval($xTicketRow["fkCliente"]);
    $nIVA = floatval($xTicketRow["IVA"]);
    $nImp2 = floatval($xTicketRow["Imp2"]);
    $nImp3 = floatval($xTicketRow["Imp3"]);
    $nImp4 = floatval($xTicketRow["Imp4"]);
    $nImp5 = floatval($xTicketRow["Imp5"]);
	$sCancelado = $xTicketRow["Cancelado"];	
}
mysql_free_result($xTicketRS);
$SQL_query = "SELECT sucursales.NombreFiscal, sucursales.RFC, sucursales.Domicilio, sucursales.Telefono, empresa.logo FROM empresa,sucursales,cajas  where cajas.ID='".$nFKCaja."' and cajas.FkSucursal=sucursales.ID Limit 0,1";
// print "<P>".$SQL_query;
$xCajaRS = mysql_query($SQL_query) or die(mysql_error());
if ($xCajaRow = mysql_fetch_array($xCajaRS)) {
    $sNombreFiscal = convTexto($xCajaRow["NombreFiscal"]);
    $sRFC = convTexto($xCajaRow["RFC"]);
    $sDomicilio = convTexto($xCajaRow["Domicilio"]);
    $sTels = convTexto($xCajaRow["Telefono"]);
    if (trim($xCajaRow["logo"]) == "")
        $pagina = "1";
    else
        $pagina = "0";
}
mysql_free_result($xCajaRS);
#--- Configuracion del Ticket  
$ncopias=0;
$SQL_query = "SELECT MensajeInicial, MensajeFinal, MostrarMensajeInicial, MostrarMensajeFinal, MostrarImagen, MostrarPublicidad, Imagen, Publicidad, Descripcion, Codigo, Cantidad, PrecioUnitario, Total, IVA, TotalEnLetra, TotalDeProductos, Cambio, MostrarCaja, MostrarCajero, Imp2, Imp3, Imp4, Imp5, ifnull(copias,0) copias, MostrarFolio, FolioCB, ImageIndex, ifnull(copiascredito,0) copiascredito FROM configcaja left join configticketcompra on configticketcompra.ID=configcaja.fkConfigticketcompra WHERE configcaja.fkCaja='" . $nFKCaja . "'";
//print "<P>".$SQL_query;
$xTicketRS = mysql_query($SQL_query) or die(mysql_error());
if ($xTicketRow = mysql_fetch_array($xTicketRS)) {
    $bMostrarMensajeInicial = $xTicketRow["MostrarMensajeInicial"];
    $bMostrarMensajeFinal = $xTicketRow["MostrarMensajeFinal"];
    $sMensajeInicial = $xTicketRow["MensajeInicial"];
    $sMensajeFinal = $xTicketRow["MensajeFinal"];
    $bMostrarImagen = $xTicketRow["MostrarImagen"];
    $bMostrarPublicidad = $xTicketRow["MostrarPublicidad"];
    $sImagen = $xTicketRow["Imagen"];
    $sPublicidad = $xTicketRow["Publicidad"];
    $bDescripcion = $xTicketRow["Descripcion"];
    $bCodigo = $xTicketRow["Codigo"];
    $bPrecioUnitario = $xTicketRow["PrecioUnitario"];
    $bIVA = $xTicketRow["IVA"];
    $bTotalEnLetra = $xTicketRow["TotalEnLetra"];
    $bTotalDeProductos = $xTicketRow["TotalDeProductos"];
    $bCambio = $xTicketRow["Cambio"];
    $bMostrarCaja = $xTicketRow["MostrarCaja"];
    $bMostrarCajero = $xTicketRow["MostrarCajero"];
    $bImp2 = $xTicketRow["Imp2"];
    $bImp3 = $xTicketRow["Imp3"];
    $bImp4 = $xTicketRow["Imp4"];
    $bImp5 = $xTicketRow["Imp5"];
    $ncopias = $xTicketRow["copias"];
	$ncopiascredito = $xTicketRow["copiascredito"];
    $bMostrarFolio = $xTicketRow["MostrarFolio"];
    $bFolioCB = $xTicketRow["FolioCB"];
    $bImageIndex = $xTicketRow["ImageIndex"];
}
mysql_free_result($xTicketRS);

#--- Configuracion de Impuestos      
$dFecha = date("d/m/Y h:i:sa");
$SQL_query = "SELECT NombreImp2, NombreImp3, NombreImp4, NombreImp5 FROM parametros Limit 0,1; ";
#print "<P>".$SQL_query;
$xTicketRS = mysql_query($SQL_query) or die(mysql_error());
if ($xTicketRow = mysql_fetch_array($xTicketRS)) {
    $sNombreImp2 = convTexto(trim($xTicketRow["NombreImp2"]));
    $sNombreImp3 = convTexto(trim($xTicketRow["NombreImp3"]));
    $sNombreImp4 = convTexto(trim($xTicketRow["NombreImp4"]));
    $sNombreImp5 = convTexto(trim($xTicketRow["NombreImp5"]));
}
mysql_free_result($xTicketRS);

 if (trim($sNombreImp2) == "")
        $sNombreImp2 = "IMPUESTO 2";
    if (trim($sNombreImp3) == "")
        $sNombreImp3 = "IMPUESTO 3";
    if (trim($sNombreImp4) == "")
        $sNombreImp4 = "IMPUESTO 4";
    if (trim($sNombreImp5) == "")
        $sNombreImp5 = "IMPUESTO 5";
 #--- Datos Caja
    $SQL_query = "SELECT Nombre, Imprime FROM cajas WHERE ID='".$nFKCaja."'";
   // print "<P>".$SQL_query;
    $xCajaRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xCajaRow = mysql_fetch_array($xCajaRS)) {
        $sCaja=$xCajaRow["Nombre"];
		$bImprime=$xCajaRow["Imprime"];
    }
    mysql_free_result($xCajaRS);  	
#--- Datos Vendedor
    $SQL_query = "SELECT UsrVendor FROM productosticket left join usuarios on usuarios.ID=productosticket.Vendor WHERE fkTicket='". $nTicketID."' order by productosticket.ID Limit 0,1";
   // print "<P>".$SQL_query;
    $xCajaRS = mysql_query($SQL_query) or die (mysql_error());
    if ($xCajaRow = mysql_fetch_array($xCajaRS)) {
        $sVendedor=$xCajaRow["UsrVendor"];
    }
    mysql_free_result($xCajaRS);  	
#--- Datos Cliente    
$SQL_query = "SELECT Clave, Nombre, Direccion, Entre1, Entre2, Colonia FROM clientes WHERE ID='" . $nFKCliente . "'";
#print "<P>".$SQL_query;
$xClienteRS = mysql_query($SQL_query) or die(mysql_error());
if ($xClienteRow = mysql_fetch_array($xClienteRS)) {
    $sClienteNombre =$xClienteRow["Nombre"];
    $sClave = $xClienteRow["Clave"];
	$sDomicilioc = $xClienteRow["Direccion"]." ".$xClienteRow["Entre1"]." ".$xClienteRow["Entre2"]." ".$xClienteRow["Colonia"];
}
mysql_free_result($xClienteRS);

#--- Secuencia de dígitos para probar cuántos caracteres caben en el ticket:
// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    //Page header
    public function Header() {        
    }
    // Page footer
    public function Footer() {       
    }
}

// create new PDF document
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('CajaFacil');
$pdf->SetTitle('CajaFacil');
$pdf->SetSubject('');
$pdf->SetKeywords('');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $sNombreFiscal, $sTextoHeader);

// set header and footer fonts
$pdf->setHeaderFont(Array('courier', '', 7));
// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
if ($bMostrarImagen == 1 && $bImageIndex == 1 && trim($sImagen) != "") 
    $pdf->SetMargins(-1, 25, 0);
 else 
    $pdf->SetMargins(-1, 0, 0);

$pdf->SetHeaderMargin(0);
// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
    require_once(dirname(__FILE__) . '/lang/eng.php');
    $pdf->setLanguageArray($l);
}
// set font
$pdf->SetFont('courier', '', 7);
// add a page
$pdf->AddPage();

$pdf->setCellPaddings(1, 1, 1, 1);
$pdf->setCellMargins(1, 1, 1, 1);
$pdf->SetFillColor(255, 255, 255);

$pdf->Ln(4);
//imagen superior
if ($bMostrarImagen == 1 && $bImageIndex == 1 && trim($sImagen) != "") {
    $sExt = strafter($sPublicidad, '.');
    $pdf->Image('fotos/' . $sImagen, 0, 0, 100, '', $sExt, '', 'L', false, 150, '', false, true, 0, true, false, true);
    $pdf->Ln(4);
    $pdf->Ln(4);
}
if ($bReimpresion) {
    $pdf->Ln(4);
    $pdf->Cell(0, 15, "COPIA DE TICKET (REIMPRESION) ".$sCancelado, 0, false, 'L', 0, '', 0, false, 'M', 'M');
    $sTicketTexto.="\nCOPIA DE TICKET (REIMPRESION) ".$sCancelado;
    $pdf->Ln(4);
} else if ($bSuspendido) {
    $pdf->Ln(4);
    $pdf->Cell(0, 15, "REIMPRESION DE TICKET SUSPENDIDO", 0, false, 'L', 0, '', 0, false, 'M', 'M');
    $sTicketTexto.="\nREIMPRESION DE TICKET SUSPENDIDO";
    $pdf->Ln(4);
}

$pdf->Cell(0, 15, $sNombreFiscal, 0, false, 'L', 0, '', 0, false, 'M', 'M');
$sTicketTexto.="\n" . textoTicket($sNombreFiscal,$cMaxLong,"C");
$pdf->Ln(4);
$pdf->Cell(0, 15, $sRFC, 0, false, 'L', 0, '', 0, false, 'M', 'M');
$sTicketTexto.="\n" . textoTicket($sRFC,$cMaxLong,"C");
$pdf->Ln(4);
$pdf->Cell(0, 15, $sDomicilio, 0, false, 'L', 0, '', 0, false, 'M', 'M');
$sTicketTexto.="\n" . textoTicket($sDomicilio,$cMaxLong,"C");
$pdf->Ln(4);
$pdf->Cell(0, 15, "Tel(s): " . $sTels, 0, false, 'L', 0, '', 0, false, 'M', 'M');
$sTicketTexto.="\n".textoTicket("Tel(s): " . $sTels,$cMaxLong,"C");
$pdf->Ln(4);
$pdf->Ln(4);
//mensaje inicial
if ($bMostrarMensajeInicial == 1) {
    $pdf->Cell(0, 15, $sMensajeInicial, 0, false, 'L', 0, '', 0, false, 'M', 'M');
    $sTicketTexto.="\n" . textoTicket($sMensajeInicial,$cMaxLong,"C");
    $pdf->Ln(4);
    $pdf->Ln(4);
}
#MultiCell ($w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false)
$pdf->MultiCell(100, 5, 'Cliente No. ' . $sClave, 0, 'L', 1, 0, '', '', true);
$sTicketTexto.="\n".textoTicket("Cliente No. " . $sClave,$cMaxLong,"L");
$pdf->Ln(4);
$pdf->MultiCell(100, 5, 'Nombre: ' . convTexto($sClienteNombre), 0, 'L', 1, 0, '', '', true);
$sTicketTexto.="\n".textoTicket("Nombre: " .convTexto( $sClienteNombre),$cMaxLong,"L");
$pdf->Ln(4);
$sDomicilioc1= substr($sDomicilioc,0,22);
$sDomicilioc2= substr($sDomicilioc,22,54);
$sDomicilioc3= substr($sDomicilioc,54,92);
$sDomicilioc4= substr($sDomicilioc,92,127);
$pdf->MultiCell(100, 5, 'Domicilio: ' . convTexto($sDomicilioc1), 0, 'L', 1, 0, '', '', true);
$sTicketTexto.="\n".textoTicket("Domicilio: " .convTexto( $sDomicilioc1),$cMaxLong,"L");
$pdf->Ln(4);
if(trim($sDomicilioc2)!="")
{
$pdf->MultiCell(100, 5, convTexto($sDomicilioc2), 0, 'L', 1, 0, '', '', true);
$sTicketTexto.="\n".textoTicket(convTexto( $sDomicilioc2),$cMaxLong,"L");
$pdf->Ln(4);

}
if(trim($sDomicilioc3)!="")
{
$pdf->MultiCell(100, 5, convTexto($sDomicilioc3), 0, 'L', 1, 0, '', '', true);
$sTicketTexto.="\n".textoTicket(convTexto( $sDomicilioc3),$cMaxLong,"L");
$pdf->Ln(4);

}
if(trim($sDomicilioc4)!="")
{
$pdf->MultiCell(100, 5, convTexto($sDomicilioc4), 0, 'L', 1, 0, '', '', true);
$sTicketTexto.="\n".textoTicket(convTexto( $sDomicilioc4),$cMaxLong,"L");
$pdf->Ln(4);

}
$pdf->Ln(4); 
$sTicketTexto.="\n";
$pdf->MultiCell(100, 5, convTexto('Fecha Impresión: ') . $dFecha, 0, 'L', 1, 0, '', '', true);
$sTicketTexto.="\n".textoTicket(convTexto("Fecha Impresión: ") . $dFecha,$cMaxLong,"L");
$pdf->Ln(4);
if ($bMostrarCaja == 1) {
    $pdf->MultiCell(100, 5, 'Caja: ' . $sCaja, 0, 'L', 1, 0, '', '', true);
    $sTicketTexto.="\n".textoTicket("Caja: " . $sCaja,$cMaxLong,"L");
    $pdf->Ln(4);
}
if ($bMostrarCajero == 1) {
    $pdf->MultiCell(100, 5, 'Cajero: ' . $sCajero, 0, 'L', 1, 0, '', '', true);
    $sTicketTexto.="\n".textoTicket("Cajero: " . $sCajero,$cMaxLong,"L");
    $pdf->Ln(4);
}
if ($bMostrarFolio == 1) {
if(trim($nFolio)!=0)
	$sFolio=$nFolio;
    $pdf->MultiCell(100, 5, 'Folio: ' . $sFolio, 0, 'L', 1, 0, '', '', true);
    $sTicketTexto.="\n".textoTicket("Folio: " . $sFolio,$cMaxLong,"L");
    $pdf->Ln(4);
}
if ($bMostrarCajero == 1) {
    $pdf->MultiCell(100, 5, 'Vendedor: ' . $sVendedor, 0, 'L', 1, 0, '', '', true);
    $sTicketTexto.="\n".textoTicket("Vendedor: " . $sVendedor,$cMaxLong,"L")."\n";
    $pdf->Ln(4);
}
if ($bFolioCB == 1) {
    // define barcode style
    $style = array(
        'position' => '',
        'align' => 'L',
        'stretch' => false,
        'fitwidth' => true,
        'cellfitalign' => '',
        'border' => false,
        'hpadding' => 'auto',
        'vpadding' => 'auto',
        'fgcolor' => array(0, 0, 0),
        'bgcolor' => false, //array(255,255,255),
        'text' => false,
        'font' => 'courier',
        'fontsize' => 8,
        'stretchtext' => 4
    );

    $pdf->Ln(4);
    $pdf->write1DBarcode($sFolio, 'C128A', '', '', '', 25, 0.5, $style, 'N');
    $pdf->Ln(4);
	$bbarcode=1;	
}

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
#--- Productos del ticket...
$iProd = 0;
$nArticulos = 0;
$nArticulos = 0;
$nSubtotal = 0;
$nIVA = 0;
$nImp2 = 0;
$nImp3 = 0;
$nImp4 = 0;
$nImp5 = 0;
$nDescuento =0;
$SQL_query = "SELECT * FROM productosticket pt 
             INNER JOIN codigosbarra cb ON (pt.fkCodigo=cb.ID) 
             INNER JOIN productos p ON (cb.fkProducto=p.ID) 
             WHERE fkTicket='" . $nTicketID . "' ORDER BY hora ASC";
	/*		  $pdf->Ln(4);
 $pdf->MultiCell(180, 5, $SQL_query, 0, 'L', 1, 0, '', '', true);	
  $pdf->Ln(4);
   $pdf->Ln(4);		
    $pdf->Ln(4);
	 $pdf->Ln(4);*/
$xProdsRS = mysql_query($SQL_query) or die(mysql_error());
while ($xProdsRow = mysql_fetch_array($xProdsRS)) {
    $nArticulos+=$xProdsRow["Cantidad"];
    $nSubtotalLinea = floatval($xProdsRow["Cantidad"]) * floatval($xProdsRow["PrecioUnitario"]);
    //quitar impuestos y calcular cada uno       
    $nPrecioSImp = (floatval($xProdsRow["Cantidad"]) * floatval($xProdsRow["PrecioUnitario"]));
	 $nImpuestos=  0;
	  $nDescuento1=(floatval($xProdsRow["Cantidad"]) * ( floatval($xProdsRow["precioLista1"]) -floatval($xProdsRow["PrecioUnitario"]) ));
	if ($bIVA == 1){ $nImpuestos=  $xProdsRow["PorcIVA"]/100;  }
	if ($bImp2 == 1) {  $nImpuestos+=   $xProdsRow["PorcIMP2"]/100;  }
	if ($bImp3 == 1) {  $nImpuestos+=  $xProdsRow["PorcIMP3"]/100 ; }
	if ($bImp4 == 1) {  $nImpuestos+=  $xProdsRow["PorcIMP4"]/100; $nImp4+=$xProdsRow["montoIMP4"];}
	if ($bImp5 == 1)  { $nImpuestos+=   $xProdsRow["PorcIMP5"]/100; $nImp5+=$xProdsRow["montoIMP5"];}
	$nPrecioSImp =$nPrecioSImp /(1+$nImpuestos);
	if ($bIVA == 1){$nIVA+=$nPrecioSImp*($xProdsRow["PorcIVA"]/100);}
	if ($bImp2 == 1) {$nImp2+=$nPrecioSImp*($xProdsRow["PorcIMP2"]/100);}
	if ($bImp3 == 1) {$nImp3+=$nPrecioSImp*($xProdsRow["PorcIMP3"]/100);}
	if ($bImp4 == 1) {$nImp5+=$nPrecioSImp*($xProdsRow["PorcIMP4"]/100);}
	if ($bImp5 == 1)  {$nImp5+=$nPrecioSImp*($xProdsRow["PorcIMP5"]/100);}
    $nSubtotal+=$nPrecioSImp;
    $nTotal+=$nSubtotalLinea;
	 $nDescuento+=$nDescuento1;
    $pdf->Ln(4);
    //solo descripcion
    if ($bDescripcion == "1" && $bCodigo == "0" && $bPrecioUnitario == "0") {
        $pdf->MultiCell(5, 5, floatval($xProdsRow["Cantidad"]), 0, '0', 1, 0, '', '', true);
		  $sTicketTexto.="\n".textoTicket(floatval($xProdsRow["Cantidad"]) . " x",6,"L")." ";
		//si es codigo secundario y tiene descripcion ponerla
		if(($xProdsRow["Tipo"]=="Secundaria" || $xProdsRow["Tipo"]=="Multiple") && trim($xProdsRow["descripcion"])!="" )
		{
	        $pdf->MultiCell(40, 5, convTexto(substr($xProdsRow["descripcion"], 0, 29)), 0, 'L', 1, 0, '', '', true);
            $sTicketTexto.=textoTicket(convTexto(substr($xProdsRow["descripcion"], 0, 29)),24,"L")." ";			
		}
		else		
		{
	        $pdf->MultiCell(40, 5, convTexto(substr($xProdsRow["NombreLargo"], 0, 29)), 0, 'L', 1, 0, '', '', true);
			$sTicketTexto.=textoTicket(convTexto($xProdsRow["NombreLargo"]),24,"L")." ";
		}
        $pdf->MultiCell(10, 5, "$" . number_format($nSubtotalLinea, 2), 0, 'R', 1, 0, '', '', true);
        $sTicketTexto.=textoTicket("$" . number_format($nSubtotalLinea, 2),8,"L");
        
    } else if ($bDescripcion == "1" && $bCodigo == "1" && $bPrecioUnitario == "0") {
        $pdf->MultiCell(5, 0, floatval($xProdsRow["Cantidad"]), 0, '0', 1, 0, '', '', true);
		$sTicketTexto.="\n".textoTicket(floatval($xProdsRow["Cantidad"]) . " x",6,"L")." ";
		if(($xProdsRow["Tipo"]=="Secundaria" || $xProdsRow["Tipo"]=="Multiple") && trim($xProdsRow["descripcion"])!="" )
		{
	        $pdf->MultiCell(46, 0, convTexto(substr($xProdsRow["descripcion"], 0, 27)), 0, 'L', 1, 0, '', '', true);
			$sTicketTexto.=textoTicket(convTexto(substr($xProdsRow["descripcion"], 0, 27)),20,"L")." ";			
		}
		else
		{			
        	$pdf->MultiCell(46, 0, convTexto(substr($xProdsRow["NombreLargo"], 0, 27)), 0, 'L', 1, 0, '', '', true);
			 $sTicketTexto.=textoTicket(convTexto($xProdsRow["NombreLargo"]),20,"L")." ";
		}
        $pdf->MultiCell(30, 0, convTexto($xProdsRow["Codigo"]), 0, 'L', 1, 0, '', '', true);
        $pdf->Ln(4);
        $pdf->MultiCell(80, 5, "$" . number_format($nSubtotalLinea, 2), 0, 'L', 1, 0, '', '', true);
        
       
        $sTicketTexto.=textoTicket(convTexto($xProdsRow["Codigo"]),10,"L")." ";
        $sTicketTexto.="\n".textoTicket("$" . number_format($nSubtotalLinea, 2),10,"R");

    } else if ($bDescripcion == "1" && $bCodigo == "1" && $bPrecioUnitario == "1") {
		if(($xProdsRow["Tipo"]=="Secundaria" || $xProdsRow["Tipo"]=="Multiple") && trim($xProdsRow["descripcion"])!="" )
		{
	        $pdf->MultiCell(60, 4, convTexto(substr($xProdsRow["descripcion"], 0, 34)), 0, 'L', 1, 0, '', '', true);
			$sTicketTexto.="\n".textoTicket(convTexto(substr($xProdsRow["descripcion"], 0, 34)),25,"L")."  ";			
		}
		else		
		{
	        $pdf->MultiCell(60, 4, convTexto(substr($xProdsRow["NombreLargo"], 0, 34)), 0, 'L', 1, 0, '', '', true);
			$sTicketTexto.="\n".textoTicket(convTexto($xProdsRow["NombreLargo"]),25,"L")."  ";
		}
        $pdf->MultiCell(60, 0, convTexto($xProdsRow["Codigo"]), 0, 'L', 1, 0, '', '', true);
        $pdf->Ln(4);
        $pdf->MultiCell(15, 5, floatval($xProdsRow["Cantidad"]) . " x", 0, '0', 1, 0, '', '', true);
        $pdf->MultiCell(30, 5, "$" . number_format($xProdsRow["PrecioUnitario"], 2), 0, 'R', 1, 0, '', '', true);
        $pdf->MultiCell(35, 5, "$" . number_format($nSubtotalLinea, 2), 0, 'R', 1, 0, '', '', true);
        
        $sTicketTexto.=textoTicket(convTexto($xProdsRow["Codigo"]),12,"L");
        $sTicketTexto.="\n".textoTicket(floatval($xProdsRow["Cantidad"]) . " x",6,"L")." ";
        $sTicketTexto.=textoTicket("$" . number_format($xProdsRow["PrecioUnitario"], 2),12,"R")." ";
        $sTicketTexto.=textoTicket("$" . number_format($nSubtotalLinea, 2),20,"R");

    } else if ($bDescripcion == "1" && $bCodigo == "0" && $bPrecioUnitario == "1") {
        $pdf->MultiCell(10, 5, floatval($xProdsRow["Cantidad"]), 0, '0', 1, 0, '', '', true);
		 $sTicketTexto.="\n".textoTicket(floatval($xProdsRow["Cantidad"]) . " x",6,"L")." ";
		if(($xProdsRow["Tipo"]=="Secundaria" || $xProdsRow["Tipo"]=="Multiple") && trim($xProdsRow["descripcion"])!="" )
		{
	         $pdf->MultiCell(39, 5,  convTexto(substr($xProdsRow["descripcion"], 0, 29)), 0, 'L', 1, 0, '', '', true);	
			  $sTicketTexto.=textoTicket(convTexto(substr($xProdsRow["descripcion"], 0, 29)),20,"L")." ";
		}
		else
		{
       		 $pdf->MultiCell(39, 5, convTexto(substr($xProdsRow["NombreLargo"], 0, 29)), 0, 'L', 1, 0, '', '', true);
			  $sTicketTexto.=textoTicket(convTexto($xProdsRow["NombreLargo"]),14,"L")." ";
		}
        $pdf->MultiCell(15, 5, "$" . number_format($xProdsRow["PrecioUnitario"], 2), 0, 'R', 1, 0, '', '', true);
        $pdf->MultiCell(15, 5, "$" . number_format($nSubtotalLinea, 2), 0, 'R', 1, 0, '', '', true);
       
        $sTicketTexto.=textoTicket("$" . number_format($xProdsRow["PrecioUnitario"],2),7,"R")." ";
        $sTicketTexto.=textoTicket("$" . number_format($nSubtotalLinea, 2),10,"R");

    } else if ($bDescripcion == "0" && $bCodigo == "1" && $bPrecioUnitario == "1") {
        $pdf->MultiCell(150, 5, convTexto($xProdsRow["Codigo"]), 0, 'L', 1, 0, '', '', true);
        $pdf->Ln(4);
        $pdf->MultiCell(15, 5, floatval($xProdsRow["Cantidad"]) . " x ", 0, '0', 1, 0, '', '', true);
        $pdf->MultiCell(30, 5, "$" . number_format($xProdsRow["PrecioUnitario"], 2), 0, 'R', 1, 0, '', '', true);
        $pdf->MultiCell(35, 5, "$" . number_format($nSubtotalLinea, 2), 0, 'R', 1, 0, '', '', true);

        $sTicketTexto.="\n".textoTicket(convTexto($xProdsRow["Codigo"]),20,"L");
        $sTicketTexto.="\n".textoTicket(floatval($xProdsRow["Cantidad"]) . " x",6,"L")." ";
        $sTicketTexto.=textoTicket("$" . number_format($xProdsRow["PrecioUnitario"], 2),10,"R")." ";
        $sTicketTexto.=textoTicket("$" . number_format($nSubtotalLinea, 2),13,"R");

    } else if ($bDescripcion == "0" && $bCodigo == "1" && $bPrecioUnitario == "0") {
        $pdf->MultiCell(15, 5, floatval($xProdsRow["Cantidad"]), 0, '0', 1, 0, '', '', true);
        $pdf->MultiCell(35, 5, convTexto($xProdsRow["Codigo"]), 0, 'L', 1, 0, '', '', true);
        $pdf->MultiCell(30, 5, "$" . number_format($nSubtotalLinea, 2), 0, 'R', 1, 0, '', '', true);
        $pdf->Ln(4);
        
        $sTicketTexto.="\n".textoTicket(floatval($xProdsRow["Cantidad"]) . " x",6,"L")." ";
        $sTicketTexto.=textoTicket(convTexto($xProdsRow["Codigo"]),14,"L")." ";
        $sTicketTexto.=textoTicket("$" . number_format($nSubtotalLinea, 2),20,"L---------");
                
    }
    $pdf->Ln(4);
    $iProd++;
}
mysql_free_result($xProdsRS);

#--- Totales...
$pdf->Ln(4);
$pdf->MultiCell(180, 5, '      --------------------------------------', 0, 'L', 1, 0, '', '', true);
$sTicketTexto.="\n".textoTicket('--------------------------------------',$cMaxLong,"R");

#--- Subtotal
$pdf->Ln(4);
$pdf->MultiCell(45, 5, 'SUB', 0, 'R', 1, 0, '', '', true);
$pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
$pdf->MultiCell(30, 5, '$ ' . number_format($nSubtotal, 2), 0, 'R', 1, 0, '', '', true);
$pdf->Ln(4);

$nImpuestos = $nIVA + $nImp2 + $nImp3 + $nImp4 + $nImp5;

#--- IVA
if ($bIVA == 1) {
	$sTicketTexto.="\n".textoTicket('SUB  =',20,"R");
	$sTicketTexto.=textoTicket('$ ' . number_format($nSubtotal, 2),20,"R");
    $pdf->MultiCell(45, 5, 'IVA', 0, 'R', 1, 0, '', '', true);
    $pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
    $pdf->MultiCell(30, 5, '$ ' . number_format($nIVA, 2), 0, 'R', 1, 0, '', '', true);
    $pdf->Ln(4);
    
    $sTicketTexto.="\n".textoTicket('IVA  =',20,"R");
    $sTicketTexto.=textoTicket('$ ' . number_format($nIVA, 2),20,"R");
}

#--- IMPUESTO 2
if ($bImp2 == 1) {
    $pdf->MultiCell(45, 5, $sNombreImp2, 0, 'R', 1, 0, '', '', true);
    $pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
    $pdf->MultiCell(30, 5, '$ ' . number_format($nImp2, 2), 0, 'R', 1, 0, '', '', true);
    $pdf->Ln(4);

    $sTicketTexto.="\n".textoTicket($sNombreImp2.'  =',20,"R");
    $sTicketTexto.=textoTicket('$ ' . number_format($nImp2, 2),20,"R");
}

#--- IMPUESTO 3
if ($bImp3 == 1) {
    $pdf->MultiCell(45, 5, $sNombreImp3, 0, 'R', 1, 0, '', '', true);
    $pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
    $pdf->MultiCell(30, 5, '$ ' . number_format($nImp3, 2), 0, 'R', 1, 0, '', '', true);
    $pdf->Ln(4);

    $sTicketTexto.="\n".textoTicket($sNombreImp3.'  =',20,"R");
    $sTicketTexto.=textoTicket('$ ' . number_format($nImp3, 2),20,"R");
}

#--- IMPUESTO 4
if ($bImp4 == 1) {
    $pdf->MultiCell(45, 5, $sNombreImp4, 0, 'R', 1, 0, '', '', true);
    $pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
    $pdf->MultiCell(30, 5, '$ ' . number_format($nImp4, 2), 0, 'R', 1, 0, '', '', true);
    $pdf->Ln(4);

    $sTicketTexto.="\n".textoTicket($sNombreImp4.'  =',20,"R");
    $sTicketTexto.=textoTicket('$ ' . number_format($nImp4, 2),20,"R");
}

#--- IMPUESTO 5
if ($bImp5 == 1) {
    $pdf->MultiCell(45, 5, $sNombreImp5, 0, 'R', 1, 0, '', '', true);
    $pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
    $pdf->MultiCell(30, 5, '$ ' . number_format($nImp5, 2), 0, 'R', 1, 0, '', '', true);
    $pdf->Ln(4);

    $sTicketTexto.="\n".textoTicket($sNombreImp5.'  =',20,"R");
    $sTicketTexto.=textoTicket('$ ' . number_format($nImp5, 2),20,"R");
}

#--- TOTAL
$pdf->Ln(4);
$pdf->MultiCell(45, 5, 'TOTAL', 0, 'R', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
$pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
$pdf->MultiCell(30, 5, '$ ' . number_format($nTotal-$nCredito, 2), 0, 'R', 1, 0, '', '', true);
$pdf->Ln(4);

$sTicketTexto.="\n".textoTicket('TOTAL  =',20,"R");
$sTicketTexto.=textoTicket('$ ' . number_format($nTotal-$nCredito, 2),20,"R")."\n";

#MultiCell ($w, $h, $txt, $border=0, $align='J', $fill=false, $ln=1, $x='', $y='', $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='T', $fitcell=false)
#--- TIPOS DE PAGO USADOS
$nCredito = 0;
$nTotalPagado = 0;
$SQL_query = "SELECT p.PagoReal PagoReal, p.TipoCambio, tp.Nombre, tp.ID  FROM pagos p INNER JOIN tipospago tp ON (p.fkTipoPago=tp.ID) 
            WHERE fkTicket='" . $nTicketID . "' group by TipoCambio, Nombre
            ORDER BY  Nombre ASC";
		//	 $pdf->MultiCell(30, 5, $SQL_query, 0, 'R', 1, 0, '', '', true);$pdf->Ln(4); $pdf->Ln(4); $pdf->Ln(4); $pdf->Ln(4);
			  
$xTiposPagosRS = mysql_query($SQL_query) or die(mysql_error());
while ($xTiposPagosRow = mysql_fetch_array($xTiposPagosRS)) {
    $sTipoNombre = $xTiposPagosRow["Nombre"];
    $nEstePago = round($xTiposPagosRow["PagoReal"] * $xTiposPagosRow["TipoCambio"], 2);
    $nTotalPagado+=$nEstePago;
   if (str_replace("é","e",$sTipoNombre) == "Credito" ) {
        $nCredito = $nEstePago;
    }
    if ($bCambio == 1 ) {
        $pdf->Ln(4);
        $pdf->MultiCell(45, 5, convTexto($sTipoNombre), 0, 'R', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
        $pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
        $pdf->MultiCell(30, 5, '$ ' . number_format($nEstePago, 2), 0, 'R', 1, 0, '', '', true);

        $sTicketTexto.="\n".textoTicket(convTexto($sTipoNombre).'  =',20,"R");
        $sTicketTexto.=textoTicket('$ ' . number_format($nEstePago, 2),20,"R");
        
        if (floatval($xTiposPagosRow["TipoCambio"]) != 1) {
            $pdf->Ln(4);
            $pdf->MultiCell(65, 5, "( $ " . number_format($xTiposPagosRow["PagoReal"], 2) . " x " . number_format($xTiposPagosRow["TipoCambio"], 2) . " = $ " . number_format($nEstePago, 2) . " )", 0, 'R', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
            
            $sTicketTexto.="\n".textoTicket("( $ " . number_format($xTiposPagosRow["PagoReal"], 2) . " x " . number_format($xTiposPagosRow["TipoCambio"], 2) . " = $ " . number_format($nEstePago, 2) . " )",$cMaxLong,"R");
            
        }
    }
}
mysql_free_result($xTiposPagosRS);

if ($nCredito > 0) {
	$pdf->MultiCell(45, 5, 'TOTAL DE CREDITO', 0, 'R', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
	$pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
	$pdf->MultiCell(30, 5, '$ ' . number_format($nCredito, 2), 0, 'R', 1, 0, '', '', true);
	$pdf->Ln(4);
	$sTicketTexto.="\n\n".textoTicket(convTexto('TOTAL DE CRÉDITO ='),20,"R");
	$sTicketTexto.=textoTicket('$ ' . number_format($nCredito, 2),20,"R")."\n";
	$ncopias=$ncopiascredito;//imprime la cantidad de copias elegias para el ticket de credito, si no las copias del ticket de compra
}
$pdf->Ln(4);

if ($bCambio == 1 && $nFolio==0) {
    #--- Total pagado
    $pdf->Ln(4);
    $pdf->MultiCell(45, 5, "Total", 0, 'R', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
    $pdf->MultiCell(30, 5, '$ ' . number_format($nTotalPagado, 2), 0, 'R', 1, 0, '', '', true);
    $pdf->Ln(4);

    $sTicketTexto.="\n".textoTicket('Total  =',20,"R");
    $sTicketTexto.=textoTicket('$ ' . number_format($nTotalPagado, 2),20,"R");


    #--- Cambio	
    $pdf->MultiCell(45, 5, "Cambio", 0, 'R', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
    $pdf->MultiCell(30, 5, '$ ' . number_format($nTotalPagado - ($nSubtotal + $nImpuestos), 2), 0, 'R', 1, 0, '', '', true);
    $pdf->Ln(4);
    $pdf->Ln(4);
    $pdf->Ln(4);

    $sTicketTexto.="\n".textoTicket('Cambio =',20,"R");
    $sTicketTexto.=textoTicket('$ ' . number_format($nTotalPagado - ($nSubtotal + $nImpuestos), 2),20,"R");
    $sTicketTexto.="\n\n";
}

#--- Total en letra
if ($bTotalEnLetra == 1) {
    $V = new EnLetras();
    $sTotalEnLetras = rtrim($V->ValorEnLetras($nSubtotal + $nIVA, "PESOS"));
    $pdf->MultiCell(190, 5, ucwords(strtolower($sTotalEnLetras)), 0, 'R', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $pdf->Ln(4);
    $sTicketTexto.="\n".textoTicket(ucwords(strtolower($sTotalEnLetras)),$cMaxLong,"L");
}
 #--- Total ahorrado por descuentos o mayoreo
 if ($nDescuento  >0) {
    $pdf->MultiCell(45, 5, "Usted Ahorro", 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $pdf->MultiCell(5, 5, '=', 0, 'L', 1, 0, '', '', true);
    $pdf->MultiCell(30, 5, '$ ' . number_format($nDescuento, 2), 0, 'L', 1, 0, '', '', true);
    $pdf->Ln(4);

    $sTicketTexto.=textoTicket('Usted Ahorro  =',20,"R");
    $sTicketTexto.=textoTicket('$ ' . number_format($nDescuento, 2),20,"R");
}
$pdf->MultiCell(190, 5, '---------------------------------------------------------', 0, 'L', 1, 0, '', '', true);
$pdf->Ln(4);
$sTicketTexto.="\n".textoTicket('---------------------------------------------------------',$cMaxLong,"R");

if ($bTotalDeProductos == 1) {
    $pdf->MultiCell(190, 5, $nArticulos . " Producto(s)", 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $pdf->Ln(4);
    $sTicketTexto.="\n".textoTicket($nArticulos . " Producto(s)",$cMaxLong,"R");
}
$pdf->Ln(4);
$sTicketTexto.="\n";

//mensaje inicial
if ($bMostrarMensajeFinal == 1) {
    $pdf->MultiCell(180, 5, $sMensajeFinal, 0, 'L', 1, 0, '', '', true);
    $pdf->Ln(4);
    $sTicketTexto.="\n".textoTicket($sMensajeFinal,$cMaxLong,"C");
}
#--- Debo y pagaré
if ($nCredito > 0) {
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, '=========================================', 0, 'L', 1, 0, '', '', true);
    $sTicketTexto.="\n".textoTicket('=========================================',$cMaxLong,"L");
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, convTexto("Debo y pagaré a la orden de "), 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket(convTexto("Debo y pagaré a la orden de"),$cMaxLong,"L");
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, $sNombreFiscal, 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket($sNombreFiscal,$cMaxLong,"L");
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, "en esta ciudad o cualquier otra", 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket("en esta ciudad o cualquier otra",$cMaxLong,"L");
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, "la cantidad de: $ " . number_format($nCredito, 2), 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket("la cantidad de: $ " . number_format($nCredito, 2),$cMaxLong,"L")."\n";
    $pdf->Ln(4);
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, "Valor de la mercancia que he recibido", 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket("Valor de la mercancia que he recibido",$cMaxLong,"L");
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, convTexto("a mi entera satisfacción. Este pagaré"), 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket(convTexto("a mi entera satisfacción. Este pagaré"),$cMaxLong,"L");
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, "es mercantil y se rige por las", 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket("es mercantil y se rige por las",$cMaxLong,"L");
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, "disposiciones de la ley general de", 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket("disposiciones de la ley general de",$cMaxLong,"L");
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5,convTexto("títulos y operaciones de crédito."), 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket(convTexto("títulos y operaciones de crédito."),$cMaxLong,"L")."\n\n\n\n";
    $pdf->Ln(4);
    $pdf->Ln(4);
    $pdf->Ln(4);
    $pdf->Ln(4);
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, "------------------------", 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket("------------------------",$cMaxLong,"C");
    $pdf->Ln(4);
    $pdf->MultiCell(190, 5, "Firma de Recibido", 0, 'L', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket("Firma de Recibido",$cMaxLong,"C");
    $pdf->Ln(4);
}

if ($pagina == "1") {
    $pdf->MultiCell(190, 5, "www.cajafacil.com", 0, 'L', 1, 0, '27', '', true, 0, false, false, 0, 'T', false);
    $sTicketTexto.="\n".textoTicket("www.cajafacil.com",$cMaxLong,"R");
    $pdf->Ln(4);
}
//imagen publicidad
/*if ($bMostrarPublicidad == 1 && $bImageIndex == 1 && trim($sPublicidad) != "") {

    $sExt = strafter($sPublicidad, '.');
    $pdf->Image('fotos/' . $sPublicidad, '', '', 100, '', $sExt, '', 'C', false, 100, '', false, false, 0, true, false, true);
    $pdf->Ln(4);
    $pdf->Ln(4);
}*/
	$SQL_query = "SELECT sum(PagoReal) PagoReal  FROM pagos p where fkTipoPago not in (3,5,6,7) and  fkTicket='" . $nTicketID . "'";
		//	 $pdf->MultiCell(30, 5, $SQL_query, 0, 'R', 1, 0, '', '', true);$pdf->Ln(4); $pdf->Ln(4); $pdf->Ln(4); $pdf->Ln(4);
	$abrir=0;			  
	$xTiposPagosRS = mysql_query($SQL_query) or die(mysql_error());
	if ($xTiposPagosRow = mysql_fetch_array($xTiposPagosRS)) {
		if(floatval($xTiposPagosRow["PagoReal"])>0)
			$abrir=1;		
	}
//  $pdf->MultiCell(190, 5, chr(27). chr(112). chr(48). chr(55). chr(121), 0, 'R', 1, 0, '', '', true, 0, false, false, 0, 'T', false);
$pdf->Ln(4);
$pdf->Ln(4);
$pdf->Ln(4);
// ---------------------------------------------------------
//Close and output PDF document
if (false) {
    $sFileMethod = "I";
    if ($bReimpresion) {
        $sFileMethod = "F";
    }
}
$sFileMethod = "F";

$pdf->Output(realpath('impresiones') . '/ticket_' . $nTicketID . '.pdf', $sFileMethod);
$myfile = fopen(realpath('impresiones') . '/ticket_' . $nTicketID . '.txt', "w") ;
$txt =  textoSimple($sTicketTexto);
fwrite($myfile, $txt);
fclose($myfile);

$aResultado["Error"] = "";
$aResultado["Dummy"] = "1";
$aResultado["TicketTexto"] = textoSimple($sTicketTexto);
$aResultado["copias"] = $ncopias;
$aResultado["bImagen"] =$bMostrarImagen;
$aResultado["bPublicidad"] =$bMostrarPublicidad;
$aResultado["bbarcode"] =$bbarcode;
$aResultado["Folio"] =$sFolio;
$aResultado["abrir"] =$abrir;
$aResultado["Imagen"] =$sImagen;
$aResultado["Publicidad"] =$sPublicidad;
$aResultado = array_map('utf8_encode', $aResultado);
print json_encode($aResultado);
