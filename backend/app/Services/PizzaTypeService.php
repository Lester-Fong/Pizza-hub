<?php

namespace App\Services;

use App\Models\PizzaType;
use Config;
use Log;

class PizzaTypeService
{
    protected $pizza_type_model;

    public function __construct()
    {
        $this->pizza_type_model = new PizzaType();
    }

    public function importPizzaTypeCSV($file)
    {

        $csvArray = array_map('str_getcsv', file($file));

        foreach ($csvArray as $key => $value) {
            // Skip the first row since it is the header of the csv file
            if ($key == 0) {
                continue;
            } else {
                // add or update pizzaType to database
                $pizzaType = $this->pizza_type_model->where('pizza_type_id', $value[0])->first();
                if (!$pizzaType) {
                    $pizzaType = new PizzaType();
                }

                $pizzaType->pizza_type_id = $value[0];
                $pizzaType->name = $value[1];
                $pizzaType->category = $value[2];
                $pizzaType->ingredients = mb_convert_encoding($value[3], 'UTF-8', 'UTF-8');
                $pizzaType->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'CSV file is imported successfully'], 200);
    }
}
