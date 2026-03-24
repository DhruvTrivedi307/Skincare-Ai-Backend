@extends('layouts.app')

@section('content')

    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-24">
        <h6 class="fw-semibold mb-0">Dashboard</h6>
    </div>

    @if (Auth::user()->role === 'Super-Admin')
        <div class="row row-cols-xxxl-5 row-cols-lg-3 row-cols-sm-2 row-cols-1 gy-4">
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-1 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Total Users</p>
                                <h6 class="mb-0">{{ $users }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-cyan rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="gridicons:multiple-users" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon
                                    icon="bxs:up-arrow" class="text-xs"></iconify-icon> +5000</span>
                            Last 30 days users
                        </p>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-2 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Total Subscription</p>
                                <h6 class="mb-0">{{ $subscription }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-purple rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fa-solid:award" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center gap-1 text-danger-main"><iconify-icon
                                    icon="bxs:down-arrow" class="text-xs"></iconify-icon> -800</span>
                            Last 30 days subscription
                        </p>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-3 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Total Scans</p>
                                <h6 class="mb-0">{{ $scans }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-info rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fluent:scan-person-24-filled"
                                    class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon
                                    icon="bxs:up-arrow" class="text-xs"></iconify-icon> +200</span>
                            Last 30 days users
                        </p>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-6 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Total Token Usage</p>
                                <h6 class="mb-0">{{ $token_usage }}</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-warning rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fluent:ticket-diagonal-16-filled"
                                    class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon
                                    icon="bxs:up-arrow" class="text-xs"></iconify-icon> +200</span>
                            Last 30 days users
                        </p>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-4 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Total Income</p>
                                <h6 class="mb-0">$0</h6>
                            </div>
                            <div
                                class="w-50-px h-50-px bg-success-main rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="solar:wallet-bold" class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon
                                    icon="bxs:up-arrow" class="text-xs"></iconify-icon> +$20,000</span>
                            Last 30 days income
                        </p>
                    </div>
                </div><!-- card end -->
            </div>
            <div class="col">
                <div class="card shadow-none border bg-gradient-start-5 h-100">
                    <div class="card-body p-20">
                        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                            <div>
                                <p class="fw-medium text-primary-light mb-1">Total Expense</p>
                                <h6 class="mb-0">${{ number_format($expenses, 2) }}</h6>
                            </div>
                            <div class="w-50-px h-50-px bg-red rounded-circle d-flex justify-content-center align-items-center">
                                <iconify-icon icon="fa6-solid:file-invoice-dollar"
                                    class="text-white text-2xl mb-0"></iconify-icon>
                            </div>
                        </div>
                        <p class="fw-medium text-sm text-primary-light mt-12 mb-0 d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center gap-1 text-success-main"><iconify-icon
                                    icon="bxs:up-arrow" class="text-xs"></iconify-icon> +$5,000</span>
                            Last 30 days expense
                        </p>
                    </div>
                </div><!-- card end -->
            </div>
        </div>
    @endif

    <div class="flex flex-row mt-5">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between">
                    <h6 class="mb-2 fw-bold text-lg mb-0">Generated Content - {{ now()->year }}</h6>
                    {{-- <select class="form-select form-select-sm w-auto bg-base border text-secondary-light radius-8">
                        <option>Today</option>
                        <option>Weekly</option>
                        <option>Monthly</option>
                        <option>Yearly</option>
                    </select> --}}
                </div>

                <ul class="d-flex flex-wrap align-items-center mt-3 gap-3">
                    <li class="d-flex align-items-center gap-2">
                        <span class="w-12-px h-12-px rounded-circle bg-primary-600"></span>
                        <span class="text-secondary-light text-sm fw-semibold">Success:
                            <span class="text-primary-light fw-bold">{{ array_sum($completed) }}</span>
                        </span>
                    </li>
                    <li class="d-flex align-items-center gap-2">
                        <span class="w-12-px h-12-px rounded-circle bg-yellow"></span>
                        <span class="text-secondary-light text-sm fw-semibold">Failure:
                            <span class="text-primary-light fw-bold">{{ array_sum($failed) }}</span>
                        </span>
                    </li>
                </ul>

                <div class="mt-40">
                    <div id="paymentStatusChart" class="margin-16-minus"></div>
                </div>

            </div>
        </div>

        <div class="card mt-5">

            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Analysis Records</h6>
                    </div>
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col" class="text-center">ID</th>
                                    {{-- <th scope="col" class="text-center">IP Address</th> --}}
                                    <th scope="col" class="text-center">Admin ID</th>
                                    <th scope="col" class="text-center">Token</th>
                                    <th scope="col" class="text-center">Result</th>
                                    <th scope="col" class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $d)
                                    <tr>
                                        <td class="text-center">{{ $d->id }}</td>
                                        {{-- <td class="text-center">{{ $d->ip_address }}</td> --}}
                                        <td class="text-center">{{ $d->admin_id }}</td>
                                        <td class="text-center">{{ $d->user_token }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm text-primary" data-bs-toggle="modal"
                                                data-bs-target="#jsonModal{{ $d->id }}">
                                                View Details
                                            </button>

                                            @php
                                                $decoded = is_string($d->result) ? json_decode($d->result, true) : $d->result;
                                            @endphp

                                            <div class="modal fade" id="jsonModal{{ $d->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                                    <div class="modal-content">
                                                        <div class="modal-header p-24">
                                                            <h6 class="modal-title">Analysis Result - ID {{ $d->id }}</h6>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">

                                                            @if(is_array($decoded) && isset($decoded['error']) && $decoded['error'] === true)

                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Error</th>
                                                                                <th>Title</th>
                                                                                <th>Message</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>{{ $decoded['error'] ?? '-' }}</td>
                                                                                <td>{{ $decoded['title'] ?? '-' }}</td>
                                                                                <td>{{ $decoded['message'] ?? '-' }}</td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                            @elseif(is_array($decoded))

                                                                <div class="table-responsive">
                                                                    <table class="table table-bordered">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Region</th>
                                                                                <th>Issues</th>
                                                                                <th>Confidence</th>
                                                                                <th>Rating</th>
                                                                                <th>Skin Rating</th>
                                                                                <th>Result</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                            @foreach($decoded as $region => $details)
                                                                                <tr>
                                                                                    <td>{{ ucfirst(str_replace('_', ' ', $region)) }}
                                                                                    </td>
                                                                                    <td>
                                                                                        @if(isset($details['issue']))
                                                                                            {{ implode(', ', $details['issue']) }}
                                                                                        @endif
                                                                                    </td>
                                                                                    <td>{{ $details['confidence'] ?? '-' }}</td>
                                                                                    <td>{{ $details['rating'] ?? '-' }}</td>
                                                                                    <td>{{ $details['skin_rating'] ?? '-' }}</td>
                                                                                    <td>{{ $details['result'] ?? '-' }}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        </tbody>
                                                                    </table>
                                                                </div>

                                                            @else
                                                                <pre>{{ $d->result }}</pre>
                                                            @endif

                                                            <span><b>Token Used :</b> {{ $d->token_usage ?? 'N/A' }}</span> <br>
                                                            <span><b>Date : </b> {{ $d->created_at ?? 'N/A' }}</span>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            @if(is_array($decoded) && isset($decoded['error']) && $decoded['error'] === true)
                                                <span
                                                    class="bg-danger-focus text-danger-main px-24 py-4 rounded-pill fw-medium text-sm">Error</span>
                                            @else
                                                <span class="
                                                    @if ($d->status === "completed")
                                                        bg-success-focus text-success-main
                                                    @elseif ($d->status === "failed")
                                                        bg-danger-focus text-danger-main
                                                    @elseif ($d->status === "processing")
                                                        bg-warning-focus text-warning-main
                                                    @endif    
                                                    px-24 py-4 rounded-pill fw-medium text-sm
                                                ">{{ $d->status }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            window.completed = @json($completed);
            window.failed = @json($failed);
        </script>
@endsection