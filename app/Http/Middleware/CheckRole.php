<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Доступ запрещен');
        }

        // Проверка ролей
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Для Inertia возвращаем ответ, который будет обработан на фронтенде
        if ($request->inertia()) {
            abort(403, 'Недостаточно прав');
        }

        return redirect('/')->with('error', 'Недостаточно прав');
    }
}
