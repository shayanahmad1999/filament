<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeesFileResource\Pages;
use App\Filament\Resources\EmployeesFileResource\RelationManagers;
use App\Models\Employee;
use App\Models\EmployeesFile;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class EmployeesFileResource extends Resource
{
    protected static ?string $model = EmployeesFile::class;

    protected static ?string $navigationIcon = 'heroicon-o-viewfinder-circle';

    protected static ?string $navigationGroup = 'Employee Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Employee Info')
                    ->schema([
                        Forms\Components\Select::make('team_id')
                            ->relationship(
                                name: 'team',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('employee_id')
                            ->options(fn(Get $get): Collection => Employee::query()
                                ->where('team_id', $get('team_id'))
                                ->pluck('first_name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                    ])->columns(2),
                Forms\Components\Section::make('Image Uploads')
                    ->schema([
                        FileUpload::make('file_path')
                            ->directory('file/employee')
                            ->visibility('public')
                            ->storeFileNamesIn('file_name')
                            ->required()
                            ->reorderable()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee.first_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('file_name')->label('File Name'),

                // download a file directly
                // Tables\Columns\TextColumn::make('file_path')
                //     ->label('Document')
                //     ->formatStateUsing(function ($state) {
                //         $url = Storage::url($state);
                //         $ext = pathinfo($state, PATHINFO_EXTENSION);
                //         return "<a href='{$url}' target='_blank'>{$ext} File</a>";
                //     })
                //     ->html(),

                // download a file using with blade file
                Tables\Columns\ViewColumn::make('file_path')
                    ->view('tables.columns.download-button'),

                // view a image
                // Tables\Columns\ImageColumn::make('file_path')
                //     ->circular()
                //     ->stacked()
                //     ->extraImgAttributes(['loading' => 'lazy']),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('download_selected')
                        ->label('Download Selected Files')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            $zipFileName = 'documents_' . now()->format('Ymd_His') . '.zip';
                            $zipPath = storage_path("app/public/{$zipFileName}");

                            $zip = new ZipArchive();

                            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
                                foreach ($records as $record) {
                                    $relativePath = $record->file_path;
                                    $fullPath = storage_path('app/public/' . $relativePath);

                                    if (file_exists($fullPath)) {
                                        $zip->addFile($fullPath, basename($fullPath));
                                    } else {
                                        Log::warning("File not found: {$fullPath}");
                                    }
                                }
                                $zip->close();
                            } else {
                                abort(500, 'Unable to create ZIP archive.');
                            }

                            // Confirm the ZIP exists before download
                            if (!file_exists($zipPath)) {
                                abort(500, 'ZIP file was not created.');
                            }

                            return response()->download($zipPath)->deleteFileAfterSend(true);
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeesFiles::route('/'),
            'create' => Pages\CreateEmployeesFile::route('/create'),
            'edit' => Pages\EditEmployeesFile::route('/{record}/edit'),
        ];
    }
}
