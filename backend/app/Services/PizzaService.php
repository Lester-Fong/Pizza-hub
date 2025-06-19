<?php

namespace App\Services;

use App\Models\Pizza;
use Config;
use Log;
use Maatwebsite\Excel\Facades\Excel;

class PizzaService
{
    protected $pizza_model;

    public function __construct()
    {
        $this->pizza_model = new Pizza();
    }

    public function importPizzaCSV($file)
    {
        $csvArray = array_map('str_getcsv', file($file));

        foreach ($csvArray as $key => $value) {
            // Skip the first row since it is the header of the csv file
            if ($key == 0) {
                continue;
            } else {
                // add or update pizza to database
                $pizza = $this->pizza_model->where('pizza_id', $value[0])->first();
                if (!$pizza) {
                    $pizza = new Pizza();
                }
                $pizza->pizza_id = $value[0];
                $pizza->pizza_type_id = $value[1];
                $pizza->size = $value[2];
                $pizza->price = $value[3];
                $pizza->save();
            }
        }
        return response()->json(['success' => true, 'message' => 'CSV file is imported successfully'], 200);
    }
}
