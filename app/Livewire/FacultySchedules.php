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

class FacultySchedules extends Component
{
    public $archive_mode = false; // This will change the view of the page. Showing all archived schedules of a selected student.
    public $semester, $year;
    public $instructor; // wire:model (select instructor)
    public $selectedInstructor, $selectedCourse; // appointment modal

    public function rules()
    {
        return [
            'selectedInstructor' => 'required',
            'selectedCourse' => 'required'
        ];
    }

    public function updated($property)
    {
        if ($property == 'selectedInstructor') {
            $refreshed_courses = $this->loadCourses();
            $this->dispatch('refresh-course-select', $refreshed_courses);
        }
    }

    #[On('exportExcel')]
    public function exportToExcel()
    {
        $appointments = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
            ->join('rooms', 'rooms.id', '=', 'courses.room_id')
            ->join('users', 'users.id', '=', 'appointments.user_id')
            ->select(
                'courses.subject AS course_subject',
                'courses.day AS courses_day',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
                'rooms.name AS room_name',
                // DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(users.name, ' ', 2), ' ', -1) AS users_last_name")
            )
            ->where('appointments.user_id', $this->instructor)
            ->where('status', '1')
            ->orderBy('courses.time_end', 'ASC')
            ->get();

        $header_style = (new Style())->setFontBold();

        $rows_style = (new Style())
            ->setFontSize(8)
            ->setShouldWrapText()
            ->setBackgroundColor("EDEDED");

        return (new FastExcel($appointments))->headerStyle($header_style)
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

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => $this->selectedInstructor,
            'course_id' => $this->selectedCourse
        ];

        // Retrieve the selected course details
        $selectedCourse = CourseModel::where('id', $this->selectedCourse)
            ->select('day', 'time_start', 'time_end')
            ->first();

        if (!$selectedCourse) {
            $this->dispatch('alert-something-went-wrong');
        }

        // Decode the days array
        $selectedDays = $selectedCourse->day;

        // Check for overlapping appointments
        $overlappingAppointments = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
            ->where('appointments.status', '1')
            ->where('appointments.user_id', $this->selectedInstructor)
            ->where(function ($query) use ($selectedCourse, $selectedDays) {
                $query->where(function ($query) use ($selectedCourse, $selectedDays) {
                    foreach ($selectedDays as $day) {
                        $query->orWhereJsonContains('courses.day', $day);
                    }
                })
                    ->where(function ($query) use ($selectedCourse) {
                        $query->whereBetween('courses.time_start', [$selectedCourse->time_start, $selectedCourse->time_end])
                            ->orWhereBetween('courses.time_end', [$selectedCourse->time_start, $selectedCourse->time_end])
                            ->orWhere(function ($query) use ($selectedCourse) {
                                $query->where('courses.time_start', '<=', $selectedCourse->time_start)
                                    ->where('courses.time_end', '>=', $selectedCourse->time_end);
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
            $this->reset('selectedInstructor', 'selectedCourse');
            $this->dispatch('hide-appointFacultyModal');
            $this->dispatch('reset-virtual-selects'); // Emit event to reset Virtual Select dropdowns
            $this->dispatch('success-toast-message');
            // $this->redirect('faculty-schedules');
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

    #[On('archive-all-appointments')]
    public function archiveAllAppointments()
    {
        if ($this->instructor) {
            $data = [
                'status' => '0'
            ];

            $query = AppointmentsModel::query();
            $query->where('user_id', $this->instructor);
            $query->update($data);
            $this->dispatch('success-toast-message');
        }
    }

    public function clear()
    {
        $this->reset();
        $this->resetValidation();
        $this->dispatch('reset-virtual-selects'); // Emit event to reset Virtual Select dropdowns
    }

    public function loadAppointments() // Edit Schedule card and Time Blocks card
    {
        $appointments = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
            ->join('rooms', 'rooms.id', '=', 'courses.room_id')
            ->join('users', 'users.id', '=', 'appointments.user_id')
            ->select(
                'appointments.id AS appointments_id',
                'courses.id AS course_id',
                'courses.subject AS course_subject',
                'rooms.name AS room_name',
                'courses.day AS courses_day',
                'courses.block',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(users.name, ' ', 2), ' ', -1) AS users_last_name")
            )
            ->where('appointments.user_id', $this->instructor)
            ->where('status', '1')
            ->orderBy('courses.time_end', 'ASC')
            ->get();

        return $appointments;
    }

    public function loadArchivedAppointments() // For archive mode
    {
        $archived_appointments = AppointmentsModel::join('courses', 'courses.id', '=', 'appointments.course_id')
            ->join('rooms', 'rooms.id', '=', 'courses.room_id')
            ->join('users', 'users.id', '=', 'appointments.user_id')
            ->select(
                'appointments.id AS appointments_id',
                'courses.id AS course_id',
                'courses.subject AS course_subject',
                'rooms.name AS room_name',
                'courses.day AS courses_day',
                'courses.block',
                DB::raw("CONCAT(DATE_FORMAT(courses.time_start, '%h:%i%p'), ' - ', DATE_FORMAT(courses.time_end, '%h:%i%p')) AS time_block"),
                DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(users.name, ' ', 2), ' ', -1) AS users_last_name")
            )
            ->where('appointments.user_id', $this->instructor)
            ->where('courses.semester', '=', $this->semester)
            ->whereYear('courses.created_at', '=', $this->year)
            ->where('status', '0')
            ->orderBy('courses.time_end', 'ASC')
            ->get(); // Make sure to get the result here

        return $archived_appointments;
    }

    public function loadYear() // for the filters. This is based what year the appointment was processed.
    {
        $select_year = DB::table('appointments')
            ->select(DB::raw('YEAR(created_at) as year'))
            ->distinct()
            ->orderBy('year', 'desc') // Optional: Order by year descending
            ->get();

        return $select_year;
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
            ->where('courses.is_active', '1')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('appointments')
                    ->join('users', 'users.id', '=', 'appointments.user_id')
                    ->whereColumn('courses.id', 'appointments.course_id') // Ensure the courses are related
                    ->whereRaw('courses.id = appointments.course_id')
                    ->where('users.role', 'Instructor')
                    ->whereRaw('appointments.status = 1');
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
                    'Semester: ' . $item->semester;

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
            ->where('is_active', '1')
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
            'appointments' => $this->loadAppointments(),
            'select_year' => $this->loadYear(),
            'archived_appointments' => $this->loadArchivedAppointments()
        ];

        return view('livewire.faculty-schedules', $data);
    }
}
