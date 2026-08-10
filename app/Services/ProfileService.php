<?php
namespace App\Services;

use App\Models\Profile;
use App\Http\Resources\ProfileResource;
use Illuminate\Support\Facades\Hash;

use DomainException;

class ProfileService
{
    public function delete(Profile $profile, string $password): void
    {
        if (!Hash::check($password, $profile->password))
            {
                throw new DomainException('Incorrect password', 401);
            }
        $profile->tokens()->delete();
        $profile->delete();
    }

    public function rename(Profile $profile, string $newName): ProfileResource
    {
        $profile->name = $newName;
        $profile->save();
        return new ProfileResource($profile);
    }

    public function exit(Profile $profile): void
    {
        $profile->currentAccessToken()->delete();
    }

    public function showStats(Profile $profile): ProfileResource
    {
        return new ProfileResource($profile);
    }
}