<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Lead;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends  ServiceProvider {
	public function boot(): void
	{
		Gate::define('users.manage', fn(User $user)=> $user->role === 'admin');
		Gate::define('users.delete', fn(User $user)=> $user->role === 'admin');

		Gate::define('Leads.modify', function(User $user, Lead $lead) {
			return $user->role === 'admin' || $lead->owner_id === $user->id;
		});
	}
}
