@extends('layouts.app')

@section('content')
<style type="text/css">
  	nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 100%;    
    margin-top: -61px;
	}
	i.fas.fa-bars {
    display: none;
	}
		@media screen and (max-width: 600px) {
      nav.navbar.navbar-expand.navbar-white.navbar-light {
      border-bottom: 1px solid #d2cccc;
      position: fixed;
      z-index: 99999;
      width: 124%;
      cursor: pointer;
  }
	li.nav-item.dropdown.user-menu {
    margin-left: -120px;
	}
  .navbar-expand .navbar-nav .nav-link {
    padding-right: 8rem;
    padding-left: 0rem;
  }
  .col-2 { 
      flex: 0 0 16.666667%;
      max-width: 18.666667%;
  }
  .mob-row-style{
    display: block;
  }
 .mobile-col-style{
  max-width: 100%;
 }
}
  </style>
<section class="content">
<div class="content-wrapper">
<div class="row mob-row-style">
	<div class="col-6 mobile-col-style">
	 <!--Content Wrapper. Contains page content -->
		  <div class="col-xl-12 col-lg-12 col-md-4 col-sm-4 form p-4">
			<div class="card card-primary">
				  <div class="card-header">
					<h3 class="card-title">Update User</h3>
				  </div>
				  <!-- /.card-header -->
				  <!-- form start -->
				  <form role="form" method="post" action="{{ route('updateUser',[$id]) }}">
				   @csrf
					<div class="card-body">
					  <div class="form-group">
						<label for="firstName">First Name<span style="color:red;">*</span></label>
						<input type="text" class="form-control" id="first_name" required name="first_name" value="{{$userData->name}}" placeholder="Enter First Name">
            <input type="hidden" name="is_signup" value="{{$userData->is_signup}}">
					  </div>
					  <div class="form-group">
						<label for="lastName">Last Name</label>(optional)
						<input type="text" class="form-control" id="last_name" name="last_name" value="{{$userData->last_name}}" placeholder="Enter Last Name">
					  </div>
					  <div class="form-group">
						<label for="exampleInputEmail1">Email address<span style="color:red;">*</span></label>
						<input type="email" class="form-control" id="email" name="email" required value="{{$userData->email}}" placeholder="Enter email">
					  </div>
					  
					  
					  <div class="form-group">
						<label for="exampleInputPassword1">Password</label>
						<input type="password" class="form-control" id="password" name="password" placeholder=" Enter Password">
					  </div>
				

					  
					 
					  
					  <div class="form-group">
						  <label>Permissions<span style="color:red;">*</span></label>
						  <select class="form-control"  required name="permissions" data-placeholder="Select a Permission" style="width: 100%;">
							<option value="" >Select Permission</option>
							<option value="viewer" @php if($userData->permissions == 'viewer') echo 'selected'; @endphp >Viewer</option>
							<option value="explorer" @php if($userData->permissions == 'explorer') echo 'selected'; @endphp >Explorer</option>
              <option value="schedular" @php if($userData->permissions == 'schedular') echo 'selected'; @endphp >Schedular</option>
							
						  </select>
						</div>

					  <div class="form-group">
						<label for="models">User Attributes</label>(optional)
						<input type="text" class="form-control" id="user_attributes" name="user_attributes" value="{{$userData->user_attributes}}" placeholder=" Enter User Attributes">
					  </div>
					  <div class="form-group">
                    <label for="firstName">Group<span style="color:red;">*</span></label>
                    <input type="text" class="form-control" id="Group" name="group" placeholder="Enter Group Name" style="display:none;">
                    <select class="form-control" id="groupDD" name="groupDD" data-placeholder="Select a Group" style="width: 100%;" required>
                        <option value="" >Select Group</option>
                        @foreach($GroupData as $grp)
                          @if(isset($RolesData[0]) && $RolesData[0]->group_id == $grp->group_id)  
                          <option value="{{$grp->group_id}}" selected>{{$grp->group_name}}</option>
                          @elseif(isset($grp_dtl[0]) && $grp_dtl[0]['group_id'] == $grp->group_id)
                          <option value="{{$grp->group_id}}" selected>{{$grp->group_name}}</option>
                          @else
                          <option value="{{$grp->group_id}}">{{$grp->group_name}}</option>
                          @endif
                        @endforeach              
                      </select>
                  </div>    
                  <div class="form-group">
                      <label>Role<span style="color:red;">*</span></label>
                      <select class="form-control" id="RoleDD" name="role" data-placeholder="Select a Role" style="width: 100%;" required>
                        <option value="" >Select Role</option>
                        @foreach($roleData as $role)   
                          @if(isset($RolesData[0]) && $RolesData[0]->role_id == $role->role_id)                     
                          <option value="{{$role->role_id}}" selected>{{$role->role}}</option>
                          @else
                          <option value="{{$role->role_id}}">{{$role->role}}</option>
                          @endif
                        @endforeach
              
                      </select>
                  </div>
                  
					  <label>Role Access<span style="color:red;">*</span></label> 
                  <div class="row">
                      <div class="col-3">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input" id="UsersCrud" name="users" @php if(isset($RolesData[0]) && $RolesData[0]->users == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users" >Users</label>                           
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_add" name="user_add" @php if(isset($RolesData[0]) && $RolesData[0]->user_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_edit" name="user_edit" @php if(isset($RolesData[0]) && $RolesData[0]->user_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_delete" name="user_delete" @php if(isset($RolesData[0]) && $RolesData[0]->user_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_view" name="user_view" @php if(isset($RolesData[0]) && $RolesData[0]->user_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Users">View</label>                                                     
                        </div> 
                      </div>
                  </div>
                    <div class="form-check col-lg-4">
                      <input type="checkbox" class="form-check-input" id="Looker" name="looker" @php if(isset($RolesData[0]) && $RolesData[0]->looker == 1) echo 'checked'; @endphp>
                      <label class="form-check-label" for="Looker">Looker</label> 
                    </div> 
                    <div class="form-check col-lg-4">
                      <input type="checkbox" class="form-check-input" id="Matillion" name="snowflake" @php if(isset($RolesData[0]) && $RolesData[0]->snowflake == 1) echo 'checked'; @endphp>
                      <label class="form-check-label" for="Matillion">Snowflake</label> 
                    </div> 
                    
                    <div class="row">
                      <div class="col-3">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input" id="Group_module" name="group_module" @php if(isset($RolesData[0]) && $RolesData[0]->group_module == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Group_module">Group</label> 
                        </div>
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_add" name="group_add" @php if(isset($RolesData[0]) && $RolesData[0]->group_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="group_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_edit" name="group_edit" @php if(isset($RolesData[0]) && $RolesData[0]->group_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="group_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_delete" name="group_delete" @php if(isset($RolesData[0]) && $RolesData[0]->group_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="group_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_view" name="group_view" @php if(isset($RolesData[0]) && $RolesData[0]->group_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="group_view">View</label>                                                     
                        </div> 
                      </div>
                    </div> 
                 
                    <div class="row">
                      <div class="col-3">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input" id="Roles" name="roles" @php if(isset($RolesData[0]) && $RolesData[0]->roles == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Roles">Roles</label> 
                        </div> 
                      </div> 
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_add" name="role_add" @php if(isset($RolesData[0]) && $RolesData[0]->role_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="role_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_edit" name="role_edit" @php if(isset($RolesData[0]) && $RolesData[0]->role_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="role_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_delete" name="role_delete" @php if(isset($RolesData[0]) && $RolesData[0]->role_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="role_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_view" name="role_view" @php if(isset($RolesData[0]) && $RolesData[0]->role_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="role_view">View</label>                                                     
                        </div> 
                      </div>
                    </div> 
                    <div class="row">
                      <div class="col-3"> 
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input" id="ClientSel" name="clients" @php if(isset($RolesData[0]) && $RolesData[0]->clients == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="Clients">Clients</label> 
                        </div> 
                      </div> 
                      <div class="col-2">
                          <div class="form-check col-lg-4">
                            <input type="checkbox" class="form-check-input client_cruds" id="client_add" name="client_add" @php if(isset($RolesData[0]) && $RolesData[0]->client_add == 1) echo 'checked'; @endphp>
                            <label class="form-check-label" for="Users">Add</label>                                                     
                          </div> 
                        </div>
                        <div class="col-2">
                          <div class="form-check col-lg-4">
                            <input type="checkbox" class="form-check-input client_cruds" id="client_edit" name="client_edit" @php if(isset($RolesData[0]) && $RolesData[0]->client_edit == 1) echo 'checked'; @endphp>
                            <label class="form-check-label" for="Users">Edit</label>                                                     
                          </div> 
                        </div>
                        <div class="col-2">
                          <div class="form-check col-lg-4">
                            <input type="checkbox" class="form-check-input client_cruds" id="client_delete" name="client_delete" @php if(isset($RolesData[0]) && $RolesData[0]->client_delete == 1) echo 'checked'; @endphp>
                            <label class="form-check-label" for="Users">Delete</label>                                                     
                          </div> 
                        </div>
                        <div class="col-2">
                          <div class="form-check col-lg-4">
                            <input type="checkbox" class="form-check-input client_cruds" id="client_view" name="client_view" @php if(isset($RolesData[0]) && $RolesData[0]->client_view == 1) echo 'checked'; @endphp>
                            <label class="form-check-label" for="Users">View</label>                                                     
                          </div> 
                        </div>
                    </div>
                   <!--  <div class="form-check col-lg-4">
                      <input type="checkbox" class="form-check-input" id="Dashboards" name="dashboards" @php if(isset($RolesData[0]) && $RolesData[0]->dashboards == 1) echo 'checked'; @endphp>
                      <label class="form-check-label" for="Dashboards">Dashboards</label> 
                    </div> -->
                    
                  <div class="row">
                    <div class="col-3">  
                      <div class="form-check col-lg-4">
                        <input type="checkbox" class="form-check-input" id="PHM" name="reports" @php if(isset($RolesData[0]) && $RolesData[0]->reports == 1) echo 'checked'; @endphp>
                        <label class="form-check-label" for="PHM">Reports</label> 
                      </div>     
                    </div>  
                    <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_add" name="report_add" @php if(isset($RolesData[0]) && $RolesData[0]->report_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="report_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_edit" name="report_edit" @php if(isset($RolesData[0]) && $RolesData[0]->report_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="report_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_delete" name="report_delete" @php if(isset($RolesData[0]) && $RolesData[0]->report_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="report_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_view" name="report_view" @php if(isset($RolesData[0]) && $RolesData[0]->report_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="report_view">View</label>                                                     
                        </div> 
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-3">  
                      <div class="form-check col-lg-12">
                        <input type="checkbox" class="form-check-input" id="gen_Reports" name="gene_report" @php if(isset($RolesData[0]) && $RolesData[0]->generate_report == 1) echo 'checked'; @endphp>
                        <label class="form-check-label" for="gene_report">Generate Reports</label> 
                      </div>   
                    </div>  
                    <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_add" name="gene_report_add" @php if(isset($RolesData[0]) && $RolesData[0]->generate_report_add == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="gene_report_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_edit" name="gene_report_edit" @php if(isset($RolesData[0]) && $RolesData[0]->generate_report_edit == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="gene_report_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_delete" name="gene_report_delete" @php if(isset($RolesData[0]) && $RolesData[0]->generate_report_delete == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="gene_report_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_view" name="gene_report_view" @php if(isset($RolesData[0]) && $RolesData[0]->generate_report_view == 1) echo 'checked'; @endphp>
                          <label class="form-check-label" for="gene_report_view">View</label>                                                     
                        </div> 
                      </div>
                  </div>
                  <div class="row">
                    <div class="col-3">  
                      <div class="form-check col-lg-12">
                        <input type="checkbox" class="form-check-input" id="invite_user" name="invite_user" @php if(isset($RolesData[0]) && $RolesData[0]->invite_user == 1) echo 'checked'; @endphp>
                        <label class="form-check-label" for="invite_user">Invite User</label> 
                      </div>   
                    </div>  
                  </div>
                  <br/>
                  @if(auth()->user()->role == 1)
                  <div class="form-group">
                      <label>Status*</label>
                      <select class="form-control" id="is_active" name="is_active" data-placeholder="Select a status" style="width: 100%;" required>
                        <option value="" >Select status</option>
                          @if(isset($activeflag) && $activeflag == 0)                     
                          <option value="0" selected>In Active</option>
                          <option value="1" >Active</option>
                          @else
                          <option value="0" >In Active</option>
                          <option value="1" selected>Active</option>
                          @endif
              
                      </select>
                  </div>
                  @endif
					</div>
					<!-- /.card-body -->
					<!-- <input type="hidden" class="form-check-input" id="user_id" value="{{$userData->id}}" name="user_id"> -->
					<!-- <div class="card-footer">
					  <button type="submit" class="btn btn-primary">Submit</button>
					  <a href="\users" class="btn btn-default float-right" >Cancel</a>
					</div> -->
				</div>
				<!-- /.card -->
		
		</div>
  </div>
  <div class="col-6 mobile-col-style">
  <!-- Content Wrapper.,.. Contains page content -->
     <div class="col-xl-12 col-lg-12 col-md-4 col-sm-4 form p-4">
    <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Dashboard Access</h3>
              </div>
              <!-- /.card-header -->

              
              <!-- form start -->
              
                <div class="card-body">
                    
                    <div class="row">
                      <div class="col-6">
                        <div class="form-check col-lg-8">
                          <input type="checkbox" class="form-check-input CheckAllClient" id="CheckAllClient">
                          <label class="form-check-label" for="Client"><b style="color: #007bff;">Check All</b></label>
                        </div> 
                      </div>
                    </div>
                     @foreach($ClientDash as $k => $clientArr)
                    <div class="row">
                      <div class="col-6">
                        <div class="form-check col-lg-12">
	                          @if(isset($SelectedClientData[$clientArr['id']]))
	                          <input type="checkbox" class="form-check-input ClientName ChkAllClint"  id="ClientName" data-id="Chk_{{$k}}" name="clientids[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}" select-id="{{$clientArr['id']}}" checked>
	                          @else
	                          <input type="checkbox" class="form-check-input ClientName ChkAllClint" id="ClientName" data-id="Chk_{{$k}}" name="clientids[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}" select-id="{{$clientArr['id']}}">
	                          @endif                          
                            <label class="form-check-label" for="Client"><b>{{$clientArr['name']}}</b></label>    
                            <a href="#democlnt_{{$clientArr['id']}}" class="exp" data-toggle="collapse">&nbsp;&nbsp;&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>                               
                        </div> 
                      </div>
                    </div>  
                    @if(isset($clientArr['dashboard']) && !empty($clientArr['dashboard']))
                      @foreach($clientArr['dashboard'] as $idx => $dash)
                      <?php  $fname = explode(".",$dash['name']);  ?>  
                      <div id="democlnt_{{$clientArr['id']}}" class="collapse">                     
                        <div class="row">
                          <div class="col-3">
                            <div class="form-check col-lg-6">
                            </div>
                          </div>
                          <div class="col-9">
                            <div class="form-check col-lg-6">
	                              @if(isset($SelectedClientData[$clientArr['id']][$dash['id']]))
	                              <input type="checkbox" class="form-check-input dashboardclient_{{$k}} clientDash ChkAllClint" data-id="dashboardchk_{{$k}}_{{$idx}}" id="dashboardclient_{{$k}}" name="dashboard_ids[]" value="{{$dash['id']}}" select-id="{{$clientArr['id']}}_{{$dash['id']}}" checked>
	                              @else
	                              <input type="checkbox" class="form-check-input dashboardclient_{{$k}} clientDash ChkAllClint" data-id="dashboardchk_{{$k}}_{{$idx}}" id="dashboardclient_{{$k}}" name="dashboard_ids[]" value="{{$dash['id']}}" select-id="{{$clientArr['id']}}_{{$dash['id']}}">
	                              @endif
                                @if (!empty($fname[1])) 
                                <label class="form-check-label" for="dashboard">{{$fname[1]}}</label>
                                <a href="#demo_{{$clientArr['id']}}_{{$dash['id']}}" class="exp" data-toggle="collapse">&nbsp;&nbsp;&nbsp;<i class="fa fa-expand" aria-hidden="true"></i></a>  
                                @else
                                <label class="form-check-label" for="dashboard">{{$fname[0]}}</label> 
                                <a href="#demo_{{$clientArr['id']}}_{{$dash['id']}}" class="exp" data-toggle="collapse">&nbsp;&nbsp;&nbsp;<i class="fa fa-expand" aria-hidden="true"></i></a>  
                              @endif                  
                            </div> 
                          </div>
                        </div>  
                        @if(isset($ClientSubDash[$k]['dashboard'][$dash['id']]))
                          @foreach($ClientSubDash[$k]['dashboard'][$dash['id']] as $Subdash)
                          <div id="demo_{{$clientArr['id']}}_{{$dash['id']}}" class="collapse">
                            <div class="row">
                              <div class="col-4">
                                <div class="form-check col-lg-6">
                                </div>
                              </div>
                              <div class="col-8">
                                <div class="form-check col-lg-12">
                                  @if(isset($SelectedClientData[$clientArr['id']][$dash['id']]) && in_array($Subdash['id'],$SelectedClientData[$clientArr['id']][$dash['id']]))
                                  <input type="checkbox" class="form-check-input dashboardclient_{{$k}} subdashboardchk_{{$k}}_{{$idx}} ChkAllClint" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}_{{$dash['id']}}_{{$Subdash['id']}}" select-id="{{$clientArr['id']}}_{{$dash['id']}}_{{$Subdash['id']}}" checked>
                                  @else
                                  <input type="checkbox" class="form-check-input dashboardclient_{{$k}} subdashboardchk_{{$k}}_{{$idx}} ChkAllClint" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}_{{$dash['id']}}_{{$Subdash['id']}}" select-id="{{$clientArr['id']}}_{{$dash['id']}}_{{$Subdash['id']}}">
                                  @endif
                                  <label class="form-check-label sd" for="dashboard">{{$Subdash['title']}}</label>                                                    
                                </div> 
                              </div>
                            </div>
                          </div>
                          @endforeach 
                        @endif

                        </div> 
                      @endforeach 
                    @else
                    <div class="row">
                        <div class="col-4">
                          <div class="form-check col-lg-6">
                          </div>
                        </div>
                        <div class="col-8">
                          <div class="form-check col-lg-12">
                            @if(isset($SelectedClientData[$clientArr['id']]))
                            <input type="checkbox" class="form-check-input dashboardclient_{{$k}} ChkAllClint clientDashsub" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}" select-id="{{$clientArr['id']}}" style="display:none;" checked>
                            @else
                            <input type="checkbox" class="form-check-input dashboardclient_{{$k}} ChkAllClint clientDashsub" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}" select-id="{{$clientArr['id']}}" style="display:none;">
                            @endif                                       
                          </div> 
                        </div>
                      </div>
                    @endif
                    @endforeach 
                  <input type="hidden" name="groupFlag" id="groupFlag" value="0">
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="\users" class="btn btn-default float-right" >Cancel</a>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</script> <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js" ></script>
<script type="text/javascript">
  $( document ).ready(function() {
   $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });

    $("#groupDD").change(function () {
      var grp_id = this.value;
      $.ajax({
             type:'POST',
             url:'/groups/getGroupRoleDetails',
             data:{group_id:grp_id},
             success:function(data){
              $('#RoleDD').empty();
              var options = "<option value=''>Select Role</option>";
              $.each(data.rolesData, function(index, value) {
                 options += "<option value="+value.role_id+">"+value.role+"</option>";
              });
              $('#RoleDD').append(options);
              console.log(options);
              }
             
           });
    });
    $("#RoleDD").change(function () {
          var role_id = this.value;
          var grp_id = $('#groupDD').val();
          $.ajax({
             type:'POST',
             url:'/groups/getRoleDetails',
             data:{role_id:role_id,group_id:grp_id},
             success:function(data){
               (data.roleData[0]['users'] === 1) ? $('#UsersCrud').prop('checked', true) :$('#UsersCrud').prop('checked', false);
               (data.roleData[0]['user_add'] === 1) ? $('#user_add').prop('checked', true) :$('#user_add').prop('checked', false);
               (data.roleData[0]['user_edit'] === 1) ? $('#user_edit').prop('checked', true) :$('#user_edit').prop('checked', false);
               (data.roleData[0]['user_delete'] === 1) ? $('#user_delete').prop('checked', true) :$('#user_delete').prop('checked', false);
               (data.roleData[0]['user_view'] === 1) ? $('#user_view').prop('checked', true) :$('#user_view').prop('checked', false);

               (data.roleData[0]['looker'] === 1) ? $('#Looker').prop('checked', true) :$('#Looker').prop('checked', false);
               (data.roleData[0]['matillion'] === 1) ? $('#Matillion').prop('checked', true) :$('#Matillion').prop('checked', false);

               (data.roleData[0]['roles'] === 1) ? $('#Roles').prop('checked', true) :$('#Roles').prop('checked', false);
               (data.roleData[0]['role_add'] === 1) ? $('#role_add').prop('checked', true) :$('#role_add').prop('checked', false);
               (data.roleData[0]['role_edit'] === 1) ? $('#role_edit').prop('checked', true) :$('#role_edit').prop('checked', false);
               (data.roleData[0]['role_delete'] === 1) ? $('#role_delete').prop('checked', true) :$('#role_delete').prop('checked', false);
               (data.roleData[0]['role_view'] === 1) ? $('#role_view').prop('checked', true) :$('#role_view').prop('checked', false);

               (data.roleData[0]['group_module'] === 1) ? $('#Group_module').prop('checked', true) :$('#Group_module').prop('checked', false);
               (data.roleData[0]['group_add'] === 1) ? $('#group_add').prop('checked', true) :$('#group_add').prop('checked', false);
               (data.roleData[0]['group_edit'] === 1) ? $('#group_edit').prop('checked', true) :$('#group_edit').prop('checked', false);
               (data.roleData[0]['group_delete'] === 1) ? $('#group_delete').prop('checked', true) :$('#group_delete').prop('checked', false);
               (data.roleData[0]['group_view'] === 1) ? $('#group_view').prop('checked', true) :$('#group_view').prop('checked', false);

               (data.roleData[0]['clients'] === 1) ? $('#ClientSel').prop('checked', true) :$('#ClientSel').prop('checked', false);
               (data.roleData[0]['client_add'] === 1) ? $('#client_add').prop('checked', true) :$('#client_add').prop('checked', false);
               (data.roleData[0]['client_edit'] === 1) ? $('#client_edit').prop('checked', true) :$('#client_edit').prop('checked', false);
               (data.roleData[0]['client_delete'] === 1) ? $('#client_delete').prop('checked', true) :$('#client_delete').prop('checked', false);
               (data.roleData[0]['client_view'] === 1) ? $('#client_view').prop('checked', true) :$('#client_view').prop('checked', false);


               (data.roleData[0]['dashboards'] === 1) ? $('#Dashboards').prop('checked', true) :$('#Dashboards').prop('checked', false);

               (data.roleData[0]['phm'] === 1) ? $('#PHM').prop('checked', true) :$('#PHM').prop('checked', false);
               (data.roleData[0]['report_add'] === 1) ? $('#report_add').prop('checked', true) :$('#report_add').prop('checked', false);
               (data.roleData[0]['report_edit'] === 1) ? $('#report_edit').prop('checked', true) :$('#report_edit').prop('checked', false);
               (data.roleData[0]['report_delete'] === 1) ? $('#report_delete').prop('checked', true) :$('#report_delete').prop('checked', false);
               (data.roleData[0]['report_view'] === 1) ? $('#report_view').prop('checked', true) :$('#report_view').prop('checked', false);

         (data.roleData[0]['generate_report'] === 1) ? $('#gen_Reports').prop('checked', true) :$('#gen_Reports').prop('checked', false);
         (data.roleData[0]['generate_report_add'] === 1) ? $('#gene_report_add').prop('checked', true) :$('#gene_report_add').prop('checked', false);
         (data.roleData[0]['generate_report_edit'] === 1) ? $('#gene_report_edit').prop('checked', true) :$('#gene_report_edit').prop('checked', false);
         (data.roleData[0]['generate_report_delete'] === 1) ? $('#gene_report_delete').prop('checked', true) :$('#gene_report_delete').prop('checked', false);
         (data.roleData[0]['generate_report_view'] === 1) ? $('#gene_report_view').prop('checked', true) :$('#gene_report_view').prop('checked', false);

         (data.roleData[0]['invite_user'] === 1) ? $('#invite_user').prop('checked', true) :$('#invite_user').prop('checked', false);
                // console.log(data.roleData[0]);
                for (let i = 0; i < data.userRoleData.length; ++i) {
                $("#UserRoleDD option[value='"+data.userRoleData[i]['id']+"']").attr("selected","selected");                    
                }

                $('.ClientName').prop('checked', false);
                $('.clientDash').prop('checked', false);
                $('.clientDashsub').prop('checked', false);
                // console.log(data.dashobardMapping);
                for(var i=0;i<data.ClientList.length;i++)
                {                    
                  if(typeof(data.dashobardMapping[data.ClientList[i].id]) != "undefined" && data.dashobardMapping[data.ClientList[i].id] !== null)
                  {
                    $("input[select-id='" + data.ClientList[i].id + "']").prop('checked', true);
                    var dashArr = data.dashobardMapping[data.ClientList[i].id];
                    $.each(dashArr, function(index, value) {
                      $("input[select-id='" + data.ClientList[i].id +"_"+index+ "']").prop('checked', true);
                      var subDashArr = data.dashobardMapping[data.ClientList[i].id][index];
                      $.each(subDashArr, function(indexx, valuee) {
                      $("input[select-id='" + data.ClientList[i].id +"_"+index+"_"+valuee+"']").prop('checked', true)
                        // console.log(data.ClientList[i].id+"_"+index+"_"+valuee);
                      });
                    });
                  }
                }




             }
          });
    });

  });





  $( document ).ready(function() {
    $(document).on('click','#CheckAllClient',function () {
       $('.ChkAllClint').not(this).prop('checked', this.checked);
      });
    $(document).on('click','#UsersCrud',function () {
       $('.users_cruds').not(this).prop('checked', this.checked);
      });
    $(document).on('click','#ClientSel',function () {
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
    $(function () {
      $('.ClientName').click(function () {
        var id = $(this).attr('data-id');
        var getid = id.split("_");
       $('.dashboardclient_'+getid[1]).not(this).prop('checked', this.checked);
      });
      $('.clientDash').click(function () {
        var id = $(this).attr('data-id');
       //  var getid = id.split("_");
       $('.sub'+id).not(this).prop('checked', this.checked);
      });
      $("#createlink").click(function(){
        $("#Group").css("display","block");
        $("#oldlink").css("display","block");
        $("#createlink").css("display","none");
        $("#groupDD").css("display","none");
        $("#groupFlag").val(1);
        $("#Group").prop('required',true);
        $("#groupDD").prop('required',false);
        return false;
      });
      $("#oldlink").click(function(){
        $("#Group").css("display","none");
        $("#groupDD").css("display","block");
        $("#oldlink").css("display","none");
        $("#createlink").css("display","block");
        $("#groupFlag").val(0);
        $("#groupDD").prop('required',true);
        $("#Group").prop('required',false);
        return false;
      });

    });  

  });


</script>


