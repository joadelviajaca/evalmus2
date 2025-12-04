<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Rubric;
use Illuminate\Auth\Access\HandlesAuthorization;

class RubricPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Rubric');
    }

    public function view(AuthUser $authUser, Rubric $rubric): bool
    {
        return $authUser->can('View:Rubric');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Rubric');
    }

    public function update(AuthUser $authUser, Rubric $rubric): bool
    {
        return $authUser->can('Update:Rubric');
    }

    public function delete(AuthUser $authUser, Rubric $rubric): bool
    {
        return $authUser->can('Delete:Rubric');
    }

    public function restore(AuthUser $authUser, Rubric $rubric): bool
    {
        return $authUser->can('Restore:Rubric');
    }

    public function forceDelete(AuthUser $authUser, Rubric $rubric): bool
    {
        return $authUser->can('ForceDelete:Rubric');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Rubric');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Rubric');
    }

    public function replicate(AuthUser $authUser, Rubric $rubric): bool
    {
        return $authUser->can('Replicate:Rubric');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Rubric');
    }

}