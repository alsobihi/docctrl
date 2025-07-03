<?php

namespace App\Console\Commands;

use App\Models\EmployeeDocument;
use App\Models\Workflow;
use App\Models\WorkflowHistory;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckExpiredDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for expired documents and reopen workflows if needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for expired documents...');
        
        // Get documents that expired today
        $today = Carbon::today();
        $expiredDocuments = EmployeeDocument::where('expiry_date', $today)
            ->with(['employee', 'documentType'])
            ->get();
        
        $this->info("Found {$expiredDocuments->count()} documents that expired today.");
        
        $reopenedCount = 0;
        
        foreach ($expiredDocuments as $document) {
            $employee = $document->employee;
            $documentType = $document->documentType;
            
            if (!$employee || !$documentType) {
                continue;
            }
            
            // Create history record for document expiration
            WorkflowHistory::create([
                'employee_id' => $employee->id,
                'action' => 'document_expired',
                'details' => "{$documentType->name} expired on {$today->format('Y-m-d')}",
                'document_type_id' => $documentType->id,
                'document_id' => $document->id,
            ]);
            
            // Check if any completed workflows need to be reopened
            $reopenedWorkflows = Workflow::reopenWorkflowsForDocument($employee, $documentType, 'expired');
            $reopenedCount += $reopenedWorkflows;
            
            if ($reopenedWorkflows > 0) {
                $this->info("Reopened {$reopenedWorkflows} workflows for employee {$employee->full_name} due to expired {$documentType->name}");
            }
        }
        
        $this->info("Total workflows reopened: {$reopenedCount}");
        
        return Command::SUCCESS;
    }
}