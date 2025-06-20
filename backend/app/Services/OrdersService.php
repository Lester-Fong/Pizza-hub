<?php

namespace App\Services;

use App\Models\Orders;
use Illuminate\Support\Facades\DB;
use App\Jobs\ImportOrdersJob;
use Illuminate\Support\Facades\Storage;


class OrdersService
{
    protected $orders_model;

    public function __construct()
    {
        $this->orders_model = new Orders();
    }

    public function runOrdersImportJob($file)
    {
        $tempPath = 'tmp/' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::put($tempPath, file_get_contents($file->getRealPath()));

        if (!Storage::exists($tempPath)) {
            return response()->json(['error' => 'Temporary file storage failed'], 400);
        }

        // Create import job entry
        $jobId = DB::table('import_jobs')->insertGetId([
            'job_type' => 'orders',
            'file_name' => $file->getClientOriginalName(),
            'status' => 'queued',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dispatch async job with the storage path and job ID
        ImportOrdersJob::dispatch(Storage::path($tempPath), $jobId)->onQueue('orders-import');

        return response()->json([
            'message' => 'Import job queued successfully. Check status for progress.',
            'status' => 'queued',
            'job_id' => $jobId
        ], 202);
    }

    // ========================  FOR INLINE SAVING OF ROWS (if preferred - not recommended for large files) ==================================
    public function importOrdersCSV($file)
    {
        $csvArray = array_map('str_getcsv', file($file));

        foreach ($csvArray as $key => $value) {
            // Skip the first row since it is the header of the csv file
            if ($key == 0) {
                continue;
            } else {
                // add or update orders to database
                $orders = $this->orders_model->where('order_id', $value[0])->first();
                if (!$orders) {
                    $orders = new Orders();
                }
                $orders->order_id = $value[0];
                $orders->date = $value[1];
                $orders->time = $value[2];
                $orders->save();
            }
        }
        return response()->json(['success' => true, 'message' => 'CSV file is imported successfully'], 200);
    }
}
