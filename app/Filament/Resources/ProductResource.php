<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationLabel = 'Produtos';

    protected static ?string $navigationGroup = 'Catálogo';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações Básicas')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nome do Produto')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\Select::make('category_id')
                            ->label('Categoria')
                            ->relationship(
                                'category',
                                'name',
                                fn(Builder $query) => $query->where('company_id', auth()->user()->current_company_id)
                            )
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->maxLength(65535),
                            ]),

                        Forms\Components\Select::make('unit')
                            ->label('Unidade')
                            ->options([
                                'UN' => 'Unidade',
                                'KG' => 'Quilograma',
                                'CX' => 'Caixa',
                                'FD' => 'Fardo',
                                'LT' => 'Litro',
                                'PC' => 'Pacote',
                                'DZ' => 'Dúzia',
                            ])
                            ->required()
                            ->default('UN'),

                        Forms\Components\TextInput::make('min_stock')
                            ->label('Estoque Mínimo')
                            ->numeric()
                            ->default(0)
                            ->suffix('unidades'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Produto Ativo')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Descrição')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Descrição')
                            ->maxLength(65535)
                            ->rows(4),
                    ]),

                Forms\Components\Section::make('Imagem')
                    ->schema([
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto do Produto')
                            ->image()
                            ->directory('products')
                            ->maxSize(2048)
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                '1:1',
                                '4:3',
                            ]),
                    ]),

                Forms\Components\Section::make('Códigos')
                    ->schema([
                        Forms\Components\Repeater::make('codes')
                            ->label('Códigos (EAN, Interno, etc)')
                            ->relationship('codes')
                            ->schema([
                                Forms\Components\TextInput::make('code')
                                    ->label('Código')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Select::make('type')
                                    ->label('Tipo')
                                    ->options([
                                        'ean' => 'EAN',
                                        'internal' => 'Código Interno',
                                        'supplier' => 'Código do Fornecedor',
                                        'other' => 'Outro',
                                    ])
                                    ->required()
                                    ->default('ean'),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Adicionar Código'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn() => 'https://ui-avatars.com/api/?name=Produto&color=7F9CF5&background=EBF4FF'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nome')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('codes.code')
                    ->label('EAN')
                    ->getStateUsing(fn($record) => $record->codes()->where('type', 'ean')->first()?->code ?? '-')
                    ->searchable(),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Unidade')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('min_stock')
                    ->label('Estoque Mín.')
                    ->numeric()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Ativo')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Categoria')
                    ->relationship(
                        'category',
                        'name',
                        fn(Builder $query) => $query->where('company_id', auth()->user()->current_company_id)
                    ),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Apenas Ativos')
                    ->falseLabel('Apenas Inativos'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('company_id', auth()->user()->current_company_id);
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
