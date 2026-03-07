<?php

return [
    // General
    'name' => 'Name',
    'active' => 'Active',
    'visible' => 'Visible',
    'date' => 'Date',
    'email' => 'Email',
    'notes' => 'Notes',
    'save' => 'Save',
    'delete' => 'Delete',
    'cancel' => 'Cancel',
    'search' => 'Search',

    // Customer
    'customer' => 'Customer',
    'customer_details' => 'Customer Details',
    'contact' => 'Contact Person',
    'address' => 'Address',
    'zip' => 'ZIP Code',
    'locality' => 'Locality',
    'town' => 'Town / Province',
    'telephone' => 'Phone',

    // Worker
    'worker_details' => 'Worker Details',
    'work_center' => 'Work Center',
    'category' => 'Category',
    'available_from' => 'Leave From',
    'available_until' => 'Leave Until',

    // Vehicle
    'vehicle_details' => 'Vehicle Details',
    'license_plate' => 'License Plate',

    // Project File
    'project_file' => 'Project File',
    'project_file_details' => 'Project File Details',

    // Work Order
    'work_order' => 'Work Order',
    'work_order_details' => 'Work Order Details',
    'status' => 'Status',
    'start_date' => 'Start Date',
    'end_date' => 'End Date',
    'start_time' => 'Start Time',
    'foreman' => 'Foreman',
    'location' => 'Location',
    'resources' => 'Resources',
    'assigned_workers' => 'Assigned Workers',
    'assigned_vehicles' => 'Assigned Vehicles',

    // Work Part
    'work_part_details' => 'Work Part Details',
    'special_time' => 'Special Time',

    // Notifications
    'notification_subject' => 'Work Order Notification',
    'notification_sent' => 'Notification sent to',
    'no_foreman_email' => 'Foreman has no email configured',

    // Delete guards
    'cannot_delete_has_project_files' => 'Cannot delete: customer has linked project files',
    'cannot_delete_has_work_orders' => 'Cannot delete: project file has linked work orders',
    'cannot_delete_has_work_parts' => 'Cannot delete: work order has linked work parts',
    'cannot_delete_is_foreman' => 'Cannot delete: worker is foreman of active orders',

    // Search
    'search_results' => 'Search Results',
    'no_results' => 'No results found',

    // Dashboard
    'dashboard_title' => 'Work Order Calendar Overview',
    'quick_view' => 'Quick View',
    'work_orders' => 'Work Orders',
    'vehicles' => 'Vehicles',
    'workers' => 'Workers',
    'show_orders_after' => 'Show orders after',
];
