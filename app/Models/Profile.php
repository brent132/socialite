<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function profileImage()
    {
        // Check if this profile has an image
        if ($this->image) {
            return '/storage/' . $this->image;
        }
        
        // If the default avatar file exists in storage, use it
        if (Storage::disk('public')->exists('profile/default-avatar.png')) {
            return '/storage/profile/default-avatar.png';
        }
        
        // Fallback to a placeholder avatar service if the file doesn't exist
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->user->username) . '&color=7F9CF5&background=EBF4FF';
    }

    public function backgroundImage()
    {
        return $this->background ? '/storage/' . $this->background : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}



