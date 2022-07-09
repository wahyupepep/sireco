<nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
      {{-- <a class="navbar-brand brand-logo" href="index.html"><img src="{{asset('assets/images/logo.svg')}}" alt="logo" /></a> --}}
      {{-- <a class="navbar-brand brand-logo" href="{{route('home')}}"><img src="{{asset('assets/images/pdam-purwa-tirta-dharma.png')}}" alt="logo" style="width: calc(225px - 120px); height:auto" /></a> --}}
      {{-- <a class="navbar-brand brand-logo-mini" href="{{route('home')}}"><img src="{{asset('assets/images/logo-pdam-blue.png')}}" alt="logo" style="width: calc(95px - 50px); height:auto" /></a> --}}
    </div>
    <div class="navbar-menu-wrapper d-flex align-items-stretch">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
        <span class="mdi mdi-menu"></span>
      </button>
      <ul class="navbar-nav navbar-nav-right">
        <li class="nav-item nav-profile dropdown">
          <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
            <div class="nav-profile-img">
              <img src="{{asset('assets/images/faces/face1.jpg')}}" alt="image">
              <span class="availability-status online"></span>
            </div>
            <div class="nav-profile-text">
              <p class="mb-1 text-black">{{Auth::user()->name}}</p>
            </div>
          </a>
          <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
            <a class="dropdown-item" href="#">
              <i class="mdi mdi-cached mr-2 text-success"></i> Activity Log </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">
              <i class="mdi mdi-logout mr-2 text-primary"></i> Signout </a>
          </div>
        </li>
        {{-- <li class="nav-item d-none d-lg-block full-screen-link"> --}}
        {{-- <li class="nav-item nav-profile dropdown dropdown-notification">
          <a class="nav-link dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
            <div class="nav-profile-text">
              <i class="mdi mdi-bell" id="bell-button"></i><sup style="color: red" class="rounded-circle">1</sup>
            </div>
           
          </a>
          <div class="dropdown-menu navbar-dropdown" aria-labelledby="NotificationDropdown">
            <a class="dropdown-item" href="#">
              <i class="mdi mdi-cached mr-2 text-success">Hello World</i> 
             </a>
            <div class="dropdown-divider"></div>
          </div>
        </li> --}}
        <li class="nav-item dropdown">
          <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
            <i class="mdi mdi-bell-outline"></i>
            <span class="count-symbol bg-danger"></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown" style="width: 300px">
            <h6 class="p-3 mb-0">Notifications</h6>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item preview-item">
              <div class="preview-thumbnail">
                <div class="preview-icon bg-success">
                  <i class="mdi mdi-cart-plus"></i>
                </div>
              </div>
              <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                <h6 class="preview-subject font-weight-normal mb-1 font-weight-bold">New Booking</h6>
                <p class="text-gray ellipsis mb-0 text-muted" style="font-size: 13px"> INV-2022/07/01/A1 </p>
              </div>
            </a>
            {{-- <div class="dropdown-divider"></div>
            <a class="dropdown-item preview-item">
              <div class="preview-thumbnail">
                <div class="preview-icon bg-warning">
                  <i class="mdi mdi-settings"></i>
                </div>
              </div>
              <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                <h6 class="preview-subject font-weight-normal mb-1">Settings</h6>
                <p class="text-gray ellipsis mb-0"> Update dashboard </p>
              </div>
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item preview-item">
              <div class="preview-thumbnail">
                <div class="preview-icon bg-info">
                  <i class="mdi mdi-link-variant"></i>
                </div>
              </div>
              <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                <h6 class="preview-subject font-weight-normal mb-1">Launch Admin</h6>
                <p class="text-gray ellipsis mb-0"> New admin wow! </p>
              </div>
            </a>
            <div class="dropdown-divider"></div>
            <h6 class="p-3 mb-0 text-center">See all notifications</h6> --}}
          </div>
        </li>
       
        <li class="nav-item nav-logout d-none d-lg-block">
            <a id="logout-form" class="nav-link" href="{{ route('manage.logout') }}">
              <i class="mdi mdi-power"></i>
              <form id="logout-form" action="{{ route('manage.logout') }}" method="POST" class="d-none">
                @csrf
              </form>
            </a>
        </li>
      </ul>
      <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
        <span class="mdi mdi-menu"></span>
      </button>
    </div>
</nav>