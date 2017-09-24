$(function(){

    /////C A R T
    $( ".delete_cart_item" ).click( function(){
        var cart_id = $(this).attr('data-id');
        $('.cart_delete[data-id="' + cart_id + '"]').css('display','block');
        $('.payment_cart').removeAttr("disabled");
    });
    $( ".btn-cancel-cart" ).click( function(){
        var cart_id = $(this).attr('data-id');
        $('.cart_delete[data-id="' + cart_id + '"]').css('display','none');
        $('.payment_cart').removeAttr("disabled");
    });
    $( ".modify_cart_item" ).click( function(){
        var cart_id = $(this).attr('data-id');
        $('.cart-edit[data-id="' + cart_id + '"]').css('display','table-row');
        $('.cart-display[data-id="' + cart_id + '"]').css('display','none');
        if ($('.cart-edit[data-id="' + cart_id + '"]').is(':visible')) {
            $('.payment_cart').attr("disabled", "disabled");
        }
    });
    $( ".btn-cancel-modify" ).click( function(){
        var cart_id = $(this).attr('data-id');
        $('.cart-edit[data-id="' + cart_id + '"]').css('display','none');
        $('.cart-display[data-id="' + cart_id + '"]').css('display','table-row');
        $('.cart-buttons[data-id="' + cart_id + '"]').css('display','block');
        $('.payment_cart').removeAttr("disabled");
    });
    $( ".cart_back" ).click( function(){
        var cart_id = $(this).attr('data-id');
        $('.cart-edit[data-id="' + cart_id + '"]').css('display','none');
        $('.cart-display[data-id="' + cart_id + '"]').css('display','table-row');
        $('.cart-buttons[data-id="' + cart_id + '"]').css('display','block');
        $('.payment_cart').removeAttr("disabled");
    });

    $(".delete_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-modal-container[data-id="' + id + '"]').css('display','block');
    });
    $(".hide_modal").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-modal-container[data-id="' + id + '"]').css('display','none');
    });
    $("#delete_modal").click( function (){
        $('.delete-modal-container').css('display','block');
    });
    $("#delete_modal_mobile").click( function (){
        $('.delete-modal-container').css('display','block');
    });
    $(".hide_modal").click( function (){
        $('.delete-modal-container').css('display','none');
    });
    $(".activate").click( function (){
        var id = $(this).attr('data-id');
        $('.activate-confirmation[data-id="' + id + '"]').css('display','block');
    });
    $(".hide_activate").click( function (){
        var id = $(this).attr('data-id');
        $('.activate-confirmation[data-id="' + id + '"]').css('display','none');
    });
    $(".deactivate").click( function (){
        var id = $(this).attr('data-id');
        $('.deactivate-confirmation[data-id="' + id + '"]').css('display','block');
    });
    $(".hide_deactivate").click( function (){
        var id = $(this).attr('data-id');
        $('.deactivate-confirmation[data-id="' + id + '"]').css('display','none');
    });

    $(".delete_page").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-modal-container[data-id="' + id + '"]').css('display','block');
    });
    $(".cancel_page").click( function (){
        var id = $(this).attr('data-id');
        $('.delete-modal-container[data-id="' + id + '"]').css('display','none');
    });
});
