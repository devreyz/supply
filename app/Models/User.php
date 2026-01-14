<?php

namespace App\Models;

use App\Models\Shop\Customer;
use App\Notifications\ResetPassword;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'googleid',
        'zetools_id',
        'current_company_id',
        'zetools_token',
        'zetools_refresh_token',
        'token_expires_at',
        'service_access_expires_at',
        'last_access_check_at',
        'subscriptions_cache',
    ];

    protected $hidden = ['password', 'remember_token', 'zetools_token', 'zetools_refresh_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'token_expires_at' => 'datetime',
        'service_access_expires_at' => 'datetime',
        'last_access_check_at' => 'datetime',
        'subscriptions_cache' => 'array',
    ];

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    /**
     * Empresas do usuário (multi-tenant)
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Empresa atual selecionada
     */
    public function currentCompany(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'current_company_id');
    }

    /**
     * Cotações criadas pelo usuário
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'created_by');
    }

    /**
     * Verifica se o usuário tem acesso ao painel Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Usuário precisa ter uma empresa atual ou ser admin
        return $this->hasRole('admin') || $this->current_company_id !== null;
    }

    /**
     * Verifica se o usuário é owner de uma empresa
     */
    public function isOwnerOf(Company $company): bool
    {
        return $this->companies()
            ->wherePivot('role', 'owner')
            ->where('companies.id', $company->id)
            ->exists();
    }

    /**
     * Verifica se o usuário é admin de uma empresa
     */
    public function isAdminOf(Company $company): bool
    {
        return $this->companies()
            ->wherePivot('role', 'admin')
            ->where('companies.id', $company->id)
            ->exists();
    }
}

