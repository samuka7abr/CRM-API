<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ActingAs {
	public function handle(Request $request, Closure $next){
		$id = $request->header('X-User-Id');
		if($id){
			$user = User::where('id', $id)->whereNull('deleted_at')->first();
			if($user){
				$request->setUserResolver(fn() => $user);
			}
		}
		return $next($request);
	}
}
