<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Filament\Resources\EmployeeResource\RelationManagers\FilesRelationManager;
use App\Models\City;
use App\Models\Country;
use App\Models\Department;
use App\Models\Employee;
use App\Models\State;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Employee Management';

    protected static ?string $recordTitleAttribute = 'first_name';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->last_name;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'middle_name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Country' => $record->country->name,
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['country']);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::count() > 5 ? 'success' : 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Relationships')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->relationship(
                                name: 'country',
                                titleAttribute: 'name',
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function (Set $set) {
                                $set('state_id', null);
                                $set('city_id', null);
                            })
                            ->required(),
                        Forms\Components\Select::make('state_id')
                            ->options(fn(Get $get): Collection => State::query()
                                ->where('country_id', $get('country_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Set $set) => $set('city_id', null))
                            ->required(),
                        Forms\Components\Select::make('city_id')
                            ->options(fn(Get $get): Collection => City::query()
                                ->where('state_id', $get('state_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('team_id')
                            ->relationship(
                                name: 'team',
                                titleAttribute: 'name'
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('department_id')
                            ->options(fn(Get $get): Collection => Department::query()
                                ->where('team_id', $get('team_id'))
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])->columns(3),
                Forms\Components\Section::make('User Name')
                    ->description('Put the user name details in.')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),
                Forms\Components\Section::make('User Address')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip_code')
                            ->required()
                            ->maxLength(255),
                    ])->columns(2),
                Forms\Components\Section::make('Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                        Forms\Components\DatePicker::make('date_hired')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->required(),
                    ])->columns(2),

                Forms\Components\Section::make('Employee File Uploads')
                    ->schema([
                        Repeater::make('files') // ← refers to Employee::files()
                            ->relationship()
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('Upload File')
                                    ->directory('file/employee')
                                    ->visibility('public')
                                    ->storeFileNamesIn('file_name') // optional: only if column exists
                                    ->required(),
                            ])
                            ->label('Multiple Files')
                            ->createItemButtonLabel('Add File')
                            ->collapsible()
                            ->reorderable(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->heading('Employees')
            ->description('Manage your employees here.')
            ->reorderable('sort')
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),
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
                SelectFilter::make('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Department')
                    ->indicator('Department'),
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('created_until')
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['created_from'] ?? null) {
                            $indicators[] = Indicator::make('Created from ' . Carbon::parse($data['created_from'])->toFormattedDateString())
                                ->removeField('created_from');
                        }

                        if ($data['created_until'] ?? null) {
                            $indicators[] = Indicator::make('Created until ' . Carbon::parse($data['created_until'])->toFormattedDateString())
                                ->removeField('created_until');
                        }

                        return $indicators;
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title('Employee deleted.')
                            ->body('The Employee deleted successfully'),
                    )
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Employee Info')
                ->schema([
                    Section::make('Relationships')->schema([
                        TextEntry::make('country.name')->label('Country name'),
                        TextEntry::make('state.name')->label('State name'),
                        TextEntry::make('city.name')->label('City name'),
                        TextEntry::make('department.name')->label('Department name'),
                    ])->columns(2),

                    Section::make('User Name')
                        ->description('The user name details in.')
                        ->schema([
                            TextEntry::make('first_name')->label('First name'),
                            TextEntry::make('last_name')->label('Last name'),
                            TextEntry::make('middle_name')->label('Middle name'),
                        ])->columns(3),

                    Section::make('User Address')
                        ->schema([
                            TextEntry::make('address')->label('Address'),
                            TextEntry::make('zip_code')->label('Zip code'),
                        ])->columns(2),

                    Section::make('Dates')
                        ->schema([
                            TextEntry::make('date_of_birth')->label('Date of birth'),
                            TextEntry::make('date_hired')->label('Date of hiring'),
                        ])
                        ->columns(2),
                ])
        ]);
    }

    public static function getRelations(): array
    {
        return [
            FilesRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            // 'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
