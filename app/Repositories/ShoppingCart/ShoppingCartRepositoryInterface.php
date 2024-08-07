<?php

namespace App\Repositories\ShoppingCart;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface ShoppingCartRepositoryInterface extends EloquentRepositoryInterface
{
     public function getTotalPrice();
     public function clear();
     public function getItems();
     public function findByUserId($userId);
     public function getQuantityByProductId($productId);
     public function deleteByProductId($id);
}
