function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr;b=MM_swapImgRestore.arguments;
   for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
 }
function MM_swapImgRestore1() { //v3.0
  var i,x,a=document.MM_sr;b=MM_swapImgRestore1.arguments;
   for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
  if ((x=MM_findObj(b[0]))!=null){document.getElementById(b[0]).style.color ="#333333"; 
   }
}


function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}

function MM_swapImage1() { //v3.0

  var i,j=0,x,a=MM_swapImage1.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-3);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];
   }

   if ((x=MM_findObj(a[4]))!=null){document.getElementById(a[4]).style.color ="#DA251D"; 
   }

}

function MM_swapImage() { //v3.0

  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-3);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];
   }
document.MM_sr=new Array; for(i=0;i<(a.length-3);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];
   }
  

}


function cambiartab() { //v3.0
  var a=cambiartab.arguments;
   if ((x=MM_findObj(a[0]))!=null){document.getElementById(a[0]).style.display ="block";}

	// First, lets get all the div tags in this document	
	alldivs = document.getElementsByTagName('div');
	// Now we'll step through all the divs
	for(i = 0; i < alldivs.length; i++) {
		// assign the div to a working variable.
		adiv = alldivs[i];

		if (adiv.id.substr(0,6) == "DivTab" & adiv.id!=a[0]) {
		document.getElementById(adiv.id).style.display ="none";}
	}
}

function cambiartab2() { //v3.0

  var a=cambiartab2.arguments;
   if ((x=MM_findObj(a[0]))!=null){document.getElementById(a[0]).style.display ="block";}

	// First, lets get all the div tags in this document	
	alldivs = document.getElementsByTagName('div');
	// Now we'll step through all the divs
	for(i = 0; i < alldivs.length; i++) {
		// assign the div to a working variable.
		adiv = alldivs[i];

		if (adiv.id.substr(0,7) == "Div2Tab" & adiv.id!=a[0]) {
		document.getElementById(adiv.id).style.display ="none";}
	}
	
}


