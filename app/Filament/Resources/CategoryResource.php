<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Set;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Enums\ProductTypeEnum;
use Filament\Resources\Resource;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\CategoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CategoryResource\RelationManagers;


class CategoryResource extends Resource
{
  protected static ?string $model = Category::class;

  protected static ?string $navigationIcon = 'heroicon-o-tag';

  protected static ?int $navigationSort = 4;
  protected static ?string $navigationGroup = 'Shop';
  public static function form(Form $form): Form
  {
    return $form
      ->schema([
        Group::make()->schema([
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
          MarkdownEditor::make('description')->columnSpanFull(),
        ])->columns(2),  // Correct method is 'columns', not 'column'
        Group::make()->schema([
          Section::make('status')->schema([
            Toggle::make('is_visible')->label('Visibility')
              ->helperText('Enable or disable category visibility')->default(true),
            Select::make('parent_id')->relationship('parent', 'name'),
          ])
        ])
      ]);
  }

  public static function table(Table $table): Table
  {
    return $table
      ->columns([
        TextColumn::make('name')->sortable()->searchable(),
        TextColumn::make('parent.name')->label('Parent')->sortable()->searchable(),
        IconColumn::make('is_visible')->label('Visibility')->boolean()->sortable(),
        TextColumn::make('updated_at')->date()->label('Updated Date')->sortable(),
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
      'index' => Pages\ListCategories::route('/'),
      'create' => Pages\CreateCategory::route('/create'),
      'edit' => Pages\EditCategory::route('/{record}/edit'),
    ];
  }
}
