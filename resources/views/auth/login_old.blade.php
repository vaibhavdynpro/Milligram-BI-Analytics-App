
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Healthcare Analytics</title>
  <link rel = "icon" href="{{ asset('dist/img/magnifying_logo.jpg') }}"type = "image/x-icon"> 
  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">
    <!-- icheck bootstrap -->
    <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="../../dist/css/adminlte.min.css">
  {!! NoCaptcha::renderJs() !!}
  <style>
        .input-group-text{
    cursor:pointer;
    }
    
    .input-group-text:hover .fa-lock:before{
    content:"\f09c";
    }
    img.loginlogo {
    height: 83px;
    margin-bottom: 20px;
    margin-left: 22%;
    margin-top: 5%;
  }
  img.loginlogo1 {
    height: 83px;
    margin-bottom: 20px;
    margin-left: 8%;
    margin-top: 5%;
  }
    </style>

</head>
<body class="hold-transition login-page" style="background-color:white;min-height: 459px;
    background-image: url(dist/img/bg1.jpg);
    background-size: cover;">

<div class="login-box">

  <!-- /.login-logo -->
  <div class="card">
    <div>
      <!-- <a href="../../index2.html" class="h1"><b>Admin</b>LTE</a> -->
      @if(env('env_entity_id') == 1)
      <img src="{{ asset('dist/img/kairos_logo_doc.png') }}" class="loginlogo">
      @else
      <img src="{{ asset('dist/img/M.R.S. Analytics_Kairos_logo.png') }}" class="loginlogo1">
      @endif
    </div>
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>
      @if ($errors->has('g-recaptcha-response'))
          <span class="help-block">
              <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
          </span>
      @endif
      <form method="POST" action="{{ route('Manuallogin') }}">
      @csrf
        <div class="input-group mb-3">
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
          @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
          @enderror
        </div>
        <div class="input-group mb-3">
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
         
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock" onclick="myFunction()"></span>
            </div>
          </div>
          @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
          @enderror
        </div>
        <div class="form-group mt-4 mb-4">
            <div class="captcha">
                {!! NoCaptcha::display() !!}
            </div>
        </div>

        <div class="form-group mb-4">
            <!-- <input id="captcha" type="text" class="form-control" placeholder="Enter Captcha" name="captcha"> -->
        </div>

        <div class="row">
          <div class="col-8">
          <p class="mb-1">
      @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">Forgot Password?</a>
        @endif
      </p>
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>

      <!-- <div class="social-auth-links text-center mt-2 mb-3">
        <a href="#" class="btn btn-block btn-primary">
          <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
        </a>
        <a href="#" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
        </a>
      </div> -->
      <!-- /.social-auth-links -->

      
      <!-- <p class="mb-0">
        <a href="register.html" class="text-center">Register a new membership</a>
      </p> -->
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- jQuery -->
<script src="../../plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="../../dist/js/adminlte.min.js"></script>
<script>
  $( document ).ready(function() {
  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
   $('#reload').click(function () {
    var token = "{{csrf_token()}}"
        $.ajax({
            type: 'GET',
            url: 'reload-captcha',
            success: function (data) {
                $(".captcha span").html(data.captcha);
            }
        });
    });
  });


function myFunction() {
  var x = document.getElementById("password");
  if (x.type === "password") {
    x.type = "text";
  } else {
    x.type = "password";
  }
}

</script>
</body>
</html>
