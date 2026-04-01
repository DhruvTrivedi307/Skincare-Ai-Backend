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
                <div class="modal fade" id="addSubscriptionModal" tabindex="-1">
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
                                        <label for="plan_id" class="form-label">Price ($)</label>
                                        <input type="number" class="form-control" id="price" name="price" required>
                                        @error('price')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">Duration (Month(s))</label>
                                        <input type="text" class="form-control" id="duration" name="duration" required>
                                        @error('duration')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="scans" class="form-label">Scans</label>
                                        <input type="number" class="form-control" id="scans" name="scans" required>
                                        <small id="addTokensText">1 scan = 40 credit</small>
                                        @error('scans')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="tokens" class="form-label">
                                            Credits
                                            <br>
                                        </label>
                                        <input type="number" class="form-control" id="credits" name="credits" required>
                                        <small id="addCreditsText">1 credit = 100 tokens (~0.025 scan)</small>
                                        @error('credits')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <span id="estimatedCostText" class="mb-3 d-block fw-semibold">Estimated Cost: $0</span>
                                    <button type="submit" id="addSubscriptionBtn" class="btn btn-primary">Add Subscription</button>
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
                                <th class="text-center" scope="col">Credits</th>
                                <th class="text-center" scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-center">
                            @foreach ($subscriptions as $s)
                                <tr>
                                    <td>{{ $s->id."." }}</td>
                                    <td>{{ $s->type }}</td>
                                    <td>USD. {{ $s->price }}</td>
                                    <td>{{ $s->duration }} Months</td>
                                    <td>{{ $s->scans }}</td>
                                    <td>{{ $s->tokens }} ~</td>
                                    <td>{{ $s->credits }}</td>
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

        <div class="modal fade" id="editSubsModal{{ $s->id }}" tabindex="-1">
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
                                <label for="duration" class="form-label">Duration (Month(s))</label>
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
                                <small id="addTokensText">100 credit = 1 scan</small>
                                @error('scans')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="credits" class="form-label">Credits</label>
                                <input type="number" class="form-control" id="credits{{ $s->id }}" name="credits" value="{{ $s->credits }}"
                                    required>
                                <small><input class="w-100" type="text" id="tokensPreview{{ $s->id }}" placeholder="{{ $s->credits }} credits = {{ $s->credits * 100 }} tokens" readonly></small>
                                @error('credits')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <span id="estimatedCostText_u" class="mb-3 d-block fw-semibold">Estimated Cost: $0</span>
                            <button type="submit" class="btn btn-primary">Update Subscription</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteSubsModal{{ $s->id }}" tabindex="-1">
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
        <input type="hidden" id="usdtoinr" value="{{ $usdToInr }}">

<script>
    
    document.querySelectorAll('[id^="scans"]').forEach(input => {
        input.addEventListener('input', function () {
            const scans = parseInt(this.value) || 0;
            const credits = scans * 40;

            const modal = this.closest('.modal') || document;
            const creditsField = modal.querySelector('[id^="credits"]');

            if (creditsField) {
                creditsField.value = credits;
                creditsField.dispatchEvent(new Event('input'));
            }
        });
    });

    document.querySelectorAll('[id^="credits"]').forEach(input => {
        input.addEventListener('input', function () {
            const credits = parseInt(this.value) || 0;
            const tokens = credits * 100;
            const scans = Math.floor(credits / 40);

            const modal = this.closest('.modal') || document;

            const id = modal.id.replace('editSubsModal', '');
            const preview = document.getElementById(`tokensPreview${id}`);
            if (preview) {
                preview.value = `${credits} credits = ${tokens} tokens`;
            }

            const scansField = modal.querySelector('[id^="scans"]');
            if (scansField) {
                scansField.value = scans;
            }

            const text = modal.querySelector('#addCreditsText');
            if (text) {
                text.innerText = `${credits} credits = ${tokens} tokens (~${Math.floor(tokens / 4000)} scans)`;
            }

            const costText = document.getElementById('estimatedCostText');
            if (costText) {
                const cost = (tokens / 1000000) * 1.4;

                const usdtoinr = document.getElementById('usdtoinr').value;
                const costINR = cost * usdtoinr;

                costText.innerText = `Estimated Cost: $${cost.toFixed(2)}~ (₹${costINR.toFixed(2)}~)`;
            }

            const costText_u = document.getElementById('estimatedCostText_u');
            if (costText_u) {
                const cost = (tokens / 1000000) * 1.4;

                const usdtoinr = document.getElementById('usdtoinr').value;
                const costINR = cost * usdtoinr;

                costText_u.innerText = `Estimated Cost: $${cost.toFixed(2)}~ (₹${costINR.toFixed(2)}~)`;
            }
        });
    });
</script>

@endsection