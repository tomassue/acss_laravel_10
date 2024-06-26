<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class Users extends Component
{
    public $search;
    public $name, $id_no, $contact_no, $email, $password, $confirm_password, $role, $is_active; # wire:model
    public $editMode = false;
    public $key; // Holds the id of the user when updating.

    public function rules()
    {
        return [
            'name' => 'required',
            'id_no' => 'required',
            'contact_no' => 'required',
            'email' => 'required|email:rfc,dns',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
            'role' => 'required'
        ];
    }

    public function loadUsers()
    {
        $query = User::select(
            'id',
            'name',
            'id_no',
            'contact_no',
            'email',
            'role',
            'is_active'
        )
            ->whereNot('role', 'Super Admin')
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('id_no', 'like', '%' . $this->search . '%')
                    ->orWhere('contact_no', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->get();
        return $query;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'id_no' => $this->id_no,
            'contact_no' => $this->contact_no,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role
        ];
        $query = User::query();
        $query->create($data);
        $this->reset();
        // $this->loadUsers();
        $this->dispatch('success-toast-message');
    }

    public function edit(User $key)
    {
        $this->reset();
        $this->resetValidation();
        $this->editMode = true;
        $this->key = $key->id;
        $this->name = $key->name;
        $this->id_no = $key->id_no;
        $this->contact_no = $key->contact_no;
        $this->email = $key->email;
        $this->role = $key->role;
        $this->is_active = $key->is_active;
    }

    public function update()
    {
        $rules = [
            'name' => 'required',
            'id_no' => 'required',
            'contact_no' => 'required',
            'email' => 'required|email:rfc,dns',
            'role' => 'required',
            'is_active' => 'required',
        ];

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'id_no' => $this->id_no,
            'contact_no' => $this->contact_no,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active
        ];
        $query = User::findOrFail($this->key);
        $query->update($data);
        $this->reset();
        // $this->loadUsers();
        $this->dispatch('success-toast-message');
    }

    #[On('soft-delete-user')]
    public function softDelete(User $key)
    {
        $data = [
            'is_active' => 0
        ];

        $key->update($data);
        $this->reset();
        // $this->loadUsers();
        $this->dispatch('success-toast-message');
    }

    public function clear()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function render()
    {
        $data = [
            'users' => $this->loadUsers()
        ];

        return view('livewire.users', $data);
    }
}
