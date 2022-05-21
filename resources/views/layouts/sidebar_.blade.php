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
          <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="{{route('home')}}" class="nav-link ">
          <span class="menu-title">Beranda</span>
          <i class="mdi mdi-home menu-icon"></i>
        </a>
      </li>
      @can('jenis-barang-list')
      <li class="nav-item {{ Request::route()->getPrefix() === 'admin/jenis_barang' ? 'active' : '' }}">
        <a href="{{route('jenis_barang.index')}}" class="nav-link ">
          <span class="menu-title">Jenis Barang</span>
          <i class="mdi mdi-bookmark-check menu-icon"></i>
        </a>
      </li>
      @endcan
      
      @can('merk-list')
      <li class="nav-item {{ Request::route()->getPrefix() === 'admin/merk' ? 'active' : '' }}">
        <a href="{{route('merk.index')}}" class="nav-link ">
          <span class="menu-title">Merk</span>
          <i class="mdi mdi-bookmark menu-icon"></i>
        </a>
      </li>
      @endcan
      @can('produk-list')
      <li class="nav-item {{ Request::route()->getPrefix() === 'admin/produk' ? 'active' : '' }}">
        <a href="{{route('produk.index')}}" class="nav-link ">
          <span class="menu-title">Produk</span>
          <i class="mdi mdi-dropbox menu-icon"></i>
        </a>
      </li>
      @endcan
      @can('barang-masuk-list')
      <li class="nav-item {{ Request::route()->getPrefix() === 'admin/barang-masuk' ? 'active' : '' }}">
        <a href="{{route('barang-masuk.index')}}" class="nav-link ">
          <span class="menu-title">Barang Masuk</span>
          <i class="mdi mdi-arrow-right menu-icon"></i>
        </a>
      </li>
      @endcan
      @can('barang-keluar-list')
      <li class="nav-item {{ Request::route()->getPrefix() === 'admin/barang-keluar' ? 'active' : '' }}">
        <a href="{{route('barang-keluar.index')}}" class="nav-link ">
          <span class="menu-title">Barang Keluar</span>
          <i class="mdi mdi-arrow-left menu-icon"></i>
        </a>
      </li>
      @endcan
     
      @can('role-list')
      <li class="nav-item {{ Request::route()->getPrefix() === 'admin/role' ? 'active' : '' }}">
        <a href="{{route('role.index')}}" class="nav-link ">
          <span class="menu-title">Manajemen Akses</span>
          <i class="mdi mdi-lock menu-icon"></i>
        </a>
      </li>
      @endcan
      @can('user-list')
      <li class="nav-item {{ Request::route()->getPrefix() === 'admin/user' ? 'active' : '' }}">
        <a href="{{route('user.index')}}" class="nav-link ">
          <span class="menu-title">User</span>
          <i class="mdi mdi-account menu-icon"></i>
        </a>
      </li>
      @endcan
    </ul>
</nav>