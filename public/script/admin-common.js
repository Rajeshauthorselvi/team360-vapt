
$(document).ready(function() {

   $('#copy-survey').on('click',function(e){ 

    e.preventDefault();

    if($('input[type=radio][name=survey]').is(':checked'))
   { 
        var survey_id=$('input[type=radio][name=survey]:checked').val();
        window.location=$(this).attr('href')+"/"+survey_id;
   }
   else
   {
     swal("Please Select Survey");
   }

   });
// Adding Logo Image while creating new survey
$('#logo-image-survey').on('change',function(){
    var ext = $(this).val().split('.').pop().toLowerCase();
    if($.inArray(ext, ['png','jpg','jpeg']) == -1) {
        alert('invalid extension!'+'\n'+'Only jpg and png are allowed!');
        $(this).filestyle('clear');
    }
});


//Adding value to checkbox in creating new survey
$('#fchk').on('click',function(){

   if ($(this).is(':checked')) { 
    $(this).val(1);
   }
   else
   {
     $(this).val(0);
   }


});

//Image change and cancel during update operation
$('.img-change').on('click',function(){

    $(this).parent().parent().next('.form-group').css('display','inherit');
    $(this).siblings('.img-cancel').css('display','inline-block');
});

$('.img-cancel').on('click',function(){

    $(this).parent().parent().next('.form-group').css('display','none');
    $(this).css('display','none');
});

 
});


$(function () {
    $('.list-group.checked-list-box .list-group-item').each(function () {
        
        // Settings
        var checkedItems = {};
        var emailgroup= ($(this).data('emailgroup') ? $(this).data('emailgroup') : "");
        var inputname="";
        if(emailgroup=="notification") inputname="bcc[]";
        if(emailgroup=="reminder") inputname="bcc_for_reminder[]";

        var $widget = $(this),
            $checkbox = $('<input name="'+inputname+'" type="checkbox" class="hidden" />'),
            color = ($widget.data('color') ? $widget.data('color') : "primary"),
            style = ($widget.data('style') == "button" ? "btn-" : "list-group-item-"),
            values =($widget.data('rel') ? $widget.data('rel') : ""),
            settings = {
                on: {
                    icon: 'glyphicon glyphicon-check'
                },
                off: {
                    icon: 'glyphicon glyphicon-unchecked'
                }
            };

           
        $checkbox.val(values); 
        $widget.css('cursor', 'pointer')
        $widget.append($checkbox);

        // Event Handlers
        $widget.on('click', function () {
            $checkbox.prop('checked', !$checkbox.is(':checked'));
            $checkbox.triggerHandler('change');
            updateDisplay();
        });
        $checkbox.on('change', function () {
            updateDisplay();
        });
          

        // Actions
        function updateDisplay() {
            var isChecked = $checkbox.is(':checked');


            // Set the button's state
            $widget.data('state', (isChecked) ? "on" : "off");

            // Set the button's icon
            $widget.find('.state-icon')
                .removeClass()
                .addClass('state-icon ' + settings[$widget.data('state')].icon);

            // Update the button's color
            if (isChecked) {
                $widget.addClass(style + color + ' active');
                
               
            } else {
                $widget.removeClass(style + color + ' active');
            }
        }

        // Initialization
        function init() {
            
            if ($widget.data('checked') == true) {
                $checkbox.prop('checked', !$checkbox.is(':checked'));
            }
            
            updateDisplay();

            // Inject the icon if applicable
            if ($widget.find('.state-icon').length == 0) {
                $widget.prepend('<span class="state-icon ' + settings[$widget.data('state')].icon + '"></span>');
            }
        }
        init();
    });
    
    
   
});
