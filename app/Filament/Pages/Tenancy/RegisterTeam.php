<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Facades\DB;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register team';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('slug'),
            ]);
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::create($data);

        DB::table('countries')->whereNull('team_id')->update(['team_id' => $team->id]);
        DB::table('states')->whereNull('team_id')->update(['team_id' => $team->id]);
        DB::table('cities')->whereNull('team_id')->update(['team_id' => $team->id]);

        $team->members()->attach(auth()->user());

        return $team;
    }
}
