@extends('template.public')

@section('title', $post->title)
@section('meta_description', Str::limit(strip_tags($post->content), 150))

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <a href="{{ route('public.blog.index') }}" class="text-decoration-none mb-4 d-inline-block"><i class="fas fa-arrow-left me-2"></i> Back to Blog</a>
            
            <h1 class="display-5 fw-bold mb-3">{{ $post->title }}</h1>
            <div class="text-muted mb-4"><i class="far fa-calendar-alt me-2"></i> Published on {{ $post->created_at->format('F d, Y') }}</div>

            @if($post->image)
                <img src="{{ asset($post->image) }}" class="img-fluid rounded mb-5 shadow-sm w-100" alt="{{ $post->title }}" style="max-height: 400px; object-fit: cover;">
            @endif

            <div class="blog-content fs-5" style="line-height: 1.8; color: #475569;">
                {!! $post->content !!}
            </div>

            <hr class="my-5">
            
            <div class="text-center">
                <h4 class="fw-bold mb-3">Ready to experience Bella Vista Lodge?</h4>
                <a href="{{ route('public.rooms') }}" class="btn btn-hotel-primary btn-lg px-5 rounded-pill">Book Your Stay</a>
            </div>
        </div>
    </div>
</div>
@endsection
