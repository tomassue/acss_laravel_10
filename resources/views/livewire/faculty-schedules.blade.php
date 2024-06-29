<main id="main" class="main">

    <div class="pagetitle">
        <h1>Faculty Schedules</h1>
    </div><!-- End Page Title -->

    <section class="section">
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
                            <option selected="">Select...</option>
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
                        <h5 class="card-title">Edit Schedules</h5>
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
                                    <tr wire:key="{{ $item->course_id }}">
                                        <th scope="row">{{ $item->course_subject }}</th>
                                        <td>{{ $item->room_name }}</td>
                                        <td>{{ $item->time_block }}</td>
                                        <td>
                                            <a class="btn btn-danger" href="#" role="button"><i class="bi bi-trash"></i></a>
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
                        <h5 class="card-title">Time Blocks</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered text-center" style="vertical-align: middle;">
                                <thead>
                                    <tr>
                                        <th scope="col">Time</th>
                                        <th scope="col">Mon Thur</th>
                                        <th scope="col">Tue Fri</th>
                                        <th scope="col">Wed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th scope="row">1</th>
                                        <td>Mark</td>
                                        <td>Otto</td>
                                        <td>@mdo</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">2</th>
                                        <td>Jacob</td>
                                        <td>Thornton</td>
                                        <td>@fat</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">3</th>
                                        <td colspan="2">Larry the Bird</td>
                                        <td>@twitter</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
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
    })

    // Reset selected options in Virtual Select
    $wire.on('reset-virtual-selects', () => {
        document.querySelector('#instructor-select').reset();
        document.querySelector('#course-select').reset();
    })
</script>
@endscript