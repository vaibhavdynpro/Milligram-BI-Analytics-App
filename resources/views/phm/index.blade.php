@extends('layouts.app')

@section('content')
<!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
  <link rel="stylesheet" href="{{ asset('plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
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
			  <div class="col-sm-8"><h2 class="card-title">Reports List</h2></div>
			  @if ($accessData[0]->report_add == 1)
			  <div class="col-sm-4">
			  <a href="/reports/add" class="btn btn-primary float-sm-right" >Create</a>
			  </div>
			  @endif
			</div>
			</div>
			@if ($accessData[0]->report_view == 1)
            <!-- /.card-header -->
            <div class="card-body" style="margin-top: 65px;">
              <table id="example2" class="table table-bordered table-striped">
                <thead>
                <tr>
                  <th>Sr.No.</th>
                  <th>Name</th>
                  <th>Report Type</th>
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
						<td>@php echo $count; @endphp <input type="checkbox" @if($phm->is_master) checked @endif class="float-right" phmId="{{$phm->id}}" name="chkbx"/></td>
						<td>{{$phm->name}}</td>
						<td>{{$phm->report_type}}</td>
						<td>{{$phm->folder_name}}</td>
						<td>
						<div class="row">
							@if ($accessData[0]->report_edit == 1)
							<div class="col-md-2">
								<a href="reports/edit/{{ \app\Libraries\Helpers::encrypt_decrypt($phm->id, 'encrypt') }}" data-toggle="tooltip" title="Edit PHM" class=" badge btn btn-light" ><i class="fas fa-edit"></i> </a>
							</div>
							@endif
							@if ($accessData[0]->report_add == 1)
							@if( !$phm->is_master)
							<div class="col-md-2">
								<a href="reports/copy/{{ \app\Libraries\Helpers::encrypt_decrypt($phm->id, 'encrypt') }}" data-toggle="tooltip" title="Save As" class=" badge btn btn-light" ><i class="fas fa-copy"></i> </a>
							</div>
							@else
							<div class="col-md-2">
								<a href="reports/canned/{{ \app\Libraries\Helpers::encrypt_decrypt($phm->id, 'encrypt') }}" data-toggle="tooltip" title="Create from Master" class=" badge btn btn-light" ><i class="fas fa-photo-video"></i> </a>
							</div>
							@endif
							@endif
							<div class="col-md-2">
								<a href="reports/downloadWord/{{ \app\Libraries\Helpers::encrypt_decrypt($phm->id, 'encrypt') }}" target="_blank" data-toggle="tooltip" title="Download Word file" class=" badge btn btn-light" ><i class="fas fa-file-word"></i> </a>
							</div>
							@if ($accessData[0]->report_delete == 1)
							@if(!$phm->is_master)
							<div class="col-md-2" data-toggle="tooltip" title="Delete PHM">
								<form action="{{ route('deletePhm',[$phm->id]) }}" method="post">
								@csrf
								<input name="_method" type="hidden" value="DELETE">
								<button class="badge btn btn-light" onclick="return confirm('Are you sure?')" type="submit"><i class="fas fa-trash"></i> </button>
								</form>
							</div>
							@endif					
							@endif					
						
						<div class="col-md-2">
							<div class="btn-group dropup pull-right">
		            <button type="button" class="btn btn-default btn-transparent btn-sm queryDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		              <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
		            </button>
		            <ul class="dropdown-menu">
		            	@if ($accessData[0]->report_add == 1)
		              <li class="dd_li"><button class="btn btn-anch1" onclick="openModal1({{$phm->id}})" type="submit"><i class="fas fa-upload"></i> &nbsp;&nbsp;&nbsp;Upload Formatted Copy</button></li>
		              @endif		
		              @if( $phm->file_path != Null || !empty($phm->file_path))
		              <li class="dd_li"><a href="phm/DownloadFormattedDoc/{{$phm->file_path}}" data-toggle="tooltip" class="anch" title="Create from Master" ><i class="fas fa-download"></i> &nbsp;&nbsp;&nbsp;Download Formatted Copy </a></li>
		              @endif
		            </ul>
		          </div>
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
    <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">Upload Formatted Document</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <form role="form" method="post" action="{{ route('uploadDoc') }}" enctype="multipart/form-data">
        <div class="modal-body">
			   	@csrf
            <div class="form-group">
              <!-- <label for="customFile">Custom File</label> -->

              <div class="custom-file">
                <input type="file" class="custom-file-input" id="customFile" name="uploadFile">
                <label class="custom-file-label" for="customFile">Choose file</label>
              </div>
            </div>
            <input type="hidden" name="phm_id" id="phm_id" class="phm_id_hidden" value="">
        </div>
        <div class="modal-footer">
          <button type="submit" name="submit" class="btn btn-primary">Submit</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        </form>
      </div>
      
    </div>
  </div>
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

$(document).on('change',"input[type='checkbox']", function(e){
  // $("input[type='checkbox']").change(function() {
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

  function openModal1(id){
    $('#phm_id').val(id);
    $('#myModal').modal();
}    
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