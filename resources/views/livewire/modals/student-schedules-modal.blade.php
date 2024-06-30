<!-- setScheduleStudentModal -->
<div wire:ignore.self class="modal fade" id="setScheduleStudentModal" tabindex="-1" aria-labelledby="setScheduleStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="setScheduleStudentModalLabel">Appointment</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" wire:click="clear"></button>
            </div>
            <div class="modal-body">
                <form class="row g-3" data-bitwarden-watching="1" wire:submit="save">
                    <div class="col-12">
                        <label for="inputNanme4" class="form-label">Student</label>
                        <div id="student-select" wire:ignore></div>

                        <input type="hidden" wire:model.live="selectedStudent" id="selectedStudentInput">

                        @error('selectedStudent') <span class="custom-invalid-feedback"> {{ $message }} </span> @enderror
                    </div>
                    <div class="col-12">
                        <label for="inputEmail4" class="form-label">Subject</label>
                        <div id="subject-select" wire:ignore></div>

                        <input type="hidden" wire:model.live="selectedSubject" id="selectedSubjectInput">

                        @error('selectedSubject') <span class="custom-invalid-feedback"> {{ $message }} </span> @enderror
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