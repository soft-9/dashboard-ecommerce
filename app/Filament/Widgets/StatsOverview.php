<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\Customer;
use App\Enums\OrderStatusEnum;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatsOverview extends BaseWidget
{
  protected static ?int $sort = 1;
  protected static ?string $pollingInterval = '16s';
  protected static bool $isLazy = true;
  protected function getStats(): array
  {
    return [
      Stat::make('Total Customers', Customer::count())
        ->description('Increase in customers')->descriptionIcon('heroicon-m-arrow-trending-up')
        ->color('success')->chart([1, 2, 3, 5, 8, 3, 10, 9, 15, 18, 20, 15, 25]),
      Stat::make('Total Products', Product::count())
        ->description('Total Products in App')->descriptionIcon('heroicon-m-arrow-trending-down')
        ->color('danger')->chart([1, 2, 3, 5, 8, 3, 10, 9, 15, 18, 20, 15, 5]),
      Stat::make('Pending Orders', Order::where('status', OrderStatusEnum::PENDING->value)->count())
        ->descriptionIcon('heroicon-m-arrow-trending-down')
        ->color('danger')->chart([1, 2, 3, 5, 8, 3, 10, 9, 15, 18, 20, 15, 5]),
    ];
  }
}
