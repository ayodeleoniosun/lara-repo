<?php

namespace App\Repositories;

use App\Models\BusinessProfile;
use App\Models\File;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserProfilePicture;
use App\Repositories\Interfaces\FileRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository implements UserRepositoryInterface
{
    private User $user;

    protected FileRepositoryInterface $fileRepo;

    public function __construct(User $user, FileRepositoryInterface $fileRepo)
    {
        $this->user = $user;
        $this->fileRepo = $fileRepo;
    }

    public function getUsers(): Collection
    {
        return User::all();
    }

    public function getUser(string $slug): ?User
    {
        $user = $this->user->where('slug', $slug);

        if ($user->first()) {
            return $user->with('profile', 'businessProfile', 'pictures')->first();
        }

        return null;
    }

    public function getUserByEmailAddress(string $emailAddress): ?User
    {
        return $this->user->where('email_address', $emailAddress)?->first();
    }

    public function getDuplicateUserByPhoneNumber(string $phoneNumber, int $id): ?User
    {
        return $this->user->where('phone_number', $phoneNumber)->where('id', '<>', $id)->first();
    }

    public function updateProfile(array $data, User $user): User
    {
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->phone_number = $data['phone_number'];
        $user->update();

        $user->refresh();

        return $this->getUser($user->slug);
    }

    public function updateUserProfile(array $data, User $user): User
    {
        if (!$user->profile) {
            $user->profile = new UserProfile();
            $user->profile->user_id = $user->id;
        }

        $user->profile->state_id = $data['state'];
        $user->profile->city_id = $data['city'];
        $user->profile->id ? $user->profile->update() : $user->profile->save();

        return $user;
    }

    public function updatePassword(array $data, User $user): User
    {
        $user->password = bcrypt($data['new_password']);
        $user->update();

        return $user;
    }

    public function updateProfilePicture(string $path, User $user): User
    {
        $file = $this->fileRepo->create(['path' => $path]);

        $user->pictures = new UserProfilePicture();
        $user->pictures->user_id = $user->id;
        $user->pictures->profile_picture_id = $file->id;
        $user->pictures->save();

        $user->fresh();

        return $this->getUser($user->slug);
    }
}
