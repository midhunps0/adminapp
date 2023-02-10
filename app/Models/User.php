<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ynotz\AccessControl\Traits\WithRoles;
use Ynotz\MediaManager\Contracts\MediaOwner;
use Ynotz\MediaManager\Traits\OwnsMedia;

class User extends Authenticatable implements MediaOwner
{
    use HasApiTokens, HasFactory, Notifiable, WithRoles, OwnsMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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

    public function getMediaVariants(): array
    {
        return [
            'photo' => [
                'process_on_upload' => false,
                'variants' => [
                    'thumbnail' => [
                        'size' => '100 x 100',
                        'conversion' => 'resize',
                        'custom_method' => null
                    ],
                ],
            ]
        ];
    }

    public function getMediaStorage(): array
    {
        return [
            'photo' => [
                'disk' => 'local',
                'folder' => 'public/images/photo'
            ]
        ];
    }
}
