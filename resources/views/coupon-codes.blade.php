@section('title')
    Coupon Codes
@endsection

@extends('layouts.app')

@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title mb-0">Coupon Management</h5>
                <button class="btn btn-secondary d-flex align-items-center gap-2" data-bs-toggle="modal"
                    data-bs-target="#addCouponModal">
                    <small>+ Add New Coupon</small>
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive scroll-sm">
                    <table class="table bordered-table mb-0 fs-6">
                        <thead>
                            <tr>
                                <th class="text-center">Description</th>
                                <th class="text-center">Code</th>
                                <th class="text-center">Percentage</th>
                                <th class="text-center">Amount</th>
                                <th class="text-center">Active From</th>
                                <th class="text-center">Active To</th>
                                <th class="text-center">Limit Number</th>
                                <th class="text-center">Used Limit</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($coupons as $c)
                                <tr>
                                    <td style="max-width: 220px;">
                                        <button class="d-inline-block text-truncate" style="max-width: 150px;"
                                            data-bs-container="body" data-bs-toggle="popover" data-bs-trigger="focus"
                                            data-bs-placement="right" data-bs-content="{{ $c->description }}" type="button">
                                            {{ $c->description }}
                                        </button>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success px-2.5 py-2.5 fs-6">
                                            {{ $c->code }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $c->discount_percentage }}%
                                    </td>
                                    <td>
                                        ₹{{ $c->discount_amount }}
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($c->valid_from)->format('d M Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($c->valid_until)->format('d M Y') }}</td>
                                    <td>{{ $c->usage_limit }}</td>
                                    <td>{{ $c->used_count }}</td>
                                    <td class="text-center">
                                        <div class="d-flex align-items-center gap-10 justify-content-center">
                                            <button type="button"
                                                class="bg-success-focus text-success-600 bg-hover-success-200 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                data-bs-toggle="modal" data-bs-target="#editCouponModal{{ $c->id }}">
                                                <iconify-icon icon="lucide:edit" class="menu-icon"></iconify-icon>
                                            </button>
                                            <button type="button"
                                                class="remove-item-btn bg-danger-focus bg-hover-danger-200 text-danger-600 fw-medium w-40-px h-40-px d-flex justify-content-center align-items-center rounded-circle"
                                                data-bs-toggle="modal" data-bs-target="#deleteCouponModal{{ $c->id }}">
                                                <iconify-icon icon="fluent:delete-24-regular" class="menu-icon"></iconify-icon>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <div class="modal" id="addCouponModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-3">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Coupon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('coupon-codes.add') }}"
                        class="d-flex flex-column justify-content-center">
                        @csrf
                        <div class="mb-3">
                            <label for="code" class="form-label">Code</label>
                            <input type="text" class="form-control" id="code" name="code" required>
                            @error('code')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" class="form-control"></textarea>
                            @error('description')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                            <input type="number" step="0.01" class="form-control" id="discount_percentage"
                                name="discount_percentage">
                            @error('discount_percentage')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="discount_amount" class="form-label">Discount Amount (₹)</label>
                            <input type="number" step="0.01" class="form-control" id="discount_amount"
                                name="discount_amount">
                            @error('discount_amount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="sub_total_limit" class="form-label">Sub Total Limit (₹)</label>
                            <input type="number" step="0.01" class="form-control" id="sub_total_limit"
                                name="sub_total_limit">
                            @error('sub_total_limit')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="valid_from" class="form-label">Valid From</label>
                            <input type="datetime-local" class="form-control" id="valid_from" name="valid_from">
                            @error('valid_from')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="valid_until" class="form-label">Valid Until</label>
                            <input type="datetime-local" class="form-control" id="valid_until" name="valid_until">
                            @error('valid_until')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="usage_limit" class="form-label">Usage Limit</label>
                            <input type="number" class="form-control" id="usage_limit" name="usage_limit">
                            @error('usage_limit')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Add Coupon</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @foreach ($coupons as $c)

        <div class="modal" id="editCouponModal{{ $c->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content p-3">
                    <div class="modal-header">
                        <h6 class="modal-title">Edit Subscription {{ $c->id }}</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="{{ route('coupon-codes.update', $c->id) }}"
                            class="d-flex flex-column justify-content-center">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="code" class="form-label">Code</label>
                                <input type="text" class="form-control" id="code" name="code" value="{{ $c->code }}" required>
                                @error('code')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control">{{ $c->description }}</textarea>
                                @error('description')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="discount_percentage" class="form-label">Discount Percentage (%)</label>
                                <input type="number" step="0.01" class="form-control" id="discount_percentage"
                                    name="discount_percentage" value="{{ $c->discount_percentage }}">
                                @error('discount_percentage')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="discount_amount" class="form-label">Discount Amount (₹)</label>
                                <input type="number" step="0.01" class="form-control" id="discount_amount"
                                    name="discount_amount" value="{{ $c->discount_amount }}">
                                @error('discount_amount')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="sub_total_limit" class="form-label">Sub Total Limit (₹)</label>
                                <input type="number" step="0.01" class="form-control" id="sub_total_limit"
                                    name="sub_total_limit" value="{{ $c->sub_total_limit }}">
                                @error('sub_total_limit')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="valid_from" class="form-label">Valid From</label>
                                <input type="datetime-local" class="form-control" id="valid_from" name="valid_from" value="{{ $c->valid_from }}">
                                @error('valid_from')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="valid_until" class="form-label">Valid Until</label>
                                <input type="datetime-local" class="form-control" id="valid_until" name="valid_until" value="{{ $c->valid_until }}">
                                @error('valid_until')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="usage_limit" class="form-label">Usage Limit</label>
                                <input type="number" class="form-control" id="usage_limit" name="usage_limit" value="{{ $c->usage_limit }}">
                                @error('usage_limit')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Update Subscription</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal" id="deleteCouponModal{{ $c->id }}" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content p-3">
                    <form method="POST" action="{{ route('coupon-codes.delete', $c->id) }}" class="modal-content">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body text-center">
                            <p class="mb-3">Are you sure you want to delete? <strong>(Code: {{ $c->code }})</strong></p>
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