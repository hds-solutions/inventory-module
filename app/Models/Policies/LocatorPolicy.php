<?php

namespace hDSSolutions\Finpar\Models\Policies;

use HDSSolutions\Finpar\Models\Locator as Resource;
use HDSSolutions\Finpar\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocatorPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return $user->can('locators.crud.index');
    }

    public function view(User $user, Resource $resource) {
        return $user->can('locators.crud.show');
    }

    public function create(User $user) {
        return $user->can('locators.crud.create');
    }

    public function update(User $user, Resource $resource) {
        return $user->can('locators.crud.update');
    }

    public function delete(User $user, Resource $resource) {
        return $user->can('locators.crud.destroy');
    }

    public function restore(User $user, Resource $resource) {
        return $user->can('locators.crud.destroy');
    }

    public function forceDelete(User $user, Resource $resource) {
        return $user->can('locators.crud.destroy');
    }
}
