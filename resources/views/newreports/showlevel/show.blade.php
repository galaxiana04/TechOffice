{{-- resources/views/newreports/showlevel/show.blade.php --}}

@extends('layouts.universal')

@section('container2')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-12">
                    <ol class="breadcrumb bg-white px-2 float-left">
                        <li class="breadcrumb-item"><a href="{{ route('newreports.indexlevel') }}">List Level & Project</a>
                        </li>
                        <li class="breadcrumb-item active">Level Progress</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('container3')
    <div class="row justify-content-center">
        <div class="col-11">
            <div class="card card-danger card-outline">
                <div class="card-header">
                    <h3 class="card-title">Progress Dokumen - {{ $level->title }}</h3>
                </div>

                <div class="card card-outline card-danger">
                    <div class="card-header">
                        <table class="table table-bordered my-2 table-responsive-">
                            <tbody>
                                <tr>
                                    <td rowspan="7" style="width: 25%" class="text-center">
                                        <img src="{{ asset('images/logo-inka.png') }}" alt="IMS Logo" class="p-2"
                                            style="max-width: 250px">
                                    </td>
                                    <td rowspan="7" style="width: 50%">
                                        <h1 class="text-xl text-center mt-2">DAFTAR DOKUMEN & GAMBAR</h1>
                                    </td>
                                    <td style="width: 25%" class="p-1">Project:
                                        <b>{{ $project->title }}</b>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="width: 25%" class="p-1">Tanggal:
                                        <b>{{ date('d F Y') }}</b>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <div class="card-header">
                        <h3 class="card-title">Progres Dokumen</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Tabel Utama -->
                    @component('newreports.showlevel.componentstable', [
                        'progressReports' => $progressReports,
                        'jenisdokumen' => $jenisdokumen,
                        'useronly' => $useronly,
                        'tableId' => 'table-level',
                        'checkboxName' => 'document_ids[]',
                        'checkAllId' => 'checkAllLevel',
                    ])
                    @endcomponent
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables & Plugins -->
    <script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable untuk tabel level
            $('#table-level').DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true,
                pageLength: 10,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Belum ada dokumen di level ini"
                },
                columnDefs: [{
                        orderable: false,
                        targets: 0
                    }, // kolom checkbox tidak bisa di-sort
                    {
                        orderable: false,
                        targets: -1
                    } // kolom aksi tidak bisa di-sort
                ]
            });

            // Check/Uncheck All Checkbox
            $(document).on('click', '#checkAllLevel', function() {
                const isChecked = this.checked;
                $('input[name="document_ids[]"]').prop('checked', isChecked).trigger('change');
            });

            // Optional: highlight row saat checkbox dicentang
            $(document).on('change', 'input[name="document_ids[]"]', function() {
                $(this).closest('tr').toggleClass('table-warning', this.checked);
            });
        });

        // === FUNGSI MULTIPLE ACTION ===
        function handleDeleteMultipleItems() {
            const selected = $('input[name="document_ids[]"]:checked').map(function() {
                return this.value;
            }).get();

            if (selected.length === 0) {
                Swal.fire('Peringatan', 'Tidak ada dokumen yang dipilih', 'warning');
                return;
            }

            Swal.fire({
                title: 'Yakin ingin menghapus?',
                text: 'Dokumen beserta anak dan riwayatnya akan terhapus permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post('{{ route('newprogressreports.handleDeleteMultipleItems') }}', {
                            _token: '{{ csrf_token() }}',
                            document_ids: selected
                        })
                        .done(() => {
                            Swal.fire('Berhasil!', 'Dokumen berhasil dihapus', 'success')
                                .then(() => location.reload());
                        })
                        .fail(xhr => {
                            Swal.fire('Error', xhr.responseJSON?.error || 'Gagal menghapus dokumen', 'error');
                        });
                }
            });
        }

        function handleReleaseMultipleItems() {
            const selected = $('input[name="document_ids[]"]:checked').map(function() {
                return this.value;
            }).get();
            if (selected.length === 0) return Swal.fire('Info', 'Pilih minimal satu dokumen', 'info');

            Swal.fire({
                title: 'Release dokumen?',
                text: 'Status dokumen akan diubah menjadi RELEASED',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'Ya, release!'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post('{{ route('newprogressreports.handleReleaseMultipleItems') }}', {
                            _token: '{{ csrf_token() }}',
                            document_ids: selected
                        })
                        .done(() => location.reload())
                        .fail(() => Swal.fire('Error', 'Gagal merilis dokumen', 'error'));
                }
            });
        }

        function handleUnreleaseMultipleItems() {
            const selected = $('input[name="document_ids[]"]:checked').map(function() {
                return this.value;
            }).get();
            if (selected.length === 0) return Swal.fire('Info', 'Pilih minimal satu dokumen', 'info');

            Swal.fire({
                title: 'Batalkan release?',
                text: 'Status akan dikembalikan ke UNRELEASED',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#6c757d',
                confirmButtonText: 'Ya, unrelease!'
            }).then(result => {
                if (result.isConfirmed) {
                    $.post('{{ route('newprogressreports.handleUnreleaseMultipleItems') }}', {
                            _token: '{{ csrf_token() }}',
                            document_ids: selected
                        })
                        .done(() => location.reload())
                        .fail(() => Swal.fire('Error', 'Gagal membatalkan release', 'error'));
                }
            });
        }
    </script>
@endpush
