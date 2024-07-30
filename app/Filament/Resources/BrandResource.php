<?php

namespace App\Filament\Resources;

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
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
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
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Filament\Resources\BrandResource\RelationManagers\ProductsRelationManager;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort =  1 ;
    protected static ?string $navigationGroup = 'Shop';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                  ->schema([
                    Section::make([
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
                      ->unique(),
                      TextInput::make('url')
                        ->label('Website URL')
                        ->required()
                        ->unique() 
                      ->columnSpan('full'),
                      MarkdownEditor::make('description')
                      ->columnSpan('full')                               
                    ])->columns(2)
                ]),
                Group::make()
                  ->schema([
                    Section::make('Status')
                      ->schema([
                        Toggle::make('is_visible')
                          ->label('Visibility')
                          ->helperText('Enable or disable brand visibility')
                          ->default(true)
                      ]),

                      Group::make()
                        ->schema([
                          Section::make('color')
                            ->schema([
                              ColorPicker::make('primary_hex')
                                ->label('Primary Color')
                            ])
                        ])
                  ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('url')->label('Website Url')->sortable()->searchable(),
                ColorColumn::make('primary_hex')->label('Primary Color'),
                IconColumn::make('is_visible')->boolean()->sortable()->label('Visibility'),
                TextColumn::make('updated_at')->date()->sortable()
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
            ProductsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBrands::route('/'),
            'create' => Pages\CreateBrand::route('/create'),
            'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
