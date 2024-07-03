<div>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center border-bottom border-black border-4">

        <div class="container-fluid d-flex align-items-center justify-content-between">
            <a href="" class="logo d-flex align-items-center">
                <img src="{{ asset('img/logotrans.png') }}" alt="" style="max-height: 50px;">
                <span class="d-none d-lg-block fs-6">Automated Class Scheduling System | XU Computer Studies</span>
            </a>
            <a href="{{ route('home') }}" role="button" class="btn btn-primary ms-auto" style="background-color: #1D3D76; border-color: #1D3D76;"><i class="bi bi-box-arrow-in-left"></i> Login</a>
        </div>
        <!-- End Search Bar --><!-- End Icons Navigation -->

    </header>


    <main id="main" class="main" style="margin-left: unset;">
        <div class="row gy-3">
            <div class="col-md-6">
                <select class="form-select" aria-label="Default select example" wire:model.live="course_type">
                    <option selected>Select Course</option>
                    @foreach ($course_types as $item)
                    <option value="{{ $item->type }}">{{ $item->type }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-select" aria-label="Default select example" wire:model.live="year">
                    <option selected>Year</option>
                    <option value="1">1st Year</option>
                    <option value="2">2nd Year</option>
                    <option value="3">3rd Year</option>
                    <option value="4">4th Year</option>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-select" aria-label="Default select example" wire:model.live="semester">
                    <option selected>Select Semester</option>
                    <option value="1">1st Semester</option>
                    <option value="2">2nd Semester</option>
                    <option value="3">Intersession</option>
                </select>
            </div>
            <div class="col-md-6">
                <select class="form-select" aria-label="Default select example" wire:model.live="block">
                    <option selected>Block</option>
                    @foreach ($blocks as $item)
                    <option value="{{ $item->block }}">{{ $item->block }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $course_type ?  $course_type : 'Course' }}</h5>
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
                                    @forelse($courses as $item)
                                    @php
                                    $days = $item->day;
                                    $ins = App\Models\AppointmentsModel::where('course_id', $item->course_id)
                                    ->where('status', '1')
                                    ->pluck('user_id');
                                    $ins2 = App\Models\User::select(DB::raw("COALESCE(SUBSTRING_INDEX(SUBSTRING_INDEX(users.name, ' ', 2), ' ', -1), '') AS ins_last_name"))
                                    ->whereIn('id', $ins)
                                    ->where('role', 'Instructor')
                                    ->first();
                                    @endphp
                                    <tr wire:key="{{ $item->appointments_id }}">
                                        <th scope="row">{{ $item->time_block }}</th>
                                        <td>
                                            @if(in_array('Sunday', $days))
                                            {!!
                                            $item->subject . ' ' . 'BLOCK ' . $item->block . '<br>' .
                                            optional($ins2)->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array('Monday', $days))
                                            {!!
                                            $item->subject . ' ' . 'BLOCK ' . $item->block . '<br>' .
                                            optional($ins2)->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array('Tuesday', $days))
                                            {!!
                                            $item->subject . ' ' . 'BLOCK ' . $item->block . '<br>' .
                                            optional($ins2)->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array('Wednesday', $days))
                                            {!!
                                            $item->subject . ' ' . 'BLOCK ' . $item->block . '<br>' .
                                            optional($ins2)->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array('Thursday', $days))
                                            {!!
                                            $item->subject . ' ' . 'BLOCK ' . $item->block . '<br>' .
                                            optional($ins2)->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array('Friday', $days))
                                            {!!
                                            $item->subject . ' ' . 'BLOCK ' . $item->block . '<br>' .
                                            optional($ins2)->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if(in_array('Saturday', $days))
                                            {!!
                                            $item->subject . ' ' . 'BLOCK ' . $item->block . '<br>' .
                                            optional($ins2)->ins_last_name . '<br>' .
                                            $item->room_name
                                            !!}
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8">No data</td>
                                    </tr>
                                    @endforelse
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>