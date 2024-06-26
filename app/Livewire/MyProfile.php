<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MyProfile extends Component
{
    public $name, $email, $password, $confirmPassword;

    public function mount()
    {
        $this->loadFields();
    }

    public function loadFields()
    {
        $query = User::where('id', Auth::user()->id)->firstOrFail();
        $this->name = $query->name;
        $this->email = $query->email;
    }

    public function update()
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email:rfc,dns',
            'password' => 'required|current_password',
            'confirmPassword' => 'required|same:password',
        ];

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password)
        ];
        $this->validate($rules);

        $query = User::findOrFail(Auth::user()->id);
        $query->update($data);

        $this->reset(['password', 'confirmPassword']);
        $this->loadFields();
        $this->dispatch('success-toast-message');
    }

    public function render()
    {
        return view('livewire.my-profile');
    }
}
