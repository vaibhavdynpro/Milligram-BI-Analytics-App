@extends('layouts.app')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link rel="stylesheet" href="{{ asset('plugins/daterangepicker/daterangepicker.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap-multiselect.css') }}">
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/css/bootstrap-multiselect.css">  -->

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
div#Document {
    width: 210mm;
    background-color: white;
    height: auto;
    margin-left: 25%;
    padding-left: 65px;
    padding-top: 90px;
    padding-right: 65px;
    font-family: arial;
}

.h2, h2 {
    font-size: 16px;
}
p {
    font-size: 13px;
}
.secTitle{
	font-size: 18px;
	font-family: sans-serif;
	font-weight: bold;
}
.img2
{
	margin-bottom: 20px;
}
div#myDiv {
    height: 50px;
    margin-left: 35%;
}
#loadMsg
{
    margin-left: 7%;
    font-size: 14px;
}
button.multiselect.dropdown-toggle.btn.btn-default {
    height: 35px;
    margin-top: -5px;
    margin-left: 5px;
}
ul.multiselect-container.dropdown-menu.show {
    width: 275px;
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
				<!-- <h5 class="mb-2 mt-4">Report</h5> -->
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
			
			<form role="form" method="post" action="{{ route('storePatientSummary') }}">
				@csrf
				<div class="row">
					<div class="form-group col-3">
						<b>Client Name: </b>
						<select  name="client_id" id="client_id" style="width: 40%; height: 50%;" data-placeholder="Select a client" required >
							<option value="" >Select client</option>
							@foreach($ClientFolders as $folder)
							<option value="{{$folder->folder_id}}/{{$folder->schema_name}}">{{$folder->folder_name}}</option>
							@endforeach
						</select>
					</div>

					<input type="hidden" name="client_name" id="client_name">
                    <input type="hidden" name="dash_id" id="dash_id">
				<!-- 	<div class="form-group col-3">
						<b>Limit: </b>
						<select  name="limit" id="limit" style="width: 40%; height: 100%;" data-placeholder="Select a patient limit" required >
							<option value="" >Select Limit</option> 
	        				<option value="100">Top 100</option>
							<option value="500">Top 500</option>
							<option value="1000">Top 1000</option>
							<option value="20000">All</option>
						</select>
					</div> -->
					<div class="form-group col-3">
						<label for="firstName">Patient List : </label>
						<select id="multiple-checkboxes" multiple="multiple" name="patientlist[]" >  
	        			
	    				</select>  
                        <p style="color:red;">*You can select max 100 Patients</p>
    				</div>
					<div class="form-group col-3">
						<b>Frequency: </b>
						<select  name="frequency" id="frequency" style="width: 40%; height: 50%;" data-placeholder="Select a client" required >
							<option value="" >Select Frequency</option> 
	        				<!-- <option value="1">Once</option>
							<option value="2">Weekly</option> -->
							<option value="3">Monthly</option>
							<option value="4">Quarterly</option>
						</select>
					</div>
					
					

				</div>
				<div id="myDiv" style="display:none;">
			        <img id="loading-image" src="{{ asset('dist/img/loader2.gif') }}" style="display:none;"/>
			        <p id="loadMsg">Fetching Patient List Please Wait...</p>
			    </div>
				<button type="submit" class="btn btn-primary">Save </button>
				<a href="\all_reports" class="btn btn-default" >Cancel</a>
				</form>
				<hr>
				
			
			
	</section>
	<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
	<script src="{{ asset('plugins/daterangepicker/daterangepicker.js') }}"></script>	
	<script src="{{ asset('js/bootstrap-multiselect.js') }}"></script>	
	 <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.13/js/bootstrap-multiselect.js"></script> -->
	<!-- /.content -->
</div>
<script type="text/javascript">
	$( document ).ready(function() {
	   $.ajaxSetup({

	        headers: {
	            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	        }

	    });
	});

   $('#reservation,#reservation1').daterangepicker({ 
   
   locale: {
   	format: 'MM-DD-YYYY'
   },
   	 singleDatePicker: true,
    		 showDropdowns: true
   });  

   $(document).ready(function() {  
        $('#multiple-checkboxes').multiselect({
         // includeSelectAllOption:true,
         enableFiltering: true,
         maxHeight: 450,    
         maxWidth: 300,
         enableClickableOptGroups: true,
          enableCollapsibleOptGroups: true,
         templates: {
            filter: '<li class="multiselect-item multiselect-filter"><div class="input-group mb-3"><div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search"></i></span></div><input class="form-control multiselect-search" type="text" /></div></li>',
            filterClearBtn: '<span class="input-group-btn"><button class="btn btn-default multiselect-clear-filter" type="button"><i class="fa fa-eraser"></i></button></span>'

        },
                onChange: function(option, checked) {
                // Get selected options.
                var selectedOptions = jQuery('#multiple-checkboxes option:selected');
 
                if (selectedOptions.length >= 100) {
                    // Disable all other checkboxes.
                    var nonSelectedOptions = jQuery('#multiple-checkboxes option').filter(function() {
                        return !jQuery(this).is(':selected');
                    });
 
                    nonSelectedOptions.each(function() {
                        var input = jQuery('input[value="' + jQuery(this).val() + '"]');
                        input.prop('disabled', true);
                        input.parent('li').addClass('disabled');
                    });
                }
                else {
                    // Enable all checkboxes.
                    jQuery('#multiple-checkboxes option').each(function() {
                        var input = jQuery('input[value="' + jQuery(this).val() + '"]');
                        input.prop('disabled', false);
                        input.parent('li').addClass('disabled');
                    });
                }
            }
        });
        var shiftClick = jQuery.Event("click");
        shiftClick.shiftKey = true;

            $(".multiselect-container li *").click(function(event) {
                if (event.shiftKey) {
                   //alert("Shift key is pressed");
                    event.preventDefault();
                    return false;
                }
                else {
                    //alert('No shift hey');
                }
            });
    }); 
   	// $('#client_id').on('change',function(){
   	// 	var selectedClient = $('#client_id').val();
   	// 	var splitArr = selectedClient.split("/");
   	// 	$.ajax({
    //        type:'POST',
    //        url:'/get_patientCount',
    //        data:{client_id:splitArr[0]},           
    //        success:function(data){
    //        	var PatientCount = data['count'];
    //        	 $("#limit").empty();
    //        	$('#limit').append("<option value=''>Select Limit</option>");
    //        	if(PatientCount != 0)
    //        	{
    //        		var splitCount = PatientCount / 500;
    //        		let RoundCnt = Math.round(splitCount);
    //        		var LimitOptions = "";
    //        		var limit = 0;
    //        		var min =1;
    //        		for(var i=0;i<RoundCnt;i++)
    //        		{
    //        			limit=parseInt(limit+500);
    //        			if(i != 0)
    //        			{
    //        				min=parseInt(min+500);
    //        			}
    //        			// else if(i == RoundCnt)
    //        			// {
    //        			// $('#limit').append("<option value='"+min+"-"+PatientCount+"'>"+min+"-"+PatientCount+"</option>");
           				
    //        			// }
    //        			$('#limit').append("<option value='"+min+"-"+limit+"'>"+min+"-"+limit+"</option>");
    //        		}
    //        	}
    //        }	
    //     });
   		
   	// });
   	
 	$('#client_id').on('change',function(){
        $('#client_name').val($('#client_id').find(":selected").text());
        var selectedId = $('#client_id').val();
        var limit = "1-50000";
	    var split = selectedId.split("/");
 		$.ajax({
           type:'POST',
           url:'/get_mapping',
           data:{client_id:split[0],cat:"PatientSummary"},           
           success:function(data){
           	$('#dash_id').val(data['result'][0]['map_id']);
           }	
        });
        $.ajax({
           type:'POST',
           url:'/get_patient',
           data:{schema_name:split[1],limit:limit},
           beforeSend: function() {
              $("#loading-image").show();
              $("#myDiv").show();
           },
           success:function(data){
           	var options = "";
           	for (let i = 0; i < data.patientlist[0]['DEMOGRAPHIC_VALUES']['patient_details'].length; i++) {
            	options += "<option value='"+data.patientlist[0]['DEMOGRAPHIC_VALUES']['patient_details'][i].name+"'>"+data.patientlist[0]['DEMOGRAPHIC_VALUES']['patient_details'][i].name+"</option>";
            }
          
                $("#multiple-checkboxes").html(options);
        		$("#multiple-checkboxes").multiselect('rebuild');
                $("#loading-image").hide();
                $("#myDiv").hide();
           }	
       });

 	});

   
</script> 
@endsection
