$( document ).ready(function() {
var sect_count = 2;
var sub_sect_count = [{'section_id':"1", 'subSectCount':1}];
$("#countsArray").val(JSON.stringify(sub_sect_count));
    $(document).on('click',"button[data-action='add-section']",function(){
      sub_sect_count.push({'section_id':sect_count.toString(), 'subSectCount':1});
      $("#countsArray").val(JSON.stringify(sub_sect_count));
        $("#sectionTemplate").append('<div class="card card-widget card-outline card-primary" id="section_box_'+sect_count+'">\
        <div class="card-header">\
          <div class="user-block">\
          <b>Section '+sect_count+': </b><input size="70" id="section_heading_'+sect_count+'_1" type="text" name="section_heading_'+sect_count+'_1" placeholder="Enter value" />\
          </div>\
          <div class="card-tools">\
          <button type="button" class="btn btn-tool" data-toggle="tooltip" data-action="add-section" title="Add New Section">\
          <i class="far fa-cart-plus"></i>Add Section</button>\
          <button type="button" class="btn btn-tool" data-card-widget="collapse" data-toggle="tooltip" title="Expand/Minimise" ><i class="fas fa-minus"></i>\
          </button>\
          <button type="button" class="btn btn-tool" sectionId_remove="'+sect_count+'" data-card-action="remove" data-toggle="tooltip" title="Close" ><i class="fas fa-times"></i>\
          </button>\
          </div>\
        </div>\
        <div class="card-body">\
          <div class="form-group">\
            <label>Section Text</label>\
            <textarea class="form-control" id="section_text_'+sect_count+'_1" name="section_text_'+sect_count+'_1" rows="3" placeholder="Enter ..."></textarea>\
          </div>\
          <div class="col-md-11 float-right" id="subSectionTemplate_'+sect_count+'">\
            <div class="card card-widget card-outline card-warning collapsed-card" id="sub_section_box_'+sect_count+'_1">\
              <div class="card-header">\
                <div class="user-block">\
                <b>Sub section 1:</b>  <input size="50" id="sub_section_heading_'+sect_count+'_1" type="text" name="sub_section_heading_'+sect_count+'_1" placeholder="Enter value" />\
                </div>\
                <div class="card-tools">\
                  <button type="button" class="btn btn-tool" data-toggle="tooltip" sectionId="'+sect_count+'" data-action="add-sub-section" title="Mark as read">\
                  <i class="far fa-cart-plus"></i>Add Sub-Section</button>\
                  <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>\
                  </button>\
                  <button type="button" disabled class="btn btn-tool" sub_sectionId_sect="'+sect_count+'" sub_sectionId_subsect="1" data-card-subsection="remove"><i class="fas fa-times"></i>\
                  </button>\
                </div>\
              </div>\
              <div class="card-body">\
                <div class="form-group">\
                  <label>Sub-Section Text</label>\
                  <textarea class="form-control" id="sub_section_text_'+sect_count+'_1" name="sub_section_text_'+sect_count+'_1" rows="3" placeholder="Enter ..."></textarea>\
                 </div>\
                <div class="image-upload-wrap" id="image_upload_wrap_'+sect_count+'_1">\
                 <input id="chart_'+sect_count+'_1" type="hidden" name="chart_'+sect_count+'_1"  />\
                 <input id="chart_id_'+sect_count+'_1" type="hidden" name="chart_id_'+sect_count+'_1"  />\
									<input id="chart_name_'+sect_count+'_1" type="hidden" name="chart_name_'+sect_count+'_1"  />\
                 <input class="file-upload-input" data-id="'+sect_count+'_1" type="button" data-toggle="modal"  />\
                  <div class="drag-text">\
                  <h3>Click here to select Look</h3>\
                  </div>\
                </div>\
                <div class="file-upload-content" id="file_upload_content_'+sect_count+'_1">\
                  <img class="file-upload-image" id="file_upload_image_'+sect_count+'_1" src="" alt="your image" />\
                  <div class="image-title-wrap">\
                  <button type="button" section_pos="'+sect_count+'_1" class="remove-image">Remove <span class="image-title">Uploaded Look</span></button>\
                  </div>\
                </div>\
                </br>\
              </div>\
            </div>\
          </div>\
        </div>\
      </div>');
      sect_count++;

      var allEditors = document.querySelectorAll('.form-control');
        for (var i = 0; i < allEditors.length; ++i) {
          // ClassicEditor.create(allEditors[i]); 
           CKEDITOR.replace(allEditors[i],
          {
           customConfig : 'config.js',
           toolbar : [
            { name: 'document', items: [ 'Source', 'Preview'] },
            { name: 'clipboard', items: [ 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-' ] },
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
            { name: 'links', items: [ 'Link', 'Unlink' ] },
            { name: 'insert', items: [ 'Table', 'SpecialChar' ] },
            '/',
            { name: 'styles', items: [  'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
          ]
           })
   		}

    });


    $(document).on('click',"button[data-action='add-sub-section']",function(){
      
      var sectionId = $(this).attr("sectionId");
      var subSectionId = 0;
      console.log(sectionId);
      let obj = sub_sect_count.find(o => o.section_id === sectionId);
      if (!(obj)){
        sub_sect_count.push({'section_id':sectionId, 'subSectCount':2});
        subSectionId = 2;
      }else{
        let obj1 = sub_sect_count.find((o, i) => {
            if (o.section_id === sectionId) {
              
              sub_sect_count[i] = { section_id: sectionId, 'subSectCount':parseInt(o.subSectCount)+1 };
              subSectionId = parseInt(o.subSectCount)+1;
                return true; // stop searching
            }
        });

      }
      console.log(sub_sect_count);
      $("#subSectionTemplate_"+sectionId).append('<div class="card card-widget card-outline card-warning collapsed-card" id="sub_section_box_'+sectionId+'_'+subSectionId+'">\
        <div class="card-header">\
          <div class="user-block">\
            <b>Sub section '+subSectionId+':</b>  <input size="50" id="sub_section_heading_'+sectionId+'_'+subSectionId+'" type="text" name="sub_section_heading_'+sectionId+'_'+subSectionId+'" placeholder="Enter value" />\
          </div>\
          <div class="card-tools">\
            <button type="button" class="btn btn-tool" data-toggle="tooltip" sectionId="'+sectionId+'" data-action="add-sub-section" title="Mark as read">\
            <i class="far fa-cart-plus"></i>Add Sub-Section</button>\
            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-plus"></i>\
            </button>\
            <button type="button" class="btn btn-tool"  sub_sectionId_sect="'+sectionId+'" sub_sectionId_subsect="'+subSectionId+'" data-card-subsection="remove"><i class="fas fa-times"></i>\
            </button>\
          </div>\
        </div>\
        <div class="card-body">\
          <div class="form-group">\
            <label>Sub-Section Text</label>\
            <textarea class="form-control" id="sub_section_text_'+sectionId+'_'+subSectionId+'" name="sub_section_text_'+sectionId+'_'+subSectionId+'" rows="3" placeholder="Enter ..."></textarea>\
          </div>\
          <div class="image-upload-wrap" id="image_upload_wrap_'+sectionId+'_'+subSectionId+'">\
            <input id="chart_'+sectionId+'_'+subSectionId+'" type="hidden" name="chart_'+sectionId+'_'+subSectionId+'"  />\
            <input id="chart_id_'+sectionId+'_'+subSectionId+'" type="hidden" name="chart_id_'+sectionId+'_'+subSectionId+'"  />\
						<input id="chart_name_'+sectionId+'_'+subSectionId+'" type="hidden" name="chart_name_'+sectionId+'_'+subSectionId+'"  />\
            <input class="file-upload-input" data-id="'+sectionId+'_'+subSectionId+'" type="button" data-toggle="modal"  />\
            <div class="drag-text">\
              <h3>Click here to select Look</h3>\
            </div>\
          </div>\
          <div class="file-upload-content" id="file_upload_content_'+sectionId+'_'+subSectionId+'">\
            <img class="file-upload-image" id="file_upload_image_'+sectionId+'_'+subSectionId+'" src="" alt="your image" />\
            <div class="image-title-wrap">\
              <button type="button" section_pos="'+sectionId+'_'+subSectionId+'" class="remove-image">Remove <span class="image-title">Uploaded Look</span></button>\
            </div>\
          </div>\
          </br>\
        </div>\
      </div>');

      $("#countsArray").val(JSON.stringify(sub_sect_count));

      var allEditors = document.querySelectorAll('.form-control');
      for (var i = 0; i < allEditors.length; ++i) {
        // ClassicEditor.create(allEditors[i]); 
          CKEDITOR.replace(allEditors[i],
        {
          customConfig : 'config.js',
          toolbar : [
            { name: 'document', items: [ 'Source', 'Preview'] },
            { name: 'clipboard', items: [ 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace', '-', 'SelectAll', '-' ] },
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl' ] },
            { name: 'links', items: [ 'Link', 'Unlink' ] },
            { name: 'insert', items: [ 'Table', 'SpecialChar' ] },
            '/',
            { name: 'styles', items: [  'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize', 'ShowBlocks' ] }
          ]
          })
    }

    });


    $(document).on('click',"button[data-card-action='remove']",function(){
      var sectionId_rem = $(this).attr("sectionId_remove");
      var r = confirm("do you want to remove section");
      if (r == true) {
        sect_count--;
        $("#section_box_"+sectionId_rem).remove();
        console.log(sect_count);
      }
    });

    $(document).on('click',"button[data-card-subsection='remove']",function(){
      var sub_sectionId_sect_rem = $(this).attr("sub_sectionId_sect");
      var sub_sectionId_subsect_rem = $(this).attr("sub_sectionId_subsect");

      var r = confirm("do you want to remove Sub-Section");
      if (r == true) {
        let obj = sub_sect_count.find(o => o.section_id === sub_sectionId_sect_rem);
        if (obj){
          let obj1 = sub_sect_count.find((o, i) => {
              if (o.section_id === sub_sectionId_sect_rem) {
                
                sub_sect_count[i] = { section_id: sub_sectionId_sect_rem, 'subSectCount':parseInt(o.subSectCount)-1 };
               // subSectionId = parseInt(o.subSectCount)-1;
                  return true; // stop searching
              }
          });
  
        }
        console.log(sub_sect_count);

        $("#sub_section_box_"+sub_sectionId_sect_rem+"_"+sub_sectionId_subsect_rem).remove();
       
      }
    });

});