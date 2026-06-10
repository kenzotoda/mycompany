<?php

namespace App\Http\Middleware;

use App\Services\CompanyProvisioningService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasCompany
{
    public function __construct(private readonly CompanyProvisioningService $companyProvisioning)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->company_id) {
            $this->companyProvisioning->provision($user);
            $user->refresh();
        }

        return $next($request);
    }
}
