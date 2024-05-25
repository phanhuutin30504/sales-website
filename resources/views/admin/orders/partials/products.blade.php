@if (!isset($order))
<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalAddProduct">
    <i class="ti ti-plus"></i> {{ __('Thêm sản phẩm') }}
</button>
@endif
<table id="tableProduct" class="table table-transparent table-responsive table-striped">
    <thead>
        <th style="width: 5%">{{ __('Hình ảnh') }}</th>
        <th style="width: 20%">{{ __('Sản phẩm') }}</th>
        <th style="width: 12%">{{ __('Số lượng') }}</th>
        @if (isset($order) && $order->user->roles == App\Enums\User\UserRoles::Seller)
        <th style="width: 12%">{{ __('Tặng') }}</th>
        @endif
        <th style="width: 12%">{{ __('Đơn giá') }}</th>
        <th style="width: 12%">{{ __('ĐV tính') }}</th>
        <th style="width: 12%">{{ __('Tổng tiền') }}</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($order->details ?? [] as $orderDetail)
        <tr>
            <td><img src="{{ asset($orderDetail->product->feature_image) }}"></td>
            <td>{{ $orderDetail->product->name }}</td>
            {{-- <td>{{ $orderDetail->quantity }}</td> --}}
            <td>{{ $orderDetail->qty }}</td>
            @if (isset($order) && $order->user->roles == App\Enums\User\UserRoles::Seller)
            <td>{{ $orderDetail->qty_donate }}</td>
            @endif
            <td>{{ format_price($orderDetail->unit_price) }}</td>
            @if (isset($order) && $orderDetail->unit == App\Enums\Product\ProductUnit::Pail)
            <td>Thùng</td>
            @else
            <td>Bình</td>
            @endif
            @if (isset($order) && $order->user->roles == App\Enums\User\UserRoles::Agent)
            <td>{{ format_price($orderDetail->unit_price * $orderDetail->qty) }}</td>
            @else
            <td>{{ format_price($orderDetail->unit_price * $orderDetail->qty) }}</td>
            @endif

        </tr>
        @endforeach
    </tbody>
</table>

@include('admin.orders.partials.total', [
'total' => $order->total ?? 0,
'sub_total' => $order->sub_total ?? 0,
'discount' => $order->discount ?? 0,
'bonus' => $order->bonus ?? 0,
'user' => $order->user->roles ?? null,
'reward' => $reward ?? 0
])
