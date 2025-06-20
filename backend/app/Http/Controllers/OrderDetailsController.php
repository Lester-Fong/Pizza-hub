<?php

namespace App\Http\Controllers;

use App\Models\OrderDetails;
use App\Services\OrderDetailsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;

class OrderDetailsController extends Controller
{
    protected $order_details_service;

    public function __construct(OrderDetailsService $orderDetailsService)
    {
        $this->order_details_service = $orderDetailsService;
    }

    public function showOrderDetails()
    {
        try {
            $orderDetails = OrderDetails::all();
            return response()->json($orderDetails);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve order details: ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to retrieve order details'], 500);
        }
    }

    public function importOrderDetails(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        try {
            $file = $request->file('csv_file');
            return $this->order_details_service->runOrderDetailsImportJob($file);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Failed to import order details.'], 500);
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
