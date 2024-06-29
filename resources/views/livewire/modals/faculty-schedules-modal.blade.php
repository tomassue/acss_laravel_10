<!-- appointFacultyModal -->
<div wire:ignore.self class="modal fade" id="appointFacultyModal" tabindex="-1" aria-labelledby="appointFacultyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="appointFacultyModalLabel">Appointment</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="clear"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" data-bitwarden-watching="1" wire:submit="save">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Instructor</label>
                        <div id="instructor-select" wire:ignore></div>
                        @error('selectedInstructor') <span class="custom-invalid-feedback"> {{ $message }} </span> @enderror
                    </div>
                    <div class="col-12">
                        <label for="inputEmail4" class="form-label">Course</label>
                        <div id="course-select" wire:ignore></div>
                        @error('selectedCourse') <span class="custom-invalid-feedback"> {{ $message }} </span> @enderror
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="clear">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</div>