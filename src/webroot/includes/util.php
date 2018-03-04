<?
#---  - simplifica caracteres especiales
function textoSimple($sTexto) {
    $unwanted_array = array(    '?'=>'S', '?'=>'s', '?'=>'Z', '?'=>'z', '¿'=>'A', '¡'=>'A', '¬'=>'A', '√'=>'A', 'ƒ'=>'A', '≈'=>'A', '∆'=>'A', '«'=>'C', '»'=>'E', '…'=>'E',
                                ' '=>'E', 'À'=>'E', 'Ã'=>'I', 'Õ'=>'I', 'Œ'=>'I', 'œ'=>'I', '—'=>'N', '“'=>'O', '”'=>'O', '‘'=>'O', '’'=>'O', '÷'=>'O', 'ÿ'=>'O', 'Ÿ'=>'U',
                                '⁄'=>'U', '€'=>'U', '‹'=>'U', '›'=>'Y', 'ﬁ'=>'B', 'ﬂ'=>'Ss', '‡'=>'a', '·'=>'a', '‚'=>'a', '„'=>'a', '‰'=>'a', 'Â'=>'a', 'Ê'=>'a', 'Á'=>'c',
                                'Ë'=>'e', 'È'=>'e', 'Í'=>'e', 'Î'=>'e', 'Ï'=>'i', 'Ì'=>'i', 'Ó'=>'i', 'Ô'=>'i', ''=>'o', 'Ò'=>'n', 'Ú'=>'o', 'Û'=>'o', 'Ù'=>'o', 'ı'=>'o',
                                'ˆ'=>'o', '¯'=>'o', '˘'=>'u', '˙'=>'u', '˚'=>'u', '˝'=>'y', '˛'=>'b', 'ˇ'=>'y' );
    #$str = strtr( $sTexto, $unwanted_array );    
    return strtr( $sTexto, $unwanted_array );
}

#--- CONVIERTE TEXTO A FORMATO DE TICKET
#---  - asegura que el texto no sobrepase el ancho del ticket
function textoTicket($sTexto, $nLongitudMax, $sJustificacion) {
    $sTextoNuevo = $sTexto;
    $nLong = strlen($sTextoNuevo);
        while (strlen($sTextoNuevo)<$nLongitudMax) {
            if ($sJustificacion=="L") 
                $sTextoNuevo.=" ";
            else if ($sJustificacion=="R") 
                $sTextoNuevo=" ".$sTextoNuevo;
            else if ($sJustificacion=="C") 
                $sTextoNuevo=" ".$sTextoNuevo." ";
        }
    $sTextoNuevo=substr($sTextoNuevo,0, $nLongitudMax);
    return $sTextoNuevo;
}

function convTexto($pTexto) {
    return iconv("UTF-8", "ISO-8859-1",$pTexto);
}
function convTexto2($pTexto) {
    return iconv("ISO-8859-1","UTF-8",$pTexto);
}

function fechahumana($fecha) {
    return date('d/m/Y', strtotime($fecha));
}

function limpiaCantidad($pCantidad) {
    #--- quita coma a la cantidad para que sea numerico
    return str_replace(",","",$pCantidad);
}

function humanFKey($pKeyValue) {
    $aHumanFKeys= array(
        "112" => "F1",
        "113" => "F2",
        "114" => "F3",
        "115" => "F4",
        "116" => "F5",
        "117" => "F6",
        "118" => "F7",
        "119" => "F8",
        "120" => "F9",
        "121" => "F10",
        "122" => "F11",
        "123" => "F12",
        "1783" => "Ctrl+S",
    );
    
    return $aHumanFKeys[$pKeyValue];
}


function scriptname() {
    return basename($_SERVER['SCRIPT_NAME'], '.php');
}

function debugArray($pArray) {
    print "<PRE>";
    print_r($pArray);
    print "</PRE>";
}

function redirect($pUrl) {
    echo "<script language='JavaScript'>window.location='".$pUrl."';</script>";
}

function atras($pUrl) {
    echo "<script language='JavaScript'> window.history.back();</script>";
}

//manda mensaje
function aviso($pmensaje)
{	
        echo ("<script language=JavaScript>alert('".$pmensaje."');</script>");
}

//manda mensaje y va a otra pantalla
function aviso_accion($pmensaje,$pUrl)
{	
        echo ("<script language=JavaScript>alert('".$pmensaje."'); window.location='".$pUrl."';</script>");
}
//manda mensaje y regesa a pantalla anterior
function aviso_atras($pmensaje)
{	
        echo ("<script language=JavaScript>alert('".$pmensaje."'); window.history.back();</script>");
}

function aviso_cierra($pmensaje)
{	
        echo ("<script language=JavaScript>alert('".$pmensaje."'); window.close();</script>");
}


//cambia href de un link pasando id y neuvo link
function cambiarlink($pid,$purl)
{

        echo ("<script language=JavaScript>   document.getElementById('".$pid."').href='".$purl."'; </script>");
       
}

//muestra un id con display: block
function mostrar($pid)
{
        echo ("<script language=JavaScript>  document.getElementById('".$pid."').style.display='block';</script>");
}
//muestra un id con visibility: visible
function mostrar2($pid)
{
        echo ("<script language=JavaScript>  document.getElementById('".$pid."').style.visibility='visible';</script>");
}

//oculta un id con display: block
function ocultar($pid)
{
        echo ("<script language=JavaScript>  document.getElementById('".$pid."').style.display='none';</script>");
}
//TRAE LA EMPRESA ACTIVA
function empresaactiva()
{
	$SQL_query = "SELECT Nombre FROM empresa ";
	#print "<P>".$SQL_query;
	$xCatalogRS = mysql_query($SQL_query) ;
	if ($xCatalogRow = mysql_fetch_array($xCatalogRS)) {
		$nombre=$xCatalogRow["Nombre"];
		echo $nombre;
	}
	else
	{
	echo "CAJA F√ÅCIL";	
	}
	
}

//TRAE el LOGO DE LA EMPRESA ACTIVA
function logoempresaactiva($mainroot)
{
	$SQL_query = "SELECT logo FROM empresa ";
	//print "<P>".$SQL_query;
	$xCatalogRS = mysql_query($SQL_query) ;
	if ($xCatalogRow = mysql_fetch_array($xCatalogRS)) {
		$logo=$xCatalogRow["logo"];
		if(trim($logo)=="")
		{
			echo '<img src="'.$mainroot.'images/logo_nuevogral.png"  border="0" />';
		}
		else
		{
			echo '<img src="'.$mainroot.'images/'.$logo.'" height="155" border="0" />';
		}
	}
	else
	{
		echo '<img src="'.$mainroot.'images/logo_nuevogral.png"  border="0" />';	
	}
	
}
//TRAE el slogan DE LA EMPRESA ACTIVA
function sloganempresaactiva($mainroot)
{
	$SQL_query = "SELECT slogan FROM empresa ";
	//print "<P>".$SQL_query;
	$xCatalogRS = mysql_query($SQL_query) ;
	if ($xCatalogRow = mysql_fetch_array($xCatalogRS)) {
		$slogan=$xCatalogRow["slogan"];
		if(trim($slogan)=="")
		{
			echo '<img src="'.$mainroot.'images/slogan_gral.png"  border="0" />';
		}
		else
		{
			echo '<img src="'.$mainroot.'images/'.$slogan.'" border="0" />';
		}
	}
	else
	{
		echo '<img src="'.$mainroot.'images/slogan_gral.png"  border="0" />';	
	}
	
}
//TRAE el LOGO DE LA EMPRESA ACTIVA
function logoempresaactivapv($mainroot)
{
	$SQL_query = "SELECT logo FROM empresa ";
	//print "<P>".$SQL_query;
	$xCatalogRS = mysql_query($SQL_query) ;
	if ($xCatalogRow = mysql_fetch_array($xCatalogRS)) {
		$logo=$xCatalogRow["logo"];
		if(trim($logo)=="")
		{
			echo '<img src="'.$mainroot.'images/logo_gral.png"  border="0" />';
		}
		else
		{
			echo '<img src="'.$mainroot.'images/'.$logo.'" height="111" border="0" />';
		}
	}
	else
	{
		echo '<img src="'.$mainroot.'images/logo_gral.png"  border="0" />';	
	}
	
}


//TRAE LA SUCURSAL DEL USUARIO LOGEADO, SI HAY MAS DE UNA SUCURSAL LO IMPRIME
function sucursalusuario()
{	
	$SQL_query2 = "SELECT Nombre FROM sucursales WHERE ID<>0 ";
	//print "<P>".$SQL_query2;
	$xCatalogRS2 = mysql_query($SQL_query2) ;
	$filas = mysql_num_rows($xCatalogRS2);
	if ($xCatalogRow2 = mysql_fetch_array($xCatalogRS2)) {
		if($filas>1 )	
		{
			$SQL_query2 = "SELECT Nombre FROM sucursales WHERE sucursales.ID='".$_SESSION["Sucursal"]."' ";
			//print "<P>".$SQL_query2;
			$xCatalogRS2 = mysql_query($SQL_query2) ;
			$filas = mysql_num_rows($xCatalogRS2);
			if ($xCatalogRow2 = mysql_fetch_array($xCatalogRS2)) {
				$ssucursal=$xCatalogRow2["Nombre"];	
				return "<span class='sucursal'>".$ssucursal."</span>";			
			}
			else
			{
			return "";	
			}
		}
	}
}

//TRAE LA SUCURSAL DEL USUARIO LOGEADO, SI ES TODOS DEBE PONER EL CAMPO SUCURSAL EN TODAS LAS PANTALLAS
function sucursalusuario2()
{	
	$SQL_query2 = "SELECT 1 admin FROM usuarios WHERE usuarios.ID='".$_SESSION["AdminUser"]."' and  usuarios.fkSucursal=0 ";
	//print "<P>".$SQL_query2;
	$xCatalogRS2 = mysql_query($SQL_query2) ;
	if ($xCatalogRow2 = mysql_fetch_array($xCatalogRS2)) {
		return 0;
	}
	else
	{
		$SQL_query3 = "SELECT ID FROM sucursales WHERE sucursales.ID='".$_SESSION["Sucursal"]."' ";
		//print "<P>".$SQL_query2;
		$xCatalogRS3 = mysql_query($SQL_query3) ;
		while ($xCatalogRow3 = mysql_fetch_array($xCatalogRS3)) {
			$ssucursal=$xCatalogRow3["ID"];	
			return $ssucursal;
		}
	}
}

//TRAE LA SUCURSAL de la caja, SI ES TODOS DEBE PONER EL CAMPO SUCURSAL EN TODAS LAS PANTALLAS
function sucursalcaja()
{	

	$SQL_query2 = "SELECT FkSucursal FROM cajas WHERE cajas.ID='".$_SESSION["fkCaja"]."'";
	//print "<P>".$SQL_query2;
	$xCatalogRS2 = mysql_query($SQL_query2) ;
	if ($xCatalogRow2 = mysql_fetch_array($xCatalogRS2)) {
		return $xCatalogRow2["FkSucursal"];
	}

}
//muestra el div de sucursal, antes chec con tora funcion si el usuario puede ver todas
function mostrarsucursal()
{
	        echo ("<script language=JavaScript> if (document.getElementById('DivSucursal')!=null) { document.getElementById('DivSucursal').style.display='block';}</script>");
}

//muestra el div de sucursal2, antes chec con tora funcion si el usuario puede ver todas
function mostrarsucursal2()
{
	        echo ("<script language=JavaScript>  document.getElementById('DivSucursal2').style.display='block';</script>");
}
//checa si el usuario logeado tiene permiso para la pantalla o el boton
function permiso($pPantalla)
{
	$SQL_query2 = "SELECT 1 admin FROM usuarios WHERE usuarios.ID='".$_SESSION["AdminUser"]."' and  usuarios.fkRol=1 ";
	//print "<P>".$SQL_query2;
	$xCatalogRS2 = mysql_query($SQL_query2) ;
	$filas = mysql_num_rows($xCatalogRS2);
	if ($xCatalogRow2 = mysql_fetch_array($xCatalogRS2)) {
		$pPantalla=$xCatalogRow2["admin"];	
		return $pPantalla;
	}	
	else
	{   
		$SQL_query2 = "SELECT atributosusuarios.fkAtributo FROM atributosusuarios, usuarios where usuarios.ID='".$_SESSION["AdminUser"]."' and ( (usuarios.ID=atributosusuarios.fkUsuario and atributosusuarios.fkAtributo='".$pPantalla."') or ( usuarios.fkRol=1))";		
		//print "<P>".$SQL_query2;
		$xCatalogRS2 = mysql_query($SQL_query2) ;
		$filas = mysql_num_rows($xCatalogRS2);
		if ($xCatalogRow2 = mysql_fetch_array($xCatalogRS2)) {
			$pPantalla=$xCatalogRow2["fkAtributo"];	
			return $pPantalla;	
		}
	}
}



//manda mensaje
function ocultarboton($pBoton)
{	

        echo ("<script language=JavaScript> var hlink = document.getElementById('".$pBoton."');
		 hlink.className='botondesabilitado'; 		
		 document.getElementById('".$pBoton."1').href='#'; 
		 	document.getElementById('".$pBoton."1').style.color='#ccc';
		 </script>");
}	

function ocultaryfijar($pid,$pvalue)
{
        echo ("<script language=JavaScript>  document.getElementById('".$pid."').value='".$pvalue."'; document.getElementById('".$pid."').style.display='none';		
		</script>");
}
function abrir($pUrl)
{
	 echo ("<script language=JavaScript>   var win = window.open('".$pUrl."', '_blank');		win.focus();
		</script>");
}

function humanTiming ($time)
{

 $time = time() - $time; // to get the time since that moment

    $tokens = array (
        86400 => 'dia'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }

}

function fechacorta($fecha) {
	return date('Y-m-d', strtotime($fecha));
}

/*cantidad con letras**/
class EnLetras
{
  var $Void = "";
  var $SP = " ";
  var $Dot = ".";
  var $Zero = "0";
  var $Neg = "MENOS";

fUNction ValorEnLetras($x, $Moneda ) 
{
    $s="";
    $Ent="";
    $Frc="";
    $Signo="";
        
    if(floatVal($x) < 0)
     $Signo = $this->Neg . " ";
    else
     $Signo = "";
    
    if(intval(number_format($x,2,'.','') )!=$x) //<- averiguar si tiene decimales
      $s = number_format($x,2,'.','');
    else
      $s = number_format($x,0,'.','');
         $Pto = strpos($s, $this->Dot);
       
    if ($Pto === false)
    {
      $Ent = $s;
      $Frc = $this->Void;
    }
    else
    {
      $Ent = substr($s, 0, $Pto );
      $Frc =  substr($s, $Pto+1);
    }

    if($Ent == $this->Zero || $Ent == $this->Void)
       $s = "CERO ";
    elseif( strlen($Ent) > 7)
    {
       $s = $this->SubValLetra(intval( substr($Ent, 0,  strlen($Ent) - 6))) . 
             "MILLONES " . $this->SubValLetra(intval(substr($Ent,-6, 6)));
    }
    else
    {
      $s = $this->SubValLetra(intval($Ent));
    }

    if (substr($s,-9, 9) == "MILLONES " || substr($s,-7, 7) == "MILL√ìN ")
       $s = $s . "de ";
    $s = $s . $Moneda;

    if($Frc != $this->Void)
    {
        $s = $s . " " . $Frc . "/100";
       //$s = $s . " " . $Frc . "/100";
    }
	else
	{
	$s = $s . " 00/100";
	}
    return ($Signo . $s . " M. N.");   
}

fUNction ValorEnLetras2($x, $Moneda ) 
{
    $s="";
    $Ent="";
    $Frc="";
    $Signo="";
        
    if(floatVal($x) < 0)
     $Signo = $this->Neg . " ";
    else
     $Signo = "";
    
    if(intval(number_format($x,2,'.','') )!=$x) //<- averiguar si tiene decimales
      $s = number_format($x,2,'.','');
    else
      $s = number_format($x,0,'.','');
           $Pto = strpos($s, $this->Dot);
        
    if ($Pto === false)
    {
      $Ent = $s;
      $Frc = $this->Void;
    }
    else
    {
      $Ent = substr($s, 0, $Pto );
      $Frc =  substr($s, $Pto+1);
    }
    if($Ent == $this->Zero || $Ent == $this->Void)
       $s = "CERO ";
    elseif( strlen($Ent) > 7)
    {
       $s = $this->SubValLetra2(intval( substr($Ent, 0,  strlen($Ent) - 6))) . 
             "MILLONES " . $this->SubValLetra2(intval(substr($Ent,-6, 6)));
    }
    else
    {
      $s = $this->SubValLetra2(intval($Ent));
    }

    if (substr($s,-9, 9) == "MILLONES " || substr($s,-7, 7) == "MILL√ìN ")
       $s = $s . "de ";
    $s = $s . $Moneda;
    if($Frc != $this->Void)
    {
       $s = $s . " ( " . $Frc . "/100)";
       //$s = $s . " " . $Frc . "/100";
    }
    return ($Signo . $s );
   }

fUNction SubValLetra($numero) 
{    $Ptr="";
    $n=0;
    $i=0;
    $x ="";
    $Rtn ="";
    $Tem ="";

    $x = trim("$numero");
    $n = strlen($x);
    $Tem = $this->Void;
    $i = $n;    

    while( $i > 0)
    {
       $Tem = $this->Parte(intval(substr($x, $n - $i, 1). 
                           str_repeat($this->Zero, $i - 1 )));
       If( $Tem != "CERO" )
          $Rtn .= $Tem . $this->SP;
       $i = $i - 1;
    }    
    //--------------------- GoSub FiltroMIL ------------------------------
    $Rtn=str_replace(" MIL MIL", " UN MIL", $Rtn );
    while(1)
    {
       $Ptr = strpos($Rtn, "MIL ");       
       If(!($Ptr===false))
       {
          If(! (strpos($Rtn, "MIL ",$Ptr + 1) === false ))
            $this->ReplaceStringFrom($Rtn, "MIL ", "", $Ptr);
          else
           break;
       }
       else break;
    }
    //--------------------- GoSub FiltroCIENto ------------------------------

    $Ptr = -1;
    do{
       $Ptr = strpos($Rtn, "CIEN ", $Ptr+1);
       if(!($Ptr===false))
       {
          $Tem = substr($Rtn, $Ptr + 5 ,1);
          if( $Tem == "M" || $Tem == $this->Void)
             ;
          else          
             $this->ReplaceStringFrom($Rtn, "CIEN", "CIENTO", $Ptr);
       }
    }while(!($Ptr === false));

    //--------------------- FiltroEspeciales ------------------------------
    $Rtn=str_replace("DIEZ UN", "ONCE", $Rtn );
    $Rtn=str_replace("DIEZ DOS", "DOCE", $Rtn );
    $Rtn=str_replace("DIEZ TRES", "TRECE", $Rtn );
    $Rtn=str_replace("DIEZ CUATRO", "CATORCE", $Rtn );
    $Rtn=str_replace("DIEZ CINCO", "QUINCE", $Rtn );
    $Rtn=str_replace("DIEZ SEIS", "DIECISEIS", $Rtn );
    $Rtn=str_replace("DIEZ SIETE", "DIECISIETE", $Rtn );
    $Rtn=str_replace("DIEZ OCHO", "DIECIOCHO", $Rtn );
    $Rtn=str_replace("DIEZ NUEVE", "DIECINUEVE", $Rtn );
    $Rtn=str_replace("VEINTE UN", "VEINTIUN", $Rtn );
    $Rtn=str_replace("VEINTE DOS", "VEINTIDOS", $Rtn );
    $Rtn=str_replace("VEINTE TRES", "VEINTITRES", $Rtn );
    $Rtn=str_replace("VEINTE CUATRO", "VEINTICUATRO", $Rtn );
    $Rtn=str_replace("VEINTE CINCO", "VEINTICINCO", $Rtn );
    $Rtn=str_replace("VEINTE SEIS", "VEINTISEIS", $Rtn );
    $Rtn=str_replace("VEINTE SIETE", "VEINTISIETE", $Rtn );
    $Rtn=str_replace("VEINTE OCHO", "VEINTIOCHO", $Rtn );
    $Rtn=str_replace("VEINTE NUEVE", "VEINTINUEVE", $Rtn );

    //--------------------- FiltroUN ------------------------------
    If(substr($Rtn,0,1) == "M") $Rtn = "UN " . $Rtn;
    //--------------------- Adicionar Y ------------------------------
    for($i=65; $i<=88; $i++)
    {
      If($i != 77)
         $Rtn=str_replace("A " . Chr($i), "* Y " . Chr($i), $Rtn);
    }
    $Rtn=str_replace("*", "A" , $Rtn);
    return($Rtn);
}

fUNction SubValLetra2($numero) 
{
    $Ptr="";
    $n=0;
    $i=0;
    $x ="";
    $Rtn ="";
    $Tem ="";
    $x = trim("$numero");
    $n = strlen($x);
    $Tem = $this->Void;
    $i = $n;    
    while( $i > 0)
    {
       $Tem = $this->Parte2(intval(substr($x, $n - $i, 1). 
                           str_repeat($this->Zero, $i - 1 )));
       If( $Tem != "CERO" )
          $Rtn .= $Tem . $this->SP;
       $i = $i - 1;
    }    

    //--------------------- GoSub FiltroMIL ------------------------------
    $Rtn=str_replace(" MIL MIL", " UN MIL", $Rtn );
    while(1)
    {
       $Ptr = strpos($Rtn, "MIL ");       
       If(!($Ptr===false))
       {
          If(! (strpos($Rtn, "MIL ",$Ptr + 1) === false ))
            $this->ReplaceStringFrom($Rtn, "MIL ", "", $Ptr);
          Else
           break;
       }
       else break;
    }
    //--------------------- GoSub FiltroCIENto ------------------------------

    $Ptr = -1;
    do{
       $Ptr = strpos($Rtn, "CIEN ", $Ptr+1);
       if(!($Ptr===false))
       {
          $Tem = substr($Rtn, $Ptr + 5 ,1);
          if( $Tem == "M" || $Tem == $this->Void)
             ;
          else          
             $this->ReplaceStringFrom($Rtn, "CIEN", "CIENto", $Ptr);
       }
    }while(!($Ptr === false));

    //--------------------- FiltroEspeciales ------------------------------
    $Rtn=str_replace("DIEZ UNO", "ONCE", $Rtn );
    $Rtn=str_replace("DIEZ DOS", "DOCE", $Rtn );
    $Rtn=str_replace("DIEZ TRES", "TRECE", $Rtn );
    $Rtn=str_replace("DIEZ CUATRO", "CATORCE", $Rtn );
    $Rtn=str_replace("DIEZ CINCO", "QUINCE", $Rtn );
    $Rtn=str_replace("DIEZ SEIS", "DIECISEIS", $Rtn );
    $Rtn=str_replace("DIEZ SIETE", "DIECISIETE", $Rtn );
    $Rtn=str_replace("DIEZ OCHO", "DIECIOCHO", $Rtn );
    $Rtn=str_replace("DIEZ NUEVE", "DIECINUEVE", $Rtn );
    $Rtn=str_replace("VEINTE UN", "VEINTIUN", $Rtn );
    $Rtn=str_replace("VEINTE DOS", "VEINTIDOS", $Rtn );
    $Rtn=str_replace("VEINTE TRES", "VEINTITRES", $Rtn );
    $Rtn=str_replace("VEINTE CUATRO", "VEINTICUATRO", $Rtn );
    $Rtn=str_replace("VEINTE CINCO", "VEINTICINCO", $Rtn );
    $Rtn=str_replace("VEINTE SEIS", "VEINTIse√≠s", $Rtn );
    $Rtn=str_replace("VEINTE SIETE", "VEINTISIETE", $Rtn );
    $Rtn=str_replace("VEINTE OCHO", "VEINTIOCHO", $Rtn );
    $Rtn=str_replace("VEINTE NUEVE", "VEINTINUEVE", $Rtn );
    //--------------------- FiltroUN ------------------------------
    If(substr($Rtn,0,1) == "M") $Rtn = "UN " . $Rtn;
    //--------------------- Adicionar Y ------------------------------
    for($i=65; $i<=88; $i++)
    {
      If($i != 77)
         $Rtn=str_replace("A " . Chr($i), "* Y " . Chr($i), $Rtn);
    }
    $Rtn=str_replace("*", "A" , $Rtn);
    return($Rtn);
}

fUNction ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr)
{
  $x = substr($x, 0, $Ptr)  . $NewWrd . substr($x, strlen($OldWrd) + $Ptr);
}

fUNction Parte($x)
{
    $Rtn='';
    $t='';
    $i='';
    Do
    {
      switch($x)
      {
         Case 0:  $t = "CERO";break;
         Case 1:  $t = "UN";break;
         Case 2:  $t = "DOS";break;
         Case 3:  $t = "TRES";break;
         Case 4:  $t = "CUATRO";break;
         Case 5:  $t = "CINCO";break;
         Case 6:  $t = "SEIS";break;
         Case 7:  $t = "SIETE";break;
         Case 8:  $t = "OCHO";break;
         Case 9:  $t = "NUEVE";break;
         Case 10: $t = "DIEZ";break;
         Case 20: $t = "VEINTE";break;
         Case 30: $t = "TREINTA";break;
         Case 40: $t = "CUARENTA";break;
         Case 50: $t = "CINCUENTA";break;
         Case 60: $t = "SESENTA";break;
         Case 70: $t = "SETENTA";break;
         Case 80: $t = "OCHENTA";break;
         Case 90: $t = "NOVENTA";break;
         Case 100: $t = "CIEN";break;
         Case 200: $t = "DOSCIENTOS";break;
         Case 300: $t = "TRESCIENTOS";break;
         Case 400: $t = "CUATROCIENTOS";break;
         Case 500: $t = "QUINIENTOS";break;
         Case 600: $t = "SEISCIENTOS";break;
         Case 700: $t = "SETECIENTOS";break;
         Case 800: $t = "OCHOCIENTOS";break;
         Case 900: $t = "NOVECIENTOS";break;
         Case 1000: $t = "MIL";break;
         Case 1000000: $t = "MILL√ìN";break;
      }

      If($t == $this->Void)
      {
        $i = $i + 1;
        $x = $x / 1000;
        If($x== 0) $i = 0;
      }
      else
         break;
           
    }while($i != 0);   
    $Rtn = $t;
    Switch($i)
    {
       Case 0: $t = $this->Void;break;
       Case 1: $t = " MIL";break;
       Case 2: $t = " MILLONES";break;
       Case 3: $t = " BILLONES";break;
    }
    return($Rtn . $t);
}

fUNction Parte2($x)
{
    $Rtn='';
    $t='';
    $i='';
    Do
    {
      switch($x)
      {
         Case 0:  $t = "CERO";break;
         Case 1:  $t = "UNO";break;
         Case 2:  $t = "DOS";break;
         Case 3:  $t = "TRES";break;
         Case 4:  $t = "CUATRO";break;
         Case 5:  $t = "CINCO";break;         
		 Case 6:  $t = "SEIS";break;
         Case 7:  $t = "SIETE";break;
         Case 8:  $t = "OCHO";break;
         Case 9:  $t = "NUEVE";break;
         Case 10: $t = "DIEZ";break;
         Case 20: $t = "VEINTE";break;
         Case 30: $t = "TREINTA";break;
         Case 40: $t = "CUARENTA";break;
         Case 50: $t = "CINCUENTA";break;
         Case 60: $t = "SESENTA";break;
         Case 70: $t = "SETENTA";break;
         Case 80: $t = "OCHENTA";break;
         Case 90: $t = "NOVENTA";break;
         Case 100: $t = "CIEN";break;
         Case 200: $t = "DOSCIENTOS";break;         
		 Case 300: $t = "TRESCIENTOS";break;
         Case 400: $t = "CUATROCIENTOS";break;
         Case 500: $t = "QUINIENTOS";break;
         Case 600: $t = "SEISCIENTOS";break;
         Case 700: $t = "SETECIENTOS";break;
         Case 800: $t = "OCHOCIENTOS";break;
         Case 900: $t = "NOVECIENTOS";break;
         Case 1000: $t = "MIL";break;
         Case 1000000: $t = "MILL√ìN";break;
      }

      If($t == $this->Void)
      {
        $i = $i + 1;
        $x = $x / 1000;
        If($x== 0) $i = 0;
      }
      else
         break;           
    }while($i != 0);   

    $Rtn = $t;
    Switch($i)
    {
       Case 0: $t = $this->Void;break;
       Case 1: $t = " MIL";break;
       Case 2: $t = " MILLONES";break;
       Case 3: $t = " BILLONES";break;
    }
    return($Rtn . $t);
}
}

fUNction mes($num) { 
$end_num="";
switch($num)
{
case 1:
echo "ENERO";
break;
case 2:
echo "FEBRERO";
break;
case 3:
echo "MARZO";
break;
case 4:
echo "ABRIL";
break;
case 5:
echo "MAYO";
break;
case 6:
echo "JUNIO";
break;
case 7:
echo "JULIO";
break;
case 8:
echo "AGOSTO";
break;
case 9:
echo "SEPTIEMBRE";
break;
case 10:
echo "OCTUBRE";
break;
case 11:
echo "NOVIEMBRE";
break;
case 12:
echo "DICIEMBRE";
break;
 return $end_num;
}
}

class mes1 {
    private $mes2;
    public function setmes( $num ) {
     switch($num)
{
case 1:
 $this -> mes2 ="ENERO";
break;

case 2:
$this -> mes2="FEBRERO";
break;

case 3:
 $this -> mes2="MARZO";
break;

case 4:
 $this -> mes2="ABRIL";
break;

case 5:
 $this -> mes2="MAYO";
break;

case 6:
 $this -> mes2="JUNIO";
break;

case 7:
 $this -> mes2="JULIO";
break;

case 8:
 $this -> mes2= "AGOSTO";
break;

case 9:
 $this -> mes2="SEPTIEMBRE";
break;

case 10:
 $this -> mes2="OCTUBRE";
break;

case 11:
 $this -> mes2= "NOVIEMBRE";
break;

case 12:
 $this -> mes2="DICIEMBRE";
break;  
    }}

    public function getmes() {
        return $this -> mes2;
    }

}


/**
 * Class casting
 *
 * @param string|object $destination
 * @param object $sourceObject
 * @return object
 */
function cast($destination, $sourceObject)
{
    if (is_string($destination)) {
        $destination = new $destination();
    }
    $sourceReflection = new ReflectionObject($sourceObject);
    $destinationReflection = new ReflectionObject($destination);
    $sourceProperties = $sourceReflection->getProperties();
    foreach ($sourceProperties as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
        $value = $sourceProperty->getValue($sourceObject);
        if ($destinationReflection->hasProperty($name)) {
            $propDest = $destinationReflection->getProperty($name);
            $propDest->setAccessible(true);
            $propDest->setValue($destination,$value);
        } else {
            $destination->$name = $value;
        }
    }
    return $destination;
}

?>