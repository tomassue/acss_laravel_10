<div>
    <main id="main" class="main">

        <section class="section">
            <div class="row">
                <div class="col-lg-4">

                    <div class="card" style="background-color: #1f2937; border-radius: 1rem;" wire:loading.class="opacity-50" wire:target="save, edit, clear">
                        <div class="card-body">
                            <h5 class="card-title" style="color: #ffffff">Room Form</h5>
                            <form class="row g-3" data-bitwarden-watching="1" wire:submit="{{ $editMode == false ? 'save' : 'update' }}" novalidate>
                                <div class="col-12">
                                    <label for="inputRoom" class="form-label" style="color: #ffffff">Room</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="inputRoom" wire:model="name">
                                    @error('name') <div class="invalid-feedback"> {{ $message }} </div> @enderror
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
                            <h5 class="card-title" style="color: #ffffff">Room List</h5>
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
                                    @forelse($rooms as $item)
                                    <div class="flex items-center bg-white rounded p-2 mb-2">
                                        <div class="container text-center">
                                            <div class="row g-2" wire:key="{{ $item->id }}">
                                                <div class="col-8 text-start">
                                                    <div class="p-1">Room: {{ $item->name }}</div>
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