<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuarios';
    protected $primaryKey = 'user_id';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'user_nombre',
        'user_correo',
        'user_telefono',
        'user_contrasena',
        'rol_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'user_contrasena',
        'remember_token',
    ];

    public function getAuthPasswordName()
    {
        return 'user_contrasena';
    }

    /**
     * Get the e-mail address where password reset links are sent.
     */
    public function getEmailForPasswordReset()
    {
        return $this->user_correo;
    }

    /**
     * Route notifications for the mail channel.
     */
    public function routeNotificationForMail($notification = null)
    {
        return $this->user_correo;
    }
    
    public function getAvatarColorAttribute()
    {
        $colors = ['purple', 'green', 'orange', 'blue'];
        $lastDigit = (int) substr((string) $this->user_id, -1);
        return $colors[$lastDigit % 4];
    }

    public function getAvatarInitialAttribute()
    {
        return strtoupper(substr($this->user_nombre, 0, 1));
    }

    public function getFirstNameAttribute()
    {
        return explode(' ', trim($this->user_nombre))[0];
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'user_contrasena' => 'hashed',
        ];
    }
}
