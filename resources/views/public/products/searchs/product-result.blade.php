@extends('public.layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center bg-light p-4">
        <div class="col-md-8">
            <h2 class="text-center mb-4">Danh sách sản phẩm tìm kiếm</h2>
            <ul class="list-group">
                @forelse ($products as $product)
                <li class="list-group-item p-2 mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-3 col-12 mb-md-0 mb-3">
                            <img src="{{ asset($product->feature_image) }}" alt="{{ $product->name }}"
                                class="img-fluid rounded">
                        </div>
                        <div class="col-md-9 col-12">
                            <h5 class="fw-bold text-cyan text-truncate text-uppercase mb-2"
                                style="max-width: 250px">{{ $product->name }}</h5>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="fw-semibold">Giá khuyến mãi:
                                        <span class="text-cyan fw-bold fs-5">{{ format_price($product->price_promotion) }}
                                            / {{ $product->unit->description() }}</span>
                                    </span>
                                    <br>
                                    <small class="text-decoration-line-through text-danger fs-6">Giá gốc:
                                        {{ format_price($product->price) }} / {{ $product->unit->description() }}</small>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('product.show', $product->slug) }}"
                                        class="btn btn-primary">Mua ngay</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                @empty
                <li class="list-group-item">
                    @include('public.partials.no-record')
                </li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection
