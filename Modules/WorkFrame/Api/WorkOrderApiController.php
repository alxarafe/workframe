<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Api;

use Alxarafe\Base\Controller\ApiController;
use Modules\WorkFrame\Model\WorkOrder;

class WorkOrderApiController extends ApiController
{
    public function doIndex(): void
    {
        $orders = WorkOrder::with(['projectFile', 'foreman', 'workers', 'vehicles'])
            ->where('active', true)
            ->orderByDesc('date')
            ->get();

        static::jsonResponse($orders->toArray());
    }

    public function doShow(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $order = WorkOrder::with(['projectFile', 'foreman', 'workers', 'vehicles', 'notes', 'workParts'])->find($id);

        if (!$order) {
            static::badApiCall('Work order not found', 404);
        }

        static::jsonResponse($order->toArray());
    }

    public function doCreate(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $order = WorkOrder::create($data);

        if (isset($data['worker_ids'])) {
            $order->workers()->sync($data['worker_ids']);
        }
        if (isset($data['vehicle_ids'])) {
            $order->vehicles()->sync($data['vehicle_ids']);
        }

        static::jsonResponse($order->load(['workers', 'vehicles'])->toArray(), 201);
    }

    public function doUpdate(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $order = WorkOrder::find($id);

        if (!$order) {
            static::badApiCall('Work order not found', 404);
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $order->update($data);

        if (isset($data['worker_ids'])) {
            $order->workers()->sync($data['worker_ids']);
        }
        if (isset($data['vehicle_ids'])) {
            $order->vehicles()->sync($data['vehicle_ids']);
        }

        static::jsonResponse($order->load(['workers', 'vehicles'])->toArray());
    }

    public function doDelete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $order = WorkOrder::find($id);

        if (!$order) {
            static::badApiCall('Work order not found', 404);
        }

        $order->workers()->detach();
        $order->vehicles()->detach();
        $order->delete();
        static::jsonResponse(['deleted' => true]);
    }
}
