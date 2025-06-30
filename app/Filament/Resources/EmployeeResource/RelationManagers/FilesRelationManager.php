<?php

namespace App\Filament\Resources\EmployeeResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FilesRelationManager extends RelationManager
{
    protected static string $relationship = 'files';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Image Uploads')
                    ->schema([
                        FileUpload::make('file_path')
                            ->directory('file/employee')
                            ->visibility('public')
                            ->storeFileNamesIn('file_name')
                            ->required()
                            ->reorderable()
                        // ->multiple()
                    ])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('file_path')
            ->columns([
                Tables\Columns\TextColumn::make('file_name')->label('File Name'),
                Tables\Columns\ImageColumn::make('file_path')
                    ->circular(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
