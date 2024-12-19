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
    <!--sweet alert-->
    <link rel="stylesheet" href="{{ asset('login_assets/css/sweetalert.css')}}">
    <link rel="stylesheet" href="{{ asset('login_assets/css/owl.carousel.min.css') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('login_assets/css/bootstrap.min.css') }}">
    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('login_assets/css/style.css') }}">
    <link href="{{ asset('css/mg.css') }}" rel="stylesheet">
     {!! NoCaptcha::renderJs() !!}
  </head>
  
  <body>
  
  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-color: #0169a6">
      <div class="container">
        <div class="row align-items-center justify-content-center">

          <div class="col-md-7 from rhs_login_form" >
           
            <!-- <h3>Login to <strong>Colorlib</strong></h3>
            <p class="mb-4">Lorem ipsum dolor sit amet elit. Sapiente sit aut eos consectetur adipisicing.</p> -->
              <!-- @if (\Session::has('success'))           
              <div class="alert alert-success alert-dismissible col-xl-12 col-lg-12 col-md-12 col-sm-10 mx-auto" role="alert" >
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                {!! \Session::get('success') !!}
              </div>
              @endif -->  
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
              <div id="SignupForm">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                  @endif
              <form  role="form" method="POST" action="{{ route('storeotp') }}" id="SignupForm">
              		@csrf
                  <div class="form-group last mb-3">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" placeholder="First Name" id="firstname" name="firstname" value="{{old('firstname')}}">                    
                  </div>
                  <div class="form-group last mb-3">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" placeholder="Last Name" id="lastname" name="lastname" value="{{old('lastname')}}">                
                  </div>                
                  <div class="form-group last mb-3">
                    <label for="email">Email</label>
                    <input type="text" class="form-control" placeholder="Email" id="email" name="email" value="{{old('email')}}">
                  </div>
                  <div class="form-group last mb-3">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Your Password" id="password" value="{{old('password')}}">
                  </div>
                  <div class="form-group last mb-3">
                    <label for="conpassword">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" placeholder="Confirm Password" id="confirm_password" value="{{old('confirm_password')}}">
                  </div>
                  <div class="form-group last mb-3">
                    <label for="group_code">Group Code</label>
                    <input type="text" class="form-control" placeholder="Group Code" name="group_code" id="group_code" value="{{old('group_code')}}" autocomplete="off">
                  </div>
                  <input type="submit" value="Sign Up" class="btn btn-primary  signup_btn1" id="signup_Submit">
                  <a class="btn btn-primary login_btn1" href="{{ route('login') }}">Log In</a>
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
    <script src="{{ asset('login_assets/js/sweetalert.all.min.js') }}"></script>
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
  
  
  </script>
   
  </body>
</html>