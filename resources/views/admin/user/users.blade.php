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

        <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-body">
            <h2 class="text-center mb-4 text-primary">Users</h2>
            <table class="table table-striped table-hover mb-0">
                <thead class="table-primary">
                <tr>
                    <th scope="col"> Name</th>
                    <th scope="col"> Email</th>
                    <th scope="col"> Phone</th>
                    <th scope="col"> Address</th>
                    <th scope="col"> Role</th>
                    {{-- <th scope="col"> Password</th> --}}
                    <th scope="col"> Action</th>

                </tr>
                </thead>
                <tbody>
                @foreach ($data as $data )
                <tr>
                    <td>{{ $data->name }}</td>
                    <td>{{ $data->email }}</td>
                    <td>{{ $data->phone }}</td>
                    <td>{{ $data->address }}</td>
                    <td>{{ $data->usertype }}</td>
                    {{-- <td>{{ $data->password }}</td> --}}

                    <td>
                        <a class="btn btn-success"
                        href="{{ url('edit_user', $data->id) }}">Edit</a>
                        <a class="btn btn-danger" onclick="confirmation(event)"
                        href="{{ url('delete_user', $data->id) }}">Delete</a>

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
