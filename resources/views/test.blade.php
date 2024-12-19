
<!DOCTYPE html>
<html>
<head>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.debug.js" ></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.0.0-rc.5/dist/html2canvas.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/1.3.8/FileSaver.js"></script>
</head>
  <body>
    <div id="test">
       <p><font size="3" color="red">This is p one</font></p>
       <p><font size="10" color="green">More Text to be printed on PDF</font></p>
      <img src="{{$url}}" id="img">
       
          
  
    </div>
    
<!-- <a href="javascript:generatePDF()">Dowload PDF</a> -->
<!-- <button id="btnSave">getContent</button> -->
  </body>
  <script type="text/javascript">
//     $(function() { 
//     $("#btnSave").on('click', function(){
//         html2canvas($("#test"), {
//             onrendered: function(canvas) {
//                 theCanvas = canvas;


//                 canvas.toBlob(function(blob) {
//                     saveAs(blob, "Dashboard.png"); 
//                 });
//             }
//         });
//     })
// });





//       function generatePDF() {
//  var doc = new jsPDF();

//   doc.fromHTML(document.getElementById("test"), // page element which you want to print as PDF
//   15,
//   15, 
//   {
//     'width': 170
//   },
//   function(a) 
//    {
//     doc.save("HTML2PDF.pdf");
//   });
// }
  </script>
</html>
