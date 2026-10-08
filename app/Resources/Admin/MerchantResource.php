<?php

declare(strict_types=1);

namespace App\Resources\Admin;

use App\Models\Merchant;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;

class MerchantResource extends Resource
{
    protected static ?string $model = Merchant::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Merchants';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 13;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('receiving_cap')->label('Cap')->money(),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(255),
            Forms\Components\TextInput::make('category')->required()->maxLength(100),
            Forms\Components\TextInput::make('receiving_cap')->numeric()->required(),
            Forms\Components\Select::make('status')
                ->options(['active' => 'Active', 'deactivated' => 'Deactivated'])
                ->required(),
        ]);
    }
}
