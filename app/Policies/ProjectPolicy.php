<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProjectPolicy
{
    /**
     * Perform pre-authorization checks.
     * Admins can do anything.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    /**
     * Determine whether the user can update the model.
     * This will control access to the team management page.
     */
    public function update(User $user, Project $project): bool
    {
        // 1. Allow if user is a Plant Manager for the project's plant
        if ($user->role === 'manager' && $user->plant_id === $project->plant_id) {
            return true;
        }

        // 2. Allow if user is assigned as a 'manager' on this specific project
        // Note: This assumes the user's employee record is linked to their user record.
        // This is a more advanced step we haven't implemented yet.
        // For now, we will focus on the Plant Manager role.

        return false;
    }

    // Add other policy methods (view, create, delete) as needed...
}



