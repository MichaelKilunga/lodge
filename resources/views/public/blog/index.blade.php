@extends('template.public')

@section('title', 'Our Blog')

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold">Journal & Insights</h1>
        <p class="lead text-muted">Discover the latest news, travel guides, and stories from Bella Vista Lodge.</p>
    </div>

    <div class="row g-4">
        @forelse($posts as $post)
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm room-card">
                @if($post->image)
                    <img src="{{ asset($post->image) }}" class="card-img-top" alt="{{ $post->title }}" style="height: 200px; object-fit: cover;">
                @else
                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                        <i class="fas fa-newspaper fa-3x opacity-50"></i>
                    </div>
                @endif
                <div class="card-body d-flex flex-column">
                    <div class="text-muted small mb-2"><i class="far fa-calendar-alt me-1"></i> {{ $post->created_at->format('M d, Y') }}</div>
                    <h5 class="card-title fw-bold mb-3">{{ $post->title }}</h5>
                    <p class="card-text text-muted mb-4">{{ Str::limit(strip_tags($post->content), 120) }}</p>
                    <div class="mt-auto">
                        <a href="{{ route('public.blog.show', $post->slug) }}" class="btn btn-outline-primary rounded-pill px-4">Read More</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fas fa-book-open fa-3x text-muted mb-3 opacity-50"></i>
            <h4>No articles yet</h4>
            <p class="text-muted">Check back soon for updates!</p>
        </div>
        @endforelse
    </div>
    
    <div class="d-flex justify-content-center mt-5">
        {{ $posts->links() }}
    </div>
</div>
@endsection
