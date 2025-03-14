<?php

namespace App\Filament\Pages\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class MailSchema
{
    public static function schema(): array
    {
        return [
            Select::make('mail.mailer')
                ->label('Mail Provider')
                ->options([
                    'smtp' => 'SMTP',
                    'ses' => 'Amazon SES',
                    'sendmail' => 'Sendmail',
                    'mailgun' => 'Mailgun',
                    'postmark' => 'Postmark',
                    'log' => 'Log',
                    'array' => 'Array',
                ])
                ->default('smtp')
                ->helperText('Select the mail service provider to use'),
            TextInput::make('mail.host')
                ->helperText('Only required for SMTP'),
            TextInput::make('mail.port')
                ->type('number')
                ->helperText('Only required for SMTP'),
            Select::make('mail.encryption')
                ->options([
                    'TLS' => 'TLS',
                    'STARTTLS' => 'STARTTLS',
                    'none' => 'None',
                ])
                ->helperText('Only required for SMTP'),
            TextInput::make('mail.username')
                ->helperText('Only required for SMTP'),
            TextInput::make('mail.password')
                ->password()
                ->helperText('Only required for SMTP'),
            TextInput::make('mail.from')
                ->label('From Address')
                ->email()
                ->helperText('The email address to send emails from'),
        ];
    }
}  