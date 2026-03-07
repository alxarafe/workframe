<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Base\Controller\Controller;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Global search controller.
 */
class SearchController extends Controller
{
    public array $results = [];

    public function doIndex(): bool
    {
        $query = trim($_GET['q'] ?? '');

        if ($query === '') {
            $this->setDefaultTemplate('search_results');
            $this->addVariable('results', []);
            $this->addVariable('query', '');
            return true;
        }

        $like = '%' . $query . '%';

        // Search across multiple tables
        $customers = DB::table('customers')
            ->where('name', 'LIKE', $like)
            ->orWhere('contact', 'LIKE', $like)
            ->orWhere('email', 'LIKE', $like)
            ->get(['id', 'name', DB::connection()->raw("'customer' as type")]);

        $projectFiles = DB::table('project_files')
            ->where('name', 'LIKE', $like)
            ->get(['id', 'name', DB::connection()->raw("'project_file' as type")]);

        $workOrders = DB::table('work_orders')
            ->where('name', 'LIKE', $like)
            ->orWhere('locality', 'LIKE', $like)
            ->get(['id', 'name', DB::connection()->raw("'work_order' as type")]);

        $workers = DB::table('workers')
            ->where('name', 'LIKE', $like)
            ->get(['id', 'name', DB::connection()->raw("'worker' as type")]);

        $this->results = collect()
            ->merge($customers)
            ->merge($projectFiles)
            ->merge($workOrders)
            ->merge($workers)
            ->toArray();

        $this->addVariable('results', $this->results);
        $this->addVariable('query', $query);
        $this->setDefaultTemplate('search_results');
        return true;
    }
}
