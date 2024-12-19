@extends('layouts.app')

@section('content')
<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
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
            <div class="card-header">
              
            
			<div class="row">
			  <div class="col-sm-8"><h2 class="card-title">PHM Report List</h2></div>
			  <div class="col-sm-4">
			 
			  </div>
			</div>
			</div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr.No.</th>
                  <th>Name</th>
                  <th>client Name</th>
                  <th>Action</th>
                  
                </tr>
                </thead>
                <tbody>
				@php
				$count=1;
				@endphp
					@foreach($phmData as $phm)
					<tr>
					<td>@php echo $count; @endphp</td>
						<td>{{$phm->name}}</td>
						<td>{{$phm->folder_name}}</td>
						<td>
						<div class="row">
							
							<div class="col-md-3">
								<a href="../../../phm/downloadPDF/{{$phm->id}}" target="_blank" data-toggle="tooltip" title="Download Word file" class=" badge btn btn-light" ><i class="fas fa-file-word"></i> </a>
							</div>
							
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


  $("input[type='checkbox']").change(function() {
	var phmId = $(this).attr("phmId");
        if(this.checked) {
			flag = 1;		
        }else{
		 	flag = 0;
		}
		$.ajax({
				type:'POST',
				url:'/phm/markMaster',
				data:{flag:flag,phm_id:phmId},
				success:function(data){
					console.log(data);
					location.reload();
				}
			});       
    });

});
</script>
