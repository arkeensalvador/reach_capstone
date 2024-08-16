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
                <a href="{{ url('records') }}">
                    <li class="list mt-2 {{ Request::is('records', 'section', 'records-data') ? 'active' : '' }}">
                        RECORDS
                    </li>
                </a>
                <a href="{{ url('registrar-accounts') }}">
                    <li
                        class="list mt-2 {{ Request::is('registrar-accounts', 'create-registrar-account', 'registrar-accounts/*/edit') ? 'active' : '' }}">
                        ACCOUNTS
                    </li>
                </a>
                <a href="{{ url('reports') }}">
                    <li class="list mt-2 {{ Request::is('reports') ? 'active' : '' }}">
                        REPORTS
                    </li>
                </a>

                {{-- <a href="{{ url('forms-index') }}">
                    <li
                        class="list mt-3 {{ Request::is('registrar-accounts', 'create-registrar-account', 'registrar-accounts/*/edit') ? 'active' : '' }}">
                        FORMS
                    </li>
                </a> --}}
            @endif

            @if (Auth::user()->userType == 'registrar')
                <a href="{{ url('registrar-home') }}">
                    <li class="list mt-3 {{ Request::is('registrar-home') ? 'active' : '' }}">
                        DASHBOARD
                    </li>
                </a>
                <a href="{{ url('registrar-records') }}">
                    <li class="list mt-3 {{ Request::is('registrar-records') ? 'active' : '' }}">
                        RECORDS
                    </li>
                </a>
                {{-- <a href="{{ url('registrar-requests') }}">
                    <li
                        class="list mt-3 {{ Request::is('registrar-requests') ? 'active' : '' }}">
                        REQUESTS
                    </li>
                </a> --}}
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
                    setInterval(checkForNewRequestsRegistrar, 1000);
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


            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
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
    setInterval(checkForNewRequestsStudent, 1000);
</script>
