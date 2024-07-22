<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\ProductTypeEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\ProductResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
  protected static ?string $model = Product::class;

  protected static ?string $navigationIcon = 'heroicon-o-bolt';

  protected static ?string $navigationLabel = 'Products';

  protected static ?string $navigationGroup = 'Shop';
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Group::make()
          ->schema([
            Section::make()
              ->schema([
                TextInput::make('name')
                ->required()
                ->live(debounce: 600) 
                ->afterStateUpdated(function (string $operation, $state, Set $set) {
                    if ($operation !== 'create') {
                        return;
                    }
                    $set('slug', Str::slug($state));
                }),
            TextInput::make('slug')
                ->disabled()
                ->dehydrated()
                ->required()
                ->unique(Product::class, 'slug', ignoreRecord: true),
              ])->columns(2),
            Section::make('Pricing & Inventory')
              ->schema([
                TextInput::make('sku')->label("SKU (Stack keeping Unit)")->unique()->required(),
                TextInput::make('price')->required()->numeric()
                ->rules(['regex:/^\d+(\.\d{1,2})?$/']),
                TextInput::make('quantity')->required()->numeric()->minValue(0)->maxValue(100),
                Select::make('type')
                  ->options([
                    'downloadable' => ProductTypeEnum::DOWNLOADABLE->value,
                    'deliverable' => ProductTypeEnum::DELIVERABLE->value,
                  ])->required()
              ])->columns(2)
          ]),
        Group::make()
          ->schema([
            Section::make()
              ->schema([
                Toggle::make('is_visible')
                ->label('Visibility')->helperText('Enable or disable product visibility')->default(true),
                Toggle::make('is_featured')
                ->label('Featured')->helperText('Enable or disable products  featured status'),
                DatePicker::make('published_at')
                ->label('Availability')->default(now()),
              ]),

            Section::make('Image')
              ->schema([
                FileUpload::make('image')->directory('form-attachments')
                ->preserveFilenames()
                ->image()
                ->required()
                ->imageEditor(), // important method
              ])->collapsible(),

            Section::make('Associations')
              ->schema([
                Select::make('brand_id')
                  ->relationship('brand', 'name'),
              ])
          ]),

      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        ImageColumn::make('image'),
        TextColumn::make('name')
        ->searchable()->sortable(),
        TextColumn::make('brand.name')
        ->searchable()->sortable()->toggleable(),
        IconColumn::make('is_visible')->boolean()
        ->sortable()->toggleable()->label('Visibility'),
        TextColumn::make('price')
        ->searchable()->sortable(),
        TextColumn::make('quantity')
        ->searchable()->sortable(),
        TextColumn::make('published_at')
        ->date()->sortable(),
        TextColumn::make('type'),
      ])
      ->filters([
        TernaryFilter::make('is_visible')->label('Visibility')->boolean()->trueLabel('Only Visible Products')
        ->falseLabel('Only Hidden Products')
        ->native(false),
        SelectFilter::make('brand')->relationship('brand','name')
      ])
      ->actions([
        Tables\Actions\EditAction::make(),
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
      'index' => Pages\ListProducts::route('/'),
      'create' => Pages\CreateProduct::route('/create'),
      'edit' => Pages\EditProduct::route('/{record}/edit'),
    ];
  }
}
