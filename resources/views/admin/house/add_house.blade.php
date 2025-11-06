<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.css')
    <title>Add House</title>
</head>
<body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="page-content py-5">
            <div class="container">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">Add House</h3>
                    </div>

                    <div class="card-body">
                        <form action="{{ url('upload_house') }}" method="POST" enctype="multipart/form-data">
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
                                        placeholder="Enter house title"
                                        value="{{ old('title') }}"
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
                                        placeholder="Enter price"
                                        value="{{ old('price') }}"
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
                                    >{{ old('description') }}</textarea>
                                </div>

                                <!-- Category -->
                                <div class="col-md-6">
                                    <label for="category" class="form-label fw-bold">Category</label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">Select Category</option>
                                        @foreach ($category as $cat)
                                            <option value="{{ $cat->category_name }}" {{ old('category') == $cat->category_name ? 'selected' : '' }}>
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
                                        placeholder="Enter house location"
                                        value="{{ old('location') }}"
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
                                        placeholder="Enter quantity"
                                        value="{{ old('quantity') }}"
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
                                            <option value="{{ $landlord->id }}" {{ old('landlord_id') == $landlord->id ? 'selected' : '' }}>
                                                {{ $landlord->user->name ?? 'Unnamed Landlord' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Status Selection -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-bold">Status</label>
                                    <select name="status" id="status" class="form-control">
                                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                        <option value="under_maintenance" {{ old('status') == 'under_maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                    </select>
                                </div>

                                <!-- Image -->
                                <div class="col-12">
                                    <label for="image" class="form-label fw-bold">Upload Image</label>
                                    <input
                                        type="file"
                                        class="form-control"
                                        id="image"
                                        name="image"
                                        accept="image/*"
                                    >
                                    <small class="text-muted">Upload a house image (JPEG, PNG, etc.)</small>
                                </div>

                                <!-- Submit -->
                                <div class="col-12 text-center mt-4">
                                    <button type="submit" class="btn btn-primary px-5">
                                        <i class="fa fa-plus-circle me-2"></i> Add House
                                    </button>
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
