@if (auth()->check() && auth()->user()->isAgent())
    <table class="table table-vcenter card-table sticky-table tbl-cart">
        <thead>
            <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th class="text-center">Số lượng</th>
                <th class="text-center">Đơn giá</th>
                <th class="w-1"></th>
            </tr>
        </thead>
        @if ($pr->count() == 0)
            <tbody>
                <tr>
                    <td class="align-middle" colspan="5">Hiện không có sản phẩm</td>
                </tr>
            </tbody>
        @else
            @foreach ($pr as $key => $value)
                <tbody>
                    <tr>
                        <td class="align-middle" rowspan="2">{{ $stt++ }}</td>
                        <td class="align-middle" rowspan="2">
                            <a href="{{ route('product.show', ['slug' => $value->slug]) }}" target="_blank"
                                title="" class="text-decoration-none">
                                <span>{{ $value->name }}</span>
                            </a>
                        </td>

                        <td class="text-center">
                            {{ isset($qty[$value->id]) ? $qty[$value->id] : 0 }}
                        </td>


                        <td class="text-center unit-price align-middle">
                            {{ format_price($value->price_promotion) }}<sup>đ</sup>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            @endforeach
        @endif
    </table>
    <div class="mt-4">
        @if (isset($discountByProduct))
            <h4>Giá chiết khấu: <span class="fw-bold">{{ format_price($discount) }}</span></h4>
        @else
            <h4>Giá chiết khấu: <span class="fw-bold">0</span></h4>
        @endif
    </div>



@endif


@if (auth()->check() && auth()->user()->isSeller())
    <table class="table table-vcenter card-table sticky-table tbl-cart">
        <thead>
            <tr>
                <th>STT</th>
                <th>Sản phẩm</th>
                <th class="text-center">Số lượng</th>
                <th class="text-center">Đơn giá</th>
                <th class="text-center">Tặng</th>
                <th class="text-center">Tổng tiền</th>
                <th class="w-1"></th>
            </tr>
        </thead>
        @if ($pr->count() == 0)
            <tbody>
                <tr>
                    <td class="align-middle">Hien khong co san pham</td>
                </tr>
            </tbody>
        @else
            @foreach ($pr as $key => $value)
                <tbody>
                    <tr>
                        <td class="align-middle" rowspan="2">{{ $stt++ }}</td>
                        <td class="align-middle" rowspan="2">
                            <a href="{{ route('product.show', ['slug' => $value->slug]) }}" target="_blank"
                                title="" class="text-decoration-none">
                                <span>{{ $value->name }}</span>
                            </a>
                        </td>
                        <td class="text-center">
                            {{ isset($qty[$value->id]) ? $qty[$value->id] : 0 }}
                        </td>

                        <td class=" text-center unit-price align-middle">
                            {{ format_price($value->price_promotion) }}<sup>đ</sup>
                        </td>
                        <td class="text-center">
                            @if (isset($qty_donate[$value->id]))
                                {{ $qty_donate[$value->id] }}/{{ $value->unit->description() }}
                            @else
                                0
                            @endif
                        </td>

                        <td class=" text-center total-price align-middle">
                            {{ format_price($price[$key]) }}<sup>đ</sup>
                        </td>
                    </tr>
                </tbody>
            @endforeach
        @endif
    </table>



@endif
