<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="csrf-token" content="{{csrf_token()}}" />
  <title>Healthcare Analytics</title>
  <link rel = "icon" href="{{ asset('dist/img/magnifying_logo.jpg') }}"type = "image/x-icon"> 
  <!-- Scripts -->
    
  
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/mg.css') }}" rel="stylesheet">
  <!-- Laravel specific end-->
  
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/all.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{asset('plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/sweetalert2@9.17.2/dist/sweetalert2.min.css">
  <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
  
  <style type="text/css">
    .sticky-lg-top {
    margin-top: 4%;
    height: auto;
    background-color: #f5f5f5;
    margin-left: 16%;
    margin-bottom: -3%;
    padding: 10px;
    width: 1316px;
    overflow-x: scroll;
}
div#Line {
    background-color: #c1b5b5;
    width: 100%;
    height: 2px;
    margin: 8px;
}
nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
}
.layout-navbar-fixed.sidebar-mini-md.sidebar-collapse .wrapper .brand-link, .layout-navbar-fixed.sidebar-mini.sidebar-collapse .wrapper .brand-link {
    height: calc(3.5rem + 1px);
    width: 9.6rem;
}
.layout-fixed .main-sidebar {
    bottom: 0;
    float: none;
    height: 100vh;
    left: 0;
    position: fixed;
    top: 60px;
}
[class*=sidebar-dark-] {
    background-color: #060a35;
}
.os-padding {
    box-sizing: inherit;
    direction: inherit;
    position: absolute;
    overflow: visible;
    padding: 0;
    margin: 0;
    left: 0;
    top: -30px;
    bottom: 0;
    right: 0;
    width: auto!important;
    height: auto!important;
    z-index: 1;
}
.layout-navbar-fixed.layout-fixed .wrapper .sidebar {
    margin-top: calc(0.5rem + 1px);
}
.navbar-expand .navbar-nav .nav-link {
    padding-right: 1rem;
    padding-left: 11rem;
}
a.linkAnch {
    color: white;
    cursor: pointer;
    text-decoration: none;
}
a.linkAnch:hover {
    color: orange;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
}
#DefaultprofileImage {
    width: 40px;
    height: 38px;
    border-radius: 50%;
    background: #007bff;
    font-size: 20px;
    color: #fff;
    text-align: center;
    line-height: 37px;
    /* margin: 20px 0; */
}
#DefaultprofileImage1 {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: #1a2058;
    font-size: 40px;
    color: #fff;
    text-align: center;
    line-height: 86px;
    margin-left: 32%;
    /* margin: 20px 0; */
}
.os-viewport.os-viewport-native-scrollbars-invisible {
    overflow-y: scroll;
    margin-bottom: 61px;
}
li.nav-item.dropdown.setting,.user-menu {
    margin-top: -5px;
    margin-bottom: 8px;
}
a.dropdown-item {
    /*font-weight: 600;*/
    margin-left: 15px;
}
div#cont {
    padding: 28px;
    padding-left: 57px;
}

.menu-item {
    display: block;
    padding: 0;
}
.px-3 {
    padding-right: 0.75rem!important;
    padding-left: 0.75rem!important;
}
.menu-item .menu-content {
    padding: 0.65rem 1rem;
}
.symbol {
    display: inline-block;
    flex-shrink: 0;
    position: relative;
    border-radius: 0.475rem;
}
.symbol.symbol-50px>img {
    width: 50px;
    height: 50px;
}
.flex-column {
    flex-direction: column!important;
}
.fw-bolder {
    font-weight: 600!important;
}
.fs-5 {
    font-size: 1.15rem!important;
}
.align-items-center {
    align-items: center!important;
}
.d-flex {
    display: flex!important;
    padding-left: 10px;
}
.text-muted {
    color: #a1a5b7!important;
}
.text-hover-primary {
    transition: color .2s ease,background-color .2s ease;
}
.text-muted {
    bs-text-opacity: 1;
    color: #a1a5b7!important;
}
.fw-bold {
    font-weight: 500!important;
}
.fs-7 {
    font-size: .95rem!important;
    padding-left: 11px;
}
.dropdown-item:focus, .dropdown-item:hover {
    color: #16181b;
    text-decoration: none;
    background-color: #f8f9fa;
    width: 93%;
}
.stng,.lgot {
    font-size: 1.5em;
}
div#myModal {
    margin-top: 5%;
}
button.btn.btn-anch1 {
    padding: 0.4rem 1rem;
    /*font-weight: 600;*/
    margin-left: 15px;
    
}

button.btn.btn-anch1 {
    color: #010912 !important;
    margin-left: 14px !important;
    margin-top: -5px !important;
    margin-bottom: -8px !important;
}

button.btn.btn-anch {
    color: #010912 !important;
    margin-left: 18px !important;
    margin-top: 2px !important;
    margin-bottom: 8px !important;
}

img.compLogo {
    height: 77px;
    margin-left: 29%;
}
h3.clnt_ttl {
    margin-left: auto;
    margin-right: auto;
    color: #0056b3;
    font-weight: 600;
    margin-top: 6px;
}
.modal-footer {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -ms-flex-align: center;
    align-items: center;
    -ms-flex-pack: end;
    justify-content: left;
    padding: 0.75rem;
    border-top: 1px solid #e9ecef;
    border-bottom-right-radius: calc(0.3rem - 1px);
    border-bottom-left-radius: calc(0.3rem - 1px);
}
#myuserModal{
  padding:25px !important;
  margin:25px !important;

}
p#mdlbdy {
    margin-left: 113px;
}

p#cyrt {
    margin-left: 21px;
}
#timeOutWarningOverlay {
    position: fixed;
    display: none;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0,0,0,0.5);
    z-index: 2000;
    cursor: pointer;
}

@media screen and (max-width: 600px) {
img.user-image.img-circle.elevation-2 {
    margin-left: -45px;
}
  h3.clnt_ttl {
    margin-left: auto;
    margin-right: auto;
    color: #0056b3;
    font-weight: 600;
    margin-top: 6px;
    display: none;
 }
 .navbar-expand .navbar-nav .nav-link {
    padding-right: 1rem;
    padding-left: 0rem; 
}
  img.logo_img{
        width:100%;
        height:33px; 
        }
    #myusermodal{
      padding: 18px !important;
      margin-top: 39px !important;
      padding-right: 42px !important;
      padding-left: 2px !important;
      margin-top: 4rem !important;
    }
    img.compLogo {
      height: 77px;
      margin-left: 19%;
  }
  #myModal{
      margin: 2px;
      padding-top: 99px;
  }
  p#mdlbdy {
      margin-left: 46px;
  }
}


  </style>
</head>
<!-- <body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed sidebar-collapse" oncontextmenu="return false;"> -->
<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed sidebar-collapse">
<div class="wrapper">
  <!-- Navbar -->
  <nav class="navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a href="/home" class="">
        @if( Auth::user()->iss =='https://demo.909.care' || Auth::user()->iss =='https://909healthcare.com' || Auth::user()->iss =='https://909.care' || Auth::user()->iss =='https://mrs.909.care')
        <!-- <img src="{{ asset('dist/img/MRS_New_Logo.png') }}" alt="#" class="brand-image elevation-6" style="max-height: 55px;
    max-width: 150px;
    margin-left: -3px;
    margin-top: -15px;"> --> 
     <img src="{{ asset('dist/img/mg.png') }}"  alt="#"   
     style="margin-left: -3px; 
     margin-top: -15px;" class="">   
        @elseif( Auth::user()->email =='demouser1@kairosrp.com') 
        @else
          @if (isset($logoImgPath) && $logoImgPath != "")
            @if(env('env_entity_id') == 1)
            <img src="{{ url($logoImgPath) }}" alt="#"  style="margin-left: 0px;margin-top: -3px;margin-bottom: -8px;" class="logo_img">
            @else
            <img src="{{ url($logoImgPath) }}" alt="#"  style="margin-left: 0px;margin-top: -3px;" class="logo_img">
            @endif
          @else
            @if(env('env_entity_id') == 1)
                <img src="{{ asset($logo) }}" alt="#"  style="margin-left: 0px;margin-top: -3px;margin-bottom: -8px" class="logo_img">
            @else
                <img src="{{ asset($logo) }}" alt="#"  style="margin-left: 0px;margin-top: -9px;" class="logo_img">
            @endif
          @endif
        @endif 
      </a>
      </li>
      <!--<li class="nav-item d-none d-sm-inline-block">
        <a href="index3.html" class="nav-link">Home</a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="#" class="nav-link">Contact</a>
      </li>-->
    </ul>
    <ul class="navbar-nav icon_nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>
    @if (isset($Client_Name_Title)) 
        <h3 class="clnt_ttl">{{$Client_Name_Title}}</h3>
    @endif
    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <!-- Messages Dropdown Menu -->
      <!-- Notifications Dropdown Menu -->
     <!-- @if( Auth::user()->is_admin =='1')
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="fas fa-cog"></i> Settings
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" style="min-width: 200px;">
          
          <a href="/users" class="dropdown-item">
            <i class="fas fa-user-edit mr-2"></i> Manage Users
          </a>
          <div class="dropdown-divider"></div>
          <a href="/elt" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> Matillion Settings
          </a>
          <div class="dropdown-divider"></div>
          <a href="/lookerSetting" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> Looker Settings
          </a>
      
      </li>
      @endif
      <li class="nav-item">
        <a class="nav-link" href="{{ route('logout') }}"
                                          onclick="if(confirm('Are you sure?')){event.preventDefault();
                                                        document.getElementById('logout-form').submit();}else return false;"><i
                class="fas fa-sign-out-alt"></i>
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
            
      </li>-->
      <li class="nav-item dropdown AccessBtn">
        <input type="checkbox" id="toggle-two" data-onstyle="dark" class="btn-sm"data-offstyle="light" data-style="border">
      </li>
      <li class="nav-item dropdown setting">
        <a class="nav-link" data-toggle="dropdown" href="#" style="padding-left: 10px !important;" title="Setting">
         <i class="fa fa-cog fa-2x stng" aria-hidden="true"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
              <div class="menu-item px-3">
              <div class="menu-content d-flex align-items-center px-3">
                          <!--begin::Avatar-->
                          <div class="symbol symbol-50px me-5">
                            @if (!empty($profile_pic))
                            <img alt="Logo" src="{{ $profile_pic }}">
                            @else
                            <div id="DefaultprofileImage" class="user-image img-circle elevation-2"></div>
                            @endif
                            <!-- <img alt="Logo" src="{{ $profile_pic }}"> -->
                          </div>
                          <!--end::Avatar-->
                          <!--begin::Username-->
                          <div class="d-flex flex-column">
                            <a href="/users/profile/{{ \app\Libraries\Helpers::encrypt_decrypt(auth()->user()->id, 'encrypt') }}" class="d-flex align-items-center fs-5">{{ Auth::user()->name }} {{ Auth::user()->last_name }}</a>
                            <div>&nbsp;&nbsp;&nbsp;&nbsp;{{ $user_details[0]->group_name }}</div>
                          </div>
                          <!--end::Username-->
                        </div>
                        </div>
              <div class="dropdown-divider"></div>
              @if ($accessData[0]->looker == 1)              
              <a href="/lookerSetting" class="dropdown-item">
                <i class="fas fa-chart-bar mr-2"></i> Looker    
                <!-- <i class="fas fa-chart-bar"></i>             -->
              </a>
              @endif
              @if (isset($accessData[0]->snowflake) && $accessData[0]->snowflake == 1 || isset($accessData[0]->matillion) && $accessData[0]->matillion == 1 )              
              <!-- <div class="dropdown-divider"></div> -->
              <a href="/snowflake" class="dropdown-item">
                <i class="fas fa-database mr-2"></i> Snowflake
              </a>
              @endif              
              @if ($accessData[0]->users == 1)
              <!-- <div class="dropdown-divider"></div> -->
              <a href="/users" class="dropdown-item">
                <i class="fas fa-user-circle mr-2"></i> Users
              </a>              
              @endif
              @if ($accessData[0]->group_module == 1)          
              <!-- <div class="dropdown-divider"></div> -->
              <a href="/group_master" class="dropdown-item">
                <i class="fas fa-users mr-2"></i> Group Master
              </a>  
              @endif 
              @if ($accessData[0]->group_module == 1)          
              <!-- <div class="dropdown-divider"></div> -->
              <a href="/groups" class="dropdown-item">
                <i class="fas fa-users mr-2"></i> Group User Mapping
              </a>  
              @endif 
                           
              @if ($accessData[0]->roles == 1) 
              <!-- <div class="dropdown-divider"></div> -->
              <a href="/roles" class="dropdown-item">
                <i class="fas fa-universal-access mr-2"></i> Roles
              </a>  
              @endif
              @if ($accessData[0]->clients == 1)
              <!-- <div class="dropdown-divider"></div> -->
              <a href="/clients" class="dropdown-item">
                <i class="fas fa-user-plus mr-2"></i> Clients
              </a>                
              @endif
              @if (isset($accessData[0]->phm) && $accessData[0]->phm == 1 || isset($accessData[0]->reports) && $accessData[0]->reports == 1)
              <!-- <div class="dropdown-divider"></div> -->
              <a href="/reports" class="dropdown-item">
                <i class="fas fa-file mr-2"></i> Reports
              </a> 
              @endif
              
              @if ($accessData[0]->generate_report == 1)
              <a href="/all_reports" class="dropdown-item">
                <i class="fas fa-file mr-2"></i> Generate Reports
              </a> 
              @endif
            <!--   @if ($accessData[0]->generate_report == 1)
              <a href="/PatientSummary_Reports" class="dropdown-item">
                <i class="fas fa-file mr-2"></i> Patient Summary Reports
              </a> 
              @endif -->
              



              <!--@if ($accessData[0]->invite_user == 1)
              <button class="btn btn-anch1" onclick="openuserModal()" type="submit"><i class="fas fa-user-circle mr-2"></i>  &nbsp;Invite User </button>
              <br/>
              @endif -->
                                              
              @if( Auth::user()->id ==20 || Auth::user()->id ==26 || Auth::user()->id ==34 || Auth::user()->id ==35 || Auth::user()->id ==38)
              <a href="/refresh_data" class="dropdown-item">
                <i class="fas fa-circle mr-2"></i> Refresh Data
              </a> 
              @endif
              <button class="btn btn-anch1" onclick="openuserModal()" type="submit"><i class="fas fa-user-circle mr-2"></i>  &nbsp;Invite User </button>
              </br>
              <button class="btn btn-anch" onclick="openModal()" type="submit"><i class="fas fa-info-circle"></i>   &nbsp;About </button>
              
        </div>
      </li>
      <li class="nav-item dropdown user-menu">
   
        @if( Auth::user()->user_from =='0')
          <a class="nav-link dropdown-toggle" data-toggle="dropdown" style="padding-left: 10px !important;" href="{{ route('logout') }}" title="logout" onclick="if(confirm('Are you sure?')){event.preventDefault();window.localStorage.removeItem('Access');
          document.getElementById('logout-form').submit();DeleteUnsavedImages()}else return false;"><i class="fas fa-power-off  mr-2 lgot"></i></a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
          </form>
        @endif
        <!-- <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <li class="user-header bg-primary">
            @if (!empty($profile_pic))
            <img src="{{ $profile_pic }}" class="img-circle elevation-2" alt="User Image">
            @else
            <div id="DefaultprofileImage1" class="img-circle elevation-2"></div>
            @endif

            <p>
              <a href="/users/profile/{{ \app\Libraries\Helpers::encrypt_decrypt(auth()->user()->id, 'encrypt') }}" class="linkAnch">
            {{ Auth::user()->name }} {{ Auth::user()->last_name }}
            </a>
              <small>{{ Auth::user()->email }}</small>
            </p>
          </li>
          
          <li class="user-body">
            <div class="row">
              @if ($accessData[0]->looker == 1)
              <div class="col-4 text-center">
                <a href="/lookerSetting">Looker</a>
              </div>
              @endif
             
              </div>
              <div id="Line"></div>
              <div class="row">
              @if ($accessData[0]->users == 1)
              <div class="col-3 text-center">
                <a href="/users">Users</a>
              </div>
              @endif
              @if ($accessData[0]->group_module == 1)
              <div class="col-3 text-center">
                <a href="/groups">Group</a>
              </div>
              @endif
              @if ($accessData[0]->roles == 1 || $accessData[0]->clients == 1)
              @if ($accessData[0]->roles == 1)
              <div class="col-3 text-center">
                <a href="/roles">Roles</a>
              </div>
              @endif
              @if ($accessData[0]->clients == 1)
              <div class="col-3 text-center">
                <a href="/clients">Clients</a>
              </div>
              @endif
              @endif
             
            </div>
          </li>
         
          <li class="user-footer">
         
          
            @if( Auth::user()->user_from =='0')
            <a class="btn btn-default btn-flat float-right" href="{{ route('logout') }}"
                                          onclick="if(confirm('Are you sure?')){event.preventDefault();
                                                        document.getElementById('logout-form').submit();DeleteUnsavedImages()}else return false;"><i
                class="fas fa-sign-out-alt"></i>
                                            {{ __('Logout') }}
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            @csrf
                                        </form>
                                        @endif
          </li>                                     
      
        </ul> -->
      </li>
      

    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) 
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="{{ asset('dist/img/user2-160x160.jpg') }}" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">{{ Auth::user()->name }} </a>
        </div>
      </div>-->
      <br/>
      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item has-treeview {{ $activeSubMenu == '0' ? '' : 'menu-open' }}">
            <a href="#" class="nav-link {{ $activeMenu == 1 ? 'active' : '' }}">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Clients
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
            
            @foreach($headerData as $header)
              <li class="nav-item">
                <a href="/home/{{ \app\Libraries\Helpers::encrypt_decrypt($header->folder_id, 'encrypt') }}/{{ \app\Libraries\Helpers::encrypt_decrypt($header->id, 'encrypt') }}" class="nav-link {{ $activeSubMenu == $header->id ? 'active' : '' }}">
                  <i class="far fa-circle nav-icon"></i>
                  <p>{{$header->folder_name}}</p>
                </a>
              </li>
            @endforeach
              <!--<li class="nav-item">
                <a href="/home" class="nav-link ">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Client 1</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="/home" class="nav-link">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Client 2</p>
                </a>
              </li>-->
            </ul>
          </li>
          @if( Auth::user()->is_admin =='1')
          <!--<li class="nav-item">
            <a href="/users" class="nav-link {{ $activeMenu == 2 ? 'active' : '' }}">
              <i class="nav-icon fas fa-user-edit"></i>
              <p>
                Users
                
              </p>
            </a>
          </li>-->
      @endif
          </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  
            @yield('content')
       

  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

  <!-- Main Footer -->
  
</div>
<!-- ./wrapper -->

<div class="modal fade mt-10" id="myModal" role="dialog">
  <div class="modal-dialog">
  
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <img src="{{ asset('dist/img/kairos_logo_doc.png') }}" alt="#" class='compLogo'>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body">
        <p id="mdlbdy">Version 3.6 (Release - Oct 2022)</p>
      </div>
      <div class="modal-footer">
        <p id="cyrt">&#169; Copyright 2022 Kairos research partners.All rights reserved.</p>
      </div>
    </div>
  </div>
</div>

<div class="modal fade mt-10" id="myuserModal" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
       <img src="{{ asset('dist/img/kairos_logo_doc.png') }}" alt="#" class='compLogo'>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <form  role="form" method="POST" action="" id="userform"> 
       @csrf 
      <div class="form-group" id="cont">
        <h6 style ="text-align:center;color:#007bff;"><b>Invite users to create account</b></h6>
        <label for="username">Enter Email ID <p style="font-weight: 100;">(max 5 emails allowed at a time, to be separated by comma)<p></label>
        <textarea name="useremail" class="form-control" placeholder="Enter Email" id="email" required></textarea>
        <br/>
        <div class="md-3">
        <input type="checkbox" id="groupcodecheck" name="groupcode"> <label for="groupcode" class="form-label">Group_Code</label>                                  
        <input type="text" class="form-control" name="groupcode" id="group_code" value="0000" Placeholder="group_code" >  
        </div>
        <br/>
        <input type="submit"  name="submit" value="submit"  class="btn btn-primary">
      </div>
     </form>

    </div>
    
  </div>
</div>

<!-- Session Timeout Model -->
<div class="modal fade" id="mymodal1" aria-hidden="true" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        
        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-header">                 

                    <h4 class="modal-title">Session Expire Warning</h4>
                </div>
                <div class="modal-body">
                 
                  
                  <div>Your session will expire in<b>
                    <span style="background-color:yellow;">05:</span>
                    <span id="time" style="background-color:yellow;">00</span>
                    <span >Minutes</span></b>
                  </div></br>
                  Do you want to extend/logoff session?
                  
                </div>

                <div class="modal-footer">

                     <!-- <button id="staylogin" type="button" class="btn btn-default" style="padding: 6px 12px; margin-bottom: 0; font-size: 14px; font-weight: normal; border: 1px solid transparent; border-radius: 4px;  background-color: #428bca; color: #FFF;" onClick="hidepopup();">Extend</button>  -->
                      
                    <a id="keep" href="#" class="nav-link">Extend</a>
                     <a class="nav-link" data-toggle="dropdown" style="padding-left: 10px !important;" href="{{ route('logout') }}" title="logout" onclick="if(confirm('Are you sure?')){event.preventDefault();
                       document.getElementById('logout-form').submit();DeleteUnsavedImages()}else return false;">Logout</a>

                </div>

            </div>

        </div>

    </div>

<!--     <div id="timeOutWarningOverlay">
    <div style="height:auto; width:400px; background-color:white; position: fixed;top: 50%;left: 50%; -webkit-transform: translate(-50%, -50%);transform: translate(-50%, -50%);padding:10px; text-align:center;"> 
        <div>
            <b>Your session is about to expire. Please click button below to keep this session current.</b>
            <b><span style="background-color:yellow;">00:</span><span id="time" style="background-color:yellow;">59</span></b>
        </div>
        <a id="keep" href="#">Keep Me Logged In</a>
    </div>
</div>  -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery asset('dist/css/adminlte.min.css') }}-->
<!-- Bootstrap defer -->
<script src="{{ asset('js/app.js') }}" ></script>
<script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- overlayScrollbars -->
<script src="{{ asset('plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('dist/js/adminlte.js') }}"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="{{ asset('dist/js/demo.js') }}"></script>

<!-- PAGE PLUGINS -->
<!-- jQuery Mapael -->
<script src="{{ asset('plugins/jquery-mousewheel/jquery.mousewheel.js') }}"></script>
<script src="{{ asset('plugins/raphael/raphael.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-mapael/jquery.mapael.min.js') }}"></script>
<script src="{{ asset('plugins/jquery-mapael/maps/usa_states.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
 <script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
 
 <script src="https://unpkg.com/sweetalert2@7.18.0/dist/sweetalert2.all.js"></script>
 <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script>
 <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script type="text/javascript">
  $(function () {
  bsCustomFileInput.init();
});
</script>
<script type="text/javascript">
    $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
  function DeleteUnsavedImages(){
       // alert("closed11");
       $.ajax({
           type:'POST',
           url:'/home/deleteLookerUser',
           data:{user:"usr"},
           success:function(data){
              console.log(data);
           }
        });
   }
  $(document).ready(function(){
    var Access = window.localStorage.getItem('Access');
    // alert(Access);
  if(Access == "true"){
    $('#toggle-two').attr('checked', true);
  }

  var chkimg = "<?php echo $profile_pic;?>";
  var firstName = "<?php echo Auth::user()->name;?>";
  var lastName = "<?php echo Auth::user()->last_name;?>";
  var intials = firstName.charAt(0) + lastName.charAt(0);
  if(chkimg == "")
  {
  var profileImage = $('#DefaultprofileImage').text(intials);    
  var profileImage = $('#DefaultprofileImage1').text(intials);    
  }
}); 
  function openModal(id){
    $('#myModal').modal();
} 

function openuserModal(id){
    $('#myuserModal').modal();
}

function validateEmail(field) {
    var regex=/\b[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b/i;
    return (regex.test(field)) ? true : false;
}

 $('#userform').submit(function(e){
  var inputs = document.getElementById("email").value;
  var groups = document.getElementById('group_code').value;
   //alert(inputs);
   var flag = 0;
    var result = inputs.split(",");
    for(var i = 0; i < result.length;i++){
       
       if(!validateEmail(result[i])) 
          flag ++;
           e.preventDefault() 
          // return false;
     }
    if(flag > 0){
    alert('Invalid Email ID');
    return true;
    }else{
      swal({    
                text: "Users Invited successfully!",
                confirmButtonText: "Ok",    
        });
      $.ajax({
           type:'POST',
           url:'/storeuser',
           data:{useremail:inputs,groupcode:groups}, 
           success:function(data){ 
            if (data.success === true) {
                  swal("Ok", data.message, "success");
               } else {  
                          
              }
             //alert('Users Invited successfully');
             location.reload();
             
            
          }, 
        });
      }
 }); 


//  $('#userform').on('submit',function(e){
//  e.preventDefault();

//  var useremail = $('#email').val();
 
//   $.ajax({
//            type:'POST',
//            url:'/storeuser',
//            data:{useremail:useremail}, 
//            success:function(data){ 
//            //console.log(data); 
//              alert('Users Invited successfully');
//               location.reload();
//             //$("#contactForm")[0].reset(); 
            
//           }, 
//         });
//  });


// const timeout = 120000;  // 900000 ms = 15 minutes
// const timeout1 = 60000;  // 900000 ms = 15 minutes
// var idleTimer = null;
// var idleTimer1 = null;
//     $('*').bind('mousemove click mouseup mousedown keydown keypress keyup submit change mouseenter scroll resize dblclick', function () {
//         clearTimeout(idleTimer);

//         idleTimer1 = setTimeout(function () {
//             $('#mymodal1').modal('show');
//             // var timer2 = "2:01";
//             // var interval = setInterval(function() {

//             //   var timer = timer2.split(':');
//             //   //by parsing integer, I avoid all extra string processing
//             //   var minutes = parseInt(timer[0], 10);
//             //   var seconds = parseInt(timer[1], 10);
//             //   --seconds;
//             //   minutes = (seconds < 0) ? --minutes : minutes;
//             //   if (minutes < 0) clearInterval(interval);
//             //   seconds = (seconds < 0) ? 59 : seconds;
//             //   seconds = (seconds < 10) ? '0' + seconds : seconds;
//             //   //minutes = (minutes < 10) ?  minutes : minutes;
//             //   $('.countdown').html(minutes + ':' + seconds);
//             //   timer2 = minutes + ':' + seconds;
//             // }, 1000);
//         }, timeout1);
//         idleTimer = setTimeout(function () {
//             $('#mymodal1').modal('hide');
//             document.getElementById('logout-form').submit();
//         }, timeout);
//     });
//     $("body").trigger("mousemove");



// function hidepopup(){
//     // window.location.reload();
//         $('#mymodal1').modal('hide');
//         clearTimeout(idleTimer);
//         clearTimeout(idleTimer1);
//         timeout1 = 60000;
//     }
    var counter = 300;
    var idleTime = 0;
    var countdown;
    $(document).ready(function () {

        $(window).click(function () {
            console.log("click has occured");
            idleTime = 0;
        })
        $(window).keyup(function () {
            console.log("key up has occured")
            idleTime = 0;
        })

        //Increment the idle time counter every minute.
        var idleInterval = setInterval(timerIncrement, 60000); //found

        $('#keep').click(function () {
            idleTime = 0;
            // $('#mymodal1').hide();
            $('#mymodal1').modal('hide');
        });

    });

    function timerIncrement() {
        idleTime = idleTime + 1;
        console.log(idleTime);
        if (idleTime > 24) //13
        {
            // $('#mymodal1').show();
            $('#mymodal1').modal('show');
            // startTimer();
        }
        if (idleTime > 28) { // 14 
           DeleteUnsavedImages();
           window.localStorage.removeItem('Access');
        }
        if (idleTime > 29) { // 14 
            window.localStorage.removeItem('Access');
           document.getElementById('logout-form').submit();
        }
    };


    function startTimer() {
        countdown = setInterval(countDownClock, 1000);
    };
    
    function countDownClock() {
        counter = counter - 1
        if (counter < 10) {
            console.log(counter);
            $('#time').text("0" + counter);
        }
        else {
            console.log(counter);
            $('#time').text(counter);
        }
        if (counter == 0) {
            counter = 60;
            clearInterval(countdown);
            console.log(counter);
            console.log("done");
        }
    };
    
    //Invite user checkbox

    $(function(){
        $('#group_code').hide();
        $("#groupcodecheck").click(function () {
            if ($(this).is(":checked")) {
                $("#group_code").show();
            } else {
                $("#group_code").hide();
            }
        });
    });

    $(function() {
        $('#toggle-two').bootstrapToggle({
          on: 'Explorer',
          off: 'Viewer',
          height: 10,
          width: 90
        });
        $('#toggle-two').on('change', function(){
        var user_id = "<?php echo Auth::user()->id;?>";
        var is_checked = $(this).is(':checked');
        window.localStorage.setItem("Access",is_checked);
            $.ajax({
               type:'POST',
               url:'/FreePullTable',
               data:{user_id:user_id,access:is_checked}, 
               success:function(data){ 
              }, 
            });
        });
      })
</script>
<!-- PAGE SCRIPTS 
<script src="dist/js/pages/dashboard2.js"></script>-->
</body>
</html>
