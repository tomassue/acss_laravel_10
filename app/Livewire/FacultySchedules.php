<?php

namespace App\Livewire;

use App\Models\AppointmentsModel;
use App\Models\CourseModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FacultySchedules extends Component
{
    public $instructor; // wire:model (select instructor)
    public $selectedInstructor, $selectedCourse; // appointment modal

    public function rules()
    {
        return [
            'selectedInstructor' => 'required',
            'selectedCourse' => 'required'
        ];
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => $this->selectedInstructor,
            'course_id' => $this->selectedCourse
        ];

        $query = AppointmentsModel::query();
        $query->create($data);
        $this->clear();
        $this->dispatch('hide-appointFacultyModal');
        $this->dispatch('reset-virtual-selects'); // Emit event to reset Virtual Select dropdowns
        $this->dispatch('success-toast-message');
        // $this->redirect('faculty-schedules');
        //! The course dropdown won't update. Find a way where it doesn't have to redirect to be updated.
    }

    public function clear()
    {
        $this->reset('selectedInstructor', 'selectedCourse');
        $this->resetValidation();
        $this->dispatch('reset-virtual-selects'); // Emit event to reset Virtual Select dropdowns
    }

    public function loadAppointments() // Edit Schedule card
    {
        $appointments = AppointmentsModel::join('courses', 'courses.id', '=', 'course_id')
            ->join('rooms', 'rooms.id', '=', 'courses.room_id')
            ->select(
                'courses.id AS course_id',
                'courses.subject AS course_subject',
                'rooms.name AS room_name',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
            )
            ->where('appointments.user_id', $this->instructor)
            ->get();

        return $appointments;
    }

    public function loadCourses()
    {
        $courses = CourseModel::join('rooms', 'rooms.id', '=', 'courses.room_id')
            // ->join('appointments', 'appointments.course_id', '=', 'courses.id')
            ->select(
                'courses.id AS course_id',
                'courses.subject',
                'courses.room_id',
                'courses.day',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
                'courses.block',
                'courses.year',
                'courses.semester',
                'rooms.name AS room_name'
            )
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('appointments')
                    ->whereRaw('courses.id = appointments.course_id');
            })
            ->get()
            ->map(function ($item) {
                // Determine the suffix for the year
                $year = $item->year;
                $yearSuffix = ($year == 1) ? 'st year' : (($year == 2) ? 'nd year' : 'rd year');

                // Example of using decoded array
                $daysString = implode(', ', $item->day); // Convert array to comma-separated string

                // Build the description string with conditional suffix
                $description = 'Room: ' . $item->room_name . ' | ' .
                    'Block: ' . $item->block . ' <br> ' .
                    'Time: ' . $item->time_block . '<br>' .
                    'Days: ' . $daysString . '<br>' .
                    'Year: ' . $year . $yearSuffix . '<br>' .
                    'Semester: ' . $item->semester . ' semester';

                return [
                    'label' => $item->subject,
                    'value' => $item->course_id,
                    'description' => $description,
                ];
            });

        return $courses;
    }

    public function loadFaculties()
    {
        $instructors = User::where('role', 'Instructor')
            ->select(
                'id AS instructor_id',
                'name AS instructor_name',
            )
            ->get();

        $select_instructors = User::where('role', 'Instructor')
            ->select(
                'id AS select_instructor_id',
                'name AS select_instructor_name',
            )
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->select_instructor_name,
                    'value' => $item->select_instructor_id
                ];
            });

        return [
            'instructors' => $instructors,
            'select_instructors' => $select_instructors
        ];
    }

    public function render()
    {
        // Combined Queries: In loadFaculties, the instructors query is only run once, and the mapping for select_instructors is done on the same collection.
        // Data Passing in render: Adjusted how loadFaculties is used to avoid multiple calls and ensure data is structured correctly.
        $faculties = $this->loadFaculties();

        $data = [
            'instructors' => $faculties['instructors'],
            'select_instructors' => $faculties['select_instructors'],
            'courses' => $this->loadCourses(),
            'appointments' => $this->loadAppointments()
        ];

        return view('livewire.faculty-schedules', $data);
    }
}
