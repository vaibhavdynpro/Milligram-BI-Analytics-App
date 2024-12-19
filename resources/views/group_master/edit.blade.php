<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
@extends('layouts.app')

@section('content')

<style type="text/css">
  label.form-check-label.sd {
    color: #0674af;
}
p#grpName {
    margin-left: 15px;
    font-size: 14px;
    color: red;
}
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
    margin-left: -8px;
  }
  nav.navbar.navbar-expand.navbar-white.navbar-light {
    border-bottom: 1px solid #d2cccc;
    position: fixed;
    z-index: 99999;
    width: 125%;
	}
  .navbar-expand .navbar-nav .nav-link {
    padding-right: 8rem;
    padding-left: 0rem;
}
  li.nav-item.dropdown.user-menu {
    margin-left: -120px;
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
              </div>
              <!-- /.card-header -->

              
              <!-- form start -->
              <form role="form" method="post" action="{{ route('updateGroupMaster',[$group_id,$role_id]) }}">
			   @csrf
                
                <div class="card-body">
                  <div class="form-group">
                    <label for="firstName">Group<span style="color:red;">*</span></label><a href="" id="createlink" style="float:right;">Create Group</a><a href="" id="oldlink" style="float:right;display: none;">Old Group</a>
                    <input type="hidden" name="group_id" id="group_id" value="{{$group_id}}">
                    <input type="text" class="form-control" id="Group" name="group" placeholder="Enter Group Name" style="display: none">
                    
                    <select class="form-control" id="groupDD" name="groupDD" data-placeholder="Select a Group" style="width: 100%;">
                        <option value="" >Select Group</option>
                        @foreach($GroupData as $grp)
                          @if($group_id == $grp->group_id)  
                          <option value="{{$grp->group_id}}" selected>{{$grp->group_name}}</option>
                          @else
                          <option value="{{$grp->group_id}}">{{$grp->group_name}}</option>
                          @endif
                        @endforeach              
                      </select>
                    <p class="valiMsg" id="grpName"></p>
                  </div>       
                  <div class="form-group">
                      <label>Role<span style="color:red;">*</span></label>
                      <select class="form-control" id="RoleDD" name="role" data-placeholder="Select a Role" style="width: 100%;" required>
                        <option value="" >Select Role</option>
                        @foreach($roleData as $role)   
                          @if($role_id == $role->role_id)                     
                          <option value="{{$role->role_id}}" selected>{{$role->role}}</option>
                          @else
                          <option value="{{$role->role_id}}">{{$role->role}}</option>
                          @endif
                        @endforeach
              
                      </select>
                  </div>                 

                <label>Dashboard Access*</label>
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
                          <input type="checkbox" class="form-check-input ClientName ChkAllClint" id="ClientName" data-id="Chk_{{$k}}" name="clientids[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}" checked>
                          @else
                          <input type="checkbox" class="form-check-input ClientName ChkAllClint" id="ClientName" data-id="Chk_{{$k}}" name="clientids[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}">
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
                            <div class="form-check col-lg-">
                              @if(isset($SelectedClientData[$clientArr['id']][$dash['id']]))
                              <input type="checkbox" class="form-check-input dashboardclient_{{$k}} clientDash ChkAllClint" data-id="dashboardchk_{{$k}}_{{$idx}}" id="dashboardclient_{{$k}}" name="dashboard_ids[]" value="{{$dash['id']}}" checked>
                              @else
                              <input type="checkbox" class="form-check-input dashboardclient_{{$k}} clientDash ChkAllClint" data-id="dashboardchk_{{$k}}_{{$idx}}" id="dashboardclient_{{$k}}" name="dashboard_ids[]" value="{{$dash['id']}}">
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
                                  <input type="checkbox" class="form-check-input dashboardclient_{{$k}} subdashboardchk_{{$k}}_{{$idx}} ChkAllClint" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}_{{$dash['id']}}_{{$Subdash['id']}}" checked>
                                  @else
                                  <input type="checkbox" class="form-check-input dashboardclient_{{$k}} subdashboardchk_{{$k}}_{{$idx}} ChkAllClint" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}_{{$dash['id']}}_{{$Subdash['id']}}">
                                  @endif
                                  <label class="form-check-label sd" for="dashboard">{{$Subdash['title']}}</label>                                                    
                                </div> 
                              </div>
                            </div>
                          </div>
                          @endforeach 
                        @endif


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
                            <input type="checkbox" class="form-check-input dashboardclient_{{$k}} ChkAllClint" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}" style="display:none;" checked>
                            @else
                            <input type="checkbox" class="form-check-input dashboardclient_{{$k}} ChkAllClint" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}" style="display:none;">
                            @endif

                          </div> 
                        </div>
                      </div>
                    @endif
                    @endforeach 
                 <div class="form-group">
                 </div>
                <!-- /.card-body -->
                <input type="hidden" name="groupFlag" id="groupFlag" value="0">
                <div class="card-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                  <a href="\group_master" class="btn btn-default float-right" >Cancel</a>
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
<!-- <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script> -->
<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</script> <script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js" ></script>
<script type="text/javascript">
  $( document ).ready(function() {
   $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });

    $("#RoleDD").change(function () {
          var role_id = this.value;
          $.ajax({
             type:'POST',
             url:'/groups/getRoleDetails',
             data:{role_id:role_id},
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

                console.log(data.roleData[0]);


                for (let i = 0; i < data.userRoleData.length; ++i) {
                $("#UserRoleDD option[value='"+data.userRoleData[i]['id']+"']").attr("selected","selected");
                    
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
        return false;
      });
      $("#oldlink").click(function(){
        $("#Group").css("display","none");
        $("#groupDD").css("display","block");
        $("#oldlink").css("display","none");
        $("#createlink").css("display","block");
        $("#groupFlag").val(0);
        $("#groupDD").prop('required',true);
        return false;
      });

    });  


  $(document).ready(function(){
  if($("#groupFlag").val() == 1){
  $("#Group").blur(function(){
   var groupname = $("#Group").val();
   var groupid = $("#group_id").val();
     $.ajax({
           type:'POST',
           url:'/group_master/checkGroupName',
           data:{groupname:groupname, flag:2, groupid:groupid},
           success:function(data){
            if(data.resp == 1)
            {
              $("#grpName").append("<p>Group Name already exist</p>");
              $(":submit").attr("disabled", true);
            }
            else
            {
              $('#grpName').empty();
              $(":submit").attr("disabled", false);
            }
           }
        });
  });
}
  
});

$(function () {
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


