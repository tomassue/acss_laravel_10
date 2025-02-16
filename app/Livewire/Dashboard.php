<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;
use App\Models\AppointmentsModel;
use App\Models\CourseModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public $semester, $year; // filter

    public function loadYear() // for the filters. This is based what year the appointment was processed.
    {
        $select_year = DB::table('appointments')
            ->select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'desc') // Optional: Order by year descending
            ->get();

        return $select_year;
    }

    public function loadRoomsAndSection()
    {
        $roomsAndSections = CourseModel::join('rooms', 'rooms.id', '=', 'courses.room_id')
            // ->join('appointments', 'appointments.course_id', '=', 'courses.id')
            ->select(
                'courses.id AS course_id',
                'courses.day',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
                'rooms.name AS room_name'
            )
            ->get();

        return $roomsAndSections;
    }

    public function loadStudents()
    {
        $students = User::where('role', 'Student')
            ->select(
                'name'
            )
            ->get();

        return $students;
    }

    public function loadInstructors()
    {
        $instructors = User::where('role', 'Instructor')
            ->select(
                'name'
            )
            ->get();

        return $instructors;
    }

    #[On('exportExcel')]
    public function exportExcel()
    {
        $subjects = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
            ->join('users', 'users.id', '=', 'appointments.user_id')
            ->join('rooms', 'rooms.id', '=', 'courses.room_id')
            ->select(
                'appointments.id AS appointments_id',
                'courses.id AS course_id',
                'courses.subject',
                'courses.room_id',
                'courses.day',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
                'courses.block',
                'courses.year',
                'courses.semester',
                'courses.created_at',
                'rooms.name AS room_name'
            )
            ->where('appointments.user_id', Auth::user()->id)
            ->where('appointments.status', '1')
            ->orderBy('courses.time_end', 'ASC');

        if (!empty($this->semester)) {
            $subjects->where('courses.semester', '=', $this->semester);
        }

        if (!empty($this->year)) {
            // $subjects->whereYear('courses.created_at', '=', $this->year);
            $subjects->where('courses.year', '=', $this->year);
        }

        $results_subject = $subjects->get();

        $header_style = (new Style())->setFontBold();

        $rows_style = (new Style())
            ->setFontSize(8)
            ->setShouldWrapText()
            ->setBackgroundColor("EDEDED");

        return (new FastExcel($results_subject))->headerStyle($header_style)
            ->rowsStyle($rows_style)
            ->download('file.xlsx', function ($item) {
                // Decode the JSON string
                $days = json_decode($item->day, true);

                // If decoding failed, fallback to original string
                $days = is_array($days) ? implode(', ', $days) : $item->day;

                return [
                    'Subject' => $item->subject,
                    'Day' => $days,
                    'Time' => $item->time_block,
                    'Room' => $item->room_name
                ];
            });
    }

    // public function updated($property)
    // {
    //     if ($property == 'semester' || $property == 'year') {
    //         $this->loadSubjectList();
    //     }
    // }

    // Dashboard for Students
    // public function loadSubjectList()
    // {
    //     // $subjects = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
    //     //     ->join('users', 'users.id', '=', 'appointments.user_id')
    //     //     ->join('rooms', 'rooms.id', '=', 'courses.room_id')
    //     //     ->select(
    //     //         'appointments.id AS appointments_id',
    //     //         'courses.id AS course_id',
    //     //         'courses.subject',
    //     //         'courses.room_id',
    //     //         'courses.day',
    //     //         DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
    //     //         'courses.block',
    //     //         'courses.year',
    //     //         'courses.semester',
    //     //         'courses.created_at',
    //     //         'rooms.name AS room_name'
    //     //     )
    //     //     ->where('appointments.user_id', Auth::user()->id)
    //     //     ->where(function ($query) {
    //     //         $query->where('courses.semester', '=', $this->semester)
    //     //             ->orWhereRaw('YEAR(courses.created_at) = ?', [$this->year]);
    //     //     })
    //     //     ->get();

    //     $subjects = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
    //         ->join('users', 'users.id', '=', 'appointments.user_id')
    //         ->join('rooms', 'rooms.id', '=', 'courses.room_id')
    //         ->select(
    //             'appointments.id AS appointments_id',
    //             'courses.id AS course_id',
    //             'courses.subject',
    //             'courses.room_id',
    //             'courses.day',
    //             DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
    //             'courses.block',
    //             'courses.year',
    //             'courses.semester',
    //             'courses.created_at',
    //             'rooms.name AS room_name'
    //         )
    //         ->where('appointments.user_id', Auth::user()->id);

    //     if (!empty($this->semester)) {
    //         $subjects->where('courses.semester', '=', $this->semester);
    //     }

    //     if (!empty($this->year)) {
    //         $subjects->whereYear('courses.created_at', '=', $this->year);
    //     }

    //     $results_subject = $subjects->get();


    //     return $results_subject;
    // }

    public function render()
    {
        $subjects = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
            ->join('users', 'users.id', '=', 'appointments.user_id')
            ->join('rooms', 'rooms.id', '=', 'courses.room_id')
            ->select(
                'appointments.id AS appointments_id',
                'courses.id AS course_id',
                'courses.subject',
                'courses.room_id',
                'courses.day',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
                'courses.block',
                'courses.year',
                'courses.semester',
                'courses.created_at',
                'rooms.name AS room_name'
            )
            ->where('appointments.user_id', Auth::user()->id)
            ->where('appointments.status', '1')
            ->orderBy('courses.time_end', 'ASC');

        if (!empty($this->semester)) {
            $subjects->where('courses.semester', '=', $this->semester);
        }

        if (!empty($this->year)) {
            // $subjects->whereYear('courses.created_at', '=', $this->year);
            $subjects->where('courses.year', '=', $this->year);
        }

        $results_subject = $subjects->get();

        $data = [
            'roomsAndSections' => $this->loadRoomsAndSection(),
            'students' => $this->loadStudents(),
            'instructors' => $this->loadInstructors(),
            // 'subjects' => $this->loadSubjectList(),
            'subjects' =>  $results_subject, // This is for the non-super admin display
            'select_year' => $this->loadYear()
        ];

        return view('livewire.dashboard', $data);
    }
}
