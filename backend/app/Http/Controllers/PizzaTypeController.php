<?php

namespace App\Http\Controllers;

use App\Models\PizzaType;
use App\Services\PizzaTypeService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PizzaTypeController extends Controller
{
    protected $pizzaTypeService;

    public function __construct(PizzaTypeService $pizzaTypeService)
    {
        $this->pizzaTypeService = $pizzaTypeService;
    }

    public function showPizzaTypes()
    {
        try {
            $pizzaTypes = PizzaType::all();
            return response()->json($pizzaTypes);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve pizza types: ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to retrieve pizza types'], 500);
        }
    }

    public function importPizzaTypes(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('csv_file');

        try {
            return $this->pizzaTypeService->importPizzaTypeCSV($file);
        } catch (\Exception $e) {
            Log::error('Import failed: ' . $e->getMessage(), ['exception' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to import CSV: ' . $e->getMessage()], 400);
        }
    }
}
