@if (config('app.url') === 'https://inka.goovicess.com')
    @if ($file->filename == 'filekosong')
        <p>Maaf, file kosong dan tidak dapat diakses. Gunakan TeknoBot 🤖 ID: <strong
                style="font-size: 1.2em;">{{ $file->id }}</strong> untuk kirim rencana file anda.</p>
    @else
        <p>Maaf, file <strong style="font-size: 1.2em;">{{ $file->filename }}</strong> tidak dapat diakses. Gunakan
            TeknoBot 🤖
            ID: <strong style="font-size: 1.2em;">{{ $file->id }}</strong> untuk akses.</p>
        <button>Downloadfile_{{ $file->id }}</button>
    @endif
@else
    @php
        $fileExtension = pathinfo($file->link, PATHINFO_EXTENSION);
        // Daftar ekstensi untuk ditampilkan sebagai link (PDF dan gambar)
        $displayExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'PDF', 'mp4'];

        // Daftar ekstensi untuk diunduh
        $downloadExtensions = [
            'docx',
            'xlsx',
            'XLS',
            'zip',
            'doc',
            'pptx',
            'jfif',
            'rar',
            'dwg',
            'STEP',
            '7z',
            'txt',
            'csv',
            'json',
            'stp',
        ];

    @endphp

    @if (in_array($fileExtension, $displayExtensions))
        <!-- Tampilkan file PDF dan image -->
        <a href="{{ asset('storage/uploads/' . rawurlencode(str_replace('uploads/', '', $file->link))) }}"
            target="_blank">{{ $file->filename }}</a>
    @elseif(in_array($fileExtension, $downloadExtensions))
        <!-- Unduh file DOCX -->
        <a href="{{ route('download.file', ['path' => $file->link]) }}">{{ $file->filename }}</a>
    @else
        <!-- Tampilkan pesan jika tipe file tidak didukung -->
        <span>{{ $file->filename }} (Unsupported file type)</span>
    @endif
@endif


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    $(document).ready(function() {
        console.log("jQuery telah dimuat dengan sukses!");
    });

    function sendRequest(text) {
        let parts = text.split("_");
        let idfile = parts[1];

        Swal.fire({
            title: "Sedang mengirim...",
            text: "Tunggu sebentar...",
            icon: "info",
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
        });

        const dataToSend = {
            phone_numbers: ['{{ Auth::user()->waphonenumber }}'], // Pastikan ini dalam array
            message: idfile, // Menggunakan idfile untuk pesan
            wamessagekind: "file",
            idtoken: '2910219210291',
            accesstoken: '37237232u32y',
        };

        console.log("Data yang akan dikirim:", dataToSend); // Log data yang akan dikirim

        $.ajax({
            url: 'https://diyloveheart.in/api/wamessages/post',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(dataToSend),
            success: function(data) {
                Swal.fire({
                    title: "Berhasil!",
                    text: "Permintaan berhasil dikirim ke server.",
                    icon: "success",
                });
                navigator.clipboard.writeText(text).then(function() {
                    alert('Teks berhasil disalin ke clipboard');
                }, function(err) {
                    alert('Gagal menyalin teks');
                });
            },
            error: function(jqXHR) {
                console.log(jqXHR.responseText); // Tampilkan respons kesalahan
                let errorMessage = jqXHR.responseJSON?.message || jqXHR.statusText ||
                    'Terjadi kesalahan saat mengirim permintaan.';
                Swal.fire({
                    title: "Gagal!",
                    text: 'Gagal mengirim permintaan file: ' + errorMessage,
                    icon: "error",
                });
            }
        });
    }
</script>
