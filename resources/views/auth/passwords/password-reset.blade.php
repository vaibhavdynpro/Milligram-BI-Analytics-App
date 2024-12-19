
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kairos</title>
  <link rel = "icon" href="{{ asset('dist/img/magnifying_logo.jpg') }}"type = "image/x-icon"> 
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  <style>
        .input-group-text{
    cursor:pointer;
    }
    
    .input-group-text:hover .fa-key:before{
    content:"\f13e";
    }
    img.loginlogo {
    height: 83px;
    margin-bottom: 20px;
    } 
    </style>
    </head>
<body class="hold-transition login-page" style="background-color:white">
<!-- <div> -->
<div>
      <!-- <img src="{{ asset('dist/img/M.R.S. Analytics_Kairos_logo.png') }}" class="loginlogo"> -->
      @if(env('env_entity_id') == 1)
      <img src="{{ asset('dist/img/kairos_logo_doc.png') }}" class="loginlogo">
      @else
      <img src="{{ asset('dist/img/M.R.S. Analytics_Kairos_logo.png') }}" class="loginlogo">
      @endif
</div>
<div class="login-box">

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Please input the 6 digit OTP recieved</p>

      <form action="{{ route('update-password') }}" method="post">
      @csrf
      <div style="display: none;">
      <input type="text" name="user_id" id="user_id" value="{{$user_id}}"></input>
      </div>
        <div class="input-group mb-3">
          <input type="email" class="form-control" name='email_id' placeholder="Email" value="{{$email}}" readonly>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="text" class="form-control" id='otp' name='otp' placeholder="6-Digit-OTP" maxlength="6" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div> 
          </div>
         
        </div>
        @php 

          if(isset($failed) && $failed!='')
          {
              @endphp
          <span class="row" id='failed_msg' style="color:red;font-size: small;margin-left: 2px;" role="alert">
                <p>{{$failed}}</p>
                <!-- <p>dssdssddsd</p> -->
            </span>
            @php
        }
        
            @endphp
        <div class="input-group mb-3">
          <input type="password" class="form-control" id='password1' name='password1' placeholder="Please Enter New Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-key" onclick="myFunction()"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control" id='password2' name='password2' placeholder="Please Re-enter Password" required>
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-key" onclick="myFunction1()"></span>
            </div>
          </div> 
        </div>
        <span id="message"></span>
        <div class="row">
          <div class="col-12">
            <button id="submit" type="submit" class="btn btn-primary btn-block">Update Password</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <p class="mt-3 mb-1">
        <a href="/login">Login Instead?</a>
      </p>
      <!-- <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p> -->
    </div>
    <!-- /.login-card-body -->
  </div>
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>

<script>
$('#password2').on('keyup', function() {
  if ($('#password1').val() == $('#password2').val()) {
    $('#message').html('Matching').css('color', 'green');
    $('#submit').prop('disabled', false);
  } else {
    $('#message').html('Not Matching').css('color', 'red');
    $('#submit').prop('disabled', true);
  }
});

$("#otp").click(function(){
  $("#failed_msg").fadeOut();
});

function myFunction() {
  var x = document.getElementById("password1");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
function myFunction1() {
  var x = document.getElementById("password2");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}
</script>
</body>
</html>