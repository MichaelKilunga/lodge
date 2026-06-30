@extends('template.master')
@section('title', 'Blog Posts')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Manage Blog Posts</h5>
                <a href="{{ route('post.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i> New Post</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($posts as $post)
                            <tr>
                                <td>{{ $post->title }}</td>
                                <td>
                                    @if($post->is_published)
                                        <span class="badge bg-success">Published</span>
                                    @else
                                        <span class="badge bg-secondary">Draft</span>
                                    @endif
                                </td>
                                <td>{{ $post->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('post.edit', $post->id) }}" class="btn btn-sm btn-outline-secondary"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('post.destroy', $post->id) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this post?')"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No posts found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $posts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
