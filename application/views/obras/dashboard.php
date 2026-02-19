<div class="col-md-12">
    <form>
        Mostrar órdenes posteriores a
        <input id='from'  name='date' type='date' value='<?= $date ?>' /> <input type="submit" value="Ok" name="aceptar" />
    </form>
    <br>
    <div class="box box-primary">
        <div class="box-body no-padding">
            <!-- THE CALENDAR -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#fastview" data-toggle="tab">Vista rápida</a></li>
                    <li><a href="#workorders" data-toggle="tab">Órdenes de trabajo</a></li>
                    <li><a href="#vehicles" data-toggle="tab">Vehículos</a></li>
                    <li><a href="#workers" data-toggle="tab">Operarios</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="fastview">
                        <div id="fastview">
                            <?=
                            $ctrl->show_table_form($fastview, array(
                                array('name' => 'id', 'label' => 'Expediente', 'type' => 'text'),
                                array('name' => 'exp_date', 'label' => 'Fec.Exp.', 'type' => 'text', 'dataorder' => 'exp_date_order'),
                                array('name' => 'description', 'label' => 'OT/Nom.Exp./Cliente', 'type' => 'text'),
                                array('name' => 'dayofweek', 'label' => 'Día', 'type' => 'text'),
                                array('name' => 'wo_date', 'label' => 'Fec.Orden', 'type' => 'text', 'dataorder' => 'wo_date_order'),
                                array('name' => 'hour', 'label' => 'Hora', 'type' => 'text'),
                                /*
                                  array('name'=>'date', 'label'=>'Fecha', 'type'=>'text'),
                                 */
                                array('name' => 'address', 'label' => 'Localidad/Provincia', 'type' => 'text'),
                                array('name' => 'vehicles', 'label' => 'Vehículos', 'type' => 'text'),
                                array('name' => 'workers', 'label' => 'Empleados', 'type' => 'text'),
                                ), null
                            );

                            ?>
                        </div>
                    </div>
                    <div class="active tab-pane" id="workorders">
                        <div id="workorders"></div>
                    </div>
                    <div class="active tab-pane" id="vehicles">
                        <div id="vehicles"></div>
                    </div>
                    <div class="active tab-pane" id="workers">
                        <div id="workers"></div>
                    </div>
                </div>
            </div>
        </div><!-- /.box-body -->
    </div><!-- /. box -->
</div>
<?= $jsarray ?>