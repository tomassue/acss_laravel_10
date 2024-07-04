<div>
    <main id="main" class="main">

        <section class="section">
            @if (Auth::user()->role == 'Super Admin')

            <div class="row gy-3">
                <div class="col-lg-4">
                    <div>
                        <div class="card border-bottom border-black border-4">
                            <div class="card-body">
                                <h5 class="card-title">Rooms & Section </h5>
                            </div>
                        </div>
                    </div>

                    <div class="list-group">
                        @forelse($roomsAndSections as $item)
                        <a href="#" class="list-group-item list-group-item-action pe-none" aria-current="true">
                            {{ $item->room_name . ' - ' . $item->time_block . ' | ' . implode(', ', $item->day) }}
                        </a>
                        @empty
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-4">
                    <div>
                        <div class="card border-bottom border-black border-4">
                            <div class="card-body">
                                <h5 class="card-title">Instructors</h5>
                            </div>
                        </div>

                        <div class="list-group">
                            @forelse($instructors as $item)
                            <a href="{{ route('faculty-schedules') }}" class="list-group-item list-group-item-action" aria-current="true">
                                {{ $item->name }}
                            </a>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            @elseif (Auth::user()->role == 'Student')

            <div class="row gy-3">
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

                <div class="col-lg-4">
                    <div class="card text-white" style="background-color: #1f2937; border-radius: 1rem;">
                        <div class="card-body">
                            <h5 class="card-title text-white">{{ Auth::user()->name }}</h5>
                            <p>Subject List:</p>
                            <div class="col-12 item-list-scroll">
                                @forelse($subjects as $item)
                                <div class="flex items-center bg-white rounded p-2 mb-2">
                                    <div class="container text-black text-center">
                                        <div class="row g-2" wire:key="{{ $item->appointments_id }}">
                                            <div class="col-8 text-start">
                                                <div class="p-1" style="font-size: smaller;">
                                                    {!!
                                                    $item->subject . '<br>' .
                                                    $item->time_block . '<br>' .
                                                    implode(', ', json_decode($item->day)) . '<br>' .
                                                    $item->room_name
                                                    !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Time Block</h5>
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
                                        @forelse ($subjects as $item)
                                        @php
                                        $days = json_decode($item->day);
                                        $ins = App\Models\AppointmentsModel::where('course_id', $item->course_id)
                                        ->where('status', '1')
                                        ->pluck('user_id');

                                        $ins2 = App\Models\User::select(DB::raw("COALESCE(SUBSTRING_INDEX(SUBSTRING_INDEX(users.name, ' ', 2), ' ', -1), '') AS ins_last_name"))
                                        ->whereIn('id', $ins)
                                        ->where('role', 'Instructor')
                                        ->first();
                                        @endphp

                                        <tr>
                                            <th scope="row">{{ $item->time_block }}</th>
                                            <td>
                                                @if ($ins2)
                                                @if (in_array('Monday', $days) || in_array('Thursday', $days))
                                                {!!
                                                $item->subject . '<br>' .
                                                $ins2->ins_last_name . '<br>' .
                                                $item->room_name
                                                !!}
                                                @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if ($ins2)
                                                @if (in_array('Tuesday', $days) || in_array('Friday', $days))
                                                {!!
                                                $item->subject . '<br>' .
                                                $ins2->ins_last_name . '<br>' .
                                                $item->room_name
                                                !!}
                                                @endif
                                                @endif
                                            </td>
                                            <td>
                                                @if ($ins2)
                                                @if (in_array('Wednesday', $days))
                                                {!!
                                                $item->subject . '<br>' .
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

            @elseif (Auth::user()->role == 'Instructor')

            <div class="row gy-3">
                <!-- <div class="col-md-6">
                    <select class="form-select" aria-label="Default select example" wire:model.live="semester">
                        <option selected>Select Semester</option>
                        <option value="1">1st Semester</option>
                        <option value="2">2nd Semester</option>
                        <option value="3">Intersession</option>
                    </select>
                </div> -->
                <!-- <div class="col-md-6">
                    <select class="form-select" aria-label="Default select example" wire:model.live="year">
                        <option selected>Year</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div> -->

                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="card-title">Time Block</h5>
                                </div>
                                @if ($subjects)
                                <div class="col-md-6 d-flex justify-content-end align-items-center">
                                    <a class="btn btn-info" role="button" aria-disabled="true" wire:click="$dispatch('confirm-exportExcel')"><i class="bi bi-filetype-csv"></i></a>
                                </div>
                                @endif
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered text-center" style="vertical-align: middle;">
                                    <thead>
                                        <tr>
                                            <th scope="col" width="12.5%">Time</th>
                                            <th scope="col" width="12.5%">Sun</th>
                                            <th scope="col" width="12.5%">Mon</th>
                                            <th scope="col" width="12.5%">Tue</th>
                                            <th scope="col" width="12.5%">Wed</th>
                                            <th scope="col" width="12.5%">Thu</th>
                                            <th scope="col" width="12.5%">Fri</th>
                                            <th scope="col" width="12.5%">Sat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($subjects as $item)
                                        @php
                                        $days = json_decode($item->day);
                                        @endphp

                                        <tr>
                                            <th scope="row">{{ $item->time_block }}</th>
                                            <td>
                                                @if (in_array('Sunday', $days))
                                                {!!
                                                $item->subject . ' ' . 'Block ' . $item->block . '<br>' .
                                                $item->last_name . '<br>' .
                                                $item->room_name
                                                !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array('Monday', $days))
                                                {!!
                                                $item->subject . ' ' . 'Block ' . $item->block . '<br>' .
                                                $item->last_name . '<br>' .
                                                $item->room_name
                                                !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array('Tuesday', $days))
                                                {!!
                                                $item->subject . ' ' . 'Block ' . $item->block . '<br>' .
                                                $item->last_name . '<br>' .
                                                $item->room_name
                                                !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array('Wednesday', $days))
                                                {!!
                                                $item->subject . ' ' . 'Block ' . $item->block . '<br>' .
                                                $item->last_name . '<br>' .
                                                $item->room_name
                                                !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array('Thursday', $days))
                                                {!!
                                                $item->subject . ' ' . 'Block ' . $item->block . '<br>' .
                                                $item->last_name . '<br>' .
                                                $item->room_name
                                                !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array('Friday', $days))
                                                {!!
                                                $item->subject . ' ' . 'Block ' . $item->block . '<br>' .
                                                $item->last_name . '<br>' .
                                                $item->room_name
                                                !!}
                                                @endif
                                            </td>
                                            <td>
                                                @if (in_array('Saturday', $days))
                                                {!!
                                                $item->subject . ' ' . 'Block ' . $item->block . '<br>' .
                                                $item->last_name . '<br>' .
                                                $item->room_name
                                                !!}
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <th colspan="8">No data</th>
                                        </tr>
                                        @endforelse

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @endif
        </section>

    </main><!-- End #main -->
</div>

@script
<script>
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
</script>
@endscript