<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Enums\WorkflowStatus;
use App\Filament\Resources\AuditResource;
use App\Models\Control;
use App\Models\Implementation;
use App\Models\Standard;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\HtmlString;
use LucasGiovanny\FilamentMultiselectTwoSides\Forms\Components\Fields\MultiselectTwoSides;

class EditAudit extends EditRecord
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Edit Audit Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('title')
                            ->hint("Give the audit a distinctive title.")
                            ->default("My Title Here - DELETE ME")
                            ->required()
                            ->columns(1)
                            ->placeholder('2023 SOC 2 Type II Audit')
                            ->maxLength(255),
                        Select::make('manager_id')
                            ->label('Audit Manager')
                            ->hint("Who will be managing this audit?")
                            ->options(User::all()->pluck('name', 'id'))
                            ->columns(1)
                            ->searchable(),
                        Textarea::make('description')
                            ->columnSpanFull(),
                        DatePicker::make('start_date')
                            ->default(now())
                            ->required(),
                        DatePicker::make('end_date')
                            ->default(now()->addDays(30))
                            ->required(),
                    ])
            ]);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', [$this->record]);
    }

}
