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

function addusr(){
	table = document.getElementById("the_table");
	i = table.rows.length;
	row = table.insertRow();
	row.id = "fila"+i;
	row.className = "success";
	row.innerHTML=
'<td><input id="id'+i+'" name="id['+i+']" value="'+i+'" hidden />'+i+'</td>'+
'<td>user'+i+'</td>'+
'<td><input id="admin'+i+'" type="checkbox" name="admin['+i+']" value="0"> Administrador</input></td>'+
'<td><input id="active'+i+'" type="checkbox" name="active['+i+']" value="1" onClick="checkactive(0);" checked> Activo</input>';
	return false;
}

function addsec(){
	table = document.getElementById("the_table");
	i = table.rows.length;
	row = table.insertRow();
	row.id = "fila"+i;
	row.className = "success";
	row.innerHTML=
'<td><input id="id'+i+'" name="id['+i+']" value="'+i+'" hidden />'+i+'</td>'+
'<td><input id="nombre'+i+'" type="text" name="nombre['+i+']" value=""></input></td>'+
'<td><input id="active'+i+'" type="checkbox" name="active['+i+']" value="1" onClick="checkactive(0);" checked> Activo</input>';
	return false;
}