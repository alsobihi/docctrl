<?php


namespace App\Services;

use App\Models\DocumentType;
use App\Models\Employee;
use Carbon\Carbon;

class ValidityRuleService
{
    /**
     * Calculate the expiry date for a given document type and employee.
     *
     * @param DocumentType $documentType
     * @param Employee $employee
     * @param Carbon $issueDate
     * @return Carbon|null
     */
    public function calculateExpiryDate(DocumentType $documentType, Employee $employee, Carbon $issueDate): ?Carbon
    {
        $rule = $documentType->validity_rule;

        if (!$rule || !isset($rule['type'])) {
            return null; // No rule defined
        }

        switch ($rule['type']) {
            case 'fixed':
                return $this->handleFixedRule($issueDate, $rule);

            case 'dependent':
                return $this->handleDependentRule($employee, $rule);

            default:
                return null;
        }
    }

    /**
     * Handle a fixed duration validity rule.
     * e.g., {"type": "fixed", "days": 365}
     */
    protected function handleFixedRule(Carbon $issueDate, array $rule): ?Carbon
    {
        if (!isset($rule['days'])) {
            return null;
        }
        return $issueDate->copy()->addDays($rule['days']);
    }

    /**
     * Handle a rule dependent on other documents.
     * e.g., {"type": "dependent", "logic": "min", "dependencies": [{"document_type_id": 5}, ...]}
     */
    protected function handleDependentRule(Employee $employee, array $rule): ?Carbon
    {
        if (!isset($rule['dependencies']) || !is_array($rule['dependencies'])) {
            return null;
        }

        $dependencyIds = array_column($rule['dependencies'], 'document_type_id');

        // Get the expiry dates of the required documents the employee already has
        $dependencyDates = $employee->documents()
            ->whereIn('document_type_id', $dependencyIds)
            ->pluck('expiry_date');

        if ($dependencyDates->isEmpty() || $dependencyDates->count() < count($dependencyIds)) {
            // The employee is missing one or more of the required dependency documents
            return null;
        }

        // Apply the logic (min or max)
        if (isset($rule['logic']) && $rule['logic'] === 'max') {
            return $dependencyDates->max();
        }

        // Default to 'min' logic
        return $dependencyDates->min();
    }
}

