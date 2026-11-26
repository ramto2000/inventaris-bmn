<?php

namespace App\Filament\Widgets;

use App\Models\Asset;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class AssetConditionWidget extends TableWidget
{
    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Asset::query()
                    ->selectRaw('MIN(id) as id, kondisi, COUNT(*) as total')
                    ->groupBy('kondisi')
            )
            ->columns([
                Tables\Columns\TextColumn::make('kondisi')
                    ->label('Kondisi'),

                Tables\Columns\TextColumn::make('total')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
            ])
            ->defaultSort('total', 'desc');
    }
}