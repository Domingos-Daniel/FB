<?php

namespace App\Observers;

use App\Models\WorkflowOrcamento;
use Filament\Notifications\Notification;

class WorkflowOrcamentoObserver
{
    /**
     * Handle the WorkflowOrcamento "created" event.
     */
    public function created(WorkflowOrcamento $workflowOrcamento): void
    {
    }

    /**
     * Handle the WorkflowOrcamento "updated" event.
     */
    public function updated(WorkflowOrcamento $workflowOrcamento): void
    {
        Notification::make()
            ->title('Observer')
            ->body('The WorkflowOrcamento was updated!')
            ->danger()
            ->sendToDatabase(\auth()->user());
    }

    /**
     * Handle the WorkflowOrcamento "deleted" event.
     */
    public function deleted(WorkflowOrcamento $workflowOrcamento): void
    {
        //
    }

    /**
     * Handle the WorkflowOrcamento "restored" event.
     */
    public function restored(WorkflowOrcamento $workflowOrcamento): void
    {
        //
    }

    /**
     * Handle the WorkflowOrcamento "force deleted" event.
     */
    public function forceDeleted(WorkflowOrcamento $workflowOrcamento): void
    {
        //
    }
}
