$(function(){

    $('.donation_input').keyup(function(){
        if($(this).val().length !=0){
            $('.add_to_cart').attr('disabled', false);
        } else{
            $('.add_to_cart').attr('disabled',true);
        }
    });
    $('.donation_input').keypress(function(e){
        if (this.value.length == 0 && e.which == 48 ){
            return false;
        }
    });

});
