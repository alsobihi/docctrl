<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Project; // <-- Add this
use App\Policies\ProjectPolicy; // <-- Add this
use App\Models\User; // <-- Add this
use Illuminate\Support\Facades\Gate; // <-- Add this





class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Project::class => ProjectPolicy::class, // <-- Add this line
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //

         // Define the 'admin' gate
        Gate::define('admin', function (User $user) {
            return $user->role === 'admin';
        });
    }
}
