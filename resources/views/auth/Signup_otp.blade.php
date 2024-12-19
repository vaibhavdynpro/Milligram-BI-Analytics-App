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
    <link rel="stylesheet" href="{{ asset('login_assets/css/sweetalert.css')}}">
    <link rel="stylesheet" href="{{ asset('login_assets/css/owl.carousel.min.css') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('login_assets/css/bootstrap.min.css') }}">
    
    <!-- Style -->
    <link rel="stylesheet" href="{{ asset('login_assets/css/style.css') }}">
  </head>

  <body>
  
  <div class="d-lg-flex half">
    <div class="bg order-1 order-md-2" style="background-color: #0169a6">
      <div class="container">
        <div class="row align-items-center justify-content-center">
         
          <div class="col-md-7 from" >
            
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
              <form  role="form" method="POST" action="{{ route('verifyotp') }}" id="SignupForm">
              		@csrf
                <div class="form-group last mb-3">
                    <label for="validate otp">Enter OTP</label>
                    <input type="text" class="form-control" placeholder="Enter-OTP" id="validateotp" name="validateotp"> 
                    <input type="hidden" class="form-control" placeholder="Email" id="email" name="validateemail" value="{{ \app\Libraries\Helpers::encrypt_decrypt(Request::segment(2), 'decrypt') }}"> 
                 </div>
                   <input type="submit" value="Submit-OTP" class="send-otp" id="signup_Submit">       
               </form>
                <div class="d-flex mb-6 ">
                    <span class="ml-auto">
                    <form action="{{ route('updateotp',[Request::segment(2)]) }}" method="post">
							        @csrf
                      <input type="hidden" name="email" value="{{ \app\Libraries\Helpers::encrypt_decrypt(Request::segment(2), 'decrypt') }}">
				              <button class="resend-otp btn btn-link"  type="submit">Resend-OTP</button>
		                </form>
                  </span> 
               </div>
              </div>  
          </div>
        </div>
      </div>
    </div>
    <div class="contents order-2 order-md-1">
      @if(env('env_entity_id') == 1)
      <img src="{{ asset('dist/img/kairos_logo_doc.png') }}" id="translogo">
      <p id="contentdesc">Kairos Research Partners is a health care analytics consulting company, which delivers sophisticated data analysis in order to generate evidence-based population health management solutions. Kairos utilizes a proprietary relational database and reporting suite with the ability to integrate, store, and analyze various data sets.</p>
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
  	});
    
    </script>
  </body>
</html>