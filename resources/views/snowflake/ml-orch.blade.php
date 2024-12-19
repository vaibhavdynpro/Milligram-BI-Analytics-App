@extends('layouts.app')

@section('content')

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
  </style>
<section class="content">
<div class="row">
	<div class="col-12">
	<!-- Content Wrapper.,.. Contains page content -->
	  <div class="content-wrapper">
	   <div class="col-xl-6 col-lg-6 col-md-8 col-sm-10 mx-auto form p-4">
		<div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Create Data </h3>
              </div>
              <!-- /.card-header -->

              
              <!-- form start -->
              <form role="form" method="post" action="{{ route('execute') }}">
			   @csrf
                <div class="card-body">
                <div class="form-group">
                    <label for="exampleInputEmail1">SCHEMA</label>
                    <select class="form-control" id="schemas" name="schema_name" data-placeholder="Select a Schema Name" style="width: 100%;">
                        <option value="" >Select Schema Name</option>
                        <?php
                         // while(odbc_fetch_array($schema_name))                       
                         // echo "<option value=".odbc_result($schema_name, 2)."  >".odbc_result($schema_name, 2)."</option>";
                        foreach($schema_name as $val)
                        {
                         echo "<option value=".$val.">".$val."</option>";                          
                        }
                        ?>
              
                      </select>
                  </div>
                  <div class="form-group">
                    <label for="firstName">BASE YEARS</label>
                    <select class="form-control" multiple="multiple" id="years_ids" name="folders[]" data-placeholder="Select a client" required style="width: 100%;">
                    <option value="" >Select Year</option>                        
                        
                        
              
                     </select>
                   </div>
				  <div class="form-group">
                    <label for="lastName">DURATION</label>
                    <select class="form-control" name="duration" data-placeholder="Select a Duration" style="width: 100%;">
                        <option value="" >Select Duration</option>                        
                        <option value="1">12 Months</option>  
                        <option value="0">24 Months</option>                        
              
                      </select>
                  </div>
                  <div class="form-group">
                    <label for="lastName">PERFORMANCE FLAG</label>
                    <select class="form-control" name="performance_paid_amt_flag" data-placeholder="Select if to exclude null amounts" style="width: 100%;">
                        <option value="" >Select Flag</option>                        
                        <option value="1">Yes</option>  
                        <option value="0">No</option>                        
              
                      </select>
                  </div>
				  

                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="\snowflake" class="btn btn-default float-right" >Cancel</a>
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
  <script>

  $( document ).ready(function() {
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $("#schemas").change(function () {
    alert(1);
    var schema_name = this.value;
    $.ajax({
           type:'POST',
           url:'/getyr',
           data:{schema_name:schema_name},
           success:function(data){
              console.log(data.base_years);
              var s = '<option value="-1">Please Select Year</option>';  
               for (var i = 0; i < data.base_years.length; i++) {  
                   s += '<option value="' + data.base_years[i] + '">' + data.base_years[i] + '</option>';  
               }  
               $("#years_ids").html(s);  
           }
        });
  });
});
  </script>

