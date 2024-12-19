@extends('layouts.app')

@section('content')
<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  <!-- sweet alert-->
  <script src="{{ asset('login_assets/js/sweetalert.all.min.js') }}"></script>
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
    margin-top: 3rem;
}
 @media screen and (max-width: 600px) {
  .topnav{overflow: hidden;
    background-color: #f5f5f5;
    margin-top: 21px;
    margin-left: 0px;
    width: 100%;}
    i.fas.fa-bars {
    display: block;
    margin-left: auto;
	}
	
	li.nav-item.dropdown.user-menu {
    margin-left: -120px;
	}
	.card-body {
    margin-top: 4rem;
	}
   nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 126%;
	}
	.navbar-expand .navbar-nav .nav-link {
	padding-right: 8rem;
    padding-left: 0rem;
}
.card-body {
    margin-top: 3rem;
	}
  a.btn.btn-primary.float-sm-right {
	  margin-top: -23px;
    float: right;
}
}
	
  </style>
<section class="content">

<div class="row">
	<div class="col-12">
	<!-- Content Wrapper. Contains page content -->
	  <div class="content-wrapper">
	   <!-- @if (\Session::has('success'))
		<div class="alert alert-success alert-dismissible col-xl-6 col-lg-6 col-md-8 col-sm-10 mx-auto" role="alert" >
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
		<!-- Content Header (Page header) -->
		<!--<div class="content-header">
		  <div class="container-fluid">
			<div class="row mb-2">
			  <div class="col-sm-6">
				<h1 class="m-0 text-dark">Users</h1>
			  </div>
			  <div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
				  <li class="breadcrumb-item"><a href="#">Home</a></li>
				  <li class="breadcrumb-item active">Users</li>
				</ol>
			  </div>
			</div>
		  </div>
		</div>-->
		<!-- /.content-header -->
	<div class="card">
            <div class="card-header" style="position: fixed;
    width: 95%;
    z-index: 999;
    background-color: white;">
              
            
			<div class="row">
			  <div class="col-sm-8"><h2 class="card-title">Roles List</h2></div>
			  @if ($accessData[0]->role_add == 1)
			  <div class="col-sm-4">
			  <a href="/roles/add" class="btn btn-primary float-sm-right" >Create</a>
			  </div>
			   @endif
			</div>
			</div>
			@if ($accessData[0]->role_view == 1)
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr.No.</th>
                  <th>Role</th>
                  <th>is_active</th>
                  <th>Action</th>
                  
                </tr>
                </thead>
                <tbody>
				@php
				$count=1;
				@endphp
					@foreach($roleData as $user)
					<tr>
						<td>@php echo $count; @endphp</td>
						<td>{{$user->role}}</td>
						@if ($user->is_active == 1)
						<td>Yes</td>
						@else
						<td>No</td>
						@endif
						<td>
						<div class="row">
							@if ($accessData[0]->role_edit == 1)
							<div class="col-md-4">
								<a href="roles/edit/{{ \app\Libraries\Helpers::encrypt_decrypt($user->role_id, 'encrypt') }}" class=" badge btn btn-light" ><i class="fas fa-edit"></i> </a>
							</div>
							@endif
							@if ($accessData[0]->role_delete == 1)
							<div class="col-md-8">
							<form action="{{ route('deleteRole',[$user->role_id]) }}" method="post">
							@csrf
							<input name="_method" type="hidden" value="DELETE">
							<button class="badge btn btn-light" onclick="return confirm('Are you sure?')" type="submit"><i class="fas fa-trash"></i> </button>
							</form>
							</div>
							@endif
						  
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
            @else
            <div class="card-body">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                	<td>No Data Found</td>
                </tr>
              </thead>
            </table>
          </div>
            @endif
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
    
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
<!-- sweet alert-->
<script src="{{ asset('login_assets/js/sweetalert.all.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

<script>
  $(function () {
    
    $("#example2").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });
</script>
