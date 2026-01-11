<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserManager extends Component
{
    use WithPagination;

    public $name, $email, $password, $role;
    public $userId;
    public $isModalOpen = false;
    public $confirmingUserDeletion = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'role' => 'required|exists:roles,name',
    ];

    public function render()
    {
        $users = User::where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        $roles = Role::all();

        return view('livewire.user-manager', [
            'users' => $users,
            'roles' => $roles,
        ])->layout('layouts.app');
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetInputFields();
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->role = '';
        $this->userId = null;
    }

    public function store()
    {
        // Adjust validation for update
        $rules = $this->rules;
        if ($this->userId) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->userId;
            $rules['password'] = 'nullable|min:8'; // Password optional on edit
        }
        
        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::updateOrCreate(['id' => $this->userId], $data);

        // Sync Roles
        $user->syncRoles($this->role);

        session()->flash('message', $this->userId ? 'Usuario actualizado exitosamente.' : 'Usuario creado exitosamente.');

        $this->closeModal();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = ''; // Don't show password
        $this->role = $user->roles->first()->name ?? '';
    
        $this->openModal();
    }

    public function confirmDeletion($id)
    {
        $this->confirmingUserDeletion = $id;
    }

    public function delete()
    {
        if ($this->confirmingUserDeletion == auth()->id()) {
             session()->flash('error', 'No puedes eliminar tu propia cuenta.');
             $this->confirmingUserDeletion = false;
             return;
        }

        $user = User::find($this->confirmingUserDeletion);
        if ($user) {
            $user->delete();
            session()->flash('message', 'Usuario eliminado exitosamente.');
        }
        $this->confirmingUserDeletion = false;
    }
}
