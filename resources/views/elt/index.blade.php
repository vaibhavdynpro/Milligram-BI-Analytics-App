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
  <div class="col-sm-8"><h2 class="card-title">Projects List</h2></div>
  <div class="col-sm-4">
  <!-- <a href="/users/add" class="btn btn-primary float-sm-right" >Create</a> -->
  </div>
</div>
</div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr.No.</th>
                  <th>Project Name</th>
                  <th>Action</th>
                  
                </tr>
                </thead>
                <tbody>
				@php
				$count=1;
				@endphp
					@foreach($project as $proj)
					<tr>
						<td>@php echo $count; @endphp</td>
						<td>{{$proj}}</td>
						<td>
						<div class="row">
							<div class="col-md-4">
							
								<a href="{{ route('jobs',[$proj]) }}" class=" badge btn btn-light" ><i class="fas fa-eye" data-toggle="tooltip" title="View Jobs"></i> </a>
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

<script>
  $(function () {
    
    $("#example2").DataTable({
      "responsive": true,
      "autoWidth": false,
    });
  });
</script>
