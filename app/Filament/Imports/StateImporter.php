<?php

namespace App\Filament\Imports;

use App\Models\State;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class StateImporter extends Importer
{
    protected static ?string $model = State::class;

    public static function getColumns(): array
    {
        return [
            // ImportColumn::make('team')
            //     ->requiredMapping()
            //     ->rules(['required']),
            ImportColumn::make('country')
                ->relationship(resolveUsing: 'name')
                ->rules(['required']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?State
    {
        return State::firstOrNew([
            'name' => $this->data['name'],
            'country_id' => $this->data['country'],
        ]);

        return new State();
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your state import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
