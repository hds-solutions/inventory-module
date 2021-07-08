<?php

namespace hDSSolutions\Finpar\Models\Policies;

use hDSSolutions\Finpar\Models\InOut as Resource;
use HDSSolutions\Finpar\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InOutPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return $user->can('in_outs.crud.index');
    }

    public function view(User $user, Resource $resource) {
        return $user->can('in_outs.crud.show');
    }

    public function create(User $user) {
        return $user->can('in_outs.crud.create');
    }

    public function update(User $user, Resource $resource) {
        return $user->can('in_outs.crud.update');
    }

    public function delete(User $user, Resource $resource) {
        return $user->can('in_outs.crud.destroy');
    }

    public function restore(User $user, Resource $resource) {
        return $user->can('in_outs.crud.destroy');
    }

    public function forceDelete(User $user, Resource $resource) {
        return $user->can('in_outs.crud.destroy');
    }
}
