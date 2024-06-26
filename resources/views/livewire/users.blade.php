<div>
    <div>
        <main id="main" class="main">

            <section class="section">
                <div class="row">
                    <div class="col-lg-4">

                        <div class="card" style="background-color: #1f2937; border-radius: 1rem;" wire:loading.class="opacity-50" wire:target="save, edit, clear">
                            <div class="card-body">
                                <h5 class="card-title" style="color: #ffffff">{{ $editMode == false ? 'Create' : 'Edit' }} User Form</h5>
                                <form class="row g-3" data-bitwarden-watching="1" wire:submit="{{ $editMode == false ? 'save' : 'update' }}" novalidate>
                                    <div class="col-12">
                                        <label for="inputName" class="form-label" style="color: #ffffff">Name:</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="inputName" wire:model="name">
                                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="inputIDNo" class="form-label" style="color: #ffffff">ID No.:</label>
                                        <input type="text" class="form-control @error('id_no') is-invalid @enderror" id="inputIDNo" wire:model="id_no">
                                        @error('id_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="inputContactNo" class="form-label" style="color: #ffffff">Contact No.:</label>
                                        <input type="text" class="form-control @error('contact_no') is-invalid @enderror" id="inputContactNo" wire:model="contact_no">
                                        @error('contact_no') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="inputEmail" class="form-label" style="color: #ffffff">Email:</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="inputEmail" wire:model="email">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    @if ($editMode == false)
                                    <div class="col-12">
                                        <label for="inputPassword" class="form-label" style="color: #ffffff">Password:</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="inputPassword" wire:model="password">
                                        @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12">
                                        <label for="inputConfirmPassword" class="form-label" style="color: #ffffff">Confirm Password:</label>
                                        <input type="password" class="form-control @error('confirm_password') is-invalid @enderror" id="inputConfirmPassword" wire:model="confirm_password">
                                        @error('confirm_password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    @endif
                                    <div class="col-12">
                                        <label for="selectRole" class="form-label" style="color: #ffffff">Role:</label>
                                        <select id="inputState" class="form-select @error('role') is-invalid @enderror" wire:model="role">
                                            <option selected="">Choose...</option>
                                            <option value="Student">Student</option>
                                            <option value="Instructor">Instructor</option>
                                        </select>
                                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    @if ($editMode == true)
                                    <div class="col-12">
                                        <label for="selectIsActive" class="form-label" style="color: #ffffff">Active:</label>
                                        <select id="inputState" class="form-select @error('is_active') is-invalid @enderror" wire:model="is_active">
                                            <option selected="" disabled>Choose...</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        @error('role') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    @endif
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
                                <h5 class="card-title" style="color: #ffffff">Faculty List</h5>
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
                                        @forelse($users as $item)
                                        <div class="flex items-center bg-white rounded p-2 mb-2">
                                            <div class="container text-center">
                                                <div class="row g-2" wire:key="{{ $item->id }}">
                                                    <div class="col-8 text-start">
                                                        <div class="p-1">Name: {{ $item->name }}</div>
                                                        <div class="p-1">ID No.: {{ $item->id_no }}</div>
                                                        <div class="p-1">Email: {{ $item->email }}</div>
                                                        <div class="p-1">Contact No.: {{ $item->contact_no }}</div>
                                                        <div class="p-1">Role: {{ $item->role }}</div>
                                                        <div class="p-1">Status: <span class="badge {{ $item->is_active == 0 ? 'bg-secondary' : 'bg-success' }}">{{ $item->is_active == 0 ? 'Inactive' : 'Active' }}</span></div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="row p-3 g-2">
                                                            <div class="col-xxl-6">
                                                                <a class="btn btn-secondary w-100" href="#" role="button" wire:click="edit({{ $item->id }})">Edit</a>
                                                            </div>
                                                            <div class="col-xxl-6">
                                                                <!-- <a class="btn btn-danger w-100 {{ $item->is_active == 0 ? 'disabled-link' : '' }}" href="#" role="button" wire:click="softDelete({{ $item->id }})">Delete</a> -->
                                                                <a class="btn btn-danger w-100 {{ $item->is_active == 0 ? 'disabled-link' : '' }}" href="#" role="button" wire:click="$dispatch('confirm-user-softDelete', { key: {{ $item->id }} })">Delete</a>
                                                            </div>
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
    $wire.on('confirm-user-softDelete', (key) => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success",
                });
                $wire.dispatch('soft-delete-user', {
                    key: key
                });
            }
        });
        console.log(key);
    });
</script>
@endscript