<?php

namespace App\Http\Controllers;

use App\Services\OrdersService;
use Illuminate\Http\Request;
use Log;
use App\Jobs\ImportOrdersJob;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $ordersService;

    public function __construct(OrdersService $ordersService)
    {
        $this->ordersService = $ordersService;
    }

    public function importOrders(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('csv_file');
            return $this->ordersService->runOrdersImportJob($file);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function importStatus($jobId)
    {
        $job = DB::table('import_jobs')->where('id', $jobId)->first();
        if ($job) {
            $progress = $job->total_records > 0 ? ($job->processed_records / $job->total_records) * 100 : 0;
            return response()->json([
                'status' => $job->status,
                'progress' => number_format($progress, 2) . '%',
                'total_records' => $job->total_records,
                'processed_records' => $job->processed_records,
                'error' => $job->error
            ]);
        }
        return response()->json(['status' => 'not found'], 404);
    }
}
