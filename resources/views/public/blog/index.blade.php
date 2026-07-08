@extends('template.public')

@section('title', 'Journal & Insights')
@section('meta_description', 'Discover the latest travel guides, safari tips, luxury lifestyle stories, and lodge updates from Bella Vista Lodge.')
@section('meta_keywords', 'hotel blog, tanzania travel blog, luxury safari tips, Serengeti guides, lodge news, vacation advice')

@section('head')
<style>
    .blog-hero {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 41, 59, 0.85) 100%),
                    url('{{ !empty($global_settings["hero_image_path"]) ? asset($global_settings["hero_image_path"]) : asset("img/default/default-room.png") }}') center/cover fixed;
        padding: 140px 0 100px;
        color: white;
        text-align: center;
        position: relative;
    }
    .blog-hero::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 50px;
        background: linear-gradient(to top, var(--bg-light), transparent);
    }
    .blog-card {
        border-radius: 20px;
        overflow: hidden;
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05);
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .blog-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
        border-color: var(--accent-color);
    }
    .blog-card-img-wrapper {
        position: relative;
        overflow: hidden;
        height: 240px;
    }
    .blog-card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }
    .blog-card:hover .blog-card-img-wrapper img {
        transform: scale(1.08);
    }
    .blog-badge {
        position: absolute;
        top: 16px;
        left: 16px;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(8px);
        color: var(--accent-color);
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        z-index: 2;
    }
    .blog-meta-bar {
        display: flex;
        align-items: center;
        gap: 15px;
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 12px;
    }
    .blog-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
</style>

{{-- Schema.org Blog / ItemList JSON-LD --}}
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "Blog",
  "name": "{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }} Journal & Insights",
  "description": "Discover the latest travel guides, safari tips, and stories from Bella Vista Lodge.",
  "url": "{{ route('public.blog.index') }}",
  "blogPost": [
    @foreach($posts as $index => $post)
    {
      "@@type": "BlogPosting",
      "headline": "{{ addslashes($post->meta_title ?: $post->title) }}",
      "description": "{{ addslashes($post->meta_description ?: ($post->excerpt ?: Str::limit(strip_tags($post->content), 150))) }}",
      "url": "{{ route('public.blog.show', $post->slug) }}",
      "datePublished": "{{ $post->created_at->toIso8601String() }}",
      "dateModified": "{{ $post->updated_at->toIso8601String() }}",
      "image": "{{ $post->image ? asset($post->image) : asset('img/default/default-room.png') }}"
    }{{ $index < count($posts) - 1 ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endsection

@section('content')
    <!-- Hero Header -->
    <header class="blog-hero">
        <div class="container position-relative z-1">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm text-uppercase letter-spacing">
                <i class="fas fa-feather-alt me-1"></i> Our Journal
            </span>
            <h1 class="display-3 fw-bold mb-3">Stories, Guides &amp; Inspiration</h1>
            <p class="lead mx-auto text-white-50" style="max-width: 680px; line-height: 1.8;">
                Immerse yourself in curated travel advice, local wildlife insights, and behind-the-scenes stories from our luxury sanctuary.
            </p>
        </div>
    </header>

    <!-- Articles Grid -->
    <section class="py-5 my-3">
        <div class="container">
            <div class="row g-4">
                @forelse($posts as $post)
                <div class="col-md-6 col-lg-4">
                    <article class="blog-card">
                        <a href="{{ route('public.blog.show', $post->slug) }}" class="blog-card-img-wrapper text-decoration-none">
                            <span class="blog-badge"><i class="fas fa-bookmark me-1"></i> Article</span>
                            @if($post->image)
                                <img src="{{ asset($post->image) }}" alt="{{ $post->meta_title ?: $post->title }}" loading="lazy">
                            @else
                                <div class="bg-dark text-white d-flex align-items-center justify-content-center h-100">
                                    <i class="fas fa-newspaper fa-3x opacity-25"></i>
                                </div>
                            @endif
                        </a>
                        <div class="card-body p-4 d-flex flex-column flex-grow-1">
                            <div class="blog-meta-bar">
                                <span class="blog-meta-item">
                                    <i class="far fa-calendar-alt text-primary"></i> {{ $post->created_at->format('M d, Y') }}
                                </span>
                                <span class="blog-meta-item">
                                    <i class="far fa-clock text-primary"></i> {{ $post->read_time ?? max(1, ceil(str_word_count(strip_tags($post->content)) / 200)) }} min read
                                </span>
                                @if($post->views_count > 0)
                                <span class="blog-meta-item ms-auto">
                                    <i class="far fa-eye text-muted"></i> {{ number_format($post->views_count) }}
                                </span>
                                @endif
                            </div>

                            <a href="{{ route('public.blog.show', $post->slug) }}" class="text-decoration-none text-dark">
                                <h2 class="card-title h5 fw-bold mb-3" style="line-height: 1.4; font-family: 'Playfair Display', serif;">
                                    {{ $post->title }}
                                </h2>
                            </a>

                            <p class="card-text text-muted mb-4 small" style="line-height: 1.6;">
                                {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 130) }}
                            </p>

                            <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        BV
                                    </div>
                                    <span class="small fw-semibold text-dark">Editorial Team</span>
                                </div>
                                <a href="{{ route('public.blog.show', $post->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                                    Read Article <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                </div>
                @empty
                <div class="col-12 text-center py-5 my-5">
                    <div class="p-5 bg-white rounded-4 shadow-sm mx-auto" style="max-width: 500px;">
                        <i class="fas fa-book-open fa-4x text-muted mb-3 opacity-50"></i>
                        <h3 class="fw-bold">No Stories Published Yet</h3>
                        <p class="text-muted mb-4">Our editorial team is currently crafting breathtaking travel guides and lodge stories. Check back very soon!</p>
                        <a href="{{ route('public.home') }}" class="btn btn-hotel-primary rounded-pill px-4">Return Home</a>
                    </div>
                </div>
                @endforelse
            </div>
            
            <div class="d-flex justify-content-center mt-5">
                {{ $posts->links() }}
            </div>
        </div>
    </section>

    <!-- Newsletter Banner -->
    <section class="py-5 bg-white border-top">
        <div class="container py-4">
            <div class="row align-items-center g-4 p-5 rounded-4 shadow-sm text-white" style="background: linear-gradient(135deg, var(--primary-color) 0%, #1e293b 100%);">
                <div class="col-lg-6">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Stay Inspired</span>
                    <h2 class="display-6 fw-bold mb-2">Subscribe to Our Travel Journal</h2>
                    <p class="text-white-50 mb-0">Receive exclusive seasonal offers, secret safari itineraries, and lodge stories directly in your inbox.</p>
                </div>
                <div class="col-lg-6">
                    <form action="{{ route('public.subscribe') }}" method="POST" class="d-flex gap-2">
                        @csrf
                        <input type="email" name="email" class="form-control form-control-lg rounded-pill border-0 px-4" placeholder="Enter your email address" required>
                        <button type="submit" class="btn btn-warning btn-lg rounded-pill px-4 fw-bold text-dark flex-shrink-0">
                            Subscribe <i class="fas fa-paper-plane ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
