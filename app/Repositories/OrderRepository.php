<?php
namespace App\Repositories;

use App\Models\Order;

class OrderRepository {
    public function createOrder($orderData) {
        return Order::create($orderData);
    }
}