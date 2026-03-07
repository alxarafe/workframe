# WorkFrame Controllers

## ResourceControllers (CRUD)

These controllers extend `ResourceController` and automatically provide list + edit views via `ResourceTrait`.

| Controller | Model | URL | Description |
|-----------|-------|-----|-------------|
| `SectionController` | `Section` | `?module=WorkFrame&controller=Section` | Material sections |
| `CategoryController` | `Category` | `?module=WorkFrame&controller=Category` | Worker categories |
| `WorkCenterController` | `WorkCenter` | `?module=WorkFrame&controller=WorkCenter` | Work centers/offices |
| `WorkerController` | `Worker` | `?module=WorkFrame&controller=Worker` | Workers with availability |
| `VehicleController` | `Vehicle` | `?module=WorkFrame&controller=Vehicle` | Vehicles with plates |
| `OrderStatusController` | `OrderStatus` | `?module=WorkFrame&controller=OrderStatus` | Active/Completed/Cancelled |
| `CustomerController` | `Customer` | `?module=WorkFrame&controller=Customer` | Customers with contacts |
| `ProjectFileController` | `ProjectFile` | `?module=WorkFrame&controller=ProjectFile` | Project files (expedientes) |
| `WorkOrderController` | `WorkOrder` | `?module=WorkFrame&controller=WorkOrder` | Work orders (tabbed form) |
| `WorkPartController` | `WorkPart` | `?module=WorkFrame&controller=WorkPart` | Daily work parts |

### Special Actions in WorkOrderController

- `doCheckVehicle`: AJAX overlap detection for vehicle assignments
- `doCheckWorker`: AJAX overlap detection for worker assignments

## Standard Controllers

| Controller | URL | Description |
|-----------|-----|-------------|
| `IndexController` | `?module=WorkFrame&controller=Index` | Entry point — redirects to Dashboard or Login |
| `DashboardController` | `?module=WorkFrame&controller=Dashboard` | Calendar (FullCalendar) + active orders table |
| `SearchController` | `?module=WorkFrame&controller=Search&q=...` | Global search across customers, files, orders, workers |
| `ReportController` | `?module=WorkFrame&controller=Report` | — |
| ↳ `action=workers` | | Worker hours report with PDF export |
| ↳ `action=customers` | | Customer project summary |
| `MailController` | `?module=WorkFrame&controller=Mail` | IMAP email reader |
| ↳ `action=config` | | IMAP server configuration |
| `DatabaseController` | `?module=WorkFrame&controller=Database` | Run migrations and seed data |
