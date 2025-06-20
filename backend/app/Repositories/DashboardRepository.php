<?php

namespace App\Repositories;

use App\Models\OrderDetails;
use App\Models\Orders;
use App\Interfaces\DashboardRepositoryInterface;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getSalesSummary()
    {
        // Total orders and revenue
        $summary = Orders::with('orderDetails.pizza')
            ->get()
            ->flatMap(function ($order) {
                return $order->orderDetails->map(function ($detail) use ($order) {
                    return [
                        'order_id' => $order->order_id,
                        'quantity' => $detail->quantity,
                        'price' => $detail->pizza->price ?? 0,
                    ];
                });
            })
            ->groupBy('order_id')
            ->map(function ($details) {
                return [
                    'total_orders' => 1,
                    'total_revenue' => $details->sum(function ($detail) {
                        return $detail['quantity'] * $detail['price'];
                    }),
                ];
            })
            ->reduce(function ($carry, $item) {
                $carry['total_orders'] += $item['total_orders'];
                $carry['total_revenue'] += $item['total_revenue'];
                return $carry;
            }, ['total_orders' => 0, 'total_revenue' => 0.00]);

        // Top 5 pizzas by quantity
        $topPizzas = OrderDetails::with('pizza.pizzaType')
            ->get()
            ->groupBy(function ($detail) {
                return $detail->pizza->pizzaType->name;
            })
            ->map(function ($details) {
                return $details->sum('quantity');
            })
            ->sortDesc()
            ->take(5)
            ->map(function ($quantity, $name) {
                return ['name' => $name, 'total_quantity' => $quantity];
            })
            ->values();

        return [
            'total_orders' => $summary['total_orders'],
            'total_revenue' => $summary['total_revenue'],
            'top_pizzas' => $topPizzas,
        ];
    }

    public function getDailySalesTrend()
    {
        $trend = Orders::with('orderDetails.pizza')
            ->get()
            ->flatMap(function ($order) {
                return $order->orderDetails->map(function ($detail) use ($order) {
                    return [
                        'date' => $order->date,
                        'sales' => $detail->quantity * ($detail->pizza->price ?? 0),
                    ];
                });
            })
            ->groupBy('date')
            ->map(function ($details) {
                return [
                    'date' => $details->first()['date'],
                    'sales' => $details->sum('sales'),
                ];
            })
            ->values()
            ->sortBy('date')
            ->all();

        return $trend;
    }
}
