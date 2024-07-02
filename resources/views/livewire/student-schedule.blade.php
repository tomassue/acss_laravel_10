<main id="main" class="main">

    <div class="pagetitle">
        <h1>Student Schedule</h1>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">

            <div class="col-lg-12 py-3">
                <div class="d-grid gap-2 col-2 ms-auto">
                    <button type="button" class="btn btn-primary" wire:click="$dispatch('show-setScheduleStudentModal')">New</button>
                </div>
            </div>

            <div class="col-lg-12 py-3">

                <div class="d-grid gap-2 col-lg-6 mx-auto">
                    <div class="mb-3">
                        <select class="form-select form-select-lg" aria-label="Default select example" wire:model.live="student">
                            <option value="" selected>Select...</option>
                            @foreach ($students as $item)
                            <option value="{{ $item->student_id }}">{{ $item->student_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="col-lg-5">

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title">Edit Schedules</h5>
                            </div>
                            @if ($student)
                            <div class="col-md-6 d-flex justify-content-end align-items-center">
                                <a href="#" role="button" class="btn btn-danger" wire:click="$dispatch('confirm-archive-all-subjects')" wire:loading.remove>Archive All</a>
                            </div>
                            @endif
                        </div>

                        <div class="table-responsive">
                            <table class="table text-center">
                                <thead>
                                    <tr>
                                        <th scope="col">Subject</th>
                                        <th scope="col">Room</th>
                                        <th scope="col">Time</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($schedules as $item)
                                    <tr wire:key="{{ $item->appointments_id }}">
                                        <th scope="row">{{ $item->course_subject }}</th>
                                        <td>{{ $item->room_name }}</td>
                                        <td>{{ $item->time_block }}</td>
                                        <td>
                                            <a class="btn btn-danger" href="#" role="button" wire:click="$dispatch('confirm-archive-subject', { appointments_id: {{ $item->appointments_id }} })"><i class="bi bi-archive"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <th colspan="4">No data</th>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>

            </div>

            <div class="col-lg-7">

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title">Time Block</h5>
                            </div>
                            @if ($student)
                            <div class="col-md-6 d-flex justify-content-end align-items-center">
                                <a class="btn btn-info" role="button" aria-disabled="true" wire:click="$dispatch('confirm-exportExcel-student')"><i class="bi bi-filetype-csv"></i></a>
                            </div>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center" style="vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th scope="col" width="30%">Time</th>
                                        <th scope="col" width="23%">Mon Thur</th>
                                        <th scope="col" width="23%">Tue Fri</th>
                                        <th scope="col" width="23%">Wed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($schedules as $item)
                                    @php
                                    $days = json_decode($item->courses_day);
                                    @endphp
                                    <tr wire:key="{{ $item->appointments_id }}">
                                        @php
                                        $ins = App\Models\AppointmentsModel::where('course_id', $item->course_id)->where('status', '1')->pluck('user_id');
                                        $ins2 = App\Models\User::select(DB::raw("SUBSTRING_INDEX(SUBSTRING_INDEX(users.name, ' ', 2), ' ', -1) AS ins_last_name"))
                                        ->whereIn('id', $ins)->where('role', 'Instructor')->first();
                                        @endphp
                                        <th scope="row">{{ $item->time_block }}</th>
                                        <td>
                                            @if ($ins2)
                                            @if(in_array('Monday', $days) || in_array('Thursday', $days))
                                            {!!
                                            $item->course_subject . '<br>' .
                                            $ins2->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ins2)
                                            @if(in_array('Tuesday', $days) || in_array('Friday', $days))
                                            {!!
                                            $item->course_subject . '<br>' .
                                            $ins2->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if ($ins2)
                                            @if(in_array('Wednesday', $days))
                                            {!!
                                            $item->course_subject . '<br>' .
                                            $ins2->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <th colspan="4">No data</th>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    @include('livewire.modals.student-schedules-modal')
</main><!-- End #main -->

@script
<script>
    $wire.on('show-setScheduleStudentModal', () => {
        document.querySelector('#student-select').reset();
        document.querySelector('#subject-select').reset();
        $('#setScheduleStudentModal').modal('show');
    });

    $wire.on('hide-setScheduleStudentModal', () => {
        $('#setScheduleStudentModal').modal('hide');
    });

    $wire.on('confirm-exportExcel-student', () => {
        console.log('sdfasd');
        Swal.fire({
            title: "Are you sure?",
            text: "Exported data will be saved as .xlsx file.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, export it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.dispatch('exportExcel');
            }
        });
    });

    VirtualSelect.init({
        ele: '#student-select',
        maxWidth: '100%',
        search: true,
        options: @json($select_students)
    });

    let selectedStudent = document.querySelector('#student-select');
    selectedStudent.addEventListener('change', () => {
        let data = selectedStudent.value;
        @this.set('selectedStudent', data);
    });

    VirtualSelect.init({
        ele: '#subject-select',
        maxWidth: '100%',
        search: true,
        optionHeight: '110px',
        popupDropboxBreakpoint: '3000px',
        options: @json($subjects),
        hasOptionDescription: true
    });

    $wire.on('refresh-subject-select', (key) => {
        document.querySelector('#subject-select').destroy();
        let refreshed_courses = key[0]; // This will unpack the inner array which causes error in populating the data to virtual select

        // Initialize Virtual Select
        VirtualSelect.init({
            ele: '#subject-select',
            maxWidth: '100%',
            search: true,
            optionHeight: '110px',
            popupDropboxBreakpoint: '3000px',
            options: refreshed_courses,
            hasOptionDescription: true
        });
        // console.log(refreshed_courses);
    });

    let selectedSubject = document.querySelector('#subject-select');
    selectedSubject.addEventListener('change', () => {
        let data = selectedSubject.value;
        @this.set('selectedSubject', data);
    });

    // Reset selected options in Virtual Select
    $wire.on('reset-virtual-selects', () => {
        document.querySelector('#student-select').reset();
        document.querySelector('#subject-select').reset();
    })

    $wire.on('confirm-archive-subject', (appointments_id) => {
        Swal.fire({
            title: "Are you sure you want to archive this specific appointment?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, archive it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.dispatch('archive-appointment', {
                    appointments_id: appointments_id
                });
            }
        });
    });

    $wire.on('confirm-archive-all-subjects', () => {
        Swal.fire({
            title: "Are you sure you want to archive all subjects?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, archive it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $wire.dispatch('archive-all-appointments');
            }
        });
    })
</script>
@endscript