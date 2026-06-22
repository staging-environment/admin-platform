<?php

namespace App\Filament\Widgets;

use App\Models\PageView;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class MostVisitedPagesWidget extends BaseWidget
{
    protected static ?string $heading = 'Páginas Más Visitadas (Top 15)';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PageView::query()
                    ->selectRaw('path, count(*) as visits_count, count(distinct ip_address) as unique_visitors_count')
                    ->groupBy('path')
            )
            ->columns([
                Tables\Columns\TextColumn::make('path')
                    ->label('Ruta / Página')
                    ->searchable(),
                Tables\Columns\TextColumn::make('visits_count')
                    ->label('Visitas Totales')
                    ->alignEnd()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unique_visitors_count')
                    ->label('Visitantes Únicos')
                    ->alignEnd()
                    ->sortable(),
            ])
            ->defaultSort('visits_count', 'desc')
            ->paginated(false);
    }

    public function getTableRecordKey(\Illuminate\Database\Eloquent\Model|array $record): string
    {
        return is_array($record) ? (string) ($record['path'] ?? '') : (string) $record->path;
    }
}
