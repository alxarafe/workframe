<?php

declare(strict_types=1);

namespace Modules\WorkFrame\Controller;

use Alxarafe\Attribute\Menu;
use Alxarafe\Base\Controller\Controller;
use Alxarafe\Service\PdfService;
use Illuminate\Database\Capsule\Manager as DB;

/**
 * Report controller — generates worker and customer reports.
 */
#[Menu(menu: 'main_menu', label: 'Informes', icon: 'fas fa-chart-pie', order: 50, permission: 'WorkFrame.Report', route: 'WorkFrame.Report')]
class ReportController extends Controller
{
    #[Menu(menu: 'main_menu', label: 'Listado de informes', icon: 'fas fa-list', parent: 'WorkFrame.Report', order: 1, permission: 'WorkFrame.Report')]
    public function doIndex(): bool
    {
        $this->setDefaultTemplate('reports_index');
        return true;
    }

    /**
     * Worker report: hours worked, orders, parts.
     */
    #[Menu(menu: 'main_menu', label: 'Informe de operarios', icon: 'fas fa-hard-hat', parent: 'WorkFrame.Report', order: 10, permission: 'WorkFrame.Report')]
    public function doWorkers(): bool
    {
        $workerId = isset($_GET['worker_id']) ? (int) $_GET['worker_id'] : null;
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-t');

        $query = DB::table('part_workers as pw')
            ->join('work_parts as wp', 'pw.work_part_id', '=', 'wp.id')
            ->join('work_orders as wo', 'wp.work_order_id', '=', 'wo.id')
            ->join('workers as w', 'pw.worker_id', '=', 'w.id')
            ->whereBetween('wp.date', [$dateFrom, $dateTo])
            ->select([
                'w.id as worker_id',
                'w.name as worker_name',
                'wo.id as work_order_id',
                'wo.name as work_order_name',
                'wp.id as work_part_id',
                'wp.date',
                'pw.morning_from',
                'pw.morning_to',
                'pw.afternoon_from',
                'pw.afternoon_to',
                'pw.going_start',
                'pw.going_end',
                'pw.back_start',
                'pw.back_end',
                'pw.allowances',
            ])
            ->orderBy('w.name')
            ->orderBy('wp.date');

        if ($workerId) {
            $query->where('w.id', $workerId);
        }

        $data = $query->get()->toArray();

        if (isset($_GET['format']) && $_GET['format'] === 'pdf') {
            $html = '<h1>Worker Report</h1>';
            $html .= '<p>Period: ' . $dateFrom . ' to ' . $dateTo . '</p>';
            $html .= '<table border="1" cellpadding="4"><tr><th>Worker</th><th>Date</th><th>Order</th><th>Morning</th><th>Afternoon</th></tr>';

            foreach ($data as $row) {
                $row = (array) $row;
                $morning = ($row['morning_from'] ?? '') . '-' . ($row['morning_to'] ?? '');
                $afternoon = ($row['afternoon_from'] ?? '') . '-' . ($row['afternoon_to'] ?? '');
                $html .= '<tr><td>' . $row['worker_name'] . '</td><td>' . $row['date'] . '</td><td>' . $row['work_order_name'] . '</td><td>' . $morning . '</td><td>' . $afternoon . '</td></tr>';
            }

            $html .= '</table>';

            $pdfContent = \Alxarafe\Service\PdfService::htmlToPdf($html);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="worker_report.pdf"');
            echo $pdfContent;
            exit;
        }

        $workers = DB::table('workers')->where('active', true)->get(['id', 'name']);
        $this->addVariable('report_data', $data);
        $this->addVariable('workers', $workers);
        $this->addVariable('date_from', $dateFrom);
        $this->addVariable('date_to', $dateTo);
        $this->addVariable('worker_id', $workerId);
        $this->setDefaultTemplate('report_workers');
        return true;
    }

    /**
     * Customer report: project files, work orders, billing.
     */
    #[Menu(menu: 'main_menu', label: 'Informe de clientes', icon: 'fas fa-users', parent: 'WorkFrame.Report', order: 20, permission: 'WorkFrame.Report')]
    public function doCustomers(): bool
    {
        $customerId = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : null;
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-t');

        $query = DB::table('project_files as pf')
            ->join('customers as c', 'pf.customer_id', '=', 'c.id')
            ->leftJoin('work_orders as wo', 'pf.id', '=', 'wo.project_file_id')
            ->leftJoin('work_parts as wp', 'wo.id', '=', 'wp.work_order_id')
            ->select([
                'c.id as customer_id',
                'c.name as customer_name',
                'pf.id as project_file_id',
                'pf.name as project_file_name',
                'wo.id as work_order_id',
                'wo.name as work_order_name',
                DB::connection()->raw('COUNT(wp.id) as part_count'),
            ])
            ->groupBy('c.id', 'c.name', 'pf.id', 'pf.name', 'wo.id', 'wo.name')
            ->orderBy('c.name')
            ->orderBy('pf.name');

        if ($customerId) {
            $query->where('c.id', $customerId);
        }

        $data = $query->get()->toArray();

        $customers = DB::table('customers')->where('active', true)->get(['id', 'name']);
        $this->addVariable('report_data', $data);
        $this->addVariable('customers', $customers);
        $this->addVariable('date_from', $dateFrom);
        $this->addVariable('date_to', $dateTo);
        $this->addVariable('customer_id', $customerId);
        $this->setDefaultTemplate('report_customers');
        return true;
    }
}
