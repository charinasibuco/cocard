var volunteer_count = 1;
validateVolunteer = function(){
    form = $(".volunteer-form");
    var unique_url = form.data("unique_url");
    form.validate({
        debug: true,
        rules: {

            email: {
                required: true,
                email: true,
                remote: {
                    url: unique_url,
                    type: "post"
                }
            }
        },
        messages: {
            email:{
                remote: "Email has been taken"
            }
        }
    });
}

deleteVolunteer = function(){
    $(".delete-volunteer").click(function(){
        var fieldset = $(this).closest("fieldset");
        fieldset.hide("fast",function(){ fieldset.remove(); });
        volunteerSubmitButton('delete_applicant');
    });
};

volunteerSubmitButton = function(type){
    if(type == 'delete_applicant'){
        var count_fieldset = $('.volunteer-fieldset').length -1;
    }else{
        var count_fieldset = $('.volunteer-fieldset').length;
    }
    
    if(count_fieldset > 0 || $('#include_user').is(":checked")){
        $('.volunteer-submit').attr('disabled',false);
    }else{
        $('.volunteer-submit').attr('disabled',true);
    }
}
generateVolunteer = function(){
    var add_volunteer = $("#add-volunteer");
    $.post(add_volunteer.data('post_url'),{event_id: add_volunteer.data("event_id"),volunteer_group_id:add_volunteer.data("volunteer_group_id"),count:volunteer_count,_token:"f984f4re0t4oiwjo4"}).done(
        function(data){
            add_volunteer.before(data);
            var volunteer_name = $("#"+add_volunteer.data('event_id')+"-"+volunteer_count+"-name");
            $("#"+add_volunteer.data("event_id")+"-"+volunteer_count+"-volunteer-fieldset").show("fast",function(){
                volunteer_name.focus();
            });
            validateVolunteer();
            deleteVolunteer();
            volunteer_count++;
            volunteerSubmitButton('add-volunteer');
        }
    );
};
addVolunteer = function(){
    $("#add-volunteer").click(function(){
        generateVolunteer();
    });
};

applyVolunteer = function(){
    $(".apply-volunteer").click(function(){
        $(".volunteer-form-container").show("fast");
        $(this).hide("fast");
    });
    $(".hide-apply-volunteer").click(function(){
        $(".volunteer-form-container").hide("fast");
        $(".apply-volunteer").show("fast");
    });

};
function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}
/////// C A L E N D A R U S E R

loadEventDetails = function(event,token,dateClicked){
    $.post($(".calendar-modal").data("event_modal_url"),{id: event.id,slug:$("#event-modal").data("slug"),_token:token}).done(function(data){
        $(".calendar-modal .modal-content").html(data);
        $(".calendar-modal").modal();
        applyVolunteer();
        addVolunteer();
        console.log(moment(event.end).format('YYYY-MM-DD'));
        $('.past_date').val(moment(event.start).format('YYYY-MM-DD')+'/'+moment(event.end).format('YYYY-MM-DD')+'/'+event.count_);
        $('.past_date_delete').val(moment(event.start).format('YYYY-MM-DD')+'/'+event.count_);
        $('.occur_count').val(event.count_);
        $('.occur_count2').attr('value',event.count_);
        //console.log(event.count_ + '  count');

        //$('.event_loop').val();
        if(event.start != null){
            $('#start').text(moment(event.start).format('M/D/YYYY h:mm A'));
            $('#start_date_timezone').val(moment(event.start).format('M/D/YYYY h:mm A'));
            $('#date_pass_current_start_date').val(moment(event.start).format('M/D/YYYY h:mm A'));
            $('.occurrence').val(event.count_);

            if(event.end == null){
                $('#end').text(moment(event.start).format('M/D/YYYY'));
                $('#end_date_timezone').val(moment(event.start).format('M/D/YYYY'));
            }else{
                $('#end').text(moment(event.end).format('M/D/YYYY h:mm A'));
                $('#end_date_timezone').val(moment(event.end).format('M/D/YYYY h:mm A'));
            }
            if(event.volunteer_group_id != null){
                $('#volunteer_group').attr('value',event.volunteer_group_id);
            }
            console.log(+'--------vg'+event.volunteer_group_id);
        }

        //hash
        var newvalue= moment(event.start).format('YYYY-MM-DD')+'/'+moment(event.end).format('YYYY-MM-DD');
        if(event.count_ == 0){
            event.count_ = (event.count_+1);
        }
        var value = $('.js-edit-event-hash').attr('href',$('.js-edit-event-hash').attr('href') + '/instance/' + event.count_ + '#' + newvalue+'/'+event.count_);
        // var value = $('.js-edit-event-hash_modify').attr('href',$('.js-edit-event-hash_modify').attr('href') + newvalue+'/'+event.count_);
        var value = $('.js-edit-event-hash_modify').attr('href',$('.js-edit-event-hash_modify').attr('href') + '/instance/' + event.count_ + '#' + newvalue+'/'+event.count_);
        //console.log(newvalue)(;
        generateVolunteer();
        validateVolunteer();

        $("input[type=checkbox]").click(function(){
            if($(this).prop("checked") == false){
                $("#user_details").hide("fast");
            }else{
                $("#user_details").show("fast");
            }
        });

        $('.volunteer-form').submit(function(e) {
            e.preventDefault();
            var volunteer_form = $(this);
            if($(this).valid()){
                $.post(volunteer_form.prop("action"),volunteer_form.serialize()).done(function(data2){
                    var string = data2;
                    if(isJson(data2)){
                        var check_output = jQuery.parseJSON(data2);
                        var error_container = $(".error-container");
                        error_container.html("");
                        $.each(check_output,function(index,item){
                            error_html = "<div class='alert alert-danger'> <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>"+item.value+" - "+item.message+"</div>";
                            error_container.append(error_html);
                        });
                    }else{
                        $(".calendar-modal .modal-content").html(string);
                        applyVolunteer();
                    }
                });
            }
        });
    });
};

addEvent = function(start,end){
    $.post($(".calendar-modal-add").data("event_modal")).done(function(data){
        $(".calendar-modal-add .modal-content").html(data);
        $(".calendar-modal-add").modal();
        $(".start_date_modal").val(moment(start).add(8,'hours').format('M/D/YYYY h:mm A'));
        $(".end_date_modal").val(moment(end).subtract(1,'days').add(9,'hours').format('M/D/YYYY h:mm A'));

    });
};

//recurring calendar
function recurringEvent(calendar,calendar_events,token){
    $.getScript('//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.4.0/fullcalendar.min.js',function(){
        var arraySize = calendar_events.length;
        var length = 1;
        var date = new Date(calendar);
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        var data = [];
        calendar_events.forEach(function(result ){
            var bg_color = "#2874A6";//blue as today's event
            var text_color = "#FFFFFF";
            //.console.log(date.getDate()+'----------------------------------------');
            var start_date              = new Date(result.start_date);
            var end_date                = new Date(result.end_date);
            var modify_recurring_month  = result.modify_recurring_month;
            var recurring_end_date      = new Date(result.recurring_end_date);
            var current_date            = new Date();            //convert start date
            var offset = new Date().getTimezoneOffset();
            start_date = moment.utc(result.start_date);
            start_date = new Date(start_date.utcOffset(offset * -1));

            //convert end date
            end_date = moment.utc(result.end_date);
            end_date = new Date(end_date.utcOffset(offset * -1));

            //convert modify_recurring_month date
            //modify_recurring_month = moment.utc(result.modify_recurring_month);
            //modify_recurring_month = new Date(modify_recurring_month.utcOffset(offset * -1));

            //convert recurring_end_date date
            recurring_end_date = moment.utc(result.recurring_end_date);
            recurring_end_date = new Date(recurring_end_date.utcOffset(offset * -1));

            var recurring = result.recurring;
            var status = result.status;
            var no_of_repetition = parseInt(result.no_of_repetition);
            no_of_repetition = parseInt(no_of_repetition);
            //console.log(no_of_repetition);

            // //start date of the event. this is for pre-loaded events
            var t = moment(start_date);
            //see if an event is recurring
            switch(result.recurring){
                case '0'://once
                case 0:
                 //green as future event
            if(moment(start_date).format("MM DD YYYY") > moment(current_date).format("MM DD YYYY")){
                bg_color = "#056E0E";//green for future events
                if(moment(start_date).format("YYYY") > moment(current_date).format("YYYY")){
                    bg_color = "#056E0E";//green for future events
                }
                //console.log('future'+moment(start_date).format("MM DD YYYY")+'-------'+moment(t).format("MM DD YYYY"));
            }
            //set blue for date today
            if(moment(start_date).format("MM DD YYYY") == moment(current_date).format("MM DD YYYY")){
                bg_color = "#2874A6";//blue for today
                if(moment(start_date).format("YYYY") > moment(current_date).format("YYYY")){
                    bg_color = "#056E0E";//green for future events
                }
                //console.log('today'+moment(start_date).format("MM DD YYYY")+'-------'+moment(current_date).format("MM DD YYYY"));

            }
            //gray as past event
            if(moment(start_date).format("MM DD YYYY") < moment(current_date).format("MM DD YYYY")){
                bg_color = "#C0C0C0";//gray for past events
                if(moment(start_date).format("YYYY") > moment(current_date).format("YYYY")){
                    bg_color = "#056E0E";//green for future events
                }
                //console.log('past'+moment(start_date).format("MM DD YYYY")+'-------'+moment(current_date).format("MM DD YYYY"));

            }
                data.push({
                    title: result.name,
                    start: start_date,
                    count_:0,
                    end: end_date,
                    color: bg_color,
                    textColor: text_color,
                    id: result.id,
                    url: '/'//+result.id
                });
                break;
                case '1'://weekly
                case 1:
                var i = no_of_repetition;//instance
                var e = moment(recurring_end_date);//end date of recurring
                var count = 1;
                if(no_of_repetition > 0){
                    e = moment(end_date).add((i*7),'days');
                    i = 0;
                }
                    console.log(result.name+'---t: '+t+'----E: '+e);

                for(var t; t<e; i+=7){

                    var dn = new Date();
                    var sd = moment(start_date).add(i,'days');
                    var ed = moment(end_date).add(i,'days');
                    t      = moment(ed).add(7, 'days');
                    var d  = ed.diff(sd, 'days');
                    //t      = moment(ed).add(d, 'days');
                    //filter events to show
                    var arr = modify_recurring_month.split(',');
                    arr.splice(0,1);
                       //set the color to future event
                    if(moment(sd).format("MM DD YYYY") > moment(current_date).format("MM DD YYYY")){
                        bg_color = "#056E0E";//green for future events
                        if(moment(sd).format("YYYY") > moment(current_date).format("YYYY")){
                             bg_color = "#056E0E";//green for future events
                        }
                        //console.log('future'+moment(sd).format("MM DD YYYY")+'-------'+moment(t).format("MM DD YYYY"));
                    }
                    //set blue for date today
                    if(moment(sd).format("MM DD YYYY") == moment(current_date).format("MM DD YYYY")){
                        bg_color = "#2874A6";//blue for today
                        if(moment(sd).format("YYYY") > moment(current_date).format("YYYY")){
                             bg_color = "#056E0E";//green for future events
                        }
                        //console.log('today'+moment(sd).format("MM DD YYYY")+'-------'+moment(current_date).format("MM DD YYYY"));

                    }
                    //gray as past event
                    if(moment(sd).format("MM DD YYYY") < moment(current_date).format("MM DD YYYY")){

                        bg_color = "#C0C0C0";//gray for past events
                        if(moment(sd).format("YYYY") > moment(current_date).format("YYYY")){
                             bg_color = "#056E0E";//green for future events
                        }
                        //console.log('past'+moment(sd).format("MM DD YYYY")+'-------'+moment(current_date).format("MM DD YYYY"));

                    }
                    if(arr.length == 0){
                        data.push({
                            title: result.name,
                            count_:count,
                            start: sd,
                            end: ed,
                            color: bg_color,
                            textColor: text_color,
                            id: result.id,
                            url: '/'//+result.id
                        });
                    }else{

                        if(arr.indexOf(moment(sd).format('YYYY-MM-DD').toString()) == -1){
                            data.push({
                                title: result.name,
                                count_:count,
                                start: sd,
                                end: ed,
                                color: bg_color,
                                textColor: text_color,
                                id: result.id,
                                url: '/'+result.id
                            });
                        }
                    }
                    count ++;
                    ////console.log(data);
                }
                    ////console.log(data);
                break;
                case '2'://monthly
                case 2:
                var count = 1;
                var i = no_of_repetition;//instance
                var e = moment(recurring_end_date);//end date of recurring
                if(no_of_repetition > 0){
                    e = moment(start_date).add(i,'months');
                    i = 0;
                }
                //console.log(ee+"----------"+e);
                for(var t; t<e; i++){
                    //set the color to future event
                    if(i > 0){
                        bg_color = "#056E0E";
                    }
                    var dn = new Date();
                    var sd = moment(start_date).add(i,'months');
                    var ed = moment(end_date).add(i,'months');
                    t      = moment(ed).add(1, 'months');
                    //filter events to show
                    var arr = modify_recurring_month.split(',');
                    arr.splice(0,1);
                       //set the color to future event
                    if(moment(sd).format("MM DD YYYY") > moment(current_date).format("MM DD YYYY")){
                        bg_color = "#056E0E";//green for future events
                        if(moment(sd).format("YYYY") > moment(current_date).format("YYYY")){
                             bg_color = "#056E0E";//green for future events
                }
                        //console.log('future'+moment(sd).format("MM DD YYYY")+'-------'+moment(t).format("MM DD YYYY"));
                    }
                    //set blue for date today
                    if(moment(sd).format("MM DD YYYY") == moment(current_date).format("MM DD YYYY")){
                        bg_color = "#2874A6";//blue for today
                        if(moment(sd).format("YYYY") > moment(current_date).format("YYYY")){
                             bg_color = "#056E0E";//green for future events
                }
                        //console.log('today'+moment(sd).format("MM DD YYYY")+'-------'+moment(current_date).format("MM DD YYYY"));

                    }
                    //gray as past event
                    if(moment(sd).format("MM DD YYYY") < moment(current_date).format("MM DD YYYY")){
                        bg_color = "#C0C0C0";//gray for past events
                         if(moment(sd).format("YYYY") > moment(current_date).format("YYYY")){
                             bg_color = "#056E0E";//green for future events
                        }
                      //  console.log('past'+moment(sd).format("MM DD YYYY")+'-------'+moment(current_date).format("MM DD YYYY"));

                    }
                    if(arr.length == 0){
                        data.push({
                            title: result.name,
                            count_:count,
                            start: sd,
                            end: ed,
                            color: bg_color,
                            textColor: text_color,
                            id: result.id,
                            url: '/'+result.id
                        });
                    }else{
                        if(arr.indexOf(moment(sd).format('YYYY-MM-DD').toString()) == -1){
                            data.push({
                                title: result.name,
                                count_:count,
                                start: sd,
                                end: ed,
                                color: bg_color,
                                textColor: text_color,
                                id: result.id,
                                url: '/'+result.id
                            });
                        }
                    }
                    count ++;
                    ////console.log(data);
                }
                break;
                case '3'://yearly
                case 3:
                var count = 1;
                var i = no_of_repetition;//instance
                var e = moment(recurring_end_date);//end date of recurring
                if(no_of_repetition > 0){
                    e = moment(start_date).add((i*12),'months');
                    i = 0;
                }
                for(var t; t<e; i+=12){
                    //set the color to future event
                    if(i > 0){
                        bg_color = "#056E0E";
                    }
                    var dn = new Date();
                    var sd = moment(start_date).add(i,'months');
                    var ed = moment(end_date).add(i,'months');
                    if(no_of_repetition > 0){
                    t      = moment(ed).add(12,'months');
                    }else{
                    t      = moment(ed).add(12,'months');                        
                    }
                    //var diff = moment(result.end_date).diff(result.start_date, 'days');//get date days diff
                   //filter events to show

                    var arr = modify_recurring_month.split(',');
                    arr.splice(0,1);
                    //console.log(arr+ start_date);
                       //set the color to future event
                    if(moment(sd).format("MM DD YYYY") > moment(current_date).format("MM DD YYYY")){
                        bg_color = "#056E0E";//green for future events
                        //console.log('future'+moment(sd).format("MM DD YYYY")+'-------'+moment(t).format("MM DD YYYY"));
                    }
                    //set blue for date today
                    if(moment(sd).format("MM DD YYYY") == moment(current_date).format("MM DD YYYY")){
                        bg_color = "#2874A6";//blue for today
                        //console.log('today'+moment(sd).format("MM DD YYYY")+'-------'+moment(current_date).format("MM DD YYYY"));

                    }
                    //gray as past event
                    if(moment(sd).format("MM DD YYYY") < moment(current_date).format("MM DD YYYY")){
                        bg_color = "#C0C0C0";//gray for past events
                        if(moment(sd).format("YYYY") > moment(current_date).format("YYYY")){
                             bg_color = "#056E0E";//green for future events
                        }
                        //console.log('past'+moment(sd).format("MM DD YYYY")+'-------'+moment(current_date).format("MM DD YYYY"));

                    }
                    if(arr.length == 0){
                        data.push({
                            title: result.name,
                            count_:count,
                            start: sd,
                            end: ed,
                            color: bg_color,
                            textColor: text_color,
                            id: result.id,
                            url: '/'+result.id
                        });
                    }else{
                        if(arr.indexOf(moment(sd).format('YYYY-MM-DD').toString()) == -1){
                            data.push({
                                title: result.name,
                                count_:count,
                                start: sd,
                                end: ed,
                                color: bg_color,
                                textColor: text_color,
                                id: result.id,
                                url: '/'+result.id
                            });
                        }
                    }
                    count ++;
                    //console.log(sd.format("MM DD YYYY") +"---"+ed.format("MM DD YYYY")+"---"+moment(result.end_date).add('days',ed).format("MM DD YYYY"));
                }
                break;
            }

        });

        if(length == arraySize || length == 1){
            $(calendar).fullCalendar({
                header: {
                    left: 'prevYear,nextYear,today',
                    center: 'prev,title,next',
                    right: 'month,agendaWeek,agendaDay,listMonth'
                },
                navLinks: true, // can click day/week names to navigate views
                eventLimit: true, // allow "more" link when too many events
                slotEventOverlap: true,
                eventOverlap:true,
                resourceEventOverlap:true,
                selectOverlap:true,//overlap select
                overlap: true,
                fixedWeekCount: false,
                selectable: true,
                selectHelper: true,
                events: data,
                eventClick: function(event, jsEvent, view) {
                    // get and convert to date startdate
                    var startdate = new Date(event.start._d);
                    var newstartdate = startdate.getFullYear() + "-" + startdate.getMonth() + "-" + startdate.getDay();
                    loadEventDetails(event, token,'' + newstartdate);
                    if(event.url){
                            return false;
                    }
                },
                select: function(start, end) {
                    // var title = prompt('Event Title:');
                    // var eventData;
                    // if (title) {
                    //  eventData = {
                    //      title: title,
                    //      start: start,
                    //      end: end
                    //  };
                    //  $(calendar).fullCalendar('renderEvent', eventData, true); // stick? = true
                    // }
                    // $(calendar).fullCalendar('unselect');
                    //format end date

                    //console.log(moment(end).format('M/D/YYYY')+'----------------------------------');
                    addEvent(start,end);
                    return false;
                },
                buttonText: {
                    today: 'Today',
                    month: 'Month',
                    week: 'Week',
                    day: 'Day',
                    list: 'List'
                }
            });
        }else{
            length++;
            // /console.log(length);
        }
    });
}
function singleEvent(calendar,calendar_events,token) {
    $.getScript('//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.js',function(){
        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();
        var data = [];
        calendar_events.forEach(function(result){
            var text_color = "#FFFFFF";
            var start_date =  new Date(result.start_date);
            var end_date = new Date(result.end_date);
            //convert start date
            var offset = new Date().getTimezoneOffset();
            var new_start_date = moment.utc(result.start_date);
            var converted_start_date = new Date(new_start_date.utcOffset(offset * -1));
            //convert end date
            var new_end_date = moment.utc(result.end_date);
            var converted_end_date = new Date(new_end_date.utcOffset(offset * -1));
            var current_date = new Date();
            var bg_color = "#2874A6";//blue as today's event
            //green as future event
            if(moment(current_date).format("MM DD YYYY") < moment(converted_start_date).format("MM DD YYYY")){
                bg_color = "#056E0E";
            }
            //gray as past event
            if(current_date > converted_end_date){
                bg_color = "#C0C0C0";
            }
            if(converted_start_date ){
                data.push({
                    title: result.name,
                    start: converted_start_date,
                    end: converted_end_date,
                    color: bg_color,
                    textColor: text_color,
                    id: result.id,
                    url: '/'+result.id

                });
            }

        });

        $(calendar).fullCalendar({
            header: {
                left: 'prevYear,nextYear,today',
                center: 'prev,title,next',
                right: 'month,agendaWeek,agendaDay,listMonth'
            },
            navLinks: true, // can click day/week names to navigate views
            eventLimit: true, // allow "more" link when too many events
            slotEventOverlap: false,
            eventOverlap:false,
            selectOverlap:false,
            overlap: false,
            fixedWeekCount: false,
            allDay: true,
            events: data,
            eventClick: function(event) {
                if (event.url) {
                    loadEventDetails(event, token,0);
                    return false;
                }
            },
            buttonText: {
                today: 'Today',
                month: 'Month',
                week: 'Week',
                day: 'Day',
                list: 'List'
            }
        });
    });
}
