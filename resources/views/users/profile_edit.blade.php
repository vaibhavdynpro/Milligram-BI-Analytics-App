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
<script type="text/javascript">
    $(document).ready(function() {
            $("#file").change(function () {
                var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
                if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                    alert("Only formats are allowed : "+fileExtension.join(', '));
                    $(':input[type="submit"]').prop('disabled', true);
                }
                else
                {
                    $(':input[type="submit"]').prop('disabled', false);
                }
            });
        });
</script>
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
.btns {
    padding-top: 62px;
}
a.btn.btn-default.float-right.cnl {
    background-color: #6f7274;
    color: white;
}
#prof_img{
    display: none;
    margin-left: 15%;
}
#DefaultImg {
    width: 70%;
    height: 100%;
    background: #1a2058;
    font-size: 55px;
    color: #fff;
    text-align: center;
    line-height: 93px;
    margin-left: 15%;
}
.dropdown-menu.dropdown-menu-lg.dropdown-menu-right.show {
    min-width: 282px;
}
</style>
<section class="content">
<div class="container emp-profile">
            <form role="form" method="post" action="{{ route('updateProfile',[$id]) }}" enctype="multipart/form-data">
                   @csrf
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
                                echo '<img src="" alt="" id="prof_img"/>';
                                echo '<div id="DefaultImg"></div>';
                            }
                            ?>
                            
                            <div class="file btn btn-lg btn-primary">
                                Change Photo
                                <input type="file" name="file" id="file" onchange="loadFile(event)"/>
                            </div>
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
                                        
                                        <div class="row pb-2">
                                            <div class="col-md-6">
                                                <label>First Name</label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="first_name" required name="first_name" value="{{$userData[0]->name}}" placeholder="Enter First Name">
                                            </div>
                                        </div>
                                        <div class="row pb-2">
                                            <div class="col-md-6">
                                                <label>Last Name</label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="text" class="form-control" id="last_name" required name="last_name" value="{{$userData[0]->last_name}}" placeholder="Enter First Name">
                                            </div>
                                        </div>
                                        <div class="row pb-2">
                                            <div class="col-md-6">
                                                <label>Email</label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="email" class="form-control" id="email" name="email" required value="{{$userData[0]->email}}" placeholder="Enter email">
                                            </div>
                                        </div>
                                        
                                        <div class="row pb-2">
                                            <div class="col-md-6">
                                                <label>Phone</label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="number" class="form-control" id="contact" name="contact" value="{{$userData[0]->contact}}" placeholder="Enter Contact No.">
                                            </div>
                                        </div>
                                     
                            </div>
                       
                        </div>
                        <div class="btns">
                          <button type="submit" class="btn btn-primary">Submit</button>
                          <a href="/users/profile/{{ \app\Libraries\Helpers::encrypt_decrypt($id, 'encrypt') }}" class="btn btn-default float-right cnl">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>           
        </div>
      </section>
      @endsection
      <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
        <script type="text/javascript">
        	var loadFile = function (event) {
				  var image = document.getElementById("prof_img");
				  image.src = URL.createObjectURL(event.target.files[0]);
                  $('#prof_img').css('display','block');
                  $('#DefaultImg').css('display','none');
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