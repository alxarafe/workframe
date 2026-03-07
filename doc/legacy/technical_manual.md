# Manual Técnico: Aplicación Legada WorkFrame (Obras)

Este manual detalla la arquitectura interna, el esquema de datos y los patrones de la aplicación original (CodeIgniter 3) para facilitar su migración/clonación al framework Alxarafe. Actualizado con hallazgos de la exploración funcional del 06/03/2026.

---

## 1. Stack Tecnológico

| Componente | Versión |
|-----------|---------|
| Servidor web | Apache 2.4.66 (Unix) |
| PHP | 7.4.33 |
| Framework | CodeIgniter 3 |
| Base de datos | MySQL |
| Frontend | Bootstrap 3 + AdminLTE (skin-blue) |
| Fuentes | Source Sans Pro (Google Fonts) |
| Selector de fechas | bootstrap-select |
| Reportes PDF | TCPDF (roto en PHP 7.3+) |
| Email | PHPMailer vía `sendmail.php` |
| SSL | Certificado auto-firmado |
| Sesiones | Cookie `ci_session` (HttpOnly, 2h TTL) |

**Ruta del servidor:** `/srv/http/obras/` (documentado en traza de errores PHP).

---

## 2. Arquitectura de Datos

### 2.1. Tablas Maestras (Configuración)

#### `sections` — Agrupaciones de categorías

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK AUTO | |
| `name` | VARCHAR | Material de construcción, Fontanería, etc. |
| `active` | BOOLEAN | |

---

#### `categories` — Tipos de trabajador

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK AUTO | |
| `name` | VARCHAR | Fontanero, Carpintero |
| `active` | BOOLEAN | |

---

#### `workcenters` — Centros de trabajo (Delegaciones)

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK AUTO | |
| `name` | VARCHAR | Madrid, Sevilla |
| `active` | BOOLEAN | |

---

#### `orderstatus` — Estados de orden de trabajo

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK | ID 1=Activa, 2=Finalizada, 3=Cancelada |
| `name` | VARCHAR | |
| `visible` | BOOLEAN | Controla visibilidad en filtros por defecto |
| `active` | BOOLEAN | |

---

### 2.2. Autenticación y Roles

#### `users` — Cuentas de usuario

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | VARCHAR PK | Nombre de usuario (admin, user) |
| `name` | VARCHAR | |
| `email` | VARCHAR | |
| `password` | VARCHAR | Hash |
| `id_worker` | INT FK → `workers.id` | Vincula usuario con operario |
| `active` | BOOLEAN | |

**Lógica clave:** Un trabajador es "Jefe de Equipo" (Foreman) si tiene un registro en `users` donde `users.id_worker = workers.id`.

#### `roles` — Roles del sistema

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK | |
| `name` | VARCHAR | Administrador, Usuario |
| `active` | BOOLEAN | |

#### `user_roles` — Relación M:N

| Campo | Tipo |
|-------|------|
| `id_user` | FK → `users.id` |
| `id_role` | FK → `roles.id` |

---

### 2.3. Recursos Operativos

#### `workers` — Empleados/Operarios

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK AUTO | |
| `name` | VARCHAR | |
| `id_workcenter` | INT FK → `workcenters.id` | |
| `id_category` | INT FK → `categories.id` | |
| `email` | VARCHAR | Para notificaciones |
| `temporary_from` | DATE | Inicio de baja (0000-00-00 si no) |
| `temporary_to` | DATE | Fin de baja |
| `active` | BOOLEAN | |

**Informe:** Enlace a `/reports/operarios/{id}`.

---

#### `vehicles` — Flota de vehículos

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK AUTO | |
| `name` | VARCHAR(60) | Descripción |
| `id_workcenter` | INT FK → `workcenters.id` | |
| `license_plate` | VARCHAR | Matrícula |
| `active` | BOOLEAN | |

---

### 2.4. Gestión Comercial y Contratos

#### `customers` — Clientes

| Campo | Tipo | Nombre HTML |
|-------|------|-------------|
| `id` | INT PK | `id` |
| `name` | VARCHAR | `name` |
| `contact` | VARCHAR | `contact` |
| `address` | VARCHAR | `address` |
| `zip` | VARCHAR | `zip` |
| `locality` | VARCHAR | `locality` |
| `town` | VARCHAR | `town` |
| `telephone` | VARCHAR | `telephone` |
| `email` | VARCHAR | `email` |

**Informe:** enlace a `/reports/clientes/{id}`.

---

#### `customernotes` — Notas de clientes

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | TIMESTAMP PK | ⚠️ Usa timestamp como PK |
| `id_customer` | INT FK → `customers.id` | |
| `text` | TEXT | |

---

#### `files` — Expedientes (Proyectos)

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK AUTO | |
| `name` | VARCHAR | |
| `id_customer` | INT FK → `customers.id` | |
| `date` | DATE | Fecha de apertura |
| `locality` | VARCHAR | |
| `town` | VARCHAR | |

#### `filenotes` — Notas de expedientes

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | TIMESTAMP PK | ⚠️ Usa timestamp como PK |
| `id_file` | INT FK → `files.id` | |
| `text` | TEXT | |

---

### 2.5. Operaciones y Ejecución

#### `workorders` — Órdenes de Trabajo

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK AUTO | |
| `name` | VARCHAR | Hereda del expediente si vacío |
| `id_file` | INT FK → `files.id` | |
| `status` | INT FK → `orderstatus.id` | |
| `date` | DATE | Fecha de inicio |
| `end_date` | DATE | Fecha de fin (= `date` si vacío) |
| `start_hour` | TIME | Hora de inicio |
| `id_foreman` | INT FK → `workers.id` | Jefe de equipo |
| `address` | VARCHAR | |
| `zip` | VARCHAR | |
| `locality` | VARCHAR | |
| `town` | VARCHAR | |

#### `workordernotes` — Notas de órdenes

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | TIMESTAMP PK | ⚠️ Usa timestamp como PK |
| `id_workorder` | INT FK → `workorders.id` | |
| `text` | TEXT | |

#### `workorderworkers` — Pivot operarios-órdenes

| Campo | Tipo |
|-------|------|
| `id_workorder` | FK → `workorders.id` |
| `id_worker` | FK → `workers.id` |

#### `workordervehicles` — Pivot vehículos-órdenes

| Campo | Tipo |
|-------|------|
| `id_workorder` | FK → `workorders.id` |
| `id_vehicle` | FK → `vehicles.id` |

---

#### `workparts` — Partes de Trabajo

| Campo | Tipo | Notas |
|-------|------|-------|
| `id` | INT PK AUTO | |
| `id_order` | INT FK → `workorders.id` | |
| `id_foreman` | INT FK → `workers.id` | |
| `date` | DATE | |
| `special_time` | BOOLEAN | Montaje especial |
| `imagen` | VARCHAR | Extensión del archivo (jpg, png) |
| `factura` | VARCHAR | Extensión del archivo de factura |
| `notes` | TEXT | Observaciones |

**Archivos de imagen:**
- **Foto de obra:** `img/{id_parte}.{ext}`
- **Foto de factura:** `img/f{id_parte}.{ext}`

---

## 3. Estructura de URLs y Controladores

### 3.1. Mapa de Rutas (CodeIgniter 3)

| URL | Controlador | Acción |
|-----|-------------|--------|
| `/` | `Dashboard` | Redirige a `/dashboard` |
| `/auth` | `Auth` | Login form |
| `/auth/login` | `Auth` | POST login |
| `/dashboard` | `Dashboard` | Visión general |
| `/administracion/users` | `Administracion` | Listado usuarios |
| `/administracion/roles` | `Administracion` | Listado roles |
| `/administracion/editroles/{user}` | `Administracion` | Editar roles de usuario |
| `/administracion/sections` | `Administracion` | CRUD secciones |
| `/administracion/categories` | `Administracion` | CRUD categorías |
| `/administracion/workcenters` | `Administracion` | CRUD delegaciones |
| `/administracion/workers` | `Administracion` | Listado empleados |
| `/administracion/workers/{id}` | `Administracion` | Editar empleado |
| `/administracion/vehicles` | `Administracion` | Listado vehículos |
| `/administracion/vehicles/{id}` | `Administracion` | Editar vehículo |
| `/administracion/orderstatus` | `Administracion` | CRUD estados |
| `/clientes` | `Clientes` | Listado clientes |
| `/clientes/cliente/{id}` | `Clientes` | Editar cliente |
| `/clientes/notas/{id}` | `Clientes` | Añadir nota |
| `/expedientes` | `Expedientes` | Listado expedientes |
| `/expedientes/expediente/{id}` | `Expedientes` | Editar expediente |
| `/ordenes` | `Ordenes` | Listado órdenes |
| `/ordenes/orden/{id}` | `Ordenes` | Editar orden |
| `/partes` | `Partes` | Selección encargado + listado |
| `/partes/parte/{id}` | `Partes` | Editar parte |
| `/buscar?cad={term}` | `Buscar` | Búsqueda global |
| `/reports/clientes` | `Reports` | Informe PDF clientes |
| `/reports/operarios/{id}` | `Reports` | Informe PDF operario |
| `/mail` | `Mail` | Procesar correo |
| `/mail/config` | `Mail` | Configuración SMTP |

---

## 4. Lógica de Negocio Crítica

### 4.1. Detección de Solapamientos (Recursos)

Algoritmo implementado en `Obras_model.php`:

```sql
-- checkvehicle($id_vehicle, $workorder, $start, $end)
-- checkworker($id_worker, $workorder, $start, $end)
SELECT * FROM workorders a
JOIN workorder{vehicles|workers} b ON a.id = b.id_workorder
WHERE b.id_{vehicle|worker} = '$id'
  AND a.id != $current_order
  AND (
    (a.date BETWEEN '$start' AND '$end') OR
    (a.end_date BETWEEN '$start' AND '$end') OR
    ('$start' BETWEEN a.date AND a.end_date) OR
    ('$end' BETWEEN a.date AND a.end_date)
  )
```

**UI:** Se muestra `glyphicon-exclamation-sign` con tooltip indicando la orden conflictiva.

### 4.2. Herencia de Nombres

Al guardar una orden sin nombre, hereda automáticamente el nombre del expediente asociado (`files.name`). Igualmente, si `end_date` queda vacío, se copia el valor de `date`.

### 4.3. Notificación por Email

El método `sendmail` en `Ordenes.php`:
1. Busca el email en `users` donde `id_worker = id_foreman`
2. Genera texto plano con: Expediente, Dirección, Horario, Vehículos (matrículas + nombres), Operarios, Notas recientes
3. Envía vía PHPMailer (`sendmail.php`)

### 4.4. Guardas de Borrado (`check_uses`)

Antes de eliminar un registro, el modelo base (`Bbdd_model.php`) verifica si tiene dependencias:
- Cliente → verifica expedientes asociados
- Expediente → verifica órdenes asociadas
- Orden → verifica partes asociados

Si tiene dependencias, la eliminación se bloquea.

---

## 5. Patrones de UI y Frontend

### 5.1. Dos Tipos de CRUD

| Tipo | Secciones | Descripción |
|------|-----------|-------------|
| **Inline Grid** | Sections, Categories, WorkCenters, OrderStatus, Users, Roles | Edición directa en tabla, guardado masivo con "Aceptar" |
| **Formulario Individual** | Workers, Vehicles, Customers, Files, WorkOrders, WorkParts | Página de edición dedicada con Guardar/Salir/Borrar |

### 5.2. Creación en Dos Pasos (Formularios Individuales)

1. Primer formulario: solo campo **ID** + botón "Guardar"
2. El controlador (`edit_record`) detecta si es un ID nuevo
3. Si es nuevo, crea el registro con ese ID y redirige al formulario completo

### 5.3. Confirmación de Borrado por Doble Clic

JavaScript que cambia el texto del botón a "Borrar 1" en el primer clic, y ejecuta la acción en el segundo.

### 5.4. Paginación

Las tablas de listado (Clientes, Empleados) usan paginación server-side con "Mostrando página X de Y" y botones Anterior/Siguiente.

---

## 6. Modelo Base (`Bbdd_model.php`)

Proporciona operaciones genéricas:
- `save_data()` — INSERT/UPDATE genérico
- `delete_record()` — DELETE con verificación de dependencias
- `get_table()` — SELECT con `id as oldid` para soporte de migración
- `qry2array()` — Ejecuta SQL raw y devuelve array

---

## 7. Problemas Técnicos Documentados

### 7.1. Errores Críticos (Bloquean funcionalidad)

| Módulo | Error | Causa |
|--------|-------|-------|
| Secciones (Añadir) | HTTP 404 | Ruta inexistente |
| Órdenes (Crear) | `Trying to access array offset on value of type bool` | `Ordenes.php:255-256` — inicialización de nuevo registro |
| Partes | `Unknown column 'end_date'` | Desajuste entre código y esquema BD |
| Reports | `"continue" targeting switch` en TCPDF | Incompatibilidad con PHP 7.3+ |
| Mail | `parse_ini_file(email.ini)` | Archivo de configuración ausente |

### 7.2. Avisos No Bloqueantes

| Módulo | Aviso | Impacto |
|--------|-------|---------|
| Expedientes/Clientes (Guardar) | PHP Notice en `MY_Dbcontroller.php` | No afecta al guardado |
| Órdenes (Editar) | Avisos de array offset | Formulario se muestra parcialmente |

---

## 8. Mapeo para Migración a Alxarafe WorkFrame

### 8.1. Entidades → Modelos Eloquent

| Tabla Legacy | Modelo Alxarafe |
|-------------|-----------------|
| `sections` | `Section` |
| `categories` | `Category` |
| `workcenters` | `WorkCenter` |
| `orderstatus` | `OrderStatus` |
| `workers` | `Worker` |
| `vehicles` | `Vehicle` |
| `customers` | `Customer` |
| `customernotes` | `CustomerNote` |
| `files` | `ProjectFile` |
| `filenotes` | `ProjectFileNote` |
| `workorders` | `WorkOrder` |
| `workordernotes` | `WorkOrderNote` |
| `workorderworkers` | Pivot `WorkOrder` ↔ `Worker` |
| `workordervehicles` | Pivot `WorkOrder` ↔ `Vehicle` |
| `workparts` | `WorkPart` |

### 8.2. Controladores → ResourceControllers

| Ruta Legacy | Controlador Alxarafe |
|-------------|---------------------|
| `/administracion/*` | `SectionController`, `CategoryController`, `WorkCenterController`, `OrderStatusController`, `WorkerController`, `VehicleController` |
| `/clientes/*` | `CustomerController` |
| `/expedientes/*` | `ProjectFileController` |
| `/ordenes/*` | `WorkOrderController` |
| `/partes/*` | `WorkPartController` |
| `/buscar` | `SearchController` |
| `/reports/*` | `ReportController` |
| `/mail/*` | `MailController` |
| `/dashboard` | `DashboardController` |

### 8.3. Notas de Migración Importantes

1. **Timestamp como PK:** Las tablas de notas usan `TIMESTAMP` como clave primaria → migrar a `INT AUTO_INCREMENT` con columna separada `created_at`.
2. **IDs Manuales:** El patrón de creación con ID manual debe reemplazarse por auto-incremento estándar.
3. **Detección de Solapamientos:** Reimplementar como Service o Scope en Eloquent.
4. **Notificación Email:** Reimplementar con Laravel Mail/Notification.
5. **Archivos de Imagen:** Migrar de `img/{id}.{ext}` a Storage con nombres UUID.
