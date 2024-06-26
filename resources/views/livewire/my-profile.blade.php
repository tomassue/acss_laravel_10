<main id="main" class="main">

    <section class="section">
        <div class="row">
            <div class="col-lg-12">

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">My Profile</h5>
                        <form class="row g-3" data-bitwarden-watching="1" novalidate wire:submit="update">
                            <div class="col-6">
                                <label for="inputName" class="form-label">Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="inputName" wire:model="name">
                                @error('name') <div class="invalid-feedback">{{$message}}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label for="inputEmail" class="form-label">Email</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="inputEmail" wire:model="email">
                                @error('email') <div class="invalid-feedback">{{$message}}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label for="inputPassword" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" id="inputPassword" wire:model="password">
                                @error('password') <div class="invalid-feedback">{{$message}}</div> @enderror
                            </div>
                            <div class="col-6">
                                <label for="inputConfirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control @error('confirmPassword') is-invalid @enderror" id="inputConfirmPassword" wire:model="confirmPassword">
                                @error('confirmPassword') <div class="invalid-feedback">{{$message}}</div> @enderror
                            </div>
                            <div class="text-end" style="margin-top: 8px; padding-top: 15px; padding-bottom: 20px;">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
    </section>

</main><!-- End #main -->