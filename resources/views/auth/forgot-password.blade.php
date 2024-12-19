

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
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
    } 
    </style>
    </head>
<body class="hold-transition login-page" style="background-color:white">

<div>
      <!-- <a href="../../index2.html" class="h1"><b>Admin</b>LTE</a> -->
      <!-- <img src="{{ asset('dist/img/M.R.S. Analytics_Kairos_logo.png') }}" class="loginlogo"> -->
      @if(env('env_entity_id') == 1)
      <img src="{{ asset('dist/img/kairos_logo_doc.png') }}" class="loginlogo">
      @else
      <img src="{{ asset('dist/img/M.R.S. Analytics_Kairos_logo.png') }}" class="loginlogo">
      @endif
    </div>
<div class="login-box">

  <!-- /.login-logo -->
  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">You forgot your password? Here you can easily retrieve a new password.</p>

      <form action="{{ route('sendresetrequest') }}" method="post">
      @csrf
        <div class="input-group mb-3">
          <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name='email_id' placeholder="Email">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-envelope"></span>
            </div>
          </div>
         
          
        </div>
        @if (\Session::has('failed'))
          <span class="row" style="color:red;font-size: small;margin-left: 1px;" role="alert">
                <p>{!! \Session::get('failed') !!}</p>
            </span>
          @endif
        <div class="row">
          <div class="col-12">
            <button type="submit" class="btn btn-primary btn-block">Send OTP</button>
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

</script>
</body>
</html>
