@extends('layouts.app',['Client_Name_Title' => $Client_Name_Title])

@section('content')

<link href="{{ asset('css/menu.css') }}" rel="stylesheet">
<style>
   ::-webkit-scrollbar{width:2px;height:2px;}
::-webkit-scrollbar-button{width:2px;height:2px;}
a {
    color: black;
    text-decoration: none;
}
.MessageDiv {
    text-align: center;
    font-size: 28px;
    color: #006bab;
    margin-top: 27px;
}
</style>

<div class="topnav" id="myTopnav">
@if($msg == "")
  @if (!empty($lookerDashboardsArr)) 
    @foreach($lookerDashboardsArr as $k => $link)
     @if($k == 0)
      @php
       $dash_id = $link['id'];
       $client_id = $folder_id;
      @endphp
      @endif    
    @endforeach
  @endif
  <div id="folderdiv">
  @if (!empty($folderChildArr)) 
    @foreach($folderChildArr as $k => $folder)
      <div class="dropdown">
        <?php 
        $fname = explode(".",$folder['name']);
        ?>
        @if (!empty($fname[1])) 
        <button class="dropbtn" id="active_{{$k}}">{{$fname[1]}}</button>
        @else
        <button class="dropbtn" id="active_{{$k}}">{{$fname[0]}}</button>
        @endif
          <div class="dropdown-content">
            <?php
            $names = array();
            foreach($SubFolderDashboards[$folder['id']] as $key => $subfolder){
              $names[$key] = $subfolder['title']; 
            }
            array_multisort($names, SORT_ASC, $SubFolderDashboards[$folder['id']]);
            ?>
            @foreach($SubFolderDashboards[$folder['id']] as $subfolder)
              <a href="#" data-dashboard-id="{{$subfolder['id']}}" data-client-id="{{$folder_id}}" data-active-id="{{$k}}" class="dashboard_link">{{$subfolder['title']}}</a>
            @endforeach
          </div>
      </div>
    @endforeach
  @endif
  @if (!$PhmReportData->isEmpty()) 
    @if ($reportFlag == 0)
    <div class="dropdown">
      <button class="dropbtn" id="active_reports">Reports</button>
        <div class="dropdown-content">
        @foreach($PhmReportData as $kk => $reportData)
          <a href="/home/getPHMReport/{{$reportData->file_path}}" data-active-id="{{$kk}}" class="phmreport_link">{{$reportData->name}}</a>
        @endforeach
        </div>
    </div>
    @endif
  @endif
@else
  @php
   $dash_id = "";
   $client_id = $folder_id;
  @endphp
@endif
  </div>
    <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
</div>
<!-- Content Wrapper. Contains page content -->
  <!-- <div class="content-wrapper">   -->
  <div id="iframe_div"></div> 
  <!-- </div> -->
  <!-- /.content-wrapper -->
@endsection
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script>

  $( document ).ready(function() {
   $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });
   var msg = '<?php echo $msg; ?>';
   if(msg == "")
   {
    $(".dashboard_link").click(function(e){
        e.preventDefault();
    var dashboard_id = $(this).data('dashboard-id');
    var client_id = $(this).data('client-id');
    var active_id = $(this).data('active-id');
    var Access = window.localStorage.getItem('Access');
        $.ajax({
           type:'POST',
           url:'/home/getDashboard',
           data:{dashboard_id:dashboard_id, password:'password11', client_id:client_id, Access:Access},
           success:function(data){
         $("#iframe_div").html("");
         if(data.LicenceMessage != "")
             {
              var frame ='<div class="MessageDiv"><b>'+data.LicenceMessage+'</></div>';
             }
             else
             {
             var frame = '<iframe src="'+data.url+'" width="'+'1330"'+' frameborder="'+'0"'+'> </iframe>';            
             }
         // var frame = '<iframe src="'+data.url+'" width="'+'1330"'+' frameborder="'+'0"'+'> </iframe>';
         $("#iframe_div").append(frame);
         $('.dropbtn').removeClass('active1');
         $("#active_"+active_id).addClass('active1');
        // swal.fire({
        //  title: 'Dashboard',
        //  width:'100%',
        //  hieght:'100%',
        //  html: frame,
    //       showCloseButton: true,
    //       showConfirmButton:false,
    //       onClose: DeleteUnsavedImages
        // });
              // console.log(data.url);
           }
        });
  });


  var dash_id = '<?php if(isset($dash_id)){echo $dash_id;} ?>';
  if(dash_id != ""){
  var client_id = '<?php if(isset($dash_id)){echo $client_id;} ?>';
  var Access = window.localStorage.getItem('Access');
  // alert(Access);
    $.ajax({
           type:'POST',
           url:'/home/getDashboard',
           data:{dashboard_id:dash_id, password:'password11',client_id:client_id,Access:Access},
           success:function(data){
            // console.log(data);
           $("#iframe_div").html("");
             if(data.LicenceMessage != "")
             {
              var frame ='<div class="MessageDiv"><b>'+data.LicenceMessage+'</></div>';
             }
             else
             {
             var frame = '<iframe src="'+data.url+'" width="'+'1330"'+' frameborder="'+'0"'+'> </iframe>';            
             }

              $("#iframe_div").append(frame);
           }
        });
  }
  else
  {
    $("#iframe_div").append('<h2 align="center">You do not have this dashboard access</h2>');
  }



  $(".small-box1111").click(function(e){
    
        e.preventDefault();
    var look_id = $(this).data('look-id');
    console.log(look_id);
        var frameLook = '<iframe src="'+look_id+'" width="'+'1200"'+' height="'+'700"'+' frameborder="'+'0"'+'> </iframe>';
      swal.fire({
        title: 'Looks',
        width:'100%',
        height:'100%',
        html: frameLook,
        showCloseButton: true
      });

  });
  }    
  else
  {
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: msg
    });
  }
  });

  // function DeleteUnsavedImages(){
  //      alert("closed11");
  //      $.ajax({
  //          type:'POST',
  //          url:'/home/deleteLookerUser',
  //          data:{user:"usr"},
  //          success:function(data){
  //             console.log(data);
  //          }
  //       });
  //  }

  function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>
