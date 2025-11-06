<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Update House</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Update House</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ url('update_house', $house->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-3">
                                <!-- House Title -->
                                <div class="col-md-6">
                                    <label for="title" class="form-label fw-bold">House Title</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="title"
                                        name="title"
                                        value="{{ old('title', $house->title) }}"
                                        placeholder="Enter house title"
                                        required
                                    >
                                </div>

                                <!-- Price -->
                                <div class="col-md-6">
                                    <label for="price" class="form-label fw-bold">Price</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="price"
                                        name="price"
                                        value="{{ old('price', $house->price) }}"
                                        placeholder="Enter price"
                                        min="0"
                                        required
                                    >
                                </div>

                                <!-- Description -->
                                <div class="col-12">
                                    <label for="description" class="form-label fw-bold">House Description</label>
                                    <textarea
                                        class="form-control"
                                        id="description"
                                        name="description"
                                        rows="4"
                                        placeholder="Enter detailed description"
                                        required
                                    >{{ old('description', $house->description) }}</textarea>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6">
                                    <label for="category" class="form-label fw-bold">Category</label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach ($category as $cat)
                                            <option value="{{ $cat->category_name }}"
                                                {{ old('category', $house->category) == $cat->category_name ? 'selected' : '' }}>
                                                {{ $cat->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Location -->
                                <div class="col-md-6">
                                    <label for="location" class="form-label fw-bold">Location</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="location"
                                        name="location"
                                        value="{{ old('location', $house->location) }}"
                                        placeholder="Enter house location"
                                        required
                                    >
                                </div>

                                <!-- Quantity -->
                                <div class="col-md-6">
                                    <label for="quantity" class="form-label fw-bold">Quantity</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="quantity"
                                        name="quantity"
                                        value="{{ old('quantity', $house->quantity) }}"
                                        placeholder="Enter house quantity"
                                        min="1"
                                        required
                                    >
                                </div>

                                <!-- Landlord Selection -->
                                <div class="col-md-6">
                                    <label for="landlord_id" class="form-label fw-bold">Landlord</label>
                                    <select name="landlord_id" id="landlord_id" class="form-control" required>
                                        <option value="">Select Landlord</option>
                                        @foreach($landlords as $landlord)
                                            <option value="{{ $landlord->id }}"
                                                {{ old('landlord_id', $house->landlord_id) == $landlord->id ? 'selected' : '' }}>
                                                {{ $landlord->user->name ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status</label>
                                    <select name="status" id="status" class="form-control" required>
                                        <option value="available" {{ old('status', $house->status) == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="occupied" {{ old('status', $house->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                        <option value="under_maintenance" {{ old('status', $house->status) == 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                    </select>
                                </div>

                                <!-- Image Upload -->
                                <div class="col-12">
                                    <label for="image" class="form-label fw-bold">Upload Image</label>

                                    <!-- Show current image -->
                                    @if($house->image)
                                        <div class="mb-3">
                                            <p class="text-muted mb-2">Current Image:</p>
                                            <img src="/houses/{{ $house->image }}" alt="Current Image"
                                                 style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid #ddd;">
                                            <p class="text-muted small mt-1">{{ $house->image }}</p>
                                        </div>
                                    @endif

                                    <input
                                        type="file"
                                        class="form-control"
                                        id="image"
                                        name="image"
                                        accept="image/*"
                                    >
                                    <input type="hidden" name="old_image" value="{{ $house->image }}">
                                    <small class="text-muted">Leave empty to keep current image, or upload a new one to replace it.</small>
                                </div>

                                <!-- Submit and Cancel Buttons -->
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fa fa-edit me-2"></i> Update House
                                    </button>
                                    <a href="{{ url('view_house') }}" class="btn btn-secondary px-5 ms-2">
                                        <i class="fa fa-times me-2"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.js')
</body>
</html>
