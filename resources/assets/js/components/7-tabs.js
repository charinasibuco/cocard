$(function(){
$('.nav-tabs a').click(function(){
    $(this).tab('show');
});
$('#next').click(function(){
    $('.nav-tabs a[href="#template"]').tab('show');
    $('.nav-tabs a[href="#template"]').parent().addClass('active').siblings().removeClass('active');
});
$('#prev').click(function(){
    $('.nav-tabs a[href="#main"]').tab('show');
})

// Select tab by name
$('.nav-tabs a[href="#main"]').tab('show');
// $('.nav-tabs a:first').tab('show');
});
