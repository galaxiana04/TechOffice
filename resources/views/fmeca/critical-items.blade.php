@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('fmeca.index') }}">FMECA Parts</a></li>
                        <li class="breadcrumb-item active">Critical Item List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                        <h3 class="card-title fw-bold mb-0">Critical Item List</h3>
                        <a href="{{ route('fmeca.critical-items.export') }}" class="btn btn-light">
                            <i class="bi bi-download"></i> Download Excel
                        </a>
                    </div>
                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-light">
                                    <h3 class="h6 mb-0">Critical Items (Undesirable or Intolerable)</h3>
                                </div>
                                <div class="card-body p-4">
                                    <div class="form-floating mb-3">
                                        <select id="project_filter" class="form-control">
                                            <option value="">All Projects</option>
                                            @foreach ($projectTypes as $projectType)
                                                <option value="{{ $projectType->id }}">{{ $projectType->title }}</option>
                                            @endforeach
                                        </select>
                                        <label for="project_filter">Filter by Project Type</label>
                                    </div>

                                    <ul class="nav nav-tabs mb-3" id="categoryTabs" role="tablist">
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab"
                                                data-bs-target="#all" type="button" role="tab">All</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="safety-tab" data-bs-toggle="tab"
                                                data-bs-target="#safety" type="button" role="tab">Safety</button>
                                        </li>
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link" id="reliability-tab" data-bs-toggle="tab"
                                                data-bs-target="#reliability" type="button"
                                                role="tab">Reliability</button>
                                        </li>
                                    </ul>

                                    <div class="tab-content" id="categoryTabContent">
                                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped align-middle" id="allTable">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Item Ref</th>
                                                            <th>Item Name</th>
                                                            <th>Part Name</th>
                                                            <th>Project</th>
                                                            <th>Risk Level</th>
                                                            <th>Category</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($criticalItems as $item)
                                                            <tr>
                                                                <td>{{ $item->id }}</td>
                                                                <td>{{ $item->item_ref }}</td>
                                                                <td>{{ $item->item_name }}</td>
                                                                <td>{{ $item->fmecaPart->name ?? 'N/A' }}</td>
                                                                <td>{{ $item->fmecaPart->fmecaIdentity->projectType->title ?? 'N/A' }}
                                                                </td>
                                                                <td>
                                                                    @if ($item->is_safety)
                                                                        {{ $item->safety_risk_level ?? 'N/A' }}
                                                                    @else
                                                                        {{ $item->reliability_risk_level ?? 'N/A' }}
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($item->is_safety && in_array($item->safety_risk_level, ['Undesirable', 'Intolerable']))
                                                                        Safety
                                                                    @elseif (!$item->is_safety && in_array($item->reliability_risk_level, ['Undesirable', 'Intolerable']))
                                                                        Reliability
                                                                    @else
                                                                        Both
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="safety" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped align-middle"
                                                    id="safetyTable">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Item Ref</th>
                                                            <th>Item Name</th>
                                                            <th>Part Name</th>
                                                            <th>Project</th>
                                                            <th>Risk Level</th>
                                                            <th>Category</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="reliability" role="tabpanel">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-striped align-middle"
                                                    id="reliabilityTable">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Item Ref</th>
                                                            <th>Item Name</th>
                                                            <th>Part Name</th>
                                                            <th>Project</th>
                                                            <th>Risk Level</th>
                                                            <th>Category</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    @if ($criticalItems->isEmpty())
                                        <p class="text-muted mb-0">No Critical Items found.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const projectFilter = document.getElementById('project_filter');
            const allTable = document.getElementById('allTable').querySelector('tbody');
            const safetyTable = document.getElementById('safetyTable').querySelector('tbody');
            const reliabilityTable = document.getElementById('reliabilityTable').querySelector('tbody');
            const criticalItems = @json($criticalItems);

            function filterItems(projectId, category) {
                let filteredItems = criticalItems;

                if (projectId) {
                    filteredItems = filteredItems.filter(item =>
                        item.fmecaPart?.fmecaIdentity?.projectType?.id == projectId
                    );
                }

                if (category === 'safety') {
                    filteredItems = filteredItems.filter(item =>
                        item.is_safety && ['Undesirable', 'Intolerable'].includes(item.safety_risk_level)
                    );
                } else if (category === 'reliability') {
                    filteredItems = filteredItems.filter(item =>
                        !item.is_safety && ['Undesirable', 'Intolerable'].includes(item.reliability_risk_level)
                    );
                }

                return filteredItems;
            }

            function updateTable(tableBody, items) {
                tableBody.innerHTML = '';
                if (items.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-muted">No items found.</td></tr>';
                    return;
                }

                items.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.id}</td>
                        <td>${item.item_ref}</td>
                        <td>${item.item_name}</td>
                        <td>${item.fmecaPart?.name || 'N/A'}</td>
                        <td>${item.fmecaPart?.fmecaIdentity?.projectType?.title || 'N/A'}</td>
                        <td>${item.is_safety ? (item.safety_risk_level || 'N/A') : (item.reliability_risk_level || 'N/A')}</td>
                        <td>${
                            item.is_safety && ['Undesirable', 'Intolerable'].includes(item.safety_risk_level) ? 'Safety' :
                            !item.is_safety && ['Undesirable', 'Intolerable'].includes(item.reliability_risk_level) ? 'Reliability' : 'Both'
                        }</td>
                    `;
                    tableBody.appendChild(row);
                });
            }

            function updateDownloadLink() {
                const projectId = projectFilter.value;
                const downloadLink = document.querySelector('.btn-light');
                downloadLink.href = projectId ?
                    `{{ route('fmeca.critical-items.export') }}?project_type_id=${projectId}` :
                    `{{ route('fmeca.critical-items.export') }}`;
            }

            projectFilter.addEventListener('change', function() {
                const projectId = this.value;
                updateDownloadLink();
                updateTable(allTable, filterItems(projectId, 'all'));
                updateTable(safetyTable, filterItems(projectId, 'safety'));
                updateTable(reliabilityTable, filterItems(projectId, 'reliability'));
            });

            document.getElementById('categoryTabs').addEventListener('shown.bs.tab', function(e) {
                const projectId = projectFilter.value;
                const target = e.target.getAttribute('data-bs-target').substring(1);
                if (target === 'all') {
                    updateTable(allTable, filterItems(projectId, 'all'));
                } else if (target === 'safety') {
                    updateTable(safetyTable, filterItems(projectId, 'safety'));
                } else if (target === 'reliability') {
                    updateTable(reliabilityTable, filterItems(projectId, 'reliability'));
                }
            });

            // Initial table population
            updateTable(allTable, filterItems('', 'all'));
            updateTable(safetyTable, filterItems('', 'safety'));
            updateTable(reliabilityTable, filterItems('', 'reliability'));
        });
    </script>
@endpush
