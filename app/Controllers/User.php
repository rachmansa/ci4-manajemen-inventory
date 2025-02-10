<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class User extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        helper(['form']);
    }

    public function index()
    {
        $data['users'] = $this->userModel->findAll();
        return view('user/index', $data);
    }

    public function create()
    {
        if ($this->request->getMethod() == 'post') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username]',
                'password' => 'required|min_length[3]|max_length[50]',
                'email' => 'required|min_length[6]|max_length[100]|valid_email|is_unique[users.email]',
                'role_id' => 'required'
            ];

            if (!$this->validate($rules)) {
                return view('user/create', [
                    'validation' => $this->validator
                ]);
            } else {
                $data = [
                    'username' => $this->request->getVar('username'),
                    'password' => password_hash($this->request->getVar('password'), PASSWORD_BCRYPT),
                    'email' => $this->request->getVar('email'),
                    'role_id' => $this->request->getVar('role_id')
                ];

                $this->userModel->save($data);
                session()->setFlashdata('success', 'User berhasil ditambahkan.');
                return redirect()->to('/user');
            }
        }

        return view('user/create');
    }

    public function edit($id)
    {
        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User tidak ditemukan');
        }

        if ($this->request->getMethod() == 'post') {
            $rules = [
                'username' => 'required|min_length[3]|max_length[50]|is_unique[users.username,id,' . $id . ']',
                'email' => 'required|min_length[6]|max_length[100]|valid_email|is_unique[users.email,id,' . $id . ']',
                'role_id' => 'required'
            ];

            if (!$this->validate($rules)) {
                return view('user/edit', [
                    'validation' => $this->validator,
                    'user' => $user
                ]);
            } else {
                $data = [
                    'id' => $id,
                    'username' => $this->request->getVar('username'),
                    'email' => $this->request->getVar('email'),
                    'role_id' => $this->request->getVar('role_id')
                ];

                if ($this->request->getVar('password')) {
                    $data['password'] = password_hash($this->request->getVar('password'), PASSWORD_BCRYPT);
                }

                $this->userModel->save($data);
                session()->setFlashdata('success', 'User berhasil diperbarui.');
                return redirect()->to('/user');
            }
        }

        return view('user/edit', ['user' => $user]);
    }

    public function delete($id)
    {
        $this->userModel->delete($id);
        session()->setFlashdata('success', 'User berhasil dihapus.');
        return redirect()->to('/user');
    }
}
