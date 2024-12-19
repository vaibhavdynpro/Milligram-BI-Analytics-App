@extends('layouts.app')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script  src="{{ asset('js/action.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/tree.css') }}">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<div class="content-wrapper">
	<!-- Content Header (Page pandurang123) -->
	<div class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-sm-6">
				<h5 class="mb-2 mt-4">PHM Report</h5>
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
			<form role="form" method="post" action="{{ route('cannedPhm',[$id]) }}">
				@csrf
				<div class="row">
				<div class="col-4">
					<b>Report Name: </b><input id="name" type="text" name="name" value="{{$phmData->name}}_copy" required placeholder="Enter Report Name" />
				</div>
				<div class="col-4">
			    <b>Client Name: </b>
			    <select  name="client_id" id="client_id" style="width: 40%; height: 100%;" data-placeholder="Select a client" required >
			       
			        @foreach($folderChildArr as $folder)
			        <option value="{{$folder['id']}}" @php if($phmData->client_id == $folder['id']) echo 'selected'; @endphp > {{$folder['name']}}</option>
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
				<div class="col-3">
          @php
          $theDate1    = new DateTime($phmData->start_date);
          $startStringDate = $theDate1->format('m-d-Y');
          $theDate2    = new DateTime($phmData->end_date);
          $endStringDate = $theDate2->format('m-d-Y');
          $theDate3    = new DateTime($phmData->pharma_start_date);
          $startStringDate1 = $theDate3->format('m-d-Y');
          $theDate4    = new DateTime($phmData->pharma_end_date);
          $endStringDate1 = $theDate4->format('m-d-Y');
          @endphp

					<b >Medical Start Date:</b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" value="{{$startStringDate}}" name="start_date" class="" id="reservation" >
				</div>
				<div class="col-3" >
					<b >Medical End Date:</b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" value="{{$endStringDate}}" name="end_date" id="reservation1" style="width: 41%;" >
				</div>
				<div class="col-3" >
					<b >Pharmacy Start Date:</b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" value="{{$startStringDate1}}" name="pharma_start_date" class="" id="reservation" >
				</div>
				<div class="col-3" >
					<b >Pharmacy End Date:</b>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<input type="text" value="{{$endStringDate1}}" name="pharma_end_date" id="reservation1" style="width: 41%;" >
				</div>
				</div>
				<hr>
				<div class="col-md-12" id="sectionTemplate" style="display:none;">

        @foreach($phmSectionDataArr as $phmSection)
        
				<div class="card card-widget card-outline card-primary" id="section_box_{{$phmSection['section']->section_no}}" >
					<div class="card-header">
						<div class="user-block">
							<b>Section {{$phmSection['section']->section_no}}: </b><input size="70" id="section_heading_{{$phmSection['section']->section_no}}_1" value="{{$phmSection['section']->section_title}}" type="text" name="section_heading_{{$phmSection['section']->section_no}}_1" placeholder="Enter value" />
						</div>
						<!-- /.user-block -->
						<div class="card-tools">
							<button type="button" class="btn btn-tool" data-toggle="tooltip" data-action="add-section" title="Add New Section">
							<i class="far fa-cart-plus"></i>Add Section</button>
							<button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Expand/Minimise" ><i class="fas fa-minus"></i>
							</button>
							<button type="button" class="btn btn-tool" phmId_remove="{{$phmSection['section']->phm_id}}" sectionId_remove="{{$phmSection['section']->section_no}}" data-card-action="remove" data-toggle="tooltip" title="Close" ><i class="fas fa-times"></i>
							</button>
						</div>
						<!-- /.card-tools -->
					</div>
					<!-- /.card-header -->
					<div class="card-body">
						<div class="form-group">
							<label>Section Text</label>
							<textarea class="form-control"  id="section_text_{{$phmSection['section']->section_no}}_1" name="section_text_{{$phmSection['section']->section_no}}_1" rows="3" placeholder="Enter ...">{{$phmSection['section']->section_text}}</textarea>
						</div>
						<div class="col-md-11 float-right" id="subSectionTemplate_{{$phmSection['section']->section_no}}">
            @foreach($phmSection['subSection'] as $phmSubSection)
            
							<div class="card card-widget card-outline card-warning collapsed-card" id="sub_section_box_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}">
								<div class="card-header">
									<div class="user-block">
										<b>Sub section {{$phmSubSection->sub_section_no}}:</b>  <input size="50" id="sub_section_heading_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" value="{{$phmSubSection->sub_section_title}}" type="text" name="sub_section_heading_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" placeholder="Enter value" />
									</div>
									<div class="card-tools">
										<button type="button" class="btn btn-tool" data-toggle="tooltip" sectionId="{{$phmSection['section']->section_no}}" data-action='add-sub-section' title="Mark as read1">
										<i class="far fa-cart-plus"></i>Add Sub-Section</button>
										<button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>
										</button>
										<button type="button" class="btn btn-tool" phmId_remove="{{$phmSection['section']->phm_id}}" sub_sectionId_sect_id="{{$phmSection['section']->id}}" sub_sectionId_sect="{{$phmSection['section']->section_no}}" sub_sectionId_subsect="{{$phmSubSection->sub_section_no}}" data-card-subsection="remove"><i class="fas fa-times"></i>
										</button>
									</div>
								</div>
								<div class="card-body">
									<div class="form-group">
										<label>Sub-Section Text</label>
										<textarea class="form-control" id="sub_section_text_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" name="sub_section_text_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" rows="3" placeholder="Enter ...">{{$phmSubSection->sub_section_text}}</textarea>
									</div>
									<div class="image-upload-wrap" id="image_upload_wrap_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}">
										<input id="chart_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" value="{{$phmSubSection->look_img_url}}" type="hidden" name="chart_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}"  />
                    					<input id="chart_id_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" value="{{$phmSubSection->look_id}}" type="hidden" name="chart_id_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}"  />
										<input id="chart_name_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" value="{{$phmSubSection->look_name}}" type="hidden" name="chart_name_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}"  />
                    					<input class="file-upload-input" data-id="{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" type='button' data-toggle="modal"  />
										<div class="drag-text">
											<h3>Click here to select Look</h3>
										</div>
									</div>
									<div class="file-upload-content" id="file_upload_content_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}">
										<img class="file-upload-image" id="file_upload_image_{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" src="{{$phmSubSection->look_img_url}}" alt="your image" />
										<div class="image-title-wrap">
											<button type="button" section_pos="{{$phmSection['section']->section_no}}_{{$phmSubSection->sub_section_no}}" class="remove-image">Remove <span class="image-title">Uploaded Look</span></button>
										</div>
									</div>
									</br>
								</div>
              </div>
              @endforeach

						</div>
					</div>
				</div>
        @endforeach
        

				</div>
				<input type="hidden" id="countsArray" value="{{$phmSectionCountArr}}" name="countsArray" >
				<input type="hidden" id="sect_count_element" value="{{$phmSectionDataCount}}" name="sect_count_element" >
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
	<script src="{{ asset('js/phm_edit.js') }}"></script>
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
