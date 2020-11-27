$(function() {

    var required = $('input');
    var error = 0
    $(required).on( 'input', function() {
        required.each(function(){
            if($(this).attr('name') != 'cgu' && $(this).val().length < $(this).attr('minlength')){
                if (!$(this).hasClass('errorInput')) {
                    error++;
                    $(this).addClass('errorInput');
                }
            }else{
                if ($(this).hasClass('errorInput')) {
                    error--;
                    $(this).removeClass('errorInput');
                }
            }
        });
        if(error == 0){
            $( "#submitBtn" ).prop( "disabled", false );
        }else{
            $( "#submitBtn" ).prop( "disabled", true );
        }
    });
});