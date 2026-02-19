/* Copyright 2017 Rafael San José Tovar (http://alxarafe.es) */
function showCalendar(id, data) {
    $('#'+id).fullCalendar({
      header    : {
        left  : 'prev,next today',
        center: 'title',
        right : 'month,agendaWeek,agendaDay'
      },
      events : data,
      editable  : false,
      droppable : false,
      displayEventTime: false,
    })
}

$(document).ready(function ($){
	if (typeof(workorders)=='object') {
		showCalendar('workorders',workorders);
	}
	if (typeof(vehicles)=='object') {
		showCalendar('vehicles',vehicles);
	}
	if (typeof(workers)=='object') {
		showCalendar('workers',workers);
	}
	$('#workorders').removeClass('active');
	$('#vehicles').removeClass('active');
	$('#workers').removeClass('active');
});