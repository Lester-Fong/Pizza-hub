<?php

namespace App\Http\Controllers;

use App\Models\Pizza;
use App\Services\PizzaService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PizzaController extends Controller
{
    protected $pizzaService;

    public function __construct(PizzaService $pizzaService)
    {
        $this->pizzaService = $pizzaService;
    }

    public function showPizza()
    {
        try {
            $pizzas = Pizza::all();
            Log::debug(print_r($pizzas, true));
            return response()->json($pizzas);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve pizzas: ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to retrieve pizzas'], 500);
        }
    }

    public function importPizza(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv',
        ]);

        $file = $request->file('csv_file');

        try {
            return $this->pizzaService->importPizzaCsv($file);
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to import CSV: ' . $e->getMessage()], 400);
        }
    }
}
