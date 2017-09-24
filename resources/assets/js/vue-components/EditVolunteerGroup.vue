<template>
<span class="action-block">
    <div class="mask" transition="fade" v-if="show_form">

        <div class="panel modifier panel-default centered-panel">
            <div class="panel-heading">
                <span class="clearfix">
                    <span class="pull-left">&nbsp;</span>
                    <span class="pull-right">
                        <a href="#" class="close-button" @click="showForm"><i class="icon-close" aria-hidden="true"></i></a>
                    </span>
                </span>
            </div>
            <div class="panel-body">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Volunteer Group Title</label>
                        <input type="text" class="form-control" id="groupTitle" name="groupTitle">
                    </div>
                    <div class="form-group">
                        <label>Start Date &amp; Time</label>
                        <div class="input-group">
                            <input type="text" placeholder="M/D/YYYY HH:MM AM/PM" class="form-control"
                                    v-dtpicker="volunteer.start_date">
                            <span class="input-group-addon">
                                <span class="glyphicon-calendar glyphicon"></span>
                            </span>
                        </div>
                        <!-- <input type="text" class="form-control" id="startDatePick" name="startDatePick"> -->
                    </div>
                    <div class="form-group">
                        <label>End Date &amp; Time</label>
                        <div class="input-group endDatePick" id="endDatePick">
                            <input type="text" placeholder="M/D/YYYY HH:MM AM/PM" class="form-control"
                                    v-model="volunteer.end_date">
                            <span class="input-group-addon">
                                <span class="glyphicon-calendar glyphicon"></span>
                            </span>
                        </div>
                        <!-- <input type="text" class="form-control" id="endDatePick" name="endDatePick"> -->
                    </div>
                    <div class="form-group">
                        <label>Number of Volunteers Needed</label>
                        <input type="text" class="form-control" id="population" name="population">
                    </div>
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea class="form-control" id="notes" name="notes"></textarea>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <a :id="id" href="#" @click="get_volunteer_group"><i class="fa fa-edit"></i></a>
    <a href="#">Delete</a>
</span>
</template>

<script>
export default {
    props: ['id'], 

    data () {
        return { 
            show_form: false,
            volunteer: {
                name: '',
                start_date: null,
                end_date: null,
                population: 0,
                notes: '',
            }
        };
    }, 

    mounted() {
        this.$http.get('/api/volunteer-group/edit/'+this.id).then(function(response) {
                console.log(response);
            }.bind(this));

    },

    methods: {
        showForm() {
            this.show_form = (this.show_form) ? false : true;
        },

        get_volunteer_group() {
            this.showForm();

            // $(function() {
            //     $('#startDatePick').datetimepicker({
            //         format: 'M/D/YYYY h:mm A'
            //     }); 
            //     $('.endDatePick').datetimepicker({
            //         useCurrent: false,
            //         format: 'M/D/YYYY h:mm A'
            //     });
            //     $(".startDatePick").on("dp.change", function (e) {
            //         $('.endDatePick').data("DateTimePicker").minDate(e.date);
            //     });
            //     $(".endDatePick").on("dp.change", function (e) {
            //         $('.startDatePick').data("DateTimePicker").maxDate(e.date);
            //     });
            // });
        }
    }
}
</script>