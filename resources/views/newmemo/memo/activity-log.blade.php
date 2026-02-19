<div class="col-12">
    <div class="card card-primary card-outline shadow-lg border-0 modern-card">
        
        <div class="card-header gradient-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-3"></i>
                Log Aktivitas
            </h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool text-white" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool text-white" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="card-body modern-body">
            <div class="table-responsive">
                <table id="example2" class="table table-borderless table-hover">
                    <thead style="display: none;"> <tr>
                            <th>Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr>
                                <td class="p-2">
                                    <div class="log-item fade-in p-3 bg-white rounded shadow-sm border-start border-4 border-info h-100">
                                        
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="text-primary font-weight-bold mb-0">
                                                <i class="fas fa-circle-notch me-2 text-xs"></i>
                                                {{ $log->aksi }}
                                            </h6>
                                            <span class="badge bg-light text-muted border">
                                                <i class="far fa-clock me-1"></i> {{ $log->created_at->diffForHumans() }}
                                            </span>
                                        </div>

                                        <div class="log-message bg-light p-2 rounded mb-2">
                                            <p class="mb-0 text-dark">
                                                {{ json_decode($log->message)->pesan ?? 'Tidak ada pesan detail' }}
                                            </p>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center text-xs text-muted mt-2 border-top pt-2">
                                            <span>
                                                <i class="fas fa-user-circle me-1"></i> 
                                                <strong>{{ $log->user }}</strong>
                                            </span>
                                            <span>
                                                <i class="fas fa-tag me-1"></i> 
                                                {{ $log->jenisdata }}
                                            </span>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- CSS Tambahan untuk Log (Opsional, jika belum ada di file CSS utama) --}}
<style>
    /* Menghilangkan garis standar tabel agar terlihat seperti list timeline */
    #example2 td {
        border-top: none !important;
    }
    
    /* Hover effect untuk item log */
    .log-item {
        transition: all 0.3s ease;
    }
    .log-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1) !important;
    }

    /* Memastikan background text pesan nyaman dibaca */
    .log-message {
        background-color: #f8f9fa;
        border-left: 2px solid #dee2e6;
    }
</style>