<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Collection;

#[Fillable(['name', 'email', 'password', 'first_login_at', 'last_login_at', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    protected $casts = [
        'first_login_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function getFirstLoginFormattedAttribute(): ?string
    {
        return $this->first_login_at?->locale('ru')->isoFormat('LLL');
    }

    public function getLastLoginFormattedAttribute(): ?string
    {
        return $this->last_login_at?->locale('ru')->isoFormat('LLL');
    }
}
