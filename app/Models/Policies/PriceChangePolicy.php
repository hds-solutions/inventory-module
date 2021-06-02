<?php

namespace hDSSolutions\Finpar\Models\Policies;

use HDSSolutions\Finpar\Models\PriceChange as Resource;
use HDSSolutions\Finpar\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PriceChangePolicy {
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return $user->can('price_changes.crud.index');
    }

    public function view(User $user, Resource $resource) {
        return $user->can('price_changes.crud.show');
    }

    public function create(User $user) {
        return $user->can('price_changes.crud.create');
    }

    public function update(User $user, Resource $resource) {
        return $user->can('price_changes.crud.update');
    }

    public function delete(User $user, Resource $resource) {
        return $user->can('price_changes.crud.destroy');
    }

    public function restore(User $user, Resource $resource) {
        return $user->can('price_changes.crud.destroy');
    }

    public function forceDelete(User $user, Resource $resource) {
        return $user->can('price_changes.crud.destroy');
    }
}
