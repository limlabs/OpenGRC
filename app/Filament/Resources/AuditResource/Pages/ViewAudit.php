<?php

namespace App\Filament\Resources\AuditResource\Pages;

use App\Enums\WorkflowStatus;
use App\Filament\Resources\AuditResource;
use App\Models\Audit;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;

class ViewAudit extends ViewRecord
{
    protected static string $resource = AuditResource::class;

    protected function getHeaderWidgets(): array
    {
        $record = $this->getRecord();

        switch ($record->status) {
            case WorkflowStatus::NOTSTARTED:
                $message = 'This audit has not yet been started. The audit manager can use the workflow actions above to set the state
                of this audit.';
                $bgcolor = 'grcblue';
                $fgcolor = 'white';
                $icon = 'heroicon-m-information-circle';
                break;
            case WorkflowStatus::COMPLETED:
                $message = 'This audit has been marked as complete. An administrator will need to reopen the audit if necessary.';
                $bgcolor = 'grcblue';
                $fgcolor = 'white';
                $icon = 'heroicon-m-exclamation-circle';
                break;
            default:
                return [];
        }

        return [
            AuditResource\Widgets\TextWidget::make([
                'message' => $message,
                'bg_color' => $bgcolor,
                'fg_color' => $fgcolor,
                'icon' => $icon,
            ]),
        ];
    }

    protected function getHeaderActions(): array
    {
        $record = $this->record;

        return [
            Actions\EditAction::make()
                ->label('Edit')
                ->icon('heroicon-m-pencil')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button(),
            ActionGroup::make([
                Action::make('ActionsButton')
                    ->label('Transition to In Progress')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Begin Audit')
                    ->modalDescription('Are you sure you want to begin this audit?')
                    ->modalSubmitActionLabel('Yes, start the audit!')
                    ->disabled(function (Audit $record, $livewire) {

                        if ($record->manager_id != auth()->id()) {
                            return true; // Disable if not the audit manager
                        }

                        if ($record->status == WorkflowStatus::INPROGRESS) {
                            return true; // Disable if already in progress
                        }

                        if (auth()->user()->hasRole('Super Admin')) {
                            return false; // Enable for Super Admin
                        }

                        if ($record->manager_id == auth()->id() && $record->status != WorkflowStatus::COMPLETED) {
                            return false; // Enable for Audit Manager
                        }

                        if ($record->status == WorkflowStatus::COMPLETED && auth()->user()->hasRole('Super Admin')) {
                            return false; // Enable for super admin when status is COMPLETED
                        }

                        return true; // Disable for everyone else

                    })
                    ->action(function (Audit $record, $livewire) {
                        $record->update(['status' => WorkflowStatus::INPROGRESS]);
                        $livewire->redirectRoute('filament.app.resources.audits.view', $record);
                    }),
                Action::make('complete_audit')
                    ->label('Transition to Complete')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Complete Audit')
                    ->modalDescription('Are you sure you want to complete this audit? Ths can only be undone by an administrator.')
                    ->modalSubmitActionLabel('Yes, complete this audit!')
                    ->modalIconColor('danger')
                    ->disabled(function (Audit $record, $livewire) {
                        if ($record->manager_id != auth()->id()) {
                            return true; // Disable if not the audit manager
                        }

                        if (auth()->user()->hasRole('Super Admin')) {
                            return false; // Enable for Super Admin
                        }

                        if ($record->manager_id != auth()->id()) {
                            return true; // Disable if not the audit manager
                        }

                        if ($record->status == WorkflowStatus::INPROGRESS) {
                            return false; // Disable if already in progress
                        }

                        return true;
                    })
                    ->action(function (Audit $record, $livewire) {

                        foreach ($record->auditItems as $auditItem) {
                            // If the audit item is not completed, mark it as completed
                            $auditItem->update(['status' => WorkflowStatus::COMPLETED]);

                            // If the audit item is an implementation, update the effectiveness
                            if ($auditItem->auditable_type == 'App\Models\Implementation') {
                                $auditItem->implementation()->update(['effectiveness' => $auditItem->effectiveness]);
                            }
                        }

                        //Save the final audit report
                        $auditItems = $record->auditItems;
                        $reportTemplate = 'reports.audit';
                        if ($record->audit_type == 'implementations') {
                            $reportTemplate = 'reports.implementation-report';
                        }
                        $filepath = "app/private/audit_reports/AuditReport-{$record->id}.pdf";
                        $pdf = Pdf::loadView($reportTemplate, ['audit' => $record, 'auditItems' => $auditItems]);
                        $pdf->save(storage_path($filepath));

                        // Mark the audit as completed
                        $record->update(['status' => WorkflowStatus::COMPLETED]);
                        $livewire->redirectRoute('filament.app.resources.audits.view', $record);
                    }),
            ])
                ->label('Workflow')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button(),
            ActionGroup::make([
                Action::make('ReportsButton')
                    ->label('Download Audit Report')
                    ->size(ActionSize::Small)
                    ->color('primary')
                    ->disabled(function (Audit $record) {
                        $record->load('members');

                        if ($record->status == WorkflowStatus::NOTSTARTED) {
                            return true;
                        } elseif ($record->manager_id != auth()->id() && $record->members->doesntContain(auth()->user())) {
                            return true;
                        } else {
                            return false;
                        }
                    })
                    ->action(function (Audit $audit, $livewire) {
                        if ($audit->status == WorkflowStatus::COMPLETED) {
                            $filepath = "app/private/audit_reports/AuditReport-{$this->record->id}.pdf";
                            if (file_exists(storage_path($filepath)) && is_readable(storage_path($filepath))) {
                                return response()->download(storage_path($filepath));
                            } else {
                                return Notification::make()
                                    ->title('Error')
                                    ->body('The final audit report is not available until the audit has been completed.')
                                    ->danger()
                                    ->send();
                            }
                        } else {
                            $auditItems = $audit->auditItems;
                            $reportTemplate = 'reports.audit';
                            if ($audit->audit_type == 'implementations') {
                                $reportTemplate = 'reports.implementation-report';
                            }
                            $pdf = Pdf::loadView($reportTemplate, ['audit' => $audit, 'auditItems' => $auditItems]);

                            return response()->streamDownload(
                                fn () => print ($pdf->stream()),
                                "DRAFT-AuditReport-{$audit->id}.pdf");
                        }
                    }
                    ),
            ])
                ->label('Reports')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button(),
        ];
    }
}
