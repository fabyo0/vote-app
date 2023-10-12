<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function ideas(): HasMany
    {
        return $this->hasMany(Idea::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }


    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(Vote::class, 'votes');
    }


    public function getAvatar(): string
    {
        //  Kullanıcının e-posta adresinin ilk karakterini alın
        $firstCharacter = $this->email[0];

        // İlk karakterin bir sayı olup olmadığını kontrol edin
        $integerToUse = is_numeric($firstCharacter)
            ? ord(strtolower($firstCharacter)) - 21
            : ord(strtolower($firstCharacter)) - 96;

        //  Gravatar için avatar URL'sini oluşturun
        return 'https://www.gravatar.com/avatar/'
            . md5($this->email) // Kullanıcının e-posta adresini md5 ile hashleyin
            . '?s=200' // Avatarın boyutunu 200x200 piksel olarak ayarlayın
            . '&d=https://s3.amazonaws.com/laracasts/images/forum/avatars/default-avatar-'
            . $integerToUse
            . '.png'; // Varsayılan avatarın URL'sini oluşturun
    }


    public function isAdmin():bool
    {
        return $this->email == 'emredikmen002@gmail.com';
    }
}
