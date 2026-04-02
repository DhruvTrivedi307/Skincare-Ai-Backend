@section('title')
    Activity Logs
@endsection

@extends('layouts.app')

@section('content')

    <div class="card mt-5">

            <div class="card h-100">
                <div class="card-body p-24">
                    <div class="d-flex align-items-center flex-wrap gap-2 justify-content-between mb-20">
                        <h6 class="mb-2 fw-bold text-lg mb-0">Analysis Records</h6>
                    </div>
                    <div class="table-responsive scroll-sm">
                        <table class="table bordered-table mb-0 fs-6">
                            <thead>
                                <tr>
                                    <th scope="col">ID</th>
                                    <th scope="col">User Id</th>
                                    <th scope="col">Activity</th>
                                    <th scope="col">IP Address</th>
                                    <th scope="col">Throttle Key</th>
                                    <th scope="col">Created At</th>
                                    <th scope="col">Updated At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($logs as $l)
                                    <tr>
                                        <td>{{ $l->id }}</td>
                                        <td>{{ $l->user_id }}</td>
                                        <td>{{ $l->activity }}</td>
                                        <td>{{ $l->ip_address }}</td>
                                        <td>{{ $l->throttle_key }}</td>
                                        <td>{{ $l->created_at }}</td>
                                        <td>{{ $l->updated_at }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

@endsection