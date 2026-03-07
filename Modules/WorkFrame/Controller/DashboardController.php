<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\Controller;
use Modules\WorkFrame\Model\WorkOrder;
use Modules\WorkFrame\Model\Worker;
use Modules\WorkFrame\Model\Vehicle;
use Modules\WorkFrame\Model\ProjectFile;
use Modules\WorkFrame\Model\Customer;

/**
 * Dashboard controller — shows calendar overview and fast work order list.
 */
#[Menu(menu: 'main_menu', label: 'Inicio', icon: 'fas fa-home', order: 1, permission: 'WorkFrame.Dashboard', route: 'WorkFrame.Dashboard')]
class DashboardController extends Controller
{
    public array $fastView = [];
    public array $calendarWorkOrders = [];
    public array $calendarVehicles = [];
    public array $calendarWorkers = [];

    public function doIndex(): bool
    {
        $date = $_GET['date'] ?? date('Y-m-d', time() - (90 * 24 * 60 * 60));

        // Fast view: work orders with related data
        $workOrders = WorkOrder::where('end_date', '>=', $date)
            ->with(['projectFile', 'projectFile.customer', 'foreman', 'workers', 'vehicles'])
            ->orderByDesc('date')
            ->get();

        $this->fastView = $workOrders->map(function ($wo) {
            $customer = $wo->projectFile?->customer;
            return [
                'id' => $wo->id,
                'project_file_id' => $wo->project_file_id,
                'project_file_name' => $wo->projectFile?->name ?? '',
                'name' => $wo->name,
                'customer_name' => $customer?->name ?? '',
                'date' => $wo->date?->format('d/m/y') ?? '',
                'end_date' => $wo->end_date?->format('d/m/y') ?? '',
                'start_time' => $wo->start_time ? substr($wo->start_time, 0, 5) : '',
                'foreman_name' => $wo->foreman?->name ?? '',
                'locality' => $wo->locality ?? '',
                'town' => $wo->town ?? '',
                'workers' => $wo->workers->pluck('name')->implode(', '),
                'vehicles' => $wo->vehicles->pluck('name')->implode(', '),
            ];
        })->toArray();

        // Calendar data
        $allOrders = WorkOrder::with(['workers', 'vehicles'])->get();

        $this->calendarWorkOrders = $allOrders->map(fn($wo) => [
            'id' => $wo->id,
            'title' => $wo->name,
            'start' => $wo->date?->format('Y-m-d') ?? '',
            'end' => $wo->end_date?->format('Y-m-d') ?? '',
            'start_time' => $wo->start_time ?? '09:00:00',
            'url' => static::url('index') . '&controller=WorkOrder&id=' . $wo->id,
        ])->toArray();

        $this->calendarVehicles = [];
        foreach ($allOrders as $wo) {
            foreach ($wo->vehicles as $vehicle) {
                $this->calendarVehicles[] = [
                    'id' => $wo->id,
                    'title' => $vehicle->name . ' (' . $vehicle->license_plate . ')',
                    'start' => $wo->date?->format('Y-m-d') ?? '',
                    'end' => $wo->end_date?->format('Y-m-d') ?? '',
                    'start_time' => $wo->start_time ?? '09:00:00',
                    'url' => static::url('index') . '&controller=WorkOrder&id=' . $wo->id,
                ];
            }
        }

        $this->calendarWorkers = [];
        foreach ($allOrders as $wo) {
            foreach ($wo->workers as $worker) {
                $this->calendarWorkers[] = [
                    'id' => $wo->id,
                    'title' => $worker->name,
                    'start' => $wo->date?->format('Y-m-d') ?? '',
                    'end' => $wo->end_date?->format('Y-m-d') ?? '',
                    'start_time' => $wo->start_time ?? '09:00:00',
                    'url' => static::url('index') . '&controller=WorkOrder&id=' . $wo->id,
                ];
            }
        }

        $this->addVariable('fast_view', $this->fastView);
        $this->addVariable('calendar_work_orders', json_encode($this->calendarWorkOrders));
        $this->addVariable('calendar_vehicles', json_encode($this->calendarVehicles));
        $this->addVariable('calendar_workers', json_encode($this->calendarWorkers));
        $this->addVariable('filter_date', $date);

        $this->setDefaultTemplate('dashboard');
        return true;
    }
}
