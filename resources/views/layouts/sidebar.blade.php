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
            <span class="text-secondary text-small">
              @foreach (Auth::user()->getRoleNames() as $name )
                  {{$name}}
              @endforeach
            </span>
          </div>
          <i class="mdi mdi-bookmark-check color-red nav-profile-badge"></i>
        </a>
      </li>
      
      <li class="nav-item {{ Request::route()->getName() === 'home' ? 'active' : '' }}">
        <a href="{{route('home')}}" class="nav-link">
          <span class="menu-title">Beranda</span>
          <i class="mdi mdi-home menu-icon"></i>
        </a>
      </li>

      @can('frontdesk-list')
        <li class="nav-item {{ Request::route()->getName() === 'fdseat.index' ? 'active' : '' }}">
          <a href="{{route('fdseat.index')}}" class="nav-link">
            <span class="menu-title">Seats</span>
            <i class="mdi mdi-seat menu-icon"></i>
          </a>
        </li>
      @endcan
      
      @can('reservation-list')
        <li class="nav-item {{ Request::route()->getName() === 'seat.index' ? 'active' : '' }}">
          <a href="{{route('seat.index')}}" class="nav-link">
            <span class="menu-title">Reservation</span>
            <i class="mdi mdi-seat menu-icon"></i>
          </a>
        </li> 
        <li class="nav-item {{ Request::route()->getName() === 'seat.list-order' ? 'active' : '' }}">
          <a href="{{route('seat.list-order')}}" class="nav-link">
            <span class="menu-title">Orders</span>
            <i class="mdi mdi-basket menu-icon"></i>
          </a>
        </li>
      @endcan
      
      @can('sales')
        <li class="nav-item {{ Request::route()->getName() === 'sale.index' ? 'active' : '' }}">
          <a href="{{route('sale.index')}}" class="nav-link">
            <span class="menu-title">Sales</span>
            <i class="mdi mdi-chart-areaspline menu-icon"></i>
          </a>
        </li> 
      @endcan
       
      @can('member-list')
      <li class="nav-item {{ Request::route()->getName() === 'member.index' ? 'active' : '' }}">
        <a href="{{route('member.index')}}" class="nav-link">
          <span class="menu-title">Member</span>
          <i class="mdi mdi-account-group-outline menu-icon"></i>
        </a>
      </li>
      @endcan
       
      @can('verification-list')
      <li class="nav-item {{ Request::route()->getName() === 'verification.index' ? 'active' : '' }}">
        <a href="{{route('verification.index')}}" class="nav-link">
          <span class="menu-title">Payment Verification</span>
          <i class="mdi mdi-marker-check menu-icon"></i>
        </a>
      </li>
      @endcan
      @can('setting-list')
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#settings" aria-expanded="false" aria-controls="ui-basic">
          <span class="menu-title">Setting</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-settings menu-icon"></i>
        </a>
        <div class="collapse" id="settings" style="">
          <ul class="nav flex-column sub-menu">
            
              <li class="nav-item {{ Request::route()->getName() === 'setting.profile' ? 'active' : '' }}"> <a class="nav-link" href="{{route('setting.profile', ['id' => Crypt::encryptString(Auth::user()->id)])}}">Profil</a></li>
            
            
              <li class="nav-item {{ Request::route()->getName() === 'setting.password' ? 'active' : '' }}"> <a class="nav-link" href="{{route('setting.password')}}">Ganti Password</a></li>
            
          </ul>
        </div>
      </li>
      @endcan
     
      @can('master-list')
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#masters" aria-expanded="false" aria-controls="ui-basic">
          <span class="menu-title">Master</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-settings menu-icon"></i>
        </a>
        <div class="collapse" id="masters" style="">
          <ul class="nav flex-column sub-menu">
            
              <li class="nav-item {{ Request::route()->getName() === 'master.payment_method.index' ? 'active' : '' }}"> <a class="nav-link" href="{{route('master.payment_method.index')}}">Payment Method</a></li>
              <li class="nav-item  {{ Request::route()->getName() === 'master.category_member.index' ? 'active' : '' }}"> <a class="nav-link" href="{{route('master.category_member.index')}}">Category Member</a></li>
              <li class="nav-item {{ Request::route()->getName() === 'master.room.index' ? 'active' : '' }}"> <a class="nav-link" href="{{route('master.room.index')}}">Room</a></li>
              <li class="nav-item  {{ Request::route()->getName() === 'master.discount.index' ? 'active' : '' }}"> <a class="nav-link" href="{{route('master.discount.index')}}">Discount</a></li>
              <li class="nav-item {{ Request::route()->getName() === 'role.index' ? 'active' : '' }}"> <a class="nav-link" href="{{route('role.index')}}">Role Authenticate</a></li>
              <li class="nav-item {{ Request::route()->getName() === 'user.index' ? 'active' : '' }}"> <a class="nav-link" href="{{route('user.index')}}">User</a></li>
            
          </ul>
        </div>
      </li>
      @endcan
     
     
  
      
      <li class="nav-item">
        <a href="#" class="nav-link ">
          <span class="menu-title">Log Out</span>
          <i class="mdi mdi-logout-variant
          menu-icon"></i>
        </a>
      </li>
    </ul>
</nav>