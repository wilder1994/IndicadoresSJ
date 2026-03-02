<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Document $document): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Document $document): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Document $document): bool
    {
        return $user->isAdmin();
    }
}
