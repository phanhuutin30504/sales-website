@extends('public.layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center py-5">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-dark text-light">
                    <h2 class="text-uppercase fw-bold mb-0 d-flex justify-content-between">
                        <span class="badge bg-primary text-light align-self-end">Thông Báo</span>
                    </h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12  col-lg-12 col-sm-12">
                            <h3 class="card-text fw-bold mb-3">
                                <span class="fw-bold">Tiêu Đề :</span> {{ $value->title }}
                            </h3>
                            <p class="card-text text-center  mb-3 mt-2 ">
                                {!!$value->desc!!}
                            </p>
                        </div>
                        <div class="col-md-12">
                            <div class="d-flex align-items-center">
                                <div class="fw-bold pe-2">Ngày gửi:</div>
                                <div>{{ \Carbon\Carbon::parse($value->created_at)->format('d/m/Y - H:i:s') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
