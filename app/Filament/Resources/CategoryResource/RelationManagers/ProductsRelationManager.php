<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\Brand;
use App\Models\Product;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\ProductTypeEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\BrandResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\BrandResource\RelationManagers;

class ProductsRelationManager extends RelationManager
{
  protected static string $relationship = 'products';

  public function form(Form $form): Form
  {
    return $form
      ->schema([
        Tabs::make('Products')
          ->tabs([
            Tab::make('Information')
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
                MarkdownEditor::make('description')->columnSpan('full')
              ])->columns(2),

            Tab::make('Pricing & Inventory')
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
              ])->columns(2),
            Tab::make('Additional Information')
              ->schema([
                Toggle::make('is_visible')
                  ->label('Visibility')->helperText('Enable or disable product visibility')->default(true),
                Toggle::make('is_featured')
                  ->label('Featured')->helperText('Enable or disable product featured status'),
                DatePicker::make('published_at')
                  ->label('Availability')->default(now()),
                Select::make('categories')
                  ->relationship('categories', 'name')->multiple()->required(),
                Forms\Components\FileUpload::make('image')->directory('form-attachments')
                  ->preserveFilenames()
                  ->image()
                  ->required()
                  ->imageEditor()->columnSpanFull()
              ])->columns(2)
          ])->columnSpanFull()
      ]);
  }


  public function table(Table $table): Table
  {
    return $table
      ->recordTitleAttribute('name')
      ->columns([
        ImageColumn::make('image')
          ->label('Image')
          ->url(function ($record) {
            return $record->image ? \Storage::url($record->image) : null;
          }),
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
        //
      ])
      ->headerActions([
        Tables\Actions\CreateAction::make(),
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
}
