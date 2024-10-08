{{-- ACTIVE MENU QUERY --}}
@php
    $segment1 = Request::segment(1);
    $pages = ['dashboard', 'section', 'records', 'registrar-accounts', 'reports'];
@endphp

<aside>
    <nav>
        {{-- {{ Auth::user()->name }} --}}
        <h1>Hi,
            @php
                $userTypes = [
                    'admin' => 'Admin',
                    'student' => 'Student',
                    'registrar' => 'Registrar',
                ];

                $userType = Auth::user()->userType;
                echo $userTypes[$userType] ?? 'Unknown';
            @endphp
        </h1>
        <ul class="navlist">
            @if (Auth::user()->userType == 'admin')
                <a href="{{ url('dashboard') }}">
                    <li class="list {{ Request::is('dashboard') ? 'active' : '' }}">
                        DASHBOARD
                    </li>
                </a>
                <a href="{{ url('admin/student-records') }}">
                    <li class="list mt-2 {{ Request::is('admin/student-records', 'admin/edit-student/*', 'admin/view-grades/*') ? 'active' : '' }}">
                        RECORDS
                    </li>
                </a>
                <a href="{{ url('admin/registrar-accounts') }}">
                    <li
                        class="list mt-2 {{ Request::is('admin/registrar-accounts', 'admin/create-registrar-account', 'admin/registrar-accounts/*/edit') ? 'active' : '' }}">
                        ACCOUNTS
                    </li>
                </a>
                <a href="{{ url('admin/reports') }}">
                    <li class="list mt-2 {{ Request::is('admin/reports') ? 'active' : '' }}">
                        REPORTS
                    </li>
                </a>
            @endif

            @if (Auth::user()->userType == 'registrar')
                <a href="{{ url('registrar-home') }}">
                    <li class="list mt-3 {{ Request::is('registrar-home') ? 'active' : '' }}">
                        DASHBOARD
                    </li>
                </a>
                <a href="{{ url('enroll-student') }}">
                    <li class="list mt-3 {{ Request::is('enroll-student') ? 'active' : '' }}">
                        ENROLL
                    </li>
                </a>
                <a href="{{ url('student-records') }}">
                    <li
                        class="list mt-3 {{ Request::is('student-records', 'view-grades/*', 'import-form') ? 'active' : '' }}">
                        RECORDS
                    </li>
                </a>

                <script>
                    function checkForNewRequestsRegistrar() {
                        $.ajax({
                            url: '{{ route('checkPendingRequestsRegistrar') }}',
                            method: 'GET',
                            success: function(data) {
                                if (data.pendingRequestsCountRegistrar > 0) {
                                    $('.badge-registrar').text(data.pendingRequestsCountRegistrar).show();
                                } else {
                                    $('.badge-registrar').hide();
                                }
                            }
                        });
                    }
                    checkForNewRequestsRegistrar();
                    setInterval(checkForNewRequestsRegistrar, 10000);
                </script>

                <a href="{{ url('registrar-logs') }}">
                    <li
                        class="list mt-3 {{ Request::is('registrar-logs', 'registrar/request-logs') ? 'active' : '' }}">
                        REQUEST LOGS
                        <span class="badge bg-danger badge-registrar" style="display: none;">0</span>
                    </li>
                </a>
            @endif

            @if (Auth::user()->userType == 'student')
                <a href="{{ url('student-home') }}">
                    <li class="list mt-5 {{ Request::is('student-home') ? 'active' : '' }}">
                        HOME
                    </li>
                </a>
                <a href="{{ url('student-requests') }}">
                    <li class="list mt-3 {{ Request::is('student-requests') ? 'active' : '' }}">
                        REQUEST
                    </li>
                </a>
                <a href="{{ url('student-logs') }}">
                    <li class="list mt-3 {{ Request::is('student-logs', 'student/request-logs') ? 'active' : '' }}">
                        REQUEST LOGS
                        <span class="badge custom-badge-bg-student badge-student" style="display: none;">0</span>
                    </li>
                </a>
            @endif

            <a href="{{ route('logout') }}" onclick="event.preventDefault(); confirmLogout();">
                <li class="list mt-3">
                    {{ __('LOGOUT') }}
                </li>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </ul>
    </nav>
</aside>
<script>
    function checkForNewRequestsRegistrar() {
        $.ajax({
            url: '{{ route('checkPendingRequestsRegistrar') }}',
            method: 'GET',
            success: function(data) {
                if (data.pendingRequestsCountRegistrar > 0) {
                    $('.badge-registrar').text(data.pendingRequestsCountRegistrar).show();
                } else {
                    $('.badge-registrar').hide();
                }
            }
        });
    }

    function checkForNewRequestsStudent() {
        $.ajax({
            url: '{{ route('checkPendingRequestsStudent') }}',
            method: 'GET',
            success: function(data) {
                if (data.pendingRequestsCountStudent > 0) {
                    $('.badge-student').text(data.pendingRequestsCountStudent).show();
                } else {
                    $('.badge-student').hide();
                }
            }
        });
    }

    // Initialize the functions and intervals
    // checkForNewRequestsRegistrar();
    checkForNewRequestsStudent();
    // setInterval(checkForNewRequestsRegistrar, 1000);
    setInterval(checkForNewRequestsStudent, 10000);
</script>
