 <!--Sidebar navigation -->
<ul id="slide-out" class="side-nav fixed custom-scrollbar ">

  <!-- Logo -->
  <li>
    <div class="logo-wrapper waves-light">
      <!--<a href="#"><img src="http://mdbootstrap.com/wp-content/uploads/2015/12/mdb-white2.png" class="img-fluid flex-center"></a>-->
    </div>
  </li>
  <!--/. Logo -->

  <!--Search Form-->
  <!--<li>
<form class="search-form" role="search">
<div class="form-group waves-light">
<input type="text" class="form-control" placeholder="ค้นหา">
</div>
</form>
</li>-->
  <!--/.Search Form-->

  <!-- Side navigation links -->
  <li>
    <ul class="collapsible collapsible-accordion">      
      <li><a href="/{{Auth::user()->stadium->name}}/reservation" class="collapsible-header waves-effect arrow-r"><i class="fa fa-calendar-plus-o"></i> การจอง</a></li>
      <li><a href="/{{Auth::user()->stadium->name}}/customer_info" class="collapsible-header waves-effect arrow-r"><i class="fa fa-users"></i> ข้อมูลลูกค้า</a></li>
      <li><a href="/{{Auth::user()->stadium->name}}/analysis" class="collapsible-header waves-effect arrow-r"><i class="fa fa-line-chart"></i> วิเคราะห์ข้อมูล</a></li>
      <li><a href="/{{Auth::user()->stadium->name}}/dashboard" class="collapsible-header waves-effect arrow-r"><i class="fa fa-tachometer"></i> แผงควบคุม</a></li>
      @if(Auth::user()->role_id == 3)
        <li><a href="/{{Auth::user()->stadium->name}}/account_management" class="collapsible-header waves-effect arrow-r"><i class="fa fa-address-book-o"></i> การจัดการบัญชีผู้ใช้</a>
      @endif 
      <li><a href="/{{Auth::user()->stadium->name}}/report_problems" class="collapsible-header waves-effect arrow-r"><i class="fa fa-bug"></i> แจ้งปัญหาการใช้งาน</a></li>
    </ul>
  </li>
  <!--/. Side navigation links -->

</ul>
<!--/. Sidebar navigation -->
