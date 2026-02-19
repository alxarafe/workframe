/* Copyright 2017 Rafael San José Tovar (http://alxarafe.es) */

var puedoCerrar=false;

function permitirCerrar() {
	puedoCerrar=true;
}

function iCanClose() {
	if (! puedoCerrar) {
		return 'No ha guardado los cambios. Use guardar, cancelar o finalizar';
	}
}

function actualizartodo(){
	$(".linea").each(function (index) {
		actualizar(index);
	});
}

function actualizar(selector){
	$item=document.getElementById("tip"+selector);
	if ($item.selectedIndex == 0) {
		$("#mat"+selector).css('display', '')
		$("#elemento"+selector).css('display', 'none')
		$("#largo"+selector).css('display', 'none')
		$("#grueso"+selector).css('display', 'none')
		$("#ancho"+selector).css('display', 'none')
	} else {
		$("#mat"+selector).css('display', 'none')
		$("#elemento"+selector).css('display', '')
		$("#largo"+selector).css('display', '')
		$("#grueso"+selector).css('display', '')
		$("#ancho"+selector).css('display', '')
	}
}

function actualizarElemento(selector){
	$item=document.getElementById("mat"+selector);
	$valor=$("#mat"+selector).val();
	$texto=$item.options[$valor].text;
	$("#elemento"+selector).val($texto);
}

function actualizar_(selector){

	if ($(selector).val() < 0) {
		$(selector).val(0);
	}
	
	sometido_a_recargo=$("#re").text()==1?true:false;
	fee_shipping=parseFloat($("#fee_shipping").text());
	fee_shipping_ds=parseFloat($("#fee_shipping_ds").text());
	
	total=0.0;
	iva=0.0;
	recargo=0.0;
	dto=0.0;

	/*
	base=[];
	$(".baseiva").each(function (index) {
		$("#base_"+index).val(0);
	});
	*/
	
	$(".linea").each(function (index) {
		if ($("#activo_"+index).text()!="1") {
			if (parseFloat($("#stock_"+index).text()) < parseFloat($("#qty_"+index).val())) {
				$("#qty_"+index).val($("#stock_"+index).text());
			}
		}
		
		fee_dto=parseFloat($("#fee_dto_"+index).text());
		fee_dto_ds=parseFloat($("#fee_dto_ds_"+index).text());

		if ($("#ds").is(':checked')) {
			$('#netprice_'+index).val($("#priceds_"+index).text());
			($("#price_"+index)).css('display', 'none');
			($("#priceds_"+index)).css('display', '');
			subtot=($("#qty_"+index).val()*parseFloat($("#priceds_"+index).text()));
			dto+=parseFloat(subtot*$("#fee_dto_ds_"+index).text()/100);
		} else {
			$('#netprice_'+index).val($("#price_"+index).text());
			($("#price_"+index)).css('display', '');
			($("#priceds_"+index)).css('display', 'none');
			subtot=($("#qty_"+index).val()*parseFloat($("#price_"+index).text()));
			dto+=parseFloat(subtot*$("#fee_dto_"+index).text()/100);
		}
		
		total += subtot;
		tipoiva = parseFloat($("#vat_"+index).text());
		/*
		tax_index=$(".tipo_"+tipoiva).text();
		if (base[tax_index]==undefined) base[tax_index]=0;
		base[tax_index]+=subtot;
		*/
		iva += subtot*(tipoiva/100);
		
		if (sometido_a_recargo) {
			tipore = parseFloat($("#re_"+index).text());
			recargo += subtot*(tipore/100);
		}

		$("#total_"+index).text(subtot.toFixed(2)+"€");
	})
	
	//bonshipping+payment-bonus	
	if ($("#ds").is(':checked')) {
		($(".dson")).css('display', '');
		($(".dsdir")).css('display', '');
		($(".dsoff")).css('display', 'none');
		shipping=fee_shipping_ds;
		//dto=fee_dto_ds/100;
	} else {
		($(".dson")).css('display', 'none');
		($(".dsdir")).css('display', 'none');
		($(".dsoff")).css('display', '');
		shipping=fee_shipping;
		//dto=fee_dto/100;
	}
	
	payment=0;	// Más adelante habrá que añadir 2 euros en caso de paypal y contrareembolso
	_bonus=dto; // antes era total*dto;
	_fees=shipping+payment;
	if (_bonus > _fees) {
		bonus=-_fees;
		($(".nofee")).css('display', 'none');
		allfees=0;
	} else {
		bonus=-_bonus;
		if (total>0) {
			($(".nofee")).css('display', '');
			allfees=parseFloat(_fees+bonus);
			iva += allfees*(parseFloat($("#iva_gastos").val())/100);
			
			if (sometido_a_recargo) {
				tipore = parseFloat($("#re_gastos").val());
				recargo += allfees*(parseFloat($("#re_gastos").val())/100);
			}
		} else {
			allfees=0;
			bonus=-_fees;
			($(".nofee")).css('display', 'none');
		}
	}
	
	$("#header_shipping").val(shipping.toFixed(2));
	$("#header_payment_fee").val(payment.toFixed(2));
	$("#header_fee_bonus").val(bonus.toFixed(2));
	$("#header_fees").val(allfees.toFixed(2));
	
	$("#header_net_amount").val(total.toFixed(2));
	$("#header_tax_amount").val(iva.toFixed(2));
	if (sometido_a_recargo) {
		$("#header_re_amount").val(recargo.toFixed(2));
	}

	$(".baseiva").each(function (index) {
		$("#base_"+index).val(base[index]);
	});

	
	/*
	if ($("#header_expenses") == null) {
		expenses=0;
	} else {
		expenses=$("#header_tax_amount").val();
	}
	*/
	
	$("#header_total_amount").val((total+iva+recargo+shipping+payment+bonus).toFixed(2));
}

function noempty(selector){
	if ($(selector).val() == "") {
		$(selector).val(0);
	}
}

function mostrarMarca(id) {
	$(".linea").each(function (index) {
		if ($("#brand_"+index).text()==id) {
			$("#fila"+index).css('display', ''); 
		} else {
			$("#fila"+index).css('display', 'none'); 
		}
	})
}

function mostrarEtiqueta(id) {
	$(".linea").each(function (index) {
		if ($("#labels_"+index).text().includes(id)) {
			$("#fila"+index).css('display', ''); 
		} else {
			$("#fila"+index).css('display', 'none'); 
		}
	})
}

function ocultarCeros() {
	$(".linea").each(function (index) {
		if ($("#qty_"+index).val()=="0") {
			$("#fila"+index).css('display', 'none'); 
		} else {
			$("#fila"+index).css('display', ''); 
		}
	})
}

function mostrarCeros() {
	$(".linea").each(function (index) {
			$("#fila"+index).css('display', ''); 
	})
}

function checkstock(line) {
	stock=$("#qty_"+line).val();
	if (stock<0) {
		$("#qty_"+line).val(stock=0);
	}
	quedan=stock;
	$(".line"+line).each(function (index) {
		ultimo=this.id;
		qty=this.value;
		if(qty>quedan) {
			this.value=quedan;
		}
		quedan-=this.value;
	});
	if (quedan!=0) {
		quedan += parseInt($("#"+ultimo).val());
		$("#"+ultimo).val(quedan);
	}
}

function checkstock2(line, ndx) {
	stock=$("#qty_"+line).val();
	quedan=stock;
	sig=null;
	nombre=null;
	$(".line"+line).each(function (index) {
		ultimo=index;
		if(parseInt(index)-1==parseInt(ndx)) {
			sig=nombre;
		}
		nombre=this.id;
		if (this.value<0) this.value=0;
		qty=this.value;
		if(qty>quedan) {
			this.value=quedan;
		}
		quedan-=this.value;
	});
	if (quedan>0) {
		linea=parseInt(ultimo)+1;
		if (sig==null) {
			alert("insertar línea "+linea);
		} else {
			quedan += parseInt($("#"+nombre).val());
			$("#"+nombre).val(quedan);
		}
	}
}


function addrow(index,title1,title2) {
	vMarkers[index] = [0, 0, ""];
	table = document.getElementById("addr_table");
	row = table.insertRow();
	i=index;
	row.id = "line"+i;
	row.innerHTML=
'<td>'+
'<div class="col-sm-12">'+
'<input type="text" class="form-control" id="direccion'+i+'" name="direccion['+i+']" placeholder="Dirección" onblur="geoaddress('+i+');"/>'+
'</div>'+
'<div class="col-sm-3">'+
'<input type="text" class="form-control" id="cp'+i+'" name="cp['+i+']" placeholder="C.P." onblur="geoaddress('+i+');"/>'+
'</div>'+
'<div class="col-sm-9">'+
'<input type="text" class="form-control" id="poblacion'+i+'" name="poblacion['+i+']" placeholder="Población" onblur="geoaddress('+i+');"/>'+
'</div>'+
'<div class="col-sm-6">'+
'<input type="text" class="form-control" id="provincia'+i+'" name="provincia['+i+']" placeholder="Provincia" onblur="geoaddress('+i+');"/>'+
'</div>'+
'<div class="col-sm-6">'+
'<input type="text" class="form-control" id="pais'+i+'" name="pais['+i+']" placeholder="País" value="España" />'+
'</div>'+
'<hr>'+
'<div class="col-sm-12">'+
'<input type="text" class="form-control" id="geolocate'+i+'" name="geolocate['+i+']" placeholder="Dirección para geolocalizar en el mapa" onblur="getCoord(vMarkers, '+i+');showmap(vMarkers, '+i+');showMarkers(vMarkers);"/>'+
'</div>'+
'<div class="col-sm-6">'+
'<input type="number" class="form-control" step="any" id="latitud'+i+'" name="latitud['+i+']">'+
'</div>'+
'<div class="col-sm-6">'+
'<input type="number" class="form-control" step="any" id="longitud'+i+'" name="longitud['+i+']">'+
'</div>'+
'</td>'+
'<td>'+
'<input type="text" class="form-control" id="telefono'+i+'" name="telefono['+i+']" placeholder="Teléfono" />'+
'<hr>'+
'<p><input type="checkbox" id="fiscal'+i+'" name="fiscal['+i+']" value="fiscal['+i+']" /> Fiscal</p>'+
'<p title="Marcando esta casilla, los datos de la tienda aparecerán en su ficha pública y buscador"><input type="checkbox" id="shop'+i+'" name="shop['+i+']" value="shop['+i+']" checked /> Tienda</p>'+
'<button class="col-sm-6 btn btn-sm btn-danger" type="button" onclick="$(\'#line'+i+'\').remove();eraseMark(vMarkers, '+i+');recalcular();"><span class="glyphicon glyphicon-trash"></span></button>'+
'<button class="col-sm-6 btn btn-sm btn-info" title="Localizar en el mapa" type="button" onclick="showmap(vMarkers, '+i+');"><span class="glyphicon glyphicon-map-marker"></span></button>'+
'</td>';
	
/*	
	row.innerHTML=
'</td>';
*/
}

$(document).ready(function($){
	/*
	// http://api.jqueryui.com/autocomplete/
    $( "#psearch" ).autocomplete({
		source: products, // El array products se genera en la vista del documento
		select: function(event,ui) {
			$('#fila'+ui.item.value).css('display','');
			$('#qty_'+ui.item.value).focus();
			$('#qty_'+ui.item.value).select();
			$this.val(null);
		},
		focus: function (event, ui) {
			$(".ui-helper-hidden-accessible").hide();
			event.preventDefault();
		}
	});
	*/
	actualizartodo();
});

