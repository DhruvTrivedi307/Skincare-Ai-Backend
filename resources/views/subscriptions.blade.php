@extends('layouts.app')

@section('content')

    <div class="card">

        <div class="card h-100">
            <div class="card-body p-24">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                    <h6 class="mb-2 fw-bold text-lg mb-0">Subscriptions</h6>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriptionModal"><small>+ Add
                            Plan</small></button>
                </div>
                <div class="modal" id="addSubscriptionModal" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content p-3">
                            <div class="modal-header">
                                <h6 class="modal-title">Add Plan</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="{{ route('subscriptions.add') }}"
                                    class="d-flex flex-column justify-content-center">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="user_id" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="type" name="type" required>
                                        @error('type')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="plan_id" class="form-label">Price</label>
                                        <input type="number" class="form-control" id="price" name="price" required>
                                        @error('price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">Duration</label>
                                        <input type="text" class="form-control" id="duration" name="duration" required>
                                        @error('duration')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="scans" class="form-label">Scans</label>
                                        <input type="number" class="form-control" id="scans" name="scans" required>
                                        @error('scans')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="tokens" class="form-label">Tokens</label>
                                        <input type="number" class="form-control" id="tokens" name="tokens" required>
                                        @error('tokens')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary">Add Subscription</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0 fs-6">
                        <thead>
                            <tr>
                                <th class="text-center" scope="col">ID</th>
                                <th class="text-center" scope="col">Name</th>
                                <th class="text-center" scope="col">Price</th>
                                <th class="text-center" scope="col">Duration</th>
                                <th class="text-center" scope="col">Scans</th>
                                <th class="text-center" scope="col">Tokens</th>
                                <th class="text-center" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($subscriptions as $s)
                                <tr>
                                    <td>{{ $s->id }}</td>
                                    <td>{{ $s->type }}</td>
                                    <td>INR. {{ $s->price }}</td>
                                    <td>{{ $s->duration }} Months</td>
                                    <td>{{ $s->scans }}</td>
                                    <td>{{ $s->tokens }}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                            <button type="button"
                                                class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                data-bs-toggle="modal" data-bs-target="#editSubsModal{{ $s->id }}">
                                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                            </button>
                                            <button type="button"
                                                class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                data-bs-toggle="modal" data-bs-target="#deleteSubsModal{{ $s->id }}">
                                                <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $subscriptions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach ($subscriptions as $s)

        <div class="modal" id="editSubsModal{{ $s->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h6 class="modal-title">Edit Subscription {{ $s->id }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('subscriptions.update', $s->id) }}"
                            class="d-flex flex-column justify-content-center">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="user_id" class="form-label">Name</label>
                                <input type="text" class="form-control" id="type" name="type" value="{{ $s->type }}"
                                    required>
                                @error('type')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="plan_id" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ $s->price }}"
                                    required>
                                @error('price')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="duration" class="form-label">Duration</label>
                                <input type="text" class="form-control" id="duration" name="duration" value="{{ $s->duration }}"
                                    required>
                                @error('duration')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="scans" class="form-label">Scans</label>
                                <input type="number" class="form-control" id="scans" name="scans" value="{{ $s->scans }}"
                                    required>
                                @error('scans')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="tokens" class="form-label">Tokens</label>
                                <input type="number" class="form-control" id="tokens" name="tokens" value="{{ $s->tokens }}"
                                    required>
                                @error('tokens')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Update Subscription</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="deleteSubsModal{{ $s->id }}" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content p-3">
                    <form method="POST" action="{{ route('subscriptions.delete', $s->id) }}" class="modal-content">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body text-center">
                            <p class="mb-3">Are you sure you want to delete? <strong>(ID: {{ $s->id }})</strong></p>
                            <div class="d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    @endforeach

@endsection