
<!DOCTYPE html>
<html>
<head>
    @include('admin.css')
    <style>
        .description-short {
            display: inline;
        }
        .description-full {
            display: none;
        }
        .more-link {
            color: #007bff;
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.875rem;
            margin-left: 5px;
        }
        .more-link:hover {
            color: #0056b3;
        }
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table {
            min-width: 1200px;
            white-space: nowrap;
        }
        .table td, .table th {
            white-space: normal;
            min-width: 100px;
        }
        .table td:nth-child(2) {
            min-width: 200px;
            max-width: 300px;
        }
        .table td img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

    </style>
</head>
  <body>
    @include('admin.header')

    <div class="d-flex align-items-stretch">
        @include('admin.sidebar')

    <div class="page-content py-5">
        <div class="container-fluid">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">House List</h3>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-primary text-center">
                        <tr>
                            <th scope="col">House Title</th>
                            <th scope="col">Description</th>
                            <th scope="col">Price</th>
                            <th scope="col">Location</th>
                            <th scope="col">Category</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Image</th>
                            <th scope="col">Action</th>
                        </tr>
                        </thead>
                        <tbody class="text-center">
                        @foreach ($house as $houses)
                        <tr>
                            <td>{{ $houses->title }}</td>
                            <td>
                                <span class="description-short" id="short-{{ $houses->id }}">
                                    {{ Str::limit($houses->description, 20, '') }}
                                </span>
                                <span class="description-full" id="full-{{ $houses->id }}">
                                    {{ $houses->description }}
                                </span>
                                @if(strlen($houses->description) > 20)
                                    <span class="more-link" onclick="toggleDescription({{ $houses->id }})" id="toggle-{{ $houses->id }}">...more</span>
                                @endif
                            </td>
                            <td>{{ $houses->price }}</td>
                            <td>{{ $houses->location }}</td>
                            <td>{{ $houses->category }}</td>
                            <td>{{ $houses->quantity }}</td>
                            <td>
                                <img src="houses/{{ $houses->image }}" alt="House Image">
                                {{ $houses->image }}
                            </td>
                            <td>
                                <a class="btn btn-success btn-sm"
                                href="{{ url('edit_house', $houses->id) }}">Edit</a>
                                <a class="btn btn-danger btn-sm" onclick="confirmation(event)"
                                href="{{ url('delete_house', $houses->id) }}">Delete</a>
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        {{ $house->links('pagination::bootstrap-5') }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    </div>
    <!-- JavaScript files-->
    @include('admin.js')

  </body>
</html>
