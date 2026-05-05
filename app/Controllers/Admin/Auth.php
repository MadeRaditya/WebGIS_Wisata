<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('is_admin_logged_in')) {
            return redirect()->to('admin/dashboard');
        }
        return view('admin/login');
    }

    public function loginProcess()
    {
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $supabase = new \App\Libraries\SupabaseClient();
        
        // Memeriksa tabel admin di Supabase berdasarkan email
        $adminData = $supabase->from('admin')
                              ->eq('email', $email)
                              ->limit(1)
                              ->get();

        if (!empty($adminData) && isset($adminData[0])) {
            $admin = $adminData[0];
            
            // Verifikasi password (asumsi menggunakan hash, atau plaintext untuk sementara jika belum di-hash)
            // Disarankan menggunakan password_verify($password, $admin['password']) jika sudah di-hash bcrypt
            if (password_verify($password, $admin['password']) || $password === $admin['password']) {
                session()->set([
                    'is_admin_logged_in' => true,
                    'admin_email' => $admin['email'],
                    'admin_name' => $admin['nama'] ?? 'Admin'
                ]);
                return redirect()->to('admin/dashboard');
            }
        }

        return redirect()->back()->with('error', 'Email atau password salah, atau tabel admin belum ada.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('admin/login');
    }
}
