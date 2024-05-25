<?php

namespace App\Admin\Http\Requests\Order;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Order\OrderPaymentMethod;
use App\Enums\Order\OrderShippingMethod;
use App\Enums\Order\OrderStatus;
use App\Enums\Product\ProductUnit;
use App\Enums\User\UserRoles;
use Illuminate\Validation\Rules\Enum;

    class OrderRequest extends BaseRequest
    {
        public function methodPost(){
            return [
                'order.user_id' => ['nullable', 'exists:App\Models\User,id'],
                'order.customer_fullname' => ['nullable', 'string'],
                'order.customer_email' => ['nullable', 'email'],
                'order.customer_phone' => ['nullable', 'regex:/((09|03|07|08|05)+([0-9]{8})\b)/'],
                'order.payment_method' => ['nullable', new Enum(OrderPaymentMethod::class)],
                'order.shipping_address' => ['nullable'],
                'order.customer_role' => ['nullable', new Enum(UserRoles::class)],
                'order.shipping_method' => ['nullable', new Enum(OrderShippingMethod::class)],
                'order.note' => ['nullable'],
                'order_detail.product_id' => ['nullable', 'array'],
                'order_detail.product_id.*' => ['nullable', 'exists:App\Models\Product,id'],
                'order_detail.product_qty' => ['nullable', 'array'],
                'order_detail.product_qty.*' => ['nullable', 'integer', 'min:1'],
                'order_detail.product_unit' => ['nullable', 'array'],
                'order_detail.product_unit.*' => ['required'],
                'order_detail.product_qty_donate' => ['nullable', 'array'],
                'order_detail.product_qty_donate.*' => ['nullable', 'integer', 'min:0'],
            ];
        }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */

    protected function methodPut()
    {
        return [
            'order.id' => ['nullable', 'exists:App\Models\Order,id'],
            'order.status' => ['nullable', new Enum(OrderStatus::class)],
            'order.user_id' => ['nullable', 'exists:App\Models\User,id'],
            'order.customer_fullname' => ['nullable', 'string'],
            'order.customer_email' => ['nullable', 'email'],
            'order.customer_phone' => ['nullable', 'regex:/((09|03|07|08|05)+([0-9]{8})\b)/'],
            'order.shipping_address' => ['nullable'],
            'order.note' => ['nullable'],
            // 'order_detail.id' => ['required', 'array'],
            // 'order_detail.product_id' => ['required', 'array'],
            // 'order_detail.product_id.*' => ['required', 'exists:App\Models\Product,id'],
            // 'order_detail.product_qty' => ['required', 'array'],
            // 'order_detail.product_qty.*' => ['required', 'integer', 'min:1'],
            // 'order_detail.product_qty_donate' => ['nullable', 'array'],
            // 'order_detail.product_qty_donate.*' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function methodGet(){
        if($this->routeIs('admin.order.render_info_shipping')){
            return [
                'user_id' => ['required', 'exists:App\Models\User,id']
            ];
        }elseif ($this->routeIs('admin.order.check_user_role')){
            return [
                'user_id' => ['required', 'exists:App\Models\User,id']
            ];
        }
        elseif($this->routeIs('admin.order.add_product')){
            return [
                'user_id' => ['nullable', 'exists:App\Models\User,id'],
                'product_id' => ['nullable', 'exists:App\Models\Product,id'],
                'product_variation_id' => ['nullable', 'exists:App\Models\ProductVariation,id'],
            ];
        }elseif($this->routeIs('admin.order.calculate_total_before_save_order')){
            return [
                'order.user_id' => ['nullable', 'exists:App\Models\User,id'],
                'order_detail.product_id.*' => ['nullable', 'exists:App\Models\Product,id'],
                'order_detail.product_variation_id.*' => ['nullable'],
                'order_detail.product_qty.*' => ['nullable', 'integer', 'min:1'],
                'order_detail.product_unit.*' => ['nullable']
            ];
        }elseif($this->routeIs('admin.order.route_update_price_product')){
            return [
                'id' => ['nullable', 'exists:App\Models\Product,id'],
                'qty' => ['nullable', 'integer', 'min:1'],
            ];
        }
        return [

        ];
    }
}
