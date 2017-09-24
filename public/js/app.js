$(function(){

    var document = $(document);
    var parent =  $('#parent_name :selected').text();
    var html = $('.form-div').html();
    var one_time_count = 0;
    parent = parent.replace(/\s+/g, '-').toLowerCase();
    width = window.innerWidth;
    height = window.innerHeight;

    setTimeout(function() {
        $("#watchButton").trigger('click');
    },1);

    $("#cart-edit").css('display','none');
    $(window).load(function(){
        var phones = [{ "mask": "(###) ###-####"}];
        $('.tel').inputmask({
            mask: phones,
            greedy: false,
            definitions: { '#': { validator: "[0-9]", cardinality: 1}}
        });
    });

    //////donation
    $('.add-donation').on('click', function(){
        $.post($(this).data("add_item_url"),{_token:$(this).data("csrf_token"),count: one_time_count}).done(function(data){
            $(data).insertBefore( $( ".add-donation" ) );
            one_time_count++;
        });
    });
    $(".donation_input").attr({
        "min" : 1
    });
    $('.add_to_cart').attr('disabled',true);

    $('#recurring_date').click(function(){
        $('#date_from').attr('disabled',false);
        $('#date_to').attr('disabled',false);
        $('.sdateFixedNoPayment').attr('disabled',true);
        $('#repetition').attr('disabled',true);
    });
    $('#recurring_times').click(function(){
        $('#date_from').attr('disabled',true);
        $('#date_to').attr('disabled',true);
        $('#repetition').attr('disabled',false);
        $('.sdateFixedNoPayment').attr('disabled',false);

    });
    //mobile side-nav
    if(width <= 768) {
        $(".side-nav").addClass("active");
        $(".navbar-blue .navbar-brand-container .navbar-brand img").css('margin-left','0');
        $(".accordion i.fa-caret-down").css('display','none');
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
        });

    }

    ///////////side nav
    $("#menu-toggle").click(toggleMenu);

    function hideMenu(e) {
        $(".side-nav").removeClass("active");
        $(".side-nav-overlay").removeClass("active");
    }
    function toggleMenu(e) {
        e.preventDefault();
        $(".side-nav").toggleClass("active");
        $(".side-nav-overlay").toggleClass("active");

        $("div.sub_menu").css('display','none');

        if($('.side-nav').hasClass('active')) {
            if(width > 768) {
                $(".side-nav ul li").css('text-align','center');
                $(".side-nav ul li a").css('margin-left','0');
            }

            $(".d-content").addClass('active');
        }else {
            if(width > 768) {
                $(".side-nav ul li").css('text-align','left');
                $(".side-nav ul li a").css('margin-left','30px');
            }
            $(".d-content").removeClass('active');
        }
    }
    $( ".accordion" ).click( function(){
        $("ul#ul_acc").toggle("fast");
    });

    $( ".toggle_event" ).click( function(){
        $("ul#event_options").toggle("fast");
    });

    $( ".toggle_donation" ).click( function(){
        $("ul#donation_options").toggle("fast");
    });

    $( ".toggle_volunteer" ).click( function(){
        $("ul#volunteer_options").toggle("fast");
    });

    $( "a.desktop_active" ).click( function(){
        $("div.sub_menu").toggle("fast");
        if ($('.sub_menu').is(':visible')) {
            $(".d-content").removeClass('active');
        } else {
            $(".d-content").addClass('active');
        }
    });

    $("a.close_btn").click( function (){
        $("div.sub_menu").fadeOut( "fast");
    });



    ////////// P a g e
    $('#next').attr('disabled',true);
    $('#titletext').keyup(function(){
        if($(this).val().length !=0){
            $('#next').attr('disabled',false);

        }
        else{
            $('#next').attr('disabled',true);
        }
    });
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

    $('#parent_name').on('change', function() {
        var parent =  $('#parent_name :selected').text();
        parent = parent.replace(/\s+/g, '-').toLowerCase();
        $('#display_slug').text(parent +'/');
        $('#slug_field').val('');
        $('#slug_id').val(parent +'/');
    });

    if(!$('input#slug_field').val()){
        $('form').submit(function(){
            var slug = $('#slug_field').val();
            slug = slug.replace(/\s+/g, '-').toLowerCase();
            $('#slug_field').val($('#slug_id').val() + slug);
            return true;
        });
    }
    else{
        $('form').submit(function(){
            $('#slug_field').val($('#slug_field').val());
            return true;
        });
    }

    $("#passwordfield").on("keyup",function(){
        if($(this).val())
        $("#fa-eye-p").show();
        else
        $("#fa-eye-p").hide();
    });
    $("#fa-eye-p").mousedown(function(){
        $("#passwordfield").attr('type','text');
    }).mouseup(function(){
        $("#passwordfield").attr('type','password');
    }).mouseout(function(){
        $("#passwordfield").attr('type','password');
    });
    $("#password").on("keyup",function(){
        if($(this).val())
        $("#fa-eye-p").show();
        else
        $("#fa-eye-p").hide();
    });
    $("#fa-eye-p").mousedown(function(){
        $("#password").attr('type','text');
    }).mouseup(function(){
        $("#password").attr('type','password');
    }).mouseout(function(){
        $("#password").attr('type','password');
    });
    $("#passwordconffield").on("keyup",function(){
        if($(this).val())
        $("#fa-eye-cp").show();
        else
        $("#fa-eye-cp").hide();
    });

    $("#fa-eye-cp").mousedown(function(){
        $("#passwordconffield").attr('type','text');
    }).mouseup(function(){
        $("#passwordconffield").attr('type','password');
    }).mouseout(function(){
        $("#passwordconffield").attr('type','password');
    });

    $('.recurring_donation').on('change', function() {
        var selecteddesc = $(this).find('option:selected').data('desc');
        $(".recurring_desc").text(selecteddesc);
    });

    tinymce.init({
        selector: '#mytextarea',
        content_css: '{{ URL::to('/') }}/css/app.css',
        relative_urls: false,
        fontsize_formats: '8pt 9pt 10pt 11pt 12pt 26pt 36pt',
        theme: 'modern',
        height: 200,

        plugins: [
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "emoticons paste textcolor responsivefilemanager"
        ],
        toolbar: "image media responsivefilemanager styleselect bold italic backcolor bullist outdent indent alignleft link emoticons undo redo pastetext | styleselect | fontselect | fontsizeselect",
        image_advtab: true,
        filemanager_title: "Responsive Filemanager",
        external_filemanager_path: "/filemanager/",
        filemanager_access_key:"{{ session('ACCESS_KEY') }}",
        external_plugins: {"filemanager": "/filemanager/plugin.min.js"},
        codemirror: {
            indentOnInit: true, // Whether or not to indent code on init.
            path: 'CodeMirror'
        }
    });

    var change_status_url = "{{ route('change_volunteer_status')  }}";
    $(".volunteer-status").click(function(){
        var switch_ = $(this);
        // dd('aaa');
        var value = switch_.closest("td").find(".status-container").html();
        $.post(change_status_url,{id:switch_.data("volunteer_id"),status:value,_token:"rj29r8498rit"}).done(function(data){
            switch_.closest("td").find(".status-container").html(data);
        });
    });

    $("input[type=number]").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('#large-modal').on('hidden.bs.modal', function() {
        return false;
    });

    $(".delete-link").click(function(){
        $delete_link = $(this);
        bootbox.confirm({
            size: "small",
            message: "Are you sure you want to delete this?",
            callback: function(result){
                if(result){
                    window.location.href = $delete_link.prop("href");
                }
            }
        });
        return false;
    });

    $('#cb_recurring').click(function(){
        if($(this).prop("checked") == true){
            $('#recurring_details').show();
            $('#repetition').attr('disabled',true);
            $('#date_start').attr('disabled',true);
            $('#recurring_date').prop('checked',true);
            $('#donation_type').val('Recurring');
        }else{
            $('#recurring_details').hide();
            $('#donation_type').val('One-Time');
            $('#recurring_date').prop('checked',true);
        }
    });
    // credit card validation 
    $("#ccform").validate({
        // debug: true,
        rules: {
            "billing-cc-number": { 
                required: true,
                creditcard: true
            },
            "billing-cc-exp": {
                required: true,
                minlength: 4,
                maxlength: 4
            },
            cvv: {
                required: true,
                minlength: 3,
                maxlength: 4
            }
        },
        messages: {
            "billing-cc-number": {
                required: "Please enter credit card number.",
                creditcard: "Please enter a valid credit card number."
            },
            "billing-cc-exp": "Please enter expiration date in MMYY format.",
            cvv: "CVV must be 3 or 4 digits."
        }
    });

    $('#search_text').change(function() {
        $('#id').val(0);
    });

    if(width <= 768) {
        $('.side-nav.default').removeClass("active");
        $('.sub-nav li').css('display','left');
        $('.fc-scroller.fc-day-grid-container').css('height','auto');
    }

    $(".side-nav-overlay").click(toggleMenu);

    if($('#donationAmnt').val() == ''){
        $('.add_to_cart').attr('disabled', true);
    }else{
        $('.add_to_cart').attr('disabled', false);
    }

    $('.js_recurring_type').click(function(){
        $('#date_from').prop('required',true);
        $('#date_to').prop('required',false);
        $('.donation_input').prop('required',true);
        $('.add_to_cart').attr('disabled', false);
        if($('#donationAmnt').val() == ''){
            $('.add_to_cart').attr('disabled', true);
        }else{
            $('.add_to_cart').attr('disabled', false);
        } 
    });
    $('.js_recurring_times').click(function(){
        $('.sdateFixedNoPayment').prop('required',true);
        $('#repetition').prop('required',true);
        $('.add_to_cart').attr('disabled', false);
        if($('#donationAmnt').val() == ''){
            $('.add_to_cart').attr('disabled', true);
        }else{
            $('.add_to_cart').attr('disabled', false);
        }
    });
    $('.js-recurring').click(function(){
        if(!$(this).prop('checked')){
            $('.js_recurring_type').prop('checked', false);
            $('.js_recurring_times').prop('checked', false);
            $('.sdateFixedNoPayment').prop('required',true);
            $('#repetition').prop('required',false);
            $('#date_from').prop('required',false);
            $('#date_to').prop('required',false);
            $('.sdateFixedNoPayment').prop('required',false);
        }
    });
});

//# sourceMappingURL=app.js.map
