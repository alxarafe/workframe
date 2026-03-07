<?php

return [
    // General
    'name' => 'Nombre',
    'active' => 'Activo',
    'visible' => 'Visible',
    'date' => 'Fecha',
    'email' => 'Correo electrónico',
    'notes' => 'Anotaciones',
    'save' => 'Guardar',
    'delete' => 'Borrar',
    'cancel' => 'Cancelar',
    'search' => 'Buscar',

    // Customer
    'customer' => 'Cliente',
    'customer_details' => 'Datos del cliente',
    'contact' => 'Persona de contacto',
    'address' => 'Dirección',
    'zip' => 'C.P.',
    'locality' => 'Población',
    'town' => 'Provincia',
    'telephone' => 'Teléfono',

    // Worker
    'worker_details' => 'Datos del operario',
    'work_center' => 'Delegación',
    'category' => 'Categoría',
    'available_from' => 'Baja desde',
    'available_until' => 'Baja hasta',

    // Vehicle
    'vehicle_details' => 'Datos del vehículo',
    'license_plate' => 'Matrícula',

    // Project File
    'project_file' => 'Expediente',
    'project_file_details' => 'Datos del expediente',

    // Work Order
    'work_order' => 'Orden de trabajo',
    'work_order_details' => 'Datos de la orden de trabajo',
    'status' => 'Estado',
    'start_date' => 'Fecha de inicio',
    'end_date' => 'Fecha de fin',
    'start_time' => 'Hora de inicio',
    'foreman' => 'Encargado',
    'location' => 'Ubicación',
    'resources' => 'Recursos',
    'assigned_workers' => 'Operarios asignados',
    'assigned_vehicles' => 'Vehículos asignados',

    // Work Part
    'work_part_details' => 'Datos del parte de trabajo',
    'special_time' => 'Montaje especial',

    // Notifications
    'notification_subject' => 'Notificación de obra',
    'notification_sent' => 'Notificación enviada a',
    'no_foreman_email' => 'El encargado no tiene correo electrónico configurado',

    // Delete guards
    'cannot_delete_has_project_files' => 'No se puede borrar: el cliente tiene expedientes asociados',
    'cannot_delete_has_work_orders' => 'No se puede borrar: el expediente tiene órdenes de trabajo asociadas',
    'cannot_delete_has_work_parts' => 'No se puede borrar: la orden tiene partes de trabajo asociados',
    'cannot_delete_is_foreman' => 'No se puede borrar: el trabajador es encargado de órdenes activas',

    // Search
    'search_results' => 'Resultados de la búsqueda',
    'no_results' => 'No se encontraron resultados',

    // Dashboard
    'dashboard_title' => 'Visión general del calendario de obras',
    'quick_view' => 'Vista rápida',
    'work_orders' => 'Órdenes de trabajo',
    'vehicles' => 'Vehículos',
    'workers' => 'Operarios',
    'show_orders_after' => 'Mostrar órdenes posteriores a',
];
