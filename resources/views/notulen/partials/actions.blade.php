@if ($notulen->status == 'open')
    <button class="btn btn-warning btn-sm" onclick="confirmClose('{{ $notulen->id }}')">Tutup</button>
@else
    <button class="btn btn-secondary btn-sm" disabled>Closed</button>
@endif
