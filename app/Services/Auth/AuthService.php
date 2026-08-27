<?php
namespace App\Services\Auth;

use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Hash;

use DomainException;

class AuthService
{
    public function login(string $name, string $password): array
    {
        $profile = Profile::with(['player', 'gameHistories'])->where('name', $name)->first();
        if (!$profile || !Hash::check($password, $profile->password))
            {
                throw new DomainException('Incorrect data', 401);
            }
        $profile->tokens()->delete();
        $token = $profile->createToken('auth-token')->plainTextToken;
        return ['profile' => $profile, 
        'token' => $token];
    }

    public function register(string $name, string $password): array
    {
        $profile = new Profile();
        $profile->name = $name;
        $profile->password = Hash::make($password);
        $profile->save();
        $token = $profile->createToken('auth-token')->plainTextToken;
        return ['profile' => $profile, 
        'token' => $token];
    }
}