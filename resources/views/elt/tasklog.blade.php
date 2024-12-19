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
  <div class="col-sm-8"><h2 class="card-title">Tasks List</h2></div>
</div>
</div>
            <!-- /.card-header -->
            <div class="card-body">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr.No.</th>
                  <th>Job Name</th>
                  <th>Task Id</th>
                  <th>Task Type</th>
                  <th>State</th>
                  <th>Start Time</th>
                  <th>End Time</th>
                  <th>Action</th>
                  
                </tr>
                </thead>
                <tbody>
				@php
				$count=1;
				@endphp
					@foreach($task_details as $task)
					<tr>
						<td>@php echo $count; @endphp</td>
                        <td>{{$task->jobName}}</td>
                        <td>{{$task->id}}</td>
                        <td>{{substr($task->type,0,3)}}</td>
                        <td>{{$task->state}}</td>
                        <td>{{date("Y-m-d H:i:s", substr($task->startTime, 0, 10))}}</td>
                        <td>{{date("Y-m-d H:i:s", substr($task->endTime, 0, 10))}}</td>
						<td>
						<div class="row">
							<div class="col-md-12">
                                <a href="{{ route('deep_dive',[$task->projectName,$task->id]) }}" class="badge btn btn-light" ><i class="fas fa-arrow-circle-right" data-toggle="tooltip" title="Know More"></i>
                                
                                </a>
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
