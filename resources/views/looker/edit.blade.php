@extends('layouts.app')

@section('content')
<style type="text/css">
  	nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 100%;
	}
  </style>
<section class="content">
<div class="row">
	<div class="col-12">
	 <!--Content Wrapper. Contains page content -->
	  <div class="content-wrapper">
	  @if (\Session::has('success'))
		<div class="alert alert-success alert-dismissible col-xl-6 col-lg-6 col-md-8 col-sm-10 mx-auto" role="alert" >
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			{!! \Session::get('success') !!}
		</div>
		@endif
		  <div class="col-xl-6 col-lg-6 col-md-8 col-sm-10 mx-auto form p-4">
			<div class="card card-primary">
				  <div class="card-header">
					<h3 class="card-title">Looker Settings</h3>
				  </div>
				  <!-- /.card-header -->
				  <!-- form start -->
				  <form role="form" method="post" action="{{ route('updateLooker',[$id]) }}">
				   @csrf
					<div class="card-body">
					  <div class="form-group">
						<label for="api_url">Api Url</label>
						<input type="text" class="form-control" id="api_url" name="api_url" value="@if(!empty($lookerData)) {{$lookerData->api_url}} @endif" placeholder="Enter Api Url">
					  </div>
					  <div class="form-group">
						<label for="client_id">Client Id</label>
						<input type="text" class="form-control" id="client_id" name="client_id" value="@if(!empty($lookerData)) {{$lookerData->client_id}} @endif" placeholder="Enter Client Id">
					  </div>
					  <div class="form-group">
						<label for="client_secret">Client Secret</label>
						<input type="text" class="form-control" id="client_secret" name="client_secret" value="@if(!empty($lookerData)) {{$lookerData->client_secret}} @endif" placeholder="Enter Secret">
					  </div>
					  
					  <div class="form-group">
						<label for="host">Host</label>
						<input type="text" class="form-control" id="host" name="host" value="@if(!empty($lookerData)) {{$lookerData->host}} @endif" placeholder=" Enter Host">
					  </div>
					  <div class="form-group">
						<label for="secret">Secret</label>
						<input type="text" class="form-control" id="secret" name="secret" value="@if(!empty($lookerData)) {{$lookerData->secret}} @endif" placeholder=" Enter Secret">
					  </div>
					  
					  
					   
					<div class="card-footer">
					  <button type="submit" class="btn btn-primary">Submit</button>
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


