<?php

namespace App\Console\Commands;

use App\Models\EmployeeDocument;
use App\Models\EmployeeWorkflow;
use App\Models\Workflow;
use App\Notifications\DocumentExpiringNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendWorkflowNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflows:send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for expiring documents in workflows';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for documents that need notifications...');
        
        // Get all workflows with notification settings
        $workflows = Workflow::whereNotNull('notification_days_before')
            ->where('notification_days_before', '>', 0)
            ->get();
        
        $notificationsSent = 0;
        
        foreach ($workflows as $workflow) {
            $notificationDays = $workflow->notification_days_before;
            
            // Get all documents for employees in this workflow that will expire in exactly notification_days_before days
            $expiryDate = Carbon::today()->addDays($notificationDays);
            
            $employeeWorkflows = EmployeeWorkflow::where('workflow_id', $workflow->id)
                ->with(['employee.documents' => function($query) use ($expiryDate) {
                    $query->where('expiry_date', $expiryDate);
                }, 'employee.documents.documentType'])
                ->get();
            
            foreach ($employeeWorkflows as $employeeWorkflow) {
                $employee = $employeeWorkflow->employee;
                
                if (!$employee) {
                    continue;
                }
                
                $expiringDocuments = $employee->documents->filter(function($document) use ($workflow) {
                    // Check if this document type is required by the workflow
                    return $workflow->documentTypes->contains('id', $document->document_type_id);
                });
                
                if ($expiringDocuments->isEmpty()) {
                    continue;
                }
                
                // Get users who should be notified (e.g., managers of the employee's plant)
                $usersToNotify = $this->getUsersToNotify($employee);
                
                if ($usersToNotify->isEmpty()) {
                    continue;
                }
                
                // Send notification
                Notification::send($usersToNotify, new DocumentExpiringNotification(
                    $employee,
                    $workflow,
                    $expiringDocuments,
                    $notificationDays
                ));
                
                // Update last notification timestamp
                $employeeWorkflow->update([
                    'last_notification_at' => now(),
                ]);
                
                $notificationsSent += $usersToNotify->count();
                
                $this->info("Sent notifications for {$employee->full_name}'s expiring documents in workflow '{$workflow->name}'");
            }
        }
        
        $this->info("Total notifications sent: {$notificationsSent}");
        
        return Command::SUCCESS;
    }
    
    /**
     * Get users who should be notified about this employee's documents
     */
    private function getUsersToNotify($employee)
    {
        // This is a placeholder - in a real application, you would implement logic
        // to determine who should be notified (e.g., plant managers, project managers, etc.)
        return \App\Models\User::where('role', 'manager')
            ->where(function($query) use ($employee) {
                $query->where('plant_id', $employee->plant_id)
                    ->orWhere('role', 'admin');
            })
            ->get();
    }
}