<?php

namespace App\Filament\Pages\Settings;

use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class Settings extends BaseSettings
{

    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 10;

    public static function canAccess(): bool
    {
        if (auth()->user()->can('Manage Preferences')) {
            return true;
        }

        return false;
    }

    public static function getNavigationLabel(): string
    {
        return 'General Settings';
    }

    public function schema(): array|Closure
    {
        return [
            Tabs::make('Settings')
                ->columns(2)
                ->schema([
                    Tabs\Tab::make('General')
                        ->schema([
                            TextInput::make('general.name')
                                ->default("ets")
                                ->minLength(2)
                                ->maxLength(16)
                                ->label('Application Name')
                                ->helperText('The name of your application')
                                ->required(),
                            TextInput::make('general.url')
                                ->default("http://localhost")
                                ->url()
                                ->label('Application URL')
                                ->helperText('The URL of your application')
                                ->required(),
                        ]),
                    Tabs\Tab::make('Mail')
                        ->columns(3)
                        ->label('Mail Settings')
                        ->schema([
                            TextInput::make('mail.host'),
                            TextInput::make('mail.port')
                                ->type('number'),
                            Select::make('mail.encryption')
                                ->options([
                                    'TLS' => 'TLS',
                                    'STARTTLS' => 'STARTTLS',
                                    'none' => 'None',
                                ]),
                            TextInput::make('mail.username'),
                            TextInput::make('mail.password')
                                ->password(),
                            TextInput::make('mail.from')
                                ->label('From Address')
                                ->email()
                                ->helperText('The email address to send emails from'),
                        ]),
                ]),
        ];
    }
}