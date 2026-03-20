@extends('layouts.app')
@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Users</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">IP Address</th>
                                <th scope="col">User Token</th>
                                <th scope="col">Created At</th>
                                <th scope="col">Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $u)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $u->ip_address }}</td>
                                    <td>{{ $u->user_token }}</td>
                                    <td>{{ $u->created_at }}</td>
                                    <td>{{ $u->updated_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div> 

@endsection