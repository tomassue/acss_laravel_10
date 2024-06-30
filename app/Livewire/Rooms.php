<?php

namespace App\Livewire;

use App\Models\RoomsModel;
use Livewire\Component;

class Rooms extends Component
{
    public $name, $is_active; # wire:model
    public $search;
    public $editMode = false;
    public $key; // ID that holds the room's ID.

    public function rules()
    {
        return [
            'name' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The room field is required.'
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name
        ];

        // Check if room's name already exist
        $check_room = RoomsModel::where('name', $this->name)->count();
        if ($check_room >= 1) {
            $this->dispatch('duplicate-room-name-error');
            $this->clear();
        } else {
            $query = RoomsModel::query();
            $query->create($data);
            $this->clear();
            $this->dispatch('success-toast-message');
        }
    }

    public function edit(RoomsModel $key)
    {
        $this->editMode = true;
        $this->key = $key->id;
        $this->name = $key->name;
        $this->is_active = $key->is_active;
    }

    public function update()
    {
        $this->validate();
        $data = [
            'name' => $this->name,
            'is_active' => $this->is_active
        ];
        $query = RoomsModel::query();
        $query->findOrFail($this->key);
        $query->update($data);
        $this->clear();
        $this->dispatch('success-toast-message');
    }

    public function loadRooms()
    {
        $query = RoomsModel::select(
            'id',
            'name',
            'is_active'
        )
            ->where('name', 'like', '%' . $this->search . '%')
            ->get();

        return $query;
    }

    public function clear()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function render()
    {
        $data = [
            'rooms' => $this->loadRooms()
        ];

        return view('livewire.rooms', $data);
    }
}
