<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Api;

use Alxarafe\Base\Controller\ApiController;
use Modules\WorkFrame\Model\WorkPart;

class WorkPartApiController extends ApiController
{
    public function doIndex(): void
    {
        $parts = WorkPart::with(['workOrder', 'foreman'])
            ->orderByDesc('date')
            ->get();

        static::jsonResponse($parts->toArray());
    }

    public function doShow(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $part = WorkPart::with(['workOrder', 'foreman', 'workers', 'vehicles'])->find($id);

        if (!$part) {
            static::badApiCall('Work part not found', 404);
        }

        static::jsonResponse($part->toArray());
    }

    public function doCreate(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $part = WorkPart::create($data);
        static::jsonResponse($part->toArray(), 201);
    }

    public function doUpdate(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $part = WorkPart::find($id);

        if (!$part) {
            static::badApiCall('Work part not found', 404);
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $part->update($data);
        static::jsonResponse($part->toArray());
    }

    public function doDelete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $part = WorkPart::find($id);

        if (!$part) {
            static::badApiCall('Work part not found', 404);
        }

        $part->delete();
        static::jsonResponse(['deleted' => true]);
    }
}
