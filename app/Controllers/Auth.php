<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form']);
    }

    public function login()
    {
        if ($this->request->getMethod() == 'post') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]',
                'password' => 'required|min_length[3]|max_length[50]'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('validation', $this->validator);
            }

            $username = $this->request->getVar('username');
            $password = $this->request->getVar('password');

            $user = $this->userModel->getUserByUsername($username);

            if ($user && password_verify($password, $user['password'])) {
                $data = [
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'role_id' => $user['role_id'],
                    'logged_in' => true,
                    'title' => 'Login Page',
                ];

                session()->set($data);
                return redirect()->to('/dashboard');
            } else {
                session()->setFlashdata('error', 'Username atau password salah.');
                return redirect()->back()->withInput();
            }
        }

        return view('auth/login');
    }


    

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
