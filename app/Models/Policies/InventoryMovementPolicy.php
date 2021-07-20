<?php

namespace hDSSolutions\Laravel\Models\Policies;

use HDSSolutions\Laravel\Models\InventoryMovement as Resource;
use HDSSolutions\Laravel\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InventoryMovementPolicy {
    use HandlesAuthorization;

    public function viewAny(User $user) {
        return $user->can('inventory_movements.crud.index');
    }

    public function view(User $user, Resource $resource) {
        return $user->can('inventory_movements.crud.show');
    }

    public function create(User $user) {
        return $user->can('inventory_movements.crud.create');
    }

    public function update(User $user, Resource $resource) {
        return $user->can('inventory_movements.crud.update');
    }

    public function delete(User $user, Resource $resource) {
        return $user->can('inventory_movements.crud.destroy');
    }

    public function restore(User $user, Resource $resource) {
        return $user->can('inventory_movements.crud.destroy');
    }

    public function forceDelete(User $user, Resource $resource) {
        return $user->can('inventory_movements.crud.destroy');
    }
}
