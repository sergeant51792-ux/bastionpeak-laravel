<?php

declare(strict_types=1);

namespace App\Resources\Admin;

use App\Models\User;
use App\Models\Account;
use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CustomerResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Customers';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 10;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('accounts_count')
                    ->label('Accounts')
                    ->getStateUsing(fn ($record) => $record->accounts()->count()),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(['active' => 'Active', 'frozen' => 'Frozen', 'locked' => 'Locked', 'closed' => 'Closed']),
            ]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255),
                Forms\Components\TextInput::make('email')->email()->required()->unique(),
                Forms\Components\TextInput::make('phone')->maxLength(30),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'frozen' => 'Frozen', 'locked' => 'Locked', 'closed' => 'Closed'])
                    ->required(),
            ]);
    }
}
