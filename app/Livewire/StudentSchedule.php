<?php

namespace App\Livewire;

use Rap2hpoutre\FastExcel\FastExcel;
use OpenSpout\Common\Entity\Style\Style;
use App\Models\AppointmentsModel;
use App\Models\CourseModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class StudentSchedule extends Component
{
    public $student; // wire:model
    public $selectedStudent, $selectedSubject; // setScheduleStudentModal

    public function rules()
    {
        return [
            'selectedStudent' => 'required',
            'selectedSubject' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'selectedStudent.required' => 'The student field is required.',
            'selectedSubject.required' => 'The subject field is required.'
        ];
    }

    #[On('exportExcel')]
    public function exportExcel()
    {
        $schedules = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
            ->join('rooms', 'rooms.id', '=', 'courses.room_id')
            ->join('users', 'users.id', '=', 'appointments.user_id')
            ->select(
                'courses.subject AS course_subject',
                'courses.day AS courses_day',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
                'rooms.name AS room_name',
            )
            ->where('appointments.user_id', $this->student)
            ->where('status', '1')
            ->orderBy('courses.time_end', 'ASC')
            ->get();

        $header_style = (new Style())->setFontBold();

        $rows_style = (new Style())
            ->setFontSize(8)
            ->setShouldWrapText()
            ->setBackgroundColor("EDEDED");

        return (new FastExcel($schedules))->headerStyle($header_style)
            ->rowsStyle($rows_style)
            ->download('file.xlsx', function ($item) {
                // Decode the JSON string
                $days = json_decode($item->courses_day, true);

                // If decoding failed, fallback to original string
                $days = is_array($days) ? implode(', ', $days) : $item->courses_day;

                return [
                    'Subject' => $item->course_subject,
                    'Day' => $days,
                    'Time' => $item->time_block,
                    'Room' => $item->room_name
                ];
            });
    }

    #[On('archive-all-appointments')]
    public function archiveAllAppointments()
    {
        if ($this->student) {
            $data = [
                'status' => '0'
            ];

            $query = AppointmentsModel::query();
            $query->where('user_id', $this->student);
            $query->update($data);
            $this->dispatch('success-toast-message');
        }
    }

    #[On('archive-appointment')]
    public function archiveAppointment(AppointmentsModel $appointments_id)
    {
        $data = [
            'status' => '0'
        ];

        $appointments_id->update($data);
        $this->dispatch('success-toast-message');
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => $this->selectedStudent,
            'course_id' => $this->selectedSubject
        ];

        // Retrieve the selected course details
        $selectedSubject = CourseModel::where('id', $this->selectedSubject)
            ->select('day', 'time_start', 'time_end')
            ->first();

        if (!$selectedSubject) {
            $this->dispatch('alert-something-went-wrong');
        }

        // Decode the days array
        $selectedDays = $selectedSubject->day;

        // Check for overlapping appointments
        $overlappingAppointments = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
            ->where('appointments.status', '1')
            ->where('appointments.user_id', $this->selectedStudent)
            ->where(function ($query) use ($selectedSubject, $selectedDays) {
                $query->where(function ($query) use ($selectedSubject, $selectedDays) {
                    foreach ($selectedDays as $day) {
                        $query->orWhereJsonContains('courses.day', $day);
                    }
                })
                    ->where(function ($query) use ($selectedSubject) {
                        $query->whereBetween('courses.time_start', [$selectedSubject->time_start, $selectedSubject->time_end])
                            ->orWhereBetween('courses.time_end', [$selectedSubject->time_start, $selectedSubject->time_end])
                            ->orWhere(function ($query) use ($selectedSubject) {
                                $query->where('courses.time_start', '<=', $selectedSubject->time_start)
                                    ->where('courses.time_end', '>=', $selectedSubject->time_end);
                            });
                    });
            })
            ->exists();

        if ($overlappingAppointments) {
            $this->dispatch('alert-overlapping-schedule');
        } else {
            // Save the appointment
            $query = AppointmentsModel::query();
            $query->create($data);
            $this->reset('selectedStudent', 'selectedSubject');
            $this->dispatch('hide-setScheduleStudentModal');
            $this->dispatch('reset-virtual-selects'); // Emit event to reset Virtual Select dropdowns
            $this->dispatch('success-toast-message');
            // $this->redirect('faculty-schedules');
        }

        // $query = AppointmentsModel::query();
        // $query->create($data);
        // $this->reset('selectedStudent', 'selectedSubject');
        // $this->dispatch('hide-setScheduleStudentModal');
        // $this->dispatch('reset-virtual-selects'); // Emit event to reset Virtual Select dropdowns
        // $this->dispatch('success-toast-message');
        // // $this->redirect('student-schedules');
    }

    public function updated($property)
    {
        if ($property == 'selectedStudent') {
            $refreshed_courses = $this->loadCourses();
            $this->dispatch('refresh-subject-select', $refreshed_courses);
        }
    }

    public function loadSchedules() // Edit Schedule card and Time Blocks card
    {
        $schedules = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
            ->join('rooms', 'rooms.id', '=', 'courses.room_id')
            ->join('users', 'users.id', '=', 'appointments.user_id')
            ->select(
                'appointments.id AS appointments_id',
                'courses.id AS course_id',
                'courses.subject AS course_subject',
                'rooms.name AS room_name',
                'courses.day AS courses_day',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(users.name, ' ', 2), ' ', -1) AS users_last_name")
            )
            ->where('appointments.user_id', $this->student)
            ->where('status', '1')
            ->orderBy('courses.time_end', 'ASC')
            ->get();

        return $schedules;
    }

    public function loadStudents()
    {
        $students = User::where('role', 'Student')
            ->select(
                'id AS student_id',
                'name AS student_name'
            )
            ->get();

        $select_students = User::where('role', 'Student')
            ->select(
                'id AS student_id',
                'name AS student_name'
            )
            ->get()
            ->map(function ($item) {
                return [
                    'label' => $item->student_name,
                    'value' => $item->student_id
                ];
            });

        return [
            'students' => $students,
            'select_students' => $select_students
        ];
    }

    public function loadCourses()
    {
        // $courses = CourseModel::join('rooms', 'rooms.id', '=', 'courses.room_id')
        //     ->select(
        //         'courses.id AS course_id',
        //         'courses.subject',
        //         'courses.room_id',
        //         'courses.day',
        //         DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
        //         'courses.block',
        //         'courses.year',
        //         'courses.semester',
        //         'rooms.name AS room_name'
        //     )
        //     ->whereNotExists(function ($query) {
        //         $query->select(DB::raw(1))
        //             ->from('appointments')
        //             ->join('users', 'users.id', '=', 'appointments.user_id')
        //             ->whereColumn('courses.id', 'appointments.course_id') // Ensure the courses are related
        //             ->where('appointments.user_id', $this->selectedStudent) // Filter by selected student
        //             // ->where('courses.id', 'appointments.course_id')
        //             ->where('appointments.status', 1);
        //     })
        //     ->get()
        //     ->map(function ($item) {
        //         // Determine the suffix for the year
        //         $year = $item->year;
        //         $yearSuffix = ($year == 1) ? 'st year' : (($year == 2) ? 'nd year' : 'rd year');

        //         // Example of using decoded array
        //         $daysString = implode(', ', $item->day); // Convert array to comma-separated string

        //         // Build the description string with conditional suffix
        //         $description = 'Room: ' . $item->room_name . ' | ' .
        //             'Block: ' . $item->block . ' <br> ' .
        //             'Time: ' . $item->time_block . '<br>' .
        //             'Days: ' . $daysString . '<br>' .
        //             'Year: ' . $year . $yearSuffix . '<br>' .
        //             'Semester: ' . $item->semester . ' semester';

        //         return [
        //             'label' => $item->subject,
        //             'value' => $item->course_id,
        //             'description' => $description,
        //         ];
        //     });

        $courses = CourseModel::join('rooms', 'rooms.id', '=', 'courses.room_id')
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
            ->where('courses.is_active', '1')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('appointments')
                    ->join('users', 'users.id', '=', 'appointments.user_id')
                    ->whereColumn('appointments.course_id', 'courses.id')
                    ->where('users.role', 'Instructor')
                    ->where('appointments.status', 1);
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('appointments')
                    ->whereColumn('appointments.course_id', 'courses.id')
                    ->where('appointments.user_id', $this->selectedStudent)
                    ->where('appointments.status', 1);
            })
            ->get()
            ->map(function ($item) {
                // Determine the suffix for the year
                $year = $item->year;
                $yearSuffix = match ($year) {
                    1 => 'st year',
                    2 => 'nd year',
                    3 => 'rd year',
                    4 => 'th year',
                    default => ''
                };

                // Example of using decoded array
                $daysString = implode(', ', $item->day); // Convert array to comma-separated string

                // Build the description string with conditional suffix
                $description = 'Room: ' . $item->room_name . ' | ' .
                    'Block: ' . $item->block . ' <br> ' .
                    'Time: ' . $item->time_block . '<br>' .
                    'Days: ' . $daysString . '<br>' .
                    'Year: ' . $year . ' ' . $yearSuffix . '<br>' .
                    'Semester: ' . $item->semester . ' semester';

                return [
                    'label' => $item->subject,
                    'value' => $item->course_id,
                    'description' => $description,
                ];
            });

        return $courses;
    }

    public function clear()
    {
        $this->reset('selectedStudent', 'selectedSubject');
        $this->resetValidation();
    }

    public function render()
    {
        $students = $this->loadStudents();

        $data = [
            'students' => $students['students'],
            'select_students' => $students['select_students'],
            'subjects' => $this->loadCourses(),
            'schedules' => $this->loadSchedules()
        ];

        return view('livewire.student-schedule', $data);
    }
}
