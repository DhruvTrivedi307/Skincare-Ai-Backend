@section('title')
    Products
@endsection

@extends('layouts.app')
@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Products</h5>
                <button data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#addProductModal"
                    class="btn btn-sm btn-primary">
                    + Add Product
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Name</th>
                                <th scope="col" class="text-center">Image</th>
                                <th scope="col" class="text-center">Concerns</th>
                                <th scope="col" class="text-center">Created At</th>
                                <th scope="col" class="text-center">Updated At</th>
                                <th scope="col" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $p)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td style="max-width: 250px;">{{ $p->name }}</td>
                                    <td><img src="{{ asset('images/' . $p->image) }}" alt="{{ $p->name }}" width="100" class="rounded-2"></td>
                                    <td style="max-width: 250px; white-space: normal;">
                                        <div class="d-flex flex-wrap gap-1 justify-content-center">
                                            @php
                                                $colors = ['secondary', 'success', 'warning', 'info'];                                            
                                            @endphp
                                            @foreach ($p->concerns as $c)
                                                <span class="badge bg-{{ $colors[$loop->iteration % count($colors)] }} text-wrap" style="font-size: 11px;">
                                                    {{ $c->concern }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td>{{ $p->created_at->toDateString() }}</td>
                                    <td>{{ $p->updated_at->toDateString() }}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                            <button type="button"
                                                class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                data-bs-toggle="modal" data-bs-target="#editProductModal{{ $p->id }}">
                                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                            </button>
                                            <button type="button"
                                                class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                data-bs-toggle="modal" data-bs-target="#deleteProductModal{{ $p->id }}">
                                                <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editProductModal{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-xl modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('products.update', ['id' => $p->id]) }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Name</label>
                                                        <input type="text" class="form-control" id="name" name="product_name"
                                                            value="{{ $p->name }}" required>
                                                    </div>
                                                    <div class="concern-selector">
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <label for="name" class="form-label">Concerns</label>
                                                            <input type="text" class="border rounded-pill p-2 concern-search" name="search" placeholder="Search..." style="font-size: 14px;">
                                                        </div>
                                                        <div style="min-height: 250px; overflow-y: auto;">
                                                            <div class="d-flex flex-wrap gap-2 concern-list">
                                                                @foreach ($concerns as $concern)
                                                                    <label class="concern-chip concern-item">
                                                                        <input type="checkbox"
                                                                            name="skin_concern_id[]"
                                                                            value="{{ $concern->id }}"
                                                                            hidden
                                                                            {{ $p->concerns->contains($concern->id) ? 'checked' : '' }}>
                                                                        <span data-name="{{ strtolower($concern->concern) }}">
                                                                            {{ $concern->concern }}
                                                                        </span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="image" class="form-label">Image</label>
                                                        <input type="file" class="form-control" id="image" name="product_image">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary"
                                                        onclick="{{ route('products.update', ['id' => $p->id]) }}">Update
                                                        Product</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="deleteProductModal{{ $p->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Delete Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('products.delete', ['id' => $p->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete this product?</p>
                                                    <p>Id:{{ $loop->iteration }}, Name: {{ $p->name }}</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger"
                                                        onclick="{{ route('products.delete', ['id' => $p->id]) }}">Delete</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addProductModal" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('products.add') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="product_name" required>
                            @error('product_name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3 concern-selector">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <label for="name" class="form-label">Concerns</label>
                                <div>
                                    <input type="text" class="border rounded-pill p-2 concern-search" name="search" placeholder="Search..." style="font-size: 14px;">
                                    <button data-bs-toggle="modal" data-bs-target="#manageConcernModal" class="p-3"><iconify-icon icon="tabler:edit"></iconify-icon></button>
                                </div>
                            </div>
                            <div style="min-height: 250px; overflow-y: auto;">
                                <div class="d-flex flex-wrap gap-2 concern-list">
                                    @foreach ($concerns as $concern)
                                        <label class="concern-chip concern-item">
                                            <input type="checkbox"
                                                value="{{ $concern->id }}"
                                                name="skin_concern_id[]"
                                                hidden>
                                            <span data-name="{{ strtolower($concern->concern) }}">
                                                {{ $concern->concern }}
                                            </span>
                                        </label>
                                    @endforeach
                                    <label class="concern-chip">
                                        <input type="button"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#addConcernModal"
                                                hidden>
                                        <span><iconify-icon icon="tabler:plus" class="menu-icon"></iconify-icon></span>
                                    </label>
                                </div>
                            </div>

                            <style>
                                .concern-chip {
                                    border: 1px solid #cbd5e1;
                                    border-radius: 999px;
                                    cursor: pointer;
                                    font-size: 14px;
                                    transition: all 0.2s ease;
                                    display: inline-flex;
                                    align-items: center;
                                    overflow: hidden;
                                }

                                .concern-chip span {
                                    padding: 6px 14px;
                                    display: inline-block;
                                }

                                .concern-chip:hover span {
                                    background: #f1f5f9;
                                }

                                .concern-chip input:checked + span {
                                    background: #999999;
                                    color: #fff;
                                    border-radius: 999px;
                                }
                            </style>
                            @error('skin_concern_id')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="image" class="form-label">Image</label>
                            <input type="file" class="form-control" id="image" name="product_image">
                            @error('product_image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" onclick="{{ route('products.add') }}">Add
                            Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal" id="addConcernModal" tabindex="2">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Add New Concern</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="concern_name">Concern Name</label>
                    <form action="{{ route('concern.add') }}" method="POST" class="d-flex flex-column justify-content-end align-items-end gap-3">
                        @csrf
                        <input type="text" name="concern_name" class="form-control">
                        <button type="submit" class="btn btn-primary">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="manageConcernModal" tabindex="2">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title">Manage Concerns</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col" class="text-center">ID</th>
                                <th scope="col" class="text-center">Concern</th>
                                <th scope="col" class="text-center"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($concerns as $c)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $c->concern }}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                            <form action="{{ route('concern.delete', ['id' => $c->id]) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                    data-bs-toggle="modal" data-bs-target="#deleteProductModal" onclick="{{ route('concern.delete', ['id' => $c->id]) }}">
                                                    <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.concern-search').forEach(function (searchInput) {
            searchInput.addEventListener('input', function () {
                let value = this.value.toLowerCase();
                let concernSelector = this.closest('.concern-selector');

                if (!concernSelector) {
                    return;
                }

                concernSelector.querySelectorAll('.concern-item').forEach(function (item) {
                    let name = item.querySelector('span').dataset.name;

                    item.style.display = name.includes(value) ? 'inline-flex' : 'none';
                });
            });
        });
    </script>

@endsection
