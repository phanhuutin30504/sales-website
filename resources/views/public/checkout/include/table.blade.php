
@if (auth()->check() && auth()->user()->isAgent())
<table class="table table-vcenter card-table sticky-table tbl-cart">
    <thead>
        <tr>
            <th>STT</th>
            <th>Sản phẩm</th>
            <th class="text-center">Số lượng</th>
            <th class="text-center">Đơn giá</th>
            <th class="text-center">Tiền Giảm Chiếc Khấu</th>

            <th class="text-center">Tổng tiền Sản Phẩm</th>
            <th class="w-1"></th>
        </tr>
    </thead>
    <tbody>
        @if ($data)
            <tr>
                <td class="align-middle">{{ $stt }}</td>
                <td class="align-middle">
                    <a href="{{ route('product.show', ['slug' => $data->slug]) }}" target="_blank" title=""
                        class="text-decoration-none">
                        <span>{{ $data->name }}</span>
                    </a>
                </td>
                <td class="text-center">{{ $quantity }}</td>
                <td class="unit-price align-middle">
                    {{ format_price($data->price_promotion) }}<sup>đ</sup>
                </td>
                @if (isset($discount) && $discount == 0)
                    <td class="text-center">0</td>
                @else
                    <td class="text-center">{{ format_price($discount) }}</td>
                @endif

                    @if ($Total == 0)
                    <td class="align-middle">
                        {{ format_price($data->price_selling) }}<sup>đ</sup>
                    </td>
                    @else
                    <td class="align-middle">
                        {{ format_price($Total) }}<sup>đ</sup>
                    </td>
                    @endif

            </tr>
        @else
            <tr>
                <td class="align-middle" colspan="13">Hiện không có sản phẩm</td>
            </tr>
        @endif




    </tbody>
</table>
@endif

    {{-- day la seller --}}
@if (auth()->check() && auth()->user()->isSeller())
<table class="table table-vcenter card-table sticky-table tbl-cart">
    <thead>
        <tr>
            <th>STT</th>
            <th>Sản phẩm</th>
            <th class="text-center">Số lượng</th>
            <th class="text-center">Đơn giá</th>
            <th class="text-center">Tặng</th>

            <th class="text-center">Tổng tiền Sản Phẩm</th>
            <th class="w-1"></th>
        </tr>
    </thead>
    <tbody>
        @if ($data)
            <tr>
                <td class="align-middle">{{ $stt }}</td>
                <td class="align-middle">
                    <a href="{{ route('product.show', ['slug' => $data->slug]) }}" target="_blank" title=""
                        class="text-decoration-none">
                        <span>{{ $data->name }}</span>
                    </a>
                </td>
                <td class="text-center">{{$qtye}}</td>
                <td class="unit-price align-middle">
                    {{ format_price($data->price_promotion) }}<sup>đ</sup>
                </td>
                @if (isset($discout) && $discout == 0)
                    <td class="text-center">0</td>
                @else
                <td class="text-center">{{ $discout }}/{{ $data->unit->name === 'Pail' ? 'Thùng' : 'Bình' }}</td>

                @endif

                    @if ($total == 0)
                    <td class=" text-center align-middle">
                        {{ format_price($data->price_selling) }}<sup>đ</sup>
                    </td>
                    @else
                    <td class="text-center  align-middle">
                        {{ format_price($total) }}<sup>đ</sup>
                    </td>
                    @endif

            </tr>
        @else
            <tr>
                <td class="align-middle" colspan="13">Hiện không có sản phẩm</td>
            </tr>
        @endif
    </tbody>
</table>
@endif



