$(document).ready(function(){

    $('.clear-response').click(function(){
     $obj=$(this).closest('form');
      swal({
      title: "Are you sure?",
      text: "Do u want to clear the response!",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-danger",
      confirmButtonText: "Yes, clear it!",
      closeOnConfirm: false
    },
    function(isConfirm){
      if (isConfirm) {
        $obj.submit();
      } 
   });

    });


    $('.reopen-survey').click(function(){
     $obj=$(this).closest('form');
      swal({
      title: "Are you sure?",
      text: "Do u want to reopen the survey!",
      type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-success",
      confirmButtonText: "Yes, reopen it!",
      closeOnConfirm: false
    },
    function(isConfirm){
      if (isConfirm) {
        $obj.submit();
      } 
   });

    });

$('.delete-user-survey').click(function(){
 var res=$(this).prevAll('.check_response_data').val();
 if (res!="true"){ var text="Your will not be able to recover this imaginary file!"; var title="Are you sure?";}
 else 
 {
    var class1="hidden";
    var text="Respondent already respondend and hence you are not allowed to delete the respondent.";
    var title="";
 }
     $obj=$(this).closest('form');
      swal({
      title: title,
      text: text,
     // type: "warning",
      showCancelButton: true,
      confirmButtonClass: "btn-danger "+class1,
      confirmButtonText: "Yes, delete it!",
      closeOnConfirm: false
    },
    function(isConfirm){

      if (isConfirm==true) {
        $obj.submit();
      } 

   });
});


    $('#stable').DataTable({
      "bSort": false
    });

     $('[data-toggle="tooltip"]').tooltip();
 
 });
   
