<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderResource;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
  protected static ?int $sort = 4;
  protected int | string | array $columnSpan='full';
  public function table(Table $table): Table
  {
    return $table
      ->query(OrderResource::getEloquentQuery())->defaultPaginationPageOption(5)
      ->defaultSort('created_at','description')
      ->columns([
        TextColumn::make('number')->searchable()->sortable(),
        TextColumn::make('customer.name')->toggleable()->searchable()->sortable(),
        TextColumn::make('status')->searchable()->sortable(),
        TextColumn::make('created_at')
          ->label('Order Date')->date(),
      ]);
  }
}
