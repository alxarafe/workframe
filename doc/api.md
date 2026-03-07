# WorkFrame REST API

All API endpoints require JWT authentication via `?token=<JWT>`.

## Authentication

```
POST /index.php?module=Admin&controller=Auth&action=login
Body: { "name": "admin", "password": "secret" }
Response: { "ok": true, "result": { "token": "eyJ..." } }
```

Use the returned token in all subsequent API requests.

## Customers

### List customers
```
GET /index.php?module=WorkFrame&controller=CustomerApi&action=index&token=<JWT>
```

### Get customer
```
GET /index.php?module=WorkFrame&controller=CustomerApi&action=show&id=1&token=<JWT>
```

### Create customer
```
POST /index.php?module=WorkFrame&controller=CustomerApi&action=create&token=<JWT>
Content-Type: application/json
Body: { "name": "Acme Corp", "email": "info@acme.com", "telephone": "555-0100" }
```

### Update customer
```
PUT /index.php?module=WorkFrame&controller=CustomerApi&action=update&id=1&token=<JWT>
Content-Type: application/json
Body: { "name": "Acme Corporation" }
```

### Delete customer
```
DELETE /index.php?module=WorkFrame&controller=CustomerApi&action=delete&id=1&token=<JWT>
```

## Work Orders

### List work orders
```
GET /index.php?module=WorkFrame&controller=WorkOrderApi&action=index&token=<JWT>
```
Returns orders with related `projectFile`, `foreman`, `workers`, and `vehicles`.

### Get work order
```
GET /index.php?module=WorkFrame&controller=WorkOrderApi&action=show&id=1&token=<JWT>
```
Returns full order with `projectFile`, `foreman`, `workers`, `vehicles`, `notes`, and `workParts`.

### Create work order
```
POST /index.php?module=WorkFrame&controller=WorkOrderApi&action=create&token=<JWT>
Content-Type: application/json
Body: {
    "name": "Repair Job #42",
    "project_file_id": 1,
    "date": "2026-03-15",
    "end_date": "2026-03-20",
    "start_time": "08:00:00",
    "foreman_id": 1,
    "worker_ids": [1, 2, 3],
    "vehicle_ids": [1]
}
```

### Update work order
```
PUT /index.php?module=WorkFrame&controller=WorkOrderApi&action=update&id=1&token=<JWT>
Content-Type: application/json
Body: { "name": "Updated Job", "worker_ids": [1, 4] }
```

### Delete work order
```
DELETE /index.php?module=WorkFrame&controller=WorkOrderApi&action=delete&id=1&token=<JWT>
```

## Work Parts

### List work parts
```
GET /index.php?module=WorkFrame&controller=WorkPartApi&action=index&token=<JWT>
```

### Get work part
```
GET /index.php?module=WorkFrame&controller=WorkPartApi&action=show&id=1&token=<JWT>
```

### Create work part
```
POST /index.php?module=WorkFrame&controller=WorkPartApi&action=create&token=<JWT>
Content-Type: application/json
Body: { "name": "Day 1 Report", "work_order_id": 1, "foreman_id": 1, "date": "2026-03-15" }
```

### Update / Delete
Same pattern as above with `action=update` / `action=delete`.

## Response Format

All responses follow:
```json
{
    "ok": true,
    "status": 200,
    "result": { ... },
    "messages": []
}
```

Error responses:
```json
{
    "ok": false,
    "status": 404,
    "response": "Work order not found",
    "messages": []
}
```
