<main id="main" class="main">

    <div class="pagetitle">
        <h1>{{ $archive_mode ? 'Archived Faculty Schedules' : 'Faculty Schedules' }}</h1>
        <span class="badge bg-secondary" role="button" wire:click="$set('archive_mode', {{ $archive_mode ? 'false' : 'true' }})"><i class="bi bi-clock-history"></i> View archived schedules</span>
    </div><!-- End Page Title -->

    <section class="section">

        @if (!$archive_mode)
        <div class="row">
            <div class="col-lg-12 py-3">
                <div class="d-grid gap-2 col-2 ms-auto">
                    <button type="button" class="btn btn-primary" wire:click="$dispatch('show-appointFacultyModal')">New</button>
                </div>
            </div>

            <div class="col-lg-12 py-3">

                <div class="d-grid gap-2 col-lg-6 mx-auto">
                    <div class="mb-3">
                        <select class="form-select form-select-lg" aria-label="Default select example" wire:model.live="instructor">
                            <option value="" selected>Select...</option>
                            @foreach ($instructors as $item)
                            <option value="{{ $item->instructor_id }}">{{ $item->instructor_name }}</option>
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
                            @if ($instructor)
                            <div class="col-md-6 d-flex justify-content-end align-items-center">
                                <a href="#" role="button" class="btn btn-danger" wire:click="$dispatch('confirm-archive-all-appointments')" wire:loading.remove>Archive All</a>
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
                                    @forelse($appointments as $item)
                                    <tr wire:key="{{ $item->appointments_id }}">
                                        <th scope="row">{{ $item->course_subject }}</th>
                                        <td>{{ $item->room_name }}</td>
                                        <td>{{ $item->time_block }}</td>
                                        <td>
                                            <a class="btn btn-danger" href="#" role="button" wire:click="$dispatch('confirm-archive-appointment', { appointments_id: {{ $item->appointments_id }} })"><i class="bi bi-archive"></i></a>
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
                            @if ($instructor)
                            <div class="col-md-6 d-flex justify-content-end align-items-center">
                                <a class="btn btn-info" role="button" aria-disabled="true" wire:click="$dispatch('confirm-exportExcel')"><i class="bi bi-filetype-csv"></i></a>
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
                                    @forelse ($appointments as $item)
                                    @php
                                    $days = json_decode($item->courses_day);
                                    @endphp
                                    <tr>
                                        <th scope="row">{{ $item->time_block }}</th>
                                        <td>
                                            @if(in_array('Monday', $days) || in_array('Thursday', $days))
                                            {!!
                                            $item->course_subject . '<br>' .
                                            $item->users_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array('Tuesday', $days) || in_array('Friday', $days))
                                            {!!
                                            $item->course_subject . '<br>' .
                                            $item->users_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array('Wednesday', $days))
                                            {!!
                                            $item->course_subject . '<br>' .
                                            $item->users_last_name . '<br>' .
                                            $item->room_name
                                            !!}
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

        @else($archive_mode)
        <div class="col-lg-12 py-3">

            <div class="d-grid gap-2 col-lg-6 mx-auto">
                <div class="mb-3">
                    <select class="form-select form-select-lg" aria-label="Default select example" wire:model.live="instructor">
                        <option value="" selected>Select...</option>
                        @foreach ($instructors as $item)
                        <option value="{{ $item->instructor_id }}">{{ $item->instructor_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="row gy-2">
                    <div class="col-md-6">
                        <select class="form-select" aria-label="Default select example" wire:model.live="semester">
                            <option selected="">Select Semester</option>
                            <option value="1">1st Semester</option>
                            <option value="2">2nd Semester</option>
                            <option value="3">Intersession</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" aria-label="Default select example" wire:model.live="year">
                            <option selected>Year</option>
                            @foreach ($select_year as $item)
                            <option value="{{ $item->year }}">{{ $item->year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="card-title">Time Block</h5>
                        </div>
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
                                @forelse ($archived_appointments as $item)
                                @php
                                $days = json_decode($item->courses_day);
                                @endphp
                                <tr wire:key="{{ $item->appointments_id }}">
                                    @php
                                    $ins = App\Models\AppointmentsModel::where('course_id', $item->course_id)->pluck('user_id');
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

        @endif

    </section>

    @include('livewire.modals.faculty-schedules-modal')

</main><!-- End #main -->

@script
<script>
    $wire.on('show-appointFacultyModal', () => {
        $('#appointFacultyModal').modal('show');
    });

    $wire.on('hide-appointFacultyModal', () => {
        $('#appointFacultyModal').modal('hide');
    });

    $wire.on('confirm-exportExcel', () => {
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

    $wire.on('confirm-archive-all-appointments', () => {
        Swal.fire({
            title: "Are you sure you want to archive all appointment?",
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
    });

    $wire.on('confirm-archive-appointment', (appointments_id) => {
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

    VirtualSelect.init({
        ele: '#instructor-select',
        maxWidth: '100%',
        search: true,
        options: @json($select_instructors)
    });

    let selectedInstructor = document.querySelector('#instructor-select');
    selectedInstructor.addEventListener('change', () => {
        let data = selectedInstructor.value;
        @this.set('selectedInstructor', data);
    });

    VirtualSelect.init({
        ele: '#course-select',
        maxWidth: '100%',
        search: true,
        optionHeight: '110px',
        popupDropboxBreakpoint: '3000px',
        options: @json($courses),
        hasOptionDescription: true
    });

    let selectedCourse = document.querySelector('#course-select');
    selectedCourse.addEventListener('change', () => {
        let data = selectedCourse.value;
        @this.set('selectedCourse', data);
    });

    $wire.on('refresh-course-select', (key) => {
        document.querySelector('#course-select').destroy();
        let refreshed_courses = key[0]; // This will unpack the inner array which causes error in populating the data to virtual select

        VirtualSelect.init({
            ele: '#course-select',
            maxWidth: '100%',
            search: true,
            optionHeight: '110px',
            popupDropboxBreakpoint: '3000px',
            options: refreshed_courses,
            hasOptionDescription: true
        });

        console.log(refreshed_courses);
    });

    // Reset selected options in Virtual Select
    $wire.on('reset-virtual-selects', () => {
        document.querySelector('#instructor-select').reset();
        document.querySelector('#course-select').reset();
    });
</script>
@endscript