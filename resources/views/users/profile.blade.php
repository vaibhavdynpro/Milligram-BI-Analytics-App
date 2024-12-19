@extends('layouts.app')

@section('content')
<?php 
// echo "<pre>";
// print_r($headerData);
// exit();
?>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="{{ asset('login_assets/css/sweetalert.css')}}">
<!------ Include the above in your HEAD tag ---------->
<style type="text/css">
nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 100%;
    margin-top: -61px;
    }
.emp-profile{
    padding: 3%;
    margin-top: 3%;
    margin-bottom: 3%;
    border-radius: 0.5rem;
    background: #fff;
        background-color: #f3f4ff;
}
.profile-img{
    text-align: center;
}
.profile-img img{
    width: 70%;
    height: 100%;
}
.profile-img .file {
    position: relative;
    overflow: hidden;
    margin-top: -20%;
    width: 70%;
    border: none;
    border-radius: 0;
    font-size: 15px;
    background: #212529b8;
}
.profile-img .file input {
    position: absolute;
    opacity: 0;
    right: 0;
    top: 0;
}
.profile-head h5{
    color: #333;
}
.profile-head h6{
    color: #0062cc;
}
.profile-edit-btn{
    border: none;
    border-radius: 1.5rem;
    width: 70%;
    padding: 2%;
    font-weight: 600;
    color: #6c757d;
    cursor: pointer;
}
.proile-rating{
    font-size: 12px;
    color: #818182;
    margin-top: 5%;
}
.proile-rating span{
    color: #495057;
    font-size: 15px;
    font-weight: 600;
}
.profile-head .nav-tabs{
    margin-bottom:5%;
}
.profile-head .nav-tabs .nav-link{
    font-weight:600;
    border: none;
}
.profile-head .nav-tabs .nav-link.active{
    border: none;
    border-bottom:2px solid #0062cc;
}
.profile-work{
    padding: 14%;
    margin-top: -1%;
}
.profile-work p{
    font-size: 12px;
    color: #818182;
    font-weight: 600;
    margin-top: 10%;
}
.profile-work a{
    text-decoration: none;
    color: #495057;
    font-weight: 600;
    font-size: 14px;
}
.profile-work ul{
    list-style: none;
}
.profile-tab label{
    font-weight: 600;
}
.profile-tab p{
    font-weight: 600;
    color: #0062cc;
}
a.wrlLnk:hover {
    font-size: 16px;
    color: #0062cc;
}
a.wrlLnk {
    font-size: 16px;
}
i.fas.fa-bars {
    display: none;
	}
    a.profile-edit-btn.btn.chgPass {
    width: 171px;
    background-color: #001f3f;
    color: white;
    border: 2px solid #1fa8e0;
    cursor: pointer;
}
a.profile-edit-btn.btn.chgPass:hover {
    width: 171px;
    background-color: #001f3f;
    color: #e3ccc9;
    border: 2px solid #1fa8e0;
    cursor: pointer;
}
a.profile-edit-btn.btn {
    width: 171px;
    background-color: #001f3f;
    color: white;
    border: 2px solid #1fa8e0;
    cursor: pointer;
    margin-bottom: 10px;
}
a.profile-edit-btn.btn:hover {
    width: 171px;
    background-color: #001f3f;
    color: e3ccc9;
    border: 2px solid #1fa8e0;
    cursor: pointer;
    margin-bottom: 10px;
}
#DefaultImg {
    width: 70%;
    height: 100%;
    background: #1a2058;
    font-size: 55px;
    color: #fff;
    text-align: center;
    line-height: 141px;
}
.dropdown-menu.dropdown-menu-lg.dropdown-menu-right.show {
    min-width: 282px;
}
</style>
<section class="content">
<div class="container emp-profile">
            @if(Session::has('success'))
                    <script>
                            window.addEventListener('load',function(){
                                    Swal.fire({
                                        icon: 'success',
                                        text: '{{ Session::get("success") }}'
                                    
                                });
                            });
                    </script>
              @endif
            <form method="post">
                <div class="row">
                    <div class="col-md-4">
                        <div class="profile-img">
                            <?php
                            if(isset($logopath) && !empty($logopath))
                            {
                                echo '<img src="'.$logopath.'" alt="" id="prof_img"/>';
                            }
                            else
                            {
                                // echo '<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS52y5aInsxSm31CvHOFHWujqUx_wWTS9iM6s7BAm21oEN_RiGoog" alt="" id="prof_img"/>';
                                echo '<div id="DefaultImg"></div>';
                            }
                            ?>
                            <!-- <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS52y5aInsxSm31CvHOFHWujqUx_wWTS9iM6s7BAm21oEN_RiGoog" alt="" id="prof_img"/> -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="profile-head">
                                    <h5>
                                        {{$userData[0]->name}} {{$userData[0]->last_name}}
                                    </h5>
                                    
                                    <p class="proile-rating"><span></span></p>
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">About</a>
                                </li>
                                <!-- <li class="nav-item">
                                    <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Timeline</a>
                                </li> -->
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <!-- <input type="submit" class="profile-edit-btn" name="btnAddMore" value="Edit Profile"/> -->
                        <a href="/users/profileEdit/{{ \app\Libraries\Helpers::encrypt_decrypt($userData[0]->id, 'encrypt') }}" class="profile-edit-btn btn">Edit Profile</a><br/>
                        <a href="{{ route('password.change') }}" class="profile-edit-btn btn chgPass">Reset Password</a>
                    </div>
            
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="profile-work">
                            <p>WORK LINK</p>
                            @foreach($headerData as $header)
								                <a href="/home/{{ \app\Libraries\Helpers::encrypt_decrypt($header->folder_id, 'encrypt') }}/{{ \app\Libraries\Helpers::encrypt_decrypt($header->id, 'encrypt') }}" class="wrlLnk">
								                  {{$header->folder_name}}
								                </a><br/>
								            @endforeach
                    
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="tab-content profile-tab" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Name</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>{{$userData[0]->name}} {{$userData[0]->last_name}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Email</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>{{$userData[0]->email}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Group</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>{{$userData[0]->group_name}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Role</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>{{$userData[0]->role_name}}</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Phone</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>{{$userData[0]->contact}}</p>
                                            </div>
                                        </div>
                                        <!-- <div class="row">
                                            <div class="col-md-6">
                                                <label>Permission</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>{{$userData[0]->permissions}}</p>
                                            </div>
                                        </div> -->
                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Experience</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>Expert</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Hourly Rate</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>10$/hr</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Total Projects</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>230</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>English Level</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>Expert</p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Availability</label>
                                            </div>
                                            <div class="col-md-6">
                                                <p>6 months</p>
                                            </div>
                                        </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <label>Your Bio</label><br/>
                                        <p>Your detail description</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>           
        </div>
      </section>
      @endsection
      <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
      <!-- sweet alert-->
      <script src="{{ asset('login_assets/js/sweetalert.all.min.js') }}"></script>
      
        <script type="text/javascript">
        	var loadFile = function (event) {
				  var image = document.getElementById("prof_img");
				  image.src = URL.createObjectURL(event.target.files[0]);
				};
            $(document).ready(function(){
                var chkimg = "<?php echo $logopath;?>";
                var firstName = "<?php echo $userData[0]->name;?>";
                var lastName = "<?php echo $userData[0]->last_name;?>";
                var intials = firstName.charAt(0) + lastName.charAt(0);
                if(chkimg == "")
                {
                var profileImage = $('#DefaultImg').text(intials);  
                }
            }); 
        </script>