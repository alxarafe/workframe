# Migration Guide: CodeIgniter → Alxarafe

## Summary of Changes

WorkFrame has been migrated from CodeIgniter 3 to Alxarafe v0.5.4. This document maps the old CI patterns to the new Alxarafe equivalents.

## Controller Mapping

| CI Controller | Alxarafe Controller(s) | Notes |
|--------------|----------------------|-------|
| `Index` | `IndexController` | Same redirect logic |
| `Dashboard` | `DashboardController` | FullCalendar + Eloquent eager loading |
| `Auth` | *(Admin module)* | Handled by Alxarafe's AuthController |
| `Administracion` | `SectionController`, `CategoryController`, `WorkCenterController`, `WorkerController`, `VehicleController`, `OrderStatusController` | Split into individual ResourceControllers |
| `Clientes` | `CustomerController` | ResourceController CRUD |
| `Expedientes` | `ProjectFileController` | Renamed to ProjectFile |
| `Ordenes` | `WorkOrderController` | ResourceController + overlap checks |
| `Partes` | `WorkPartController` | ResourceController |
| `Reports` | `ReportController` | PdfService instead of custom PDF |
| `Mail` | `MailController` | IMAP via native PHP |
| `Buscar` | `SearchController` | Eloquent queries |
| `Datos` | `DatabaseController` | Schema Builder migrations |
| `Repara` | *(removed)* | One-off repair, not needed |

## Model Mapping

| CI Model | Alxarafe Model | Notes |
|---------|---------------|-------|
| `Bbdd_model` | *(not needed)* | Replaced by Eloquent queries |
| `Obras_model` | *(split into 13 models)* | Each table gets its own Eloquent model |
| `Auth_model` | *(Admin module)* | Alxarafe handles auth |
| `Buscar_model` | *(inline)* | Search logic in SearchController |
| `Mail_model` | *(inline)* | Mail logic in MailController |

## Database Naming Changes

| Old Name | New Name |
|---------|---------|
| `workcenters` | `work_centers` |
| `customernotes` | `customer_notes` |
| `files` | `project_files` |
| `filenotes` | `project_file_notes` |
| `orderstatus` | `order_statuses` |
| `workorders` | `work_orders` |
| `workordernotes` | `work_order_notes` |
| `workordervehicles` | `work_order_vehicles` |
| `workorderworkers` | `work_order_workers` |
| `workparts` | `work_parts` |
| `partvehicles` | `part_vehicles` |
| `partworkers` | `part_workers` |
| `id_customer` | `customer_id` |
| `id_file` | `project_file_id` |
| `id_foreman` | `foreman_id` |
| `id_workcenter` | `work_center_id` |
| `id_category` | `category_id` |
| `id_order` | `work_order_id` |
| `id_worker` | `worker_id` |
| `id_vehicle` | `vehicle_id` |
| `id_part` | `work_part_id` |
| `start_hour` | `start_time` |
| `temporary_from` | `available_from` |
| `temporary_to` | `available_until` |
| `imagen` | `has_image` |
| `factura` | `has_invoice` |

## Key Pattern Changes

| CI Pattern | Alxarafe Pattern |
|-----------|-----------------|
| `$this->load->model('bbdd_model')` | Direct Eloquent: `WorkOrder::find($id)` |
| `$this->load->view('view', $data)` | Blade: `$this->setTemplate('workframe::dashboard')` |
| `$this->bbdd_model->get_table('workers')` | `Worker::where('active', true)->get()` |
| `$this->bbdd_model->get_record('workers', $id)` | `Worker::find($id)` |
| `$this->bbdd_model->qry2array($sql)` | `DB::select($sql)` or Eloquent |
| CI session auth | Alxarafe `Auth::isLogged()` + JWT for API |
| `$this->load->library('form_validation')` | Eloquent `$fillable` + controller validation |
| `MY_Dbcontroller::show_table_form()` | `ResourceTrait` auto-scaffolded list/edit |
