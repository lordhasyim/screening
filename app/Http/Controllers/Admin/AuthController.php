<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Redirect if already logged in
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'nip' => 'required|string',
            'password' => 'required|string',
        ], [
            'email.required' => 'email wajib diisi',
            'nip.required' => 'NIP/Kode wajib diisi',
            'password.required' => 'Password wajib diisi',
        ]);

        // Check credentials
        $credentials = [
            'email' => $request->email,
            'nip' => $request->nip,
            'password' => $request->password,
        ];

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Log successful login
            $admin = Auth::guard('admin')->user();
            $this->logAdminActivity($admin->id, 'login', 'Admin berhasil login ke sistem');

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang, '.$admin->name);
        }

        // Log failed login attempt
        $this->logFailedLogin($request->email, $request->nip, $request->ip());
        // $this->logFailedLogin($request->email, $request->nip ?? null, $request->ip());

        throw ValidationException::withMessages([
            'email' => 'email, NIP, atau password tidak valid.',
        ]);
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin) {
            $this->logAdminActivity($admin->id, 'logout', 'Admin logout dari sistem');
        }

        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('success', 'Anda telah berhasil logout');
    }

    private function logAdminActivity($adminId, $action, $description)
    {
        // Simple activity logging
        DB::table('admin_logs')->insert([
            'admin_user_id' => $adminId,
            'action' => $action,
            'description' => $description,
            'metadata' => json_encode([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function logFailedLogin($email, $nip, $ip)
    {
        DB::table('admin_logs')->insert([
            'admin_user_id' => null,
            'action' => 'failed_login',
            'description' => "Login gagal untuk email: {$email}, NIP: {$nip}",
            'metadata' => json_encode([
                'email' => $email,
                'nip' => $nip,
                'ip' => $ip,
                'user_agent' => request()->userAgent(),
                'timestamp' => now(),
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
