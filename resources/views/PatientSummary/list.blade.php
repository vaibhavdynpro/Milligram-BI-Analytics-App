
@extends('layouts.app')

@section('content')
<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
  
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
  <link href="{{ asset('css/phm.css') }}" rel="stylesheet">
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

.title-element-name {
  color: #FF8200;
  font-weight: bold;
}

.table-responsive {
  z-index: 999;
  overflow-y: auto !important;
}
li.dd_li {
    padding: 5px;
    /* border-top: 1px solid; */
}
.dropdown-menu {
 		padding: 10px;
 		/*left: -56px !important;*/
    min-width: max-content;
    }
 button.btn.btn-anch {
    color: #007bff;
    margin-left: -20px;
    margin-top: -10px;
    margin-bottom: -24px;
}
 button.btn.btn-anch1 {
       color: #007bff;
    margin-left: -12px;
    margin-top: -13px;
    margin-bottom: -11px;
}
td#started {
    color: red;
    font-weight: 600;
}
td#inp {
    color: #88cd39;
    font-weight: 600;
}
td#fail {
    color: red;
    font-weight: 600;
}
td#done {
    color: Green;
    font-weight: 600;
}
@media screen and (max-width: 600px) {
  .topnav{overflow: hidden;
    background-color: #f5f5f5;
    margin-top: 21px;
    margin-left: 0px;
    width: 100%;}
    i.fas.fa-bars {
    display: block;
}
  </style>
  <!-- Theme style -->
<section class="content">

<div class="row">
	<div class="col-12">
	<!-- Content Wrapper. Contains page content -->
	  <div class="content-wrapper">
	   @if (\Session::has('success'))
		<div class="alert alert-success alert-dismissible col-xl-6 col-lg-6 col-md-8 col-sm-10 mx-auto" role="alert" >
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			{!! \Session::get('success') !!}
		</div>
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
              
            
			<div class="row" >
			  <div class="col-sm-8"><h2 class="card-title">Patient List</h2></div>
			 
			</div>
			</div>
			@if ($accessData[0]->generate_report_view == 1)
            <!-- /.card-header -->
            <div class="card-body" style="margin-top: 65px;">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr.No.</th>
                  <th>Patient Name</th>
                  <th>Status</th>
                </tr>
                </thead>
                <tbody>
				@php
				$count=1;
				@endphp
					@foreach($results as $phm)
					<tr>
						<td>@php echo $count; @endphp </td>
						<td>{{$phm->patient_name}}</td>

              @if($phm->status == 0)
              <td id="started">Failed</td>
              @elseif($phm->status == 1)
              <td id="inp">Success</td>
              @endif
				
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
<script src="{{ asset('plugins/bs-custom-file-input/bs-custom-file-input.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('plugins/chart.js/Chart.min.js') }}"></script>

<script>
  $(function () {
    
    $("#example2").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });

  $(document).ready(function() {

	$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  	});
});
   
</script>
<script>
$(function () {
  bsCustomFileInput.init();
});
   $(document).on('show.bs.dropdown',
                  function(e) {
     if ($(e.relatedTarget).hasClass('queryDropdown')) {
       $('#test').css('padding-top', '90px');
     }
   });
$(document).on('hide.bs.dropdown',
               function (e) {
  if ($(e.relatedTarget).hasClass('queryDropdown')) {
    $('#test').css('padding-top', '25px');
  }
});
</script>
<script>

</script>