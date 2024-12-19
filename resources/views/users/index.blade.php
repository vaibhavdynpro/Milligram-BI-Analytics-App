@extends('layouts.app')

@section('content')

<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('login_assets/css/sweetalert.css')}}">
  <!-- Theme style -->
  <style type="text/css">
  	nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 100%;
	}
	i.fas.fa-bars {
    display: none;
	}
	.card-body {
    margin-top: 6rem;
    }

  #exTab2 ul li {
	color : white;
	padding: 4px 1px;
    margin: 3px;
	
	}
	#exTab2 .nav-tabs > li > a {
	border-radius: 0;
	}
	.nav-tabs > li > a{
    padding: 8px 24px;
	color:#FFFFFF;
    margin-bottom: -2px !important;
    border-bottom: none;
	background-color:#808080;
	border: none;
	}
  #exTab2 .nav-tabs > li > a.targetactive{
		background-color:#007bff;
		color:#fff;
  }
  .nav-tabs {
    border-bottom: 0px solid #dee2e6;
}
	/*div#\31 {
    overflow-y: scroll;
    height: 600px;
    
  }*/
  @media screen and (max-width: 600px) {
  .topnav{overflow: hidden;
    background-color: #f5f5f5;
    margin-top: 21px;
    margin-left: 0px;
    width: 100%;}
    i.fas.fa-bars {
    display: block;
    margin-left: -150px;
	}
	.card-body {
      margin-top: 0rem;
	}
}
@media screen and (max-width: 600px){
	nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 100%;
	cursor: pointer;
}
nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 97%;
	}
.navbar-expand .navbar-nav .nav-link{
	padding-right: 1rem;
    padding-left: 0rem;
}

	i.fas.fa-bars {
    display: block;
    margin-left: auto;
}
a.btn.btn-primary.float-sm-right {
	margin-top: -23px;
    float: right;
	margin-right: 0rem;
}
.nav-tabs > li > a {
    padding: 5px 8px;
    color: #FFFFFF;
    margin-bottom: -2px !important;
    border-bottom: none;
    background-color: #808080;
    border: none;
}
}
	
  </style>
<section class="content">

<div class="row">
	<div class="col-12">
	<!-- Content Wrapper. Contains page content -->
		<div class="content-wrapper">
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
			
			
		<div class="card">
			<div class="card-header" style="position: fixed;width: 95%;z-index: 999;background-color: white;">
				<div class="row">
				  <div class="col-sm-8">
				  	<h2 class="card-title">Users List</h2>
				  </div>
					  @if ($accessData[0]->user_add == 1)
						  <div class="col-sm-4">
						  <a href="/users/add" class="btn btn-primary float-sm-right" >Create</a>
						  </div> 
					  @endif
				</div>
				
				<div id="exTab2" class="container1">	
					<ul class="nav nav-tabs">
						<li class="active">
							<a href="#1" data-toggle="tab" class="targetactive">Active</a>
						</li>
						<li>
							<a href="#2" data-toggle="tab" class="target">Approval Pending </a>
						</li>
					</ul>
				</div>
			</div>
			<div class="tab-content">
				<div class="tab-pane active" id="1">
				  <div class="card-body">
					 <table id="example2" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Sr.No.</th>
								<th>Name</th>
								<th>Group</th>
								<th>Role</th>
								<th>Email/User Id</th>
								<th>Action</th>    
							</tr>
						</thead>
					  <tbody>
					       @php
								 $count=1;
									@endphp
										@foreach($userData as $user)
										<tr>
											<td>@php echo $count; @endphp</td>
											<td>{{$user->name}} {{$user->last_name}}</td>
											<td>{{$user->group_name}}</td>
											<td>{{$user->role_name}}</td>
											<td>{{$user->email}}</td>
											<td>
											<div class="row">
												@if ($accessData[0]->user_edit == 1)
												<div class="col-md-2">
													<a href="users/edit/{{ \app\Libraries\Helpers::encrypt_decrypt($user->id, 'encrypt') }}" class=" badge btn btn-light" ><i class="fas fa-edit"></i> </a>
												</div>
												@endif
												@if ($accessData[0]->user_delete == 1)
												<div class="col-md-2">
												<form action="{{ route('deleteUser',[$user->id]) }}" method="post">
												@csrf
												<input name="_method" type="hidden" value="DELETE">
												<button class="badge btn btn-light" onclick="return confirm('Are you sure?')" type="submit"><i class="fas fa-trash"></i> </button>
												</form>
												</div>
											  @endif
											  <?php
											  if (auth()->user()->role == 1) {
											  	$check = array_search($user->id, array_column($session_data, 'user_id'));
											  	if ($session_data[$check]->user_id == $user->id) { 
											  	  ?>
											  		@if ($accessData[0]->user_delete == 1)
														<div class="col-md-2">
														<form action="{{ route('terminateSession',[$session_data[$check]->id]) }}" method="post">
														@csrf
														<input name="_method" type="hidden" value="DELETE">
														<button class="badge btn btn-light" onclick="return confirm('Are you sure?')" type="submit"><i class="fas fa-sign-out-alt"></i> </button>
														</form>
														</div>
													  @endif
												  <?php
											  	}
											  }
											  ?>
											  
											</div>
											
										</td>
										</tr>
										@php
										$count++;
										@endphp
										@endforeach	
								  </tbody>				
				        </table>
				     </div>
				</div>
				<div class="tab-pane" id="2">
					 <div class="card-body">
					     <table id="example3" class="table table-bordered table-striped table-responsive-sm">
							 <thead>
									<tr>
										<th>Sr.No.</th>
										<th>Name</th>
										<th>Group</th>
										<th>Role</th>
										<th>Email/User Id</th>
										<th>Action</th>    
									</tr>
								</thead>
					      <tbody>
								@php
									$count=1;
										@endphp
											@foreach($listData as $user)
										<tr>
											<td>@php echo $count; @endphp</td>
											<td>{{$user->name}} {{$user->last_name}}</td>
											<td>{{$user->group_name}}</td>
											<td>{{$user->role_name}}</td>
											<td>{{$user->email}}</td>
											<td>
											<div class="row">
												@if ($accessData[0]->user_edit == 1)
												<div class="col-md-2">
													<a href="users/edit/{{ \app\Libraries\Helpers::encrypt_decrypt($user->id, 'encrypt') }}" class=" badge btn btn-light" ><i class="fas fa-edit"></i> </a>
												</div>
												@endif
												@if ($accessData[0]->user_delete == 1)
												<div class="col-md-2">
												<form action="{{ route('deleteUser',[$user->id]) }}" method="post">
												@csrf
												<input name="_method" type="hidden" value="DELETE">
												<button class="badge btn btn-light" onclick="return confirm('Are you sure?')" type="submit"><i class="fas fa-trash"></i> </button>
												</form>
												</div>
											  @endif
											  <?php
											  if (auth()->user()->role == 1) {
											  	$check = array_search($user->id, array_column($session_data, 'user_id'));
											  	if ($session_data[$check]->user_id == $user->id) { 
											  		?>
											  		@if ($accessData[0]->user_delete == 1)
														<div class="col-md-2">
														<form action="{{ route('terminateSession',[$session_data[$check]->id]) }}" method="post">
														@csrf
														<input name="_method" type="hidden" value="DELETE">
														<button class="badge btn btn-light" onclick="return confirm('Are you sure?')" type="submit"><i class="fas fa-sign-out-alt"></i> </button>
														</form>
														</div>
												 @endif
												  <?php
											  	}
											  }
											  ?>
									 
											</div>

											</td>
										</tr>
										@php
										$count++;
										@endphp
										@endforeach
						    </tbody>			
			            </table>
			          </div>				  
				</div> 
			</div>
		</div>
		</div>	

      <!-- /.card-header -->
      
   </div>
  </div>

  </section>
  <!-- /.content-wrapper -->
@endsection
<!-- DataTables -->
<script src="{{ asset('js/app.js') }}" ></script>
<script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>

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
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<!-- sweet alert-->
<script src="{{ asset('login_assets/js/sweetalert.all.min.js') }}"></script>


<script>

$(document).ready(function() {
    $("#example2").DataTable({
      "responsive": true,
      "autoWidth": false,
    });

	$("#example3").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });
 
  $(document).ready(function(){
  $('ul li a').click(function(){
    $('ul li a').removeClass("targetactive");
    $(this).addClass("targetactive");
 });
});
	

</script>
