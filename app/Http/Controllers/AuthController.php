<?php

namespace App\Http\Controllers;


use Carbon\Carbon;
use App\Models\User;
use App\Models\Category;
use App\Models\CollectFile;
use App\Models\Unit;
use App\Models\SystemLog;
use App\Models\Wagroupnumber;
use App\Services\TelegramService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Password;
use Telegram\Bot\Laravel\Facades\Telegram;
use App\Models\Role;


class AuthController extends Controller
{
    /**
     * Display the login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle the login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        // Coba login menggunakan email
        if (
            Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password']])
            || Auth::attempt(['username' => $credentials['email'], 'password' => $credentials['password']])
        ) {

            $user = Auth::user();

            // Simpan email ke session
            $request->session()->put('email', $user->email);

            // Daftar role yang masuk ke halaman '/'
            $rolesToRoot = [
                'superuser',
                'Senior Manager Desain',
                'Senior Manager Teknologi Produksi',
                'Senior Manager Engineering',
                'MTPR',
                'Manager MTPR',
                'Product Engineering',
                'Manager Product Engineering',
                'Electrical Engineering System',
                'Manager Electrical Engineering System',
                'Mechanical Engineering System',
                'Manager Mechanical Engineering System',
                'Quality Engineering',
                'Manager Quality Engineering',
                'RAMS',
                'Manager RAMS',
                'Desain Mekanik & Interior',
                'Manager Desain Mekanik & Interior',
                'Desain Bogie & Wagon',
                'Manager Desain Bogie & Wagon',
                'Desain Carbody',
                'Manager Desain Carbody',
                'Desain Elektrik',
                'Manager Desain Elektrik',
                'Preparation & Support',
                'Manager Preparation & Support',
                'Welding Technology',
                'Manager Welding Technology',
                'Shop Drawing',
                'Manager Shop Drawing',
                'Teknologi Proses',
                'Manager Teknologi Proses',
            ];

            $rolesToProgress = [
                'PPC',
                'PPO',
                'Finishing Bogie',
                'Fabrikasi Bogie',
            ];

            // Redirect berdasarkan role
            $userRule = strtolower($user->rule);

            if (in_array($userRule, array_map('strtolower', $rolesToProgress))) {
                return redirect('/newreports');
            } elseif (in_array($userRule, array_map('strtolower', $rolesToRoot))) {
                return redirect()->intended('/');
            } else {
                // Jika tidak ada role yang cocok, redirect ke halaman default
                return redirect()->intended('/');
            }
        }

        // Gagal login
        return back()->withErrors([
            'email' => 'Email atau sandi yang anda masukan salah.',
        ])->withInput($request->only('email'));
    }


    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        // Log the user out
        Auth::logout();

        // Forget the 'email' session variable
        session()->forget('email');

        // Redirect to the home page
        return redirect('/');
    }

    public function resetPassword(Request $request)
    {
        // Validasi input untuk memastikan user dipilih
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Cari user berdasarkan ID yang dipilih
        $user = User::find($request->user_id);

        // Reset password menjadi "12345"
        $user->password = bcrypt('12345');
        $user->save();

        // Kembalikan ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()->with('success', 'Password berhasil direset menjadi "12345" untuk user yang dipilih.');
    }


    public function ResetForm()
    {
        $users = User::all();
        return view('auth.resetpassword', compact('users'));
    }


    public function showRegistrationForm()
    {
        $listpic = Unit::pluck('name');
        return view('auth.register', compact('listpic'));
    }
    public function showUpdateForm()
    {
        // Get the category data as before


        $roles = Role::all(); // Ambil semua role
        $units = Unit::all(); // Ambil semua role

        // Ambil data user yang sedang login sekali saja
        $user = auth()->user();

        // Check if user already has a signature (TTD) file
        $existingFile = CollectFile::where('collectable_id', $user->id)
            ->where('collectable_type', User::class)
            ->first();

        // Kirim semua data ke view
        return view('auth.update', compact('roles', 'units', 'user', 'existingFile'));
    }

    // Handle registrasi
    public function registerform(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            'password' => 'required',
            'rule' => 'required',
            'waphonenumber' => 'required',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'rule' => $request->rule,
            'waphonenumber' => $request->waphonenumber,
        ]);

        // Assuming you want to redirect to the home page
        return redirect('/');
    }


    // Metode untuk memproses pembaruan informasi pengguna
    public function updateInformasi(Request $request)
    {

        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'waphonenumber' => 'required|string|max:255',
            'telegram_id' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'rule' => 'required|string|max:255',
        ]);





        // Tentukan role_id berdasarkan rule
        if (Str::contains($request->rule, 'Senior Manager')) {
            $user_role_id = 4; // Senior Manager
        } elseif (Str::contains($request->rule, 'Manager')) {
            $user_role_id = 2; // Manager
        } else {
            $user_role_id = 1;
        }







        if (Str::contains($request->rule, 'Product Engineering')) {
            $user_unit_id = 2;
        } elseif (Str::contains($request->rule, 'Electrical Engineering System')) {
            $user_unit_id = 3;
        } elseif (Str::contains($request->rule, 'Mechanical Engineering System')) {
            $user_unit_id = 4;
        } elseif (Str::contains($request->rule, 'Desain Mekanik & Interior')) {
            $user_unit_id = 5;
        } elseif (Str::contains($request->rule, 'Desain Bogie & Wagon')) {
            $user_unit_id = 6;
        } elseif (Str::contains($request->rule, 'Desain Carbody')) {
            $user_unit_id = 7;
        } elseif (Str::contains($request->rule, 'Desain Elektrik')) {
            $user_unit_id = 8;
        } elseif (Str::contains($request->rule, 'Preparation & Support')) {
            $user_unit_id = 9;
        } elseif (Str::contains($request->rule, 'Welding Technology')) {
            $user_unit_id = 10;
        } elseif (Str::contains($request->rule, 'Shop Drawing')) {
            $user_unit_id = 11;
        } elseif (Str::contains($request->rule, 'Teknologi Proses')) {
            $user_unit_id = 12;
        } elseif (Str::contains($request->rule, 'RAMS')) {
            $user_unit_id = 13;
        } elseif (Str::contains($request->rule, 'Quality Engineering')) {
            $user_unit_id = 14;
        } elseif (Str::contains($request->rule, 'MTPR')) {
            $user_unit_id = 31;
        } elseif (Str::contains($request->rule, 'Senior Manager Engineering')) {
            $user_unit_id = 28;
        } elseif (Str::contains($request->rule, 'Senior Manager Desain')) {
            $user_unit_id = 29;
        } elseif (Str::contains($request->rule, 'Senior Manager Teknologi Produksi')) {
            $user_unit_id = 30;
        } elseif (Str::contains($request->rule, 'Senior Manager Logistik')) {
            $user_unit_id = 43;
        } elseif (Str::contains($request->rule, 'Logistik')) {
            $user_unit_id = 36;
        } elseif (Str::contains($request->rule, 'Manager Logistik')) {
            $user_unit_id = 36;
        } else {
            $user_unit_id = $request->unit_id;
        }




        $user->update([
            'telegram_id' => $request->telegram_id,
            'name' => $request->name,
            'email' => $request->email,
            'waphonenumber' => $request->waphonenumber,
            'rule' => $request->rule,
            'role_id' => $user_role_id,
            'unit_id' => $user_unit_id,
        ]);
        return redirect()->back()->with('success', 'Informasi berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        // Verifikasi password saat ini
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->with('error', 'Password saat ini tidak cocok.');
        }

        // Enkripsi dan simpan password baru
        $user->password = Hash::make($request->password);
        $user->save();
        return redirect('/');
    }


    public function updateRole(Request $request, User $user)
    {
        $user->rule = $request->role;
        $user->save();
        return redirect()->back()->with('success', 'Peran pengguna berhasil diperbarui.');
    }
    public function deleteUser(User $user)
    {
        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }


    /**
     * Display the form to request a password reset link.
     *
     * @return \Illuminate\View\View
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle a reset password link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );
        Telegram::sendMessage([
            'chat_id' => -4276878904,
            'text' => "Terjadi ganti sandi",
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $token
     * @return \Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    /**
     * Handle a password reset request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
    public function setInternalOn(Request $request)
    {
        try {
            Session::put('internalon', true);
            if (Session::has('internalon')) {
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Failed to set session variable'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function setInternalOff(Request $request)
    {
        try {
            session()->forget('internalon');
            if (!Session::has('internalon')) {
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Failed to clear session variable'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function showAllUsers()
    {

        // Fetch logs for all users within the current week
        $logs = SystemLog::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        // Initialize an array to store the activity data
        $activityData = [
            'Sunday' => array_fill(0, 24, 0),
            'Monday' => array_fill(0, 24, 0),
            'Tuesday' => array_fill(0, 24, 0),
            'Wednesday' => array_fill(0, 24, 0),
            'Thursday' => array_fill(0, 24, 0),
            'Friday' => array_fill(0, 24, 0),
            'Saturday' => array_fill(0, 24, 0)
        ];

        // Iterate through the logs
        foreach ($logs as $log) {
            $date = Carbon::parse($log->created_at);
            $hour = $date->format('H'); // Format hour in 24-hour format (e.g., "00", "13")
            $weekday = $date->format('l'); // Full weekday name (e.g., "Sunday")

            // Ensure the hour key exists in the activity data
            if (isset($activityData[$weekday])) {
                if (isset($activityData[$weekday][$hour])) {
                    $activityData[$weekday][$hour]++;
                }
            }
        }

        // Flatten the data to match the desired output format
        $flattenedActivityData = [];
        foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $weekday) {
            foreach ($activityData[$weekday] as $hour => $value) {
                $formattedHour = Carbon::createFromFormat('H', $hour)->format('gA'); // Convert 24-hour format to 12-hour format
                $flattenedActivityData[] = [
                    'hour' => $formattedHour,
                    'weekday' => $weekday,
                    'value' => $value,
                ];
            }
        }

        $encodedFlattenedActivityData = json_encode($flattenedActivityData);
        $category = Category::where('category_name', 'unitall')->pluck('category_member');
        $users = User::all();

        return view('auth.all_users', compact('users', 'category', 'logs', 'encodedFlattenedActivityData'));
    }

    public function getUserLogs($userId)
    {
        $user = User::find($userId);

        // Validate if user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Fetch logs for the specific user for the current week
        $logsthisweek = SystemLog::where('user_id', $userId)
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->get();

        // Initialize an array to store the activity data
        $activityData = [
            'Sunday' => array_fill(0, 24, 0),
            'Monday' => array_fill(0, 24, 0),
            'Tuesday' => array_fill(0, 24, 0),
            'Wednesday' => array_fill(0, 24, 0),
            'Thursday' => array_fill(0, 24, 0),
            'Friday' => array_fill(0, 24, 0),
            'Saturday' => array_fill(0, 24, 0)
        ];

        // Iterate through the logs
        foreach ($logsthisweek as $log) {
            $date = Carbon::parse($log->created_at);
            $hour = $date->hour; // Get hour in 24-hour format as an integer
            $weekday = $date->format('l'); // Full weekday name (e.g., "Sunday")

            // Increment the activity count for the specific hour and day
            if (isset($activityData[$weekday][$hour])) {
                $activityData[$weekday][$hour]++;
            }
        }

        // Flatten the data to match the desired output format
        $flattenedActivityData = [];
        foreach ($activityData as $weekday => $hours) {
            foreach ($hours as $hour => $value) {
                $formattedHour = Carbon::createFromFormat('H', $hour)->format('gA'); // Convert 24-hour format to 12-hour format
                $flattenedActivityData[] = [
                    'hour' => $formattedHour,
                    'weekday' => $weekday,
                    'value' => $value,
                ];
            }
        }

        $encodedflattenedActivityData = json_encode($flattenedActivityData);
        $logs = SystemLog::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        // Pass logs and activity data to the view
        return view('auth.profile', compact('logs', 'user', 'encodedflattenedActivityData'));
    }

    public function numberverificator(Request $request)
    {
        $phonumber = $request->phonumber;

        // Menghilangkan tanda '+' jika ada di depan nomor telepon
        if (strlen($phonumber) < 15) {
            // Menghilangkan tanda '+' jika ada di depan nomor telepon
            if (substr($phonumber, 0, 1) === '+') {
                $phonumber = substr($phonumber, 1);
            }

            // Mengganti awalan 62 dengan 0 jika nomor telepon diawali dengan 62
            if (substr($phonumber, 0, 2) === '62') {
                $phonumberFirstZero = '0' . substr($phonumber, 2);
            } else {
                $phonumberFirstZero = $phonumber;
            }

            // Cek nomor telepon dengan awalan 62 dan juga dengan awalan 0
            $user = User::where('waphonenumber', $phonumber)
                ->orWhere('waphonenumber', $phonumberFirstZero)
                ->first();

            if ($user) {
                $verificator = 'yes';
            } else {
                $verificator = 'no';
            }
        } else {
            // Mengecek di tabel Wagroupnumber untuk nomor telepon yang ada dan terverifikasi
            $wagroup = Wagroupnumber::where('number', $phonumber)
                ->where('isverified', true)
                ->first();

            if ($wagroup) {
                $verificator = 'yes';
            } else {
                $verificator = 'no';
            }
        }





        return response()->json(['verificator' => $verificator, 'name' => $user->name ?? "Unknown"]);
    }


    public function updatettd(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('file')) {
            // Handle the file upload as before
            $uploadedFile = $request->file('file');
            $filename = $uploadedFile->getClientOriginalName();
            $fileFormat = $uploadedFile->getClientOriginalExtension();
            $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);

            // Add username to the filename to avoid conflicts
            $userName = auth()->user()->name;
            $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $userName . '.' . $fileFormat;

            // Ensure the filename is unique
            $count = 0;
            $newFilename = $filenameWithUserAndFormat;

            while (CollectFile::where('filename', $newFilename)->exists()) {
                $count++;
                $newFilename = $filenameWithoutExtension . '_' . $userName . '_' . $count . '.' . $fileFormat;
            }

            // Store the file
            $path = $uploadedFile->storeAs('public/uploads', $newFilename);

            // Save file information to the database
            $collectFile = new CollectFile();
            $collectFile->filename = $newFilename;
            $collectFile->link = str_replace('public/', '', $path);
            $collectFile->collectable_id = auth()->user()->id;
            $collectFile->collectable_type = User::class;
            $collectFile->save();

            return response()->json([
                'message' => 'File successfully uploaded and saved.',
                'filename' => $newFilename,
                'path' => $path,
            ], 200);
        }

        return response()->json(['message' => 'No file uploaded.'], 400);
    }
}
