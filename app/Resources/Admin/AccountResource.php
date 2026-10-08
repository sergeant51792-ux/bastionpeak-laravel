<?php

declare(strict_types=1);

namespace App\Resources\Admin;

use App\Models\Account;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Accounts';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 11;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('account_number')->searchable(),
                Tables\Columns\TextColumn::make('user.name')->label('Customer')->searchable(),
                Tables\Columns\TextColumn::make('currency.code')->label('Currency'),
                Tables\Columns\TextColumn::make('balance')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'frozen' => 'Frozen', 'locked' => 'Locked', 'closed' => 'Closed'])
                    ->required(),
                Forms\Components\TextInput::make('balance_cap')->numeric(),
            ]);
    }
}
