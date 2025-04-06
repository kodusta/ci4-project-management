<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function login()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $this->userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            $sessionData = [
                'user_id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'logged_in' => true
            ];
            session()->set($sessionData);
            return redirect()->to('/dashboard');
        }

        session()->setFlashdata('error', 'Kullanıcı adı veya şifre hatalı');
        return redirect()->back();
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
    
    /**
     * Test şifreleme fonksiyonu
     * Bu fonksiyon, admin şifresini hashleyip ekranda gösterir
     * Sadece geliştirme aşamasında kullanılmalıdır
     */
    public function testPassword()
    {
        // Admin şifresi
        $password = 'password';
        
        // Şifreyi hashle
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Sonuçları ekranda göster
        echo '<h2>Test Şifreleme Sonuçları</h2>';
        echo '<p>Orijinal Şifre: ' . $password . '</p>';
        echo '<p>Hash Edilmiş Şifre: ' . $hashedPassword . '</p>';
        echo '<p>Hash Doğrulama: ' . (password_verify($password, $hashedPassword) ? 'Başarılı' : 'Başarısız') . '</p>';
        
        // Veritabanındaki admin şifresi ile karşılaştır
        $admin = $this->userModel->where('username', 'admin')->first();
        if ($admin) {
            echo '<p>Veritabanındaki Admin Şifresi: ' . $admin['password'] . '</p>';
            echo '<p>Veritabanı Şifre Doğrulama: ' . (password_verify($password, $admin['password']) ? 'Başarılı' : 'Başarısız') . '</p>';
        } else {
            echo '<p>Veritabanında admin kullanıcısı bulunamadı.</p>';
        }
        
        // SQL sorgusu
        echo '<h3>SQL Sorgusu</h3>';
        echo '<pre>UPDATE users SET password = \'' . $hashedPassword . '\' WHERE username = \'admin\';</pre>';
    }
}
