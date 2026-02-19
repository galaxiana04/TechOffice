@extends('layouts.universal')

@php
    use Carbon\Carbon;
    $user = auth()->user();
@endphp

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 py-1 shadow-sm rounded">
                        <li class="breadcrumb-item"><a href="">Digital Innovation</a></li>
                        <li class="breadcrumb-item active">Progress</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Dokumentasi</h4>
                        <button class="btn btn-light btn-sm" onclick="showCreateModal()">
                            <i class="fas fa-plus me-1"></i> Add New
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Manual Book</th>
                                        <th>Flowchart</th>
                                        <th>Docs</th>
                                        @if ($user->id == 1)
                                            <th class="text-center">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($innovations as $innovation)
                                        <tr>
                                            <td><strong>{{ $innovation->name }}</strong></td>
                                            <td>{{ Str::limit($innovation->description, 80) }}</td>
                                            <td>
                                                @if ($innovation->manual_book_link)
                                                    <a href="{{ $innovation->manual_book_link }}" target="_blank"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-book"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($innovation->flow_chart_link)
                                                    <a href="{{ $innovation->flow_chart_link }}" target="_blank"
                                                        class="btn btn-sm btn-outline-info">
                                                        <i class="fas fa-project-diagram"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($innovation->documentation_link)
                                                    <a href="{{ $innovation->documentation_link }}" target="_blank"
                                                        class="btn btn-sm btn-outline-success">
                                                        <i class="fas fa-file-alt"></i>
                                                    </a>
                                                @endif
                                            </td>
                                            @if ($user->id == 1)
                                                <td class="text-center">
                                                    <button class="btn btn-warning btn-sm me-1"
                                                        onclick="showEditModal({{ $innovation }})">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-danger btn-sm"
                                                        onclick="deleteInnovation({{ $innovation->id }})">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No innovations yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div> <!-- table-responsive -->
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
