<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="{{ asset('css/processing.css') }}" rel="stylesheet">
<link href="{{ asset('css/toggle_style.css') }}" rel="stylesheet">
<link href="{{ asset('css/multi_select_dd.css') }}" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>

<div class="row">
  <div class="column1" style="background-color:#f9f9f9;">
    <h2>Tables</h2>
    <ul>
      
        @foreach ($Tabels as $k => $val)
        <!-- <li><a href="#" class="info_link">{{ $val->Tables_in_KairosApp_dev }}</a></li> -->
        <li>
          <div class="form-group">
            <input type="checkbox" id="{{ $val->Tables_in_KairosApp_dev }}{{$k}}" name="tabels_names" value="{{ $val->Tables_in_KairosApp_dev }}">
            <label for="{{ $val->Tables_in_KairosApp_dev }}{{$k}}">{{ $val->Tables_in_KairosApp_dev }}</label>
          </div>
        </li>
        @endforeach
          
    </ul>
    <button class="button">Next</button>
  </div>
  <div class="column2" style="background-color:#f1ecec;">
    <h2>Columns</h2>
    <div id="columnDiv"></div>
    <button class="button1">Proceed</button>
  </div>
  <div class="column3" style="background-color:#fff;">
    
    <div class="firsthalf">
      <h2>Component</h2><br/>
      <div id="AggDiv">
        <div class="row AggFirstRow">
          <div class="col-sm-esl">
            <div id="aggBtn"></div>
          </div>            
        </div> 
        <!-- <button class="WhrArrow" id="CaptureGroupBy"><i class="fa fa-arrow-right" aria-hidden="true"></i></button> -->
      </div>
      <div id="JoinDiv">
        <div class="row joinFirstRow">
          <div class="col-sm-esl">
            <div id="JoinBtn"></div>
          </div>            
        </div> 
      </div>
      <div class="firsthalfCond">
          <div class="row WhrFirstRow">
            <div class="col-sm-esl">
              <div id="whereBtn"></div>
            </div>            
          </div>              
      </div>
      <div class="row">
              <!-- <button class="WhrArrow" id="CaptureWhere"><i class="fa fa-arrow-right" aria-hidden="true"></i></button> -->
      </div>
      <div id="GroupByDiv">
        <div class="row GrpFirstRow">
          <div class="col-sm-esl">
            <div id="GroupByBtn"></div>
          </div>            
        </div> 
        <!-- <button class="WhrArrow" id="CaptureGroupBy"><i class="fa fa-arrow-right" aria-hidden="true"></i></button> -->
      </div>
      <div id="HavingDiv">
        <div class="row havingFirstRow">
          <div class="col-sm-esl">
            <div id="HavingBtn"></div>
          </div>            
        </div> 
        <!-- <button class="WhrArrow" id="CaptureGroupBy"><i class="fa fa-arrow-right" aria-hidden="true"></i></button> -->
      </div>
      <div id="OrderByDiv">
        <div class="row OdrFirstRow">
          <div class="col-sm-esl">
            <div id="OrderByBtn"></div>
          </div>            
        </div> 
        <!-- <button class="WhrArrow" id="CaptureOrderBy"><i class="fa fa-arrow-right" aria-hidden="true"></i></button> -->
      </div>
      <div id="LimitDiv">
        <div class="row LimitFirstRow">
          <div class="col-sm-esl">
            <div id="LimitBtn"></div>
          </div>            
        </div> 
      </div>
      
      
    <button class="WhrArrow" id="BuildQuery"><i class="fa fa-arrow-right" aria-hidden="true"></i></button> 
    </div>
    
    <div class="secondhalf">
      <h2>Query</h2>
      <textarea class="queryEditor"></textarea>
    </div>
  </div>
</div>

</body>
</html>
<script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
  $( document ).ready(function() {
   $.ajaxSetup({

        headers: {

            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

        }

    });

   
    
        $(".button").click(function(){
          
            var tbl = [];
            $.each($("input[name='tabels_names']:checked"), function(){
                tbl.push($(this).val());
            });
            var tabel = tbl.join(",");
            $.ajax({
               type:'POST',
               async: true,
               url:'/processing/getColumn',
               data:{tabel:tabel},
               success:function(data){
                // alert(data.columns.entity);
                $('#columnDiv').empty();
                var html = "<ul>";
                for (let j = 0; j < tbl.length; j++) {
                  html += "<div class='form-group'><input type='checkbox' id="+ tbl[j] +"_tbl"+" name='' value="+ tbl[j] +" class='colCls'><label for="+ tbl[j] +"_tbl"+">"+ tbl[j] +".*"+"</label></div>";
                  var alise = 'abcdefghijklmnopqrstuvwxyz'[j];
                  alise += ".";
                  for (let i = 0; i < data.columns[tbl[j]].length; i++) { 

                    html += "<div class='form-group'><input type='checkbox' id="+ tbl[j] +"."+data.columns[tbl[j]][i] +" name='column_name' value="+alise+""+ data.columns[tbl[j]][i] +" class="+ tbl[j] +"_tbl"+"><label for="+ tbl[j] +"."+data.columns[tbl[j]][i] +">"+alise+""+ data.columns[tbl[j]][i] +"</label></div>";
                  }

                  /*===CHECK ALL FUNCTION===*/
                  $(document).on('click', '#'+tbl[j]+'_tbl', function(){
                    $("."+tbl[j]+"_tbl").prop('checked', $(this).prop('checked'));
                  });
                }
                html += "</ul>";
                $("#columnDiv").append(html);
             }
            });
        });


        var WhrCondCount = 0;
        var sql ="";
        var selectColumn ="";
        var whereSql = "";
        var groupSql = "";
        var havingSql = "";
        var orderSql = "";
        var sqljoin ="";
        var whereClick = 0;
        var groupClick = 0;
        var orderClick = 0;
        var limitClick = 0;
        var AggFunClick= 0;
        var havingFunClick= 0;
        var joinClick= 0;
        var joinrows= 0;
        $(".button1").click(function(){
            var tbl = [];
            $.each($("input[name='tabels_names']:checked"), function(){
                tbl.push($(this).val());
            });
            var tabel = tbl.join(",");
            
            var col = [];
            $.each($("input[name='column_name']:checked"), function(){
                col.push($(this).val());
            });
            var columns = col.join(",");

            
            if(tbl.length >= 1)
            {
              var AggBtn = "<div><button class='aggBtn'>AGGREGATE <i class='fa fa-plus'></i></button></div>";
              $("#aggBtn").append(AggBtn);
              var whrBtn = "<div><button class='whereBtn'>WHERE <i class='fa fa-plus'></i></button></div>";
              $("#whereBtn").append(whrBtn);
              var GrpBtn = "<div><button class='groupByBtn'>GROUP BY <i class='fa fa-plus'></i></button></div>";
              $("#GroupByBtn").append(GrpBtn);
              var HavingBtn = "<div><button class='HavingBtn'>HAVING<i class='fa fa-plus'></i></button></div>";
              $("#HavingBtn").append(HavingBtn);
              var OdrBtn = "<div><button class='orderByBtn'>ORDER BY <i class='fa fa-plus'></i></button></div>";
              $("#OrderByBtn").append(OdrBtn);
              var LimitBtn = "<div><button class='LimitBtn'>LIMIT <i class='fa fa-plus'></i></button></div>";
              $("#LimitBtn").append(LimitBtn);
              if (tbl.length > 1)
              {              
              var joinBtn = "<div><button class='joinBtn'>JOIN <i class='fa fa-plus'></i></button></div>";
              $("#JoinBtn").append(joinBtn);
              }
              sql ="SELECT ";
              sql += columns;
              sql += " FROM ";
              if(tbl.length == 1)
              {
                // sql += tabel;
                sql += tabel+" as a";
              }
              $(".queryEditor").append(sql);

            }

        });




        $(document).on('click', '.whereBtn', function(){ 
          var tbl = [];
            $.each($("input[name='tabels_names']:checked"), function(){
                tbl.push($(this).val());
            });
            var tabel = tbl.join(",");
            whereClick++;
            $.ajax({
               type:'POST',
               async: true,
               url:'/processing/getColumn',
               data:{tabel:tabel},
               success:function(data){
                var DD = '<select id="whereCol_'+WhrCondCount+'" class="whereCol" name="whereCol_'+WhrCondCount+'">';
                DD += "<option value=''></option>";
                for (let j = 0; j < tbl.length; j++) {
                  var alise = 'abcdefghijklmnopqrstuvwxyz'[j];
                  alise += ".";
                  for (let i = 0; i < data.columns[tbl[j]].length; i++) { 
                    DD += "<option value="+alise+""+data.columns[tbl[j]][i] +">"+alise+""+ data.columns[tbl[j]][i] +"</option>";                  
                  }
                }
                DD += "</select>";

                var operatorDD = '<select id="operater_dd_'+WhrCondCount+'" class="operater_dd" name="operater_dd_'+WhrCondCount+'">';
                  operatorDD += "<option value=''></option>"; 
                  operatorDD += "<option value='='>=</option>"; 
                  operatorDD += "<option value='!='>!=</option>"; 
                  operatorDD += "<option value='IS NULL'>IS NULL</option>"; 
                  operatorDD += "<option value='IS NOT NULL'>IS NOT NULL</option>"; 
                operatorDD += "</select>";

                var wcond = '<input type="text" name="whrCndVal_'+WhrCondCount+'" class="whrCndVal" id="whrCndVal_'+WhrCondCount+'">';
                if(WhrCondCount == 0){
                  var whereCond = "<div class='col-sm'><div class='whereColDiv'>"+DD+"</div></div><div class='col-sm'><div class='whereColCond'>"+operatorDD+"</div></div><div class='col-sm'><div class='whereColVal'>"+wcond+"</div></div>";
                  $(".WhrFirstRow").append(whereCond);
                }
                else
                {
                  var whrcnt = WhrCondCount-1;
                  $( "#WhereDelete_"+whrcnt).remove();
                  var whereCond = "<div class='row rows_"+WhrCondCount+"'><div class='col-sm-esl'><label class='switch' id='WhrCndAndOrLabel_"+WhrCondCount+"'><input type='checkbox' id='WhrCndAndOr_"+WhrCondCount+"' name='WhrCndAndOr_"+WhrCondCount+"' value='1'><div class='slider round'></div></label></div><div class='col-sm'><div class='whereColDiv'>"+DD+"</div></div><div class='col-sm'><div class='whereColCond'>"+operatorDD+"</div></div><div class='col-sm'><div class='whereColVal'>"+wcond+"</div></div><div class='col-sm-sl' id='col-sm-sl_"+WhrCondCount+"'><i class='fa fa-times WhereDelete' id='WhereDelete_"+WhrCondCount+"' data-id='"+WhrCondCount+"' aria-hidden='true'></i></div></div>";
                  $(".firsthalfCond").append(whereCond);                  
                }
                $('#CaptureWhere').css('display','block');
                $('#BuildQuery').css('display','block');
                WhrCondCount++;
             }
            });
        });


       

        /*====DELETE WHERE CONDITION ROW=====*/
        $(document).on('click', '.WhereDelete', function(){
          var data_id = $(this).attr("data-id");
                  $( "#whereCol_"+data_id).remove();
                  $( "#operater_dd_"+data_id).remove();
                  $( "#whrCndVal_"+data_id).remove();
                  $( "#WhrCndAndOrLabel_"+data_id).remove();
                  $( "#col-sm-sl_"+data_id).remove();
                  WhrCondCount = data_id ;
                  var minuscnt = data_id-1;
                  // alert(WhrCondCount);
                  var DeleteBtn = "<i class='fa fa-times WhereDelete' id='WhereDelete_"+minuscnt+"' data-id='"+minuscnt+"' aria-hidden='true'></i>";
                  $("#col-sm-sl_"+minuscnt).append(DeleteBtn);
                  $( ".rows_"+WhrCondCount).remove();
        });


        /*====GROUP BY BUTTON FUNCTINALITY====*/
        $(document).on('click', '.groupByBtn', function(){ 
          var tbl = [];
            $.each($("input[name='tabels_names']:checked"), function(){
                tbl.push($(this).val());
            });
            var tabel = tbl.join(",");
            groupClick++;
            $.ajax({
               type:'POST',
               async: true,
               url:'/processing/getColumn',
               data:{tabel:tabel},
               success:function(data){
                var GrpDD = '<select id="GroupBycol" class="js-example-basic-multiple" name="GroupBycol[]" multiple="multiple" style="width: 100%">';
                GrpDD += "<option value=''>Select column</option>";
                for (let j = 0; j < tbl.length; j++) {
                  var alise = 'abcdefghijklmnopqrstuvwxyz'[j];
                  alise += ".";
                  for (let i = 0; i < data.columns[tbl[j]].length; i++) { 
                    GrpDD += "<option value="+alise+""+data.columns[tbl[j]][i] +">"+alise+""+ data.columns[tbl[j]][i] +"</option>";                  
                  }
                }
                GrpDD += "</select>";
                var GrpCond = "<div class='col-sm-lg'><div class='GroupColDiv'>"+GrpDD+"</div></div>";
                  $(".GrpFirstRow").append(GrpCond);
                  $('.js-example-basic-multiple').select2();
                $('#CaptureGroupBy').css('display','block');
                $('#BuildQuery').css('display','block');
                
             }
            });
        });


        /*====ORDER BY BUTTON FUNCTINALITY====*/
        $(document).on('click', '.orderByBtn', function(){ 
          var tbl = [];
            $.each($("input[name='tabels_names']:checked"), function(){
                tbl.push($(this).val());
            });
            var tabel = tbl.join(",");
            orderClick++;
            $.ajax({
               type:'POST',
               async: true,
               url:'/processing/getColumn',
               data:{tabel:tabel},
               success:function(data){
                var GrpDD = '<select id="OrderBycol" class="whereCol" name="OrderBycol">';
                GrpDD += "<option value=''></option>";
                for (let j = 0; j < tbl.length; j++) {
                  var alise = 'abcdefghijklmnopqrstuvwxyz'[j];
                  alise += ".";
                  for (let i = 0; i < data.columns[tbl[j]].length; i++) { 
                    GrpDD += "<option value="+alise+""+data.columns[tbl[j]][i] +">"+alise+""+ data.columns[tbl[j]][i] +"</option>";                  
                  }
                }
                GrpDD += "</select>";
                var OrderDD = '<select id="order_dd" class="whereCol" name="order_dd">';
                  OrderDD += "<option value=''></option>"; 
                  OrderDD += "<option value='ASC'>ASC</option>"; 
                  OrderDD += "<option value='DESC'>DESC</option>"; 
                OrderDD += "</select>";
                var GrpCond = "<div class='col-sm'><div class='GroupColDiv'>"+GrpDD+"</div></div><div class='col-sm'><div class='OrderBy'>"+OrderDD+"</div></div>";
                  $(".OdrFirstRow").append(GrpCond);
                $('#CaptureOrderBy').css('display','block');
                $('#BuildQuery').css('display','block');
                
             }
            });
        });

        /*====GROUP BY BUTTON FUNCTINALITY====*/
        $(document).on('click', '.LimitBtn', function(){ 
            limitClick++;
            var limit_val = '<div class="col-sm"><input type="text" name="limit_val" class="whrCndVal" id="limit_val"></div>';
                  $(".LimitFirstRow").append(limit_val);
                  $('#BuildQuery').css('display','block');
        });
        $(document).on('click', '.aggBtn', function(){
          var tbl = [];
            $.each($("input[name='tabels_names']:checked"), function(){
                tbl.push($(this).val());
            });
          var tabel = tbl.join(",");
          var AggFun = '<select id="AggFun" class="whereCol" name="AggFun">';
                  AggFun += "<option value='COUNT'>COUNT</option>"; 
                  AggFun += "<option value='AVG'>AVG</option>"; 
                  AggFun += "<option value='SUM'>SUM</option>"; 
                  AggFun += "<option value='MAX'>MAX</option>"; 
                  AggFun += "<option value='MIN'>MIN</option>"; 
                AggFun += "</select>";
          var AggCondDD = '<select id="AggCondDD" class="whereCol" name="AggCondDD">';
                  AggCondDD += "<option value=''></option>"; 
                  AggCondDD += "<option value='*'>*</option>"; 
                  AggCondDD += "<option value='DISTINCT'>DISTINCT</option>"; 
                AggCondDD += "</select>";
          $.ajax({
               type:'POST',
               async: true,
               url:'/processing/getColumn',
               data:{tabel:tabel},
               success:function(data){
                var AggFuncColDD = '<select id="AggFunCol" class="whereCol" name="AggFunCol">';
                AggFuncColDD += "<option value=''></option>";
                for (let j = 0; j < tbl.length; j++) {
                  var alise = 'abcdefghijklmnopqrstuvwxyz'[j];
                  alise += ".";
                  for (let i = 0; i < data.columns[tbl[j]].length; i++) { 
                    AggFuncColDD += "<option value="+alise+""+data.columns[tbl[j]][i] +">"+alise+""+ data.columns[tbl[j]][i] +"</option>";                  
                  }
                }
                AggFuncColDD += "</select>";
                var AggCondCondDD = "<div class='col-sm'><div class='AggFunDiv'>"+AggFun+"</div></div><div class='col-sm'><div class='AggFunDiv'>"+AggCondDD+"</div></div><div class='col-sm'><div class='AggFunColDiv'>"+AggFuncColDD+"</div></div>";
                $(".AggFirstRow").append(AggCondCondDD);
                
             }
            });
            $('#BuildQuery').css('display','block');
            AggFunClick++;
                
        });

        /*====HAVING CLAUSE===*/
        $(document).on('click', '.HavingBtn', function(){
          var tbl = [];
            $.each($("input[name='tabels_names']:checked"), function(){
                tbl.push($(this).val());
            });
          var tabel = tbl.join(",");
          var HavFun = '<select id="HavFun" class="whereCol" name="HavFun">';
                  HavFun += "<option value='COUNT'>COUNT</option>"; 
                  HavFun += "<option value='AVG'>AVG</option>"; 
                  HavFun += "<option value='SUM'>SUM</option>"; 
                  HavFun += "<option value='MAX'>MAX</option>"; 
                  HavFun += "<option value='MIN'>MIN</option>"; 
                HavFun += "</select>";
          var HavCondDD = '<select id="HavCondDD" class="whereCol" name="HavCondDD">';
                  HavCondDD += "<option value=''></option>"; 
                  HavCondDD += "<option value='='>=</option>"; 
                  HavCondDD += "<option value='<'><</option>"; 
                  HavCondDD += "<option value='>'>></option>"; 
                  HavCondDD += "<option value='<='><=</option>"; 
                  HavCondDD += "<option value='>='>>=</option>"; 
                HavCondDD += "</select>";
          $.ajax({
               type:'POST',
               async: true,
               url:'/processing/getColumn',
               data:{tabel:tabel},
               success:function(data){
                var HavFunColDD = '<select id="HavFunColDD" class="whereCol" name="HavFunColDD">';
                HavFunColDD += "<option value=''></option>";
                for (let j = 0; j < tbl.length; j++) {
                  var alise = 'abcdefghijklmnopqrstuvwxyz'[j];
                  alise += ".";
                  for (let i = 0; i < data.columns[tbl[j]].length; i++) { 
                    HavFunColDD += "<option value="+alise+""+data.columns[tbl[j]][i] +">"+alise+""+ data.columns[tbl[j]][i] +"</option>";                  
                  }
                }
                HavFunColDD += "</select>";
                var HavingCondRow = "<div class='col-md'><div class='AggFunDiv'>"+HavFun+"</div></div><div class='col-md'><div class='AggFunColDiv'>"+HavFunColDD+"</div></div><div class='col-md'><div class='AggFunDiv'>"+HavCondDD+"</div></div><div class='col-md'><input type='text' name='Having_Val' class='whrCndVal' id='Having_Val'></div>";
                $(".havingFirstRow").append(HavingCondRow);
                
             }
            });
            $('#BuildQuery').css('display','block');
            havingFunClick++;
                
        });
        $(document).on('change', '#AggCondDD', function(){
          if(this.value == "*")
          {
                $('.AggFunColDiv').css('display','none');
          }
          else
          {
                $('.AggFunColDiv').css('display','block');
          }
        });

        // =============JOIN BTN CONDITION===========
        $(document).on('click', '.joinBtn', function(){ 
          joinrows++;
          joinClick++;
          var tbl = [];
            $.each($("input[name='tabels_names']:checked"), function(){
                tbl.push($(this).val());
            });
            var tabel = tbl.join(",");
            var tblDD = '<select id="tblleft_'+joinrows+'" class="whereCol" name="tbldd">';
                  tblDD += "<option value=''></option>"; 
                  var index=0;
                  $.each($("input[name='tabels_names']:checked"), function(){
                    var alise = 'abcdefghijklmnopqrstuvwxyz'[index];
                    tblDD += "<option value='"+$(this).val()+" as "+alise+"'>"+$(this).val()+" as "+alise+"</option>";  
                    index++;                  
                  });
                tblDD += "</select>";
            var tblDD1 = '<select id="tblright_'+joinrows+'" class="whereCol" name="tbldd1">';
                  tblDD1 += "<option value=''></option>"; 
                  var index1=0;
                  $.each($("input[name='tabels_names']:checked"), function(){
                    var alise = 'abcdefghijklmnopqrstuvwxyz'[index1];
                    tblDD1 += "<option value='"+$(this).val()+" as "+alise+"'>"+$(this).val()+" as "+alise+"</option>";   
                    index1++;                    
                  });
                tblDD1 += "</select>";
            var JoinDD = '<select id="JoinDD_'+joinrows+'" class="whereCol" name="JoinDD">';
                  JoinDD += "<option value=''></option>"; 
                  JoinDD += "<option value='INNER JOIN'>INNER JOIN</option>"; 
                  JoinDD += "<option value='LEFT JOIN'>LEFT JOIN</option>"; 
                  JoinDD += "<option value='RIGHT JOIN'>RIGHT JOIN</option>"; 
                JoinDD += "</select>";
            if(joinrows == 1)
            {
            var JoinFirstRow = "<div class='col-sm-md'><div class='AggFunDiv'>"+tblDD+"</div></div><div class='col-sm-md'><div class='AggFunDiv'>"+JoinDD+"</div></div><div class='col-sm-md'><div class='AggFunDiv'>"+tblDD1+"</div></div><div class='col-es'><h4>ON</h4></div><div class='col-sm-md tbl_col_div1_"+joinrows+"'></div><div class='col-sm-md tbl1_col_div2_"+joinrows+"'></div>";
                $(".joinFirstRow").append(JoinFirstRow);              
            }
            else
            {
              var JoinFirstRow = "<div class='row jrows_"+joinrows+"'><div class='col-sm-esl'></div><div class='col-sm-md'><div class='AggFunDiv'>"+tblDD+"</div></div><div class='col-sm-md'><div class='AggFunDiv'>"+JoinDD+"</div></div><div class='col-sm-md'><div class='AggFunDiv'>"+tblDD1+"</div></div><div class='col-es'><h4>ON</h4></div><div class='col-sm-md tbl_col_div1_"+joinrows+"'></div><div class='col-sm-md tbl1_col_div2_"+joinrows+"'></div></div>";
                $("#JoinDiv").append(JoinFirstRow);
            }

            /*===========DYNAMIC FUNC FOR GET COL OF SELECTED TABEL============*/
             $(document).on('change', '#tblleft_'+joinrows, function(){ 
              var sel_td = $(this).val();
              var selectted_tbl=sel_td.split(" ");
               $.ajax({
                   type:'POST',
                   async: true,
                   url:'/processing/getsingletblColumn',
                   data:{tabel:selectted_tbl[0]},
                   success:function(data){
                    var tbl1_col = '<select id="tblleftval_'+joinrows+'" class="whereCol" name="tbl1_col">';
                    tbl1_col += "<option value=''></option>";
                      for (let i = 0; i < data.columns.length; i++) { 
                        tbl1_col += "<option value="+selectted_tbl[2]+"."+data.columns[i]['COLUMN_NAME'] +">"+selectted_tbl[2]+"."+ data.columns[i]['COLUMN_NAME'] +"</option>";                  
                      }
                    tbl1_col += "</select>";
                    $( ".tbl_col_div_"+joinrows ).remove();
                    if(joinrows == 1)
                    {
                      var tbl1_colDD = "<div>"+tbl1_col+"</div>";
                      $(".tbl_col_div1_"+joinrows).empty();
                      $(".tbl_col_div1_"+joinrows).append(tbl1_colDD);
                    }
                    else
                    {
                      var tbl1_colDD = "<div>"+tbl1_col+"</div>";
                      $(".tbl_col_div1_"+joinrows).empty();
                      $(".tbl_col_div1_"+joinrows).append(tbl1_colDD);
                    }
                    
                    
                 }
                });

            });
            $(document).on('change', '#tblright_'+joinrows, function(){ 
              var sel_td = $(this).val();
              var selectted_tbl=sel_td.split(" ");
               $.ajax({
                   type:'POST',
                   async: true,
                   url:'/processing/getsingletblColumn',
                   data:{tabel:selectted_tbl[0]},
                   success:function(data){
                    var tbl1_col = '<select id="tblrightval_'+joinrows+'" class="whereCol" name="tbl1_col">';
                    tbl1_col += "<option value=''></option>";
                      for (let i = 0; i < data.columns.length; i++) { 
                        tbl1_col += "<option value="+selectted_tbl[2]+"."+data.columns[i]['COLUMN_NAME'] +">"+selectted_tbl[2]+"."+ data.columns[i]['COLUMN_NAME'] +"</option>";                  
                      }
                    tbl1_col += "</select>";
                    $( ".tbl_col_div_"+joinrows ).remove();
                    $( ".eqlto_"+joinrows ).remove();
                    // var tbl1_colDD = "<div class='col-es eqlto_"+joinrows+"'></div>";
                    if(joinrows == 1)
                    {
                      var tbl1_colDD = "<div>"+tbl1_col+"</div>";
                      $(".tbl1_col_div2_"+joinrows).empty();
                      $(".tbl1_col_div2_"+joinrows).append(tbl1_colDD);
                    }
                    else
                    {
                      var tbl1_colDD = "<div>"+tbl1_col+"</div>";
                      $(".tbl1_col_div2_"+joinrows).empty();
                      $(".tbl1_col_div2_"+joinrows).append(tbl1_colDD);
                    }
                    
                    
                 }
                });
            });
            $('#BuildQuery').css('display','block');
        });
       
        $(document).on('click', '#BuildQuery', function(){
          var Where_Condition = "";
          var tbl = [];
          $.each($("input[name='tabels_names']:checked"), function(){
              tbl.push($(this).val());
          });
          var tabel = tbl.join(",");
          
          var col = [];
          $.each($("input[name='column_name']:checked"), function(){
              col.push($(this).val());
          });
          var columns = col.join(",");
          /*====CAPTURE AGG CONDITION====*/
          if(AggFunClick == 1)
          {
            sql = "SELECT ";
            var aggfun = $('#AggFun').val();
            var aggcondDD = $('#AggCondDD').val();
            var Aggfuncol = $('#AggFunCol').val();
            if(aggcondDD == "*")
            {
            sql += aggfun+"("+aggcondDD+")";
            }
            else
            {
            sql += aggfun+"("+aggcondDD+" "+Aggfuncol+")";
            }
            sql += " FROM ";
            (tbl.length == 1)? sql += tabel+" as a" :'';
            // sql += tabel+" ";

          }
          /*====CAPTURE JOIN CONDITION====*/
          if(joinClick >= 1)
          {
              joinQuery ="";  
              for(let i=0;i<joinrows;i++)
              {
                var no = i+1;
                var ltbl = $('#tblleft_'+no).val();
                var jdd = $('#JoinDD_'+no).val();
                var rtbl = $('#tblright_'+no).val();
                var lval = $('#tblleftval_'+no).val();
                var rval = $('#tblrightval_'+no).val();
                if(no == 1)
                {
                  joinQuery += ltbl+ " " + jdd + " " + rtbl + " ON " + lval + "=" + rval + " ";                  
                }
                else
                {
                  joinQuery += jdd + " " + rtbl + " ON " + lval + "=" + rval + " ";                                    
                }
              }
              sqljoin = joinQuery;
              FinalJoinQuery = sql +" "+ joinQuery;
              $(".queryEditor").empty();
              $(".queryEditor").append(FinalJoinQuery);   
          }
          /*====CAPTURE WHERE CONDITION====*/
          var whereValidation = 0;
          if(whereClick != 0)
          {
            for (let i = 0; i < WhrCondCount; i++) { 
              var WhrCol = $('#whereCol_'+i).val();
              var WhrCon = $('#operater_dd_'+i).val();
              var WhrVal = $('#whrCndVal_'+i).val();  

              if (WhrCol != "" && WhrCon != "" && WhrVal != "") 
              {
                if(i != 0){
                var isChecked = $('#WhrCndAndOr_'+i).is(':checked');
                var WhrAndOr = "";
                if(isChecked == true)
                  {   WhrAndOr ='OR'; }  else { WhrAndOr ='AND'; }

                Where_Condition += WhrAndOr +" "+ WhrCol +" "+ WhrCon + " " + WhrVal +" ";
                }
                else
                {
                  Where_Condition += 'WHERE ' + WhrCol +" "+ WhrCon + " " + WhrVal +" ";
                }       
              }
              else
              {
                whereValidation++;
              }      
            } 
            if(whereValidation == 0)
              {
                var sql1 = sql;
                sql1 += " "+sqljoin;
                whereSql = Where_Condition; 
                sql1 += ' '+ Where_Condition; 
                $(".queryEditor").empty();
                $(".queryEditor").append(sql1);   
              }
              else
              {
                alert('Please define correct "WHERE" condition');
              }   
          }
              


          /*CAPTURE GROUP BY CONDITION*/
          if(groupClick != 0)
          {
            var GrpByCol = $('#GroupBycol').select2("val");
            if(GrpByCol.length === 0)
            {
              alert('Please define correct "GROUP BY" condition');
            }
            else
            {
              groupSql = "GROUP BY "+GrpByCol;              
              var sql2 = sql;
              sql2 += ' '+ sqljoin; 
              sql2 += ' '+ whereSql; 
              sql2 += groupSql; 

              // var sql1 = sql; 
              $(".queryEditor").empty();
              $(".queryEditor").append(sql2); 
            }
          }
          /*CAPTURE HAVING CONDITION*/
          if(havingFunClick == 1)
          {          
            var HavFun = $('#HavFun').val();
            var HavFunColDD = $('#HavFunColDD').val();
            var HavCondDD = $('#HavCondDD').val();
            var HavCondVal = $('#Having_Val').val();
            var havingClause = " HAVING "+HavFun+"("+HavFunColDD+") "+HavCondDD+" "+HavCondVal;
            havingSql = havingClause;
            var havSql = sql;
            havSql += sqljoin;
            havSql += whereSql;
            havSql += groupSql;
            havSql += havingClause;
            $(".queryEditor").empty();
            $(".queryEditor").append(havSql); 
          }
            /*CAPTURE ORDER BY CONDITION*/
            if(orderClick != 0)
            {
              var OdrByCol1 = $('#OrderBycol').val();
              var OdrByCol2 = $('#order_dd').val();
              if(OdrByCol1 == "" || OdrByCol2 == "")
              {
                alert('Please define correct "ORDER BY" condition');
              }
              else
              {
                orderSql = " ORDER BY "+OdrByCol1+" "+OdrByCol2;
                
                var sql3 = sql;
                sql3 += ' '+ sqljoin; 
                sql3 += ' '+ whereSql; 
                sql3 += groupSql; 
                sql3 += havingSql; 
                sql3 += orderSql; 

                $(".queryEditor").empty();
                $(".queryEditor").append(sql3); 
              }
            }

            /*====CAPTURE LIMIT CONDITION====*/
            if(limitClick != 0)
            {
              var lmtVal = $('#limit_val').val(); 
              if(lmtVal == "")
              {
                alert('Please define correct "LIMIT" condition');
              }
              else
              {
                var limit_Cond = " LIMIT " + lmtVal;
                  var sql4 = sql;
                  sql4 += ' '+ sqljoin; 
                  sql4 += ' '+ whereSql; 
                  sql4 += groupSql; 
                  sql4 += havingSql; 
                  sql4 += orderSql; 
                  sql4 += limit_Cond; 

                  $(".queryEditor").empty();
                  $(".queryEditor").append(sql4); 
              }              
            }
        });
        
        

  });

</script>
