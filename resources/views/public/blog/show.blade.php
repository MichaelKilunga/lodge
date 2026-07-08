@extends('template.public')

@section('title', $post->meta_title ?: $post->title)
@section('meta_description', $post->meta_description ?: ($post->excerpt ?: Str::limit(strip_tags($post->content), 150)))
@section('meta_keywords', $post->meta_keywords ?: 'hotel blog, luxury lodge, tanzania travel, ' . strtolower($post->title))
@section('og_type', 'article')
@section('og_image', $post->image ? asset($post->image) : asset('img/default/default-room.png'))

@section('head')
<style>
    .article-header {
        padding: 60px 0 30px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 40px;
    }
    .article-meta-bar {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        font-size: 0.95rem;
        color: #64748b;
        margin-top: 20px;
    }
    .article-meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .article-featured-img {
        border-radius: 24px;
        overflow: hidden;
        max-height: 550px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    .article-featured-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .article-content {
        font-size: 1.15rem;
        line-height: 1.9;
        color: #334155;
    }
    .article-content h1, .article-content h2, .article-content h3, .article-content h4 {
        font-family: 'Playfair Display', serif;
        color: #0f172a;
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 700;
    }
    .article-content p {
        margin-bottom: 1.5rem;
    }
    .article-content blockquote {
        border-left: 4px solid var(--accent-color);
        padding: 1.5rem 2rem;
        background: #f8fafc;
        border-radius: 0 16px 16px 0;
        font-style: italic;
        color: #1e293b;
        margin: 2rem 0;
    }
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 16px;
        margin: 2rem 0;
        box-shadow: 0 10px 25px rgba(0,0,0,0.06);
    }
    .social-share-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        margin-top: 3rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
    }
    .share-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: transform 0.3s, filter 0.3s;
    }
    .share-btn:hover {
        transform: scale(1.15);
        color: white;
        filter: brightness(1.1);
    }
    .author-box {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2rem;
        margin-top: 3rem;
        box-shadow: 0 10px 25px rgba(0,0,0,0.03);
    }
    .related-card {
        border-radius: 16px;
        overflow: hidden;
        background: white;
        border: 1px solid #e2e8f0;
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
    }
    .related-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08);
        border-color: var(--accent-color);
    }
</style>

{{-- Schema.org Article / BlogPosting JSON-LD --}}
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BlogPosting",
  "headline": "{{ addslashes($post->meta_title ?: $post->title) }}",
  "description": "{{ addslashes($post->meta_description ?: ($post->excerpt ?: Str::limit(strip_tags($post->content), 150))) }}",
  "image": "{{ $post->image ? asset($post->image) : asset('img/default/default-room.png') }}",
  "url": "{{ route('public.blog.show', $post->slug) }}",
  "datePublished": "{{ $post->created_at->toIso8601String() }}",
  "dateModified": "{{ $post->updated_at->toIso8601String() }}",
  "author": {
    "@@type": "Organization",
    "name": "{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }} Editorial Team",
    "url": "{{ url('/') }}"
  },
  "publisher": {
    "@@type": "Organization",
    "name": "{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }}",
    "logo": {
      "@@type": "ImageObject",
      "url": "{{ !empty($global_settings['logo_path']) ? asset($global_settings['logo_path']) : asset('img/logo/sip.png') }}"
    }
  },
  "mainEntityOfPage": {
    "@@type": "WebPage",
    "@@id": "{{ route('public.blog.show', $post->slug) }}"
  }
}
</script>

{{-- Schema.org BreadcrumbList JSON-LD --}}
<script type="application/ld+json">
{
  "@@context": "https://schema.org",
  "@@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@@type": "ListItem",
      "position": 1,
      "name": "Home",
      "item": "{{ route('public.home') }}"
    },
    {
      "@@type": "ListItem",
      "position": 2,
      "name": "Blog",
      "item": "{{ route('public.blog.index') }}"
    },
    {
      "@@type": "ListItem",
      "position": 3,
      "name": "{{ addslashes($post->title) }}",
      "item": "{{ route('public.blog.show', $post->slug) }}"
    }
  ]
}
</script>
@endsection

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('public.home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('public.blog.index') }}" class="text-decoration-none">Blog</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($post->title, 40) }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-9 mx-auto">
            <!-- Article Header -->
            <header class="article-header">
                <span class="badge bg-dark text-warning px-3 py-1 rounded-pill fw-bold mb-3 text-uppercase letter-spacing">
                    <i class="fas fa-feather-alt me-1"></i> Featured Story
                </span>
                <h1 class="display-4 fw-bold mb-3" style="font-family: 'Playfair Display', serif; line-height: 1.2;">
                    {{ $post->title }}
                </h1>
                
                <div class="article-meta-bar">
                    <span class="article-meta-item">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                            BV
                        </div>
                        <strong class="text-dark">Editorial Team</strong>
                    </span>
                    <span class="article-meta-item">
                        <i class="far fa-calendar-alt text-primary"></i> {{ $post->created_at->format('F d, Y') }}
                    </span>
                    <span class="article-meta-item">
                        <i class="far fa-clock text-primary"></i> {{ $post->read_time ?? max(1, ceil(str_word_count(strip_tags($post->content)) / 200)) }} min read
                    </span>
                    @if($post->views_count > 0)
                    <span class="article-meta-item">
                        <i class="far fa-eye text-primary"></i> {{ number_format($post->views_count) }} views
                    </span>
                    @endif
                </div>
            </header>

            <!-- Featured Image -->
            @if($post->image)
            <div class="article-featured-img">
                <img src="{{ asset($post->image) }}" alt="{{ $post->meta_title ?: $post->title }}">
            </div>
            @endif

            <!-- Article Body -->
            <article class="article-content">
                {!! $post->content !!}
            </article>

            <!-- Social Share Bar -->
            <div class="social-share-box">
                <div>
                    <h6 class="fw-bold mb-1"><i class="fas fa-share-alt text-primary me-2"></i>Share This Story</h6>
                    <span class="text-muted small">Enjoyed the read? Share it with fellow travelers &amp; friends.</span>
                </div>
                <div class="d-flex gap-2">
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . route('public.blog.show', $post->slug)) }}" target="_blank" class="share-btn" style="background-color: #25d366;" title="Share on WhatsApp">
                        <i class="fab fa-whatsapp fs-5"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('public.blog.show', $post->slug)) }}" target="_blank" class="share-btn" style="background-color: #1877f2;" title="Share on Facebook">
                        <i class="fab fa-facebook-f fs-5"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(route('public.blog.show', $post->slug)) }}" target="_blank" class="share-btn" style="background-color: #000000;" title="Share on X (Twitter)">
                        <i class="fab fa-x-twitter fs-5"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('public.blog.show', $post->slug)) }}&title={{ urlencode($post->title) }}" target="_blank" class="share-btn" style="background-color: #0a66c2;" title="Share on LinkedIn">
                        <i class="fab fa-linkedin-in fs-5"></i>
                    </a>
                    <button type="button" class="share-btn border-0 copy-link-btn" style="background-color: #64748b;" data-url="{{ route('public.blog.show', $post->slug) }}" title="Copy Link">
                        <i class="fas fa-link fs-5"></i>
                    </button>
                </div>
            </div>

            <!-- Author Box -->
            <div class="author-box d-flex align-items-center gap-4 flex-wrap">
                <div class="rounded-circle bg-dark text-warning d-flex align-items-center justify-content-center fw-bold flex-shrink-0 shadow" style="width: 80px; height: 80px; font-size: 1.8rem;">
                    BV
                </div>
                <div>
                    <h5 class="fw-bold mb-1">{{ $global_settings['hotel_name'] ?? 'Bella Vista Lodge' }} Editorial Team</h5>
                    <p class="text-muted mb-2 small">Our dedicated team of travel writers, wildlife experts, and hospitality curators share insider guides, Serengeti itineraries, and stories from the lodge.</p>
                    <a href="{{ route('public.rooms') }}" class="btn btn-sm btn-hotel-primary rounded-pill px-4">Explore Our Luxury Rooms</a>
                </div>
            </div>

            <!-- Related Articles -->
            @if(isset($relatedPosts) && count($relatedPosts) > 0)
            <div class="mt-5 pt-4 border-top">
                <h3 class="fw-bold mb-4" style="font-family: 'Playfair Display', serif;">Read Next &amp; More Stories</h3>
                <div class="row g-4">
                    @foreach($relatedPosts as $related)
                    <div class="col-md-4">
                        <div class="related-card d-flex flex-column">
                            @if($related->image)
                                <img src="{{ asset($related->image) }}" alt="{{ $related->title }}" style="height: 180px; object-fit: cover;">
                            @else
                                <div class="bg-dark text-white d-flex align-items-center justify-content-center" style="height: 180px;">
                                    <i class="fas fa-newspaper fa-2x opacity-25"></i>
                                </div>
                            @endif
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <span class="text-primary small mb-1"><i class="far fa-calendar-alt me-1"></i> {{ $related->created_at->format('M d, Y') }}</span>
                                <h4 class="h6 fw-bold mb-2">
                                    <a href="{{ route('public.blog.show', $related->slug) }}" class="text-decoration-none text-dark">{{ $related->title }}</a>
                                </h4>
                                <a href="{{ route('public.blog.show', $related->slug) }}" class="small fw-bold text-primary mt-auto text-decoration-none">Read Story &rarr;</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('footer')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyBtn = document.querySelector('.copy-link-btn');
    if (copyBtn) {
        copyBtn.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            navigator.clipboard.writeText(url).then(() => {
                const originalHtml = this.innerHTML;
                this.innerHTML = '<i class="fas fa-check fs-5 text-warning"></i>';
                this.style.backgroundColor = '#16a34a';
                setTimeout(() => {
                    this.innerHTML = originalHtml;
                    this.style.backgroundColor = '#64748b';
                }, 2000);
            }).catch(e => {
                alert('Copied link: ' + url);
            });
        });
    }
});
</script>
@endsection
