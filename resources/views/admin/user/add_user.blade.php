<!DOCTYPE html>
<html>
<head>
    @include('admin.css')
</head>
  <body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

        <div class="container py-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h4>Add New User</h4>
                </div>
                <div class="card-body">
                    <form action="{{ url('upload_user') }}" method="post">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Phone</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Address</label>
                                <input type="text" name="address" class="form-control" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>User Role</label>
                                <select name="usertype" class="form-control" required>
                                    <option value="">Select Role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">User</option>
                                </select>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <!-- JavaScript files-->
    @include('admin.js')

</body>
</html>
