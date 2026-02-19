@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="">Memo</a></li>
                        <li class="breadcrumb-item"><a href="">Edit Dokumen</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="container">
            <h3>📂 List Dokumen RAMS (Project ID 2)</h3>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Document Name</th>
                        <th>Document Number</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                        <tr>
                            <td>{{ $doc->id }}</td>
                            <td>{{ $doc->documentname }}</td>
                            <td>{{ $doc->documentnumber ?? '-' }}</td>
                            <td>{{ $doc->created_at ? $doc->created_at->format('d M Y H:i') : '-' }}</td>
                            <td>
                                @if (auth()->user()->rule != 'Logistik')
                                    <a class="btn btn-primary btn-sm"
                                        href="{{ route('new-memo.show', ['memoId' => $doc->id, 'rule' => auth()->user()->rule]) }}"
                                        style="width: 100px;">
                                        <i class="fas fa-folder"></i> Detail
                                    </a>
                                @else
                                    <span class="text-muted">Tidak ada aksi</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada dokumen RAMS ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
