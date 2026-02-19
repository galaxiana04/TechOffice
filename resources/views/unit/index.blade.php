@extends('layouts.universal')

@section('container2')
    <div class="content-header py-2">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 mb-0">
                        <li class="breadcrumb-item"><a href="#">Library</a></li>
                        <li class="breadcrumb-item active font-weight-bold">Units</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card card-danger card-outline shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-building mr-2"></i> Daftar Unit</h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-end mb-3">
                            <a href="{{ route('unit.create') }}" class="btn btn-sm btn-danger">
                                <i class="fas fa-plus mr-1"></i> Tambah Unit
                            </a>
                        </div>

                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-sm">
                                <thead class="thead-light text-center">
                                    <tr>
                                        <th style="width: 5%">#</th>
                                        <th>Nama Unit</th>
                                        <th>Divisi Teknologi</th>
                                        <th class="text-center" style="width: 20%">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($units as $index => $unit)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td>{{ $unit->name }}</td>
                                            <td class="text-center">
                                                @if ($unit->is_technology_division)
                                                    <span class="badge badge-success">Ya</span>
                                                @else
                                                    <span class="badge badge-secondary">Tidak</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('unit.show', $unit) }}"
                                                    class="btn btn-sm btn-secondary">Lihat</a>
                                                <a href="{{ route('unit.edit', $unit) }}"
                                                    class="btn btn-sm btn-outline-danger">Edit</a>
                                                {{-- Uncomment jika ingin aktivasi tombol hapus
                                            <form action="{{ route('unit.destroy', $unit) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus unit ini?')">Hapus</button>
                                            </form> --}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('table').DataTable({
                responsive: true,
                paging: true,
                pageLength: 10,
                lengthChange: false,
                scrollX: true,
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                }
            });
        });
    </script>
@endpush
