<?php

namespace App\Livewire;

use App\Models\CourseModel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Guest extends Component
{
    public $course_type, $year, $semester, $block;

    public function loadCourses()
    {
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
            ->where('courses.type', $this->course_type)
            ->where('courses.year', $this->year)
            ->where('courses.semester', $this->semester)
            ->where('courses.block', $this->block)
            ->orderBy('courses.time_end', 'ASC')
            ->get();

        return $courses;
    }

    public function loadCourseTypes()
    {
        $course_types = CourseModel::select('type')->distinct()->get();

        return $course_types;
    }

    public function loadBlocks()
    {
        $blocks = CourseModel::select('block')->distinct()->get();

        return $blocks;
    }

    public function render()
    {
        $data = [
            'courses' => $this->loadCourses(),
            'course_types' => $this->loadCourseTypes(),
            'blocks' => $this->loadBlocks()
        ];

        return view('livewire.guest', $data);
    }
}
