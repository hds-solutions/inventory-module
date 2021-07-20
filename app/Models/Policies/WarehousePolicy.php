<?php

namespace hDSSolutions\Laravel\Models\Policies;

use HDSSolutions\Laravel\Models\Warehouse as Resource;
use HDSSolutions\Laravel\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WarehousePolicy {
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return $user->can('warehouses.crud.index');
    }

    public function view(User $user, Resource $resource) {
        return $user->can('warehouses.crud.show');
    }

    public function create(User $user) {
        return $user->can('warehouses.crud.create');
    }

    public function update(User $user, Resource $resource) {
        return $user->can('warehouses.crud.update');
    }

    public function delete(User $user, Resource $resource) {
        return $user->can('warehouses.crud.destroy');
    }

    public function restore(User $user, Resource $resource) {
        return $user->can('warehouses.crud.destroy');
    }

    public function forceDelete(User $user, Resource $resource) {
        return $user->can('warehouses.crud.destroy');
    }
}
