<!--Navbar-->
<nav class="navbar navbar-dark navbar-fixed-top scrolling-navbar mdb-gradient top-nav-collapse double-nav">

  <!-- SideNav slide-out button -->
  <div class="float-xs-left">
    @if (!Auth::guest())  
        <a href="#" data-activates="slide-out" class="button-collapse"><i class="fa fa-bars"></i></a>
    @endif
  </div>

  <!-- Breadcrumb-->
  <div class="breadcrumb-dn">
    <p>LENKILA - STADIUM</p>
  </div>


  <ul class="nav navbar-nav float-xs-right">
      
    <!--<li class="nav-item ">
      <a class="nav-link"><i class="fa fa-sign-in"></i> <span class="hidden-sm-down">Login</span></a>
    </li>-->
    @if (!Auth::guest())    
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> {{Auth::user()->username}}</a>
      <div class="dropdown-menu dropdown-primary dd-right" aria-labelledby="dropdownMenu1" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
        <a class="dropdown-item waves-effect dd-light" href="#"><i class="fa fa-cog" aria-hidden="true"></i> การตั้งค่า</a>
        <a class="dropdown-item waves-effect dd-light" href="{{ url('/logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fa fa-sign-out" aria-hidden="true"></i> ออกจากระบบ
        </a>
        

        <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
        </form>
      </div>
    </li>
    @endif
  </ul>

</nav>
<!--/.Navbar-->