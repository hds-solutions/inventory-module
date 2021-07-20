<?php

namespace hDSSolutions\Laravel\Models\Policies;

use HDSSolutions\Laravel\Models\Inventory as Resource;
use HDSSolutions\Laravel\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return $user->can('inventories.crud.index');
    }

    public function view(User $user, Resource $resource) {
        return $user->can('inventories.crud.show');
    }

    public function create(User $user) {
        return $user->can('inventories.crud.create');
    }

    public function update(User $user, Resource $resource) {
        return $user->can('inventories.crud.update');
    }

    public function delete(User $user, Resource $resource) {
        return $user->can('inventories.crud.destroy');
    }

    public function restore(User $user, Resource $resource) {
        return $user->can('inventories.crud.destroy');
    }

    public function forceDelete(User $user, Resource $resource) {
        return $user->can('inventories.crud.destroy');
    }
}
