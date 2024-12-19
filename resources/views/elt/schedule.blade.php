@extends('layouts.app')

@section('content')

<section class="content">
<div class="row">
	<div class="col-12">
	 <!--Content Wrapper. Contains page content -->
	  <div class="content-wrapper">
		  <div class="col-xl-6 col-lg-6 col-md-8 col-sm-10 mx-auto form p-4">
			<div class="card card-primary">
            @if (\Session::has('success'))

                    <div class="alert alert-success" id="hideMe">                 
                            <p>{!! \Session::get('success') !!}</p>    
                    </div>
            
                    @endif
            @if (\Session::has('failed'))

                  <div class="alert alert-danger" id="hideMefailed">                 
                          <p>{!! \Session::get('failed') !!}</p>    
                  </div>

                    @endif
				  <div class="card-header">

					<h3 class="card-title">Reschedule Job</h3>
				  </div>
                   
				  <!-- /.card-header -->
				  <!-- form start -->
				  <form role="form" id="schform" method="post" action="{{ route('rescheduleJob') }}">
				   @csrf
					<div class="card-body">
					  <div class="form-group">
						<label for="sch">Scheduler Name</label>
						<input type="text" class="form-control" id="sch" name="sch" readonly>
					  </div>
                      
                      <div class="form-group">
                        <label for="tz">Timezone</label>
                        <select id="tz" class="form-control selectpicker" selected="{{$schedule_details->timezone}}" name="tz" style="width: 100%" data-live-search="true"></select>
                      </div> 
                      <div class="form-group">
						<label for="hours">Hours</label>
						<input type="text" class="form-control" id="hours" name="hours" value="{{$schedule_details->hour}}" placeholder="Enter  Hours">
					  </div>
                      <div class="form-group">
						<label for="minutes">Minutes</label>
						<input type="text" class="form-control" id="minutes" name="minutes" value="{{$schedule_details->minute}}" placeholder="Enter Minutes">
					  </div>
                      <div>
						<input type="radio" id="days_week" name="toggle_days">
                        <label for="week">Days Of Week</label></br>
                      </div>
                      <div id="week" class="form-group">
                        <input type="checkbox" name="mon" @if($schedule_details->monday == 'true' ) checked @endif> Mon
                        <input type="checkbox" name="tue" @if($schedule_details->tuesday == 'true' ) checked @endif> Tue
                        <input type="checkbox" name="wed" @if($schedule_details->wednesday == 'true' ) checked @endif> Wed
                        <input type="checkbox" name="thu" @if($schedule_details->thursday == 'true' ) checked @endif> Thu
                        <input type="checkbox" name="fri" @if($schedule_details->friday == 'true' ) checked @endif> Fri
                        <input type="checkbox" name="sat" @if($schedule_details->saturday == 'true' ) checked @endif> Sat
                        <input type="checkbox" name="sun" @if($schedule_details->sunday == 'true' ) checked @endif> Sun
                    </div>
                    <div>
                        <input type="radio" id="days_month" name="toggle_month">
                        <label for="month">Days Of Month</label></br>	
                    </div>
                    <div id="month" class="form-group">
                        <input type="text" class="form-control" id="days" name="days" value="{{$schedule_details->daysOfMonth}}" placeholder="E.g: 15/L(Last Day)/15W(Closest Weekday to 15th)/*(Every Day)">				 
                    </div> 
                    <div class="form-check">
						<input type="checkbox" class="form-check-input" id="enabled" name="enabled" @if($schedule_details->enabled == 'true' ) checked @endif />
						<label class="form-check-label" for="enabled">Enabled</label>
					</div>
                    <div id="prevent_dup" class="form-check">
                        <input type="checkbox" class="form-check-input" id="prevent_dup" name="prevent_dup" @if($schedule_details->preventDuplicateJob == 'true' ) checked @endif />
						<label class="form-check-label" for="prevent_dup">Prevent Duplicate Job</label>
                   </div> 
                   <input name="env" id="env" type="hidden" value="{{$schedule_details->environmentName}}">
                   <input name="job_name" id="job_name" type="hidden" value="{{$schedule_details->jobName}}">
                   <input name="proj_name" id="proj_name" type="hidden" value="{{$proj}}">
					<div class="card-footer">
					  <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure?')">Submit</button>
					</div>
				  </form>
				</div>
				<!-- /.card -->
		
		</div>
  </div>
  </div>
  </section>
  <!-- /.content-wrapper -->
@endsection
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<!-- <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.10/moment-timezone-with-data.js"></script>
<script>

$( document ).ready(function() {

  $('#hideMe').fadeOut(5000);
  $('#hideMefailed').fadeOut(15000); // 5 seconds x 1000 milisec = 5000 milisec

    // HUMMANIZE DETAILS//
    var sch_name = "{{ $schedule_details->jobName }}";
    function humanize(str) {
        var i, frags = str.split('_');
        for (i=0; i<frags.length; i++) {
            frags[i] = frags[i].charAt(0).toUpperCase() + frags[i].slice(1);
        }
        return frags.join(' ');
    }
    $("#sch").val(humanize(sch_name));

    var timezone = moment.tz.names();
    //TIMEZONE AUTO POPULATE//
  for (var i = 0; i < timezone.length; i++) {
    // $('select').append('<option value="' + timezone[i] + '">' + timezone[i] + '</option>');
    $('select').append($('<option>', { value : timezone[i] })
          .text(timezone[i]));
  }
  $('#tz').find('option').each( function() {
    var tz = "{{ $schedule_details->timezone }}";
      var $this = $(this);
      if ($this.text() == tz) {
         $this.attr('selected','selected');
         return false;
      }
 });

    // TOGGLE BETWEEN RADIO BUTTON AND RESPECTED DIVS//
    var toggle = "{{ $schedule_details->dayOfWeek }}";
    if(toggle==true){  
        $("#month").children().attr("disabled","disabled");
        $("#days_week").attr("checked","checked");

    } else{
        $("#week").children().attr("disabled","disabled");
        $("#days_month").attr("checked","checked");
    }
    
    $("#days_month").click(function(){
        // preventDefault();
        $("#days_week").prop("checked",false);
        $("#week").children().attr("disabled","disabled");
        $("#month").children().attr("disabled",false);
        $("#days_month").prop("checked",true);
    });
    $("#days_week").click(function(){
        // preventDefault();
        $("#days_month").prop("checked",false);
        $("#month").children().attr("disabled","disabled");
        $("#week").children().attr("disabled",false);
        $("#days_week").prop("checked",true);
    });
})
</script>
