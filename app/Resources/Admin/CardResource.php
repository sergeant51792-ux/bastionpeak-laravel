<?php

declare(strict_types=1);

namespace App\Resources\Admin;

use App\Models\Card;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;
use Filament\Forms\Form;

class CardResource extends Resource
{
    protected static ?string $model = Card::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationLabel = 'Cards';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 14;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('card_number_masked')->label('Card'),
                Tables\Columns\TextColumn::make('user.name')->label('Holder'),
                Tables\Columns\TextColumn::make('account.name')->label('Linked account'),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('valid_to')->date(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('per_transaction_cap')->numeric()->label('Per-transaction cap'),
            Forms\Components\TextInput::make('daily_cap')->numeric()->label('Daily cap'),
            Forms\Components\TextInput::make('monthly_cap')->numeric()->label('Monthly cap'),
            Forms\Components\Select::make('status')
                ->options(['active' => 'Active', 'frozen' => 'Frozen', 'cancelled' => 'Cancelled'])
                ->required(),
        ]);
    }
}
