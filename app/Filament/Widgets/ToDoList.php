<?php

namespace App\Filament\Widgets;

use App\Models\DataRequestResponse;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ToDoList extends BaseWidget
{
    protected int|string|array $columnSpan = '2';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DataRequestResponse::query()->where('requestee_id', auth()->id())
            )
            ->columns([
                Tables\Columns\TextColumn::make('request.title')
                    ->label('Request Title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('request.description')
                    ->label('Description')
                    ->wrap(),
            ]);
    }
}
