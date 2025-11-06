<!DOCTYPE html>
<html>
<head>
    @include('admin.css')
</head>
  <body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <!-- Sidebar Navigation end-->
      {{-- <div class="page-content">
        <div class="page-header">
            <div class="container-fluid">
                <h1>Update Category</h1>
                <form action="" method="">
                    <input type="text" name="category" value="{{ $data->category_name }}">

                    <input type="submit" value="Update category" class="btn btn-primary">
                </form>
            </div>
      </div> --}}

      <div class="page-content py-5">
        <div class="container">
            <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white text-center">
                <h3 class="mb-0">Update Category</h3>
            </div>
            <div class="card-body">
            <form action="{{ url('update_category', $data->id) }}" method="post" class="mt-3">
                @csrf
                <div class="mb-3">
                    <label for="category" class="form-label fw-bold">Category Name</label>
                    <input
                        type="text"
                        id="category"
                        name="category"
                        value="{{ $data->category_name }}"
                        class="form-control"
                    >
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa fa-edit me-2"></i> Update Category
                    </button>
                </div>
            </form>
            </div>
            </div>
        </div>
        </div>

    </div>
    <!-- JavaScript files-->
    @include('admin.js')
    
</body>
</html>
