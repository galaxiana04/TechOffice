@php
    use App\Models\Unit;
    use App\Models\Notification;

    function singkatan($str)
    {
        // Jika nilai sudah khusus (RAMS atau MTPR), langsung kembalikan
        if ($str == 'RAMS' || $str == 'MTPR') {
            return $str;
        }

        // Trim spasi awal/akhir
        $str = trim($str);

        // Jika cuma satu kata (tidak ada spasi), kembalikan langsung
        if (strpos($str, ' ') === false) {
            return $str;
        }

        // Split string menjadi kata-kata
        $words = explode(' ', $str);

        // Buat singkatan dari huruf pertama tiap kata
        $abbreviation = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $abbreviation .= strtoupper(substr($word, 0, 1));
            }
        }

        // Batas maksimal 10 karakter
        if (strlen($abbreviation) > 10) {
            $abbreviation = substr($abbreviation, 0, 10);
        }

        // Jika kosong (semua kata kosong), beri default
        if (strlen($abbreviation) == 0) {
            return 'DEFAULT';
        }

        return $abbreviation;
    }

    // Check if the user is logged in
    if (auth()->check()) {
        $userdef = auth()->user();
        $userdefrule = $userdef->rule;
        $unitsingkatan = singkatan($userdef->rule);
        $nama_divisi = $userdef->rule;

        // Search for the unit by name
        $unit = Unit::where('name', $nama_divisi)->first();

        // Fetch notifications only if the unit is found
        if ($unit) {
            $tugasdivisis = Notification::where('idunit', $unit->id)
                ->where('status', 'unread')
                ->with(['memo', 'unit']) // Misalnya, jika Anda juga butuh unit yang terkait
                ->get();
        } else {
            $tugasdivisis = collect([]);
        }
    } else {
        // Handle case when user is not authenticated, e.g., set default value or redirect
        $tugasdivisis = collect([]);
        $userdefrule = '';
        // Opsional: redirect atau beri notifikasi bahwa user harus login
        // return redirect('login'); // contoh redirect ke halaman login
    }

@endphp


<!-- Navbar -->
@include('partials.navbarpart')
<!-- /.navbar -->

<!-- Main Sidebar Container -->
@include('partials.sidebarpart')
