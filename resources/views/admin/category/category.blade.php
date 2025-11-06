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
      <div class="page-content">
        <div class="page-header">
          <div class="container-fluid">
            <h1>Add Category</h1>
            <form action="{{ url('add_category') }}" class="mt-3" method="post">
            @csrf
            <div class="input-group mb-3">
                <input
                type="text"
                name="category"
                class="form-control"
                placeholder="Enter category name"
                required
                >
                <button class="btn btn-success" type="submit">Add Category</button>
            </div>
            </form>

        </div>
        </div>

        {{-- <div>
            <table>
                <tr>
                    <th>Category Name</th>
                </tr>
                <tr>
                    <td>Dev</td>
                </tr>
            </table>
        </div> --}}
        <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
            <h2 class="text-center mb-4 text-primary">Categories</h2>
            <table class="table table-striped table-hover mb-0">
                <thead class="table-primary">
                <tr>
                    <th scope="col">Category Name</th>
                    <th scope="col"> Action</th>

                </tr>
                </thead>
                <tbody>
                @foreach ($data as $data )
                <tr>
                    <td>{{ $data->category_name }}</td>
                    <td>
                        <a class="btn btn-success"
                        href="{{ url('edit_category', $data->id) }}">Edit</a>
                        <a class="btn btn-danger" onclick="confirmation(event)"
                        href="{{ url('delete_category', $data->id) }}">Delete</a>

                    </td>

                </tr>

                @endforeach

                </tbody>
            </table>
            </div>
        </div>
        </div>

      </div>
    <!-- JavaScript files-->

    @include('admin.js')

</body>
</html>
