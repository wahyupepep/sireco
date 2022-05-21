<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item nav-profile">
        <a href="#" class="nav-link">
          <div class="nav-profile-image">
            <img src="{{asset('assets/images/faces/face1.jpg')}}" alt="profile">
            <span class="login-status online"></span>
            <!--change to offline or busy as needed-->
          </div>
          <div class="nav-profile-text d-flex flex-column">
            <span class="font-weight-bold mb-2">{{Auth::user()->name}}</span>
            <span class="text-secondary text-small">{{Auth::user()->role == 1 ? 'Super Admin' : 'Admin'}}</span>
          </div>
          <i class="mdi mdi-bookmark-check color-red nav-profile-badge"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('home')}}" class="nav-link ">
          <span class="menu-title">Beranda</span>
          <i class="mdi mdi-home menu-icon"></i>
        </a>
      </li>
      @can('slider-list')
        <li class="nav-item">
          <a href="{{route('slider.index')}}" class="nav-link ">
            <span class="menu-title">Slider</span>
            <i class="mdi mdi-file-image menu-icon"></i>
          </a>
        </li>
      @endcan
      @can('banner-list')
        <li class="nav-item">
          <a href="{{route('banner.index')}}" class="nav-link ">
            <span class="menu-title">Banner</span>
            <i class="mdi mdi-view-agenda menu-icon"></i>
          </a>
        </li>
      @endcan
      @can('partner-list')
        <li class="nav-item">
          <a href="{{route('partner.index')}}" class="nav-link ">
            <span class="menu-title">Mitra</span>
            <i class="mdi mdi-account-multiple menu-icon"></i>
          </a>
        </li>
      @endcan
      @can('menu-list')
        <li class="nav-item">
          <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
            <span class="menu-title">Menu</span>
            <i class="menu-arrow"></i>
            <i class="mdi mdi-format-list-bulleted-type menu-icon"></i>
          </a>
          <div class="collapse" id="ui-basic" style="">
            <ul class="nav flex-column sub-menu">
              <li class="nav-item"> <a class="nav-link" href="{{route('category.index')}}">Kategori</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{route('menu.index')}}">List</a></li>
            </ul>
          </div>
        </li>
      @endcan
     
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#settings" aria-expanded="false" aria-controls="ui-basic">
          <span class="menu-title">Pengaturan</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-settings menu-icon"></i>
        </a>
        <div class="collapse" id="settings" style="">
          <ul class="nav flex-column sub-menu">
            @can('user-list')   
              <li class="nav-item"> <a class="nav-link" href="{{route('user.index')}}">Pengguna</a></li>
            @endcan
            @can('setting-general-list')  
              <li class="nav-item"> <a class="nav-link" href="{{route('setting.general.index')}}">Umum</a></li>
            @endcan
            @can('role-list')
              <li class="nav-item"> <a class="nav-link" href="{{route('role.index')}}">Hak Akses</a></li>
            @endcan
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link ">
          <span class="menu-title">Keluar</span>
          <i class="mdi mdi-logout-variant
          menu-icon"></i>
        </a>
      </li>
    </ul>
</nav>