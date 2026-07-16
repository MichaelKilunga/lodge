@extends('template.master')
@section('title', 'Lodge Blog & Content')
@section('content')
    <style>
        .blog-hub-container {
            font-family: 'Inter', sans-serif;
        }

        .text-gradient {
            background: linear-gradient(135deg, #1e293b 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        /* Post Card Styles */
        .post-card-premium {
            background: white;
            border-radius: 20px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 4px 15px -3px rgba(0, 0, 0, 0.03), 0 2px 8px -2px rgba(0, 0, 0, 0.02);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }

        .post-card-premium:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px -10px rgba(59, 130, 246, 0.15);
            border-color: rgba(59, 130, 246, 0.25);
        }

        .post-card-banner {
            height: 120px;
            background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%);
            padding: 1.5rem;
            position: relative;
            display: flex;
            align-items: center;
        }

        .post-card-banner-icon {
            font-size: 2.5rem;
            color: rgba(255, 255, 255, 0.15);
            position: absolute;
            right: 1.25rem;
            bottom: 0.75rem;
        }

        .post-status-badge {
            position: absolute;
            top: 1.25rem;
            left: 1.25rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.35rem 0.85rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .badge-published {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: white !important;
        }

        .badge-draft {
            background: linear-gradient(135deg, #64748b 0%, #475569 100%) !important;
            color: white !important;
        }

        .post-card-body {
            padding: 1.5rem;
            flex-grow: 1;
        }

        .post-card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.4;
            margin-bottom: 0.75rem;
        }

        .post-card-excerpt {
            font-size: 0.875rem;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 1.25rem;
        }

        .post-card-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid #f1f5f9;
            background-color: #fafbfc;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .post-meta-item {
            font-size: 0.78rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
        }

        /* Stats Panel */
        .stat-card-hub {
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 16px;
            background: white;
            transition: all 0.25s;
        }

        .stat-card-hub:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.03);
        }
    </style>

    <div class="blog-hub-container fade-in">
        <div class="row align-items-center mb-4">
            <div class="col-md-6 col-sm-12">
                <h1 class="h3 text-gradient mb-1">Lodge News & Articles</h1>
                <p class="text-muted mb-0 small">Publish announcements, local tourist recommendations, and promotions for lodge visitors</p>
            </div>
            <div class="col-md-6 col-sm-12 text-md-end text-start mt-3 mt-md-0">
                <a href="{{ route('post.create') }}" class="btn btn-primary rounded-pill px-4 shadow-sm fw-semibold">
                    <i class="fas fa-plus-circle me-2"></i> Create Article
                </a>
            </div>
        </div>

        <!-- Blog Hub Analytics -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stat-card-hub border-0">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-4 p-3 me-3">
                            <i class="fas fa-newspaper fa-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium">Total Articles</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $posts->total() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card-hub border-0">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="icon-shape bg-success bg-opacity-10 text-success rounded-4 p-3 me-3">
                            <i class="fas fa-check-double fa-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium">Published Articles</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $posts->where('is_published', 1)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card-hub border-0">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="icon-shape bg-secondary bg-opacity-10 text-secondary rounded-4 p-3 me-3">
                            <i class="fas fa-edit fa-2x"></i>
                        </div>
                        <div>
                            <span class="text-muted small fw-medium">Drafting Bench</span>
                            <h4 class="fw-bold mb-0 text-dark">{{ $posts->where('is_published', 0)->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Grid of Articles -->
        <div class="row">
            @forelse($posts as $index => $post)
                @php
                    $banners = [
                        'linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%)',   // Blue
                        'linear-gradient(135deg, #581c87 0%, #7c3aed 100%)',   // Purple
                        'linear-gradient(135deg, #064e3b 0%, #10b981 100%)',   // Emerald
                        'linear-gradient(135deg, #0f172a 0%, #1e293b 100%)'    // Slate
                    ];
                    $cardBanner = $banners[$index % count($banners)];
                    
                    // Simple word count to calculate reading time
                    $wordCount = str_word_count(strip_tags($post->body ?? ''));
                    $readTime = max(1, ceil($wordCount / 200));
                @endphp
                <div class="col-xl-4 col-md-6 col-sm-12 mb-4">
                    <div class="post-card-premium">
                        <!-- Top Header Banner -->
                        <div class="post-card-banner" style="background: {{ $cardBanner }};">
                            <span class="post-status-badge {{ $post->is_published ? 'badge-published' : 'badge-draft' }}">
                                {{ $post->is_published ? 'Published' : 'Draft' }}
                            </span>
                            <i class="fas fa-pencil-alt post-card-banner-icon"></i>
                        </div>

                        <!-- Card Content Body -->
                        <div class="post-card-body">
                            <h5 class="post-card-title text-truncate-2" title="{{ $post->title }}">
                                {{ Str::limit($post->title, 60) }}
                            </h5>
                            <p class="post-card-excerpt">
                                {{ Str::limit(strip_tags($post->body ?? ''), 120, '...') }}
                            </p>
                        </div>

                        <!-- Card Footer Operations -->
                        <div class="post-card-footer">
                            <div class="d-flex gap-3">
                                <span class="post-meta-item">
                                    <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                    {{ $post->created_at->format('M d, Y') }}
                                </span>
                                <span class="post-meta-item">
                                    <i class="fas fa-clock me-1 text-muted"></i>
                                    {{ $readTime }} min read
                                </span>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('post.edit', $post->id) }}" class="btn btn-sm btn-light border shadow-xs px-2 py-1" data-bs-toggle="tooltip" title="Edit Article">
                                    <i class="fas fa-edit text-primary" style="font-size: 0.8rem;"></i>
                                </a>
                                <form action="{{ route('post.destroy', $post->id) }}" method="POST" id="delete-post-form-{{ $post->id }}" class="d-inline p-0 m-0">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-sm btn-light border shadow-xs px-2 py-1 delete-article" article-id="{{ $post->id }}" article-title="{{ $post->title }}" data-bs-toggle="tooltip" title="Delete Article">
                                        <i class="fas fa-trash-alt text-danger" style="font-size: 0.8rem;"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <div class="card border-0 shadow-sm p-5 text-muted">
                        <i class="fas fa-newspaper mb-3" style="font-size: 3.5rem; opacity: 0.3;"></i>
                        <h5 class="fw-bold text-dark">No Articles Published Yet</h5>
                        <p class="mb-0">Publish helpful local directories, news, and updates for lodge guests.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $posts->links('template.paginationlinks') }}
        </div>
    </div>
@endsection

@section('footer')
    <script>
        $(function () {
            // Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // SweetAlert Confirm delete
            $('.delete-article').click(function() {
                var article_id = $(this).attr('article-id');
                var article_title = $(this).attr('article-title');
                
                Swal.fire({
                    title: 'Delete this article?',
                    text: 'The article "' + article_title + '" will be permanently deleted. This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-post-form-' + article_id).submit();
                    }
                });
            });
        });
    </script>
@endsection
