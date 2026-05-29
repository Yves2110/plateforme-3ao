<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPermission
{
 /**
 * @param string $permissions Liste séparée par des virgules (OU logique)
 */
 public function handle(Request $request, Closure $next, string $permissions): Response
 {
 $user = $request->user();

 if (! $user) {
 abort(403);
 }

 if ($user->hasRole('super_admin')) {
 return $next($request);
 }

 $list = array_filter(array_map('trim', explode(',', $permissions)));

 foreach ($list as $permission) {
 if ($user->can($permission)) {
 return $next($request);
 }
 }

 abort(403, 'Vous n\'avez pas la permission d\'accéder à cette section.');
 }
}
