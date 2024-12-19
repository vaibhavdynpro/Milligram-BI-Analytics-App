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
  @media screen and (max-width: 600px) {
  .topnav{overflow: hidden;
    background-color: #f5f5f5;
    margin-top: 21px;
    margin-left: 0px;
    width: 100%;}
    i.fas.fa-bars {
    display: block;
    margin-left: auto;
  }
  li.nav-item.dropdown.user-menu {
    margin-left: -120px;
  }
  nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 127%;
  }
  .navbar-expand .navbar-nav .nav-link {
    padding-right: 8rem;
    padding-left: 0rem;
  }
}
  
  </style>
<section class="content">
<div class="row">
	<div class="col-12">
	<!-- Content Wrapper. Contains page content -->
	  <div class="content-wrapper">
	   <div class="col-xl-6 col-lg-6 col-md-8 col-sm-10 mx-auto form p-4">
		<div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Create Client</h3>
              </div>
              <!-- /.card-header -->

              
              <!-- form start -->
              <form role="form" method="post" action="{{ route('storeClient') }}" enctype="multipart/form-data">
			   @csrf
                <div class="card-body">
                                  
                  <div class="form-group">
                    <label for="firstName">Client Name<span style="color:red;">*</span></label>
                    <input type="text" class="form-control" id="folder_name" name="folder_name" placeholder="Enter Client Name" required>
                  </div>		
                  <div class="form-group">
                    <label for="firstName">Folder Name<span style="color:red;">*</span></label>
                    <select class="form-control" name="folder_id" data-placeholder="Select a client" required style="width: 100%;">
                        <option value="" >Select Folder Name</option>
                        @foreach($folderDataArr as $folder)
                        <option value="{{$folder['id']}}"  >{{$folder['name']}}</option>
                        @endforeach
              
                      </select>
                  </div>    
                  <div class="form-group">
                    <label for="firstName">PHM Folder Name</label>
                    <select  class="form-control" name="phm_folder_id[]" id="phm_folder_id" multiple="multiple" style="width: 100%; height: 100%;" data-placeholder="Select a PHM Folder Name">
                      <option value="">Select PHM Folder</option>
                      @foreach($folderChildArr as $folder)
                      <option value="{{$folder['id']}}"  >{{$folder['name']}}</option>
                      @endforeach
                    </select>
                  </div>    
                  <div class="form-group">
                      <label>Schema Name</label>

                      <select class="form-control" name="schema_name" data-placeholder="Select a Schema Name" style="width: 100%;">
                        <option value="" >Select Schema Name</option>
                        <?php
                         // while(odbc_fetch_array($schema_name))                       
                         // echo "<option value=".odbc_result($schema_name, 2)."  >".odbc_result($schema_name, 2)."</option>";
                        foreach($schema_name as $val)
                        {
                         echo "<option value=".$val->schema_name.">".$val->schema_name."</option>";                          
                        }
                        ?>
              
                      </select>
                  </div>    
                  <div class="form-group">
                    <label for="firstName">Email</label>
                    <input type="text" class="form-control" id="contact_email" name="contact_email" placeholder="Enter Email id">
                  </div>    
                  <div class="form-group">
                      <label>Group Id<span style="color:red;">*</span></label>
                      <select class="form-control" name="group_id" data-placeholder="Select a Group" style="width: 100%;" required>
                        <option value="" >Select Group Id</option>
                        @foreach($groupArr as $group)
                        <option value="{{$group['id']}}"  >{{$group['name']}}</option>
                        @endforeach
              
                      </select>
                  </div>          
                  <div class="form-group">
                    <label for="external_group_id">External Group Id</label>(optional)
                    <input type="text" class="form-control" id="external_group_id" name="external_group_id" placeholder=" Enter External Group Id">
                  </div>
                  <div class="form-group">
                    <label for="models">Models<span style="color:red;">*</span></label>
                    <input type="text" class="form-control" id="models" name="models" placeholder=" Enter Models" required>
                  </div>
                  <div class="form-group">
                    <label for="models">Access Filters</label>(optional)
                    <input type="text" class="form-control" id="access_filters" name="access_filters" placeholder=" Enter Access Filters">
                  </div>
                  <div class="form-group">
                    <!-- <label for="customFile">Custom File</label> -->
                    <label for="models">Client Logo(Image size should be 225px * 50px)</label>
                    <div class="custom-file">
                      <input type="file" class="custom-file-input" id="customFile" name="uploadFile">
                      <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                  </div>
                  @if (count($errors) > 0)
                     <div class = "alert alert-danger">
                        <ul>
                           @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                           @endforeach
                        </ul>
                     </div>
                  @endif
				          <div class="form-group">
                      <label>API Enabled?<span style="color:red;">*</span></label>
                      <select class="form-control" name="is_approved" data-placeholder="is_approved" style="width: 100%;" required>
                        <option value="" >Please Select</option>
                        <option value="1" >Yes</option>
                        <option value="0" selected>No</option>
              
                      </select>
                  </div>			  
                 
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="\clients" class="btn btn-default float-right" >Cancel</a>
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
 
