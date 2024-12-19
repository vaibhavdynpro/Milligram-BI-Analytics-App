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
                <h3 class="card-title">Group Master</h3>
              </div>
              <!-- /.card-header -->

              
              <!-- form start -->
              <form role="form" method="post" action="{{ route('storeGroupMaster') }}">
			      @csrf
                <div class="card-body">
                  <div class="form-group">
                    <label for="firstName">Group<span style="color:red;">*</span></label> 
                    <a href="" id="createlink" style="float:right;display: none;">Create Group</a><a href="" id="oldlink" style="float:right;">Old Group</a>
                    <input type="text" class="form-control" id="Group" name="group" placeholder="Enter Group Name" required>
                    <select class="form-control" id="groupDD" name="groupDD" data-placeholder="Select a Group" style="width: 100%;display: none">
                        <option value="" >Select Group</option>
                        @foreach($GroupData as $grp)
                        
                          <option value="{{$grp->group_id}}">{{$grp->group_name}}</option>
                        @endforeach              
                      </select>
                    <p class="valiMsg" id="grpName"></p>
                  </div>    
                  <div class="form-group">
                      <label>Role<span style="color:red;">*</span></label>
                      <select class="form-control" id="RoleDD" name="role" data-placeholder="Select a Role" style="width: 100%;" required>
                        <option value="" >Select Role</option>
                        @foreach($roleData as $role)
                        <option value="{{$role->role_id}}">{{$role->role}}</option>
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
                          <input type="checkbox" class="form-check-input ClientName ChkAllClint" id="ClientName" data-id="Chk_{{$k}}" name="clientids[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}">
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
                              <input type="checkbox" class="form-check-input dashboardclient_{{$k}} clientDash ChkAllClint" data-id="dashboardchk_{{$k}}_{{$idx}}" id="dashboardclient_{{$k}}" name="dashboard_ids[]" value="{{$dash['id']}}">
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
                                  <input type="checkbox" class="form-check-input dashboardclient_{{$k}} subdashboardchk_{{$k}}_{{$idx}} ChkAllClint" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}_{{$dash['id']}}_{{$Subdash['id']}}">
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
                            <input type="checkbox" class="form-check-input dashboardclient_{{$k}} ChkAllClint" id="dashboardclient_{{$k}}" name="sub_dashboards_id[]" value="{{$clientArr['id']}}_{{$clientArr['folder_id']}}" style="display:none;">                                                  
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
<script src="http://code.jquery.com/ui/1.10.2/jquery-ui.js" ></script>
<script type="text/javascript">
  $( document ).ready(function() {
   $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });







  $( document ).ready(function() {
    $(document).on('click','#CheckAllClient',function () {
       $('.ChkAllClint').not(this).prop('checked', this.checked);
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
  

    });  

  });
  $(document).ready(function(){
  $("#Group").blur(function(){
   var groupname = $("#Group").val();
     $.ajax({
           type:'POST',
           url:'/group_master/checkGroupName',
           data:{groupname:groupname, flag:1},
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
  
});
  $(function () {
$("#createlink").click(function(){
        $("#Group").css("display","block");
        $("#oldlink").css("display","block");
        $("#createlink").css("display","none");
        $("#groupDD").css("display","none");
        $("#groupFlag").val(0);
        $("#Group").prop('required',true);
        $("#groupDD").prop('required',false);
        return false;
      });
      $("#oldlink").click(function(){
        $("#Group").css("display","none");
        $("#groupDD").css("display","block");
        $("#oldlink").css("display","none");
        $("#createlink").css("display","block");
        $("#groupFlag").val(1);
        $("#groupDD").prop('required',true);
        $("#Group").prop('required',false);
        return false;
      });
   });  
  });


</script>


