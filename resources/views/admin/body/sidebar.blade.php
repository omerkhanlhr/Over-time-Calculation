<nav class="sidebar">
    <div class="sidebar-header">
      <a href="#" class="sidebar-brand">
       Theta<span>Smart</span>
      </a>
      <div class="sidebar-toggler not-active">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
    <div class="sidebar-body">
      <ul class="nav">
        <li class="nav-item nav-category">Main</li>
        <li class="nav-item">
          <a href="{{route('admin.dashboard')}}" class="nav-link">
            <i class="link-icon" data-feather="box"></i>
            <span class="link-title">Dashboard</span>
          </a>
        </li>
        <li class="nav-item nav-category">ADMIN MANAGEMENT</li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#admin" role="button" aria-expanded="false" aria-controls="emails">
            <i class="link-icon" data-feather="box"></i>
            <span class="link-title">Users</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse" id="admin">
            <ul class="nav sub-menu">
              {{-- @if(Auth::user()->can('site.settings')) --}}
              <li class="nav-item">
                <a href="{{route('all.users')}}" class="nav-link">All Users</a>
              </li>

              <li class="nav-item">
                <a href="{{route('add.user')}}" class="nav-link">Add User</a>
              </li>

            </ul>
          </div>
        </li>

        <li class="nav-item nav-category">Employees</li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#employee" role="button" aria-expanded="false" aria-controls="emails">
            <i class="link-icon" data-feather="box"></i>
            <span class="link-title">Employees</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse" id="employee">
            <ul class="nav sub-menu">
              {{-- @if(Auth::user()->can('site.settings')) --}}
              <li class="nav-item">
                <a href="{{route('all.employee')}}" class="nav-link">All Employees</a>
              </li>

              <li class="nav-item">
                <a href="{{route('add.employee')}}" class="nav-link">Add Employee</a>
              </li>

            </ul>
          </div>
        </li>
        <li class="nav-item nav-category">Clients</li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#clients" role="button" aria-expanded="false" aria-controls="emails">
            <i class="link-icon" data-feather="box"></i>
            <span class="link-title">Clients</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse" id="clients">
            <ul class="nav sub-menu">
              {{-- @if(Auth::user()->can('site.settings')) --}}
              <li class="nav-item">
                <a href="{{ route('all.clients') }}" class="nav-link">All Clients</a>
              </li>

              <li class="nav-item">
                <a href="{{ route('add.client') }}" class="nav-link">Add Client</a>
              </li>

            </ul>
          </div>
        <li class="nav-item nav-category">Work Hours</li>
        <li class="nav-item">
          <a class="nav-link" data-bs-toggle="collapse" href="#work_hours" role="button" aria-expanded="false" aria-controls="emails">
            <i class="link-icon" data-feather="box"></i>
            <span class="link-title">WorkHour Details</span>
            <i class="link-arrow" data-feather="chevron-down"></i>
          </a>
          <div class="collapse" id="work_hours">
            <ul class="nav sub-menu">
              {{-- @if(Auth::user()->can('site.settings')) --}}
              <li class="nav-item">
                <a href="{{ route('display.work.hours') }}" class="nav-link">WorkHours Details</a>
              </li>

              <li class="nav-item">
                <a href="{{ route('add.work.hours') }}" class="nav-link">Add WorkHours</a>
              </li>

            </ul>
          </div>
        </li>

      </ul>
    </div>
  </nav>
