<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\Orders;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ImportOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $jobId;

    public function __construct($filePath, $jobId)
    {
        $this->filePath = $filePath;
        $this->jobId = $jobId;
    }

    public function handle()
    {
        DB::table('import_jobs')->where('id', $this->jobId)->update(['status' => 'processing']);

        $batchSize = 5000;
        $importedCount = 0;

        try {
            if (!file_exists($this->filePath)) {
                throw new \Exception('CSV file not found');
            }

            $csv = array_map('str_getcsv', file($this->filePath));
            $totalRecords = count($csv) - 1; // Subtract header
            DB::table('import_jobs')->where('id', $this->jobId)->update(['total_records' => $totalRecords]);

            $dataToInsert = [];
            foreach ($csv as $key => $row) {
                if ($key === 0) continue; // Skip header
                if (isset($row[0]) && $row[0] !== '' && $row[0] !== null) {
                    $dataToInsert[] = [
                        'order_id' => $row[0],
                        'date' => $row[1] ?? null,
                        'time' => $row[2] ?? null,
                    ];
                    if (count($dataToInsert) >= $batchSize) {
                        Orders::insert($dataToInsert);
                        $importedCount += count($dataToInsert);
                        DB::table('import_jobs')->where('id', $this->jobId)->update(['processed_records' => $importedCount]);
                        $dataToInsert = [];
                    }
                } else {
                }
            }

            if (!empty($dataToInsert)) {
                Orders::insert($dataToInsert);
                $importedCount += count($dataToInsert);
                DB::table('import_jobs')->where('id', $this->jobId)->update(['processed_records' => $importedCount]);
            }
        } catch (\Exception $e) {
            throw $e;
        } finally {
            if (file_exists($this->filePath)) {
                Storage::delete(str_replace(Storage::path(''), '', $this->filePath));
            }
        }
    }
}
