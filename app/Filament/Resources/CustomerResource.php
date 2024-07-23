<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Customer;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CustomerResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CustomerResource\RelationManagers;

class CustomerResource extends Resource
{
  protected static ?string $model = Customer::class;

  protected static ?string $navigationIcon = 'heroicon-o-user-group';

  protected static ?int $navigationSort  = 2;

  protected static ?string $navigationGroup = 'Shop';
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
          Section::make([
            TextInput::make('name')->required()->maxValue(100),
            TextInput::make('email')->required()->unique(ignoreRecord:true)->label('Email Address')->email(),
            TextInput::make('phone')->required()->unique()->maxValue(11)->minValue(11),
            DatePicker::make('date_of_birth')->required(),
            TextInput::make('city')->required(),
            TextInput::make('zip_code')->required(),
            TextInput::make('address')->required()->columnSpanFull(),
          ])->columns(2)
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')->sortable()->searchable(),
        TextColumn::make('email')->sortable()->searchable(),
        TextColumn::make('phone')->sortable()->searchable(),
        TextColumn::make('city')->sortable()->searchable(),
        TextColumn::make('date_of_birth')->date()->sortable(),
      ])
      ->filters([
        //
      ])
      ->actions([
        ActionGroup::make([
          DeleteAction::make(),
          ViewAction::make(),
          Tables\Actions\EditAction::make(),
        ])
      ])
      ->bulkActions([
        Tables\Actions\BulkActionGroup::make([
          Tables\Actions\DeleteBulkAction::make(),
        ]),
      ]);
  }

  public static function getRelations(): array
  {
    return [
      //
    ];
  }

  public static function getPages(): array
  {
    return [
      'index' => Pages\ListCustomers::route('/'),
      'create' => Pages\CreateCustomer::route('/create'),
      'edit' => Pages\EditCustomer::route('/{record}/edit'),
    ];
  }
}
