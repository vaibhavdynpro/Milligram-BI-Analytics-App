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
	<!-- Content Wrapper. Contains page content -->
	  <div class="content-wrapper">
	   <div class="col-xl-6 col-lg-6 col-md-8 col-sm-10 mx-auto form p-4">
		<div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Create Role</h3>
              </div>
              <!-- /.card-header -->

              
              <!-- form start -->
              <form role="form" method="post" action="{{ route('storeRole') }}">
			   @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="firstName">Role<span style="color:red;">*</span></label>
                    <input type="text" class="form-control" id="Role" name="role" placeholder="Enter Role" required>
                  </div>		
				         <!--  <div class="form-group">
                      <label>Is Active*</label>
                      <select class="form-control" name="is_active" data-placeholder="is_active" style="width: 100%;" required>
                        <option value="" >Please Select</option>
                        <option value="1" >Yes</option>
                        <option value="0" >No</option>
              
                      </select>
                  </div>	 -->	
                   <!-- <div class="form-group">
                      <label>Users</label>
                      <select class="form-control" multiple="multiple" name="Users[]" data-placeholder="Select a Users" required style="width: 100%;">
                        <option value="" ></option>
                        @foreach($userRoleData as $users)
                        <option value="{{$users->id}}"  >{{$users->name}} {{$users->last_name}} ( {{$users->role}} )</option>
                        @endforeach              
                      </select>
                  </div> -->
                  <label>Role Access<span style="color:red;">*</span></label> 
                  <div class="row">
                      <div class="col-3">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input" id="Users" name="users">
                          <label class="form-check-label" for="Users">Users</label>                           
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_add" name="user_add">
                          <label class="form-check-label" for="user_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_edit" name="user_edit">
                          <label class="form-check-label" for="user_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_delete" name="user_delete">
                          <label class="form-check-label" for="user_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input users_cruds" id="user_view" name="user_view">
                          <label class="form-check-label" for="user_view">View</label>                                                     
                        </div> 
                      </div>
                  </div>
                  <div class="form-check col-lg-4">
                    <input type="checkbox" class="form-check-input" id="Looker" name="looker">
                    <label class="form-check-label" for="Looker">Looker</label> 
                  </div> 
                  <div class="form-check col-lg-4">
                    <input type="checkbox" class="form-check-input" id="Matillion" name="matillion">
                    <label class="form-check-label" for="Matillion">Snowflake</label> 
                  </div>
                  <div class="row">
                      <div class="col-3">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input" id="Group_module" name="group_module">
                          <label class="form-check-label" for="Group_module">Group</label> 
                        </div>
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_add" name="group_add">
                          <label class="form-check-label" for="group_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_edit" name="group_edit">
                          <label class="form-check-label" for="group_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_delete" name="group_delete">
                          <label class="form-check-label" for="group_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input group_cruds" id="group_view" name="group_view">
                          <label class="form-check-label" for="group_view">View</label>                                                     
                        </div> 
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-3">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input" id="Roles" name="roles">
                          <label class="form-check-label" for="Roles">Roles</label> 
                        </div> 
                      </div> 
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_add" name="role_add">
                          <label class="form-check-label" for="role_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_edit" name="role_edit">
                          <label class="form-check-label" for="role_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_delete" name="role_delete">
                          <label class="form-check-label" for="role_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input role_cruds" id="role_view" name="role_view">
                          <label class="form-check-label" for="role_view">View</label>                                                     
                        </div> 
                      </div>
                    </div> 
                  <div class="row">
                    <div class="col-3"> 
                      <div class="form-check col-lg-4">
                        <input type="checkbox" class="form-check-input" id="Clients" name="clients">
                        <label class="form-check-label" for="Clients">Clients</label> 
                      </div> 
                    </div> 
                    <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input client_cruds" id="client_add" name="client_add">
                          <label class="form-check-label" for="client_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input client_cruds" id="client_edit" name="client_edit">
                          <label class="form-check-label" for="client_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input client_cruds" id="client_delete" name="client_delete">
                          <label class="form-check-label" for="client_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input client_cruds" id="client_view" name="client_view">
                          <label class="form-check-label" for="client_view">View</label>                                                     
                        </div> 
                      </div>
                  </div>
                  <div class="form-check col-lg-4">
                    <input type="checkbox" class="form-check-input" id="Dashboards" name="dashboards">
                    <label class="form-check-label" for="Dashboards">Dashboards</label> 
                  </div> 
                  <div class="row">
                    <div class="col-3">  
                      <div class="form-check col-lg-4">
                        <input type="checkbox" class="form-check-input" id="Reports" name="phm">
                        <label class="form-check-label" for="Dashboards">Reports</label> 
                      </div>   
                    </div>  
                    <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_add" name="report_add">
                          <label class="form-check-label" for="report_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_edit" name="report_edit">
                          <label class="form-check-label" for="report_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_delete" name="report_delete">
                          <label class="form-check-label" for="report_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input report_cruds" id="report_view" name="report_view">
                          <label class="form-check-label" for="report_view">View</label>                                                     
                        </div> 
                      </div>
                  </div>   
                  <div class="row">
                    <div class="col-3">  
                      <div class="form-check col-lg-12">
                        <input type="checkbox" class="form-check-input" id="gen_Reports" name="gene_report">
                        <label class="form-check-label" for="gene_report">Generate Reports</label> 
                      </div>   
                    </div>  
                    <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_add" name="gene_report_add">
                          <label class="form-check-label" for="gene_report_add">Add</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_edit" name="gene_report_edit">
                          <label class="form-check-label" for="gene_report_edit">Edit</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_delete" name="gene_report_delete">
                          <label class="form-check-label" for="gene_report_delete">Delete</label>                                                     
                        </div> 
                      </div>
                      <div class="col-2">
                        <div class="form-check col-lg-4">
                          <input type="checkbox" class="form-check-input gen_Reports_cruds" id="gene_report_view" name="gene_report_view">
                          <label class="form-check-label" for="gene_report_view">View</label>                                                     
                        </div> 
                      </div>
                  </div>   
                  <div class="row">
                    <div class="col-3">  
                      <div class="form-check col-lg-12">
                        <input type="checkbox" class="form-check-input" id="invite_user" name="invite_user">
                        <label class="form-check-label" for="invite_user">Invite User</label> 
                      </div>   
                    </div> 
                  </div> 
                 <div class="form-group">
                 </div>
                <!-- /.card-body -->

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
  $(document).on('click','#Reports',function () {
     $('.report_cruds').not(this).prop('checked', this.checked);
    });
  $(document).on('click','#Roles',function () {
     $('.role_cruds').not(this).prop('checked', this.checked);
    });
  $(document).on('click','#gen_Reports',function () {
     $('.gen_Reports_cruds').not(this).prop('checked', this.checked);
    });
</script>


