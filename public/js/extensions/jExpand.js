(function($){
    $.fn.jExpand = function(){
        var element = this;

        $(element).find("tr:odd").addClass("odd");
        $(element).find("tr:not(.odd)").hide();
        $(element).find("tr:first-child").show();

        $(element).find("tr.odd").click(function() {
            $(this).next("tr").toggle();
            caret = $(this).find("td.caret-column").find('.fa');
            if(caret.hasClass("fa-caret-down")){
                caret.removeClass("fa-caret-down").addClass("fa-caret-up");
            }else{
                caret.removeClass("fa-caret-up").addClass("fa-caret-down");
            }
        });
    }    
})(jQuery); 