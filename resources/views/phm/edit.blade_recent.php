@extends('layouts.app')

@section('content')


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script  src="{{ asset('js/action.js') }}"></script>
   <link rel="stylesheet" href="{{ asset('css/tree.css') }}">
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
   
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
         
			    <h5 class="mb-2 mt-4">PHM Report</h5>
          
          </div><!-- /.col -->
          <!--<div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="/home">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
          </div> -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->

    
           
  

    <section class="content">
    <div class="container-fluid">
    <form role="form" method="post" action="{{ route('updatePhm',[$id]) }}">
    @csrf
	<div class="row">
		<div class="col">
			<b>Report Name: </b><input id="name" type="text" name="name" value="{{$phmData->name}}" placeholder="Enter Report Name" />
		</div>
    <div class="col">
    <b>Client Name: </b>
    <select  name="client_id" id="client_id" style="width: 40%; height: 100%;" data-placeholder="Select a client" required >
        <option value="" >Select client</option>
        @foreach($folderChildArr as $folder)
        <option value="{{$folder['id']}}" @php if($phmData->client_id == $folder['id']) echo 'selected'; @endphp > {{$folder['name']}}</option>
        @endforeach

      </select>
    </div>
  </div>
  
  <hr>	
	<p>Key Findings and Solutions for Consideration
        The following key findings resulted from the analysis of archival health care data (i.e., medical and
        pharmacy utilization data) conducted by Kairos.</br>
        <b>Key Finding 1: </b><input id="text1" type="text" name="text1" value="Chronic Disease" /></br>
        Key Finding: Patterns of risk generally occur within any given population. In order to
        better understand these patterns, the population was risk stratified into five distinct 
        The following key findings resulted from the analysis of archival health care data (i.e., medical and
        pharmacy utilization data) conducted by Kairos.</br> 
        <b>Key Finding 2:</b>  <input id="text2" type="text" name="text2" value="Diabetes Complications and Co-Morbidities" />
</br>
        Key Finding: Patterns of risk generally occur within any given population. In order to
        better understand these patterns, the population was risk stratified into five distinct 
</p>
    
      
        <div class="image-upload-wrap">
          <input class="file-upload-input" type='button' data-toggle="modal" data-target="#myModal" />
          <div class="drag-text">
            <h3>Click here to select Look</h3>
          </div>
        </div>
        <div class="file-upload-content">
          <img class="file-upload-image" src="{{$phmData->chart1}}" alt="Look image" />
          <div class="image-title-wrap">
            <button type="button" onclick="removeUpload()" class="remove-image">Remove <span class="image-title">Uploaded Look</span></button>
          </div>
        </div>
    </br>
        <input id="chart1" type="hidden" value="{{$phmData->chart1}}" name="chart1"  />
        <button type="submit" class="btn btn-primary float-right">Save </button>
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
        <input type="button" id="next" value="Select next">-->
        
         
          </div><div id="tree"></div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
        
      </div>
    </div>
    
  </div>
  
  <script src="{{ asset('js/tree.js') }}"></script>
  <script src="{{ asset('js/example.js') }}"></script>
   
    </section>
    <!-- /.content -->
  </div>




  
@endsection


