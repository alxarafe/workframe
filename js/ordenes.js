/* Copyright 2017 Rafael San José Tovar (http://alxarafe.es) */

function hideMe(_sel,item) {
    tr='fw'+item.id.substring(1);
    $('#'+tr).prop('hidden',true);
}

function addWorker(_sel) {
    sel=$('#nw'+_sel);
    item = sel.find('option:selected');
    $('#fw'+item.val()).prop('hidden', false);
    $('#w'+item.val()).prop('checked', true);
    sel.selectpicker('refresh');
}

$(document).ready(function($){
    // Inicia
});

