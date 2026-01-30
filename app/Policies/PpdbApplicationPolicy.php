<?php

namespace App\Policies;

use App\Models\PpdbApplication;
use App\Models\User;

class PpdbApplicationPolicy
{
    public function view(User $user, PpdbApplication $app): bool
    {
        return $user->isAdmin() || $app->user_id === $user->id;
    }

    public function viewPaymentProof(User $user, PpdbApplication $app): bool
    {
        return $this->view($user, $app);
    }

    public function editAsUser(User $user, PpdbApplication $app): bool
    {
        // user hanya boleh edit aplikasi miliknya sendiri
        return $app->user_id === $user->id;
    }
}
