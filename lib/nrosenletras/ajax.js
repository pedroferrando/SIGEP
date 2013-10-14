function nuevoAjax()
{ 
	/* Crea el objeto AJAX. Esta funcion es generica para cualquier utilidad de este tipo, por
	lo que se puede copiar tal como esta aqui */
	var xmlhttp=false; 
	try 
	{ 
		// Creacion del objeto AJAX para navegadores no IE
		xmlhttp=new ActiveXObject("Msxml2.XMLHTTP"); 
	}
	catch(e)
	{ 
		try
		{ 
			// Creacion del objet AJAX para IE 
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP"); 
		} 
		catch(E) { xmlhttp=false; }
	}
	if (!xmlhttp && typeof XMLHttpRequest!='undefined') { xmlhttp=new XMLHttpRequest(); } 

	return xmlhttp; 
}

function convertir(){
	//var capa=document.getElementById("resultado");
	// Creo el objeto AJAX
	var ajax=nuevoAjax();
	// Coloco el mensaje "Cargando..." en la capa
	//capa.innerHTML="<IMG SRC='ajax-loader.gif'> Cargando...";
	// Abro la conexi�n, env�o cabeceras correspondientes al uso de POST y env�o los datos con el m�todo send del objeto AJAX
	ajax.open("POST", "prueba.php", true);
	var num=document.getElementById("numero").value;
	
	var flota=document.getElementById("f").value;
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send("numero="+num+"&f="+flota);
  	capa=document.getElementById("resultado");
	ajax.onreadystatechange=function()
	{
		if (ajax.readyState==4)
		{
		
    // Respuesta recibida. Coloco el texto plano en la capa correspondiente
		capa.innerHTML=ajax.responseText;
      
		}
	}
}
