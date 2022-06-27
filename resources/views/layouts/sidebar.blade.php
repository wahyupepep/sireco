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
      <li class="nav-item">
        <a href="{{route('fdseat.index')}}" class="nav-link ">
          <span class="menu-title">Seats</span>
          <i class="mdi mdi-seat menu-icon"></i>
        </a>
      </li>   
      <li class="nav-item">
        <a href="{{route('seat.index')}}" class="nav-link ">
          <span class="menu-title">Reservation</span>
          <i class="mdi mdi-seat menu-icon"></i>
        </a>
      </li>   
      <li class="nav-item">
        <a href="{{route('payment.index')}}" class="nav-link ">
          <span class="menu-title">Payment</span>
          <i class="mdi mdi-currency-usd menu-icon"></i>
        </a>
      </li>   
      <li class="nav-item">
        <a href="{{route('sale.index')}}" class="nav-link ">
          <span class="menu-title">Sales</span>
          <i class="mdi mdi-cash-multiple menu-icon"></i>
        </a>
      </li>   
      <li class="nav-item">
        <a href="{{route('member.index')}}" class="nav-link ">
          <span class="menu-title">Member</span>
          <i class="mdi mdi-account-group-outline menu-icon"></i>
        </a>
      </li>   
      <li class="nav-item">
        <a href="{{route('verification.index')}}" class="nav-link ">
          <span class="menu-title">Payment Verification</span>
          <i class="mdi mdi-credit-card menu-icon"></i>
        </a>
      </li>   
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#settings" aria-expanded="false" aria-controls="ui-basic">
          <span class="menu-title">Setting</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-settings menu-icon"></i>
        </a>
        <div class="collapse" id="settings" style="">
          <ul class="nav flex-column sub-menu">
            
              <li class="nav-item"> <a class="nav-link" href="{{route('setting.profile')}}">Profil</a></li>
            
            
              <li class="nav-item"> <a class="nav-link" href="{{route('setting.password')}}">Ganti Password</a></li>
            
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-toggle="collapse" href="#master" aria-expanded="false" aria-controls="ui-basic">
          <span class="menu-title">Master</span>
          <i class="menu-arrow"></i>
          <i class="mdi mdi-settings menu-icon"></i>
        </a>
        <div class="collapse" id="master" style="">
          <ul class="nav flex-column sub-menu">
            
              <li class="nav-item"> <a class="nav-link" href="{{route('master.payment_method.index')}}">Payment Method</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{route('master.category_member.index')}}">Category Member</a></li>
              <li class="nav-item"> <a class="nav-link" href="{{route('master.room.index')}}">Room</a></li>
            
          </ul>
        </div>
      </li>
  
      
      <li class="nav-item">
        <a href="#" class="nav-link ">
          <span class="menu-title">Log Out</span>
          <i class="mdi mdi-logout-variant
          menu-icon"></i>
        </a>
      </li>
    </ul>
</nav>