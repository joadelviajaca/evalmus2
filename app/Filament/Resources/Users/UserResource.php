<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Collection;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';
    protected static string|UnitEnum|null $navigationGroup = 'Administración';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $pluralLabel = 'Usuarios';

    protected static ?string $modelLabel = 'usuario';
    protected static ?string $plurarModelLabel = 'usuarios';

    public static function form(Schema $schema): Schema
    {
        // return UserForm::configure($schema);
        return $schema
            ->schema([
                Section::make('Información del usuario')
                ->schema([
                    TextInput::make('name')
                        ->required() // cannot empty
                        ->maxLength(255), // max char 255
                    TextInput::make('email')
                        ->required() // cannot empty
                        ->email() // email validation
                        ->maxLength(255), // max char 255

                ]),
                Section::make('Contraseña')
                ->schema([
                    TextInput::make('password')
                        ->required() // cannot empty
                        ->password() //  password text input
                        ->revealable() // hide show password
                        ->maxLength(255), // max char 255

                ]),
                Section::make('Roles')
                ->schema([
                    CheckboxList::make('roles')
                    ->relationship('roles', 'name')
                    ->columns(2)
                    ->helperText('Selecciona uno o varios roles para el usuario')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')->badge()
                    ->label('Roles')
                    ->colors(['primary'])
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                ->visible(fn (User $record): bool => Auth::id() !== $record->getKey()), // Evitamos que no se pueda borrar a sí mismo
            ])
            ->toolbarActions([
                BulkAction::make('delete')
                ->requiresConfirmation()
                ->action(fn (Collection $records) => $records->each->delete())
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
