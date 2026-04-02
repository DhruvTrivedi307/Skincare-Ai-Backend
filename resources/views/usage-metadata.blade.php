@section('title')
    Usage Metadata
@endsection

@extends('layouts.app')
@section('content')

    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Usage Metadata</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table bordered-table mb-0">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Analysis Token</th>
                                <th scope="col">Prompt Token Count</th>
                                <th scope="col">Total Token Count</th>
                                <th scope="col">Candidates Token Count</th>
                                <th scope="col">Cached Content Token Count</th>
                                <th scope="col">Tool Use Prompt Token Count</th>
                                <th scope="col">Thoughts Token Count</th>
                                <th scope="col">Prompt Tokens Details</th>
                                <th scope="col">Cache Tokens Details</th>
                                <th scope="col">Candidates Tokens Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($metadata as $m)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $m->analysis_token }}</td>
                                    <td>{{ $m->prompt_token_count }}</td>
                                    <td>{{ $m->total_token_count }}</td>
                                    <td>{{ $m->candidates_token_count }}</td>
                                    <td>{{ $m->cached_content_token_count }}</td>
                                    <td>{{ $m->tool_use_prompt_token_count }}</td>
                                    <td>{{ $m->thoughts_token_count }}</td>
                                    <td>
                                        <button class="btn btn-sm text-primary" data-bs-toggle="modal" data-bs-target="#promptModal{{ $m->id }}">
                                            View JSON
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm text-primary" data-bs-toggle="modal" data-bs-target="#cacheModal{{ $m->id }}">
                                            View JSON 
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm text-primary" data-bs-toggle="modal" data-bs-target="#candidateModal{{ $m->id }}">
                                            View JSON
                                        </button>
                                    </td>

                                    <!-- Prompt Tokens Modal -->
                                    <div class="modal fade" id="promptModal{{ $m->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body bg-secondary m-3 rounded text-white">
                                                    <pre style="font-size:12px; white-space:pre-wrap;">
{!! json_encode(json_decode($m->prompt_tokens_details, true), JSON_PRETTY_PRINT) !!}
                                                    </pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Cache Tokens Modal -->
                                    <div class="modal fade" id="cacheModal{{ $m->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body bg-secondary m-3 rounded text-white">
                                                    <pre style="font-size:12px; white-space:pre-wrap;">
{!! json_encode(json_decode($m->cache_tokens_details, true), JSON_PRETTY_PRINT) !!}
                                                    </pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Candidates Tokens Modal -->
                                    <div class="modal fade" id="candidateModal{{ $m->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body bg-secondary m-3 rounded text-white">
                                                    <pre style="font-size:12px; white-space:pre-wrap;">
{!! json_encode(json_decode($m->candidates_tokens_details, true), JSON_PRETTY_PRINT) !!}
                                                    </pre>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="mt-5">
                        {{ $metadata->links() }}
                    </div>
                </div>
            </div>
        </div><!-- card end -->
    </div>

@endsection