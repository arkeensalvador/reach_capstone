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
            <a href="{{ url('dashboard') }}">
                <li class="list {{ Request::is('dashboard') ? 'active' : '' }}">
                    DASHBOARD
                </li>
            </a>
            <a href="{{ url('records') }}">
                <li class="list {{ Request::is('records', 'section', 'records-data') ? 'active' : '' }}">
                    RECORDS
                </li>
            </a>
            <a href="{{ url('registrar-accounts') }}">
                <li class="list {{ Request::is('registrar-accounts', 'create-registrar-account', 'registrar-accounts/*/edit') ? 'active' : '' }}">
                    ACCOUNTS
                </li>
            </a>
            <a href="{{ url('reports') }}">
                <li class="list {{ Request::is('reports') ? 'active' : '' }}">
                    REPORTS
                </li>
            </a>


            <a href="{{ route('logout') }}"
                onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                <li class="list">
                    {{ __('LOGOUT') }}
                </li>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </ul>
    </nav>
</aside>
