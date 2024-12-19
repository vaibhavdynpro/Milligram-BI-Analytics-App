<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Healthcare Analytics</title>
    <link rel = "icon" href="{{ asset('dist/img/magnifying_logo.jpg') }}"type = "image/x-icon"> 
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('login_assets/fonts/icomoon/style.css') }}">
     <!-- Font Awesome -->
    <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('login_assets/css/owl.carousel.min.css') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('login_assets/css/bootstrap.min.css') }}">
    
    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('login_assets/css/style.css') }}">
    <link href="{{ asset('css/mg.css') }}" rel="stylesheet">
     {!! NoCaptcha::renderJs() !!}
    <style>
      .togglebtn{
        position: absolute;
        right: 80px;
        top: 157px;
      }
    </style> 
  </head>

  <body>
  

  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-color: #0169a6">
      <div class="container">
        <div class="row align-items-center justify-content-center">

          <div class="col-md-7 from rhs_login_form">
             
            <!-- <h3>Login to <strong>Colorlib</strong></h3>
            <p class="mb-4">Lorem ipsum dolor sit amet elit. Sapiente sit aut eos consectetur adipisicing.</p> -->
            
              <div id="loginForm" >
              	 @if ($errors->any())
			    <div class="alert alert-danger">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
				@endif
              <form method="POST" action="{{ route('Manuallogin') }}">
              		@csrf
                  <div class="form-group first">
                    <label for="username">Email</label>
                    <input type="text" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="your-email@gmail.com" id="username">
                  </div>
                  @error('email')
	                <span class="invalid-feedback" role="alert">
	                    <strong>{{ $message }}</strong>
	                </span>
	          	  @enderror
                  <div class="form-group last mb-3">
                    <label for="password">Password</label>
                     <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Your Password" id="password">
                     <span id="toggle_pwd" class="togglebtn fa fa-fw fa-eye field_icon"></span>
                  </div>
                  <div class="form-group last mb-3">
                    {!! NoCaptcha::display() !!}
                  </div>
                  @if ($errors->has('g-recaptcha-response'))
			          <span class="help-block">
			              <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
			          </span>
			      @endif
                  <div class="d-flex mb-5 align-items-center">
                    <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                      <input type="checkbox" checked="checked" name="remember_me" id="remember_me"/>
                      <div class="control__indicator"></div>
                    </label>
                    <span class="ml-auto">
                    	@if (Route::has('password.request'))
                    	<a href="{{ route('password.request') }}" class="forgot-pass">Forgot Password</a>        				
        				@endif
                    </span> 
                  </div>
                  <input type="submit" value="Log In" class="btn btn-block btn-primary">
                  <a class="btn btn-block btn-primary signup_btn" href="{{ route('signup') }}">Sign Up</a>
              	</form>
              </div>
                        
          </div>
        </div>
      </div>
    </div>
      <div class="contents order-2 order-md-1">
      @if(env('env_entity_id') == 1)
      <img src="{{ asset('dist/img/kairos_logo_doc.png') }}" id="translogo">
      <p id="contentdesc">Kairos Research Partners is a health care analytics consulting company, which delivers sophisticated data analysis in order to generate evidence-based population health management solutions. Kairos utilizes a proprietary relational database and reporting suite with the ability to integrate, store, and analyze various data sets.</p>
      @elseif(env('env_entity_id') == 3)
      <img src="{{ asset('dist/img/mg.png') }}" id="translogo">
      <p id="contentdesc">We use the best search feature and capability to find the lowest and best prices for your medications.</p>
      <p id="contentdesc">Milligram was created as a web-based medication price transparency engine. Our software helps patients and providers find medication at the lowest price in any given zip code. Our translations support English, Spanish, Tagalog and Chinese. With our three data partners, Milligram searches prices of medications nationwide at 65,000+ pharmacies, offering the lowest prices on the internet.</p>
      @else
      <img src="{{ asset('dist/img/M.R.S. Analytics_Kairos_logo.png') }}" id="translogo">
      <h2 id="Tagline">Predictive Analytics with Prescription Drug Management</h2>
      <p id="contentdesc1">The M.R.S. analytics system intakes ongoing medical and pharmacy data to provide year-round risk surveillance of the employer population, in order to intercept risk migration and act promptly. The M.R.S. analytics system utilizes pre-established algorithms in combination with supervised learning AI (Artificial Intelligence) to segregate the “at risk” population. Financial savings and condition stabilization will be the targeted dependent variables for actuarial and outcomes reports.</p>
      
      @endif  
      
    </div>

    
  </div>
   
    <script src="{{ asset('login_assets/js/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('login_assets/js/popper.min.js') }}"></script>
    <script src="{{ asset('login_assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('login_assets/js/main.js') }}"></script>
    <script type="text/javascript">
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
     	
    $(function () {
            $("#toggle_pwd").click(function () {
                $(this).toggleClass("fa-eye fa-eye-slash");
               var type = $(this).hasClass("fa-eye-slash") ? "text" : "password";
                $("#password").attr("type", type);
            });
        });
    </script>
  </body>
</html>