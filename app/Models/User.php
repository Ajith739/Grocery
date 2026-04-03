<?php

// app/Models/User.php
// A Model represents a database table
// The User model represents the 'users' table

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// IMPORTANT: Import HasApiTokens from Sanctum
// This adds the ability to create/manage API tokens
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    // USE these traits (traits are like reusable code packages)
    use HasApiTokens,    // Adds createToken(), tokens() methods
        HasFactory,      // Adds factory() method for testing
        Notifiable;      // Adds notification support

    /*
    |--------------------------------------------------------------------------
    | FILLABLE
    |--------------------------------------------------------------------------
    | These are the fields that can be mass-assigned
    | When we do User::create([...]), only these fields are allowed
    | This is a SECURITY feature to prevent unwanted field injection
    */
    protected $fillable = [
        'name',      // User's full name
        'phone',     // User's phone number
        'email',     // User's email address
        'password',  // User's password (will be stored as hash)
    ];

    /*
    |--------------------------------------------------------------------------
    | HIDDEN
    |--------------------------------------------------------------------------
    | These fields are hidden when the model is converted to JSON
    | We don't want to send password hash to the frontend!
    */
    protected $hidden = [
        'password',        // Never expose password in API responses
        'remember_token',  // Never expose remember token
        'id',              // Hide internal ID (optional, for security)
    ];

    /*
    |--------------------------------------------------------------------------
    | CASTS
    |--------------------------------------------------------------------------
    | Automatically convert these fields to specific types
    */
    protected $casts = [
        'email_verified_at' => 'datetime',  // Convert to Carbon datetime object
        'password' => 'hashed',             // Auto-hash when setting (Laravel 10+)
    ];
}