<?php

namespace App\Livewire;

use App\Models\CourseModel;
use App\Models\RefDays;
use App\Models\RoomsModel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Course extends Component
{
    public $type, $subject, $room_id, $day, $time_start, $time_end, $block, $year, $semester; // wire:model
    public $selectedRoom, $selectedDays = [];

    public function rules()
    {
        return [
            'type' => 'required',
            'subject' => 'required',
            'selectedRoom' => 'required',
            'selectedDays' => 'required',
            'time_start' => 'required',
            'time_end' => 'required',
            'block' => 'required',
            'year' => 'required',
            'semester' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'selectedRoom.required' => 'The room field is required.',
            'selectedDays.required' => 'The days filed is required.'
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'type' => $this->type,
            'subject' => $this->subject,
            'room_id' => $this->selectedRoom,
            'day' => $this->selectedDays,
            'time_start' => $this->time_start,
            'time_end' => $this->time_end,
            'block' => $this->block,
            'year' => $this->year,
            'semester' => $this->semester
        ];

        // Check if the room is already occupied during the specified time and days
        $occupied_rooms = CourseModel::where('room_id', $this->selectedRoom)
            ->where(function ($query) {
                // Check if any day matches with selected days
                foreach ($this->selectedDays as $day) {
                    $query->orWhereJsonContains('day', $day);
                }
            })
            ->where(function ($query) {
                $query->where(function ($query) {
                    $query->whereBetween('time_start', [$this->time_start, $this->time_end])
                        ->orWhereBetween('time_end', [$this->time_start, $this->time_end]);
                })
                    ->orWhere(function ($query) {
                        $query->where('time_start', '<=', $this->time_start)
                            ->where('time_end', '>=', $this->time_end);
                    });
            })
            ->exists();

        if ($occupied_rooms) {
            $this->addError('selectedDays', 'The selected room is already occupied during the specified day(s).');
            $this->addError('time_start', 'The selected room is already occupied during the specified and time.');
            $this->addError('time_end', 'The selected room is already occupied during the specified and time.');
            return;
        }

        $query = CourseModel::query();
        $query->create($data);
        $this->clear();
        $this->dispatch('reset-virtual-selects'); // Emit event to reset Virtual Select dropdowns
        $this->dispatch('success-toast-message');
    }



    public function clear()
    {
        $this->reset();
        $this->resetValidation();
    }

    public function loadRooms()
    {
        $room = RoomsModel::select(
            'id',
            'name'
        )
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->name,
                    'value' => $item->id
                ];
            });
        return $room;
    }

    public function loadDays()
    {
        $days = RefDays::select(
            'id',
            'day_name'
        )
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->day_name,
                    'value' => $item->id
                ];
            });
        return $days;
    }

    public function loadCourses()
    {
        $courses = CourseModel::join('rooms', 'rooms.id', '=', 'courses.room_id')
            ->select(
                'courses.type',
                'courses.subject',
                'rooms.name as room_name',
                'courses.day',
                DB::raw("DATE_FORMAT(courses.time_start, '%h:%i%p') AS time_start"),
                DB::raw("DATE_FORMAT(courses.time_end, '%h:%i%p') AS time_end"),
                'courses.block',
                'courses.year',
                'courses.semester'
            )
            ->get();

        // Fetch all reference days
        $refDays = RefDays::all()->pluck('day_name', 'id')->toArray();

        // Define the order of days
        $dayOrder = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        // Map to refDays and sort them
        $courses->transform(function ($course) use ($refDays, $dayOrder) {
            $course->day_names = collect($course->day)
                ->map(function ($dayId) use ($refDays) {
                    return $refDays[$dayId] ?? $dayId;
                })
                ->sort(function ($a, $b) use ($dayOrder) {
                    return array_search($a, $dayOrder) - array_search($b, $dayOrder);
                })->values();
            return $course;
        });
        return $courses;
    }

    public function render()
    {
        $data = [
            'rooms' =>  $this->loadRooms(),
            'days' => $this->loadDays(),
            'courses' => $this->loadCourses()
        ];

        return view('livewire.course', $data);
    }
}
