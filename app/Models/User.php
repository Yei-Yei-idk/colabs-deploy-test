<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Support\Str;
use App\Notifications\VerifyEmailCustom;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, MustVerifyEmailTrait;

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
     * Get the email address that should be used for verification.
     */
    public function getEmailForVerification()
    {
        return $this->user_correo;
    }

    /**
     * Booted method to handle model events.
     */
    protected static function booted()
    {
        static::updating(function ($user) {
            if ($user->isDirty('user_correo')) {
                $user->email_verified_at = null;
                $user->verification_token = null;
                $user->verification_token_expires_at = null;
            }
        });
    }

    /**
     * Override default sendEmailVerificationNotification
     * Genera un token en BD y lanza nuestra Notificación Custom.
     */
    public function sendEmailVerificationNotification()
    {
        if (!$this->verification_token || now()->gt($this->verification_token_expires_at)) {
            $this->verification_token = Str::random(60);
            $this->verification_token_expires_at = now()->addHour(); // Caduca en 1h
            $this->save();
        }

        $this->notify(new VerifyEmailCustom($this->verification_token));
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
