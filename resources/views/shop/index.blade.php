@extends('layouts.master')

@section('content')
<main>
    <div class="container-xl px-4 mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="text-white mb-0">Shop List</h5>
            </div>

            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between mb-3">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShopModal">
                        <i class="fas fa-plus"></i> Add Shop
                    </button>
                    <form action="{{ route('shop.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control form-control-sm me-2" placeholder="Search shop..." value="{{ request('search') }}">
                        <button type="submit" class="btn btn-outline-secondary btn-sm">Search</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Shop Name</th>
                                <th>Image</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($shops as $index => $shop)
                                <tr>
                                    <td>{{ $shops->firstItem() + $index }}</td>
                                    <td>{{ $shop->name }}</td>
                                    <td>
                                        @if ($shop->image)
                                            <img src="{{ asset('storage/' . $shop->image) }}" alt="Shop Image" width="50">
                                        @else
                                            <span class="text-muted">No image</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($shop->created_at)->format('d M Y') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editShopModal{{ $shop->id }}"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteShopModal{{ $shop->id }}"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5">No shops found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end">
                    {{ $shops->appends(['search' => request('search')])->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Shop Modal -->
    <div class="modal fade" id="addShopModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('shop.store') }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add Shop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Shop Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Shop Image (optional)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit & Delete Modals -->
    @foreach($shops as $shop)
    <div class="modal fade" id="editShopModal{{ $shop->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('shop.update', $shop->id) }}" method="POST" enctype="multipart/form-data" class="modal-content">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Shop</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label>Shop Name</label>
                    <input type="text" name="name" class="form-control" value="{{ $shop->name }}" required>
                    <label class="mt-2">Change Image (optional)</label>
                    <input type="file" name="image" class="form-control">
                    @if ($shop->image)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $shop->image) }}" alt="Current Image" width="100">
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteShopModal{{ $shop->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('shop.destroy', $shop->id) }}" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title">Delete Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete <strong>{{ $shop->name }}</strong>?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-danger">Delete</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</main>
@endsection
