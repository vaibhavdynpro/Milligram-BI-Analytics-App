function readURL(input) {
 
  if (input.files && input.files[0]) {

    var reader = new FileReader();

    reader.onload = function(e) {
      

      $('.image-upload-wrap').hide();

      $('.file-upload-image').attr('src', e.target.result);
      $('.file-upload-content').show();

      $('.image-title').html(input.files[0].name);
    };

    reader.readAsDataURL(input.files[0]);

  } else {
   // removeUpload();
  }
}
//remove-image
//function removeUpload(popup_id) {
 // $('#file_upload_input_'+popup_id).replaceWith($('#file_upload_input_'+popup_id).clone());
 $(document).on('click',".remove-image",function(){
  var popup_id = $(this).attr("section_pos");
  //alert(popup_id);
  $('#chart_'+popup_id).val("");
  $('#chart_id_'+popup_id).val("");
  $('#chart_name_'+popup_id).val("");
  $('#file_upload_content_'+popup_id).hide();
  $('#image_upload_wrap_'+popup_id).show();
});

$('.image-upload-wrap').bind('dragover', function () {
        $('.image-upload-wrap').addClass('image-dropping');
    });
    $('.image-upload-wrap').bind('dragleave', function () {
        $('.image-upload-wrap').removeClass('image-dropping');
});

