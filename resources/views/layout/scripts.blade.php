{{-- DATATABLES --}}
<script>
    $(document).ready(function() {
        $('#registrar-logs').DataTable({
            "responsive": true,
            "autoWidth": false,
            // Additional DataTables options here
        });

         $('#student-logs').DataTable({
            "responsive": true,
            "autoWidth": false,
            // Additional DataTables options here
        });

         $('#student-records').DataTable({
            "responsive": true,
            "autoWidth": false,
            // Additional DataTables options here
        });
    });
</script>

{{-- SWEET ALERTS --}}
<script>
    $(document).ready(function() {
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