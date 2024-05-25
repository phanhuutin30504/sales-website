<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasCart" aria-labelledby="offcanvasEndLabel">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title text-uppercase" id="offcanvasEndLabel">Giỏ hàng</h2>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="search-dropdown">
            <div class="overflow-y-scroll d-flex flex-column" style="height: 450px">
                {{-- Item --}}
                @if (empty($cart))
                    <span class="title-text fw-bold">Hiện chưa có sản phẩm trong giỏ hàng</span>
                @else

                        @foreach ($pr as $item)
                        <div class="d-flex mb-3 p-3 justify-content-between">
                            <div class="col-3"><img src="{{ asset($item->feature_image) }}" alt=""></div>
                            <div class="col-9 px-3">
                                <span class="title-text fw-bold">{{ $item->name}}</span>
                                <div class="d-flex flex-row my-2 justify-content-between">
                                    <span class="fw-bold text-dark">{{ $qty[$item->id] }} x {{ format_price($item->price_promotion) }}<sup>đ</sup></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <x-button.modal-delete class="btn-icon" data-route="{{ route('cart.delete', $item->id) }}" data-id="{{ $item->id }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                    </x-button.modal-delete>
                                    <span class="text-danger fw-bold ms-auto">{{format_price( $qty[$item->id] * $item->price_promotion) }}<sup>đ</sup></span>
                                </div>
                            </div>
                        </div>
                        @endforeach

                @endif
                {{-- End of Item --}}
            </div>
            <div class="d-flex flex-row justify-content-between mb-3 align-items-center my-3 border-top">
                <span class="text-uppercase fw-bold py-3">Tổng giỏ hàng:</span>
                <span class="fw-bold">{{ format_price($totalPrice) }}<sup>đ</sup></span>
            </div>
            <div class="d-flex flex-row justify-content-end px-3 gap-3 my-3">
                <a href="{{ route('cart.index') }}" class="btn btn-outline-cyan">Xem giỏ hàng</a>
                <a href="{{ route('checkout.total') }}" class="btn btn-outline-danger">Thanh toán</a>
            </div>
        </div>
    </div>
</div>
