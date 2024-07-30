<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Enums\OrderStatusEnum;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class OrdersChart extends ChartWidget
{
  protected static ?int $sort = 3;
  protected static ?string $heading = 'Chart';

  protected function getData(): array
  {
    $data = Order::select('status', DB::raw('count(*) as count'))
      ->groupBy('status')
      ->pluck('count', 'status')
      ->toArray();

    return [
      'datasets' => [
        [
          'label' => 'Orders',
          'data' => array_values($data),
          'backgroundColor' => [
            'rgba(255, 99, 132, 0.2)',
            'rgba(54, 162, 235, 0.2)',
            'rgba(255, 206, 86, 0.2)',
            'rgba(75, 192, 192, 0.2)',
          ],
          'borderColor' => [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
          ],
          'borderWidth' => 1,
        ],
      ],
      'labels' => array_map(fn ($enum) => $enum->value, OrderStatusEnum::cases()),
    ];
  }

  protected function getType(): string
  {
    return 'bar';
  }
}
