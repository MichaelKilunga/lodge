$(function() {
    const currentRoute = window.location.pathname;
    if(!currentRoute.startsWith('/facility')) return;

    const datatable = $("#facility-table").DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: `/facility`,
            type: 'GET',
            error: function(xhr, status, error) {}
        },
        columns: [
            {
                name: "number",
                data: "number"
            },
            {
                name: "icon",
                data: "icon",
                render: function(iconClass) {
                    return `<span class="badge bg-light text-primary border p-2 shadow-sm" style="font-size: 1.1rem;"><i class="${iconClass}"></i></span>`;
                }
            },
            {
                name: "name",
                data: "name",
                render: function(name) {
                    return `<span class="fw-bold">${name}</span>`;
                }
            },
            {
                name: "detail",
                data: "detail"
            },
            {
                name: "id",
                data: "id",
                width: "120px",
                render: function(facilityId) {
                    return `
                        <button class="btn btn-light btn-sm rounded shadow-sm border me-1"
                            data-action="edit-facility" data-facility-id="${facilityId}"
                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit facility">
                            <i class="fas fa-edit text-primary"></i>
                        </button>
                        <form class="d-inline btn-sm delete-facility p-0 m-0" method="POST"
                            id="delete-facility-form-${facilityId}"
                            action="/facility/${facilityId}">
                            <input type="hidden" name="_method" value="DELETE">
                            <a class="btn btn-light btn-sm rounded shadow-sm border delete"
                                href="#" facility-id="${facilityId}" facility-role="facility" data-bs-toggle="tooltip"
                                data-bs-placement="top" title="Delete facility">
                                <i class="fas fa-trash-alt text-danger"></i>
                            </a>
                        </form>
                    `;
                }
            }
        ]
    });

    const modal = new bootstrap.Modal($("#main-modal"), {
        backdrop: true,
        keyboard: true,
        focus: true
    });

    $(document).on('click', '.delete', function(e) {
        e.preventDefault();
        var facility_id = $(this).attr('facility-id');
        const swalWithBootstrapButtons = Swal.mixin({
            customClass: {
                confirmButton: 'btn btn-success ms-2',
                cancelButton: 'btn btn-danger'
            },
            buttonsStyling: false
        });

        swalWithBootstrapButtons.fire({
            title: 'Are you sure?',
            text: "Facility will be deleted. You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $(`#delete-facility-form-${facility_id}`).submit();
            }
        });
    }).on('click', '#add-button', async function() {
        modal.show();
        $('#main-modal .modal-body').html(`Fetching data...`);

        const response = await $.get(`/facility/create`);
        if (!response) return;

        $('#main-modal .modal-title').text('Create New Facility');
        $('#main-modal .modal-body').html(response.view);
    }).on('click', '#btn-modal-save', function() {
        $('#form-save-facility').submit();
    }).on('submit', '#form-save-facility', async function(e) {
        e.preventDefault();
        if (typeof CustomHelper !== 'undefined') {
            CustomHelper.clearError();
        }
        $('#btn-modal-save').attr('disabled', true);
        try {
            const response = await $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                method: $(this).attr('method'),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            });

            if (!response) return;

            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });

            modal.hide();
            datatable.ajax.reload();
        } catch (e) {
            if (e.status === 422 && typeof CustomHelper !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: e.responseJSON.message,
                });
                CustomHelper.errorHandlerForm(e);
            } else if (e.responseJSON && e.responseJSON.message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: e.responseJSON.message,
                });
            }
        } finally {
            $('#btn-modal-save').attr('disabled', false);
        }
    }).on('click', '[data-action="edit-facility"]', async function() {
        modal.show();
        $('#main-modal .modal-body').html(`Fetching data...`);

        const facilityId = $(this).data('facility-id');
        const response = await $.get(`/facility/${facilityId}/edit`);
        if (!response) return;

        $('#main-modal .modal-title').text('Edit Facility');
        $('#main-modal .modal-body').html(response.view);
    }).on('submit', '.delete-facility', async function(e) {
        e.preventDefault();
        try {
            const response = await $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                method: $(this).attr('method'),
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            });

            if (!response) return;

            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: response.message,
                showConfirmButton: false,
                timer: 1500
            });

            datatable.ajax.reload();
        } catch (e) {
            if(e && e.responseJSON && e.responseJSON.message) {
                Swal.fire({
                    position: 'top-end',
                    icon: 'error',
                    title: e.responseJSON.message,
                    showConfirmButton: false,
                    timer: 2000
                });
            }
        }
    });
});
