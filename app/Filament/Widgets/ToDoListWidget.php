<?php

namespace App\Filament\Widgets;

use App\Models\DataRequestResponse;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\HtmlString;

class ToDoListWidget extends BaseWidget
{
    protected int|string|array $columnSpan = '2';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DataRequestResponse::query()->where('requestee_id', auth()->id())
            )
            ->emptyStateHeading(new HtmlString("You're all caught up!"))
            ->emptyStateIcon('heroicon-o-check-circle')
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
