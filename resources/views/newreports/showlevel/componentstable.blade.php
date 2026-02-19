{{-- resources/views/components/table-progress.blade.php --}}
<table id="{{ $tableId }}" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>
                <input type="checkbox" id="{{ $checkAllId }}">
            </th>
            <th>No</th>
            <th>No Dokumen</th>
            <th>Nama Dokumen</th>
            <th>Jenis Dokumen</th>
            <th>Rev Terakhir</th>
            <th>Realisasi</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($progressReports as $i => $r)
            <tr>
                <td>
                    <input type="checkbox" name="{{ $checkboxName }}" value="{{ $r->id }}">
                </td>
                <td>{{ $loop->iteration }}</td>

                <td>
                    <span id="docNum_{{ $r->id }}">{{ $r->nodokumen ?? '-' }}</span>

                </td>

                <td>{{ $r->namadokumen }}</td>

                {{-- Jenis Dokumen (editable oleh MTPR) --}}
                @if ($useronly->rule == 'MTPR')
                    <td>
                        <select class="form-control form-control-sm select-documentkind" data-id="{{ $r->id }}">
                            <option value="">-- Pilih --</option>
                            @foreach ($jenisdokumen as $j)
                                <option value="{{ $j->id }}"
                                    {{ $r->documentkind_id == $j->id ? 'selected' : '' }}>
                                    {{ $j->name }}
                                </option>
                            @endforeach
                        </select>
                    </td>
                @else
                    <td>{{ $r->documentKind->name ?? '-' }}</td>
                @endif

                <td>{{ $r->rev ?? '-' }}</td>
                <td>
                    {{ $r->realisasidate ? \Carbon\Carbon::parse($r->realisasidate)->format('d/m/Y') : '-' }}
                </td>
                <td>
                    <span class="badge badge-{{ $r->status == 'RELEASED' ? 'success' : 'warning' }}">
                        {{ $r->status ?? 'UNRELEASED' }}
                    </span>
                </td>
                <td>
                    <a href="{{ route('newreports.showrev', ['newreport' => $r->newreport_id, 'id' => $r->id]) }}"
                        class="btn btn-sm btn-info">Revisi</a>

                    @if ($r->latestHistory?->fileid)
                        <a href="http://10.10.0.40/AutodeskTC/10.10.0.40/TekVault_0003_Dec2011/Document/Download?fileId={{ $r->latestHistory->fileid }}&downloadAsInline=true"
                            target="_blank" class="btn btn-sm btn-secondary">PDF</a>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<script>
    // Update Jenis Dokumen
    $('.select-documentkind').change(function() {
        const id = $(this).data('id');
        const kindId = $(this).val();
        $.post('{{ route('newprogressreports.updateDocumentKind') }}', {
            _token: '{{ csrf_token() }}',
            progressreport_id: id,
            documentkind_id: kindId
        }).then(() => location.reload());
    });

    // Edit No Dokumen (MTPR only)
    function editDocNum(id) {
        Swal.fire({
            title: 'Edit No Dokumen',
            input: 'text',
            inputValue: $('#docNum_' + id).text().trim(),
            showCancelButton: true
        }).then(result => {
            if (result.isConfirmed) {
                $.post('/newreports/update-documentnumber', {
                    _token: '{{ csrf_token() }}',
                    nodokumen: result.value,
                    nodokumenlama: $('#docNum_' + id).text().trim(),
                    newreport_id: {{ $progressReports->first()->newreport_id ?? 0 }}
                }).then(res => {
                    if (res.status === 'success') {
                        $('#docNum_' + id).text(result.value);
                        Swal.fire('Sukses', res.message, 'success');
                    }
                });
            }
        });
    }
</script>
