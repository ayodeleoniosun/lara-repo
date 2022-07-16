<?php

namespace App\Services\Interfaces;

use App\Models\PasswordReset;
use App\Models\User;

interface AccountServiceInterface
{
    public function register(array $data): User;

    public function login(array $data): array;

    public function forgotPassword(array $data): ?PasswordReset;

    public function resetPassword(array $data): User;
}
