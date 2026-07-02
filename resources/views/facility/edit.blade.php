<form id="form-save-facility" class="row g-3" method="POST" action="{{ route('facility.update', ['facility' => $facility->id]) }}">
    @method('PUT')
    @csrf
    <div class="col-md-12">
        <label for="name" class="form-label">Name</label>
        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
            value="{{ $facility->name }}">
        @error('name')
            <div class="text-danger mt-1">
                {{ $message }}
            </div>
        @enderror
        <div id="error_name" class="text-danger error"></div>
    </div>
    <div class="col-md-12">
        <label for="icon" class="form-label">FontAwesome Icon Class</label>
        <input type="text" class="form-control @error('icon') is-invalid @enderror" id="icon" name="icon"
            value="{{ $facility->icon }}">
        <small class="text-muted">Use standard FontAwesome classes like <code>fas fa-wifi</code> or <code>fas fa-swimming-pool</code></small>
        @error('icon')
            <div class="text-danger mt-1">
                {{ $message }}
            </div>
        @enderror
        <div id="error_icon" class="text-danger error"></div>
    </div>
    <div class="col-md-12">
        <label for="detail" class="form-label">Detail / Description</label>
        <textarea class="form-control" id="detail" name="detail" rows="3">{{ $facility->detail }}</textarea>
        @error('detail')
            <div class="text-danger mt-1">
                {{ $message }}
            </div>
        @enderror
        <div id="error_detail" class="text-danger error"></div>
    </div>
</form>
