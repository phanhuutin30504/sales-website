<?php

namespace App\Repositories\ShoppingCart;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\ShoppingCart\ShoppingCartRepository as AdminShoppingCartRepository;
use App\Models\ShoppingCart;
use App\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;

class ShoppingCartRepository extends EloquentRepository implements ShoppingCartRepositoryInterface
{

    public function getModel()
    {
        return ShoppingCart::class;
    }


    public function getTotalPrice()
    {
        $sellingPrices = $this->model->pluck('price_selling');
        return $sellingPrices->toArray();
    }


    public function clear()
    {
        $this->model->truncate();
    }

    public function getItems()
    {
        return $this->model->all()->toArray();
    }
    public function findByUserId($userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function getQuantityByProductId($productId)
    {
        return $this->model->where('product_id', $productId)->get('quantity');
    }

    public function deleteByProductId($id)
    {
        return $this->model->where('product_id', $id)->delete();
    }
}
