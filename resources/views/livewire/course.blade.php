<div>
    <div>
        <main id="main" class="main">

            <section class="section">
                <div class="row">
                    <div class="col-lg-4">

                        <div class="card" style="background-color: #1f2937; border-radius: 1rem;">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #ffffff">{{ $editMode == false ? 'Add Course' : 'Edit Course' }} Form</h5>
                                <form class="row g-3" data-bitwarden-watching="1" novalidate wire:submit="save">
                                    <div class="col-12">
                                        <label for="selectCourse" class="form-label" style="color: #ffffff">Course List:</label>
                                        <select id="selectCourse" class="form-select @error('type') is-invalid @enderror" wire:model="type">
                                            <option selected="">Select</option>
                                            <option value="BSCS">BSCS</option>
                                            <option value="BSEMC">BSEMC</option>
                                            <option value="BSIS">BSIS</option>
                                            <option value="BSIT">BSIT</option>
                                        </select>
                                        @error('type') <div class="invalid-feedback"> {{ $message }} </div> @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="inputSubject" class="form-label" style="color: #ffffff">Subject:</label>
                                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="inputSubject" wire:model="subject">
                                        @error('subject') <div class="invalid-feedback"> {{ $message }} </div> @enderror
                                    </div>
                                    <div class="col-6">
                                        <label for="inputYear" class="form-label" style="color: #ffffff">Year:</label>
                                        <select id="inputYear" class="form-select @error('year') is-invalid @enderror" wire:model="year">
                                            <option selected="">Select</option>
                                            <option value="1">1st Year</option>
                                            <option value="2">2nd Year</option>
                                            <option value="3">3rd Year</option>
                                            <option value="4">4th Year</option>
                                        </select>
                                        @error('year') <div class="invalid-feedback"> {{ $message }} </div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="selectSemester" class="form-label" style="color: #ffffff">Semester:</label>
                                        <select id="selectSemester" class="form-select @error('type') is-invalid @enderror" wire:model="semester">
                                            <option selected="">Select</option>
                                            <option value="1">1st Semester</option>
                                            <option value="2">2nd Semester</option>
                                            <option value="0">Intersession</option>
                                        </select>
                                        @error('type') <div class="invalid-feedback"> {{ $message }} </div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="selectedRoom" class="form-label" style="color: #ffffff">Room:</label>
                                        <div id="room-select" wire:ignore></div>
                                        <input type="hidden" wire:model="selectedRoom" id="selectedRoom">
                                        @error('selectedRoom') <span class="custom-invalid-feedback"> {{ $message }} </span> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="selectedDays" class="form-label" style="color: #ffffff">Days:</label>
                                        <div wire:ignore>
                                            <select class="form-select js-days-multiple" name="selectedDays[]" multiple="multiple" style="width: 100%;" size="7" wire:model="selectedDays">
                                                <option value="Sunday">Sunday</option>
                                                <option value="Monday">Monday</option>
                                                <option value="Tuesday">Tuesday</option>
                                                <option value="Wednesday">Wednesday</option>
                                                <option value="Thursday">Thursday</option>
                                                <option value="Friday">Friday</option>
                                                <option value="Saturday">Saturday</option>
                                            </select>
                                        </div>
                                        @error('selectedDays') <span class="custom-invalid-feedback"> {{ $message }} </span> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="inputStartTime" class="form-label" style="color: #ffffff">Time Start:</label>
                                        <input type="time" class="form-control @error('time_start') is-invalid @enderror" id="inputStartTime" wire:model="time_start">
                                        @error('time_start') <div class="invalid-feedback"> {{ $message }} </div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="inputTimeEnd" class="form-label" style="color: #ffffff">Time End:</label>
                                        <input type="time" class="form-control @error('time_start') is-invalid @enderror" id="inputTimeEnd" wire:model="time_end">
                                        @error('time_end') <div class="invalid-feedback"> {{ $message }} </div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="inputBlock" class="form-label" style="color: #ffffff">Block:</label>
                                        <input type="text" class="form-control @error('block') is-invalid @enderror" id="inputBlock" wire:model="block">
                                        @error('block') <div class="invalid-feedback"> {{ $message }} </div> @enderror
                                    </div>
                                    <div class="text-start" style="margin-top: 8px; padding-top: 15px; padding-bottom: 20px;">
                                        <button type="submit" class="btn btn-primary">{{ $editMode == false ? 'Save' : 'Update' }}</button>
                                        <button type="button" class="btn btn-secondary" wire:click="clear">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>

                    <div class="col-lg-8">

                        <div class="card" style="background-color: #1f2937; border-radius: 1rem;">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #ffffff">Course List</h5>
                                <form class="row g-3" data-bitwarden-watching="1">
                                    <div class="col-12">
                                        <div class="flex items-center bg-white rounded p-2">
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text" id="basic-addon1"><i class="ri-search-line"></i></span>
                                                <input type="text" class="form-control" placeholder="Search" aria-label="Username" aria-describedby="basic-addon1" wire:model.live="search">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 item-list-scroll">
                                        @forelse($courses as $item)
                                        <div class="flex items-center bg-white rounded p-2 mb-2">
                                            <div class="container text-center">
                                                <div class="row g-2" wire:key="{{ $item->id }}">
                                                    <div class="col-8 text-start">
                                                        <div class="p-1">Course Type: {{ $item->type }}</div>
                                                        <div class="p-1">Subject: {{ $item->subject }}</div>
                                                        <div class="p-1">Room: {{ $item->room_name }}</div>
                                                        <div class="p-1">Block: {{ $item->block }}</div>
                                                        <div class="p-1">Time: {{ $item->time_start . ' - ' . $item->time_end }}</div>
                                                        <div class="p-1">Days:
                                                            @if ($item->day_names->isNotEmpty())
                                                            @foreach ($item->day_names as $day)
                                                            {{ $day }}@if (!$loop->last), @endif
                                                            @endforeach
                                                            @endif
                                                        </div>
                                                        <div class="p-1">Year: {{ $item->year }}</div>
                                                        <div class="p-1">Semester: {{ $item->semester }}</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="row p-3 g-2">
                                                            <div class="col-xxl-6">
                                                                <a class="btn btn-secondary w-100" href="#" role="button" wire:click="edit({{ $item->id }})">Edit</a>
                                                            </div>
                                                            <!-- <div class="col-xxl-6">
                                                                <a class="btn btn-danger w-100 {{ $item->is_active == 0 ? 'disabled-link' : '' }}" href="#" role="button" wire:click="$dispatch('confirm-user-softDelete', { key: {{ $item->id }} })">Delete</a>
                                                            </div> -->
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <p class="text-white text-center pt-5">No Record</p>
                                        @endforelse
                                    </div>
                                    <div class="text-start" style="margin-top:8; padding-top: 15px; padding-bottom: 20px;">
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </section>

        </main><!-- End #main -->
    </div>
</div>

@script
<script>
    // Initialize Select2 after Livewire component is rendered
    // $(document).ready(function() {
    //     $('.js-days-multiple').select2();

    //     // Update Livewire component on change event
    //     $('.js-days-multiple').on('change', function() {
    //         var selectedValues = $(this).val(); // Get selected values
    //         @this.set('selectedDays', selectedValues); // Update Livewire property
    //     });
    // });

    VirtualSelect.init({
        ele: '#room-select',
        search: true,
        maxWidth: '100%',
        options: @json($rooms),
    });

    let selectedRoom = document.querySelector('#room-select');
    selectedRoom.addEventListener('change', () => {
        let data = selectedRoom.value;
        @this.set('selectedRoom', data);
    })

    // When edit mode, populate the data from db to virtual select through the event
    $wire.on('set-selectedRoom-values', (room) => {
        document.querySelector('#room-select').setValue(room);
        // console.log('Room:', room);
    })

    // Reset selected options in Virtual Select
    $wire.on('reset-virtual-selects', () => {
        document.querySelector('#room-select').reset();
        // document.querySelector('#days-select').reset();
    })
</script>
@endscript