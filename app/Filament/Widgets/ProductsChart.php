<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Product;
use Filament\Widgets\ChartWidget;

class ProductsChart extends ChartWidget
{
  protected static ?int $sort = 2;
  protected static ?string $heading = 'Chart';

  protected function getData(): array
  {
    $data = $this->getProductsPerMonth();
    return [
      'datasets' => [
        [
          'label' => 'Products Created',
          'data' => $data['productsPerMonth'],
          'borderColor' => '#FF6384',
          'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
        ],
      ],
      'labels' => $data['months'],
    ];
  }

  protected function getType(): string
  {
    return 'line';
  }

  private function getProductsPerMonth(): array
  {
    $now = Carbon::now();
    $productsPerMonth = [];
    $months = collect(range(1, 12))->map(function ($month) use ($now, &$productsPerMonth) {
      $count = Product::whereMonth('created_at', $month)
        ->whereYear('created_at', $now->year)
        ->count();
      $productsPerMonth[] = $count;
      return Carbon::create()->month($month)->format('M');
    })->toArray();

    return [
      'productsPerMonth' => $productsPerMonth,
      'months' => $months,
    ];
  }
}
