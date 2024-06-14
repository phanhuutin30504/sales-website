@extends('public.layouts.master')

@section('content')
    @include('public.layouts.breadcrums', [
        'breadcrums' => $breadcrums,
    ])
    <section id="activism_section" class="activism-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-9 col-12">
                    <h1 class="title-section mb-3 mt-2" style="font-size: 1.5em">Tin Tức</h1>

                    <div class="blog">
                        @foreach ($posts as $post)
                            <div class="row row-0 gap-3 mb-3">
                                <div class="col-md-12 col-lg-4 col-xl-4 col-sm-12 col-12">
                                    <a href="{{ route('post.show', $post->slug) }}">
                                        <img src="{{ asset($post->feature_image) }}" class="w-100 h-90 object-cover card-img-start img-fluid" alt="Bài Post">
                                    </a>
                                </div>
                                <div class="col-md col-lg mt-3 mt-lg-0 col img-fluid p-3 mt-1">
                                    <div class="card-body bg-light">
                                        <a href="{{ route('post.show', $post->slug) }}">
                                            <h4 class="card-title title-text">{{ $post->title }}</h4>
                                        </a>
                                        <p>Ngày viết: {{ $post->created_at->format('d/m/Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{ $posts->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
