@extends('template.master')
@section('title', 'Create Post')

@section('content')
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold">Create New Blog Post</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('post.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Thumbnail Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Content</label>
                        <textarea name="content" id="content-editor" class="form-control" rows="10"></textarea>
                    </div>

                    <div class="card bg-light border-0 mb-4 p-3 rounded-3">
                        <h6 class="fw-bold mb-3"><i class="fas fa-search me-2 text-primary"></i>SEO & Metadata Settings (Optional)</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control" placeholder="Custom SEO title (defaults to post title)">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control" placeholder="e.g. luxury, safari, hotel, vacation">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold">Meta Description</label>
                                <textarea name="meta_description" class="form-control" rows="2" placeholder="Custom SEO description (defaults to first 150 characters of content)"></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label small fw-semibold">Article Excerpt / Summary</label>
                                <textarea name="excerpt" class="form-control" rows="2" placeholder="Short summary displayed on blog cards (defaults to 160 chars)"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_published" name="is_published" checked>
                        <label class="form-check-label" for="is_published">Publish immediately</label>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('post.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Post</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer')
<script src="https://cdn.tiny.cloud/1/lsxrlyfwiy2is684dx1xc79rmq4x90wgllj5vkjnd69tw4xo/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    tinymce.init({
        selector: '#content-editor',
        plugins: 'image link media table lists',
        toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | link image media table',
        images_upload_url: '{{ route('post.upload_image') }}',
        automatic_uploads: true,
        file_picker_types: 'image',
        images_upload_credentials: true,
        setup: function (editor) {
            editor.on('change', function () {
                editor.save();
            });
        }
    });
</script>
@endsection
