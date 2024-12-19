/* globals Tree */
'use strict';


//alert($(".file-upload-image").attr('src'));
//if($(".file-upload-image").attr('src') !=''){

  // $('.file-upload-content').show();
  // $('.image-upload-wrap').hide();
//}

    $(".file-upload-image").each(function(){
      var data_id = $(this).attr("data-id");
      // alert('#file_upload_content_'+data_id); 
     
        if($(this).attr('src') == ''){    
            $('#image_upload_wrap_'+data_id).show();
        }
        else
        {
          $('#image_upload_wrap_'+data_id).hide();
          $('#file_upload_content_'+data_id).show();
        }
        
    });


$( document ).ready(function() {
  ///$('#client_id').trigger('change');

  



$( "#client_id" ).change(function() {
  var folder_id = this.value;
  var result;
  const myNode = document.getElementById("tree");
  myNode.innerHTML = '';


  var tree = new Tree(document.getElementById('tree'), {
    navigate: true // allow navigate with ArrowUp and ArrowDown
  });
  tree.on('open', e => console.log('open', e));
  tree.on('select', e => console.log('select', e));
  tree.on('action', e => console.log('action', e));
  tree.on('fetch', e => console.log('fetch', e));
  tree.on('browse', e => console.log('browse', e));

  $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  
  });
  $.ajax({
          url: '../getFolder',
          type: 'post',
          data: { "folder_id": folder_id},
          async: false,
          success: function(response) {  
            console.log(response); 
            result=response;
      }
 });

      tree.on('fetch', folder => window.setTimeout(() => {

        tree.file({
          name: 'Dashboard 2/111'
        }, folder);
        tree.file({
          name: 'Dashboard 2/2'
        }, folder);
      
        folder.resolve();
      }, 1000));
      
      var structure= JSON.parse(result);
      console.log(structure);
      Object.assign(structure[0], {type: Tree.FOLDER});
      Object.assign(structure[1], {type: Tree.FOLDER});
      console.log("structure999");
      console.log(structure);


      tree.on('created', (e, node) => {
        e.node = node;
      });
      tree.json(structure);
});

$('#client_id').trigger('change');

});






/*  var structure = [ {
  name: 'folder ddd1',
  open: false,
  type: Tree.FOLDER,
  selected: true,
  children: ''
}, {
  name: 'folder 2 (asynced)',
  type: Tree.FOLDER,
  asynced: true
}]; */
 // var structure = [{"name":"folder 1","open":false,"type":"Tree.FOLDER","selected":true,"children":""},{"name":"folder 2 (asynced)","type":"Tree.FOLDER","asynced":true}];

 




/* var structure = [{
  name: 'Dashboard 1',
  id: '9850'
}, {
  name: 'Dashboard 2'
}, {
  name: 'folder 1',
  open: false,
  type: Tree.FOLDER,
  selected: true,
  children: [{
    name: 'Dashboard 1/1'
  }, {
    name: 'Dashboard 1/2'
  }, {
    name: 'folder 1/1',
    type: Tree.FOLDER,
    children: [{
      name: 'folder 1/1/1',
      type: Tree.FOLDER,
      children: [{
        name: 'folder 1/1/1/1',
        type: Tree.FOLDER,
        children: [{
          name: 'Dashboard 1/1/1/1/1'
        }, {
          name: 'Dashboard 1/1/1/1/2'
        }]
      }]
    }]
  }]
}, {
  name: 'folder 2 (asynced)',
  type: Tree.FOLDER,
  asynced: true
}]; */


// keep track of the original node objects


/* document.getElementById('browse-1').addEventListener('click', () => {
  tree.browse(a => {
    if (a.node.name === 'folder 2 (asynced)' || a.node.name === 'file 2/2') {
      return true;
    }
    return false;
  });
});

document.getElementById('browse-2').addEventListener('click', () => {
  tree.browse(a => {
    if (a.node.name.startsWith('folder 1') || a.node.name === 'file 1/1/1/1/2') {
      return true;
    }
    return false;
  });
});

document.getElementById('unload').addEventListener('click', () => {
  const d = tree.hierarchy().pop();
  tree.unloadFolder(d);
});

document.getElementById('previous').addEventListener('click', () => {
  tree.navigate('backward');
});
document.getElementById('next').addEventListener('click', () => {
  tree.navigate('forward');
  
}); */
$(document).on('click',"a[data-type='file']",function(){
//$("a[data-type='file']").click(function(){2_2
  var id = $(this).attr('id');
  var look_id = $(this).attr('data-lookid');
  var look_name = $(this).attr('data-lookname');
  console.log(look_id);
  console.log(look_name);
  var popup_id=$( "#tree" ).next().val();
  console.log(popup_id);
  $("#chart_"+popup_id).val(id);
  $("#chart_id_"+popup_id).val(look_id);
  $("#chart_name_"+popup_id).val(look_name);
  $("#myModal").modal('hide');
  $('#image_upload_wrap_'+popup_id).hide();
  $('#file_upload_image_'+popup_id).attr('src', id);
  $('#file_upload_content_'+popup_id).show();
});

