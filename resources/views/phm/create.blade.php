@extends('layouts.app')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script  src="{{ asset('js/action.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/tree.css') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<style type="text/css">
nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 100%;
    
    margin-top: -56px;
}
i.fas.fa-bars {
    display: none;
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
<div class="content-wrapper">
	<!-- Content Header (Page pandurang123) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
				<h5 class="mb-2 mt-4">Report</h5>
				</div>
				<!-- /.col -->
				<!--<div class="col-sm-6">
				<ol class="breadcrumb float-sm-right">
					<li class="breadcrumb-item"><a href="/home">Home</a></li>
					<li class="breadcrumb-item active">Dashboard</li>
				</ol>
					</div> -->
			</div>
			<!-- /.row -->
		</div>
		<!-- /.container-fluid -->
	</div>
	<!-- /.content-header -->
	<!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<form role="form" method="post" action="{{ route('storePhm') }}">
				@csrf
				<div class="row">
					<div class="col-4">
						<b>Report Name: </b><input id="name" type="text" name="name" required placeholder="Enter Report Name" />
					</div>
					<div class="col-4">
						<b>Client Name: </b>
						<select  name="client_id" id="client_id" style="width: 40%; height: 100%;" data-placeholder="Select a client" required >
							<option value="" >Select client</option>
							@foreach($folderChildArr as $folder)
							<option value="{{$folder['id']}}"  >{{$folder['name']}}</option>
							@endforeach
						</select>
					</div>
					<div class="col-4">
						<b>Report type: </b>
						<select  name="report_type" id="report_type" style="width: 40%; height: 100%;" data-placeholder="Select a type" required >
							<option value="" >Select Type</option>
							@foreach($reporttypes as $types)
							<option value="{{$types->report_type_id}}"  >{{$types->report_type}}</option>
							@endforeach
						</select>
					</div>
				</div>
				<br/>
				<div class="row">
					
					<div class="form-group col-3">
	                <label for="firstName">Med Start Date:*</label>						
							<input type="text" name="start_date" class="" id="reservation" >
					</div>
					<div class="form-group col-3">
	                    <label for="firstName">Med End Date:*</label>
						<input type="text" name="end_date" id="reservation1">
					</div>
					<div class="form-group col-3">
	                    <label for="firstName">Pharma Start Date:*</label>
						<input type="text" name="pharma_start_date" class="" id="reservation" >
					</div>
					<div class="form-group col-3">
	                    <label for="firstName">Pharma End Date:*</label>
						<input type="text" name="pharma_end_date" id="reservation1">
					</div>			
				
				</div>
				<hr>
				<div class="col-md-12" id="sectionTemplate">
				<div class="card card-widget card-outline card-primary" id="section_box_1" >
					<div class="card-header">
						<div class="user-block">
							<b>Section 1: </b><input id="section_heading_1_1" size="70" type="text" name="section_heading_1_1" placeholder="Enter value" />
							<!-- <img class="img-circle" src="../dist/img/user1-128x128.jpg" alt="User Image">
							<span class="username"><a href="#">Jonathan Burke Jr.</a></span>
							<span class="description">Shared publicly - 7:30 PM Today</span>-->
						</div>
						<!-- /.user-block -->
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-toggle="tooltip" data-action="add-section" title="Add New Section">
							<i class="far fa-cart-plus"></i>Add Section</button>
							<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Expand/Minimise" ><i class="fas fa-minus"></i>
							</button>
							<button type="button" disabled class="btn btn-tool" sectionId_remove="1" data-card-action="remove" data-toggle="tooltip" title="Close" ><i class="fas fa-times"></i>
							</button>
						</div>
						<!-- /.card-tools -->
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<div class="form-group">
							<label>Section Text</label>
							<textarea class="form-control" id="section_text_1_1" name="section_text_1_1" rows="3" placeholder="Enter ..."></textarea>
						</div>
						<div class="col-md-11 float-right" id="subSectionTemplate_1">
							<div class="card card-widget card-outline card-warning collapsed-card" id="sub_section_box_1_1">
								<div class="card-header">
									<div class="user-block">
										<b>Sub section 1:</b>  <input id="sub_section_heading_1_1" size="50" type="text" name="sub_section_heading_1_1" placeholder="Enter value" />
									</div>
									<!-- /.user-block -->
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-toggle="tooltip" sectionId="1" data-action='add-sub-section' title="Mark as read1">
										<i class="far fa-cart-plus"></i>Add Sub-Section</button>
										<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
										</button>
										<button type="button" disabled class="btn btn-tool" sub_sectionId_sect="1" sub_sectionId_subsect="1" data-card-subsection="remove"><i class="fas fa-times"></i>
										</button>
									</div>
									<!-- /.card-tools -->
								</div>
								<!-- /.card-header -->
								<div class="card-body">
									<div class="form-group">
										<label>Sub-Section Text</label>
										<textarea class="form-control" id="sub_section_text_1_1" name="sub_section_text_1_1" rows="3" placeholder="Enter ..."></textarea>
									</div>
									<div class="image-upload-wrap" id="image_upload_wrap_1_1">
										<input id="chart_1_1" type="hidden" name="chart_1_1"  />
										<input id="chart_id_1_1" type="hidden" name="chart_id_1_1"  />
										<input id="chart_name_1_1" type="hidden" name="chart_name_1_1"  />
										<!-- <input class="file-upload-input" type='button' data-toggle="modal" data-target="#myModal" />-->
										<input class="file-upload-input" data-id="1_1" type='button' data-toggle="modal"  />
										<div class="drag-text">
											<h3>Click here to select Look</h3>
										</div>
									</div>
									<div class="file-upload-content" id="file_upload_content_1_1">
										<img class="file-upload-image" id="file_upload_image_1_1" src="" alt="your image" />
										<div class="image-title-wrap">
											<button type="button" section_pos="1_1" class="remove-image">Remove <span class="image-title">Uploaded Look</span></button>
										</div>
									</div>
									</br>
								</div>
							</div>
						</div>
					</div>
				</div>
				
				</div>
				<input type="hidden" id="countsArray" name="countsArray" >
				<button type="submit" class="btn btn-primary">Save </button>
				<a href="/reports" onclick="return confirm('Are you sure?')" class="btn btn-default">Close </a>
			</form>
			<!-- Modal -->
			<div class="modal fade" id="myModal" role="dialog">
				<div class="modal-dialog">
				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Select Dashboard/Look</h4>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<!--<input type="button" id="browse-1" value="Browse folder 2 -> file 2">
							<input type="button" id="browse-2" value="Browse folder 1 -> folder 1/1 -> folder 1/1/1 -> folder 1/1/1/1 -> filde 1/1/1/1/2">
							<input type="button" id="unload" value="Unload active folder">
							<input type="button" id="previous" value="Select previous">
							<input type="button" id="next" value="Select next">99-->
					</div>
					<div id="tree" data-myval="0">
					</div>
					<input type="hidden" id="popup_id" name="popup_id" >
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
				</div>
			</div>
		</div>
	</section>
	<script src="{{ asset('js/tree.js') }}"></script>
	<script src="{{ asset('js/example.js') }}"></script>
	<script src="{{ asset('js/phm.js') }}"></script>
	<script src="//cdn.ckeditor.com/4.15.1/full/ckeditor.js"></script>
	<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
	<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>	
	<!-- /.content -->
</div>
<script type="text/javascript">
   $('#reservation,#reservation1').daterangepicker({ 
   
   locale: {
   	format: 'MM-DD-YYYY'
   },
   	 singleDatePicker: true,
    		 showDropdowns: true
   });  
     //  CKEDITOR.replace( 'messageArea',
         //  {
         //   customConfig : 'config.js',
         //   toolbar : 'simple'
         //   })
    var allEditors = document.querySelectorAll('.form-control');
        for (var i = 0; i < allEditors.length; ++i) {
          // ClassicEditor.create(allEditors[i]);
           CKEDITOR.replace(allEditors[i],
          {
           customConfig : 'config.js',
		   toolbar : [
				{ name: 'document', items: [ 'Source', 'Preview', 'Print', 'Templates'] },
				{ name: 'clipboard', items: [ 'Undo', 'Redo', 'Cut', 'Copy', 'Paste'] },
				{ name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-' ] },
				{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
				{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Blockquote', 'CreateDiv' ] },
				{ name: 'links', items: [ 'Link', 'Unlink', 'Anchor'] },
				{ name: 'insert', items: [ 'Table', 'SpecialChar', 'Image', 'Smiley', 'PageBreak' ] },
				'/',
				{ name: 'styles', items: [  'Format', 'Font', 'FontSize', 'Styles'] },
				{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
				{ name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
			]
           })
   		}
   
   $(document).on("click", ".file-upload-input", function () {
   	var lookSubSectionId = $(this).data('id');
   	console.log(lookSubSectionId);
   	$("#popup_id").val( lookSubSectionId );
   	//$(".modal-body #bookId").val( myBookId );
   	//$('.modal-body #tree').data('myval',"8888");
   	// As pointed out in comments, 
   	// it is unnecessary to have to manually call the modal.
   	 $('#myModal').modal('show');
   });
</script> 
@endsection
