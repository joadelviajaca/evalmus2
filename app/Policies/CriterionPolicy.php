<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Criterion;
use Illuminate\Auth\Access\HandlesAuthorization;

class CriterionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Criterion');
    }

    public function view(AuthUser $authUser, Criterion $criterion): bool
    {
        return $authUser->can('View:Criterion');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Criterion');
    }

    public function update(AuthUser $authUser, Criterion $criterion): bool
    {
        return $authUser->can('Update:Criterion');
    }

    public function delete(AuthUser $authUser, Criterion $criterion): bool
    {
        return $authUser->can('Delete:Criterion');
    }

    public function restore(AuthUser $authUser, Criterion $criterion): bool
    {
        return $authUser->can('Restore:Criterion');
    }

    public function forceDelete(AuthUser $authUser, Criterion $criterion): bool
    {
        return $authUser->can('ForceDelete:Criterion');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Criterion');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Criterion');
    }

    public function replicate(AuthUser $authUser, Criterion $criterion): bool
    {
        return $authUser->can('Replicate:Criterion');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Criterion');
    }

}