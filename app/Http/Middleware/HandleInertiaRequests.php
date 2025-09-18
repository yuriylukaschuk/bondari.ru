<?php

namespace App\Http\Middleware;

use App\Http\Controllers\RazdelController;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Config;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';


    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }
    
    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'userdata' => [
                'orgFullName' => config('custom.orgFullName'),
                'orgShortName' => config('custom.orgShortName'),
                'email' => config('custom.email'),
                'phoneFull' => config('custom.phoneFull'),
                'phoneShort' => config('custom.phoneShort'),
                'telegramBot' => config('custom.telegramBot'),
                'max' => config('custom.max'),
                'vk' => config('custom.vk'),
                'ok' => config('custom.ok'),
                'rutube' => config('custom.rutube'),
                'site_url' => config('app.url')
            ],
        ];
    }
}
