<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Api;

use Alxarafe\Base\Controller\ApiController;
use Modules\WorkFrame\Model\Customer;

class CustomerApiController extends ApiController
{
    public function doIndex(): void
    {
        $customers = Customer::where('active', true)->get();
        static::jsonResponse($customers->toArray());
    }

    public function doShow(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $customer = Customer::find($id);

        if (!$customer) {
            static::badApiCall('Customer not found', 404);
        }

        static::jsonResponse($customer->toArray());
    }

    public function doCreate(): void
    {
        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

        $customer = Customer::create($data);
        static::jsonResponse($customer->toArray(), 201);
    }

    public function doUpdate(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $customer = Customer::find($id);

        if (!$customer) {
            static::badApiCall('Customer not found', 404);
        }

        $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
        $customer->update($data);
        static::jsonResponse($customer->toArray());
    }

    public function doDelete(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $customer = Customer::find($id);

        if (!$customer) {
            static::badApiCall('Customer not found', 404);
        }

        $customer->delete();
        static::jsonResponse(['deleted' => true]);
    }
}
