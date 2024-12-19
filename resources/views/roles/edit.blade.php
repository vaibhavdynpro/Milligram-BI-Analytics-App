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
  nav.navbar.navbar-expand.navbar-white.navbar-light {
        border-bottom: 1px solid #d2cccc;
        position: fixed;
        z-index: 99999;
        width: 120%;
      }
   .navbar-expand .navbar-nav .nav-link {
    padding-right: 8rem;
    padding-left: 0rem;
  }
  li.nav-item.dropdown.user-menu {
    margin-left: -120px;
  }
  .col-2 { 
    flex: 0 0 16.666667%;
    max-width: 18.666667%;
}
}
	
  </style>
<section class="content">
<div class="row">
	<div class="col-12">
	 <!--Content Wrapper. Contains page content -->
	  <div class="content-wrapper">
		  <div class="col-xl-6 col-lg-6 col-md-8 col-sm-10 mx-auto form p-4">
			<div class="card card-primary">
				  <div class="card-header">
					<h3 class="card-title">Update Role</h3>
				  </div>
				  <!-- /.card-header -->
				  <!-- form start -->
				  <form role="form" method="post" action="{{ route('updateRole',[$RolesData[0]->role_id]) }}">
				   @csrf
					<div class="card-body">
					  <div class="form-group">
						<label for="firstName">Role<span style="color:red;">*</span></label>
						<input type="text" class="form-control" id="role" required name="role" value="{{$RolesData[0]->role}}" placeholder="Enter Role">
					  </div>
				<!-- 	  <div class="form-group">
						  <label>Is Active*</label>
						  <select class="form-control" name="is_active" data-placeholder="Please Select" style="width: 100%;" required>
							<option value="" >Please Select</option>
							@if ($RolesData[0]->is_active == 1)
							<option value="1" selected>Yes</option>
                        	<option value="0" >No</option>
							@else
							<option value="1" >Yes</option>
                        	<option value="0" selected>No</option>
				  			@endif
						  </select>
					  </div> -->
	<!-- 				  <div class="form-group">
                      <label>Users</label>
	                      <select class="form-control" multiple="multiple" name="Users[]" data-placeholder="Select a Users" required style="width: 100%;">
	                        <option value="" ></option>
	                        @foreach($userRoleData as $users)
	                        @if ($users->role_id == $RolesData[0]->role_id)
	                        <option value="{{$users->id}}" selected="selected">{{$users->name}} {{$users->last_name}} ( {{$users->role}} )</option>
	                        @else
	                        <option value="{{$users->id}}">{{$users->name}} {{$users->last_name}} ( {{$users->role}} )</option>
	                        @endif
	                        @endforeach              
	                      </select>
                  	  </div> -->
					  <label>Role Access<span style="color:red;">*</span></label> 
	                 <!--  <div class="form-check col-lg-4">
	                    <input type="checkbox" class="form-check-input" id="Users" name="users" @php if($RolesData[0]->users == 1) echo 'checked'; @endphp>
	                    <label class="form-check-label" for="Users">Users</label>    
	                  </div> --> 
	                  <div class="row">
                      <div class="col-3">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input" id="Users" name="users" @php if($RolesData[0]->users == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users" >Users</label>                           
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_add" name="user_add" @php if($RolesData[0]->user_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_edit" name="user_edit" @php if($RolesData[0]->user_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_delete" name="user_delete" @php if($RolesData[0]->user_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_view" name="user_view" @php if($RolesData[0]->user_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users">View</label>                                                     
                        </div> 
                      </div>
                  </div>
	                  <div class="form-check col-lg-4">
	                    <input type="checkbox" class="form-check-input" id="Looker" name="looker" @php if($RolesData[0]->looker == 1) echo 'checked'; @endphp>
	                    <label class="form-check-label" for="Looker">Looker</label> 
	                  </div> 
	                  <div class="form-check col-lg-4">
	                    <input type="checkbox" class="form-check-input" id="Matillion" name="matillion" @php if($RolesData[0]->matillion == 1) echo 'checked'; @endphp>
	                    <label class="form-check-label" for="Matillion">Snowflake</label> 
	                  </div>
	                  <div class="row">
	                    <div class="col-3">  
			                  <div class="form-check col-lg-4">
			                    <input type="checkbox" class="form-check-input" id="Group_module" name="group_module" @php if($RolesData[0]->group_module == 1) echo 'checked'; @endphp>
			                    <label class="form-check-label" for="Group_module">Group</label> 
			                  </div>
			                </div>
			                <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_add" name="group_add" @php if($RolesData[0]->group_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="group_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_edit" name="group_edit" @php if($RolesData[0]->group_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="group_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_delete" name="group_delete" @php if($RolesData[0]->group_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="group_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_view" name="group_view" @php if($RolesData[0]->group_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="group_view">View</label>                                                     
                        </div> 
                      </div>
			              </div>
			               <div class="row">
	                    <div class="col-3">
			                  <div class="form-check col-lg-4">
			                    <input type="checkbox" class="form-check-input" id="Roles" name="roles" @php if($RolesData[0]->roles == 1) echo 'checked'; @endphp>
			                    <label class="form-check-label" for="Roles">Roles</label> 
			                  </div>  
			                </div>  
			                <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_add" name="role_add" @php if($RolesData[0]->role_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="role_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_edit" name="role_edit" @php if($RolesData[0]->role_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="role_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_delete" name="role_delete" @php if($RolesData[0]->role_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="role_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_view" name="role_view" @php if($RolesData[0]->role_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="role_view">View</label>                                                     
                        </div> 
                      </div>
			              </div>  
	                  
	                  <div class="row">
	                    <div class="col-3"> 
	                      <div class="form-check col-lg-4">
	                        <input type="checkbox" class="form-check-input" id="Clients" name="clients" @php if($RolesData[0]->clients == 1) echo 'checked'; @endphp>
	                        <label class="form-check-label" for="Clients">Clients</label> 
	                      </div> 
	                    </div> 
	                    <div class="col-2">
	                        <div class="form-check col-lg-4">
	                          <input type="checkbox" class="form-check-input client_cruds" id="client_add" name="client_add" @php if($RolesData[0]->client_add == 1) echo 'checked'; @endphp>
	                          <label class="form-check-label" for="client_add">Add</label>                                                     
	                        </div> 
	                      </div>
	                      <div class="col-2">
	                        <div class="form-check col-lg-4">
	                          <input type="checkbox" class="form-check-input client_cruds" id="client_edit" name="client_edit" @php if($RolesData[0]->client_edit == 1) echo 'checked'; @endphp>
	                          <label class="form-check-label" for="client_edit">Edit</label>                                                     
	                        </div> 
	                      </div>
	                      <div class="col-2">
	                        <div class="form-check col-lg-4">
	                          <input type="checkbox" class="form-check-input client_cruds" id="client_delete" name="client_delete" @php if($RolesData[0]->client_delete == 1) echo 'checked'; @endphp>
	                          <label class="form-check-label" for="client_delete">Delete</label>                                                     
	                        </div> 
	                      </div>
	                      <div class="col-2">
	                        <div class="form-check col-lg-4">
	                          <input type="checkbox" class="form-check-input client_cruds" id="client_view" name="client_view" @php if($RolesData[0]->client_view == 1) echo 'checked'; @endphp>
	                          <label class="form-check-label" for="client_view">View</label>                                                     
	                        </div> 
	                      </div>
	                  </div>
	                  <div class="form-check col-lg-4">
	                    <input type="checkbox" class="form-check-input" id="Dashboards" name="dashboards" @php if($RolesData[0]->dashboards == 1) echo 'checked'; @endphp>
	                    <label class="form-check-label" for="Dashboards">Dashboards</label> 
	                  </div>
	                  <div class="row">
	                    <div class="col-3"> 
			                  <div class="form-check col-lg-4">
			                    <input type="checkbox" class="form-check-input" id="PHM" name="phm" @php if($RolesData[0]->phm == 1) echo 'checked'; @endphp>
			                    <label class="form-check-label" for="Dashboards">Reports</label> 
			                  </div>    	  
			                </div>
			                <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_add" name="report_add" @php if($RolesData[0]->report_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="report_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_edit" name="report_edit" @php if($RolesData[0]->report_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="report_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_delete" name="report_delete" @php if($RolesData[0]->report_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="report_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_view" name="report_view" @php if($RolesData[0]->report_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="report_view">View</label>                                                     
                        </div> 
                      </div>    	  
			              </div>    
			              <div class="row">
                    <div class="col-3">  
                      <div class="form-check col-lg-12">
                        <input type="checkbox" class="form-check-input" id="gen_Reports" name="gene_report" @php if($RolesData[0]->generate_report == 1) echo 'checked'; @endphp>
                        <label class="form-check-label" for="gene_report">Generate Reports</label> 
                      </div>   
                    </div>  
                    <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_add" name="gene_report_add" @php if($RolesData[0]->generate_report_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="gene_report_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_edit" name="gene_report_edit" @php if($RolesData[0]->generate_report_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="gene_report_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_delete" name="gene_report_delete" @php if($RolesData[0]->generate_report_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="gene_report_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_view" name="gene_report_view" @php if($RolesData[0]->generate_report_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="gene_report_view">View</label>                                                     
                        </div> 
                      </div>
                      </div> 
                      <div class="row">
		                    <div class="col-3">  
		                      <div class="form-check col-lg-12">
		                        <input type="checkbox" class="form-check-input" id="invite_user" name="invite_user" @php if($RolesData[0]->invite_user == 1) echo 'checked'; @endphp>
		                        <label class="form-check-label" for="invite_user">Invite User</label> 
		                      </div>   
		                    </div> 
		                  </div>
                  	  
	                 <div class="form-group">
	                 </div>
				
				
					<!-- /.card-body -->
					<!-- <input type="hidden" class="form-check-input" id="role_id" value="{{$RolesData[0]->role_id}}" name="role_id"> -->
					<div class="card-footer">
					  <button type="submit" class="btn btn-primary">Submit</button>
					  <a href="\roles" class="btn btn-default float-right" >Cancel</a>
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
<script type="text/javascript">
  $(document).on('click','#Users',function () {
       $('.users_cruds').not(this).prop('checked', this.checked);
      });
  $(document).on('click','#Clients',function () {
     $('.client_cruds').not(this).prop('checked', this.checked);
    });
  $(document).on('click','#Group_module',function () {
     $('.group_cruds').not(this).prop('checked', this.checked);
    });
  $(document).on('click','#PHM',function () {
     $('.report_cruds').not(this).prop('checked', this.checked);
    });
  $(document).on('click','#Roles',function () {
     $('.role_cruds').not(this).prop('checked', this.checked);
    });
  $(document).on('click','#gen_Reports',function () {
     $('.gen_Reports_cruds').not(this).prop('checked', this.checked);
    });
</script>
