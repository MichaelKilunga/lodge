<form id="form-save-room" class="row g-3" method="POST" action="{{ route('room.update', ['room' => $room->id]) }}" enctype="multipart/form-data">
    @method('PUT')
    @csrf
    <div class="col-md-12">
        <label for="type_id" class="form-label">Type</label>
        <select id="type_id" name="type_id" class="form-control select2">
            @foreach ($types as $type)
                <option value="{{ $type->id }}" @if ($room->type->id == $type->id) selected @endif>{{ $type->name }}
                </option>
            @endforeach
        </select>
        <div id="error_type_id" class="text-danger error"></div>
    </div>
    <div class="col-md-12">
        <label for="room_status_id" class="form-label">Room Status</label>
        <select id="room_status_id" name="room_status_id" class="form-control select2">
            @foreach ($roomstatuses as $roomstatus)
                <option value="{{ $roomstatus->id }}" @if ($room->roomstatus->id == $roomstatus->id) selected @endif>
                    {{ $roomstatus->name }} ({{ $roomstatus->code }})</option>
            @endforeach
        </select>
        <div id="error_room_status_id" class="text-danger error"></div>
    </div>
    <div class="col-md-12">
        <label for="number" class="form-label">Room Number</label>
        <input room="text" class="form-control @error('number') is-invalid @enderror" id="number" name="number"
            value="{{ $room->number }}" placeholder="ex: 1A">
        <div id="error_number" class="text-danger error"></div>
    </div>
    <div class="col-md-12">
        <label for="capacity" class="form-label">Capacity</label>
        <input room="text" class="form-control @error('capacity') is-invalid @enderror" id="capacity"
            name="capacity" value="{{ $room->capacity }}" placeholder="ex: 4">
        <div id="error_capacity" class="text-danger error"></div>
    </div>
    <div class="col-md-12">
        <label for="price" class="form-label">Price</label>
        <input room="text" class="form-control @error('price') is-invalid @enderror" id="price" name="price"
            value="{{ $room->price }}" placeholder="ex: 500000">
        <div id="error_price" class="text-danger error"></div>
    </div>
    <div class="col-md-12">
        <label for="view" class="form-label">View</label>
        <textarea class="form-control" id="view" name="view" rows="3" placeholder="ex: window see beach">{{ $room->view }}</textarea>
        <div id="error_view" class="text-danger error"></div>
    </div>
    <div class="col-md-12">
        <label for="images" class="form-label">Upload Room Images (optional)</label>
        <input type="file" class="form-control mb-2" id="images" name="images[]" multiple accept="image/*">
        @if ($room->image->count() > 0)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="replace_images" name="replace_images">
                <label class="form-check-label text-danger" for="replace_images">
                    Replace all existing images with newly uploaded images
                </label>
            </div>
        @endif
        <div id="error_images" class="text-danger error"></div>
    </div>
    @if ($room->image->count() > 0)
        <div class="col-md-12">
            <label class="form-label d-block">Current Images (click X to delete instantly)</label>
            <div class="d-flex flex-wrap gap-2">
                @foreach ($room->image as $image)
                    <div class="position-relative border rounded p-1 shadow-sm room-image-item" style="width: 80px; height: 80px;">
                        <img src="{{ $image->getRoomImage() }}" class="w-100 h-100 rounded" style="object-fit: cover;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 delete-room-image-btn" style="padding: 0 5px; font-size: 11px; line-height: 1.4; border-radius: 50%; transform: translate(30%, -30%);" data-url="{{ route('image.destroy', ['image' => $image->id]) }}" title="Remove Image">
                            &times;
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</form>
