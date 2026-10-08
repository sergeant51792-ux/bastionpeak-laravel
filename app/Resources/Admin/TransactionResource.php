<?php

declare(strict_types=1);

namespace App\Resources\Admin;

use App\Models\Transaction;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-left-right';
    protected static ?string $navigationLabel = 'Transactions';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 12;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_id')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('amount')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')->options([
                    'credit' => 'Credit', 'debit' => 'Debit',
                    'deposit_credit' => 'Deposit credit', 'payment_debit' => 'Payment debit',
                ]),
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending_review' => 'In review', 'approved' => 'Approved', 'rejected' => 'Rejected',
                    'credited' => 'Credited', 'reversed' => 'Reversed',
                ]),
            ]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('memo')->maxLength(500)->columnSpanFull(),
        ]);
    }
}
