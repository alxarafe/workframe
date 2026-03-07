# WorkFrame Architecture

## Overview

WorkFrame is a work order management application built on the **Alxarafe** framework. It manages customers, project files (expedientes), work orders, work parts (daily reports), workers, vehicles, and related resources.

## Technology Stack

| Component | Technology |
|-----------|-----------|
| Framework | Alxarafe v0.5.4 |
| ORM | Eloquent (illuminate/database) |
| Templating | Blade (jenssegers/blade) |
| Auth | JWT (firebase/php-jwt) |
| PDF | DomPDF (dompdf/dompdf) |
| PHP | 8.2+ |
| Database | MariaDB 10.11 |
| Container | Docker (nginx + php-fpm) |

## Module Structure

```
Modules/WorkFrame/
├── Api/                    # REST API controllers (JWT auth)
│   ├── CustomerApiController.php
│   ├── WorkOrderApiController.php
│   └── WorkPartApiController.php
├── Controller/             # Page controllers
│   ├── CategoryController.php      (ResourceController)
│   ├── CustomerController.php      (ResourceController)
│   ├── DashboardController.php     (Controller)
│   ├── DatabaseController.php      (Controller)
│   ├── IndexController.php         (Controller)
│   ├── MailController.php          (Controller)
│   ├── OrderStatusController.php   (ResourceController)
│   ├── ProjectFileController.php   (ResourceController)
│   ├── ReportController.php        (Controller)
│   ├── SearchController.php        (Controller)
│   ├── SectionController.php       (ResourceController)
│   ├── VehicleController.php       (ResourceController)
│   ├── WorkCenterController.php    (ResourceController)
│   ├── WorkOrderController.php     (ResourceController)
│   ├── WorkPartController.php      (ResourceController)
│   └── WorkerController.php        (ResourceController)
├── Lang/                   # Translations
│   ├── en/workframe.php
│   └── es/workframe.php
├── Model/                  # Eloquent models
│   ├── Category.php
│   ├── Customer.php
│   ├── CustomerNote.php
│   ├── OrderStatus.php
│   ├── ProjectFile.php
│   ├── ProjectFileNote.php
│   ├── Section.php
│   ├── Vehicle.php
│   ├── WorkCenter.php
│   ├── WorkOrder.php
│   ├── WorkOrderNote.php
│   ├── WorkPart.php
│   └── Worker.php
├── Service/                # Business services
│   └── DatabaseMigration.php
└── Views/                  # Blade templates
    ├── dashboard.blade.php
    ├── database_status.blade.php
    ├── mail_config.blade.php
    ├── mail_index.blade.php
    ├── report_customers.blade.php
    ├── report_workers.blade.php
    ├── reports_index.blade.php
    └── search_results.blade.php
```

## Controller Hierarchy

```
GenericController (routing, menus, actions)
  └── ViewController (Blade templates, config, translations)
       └── Controller (auth, authorization, DB connection)
            ├── ResourceController (CRUD: list + edit via ResourceTrait)
            │    ├── SectionController
            │    ├── CategoryController
            │    ├── WorkCenterController
            │    ├── WorkerController
            │    ├── VehicleController
            │    ├── OrderStatusController
            │    ├── CustomerController
            │    ├── ProjectFileController
            │    ├── WorkOrderController
            │    └── WorkPartController
            ├── DashboardController
            ├── SearchController
            ├── ReportController
            ├── IndexController
            ├── MailController
            └── DatabaseController

ApiController (JWT auth, JSON responses)
  ├── CustomerApiController
  ├── WorkOrderApiController
  └── WorkPartApiController
```

## Routing

Alxarafe uses query-parameter routing:

```
index.php?module=WorkFrame&controller=Dashboard&action=index
index.php?module=WorkFrame&controller=Customer&id=5         (edit mode)
index.php?module=WorkFrame&controller=Customer              (list mode)
```

## Authentication

- **Web UI**: Session-based via Alxarafe's `Auth` class
- **API**: JWT token via `?token=xxx` query parameter
