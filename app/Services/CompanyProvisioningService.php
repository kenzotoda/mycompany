<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class CompanyProvisioningService
{
    public function provision(User $user): Company
    {
        if ($user->company_id) {
            return $user->company;
        }

        return DB::transaction(function () use ($user): Company {
            $company = Company::create([
                'name' => $user->name,
                'trade_name' => $user->name,
                'document' => $this->uniquePlaceholderDocument(),
                'email' => $user->email,
            ]);

            $user->forceFill([
                'company_id' => $company->id,
                'access_level' => $user->access_level ?: 'admin',
            ])->save();

            $adminRole = Role::query()->where('name', 'admin')->first();

            if ($adminRole && ! $user->hasRole('admin')) {
                $user->assignRole($adminRole);
            }

            return $company;
        });
    }

    private function uniquePlaceholderDocument(): string
    {
        do {
            $document = strtoupper(substr(md5(uniqid('company', true)), 0, 18));
        } while (Company::query()->where('document', $document)->exists());

        return $document;
    }
}
