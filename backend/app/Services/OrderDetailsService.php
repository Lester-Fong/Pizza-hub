<?php

namespace App\Services;

use App\Models\OrderDetails;
use Illuminate\Support\Facades\DB;
use Config;
use App\Jobs\ImportOrderDetailsJob;
use Illuminate\Support\Facades\Storage;
use Log;

class OrderDetailsService
{
    protected $order_details_model;

    public function __construct()
    {
        $this->order_details_model = new OrderDetails();
    }

    public function runOrderDetailsImportJob($file)
    {
        $tempPath = 'tmp/' . uniqid() . '.' . $file->getClientOriginalExtension();
        Storage::put($tempPath, file_get_contents($file->getRealPath()));

        if (!Storage::exists($tempPath)) {
            return response()->json(['error' => 'Temporary file storage failed'], 400);
        }

        // Create import job entry
        $jobId = DB::table('import_jobs')->insertGetId([
            'job_type' => 'order_details',
            'file_name' => $file->getClientOriginalName(),
            'status' => 'queued',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Dispatch async job with the storage path and job ID
        ImportOrderDetailsJob::dispatch(Storage::path($tempPath), $jobId)->onQueue('orders-import');

        return response()->json([
            'message' => 'Import job queued successfully. Check status for progress.',
            'status' => 'queued',
            'job_id' => $jobId
        ], 202);
    }


    // ========================  FOR INLINE SAVING OF ROWS (if preferred - not recommended for large files) ==================================
    public function importOrderDetailsCSV($file)
    {
        $csvArray = array_map('str_getcsv', file($file));

        foreach ($csvArray as $key => $value) {
            // Skip the first row since it is the header of the csv file
            if ($key == 0) {
                continue;
            } else {
                // add or update orderDetails to database
                $orderDetails = $this->order_details_model->where('order_details_id', $value[0])->first();
                if (!$orderDetails) {
                    $orderDetails = new OrderDetails();
                }
                $orderDetails->order_details_id = $value[0];
                $orderDetails->order_id = $value[1];
                $orderDetails->pizza_id = $value[2];
                $orderDetails->quantity = $value[3];
                $orderDetails->save();
            }
        }
        return response()->json(['success' => true, 'message' => 'CSV file is imported successfully'], 200);
    }
}
