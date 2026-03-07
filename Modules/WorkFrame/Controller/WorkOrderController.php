<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\ResourceController;
use Alxarafe\Lib\Messages;
use Modules\WorkFrame\Model\WorkOrder;
use Modules\WorkFrame\Model\Worker;
use Modules\WorkFrame\Model\Vehicle;
use Modules\WorkFrame\Model\ProjectFile;
use Modules\WorkFrame\Model\OrderStatus;

#[Menu(menu: 'main_menu', label: 'Órdenes de trabajo', icon: 'fas fa-clipboard-list', order: 30, permission: 'WorkFrame.WorkOrder', route: 'WorkFrame.WorkOrder')]
class WorkOrderController extends ResourceController
{
    protected bool $useTabs = true;

    protected function getModelClass(): string
    {
        return WorkOrder::class;
    }

    protected function getListColumns(): array
    {
        return [
            new \Alxarafe\Component\Fields\Integer('id', 'ID', ['readonly' => true]),
            new \Alxarafe\Component\Fields\Text('orderStatus.name', $this->_('workframe.status')),
            new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name')),
            new \Alxarafe\Component\Fields\Text('projectFile.name', $this->_('workframe.project_file')),
            new \Alxarafe\Component\Fields\Date('date', $this->_('workframe.start_date')),
            new \Alxarafe\Component\Fields\Date('end_date', $this->_('workframe.end_date')),
            new \Alxarafe\Component\Fields\Text('locality', $this->_('workframe.locality')),
        ];
    }

    protected function getEditFields(): array
    {
        $projectFiles = ProjectFile::where('active', true)->pluck('name', 'id')->toArray();
        $statuses = OrderStatus::where('active', true)->pluck('name', 'id')->toArray();

        // Foreman = Workers who have a linked user account
        $foremanIds = \Illuminate\Support\Facades\DB::table('users')
            ->whereNotNull('worker_id')
            ->pluck('worker_id')
            ->toArray();
        $foremenQuery = Worker::where('active', true);
        if (!empty($foremanIds)) {
            $foremenQuery->whereIn('id', $foremanIds);
        }
        $foremen = $foremenQuery->pluck('name', 'id')->toArray();

        // All active workers and vehicles for pivot assignment
        $allWorkers = Worker::where('active', true)->pluck('name', 'id')->toArray();
        $allVehicles = Vehicle::where('active', true)
            ->get()
            ->mapWithKeys(fn($v) => [$v->id => $v->name . ' (' . $v->license_plate . ')'])
            ->toArray();

        return [
            'main' => [
                'label' => $this->_('workframe.work_order_details'),
                'fields' => [
                    new \Alxarafe\Component\Fields\Text('name', $this->_('workframe.name'), ['required' => true, 'maxlength' => 60]),
                    new \Alxarafe\Component\Fields\Select('project_file_id', $this->_('workframe.project_file'), $projectFiles),
                    new \Alxarafe\Component\Fields\Select('status', $this->_('workframe.status'), $statuses),
                    new \Alxarafe\Component\Fields\Date('date', $this->_('workframe.start_date')),
                    new \Alxarafe\Component\Fields\Date('end_date', $this->_('workframe.end_date')),
                    new \Alxarafe\Component\Fields\Time('start_time', $this->_('workframe.start_time')),
                    new \Alxarafe\Component\Fields\Select('foreman_id', $this->_('workframe.foreman'), $foremen),
                ],
            ],
            'location' => [
                'label' => $this->_('workframe.location'),
                'fields' => [
                    new \Alxarafe\Component\Fields\Text('address', $this->_('workframe.address'), ['maxlength' => 50]),
                    new \Alxarafe\Component\Fields\Text('zip', $this->_('workframe.zip'), ['maxlength' => 10]),
                    new \Alxarafe\Component\Fields\Text('locality', $this->_('workframe.locality'), ['maxlength' => 50]),
                    new \Alxarafe\Component\Fields\Text('town', $this->_('workframe.town'), ['maxlength' => 50]),
                ],
            ],
            'resources' => [
                'label' => $this->_('workframe.resources'),
                'fields' => [
                    new \Alxarafe\Component\Fields\RelationList('workers', $this->_('workframe.assigned_workers'), [
                        'model' => Worker::class,
                        'pivot_table' => 'work_order_workers',
                        'foreign_key' => 'work_order_id',
                        'related_key' => 'worker_id',
                        'values' => $allWorkers,
                    ]),
                    new \Alxarafe\Component\Fields\RelationList('vehicles', $this->_('workframe.assigned_vehicles'), [
                        'model' => Vehicle::class,
                        'pivot_table' => 'work_order_vehicles',
                        'foreign_key' => 'work_order_id',
                        'related_key' => 'vehicle_id',
                        'values' => $allVehicles,
                    ]),
                ],
            ],
            'notes' => [
                'label' => $this->_('workframe.notes'),
                'fields' => [
                    new \Alxarafe\Component\Fields\RelationList('notes', $this->_('workframe.notes'), [
                        'model' => \Modules\WorkFrame\Model\WorkOrderNote::class,
                        'foreign_key' => 'work_order_id',
                        'columns' => [
                            ['field' => 'notes', 'label' => 'Nota', 'type' => 'textarea'],
                            ['field' => 'created_at', 'label' => 'Fecha', 'type' => 'datetime', 'readonly' => true],
                        ],
                    ]),
                ],
            ],
        ];
    }

    /**
     * Check vehicle availability for overlap detection.
     */
    public function doCheckVehicle(): bool
    {
        $vehicleId = (int) ($_GET['vehicle_id'] ?? 0);
        $workOrderId = (int) ($_GET['work_order_id'] ?? 0);
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';

        if (!$vehicleId || !$startDate || !$endDate) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Missing parameters']);
            return false;
        }

        $conflicts = WorkOrder::whereHas('vehicles', function ($q) use ($vehicleId) {
            $q->where('vehicle_id', $vehicleId);
        })
            ->where('id', '!=', $workOrderId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->get(['id', 'name', 'date', 'end_date']);

        $this->jsonResponse(['status' => 'ok', 'conflicts' => $conflicts->toArray()]);
        return true;
    }

    /**
     * Check worker availability for overlap detection.
     */
    public function doCheckWorker(): bool
    {
        $workerId = (int) ($_GET['worker_id'] ?? 0);
        $workOrderId = (int) ($_GET['work_order_id'] ?? 0);
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';

        if (!$workerId || !$startDate || !$endDate) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Missing parameters']);
            return false;
        }

        $conflicts = WorkOrder::whereHas('workers', function ($q) use ($workerId) {
            $q->where('worker_id', $workerId);
        })
            ->where('id', '!=', $workOrderId)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->get(['id', 'name', 'date', 'end_date']);

        $this->jsonResponse(['status' => 'ok', 'conflicts' => $conflicts->toArray()]);
        return true;
    }

    /**
     * Send email notification to foreman about this work order.
     */
    public function doNotifyForeman(): bool
    {
        $id = (int) ($_GET['id'] ?? 0);
        $workOrder = WorkOrder::with(['projectFile', 'foreman', 'workers', 'vehicles', 'notes'])->find($id);

        if (!$workOrder) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Work order not found']);
            return false;
        }

        $foremanEmail = $workOrder->foreman?->email;
        if (!$foremanEmail) {
            $this->jsonResponse(['status' => 'error', 'message' => $this->_('workframe.no_foreman_email')]);
            return false;
        }

        // Compose notification body
        $body = $this->_('workframe.notification_subject') . ": " . $workOrder->name . "\n\n"
            . $this->_('workframe.project_file') . ": " . ($workOrder->projectFile?->name ?? '-') . "\n"
            . $this->_('workframe.address') . ": " . $workOrder->address . ", " . $workOrder->zip . " " . $workOrder->locality . " (" . $workOrder->town . ")\n"
            . $this->_('workframe.start_date') . ": " . ($workOrder->date?->format('d/m/Y') ?? '-') . "\n"
            . $this->_('workframe.end_date') . ": " . ($workOrder->end_date?->format('d/m/Y') ?? '-') . "\n"
            . $this->_('workframe.start_time') . ": " . ($workOrder->start_time ?? '-') . "\n\n";

        if ($workOrder->vehicles->isNotEmpty()) {
            $body .= $this->_('workframe.assigned_vehicles') . ":\n";
            foreach ($workOrder->vehicles as $v) {
                $body .= "  - " . $v->name . " (" . $v->license_plate . ")\n";
            }
            $body .= "\n";
        }

        if ($workOrder->workers->isNotEmpty()) {
            $body .= $this->_('workframe.assigned_workers') . ":\n";
            foreach ($workOrder->workers as $w) {
                $body .= "  - " . $w->name . "\n";
            }
            $body .= "\n";
        }

        if ($workOrder->notes->isNotEmpty()) {
            $body .= $this->_('workframe.notes') . ":\n";
            foreach ($workOrder->notes->sortByDesc('created_at')->take(3) as $note) {
                $body .= "  [" . ($note->created_at?->format('d/m/Y H:i') ?? '') . "] " . $note->notes . "\n";
            }
        }

        // Send using PHP mail (can be upgraded to Symfony Mailer)
        $subject = $this->_('workframe.notification_subject') . ": " . $workOrder->name;
        $headers = "From: workframe@" . ($_SERVER['SERVER_NAME'] ?? 'localhost') . "\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n";

        $sent = mail($foremanEmail, $subject, $body, $headers);

        if ($sent) {
            Messages::addMessage($this->_('workframe.notification_sent') . " " . $foremanEmail);
            $this->jsonResponse(['status' => 'ok', 'message' => 'Email sent to ' . $foremanEmail]);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Failed to send email']);
        }

        return true;
    }
}
