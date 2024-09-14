{{-- Functions --}}
<script>
    $(document).ready(function() {
        // Initialize flatpickr for the release date input fields with past dates disabled
        $('.release-date-picker').flatpickr({
            dateFormat: "M d, Y",
            minDate: "today", // Disable past dates
            allowInput: false,
            enableTime: false // Disable time selection if only the date is needed
        });

        // Function to enable/disable flatpickr based on status
        function toggleFlatpickrBasedOnStatus(statusDropdown) {
            var selectedStatus = statusDropdown.val();
            var releaseDatePicker = statusDropdown.closest('tr').find('.release-date-picker');

            if (selectedStatus == 2 || selectedStatus == 0) { // Status "Rejected"
                releaseDatePicker.prop('disabled', true); // Disable the flatpickr input
                releaseDatePicker.flatpickr().clear(); // Clear any selected date
            } else {
                releaseDatePicker.prop('disabled', false);
                releaseDatePicker.flatpickr({
                    dateFormat: "M d, Y",
                    minDate: "today", // Disable past dates
                    allowInput: false,
                    enableTime: false // Disable time selection if only the date is needed
                }); // Enable the flatpickr input
            }
        }

        function fetchLogs() {
            $.ajax({
                url: '{{ route('fetchRequestLogs') }}',
                method: 'GET',
                data: {
                    status: status
                },
                success: function(response) {
                    $('#request-logs-body').html(response);
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching request logs.',
                    });
                }
            });
        }

        // Handle status update
        $('.update-status-btn').click(function() {
            var logId = $(this).data('log-id');
            var statusDropdown = $(this).closest('tr').find('.update-status');
            var newStatus = statusDropdown.val();
            var releaseDate = $(this).closest('tr').find('.release-date-picker').val();

            if (newStatus == 0 || newStatus == 2) {
                releaseDate = null; // Clear releaseDate for Pending or Rejected status
            }

            console.log('Log ID:', logId);
            console.log('New Status:', newStatus);
            console.log('Release Date:', releaseDate);

            var data = {
                _token: '{{ csrf_token() }}',
                transaction_ID: logId,
                status: newStatus,
                release_date: releaseDate
            };

            if (newStatus == 2) { // Rejection
                Swal.fire({
                    title: 'Reason for Rejection',
                    input: 'textarea',
                    inputPlaceholder: 'Enter the reason for rejection...',
                    showCancelButton: true,
                    confirmButtonText: 'Submit',
                    cancelButtonText: 'Cancel',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to provide a reason!';
                        }
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        data.rejection_reason = result.value;
                        $.ajax({
                            url: '{{ route('updateStatus') }}',
                            method: 'POST',
                            data: data,
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: response.message,
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                    toggleFlatpickrBasedOnStatus(
                                        statusDropdown); // Disable the date picker
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: response.message,
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while updating the status.',
                                });
                            }
                        });
                    }
                });
            } else {
                $.ajax({
                    url: '{{ route('updateStatus') }}',
                    method: 'POST',
                    data: data,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message,
                            }).then(() => {
                                window.location.reload();
                            });
                            toggleFlatpickrBasedOnStatus(
                                statusDropdown); // Enable the date picker if not rejected
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the status.',
                        });
                    }
                });
            }
        });


        // Trigger the toggle function when the status changes
        $('.update-status').change(function() {
            toggleFlatpickrBasedOnStatus($(this));
        });

        // Initialize flatpickr and set initial states
        $('.update-status').each(function() {
            toggleFlatpickrBasedOnStatus($(this));
        });

        $("#academic_year").select2({
            tags: true
        });
        $("#academic_year_admin").select2({
            tags: true
        });
    });
</script>
{{-- DATATABLES --}}
<script>
    $(document).ready(function() {
        $('#registrar-logs').DataTable({
            "responsive": true,
            "autoWidth": false,
            // Additional DataTables options here
        });
        $('#registrar-accounts').DataTable({
            "responsive": true,
            "autoWidth": false,
            // Additional DataTables options here
        });
        $('#student-logs').DataTable({
            "responsive": true,
            "autoWidth": false,
            // Additional DataTables options here
        });

        // Initialize Select2 for academic year and section filters
        $('#academic-year-filter').select2({
            placeholder: 'Select AY',
            width: '100%' // Adjust width as needed
        });

        $('#section-filter').select2({
            placeholder: 'Select Section',
            width: '100%' // Adjust width as needed
        });

        // Initialize DataTable
        var table = $('#student-records').DataTable({
            processing: false,
            serverSide: true,
            ajax: {
                url: "{{ route('records.data.ajax') }}",
                data: function(d) {
                    d.academic_year = $('#academic-year-filter').val();
                    d.section = $('#section-filter').val();
                },
                beforeSend: function(xhr, settings) {
                    var academicYear = $('#academic-year-filter').val();
                    var section = $('#section-filter').val();

                    // If neither academic year nor section is selected, stop the request and show a message
                    if (!academicYear && !section) {
                        $('#no-data-message').show();
                        $('#student-records').hide(); // Hide the table
                        return false; // Abort the AJAX request
                    } else {
                        $('#no-data-message').hide();
                        $('#student-records').show(); // Show the table
                    }
                }
            },
            columns: [{
                    data: 'fullname',
                    name: 'fullname'
                },
                {
                    data: 'sex',
                    name: 'sex'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'guardians_contact',
                    name: 'guardians_contact'
                },
                {
                    data: 'section',
                    name: 'section'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });

        // Handle view button click
        $('#student-records').on('click', '.view-student-data', function(e) {
            e.preventDefault();

            // Get the row data
            var data = table.row($(this).parents('tr')).data();

            // Set the modal content
            $('#studentFullName').text(data.fullname);
            $('#studentSex').text(data.sex);
            $('#studentAddress').text(data.address);
            $('#studentGuardiansName').text(data.guardians_name);
            $('#studentGuardiansContact').text(data.guardians_contact);
            $('#studentLevelToBeEnrolled').text(data.level_to_be_enrolled);
            $('#studentAdviser').text(data.adviser);
            $('#studentSection').text(data.section);
            $('#studentMotherName').text(data.mother_name);
            $('#studentAcademicYear').text(data.academic_year);

            // Show the modal
            $('#viewStudentModal').modal('show');
        });

        // Apply filter on change
        $('#filter-form').on('change', function() {
            table.draw();
        });

        // SweetAlert2 delete confirmation
        $(document).on('click', '.delete-student', function(e) {
            e.preventDefault();
            var studentId = $(this).data('student-id');
            var row = $(this).closest('tr');

            Swal.fire({
                title: 'Are you sure?',
                text: "This will also delete associated grades.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: 'student-records/' + studentId,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            table.row(row).remove().draw();
                            Swal.fire(
                                'Deleted!',
                                'Student record has been deleted.',
                                'success'
                            );
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Error!',
                                'There was a problem deleting the record.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    });


    // -----------------------------
    // ADMIN SIDE
    // Initialize Select2 for academic year and section filters
    $('#academic-year-filter2').select2({
        placeholder: 'Select AY',
        width: '100%' // Adjust width as needed
    });

    $('#section-filter2').select2({
        placeholder: 'Select Section',
        width: '100%' // Adjust width as needed
    });

    // Initialize DataTable ADMIN student records
    var table = $('#student-records2').DataTable({
        processing: false,
        serverSide: true,
        ajax: {
            url: "{{ route('admin/records.data.ajax') }}",
            data: function(d) {
                d.academic_year = $('#academic-year-filter2').val();
                d.section = $('#section-filter2').val();
            },
            beforeSend: function(xhr, settings) {
                var academicYear = $('#academic-year-filter2').val();
                var section = $('#section-filter2').val();

                // If neither academic year nor section is selected, stop the request and show a message
                if (!academicYear && !section) {
                    $('#no-data-message2').show();
                    $('#student-records2').hide(); // Hide the table
                    return false; // Abort the AJAX request
                } else {
                    $('#no-data-message2').hide();
                    $('#student-records2').show(); // Show the table
                }
            }
        },
        columns: [{
                data: 'fullname',
                name: 'fullname'
            },
            {
                data: 'sex',
                name: 'sex'
            },
            {
                data: 'address',
                name: 'address'
            },
            {
                data: 'guardians_contact',
                name: 'guardians_contact'
            },
            {
                data: 'section',
                name: 'section'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            }
        ]
    });

    // Handle view button click
    $('#student-records2').on('click', '.view-student-data', function(e) {
        e.preventDefault();

        // Get the row data
        var data = table.row($(this).parents('tr')).data();

        // Set the modal content
        $('#studentFullName_admin').text(data.fullname);
        $('#studentSex_admin').text(data.sex);
        $('#studentAddress_admin').text(data.address);
        $('#studentGuardiansName_admin').text(data.guardians_name);
        $('#studentGuardiansContact_admin').text(data.guardians_contact);
        $('#studentLevelToBeEnrolled_admin').text(data.level_to_be_enrolled);
        $('#studentAdviser_admin').text(data.adviser);
        $('#studentSection_admin').text(data.section);
        $('#studentMotherName_admin').text(data.mother_name);
        $('#studentAcademicYear_admin').text(data.academic_year);

        // Show the modal
        $('#viewStudentModal2').modal('show');
    });

    // Apply filter on change
    $('#filter-form2').on('change', function() {
        table.draw();
    });

    // SweetAlert2 delete confirmation
    $(document).on('click', '.delete-student-admin', function(e) {
        e.preventDefault();
        var studentId = $(this).data('student-id');
        var row = $(this).closest('tr');

        Swal.fire({
            title: 'Are you sure?',
            text: "This will also delete associated grades.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/admin/student-records/' + studentId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        table.row(row).remove().draw();
                        Swal.fire(
                            'Deleted!',
                            'Student record has been deleted.',
                            'success'
                        );
                    },
                    error: function(xhr) {
                        Swal.fire(
                            'Error!',
                            'There was a problem deleting the record.',
                            'error'
                        );
                    }
                });
            }
        });
    });
</script>

{{-- SWEET ALERTS --}}
<script>
    $(document).ready(function() {
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: '{{ $error }}',
                });
            @endforeach
        @endif

        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: "{{ session('success') }}",
            });
        @endif

        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "{{ session('error') }}",
            });
        @endif
    });
</script>

{{-- ADD SUBJECT AND GRADES MODAL --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const addGradesModal = document.getElementById('addGradesModal');

        // When the modal is shown, get the studentID from the button that triggered it
        addGradesModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            const studentID = button.getAttribute('data-student-id');

            // Update the hidden input field with the studentID
            addGradesModal.querySelector('input[name="studentID"]').value = studentID;
        });

        const subjectsContainer = document.getElementById('subjects-container');
        const addFieldButton = document.getElementById('add-subject');

        let fieldCount = 0; // Start at 0

        addFieldButton.addEventListener('click', () => {
            if (fieldCount >= 8) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Maximum Subjects Reached',
                    text: 'You can only add up to 8 subjects.',
                    confirmButtonText: 'OK'
                });
                return; // Prevent adding more fields
            }

            // Create a new input group
            const inputGroup = document.createElement('div');
            inputGroup.classList.add('mb-3');
            inputGroup.innerHTML = `
            <div class="input-group">
                <input type="text" class="form-control" name="subjects[${fieldCount}][name]" placeholder="Subject Name" required>
                <input type="text" class="form-control" name="subjects[${fieldCount}][grade]" placeholder="Grade" required>
                <button type="button" class="btn btn-danger remove-field-btn">Remove</button>
            </div>
        `;

            subjectsContainer.appendChild(inputGroup);

            fieldCount++;
        });

        // Event delegation for removing fields
        subjectsContainer.addEventListener('click', (event) => {
            if (event.target.classList.contains('remove-field-btn')) {
                event.target.closest('.mb-3').remove();
                fieldCount--; // Adjust the count after removing a field
            }
        });
    });

    function confirmDelete(gradeId, subject) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Type the subject name "${subject}" to confirm deletion.`,
            input: 'text',
            inputPlaceholder: 'Subject name',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to type the subject name to confirm!';
                } else if (value !== subject) {
                    return 'Subject name does not match!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if the input is valid
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('delete_grade', ':id') }}`.replace(':id', gradeId);

                // Create CSRF token input
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = "{{ csrf_token() }}";
                form.appendChild(csrfInput);

                // Create method field input
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    function confirmDeleteAdmin(gradeId, subject) {
        Swal.fire({
            title: 'Are you sure?',
            text: `Type the subject name "${subject}" to confirm deletion.`,
            input: 'text',
            inputPlaceholder: 'Subject name',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'You need to type the subject name to confirm!';
                } else if (value !== subject) {
                    return 'Subject name does not match!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit the form if the input is valid
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('delete_grade_admin', ':id') }}`.replace(':id', gradeId);

                // Create CSRF token input
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = "{{ csrf_token() }}";
                form.appendChild(csrfInput);

                // Create method field input
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>

{{-- upload script --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var uploadModal = document.getElementById('uploadModal');

        // Listen for when the modal is shown
        uploadModal.addEventListener('show.bs.modal', function(event) {
            // Get the button that triggered the modal
            var button = event.relatedTarget;

            // Extract the transaction ID from the data-transaction-id attribute
            var transactionId = button.getAttribute('data-transaction-id');

            // Find the hidden input field inside the modal and set the transaction ID
            var inputField = uploadModal.querySelector('#transaction_id');
            inputField.value = transactionId;
        });
    });

</script>
{{-- LOGOUT --}}
<script>
    function confirmLogout() {
        Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, log out!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
