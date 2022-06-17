<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property integer $id
 * @property integer $type
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $gender
 * @property string $username
 * @property string $password
 */
class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;


    public const TYPE_ADMIN = 2;
    public const TYPE_SELLER = 1;
    public const TYPE_BUYER = 0;
    public const SLUG_ADMIN = 'admin';
    public const SLUG_SELLER = 'seller';
    public const SLUG_BUYER = 'buyer';
    public const TYPE_SLUGS = [
        self::TYPE_BUYER => self::SLUG_BUYER,
        self::TYPE_SELLER => self::SLUG_SELLER,
        self::TYPE_ADMIN => self::SLUG_ADMIN
    ];


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected array $fillable = [
        'first_name',
        'last_name',
        'email',
        'type',
        'gender',
        'username',
        'password'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function isSeller(): bool
    {
        return  $this->type === self::TYPE_SELLER;
    }

    public function isAdmin(): bool
    {
        return  $this->type === self::TYPE_ADMIN;
    }

    public function isBuyer(): bool
    {
        return  $this->type === self::TYPE_BUYER;
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function products(): HasManyThrough
    {
        return $this->hasManyThrough(Product::class, Shop::class);
    }
}
