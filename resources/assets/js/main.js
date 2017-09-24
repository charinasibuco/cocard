
/**
 * First we will load all of this project's JavaScript dependencies which
 * include Vue and Vue Resource. This gives a great starting point for
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
Vue.directive('dtpicker', {
    twoWay: true,
    priority: 1000,

    params: ['options'],
    
    bind: function () {
        var self = this
            // $(this.el).select2()
            //     .on('change', function () {
            //         self.set(this.value)
            //     })
            $(this.el).datetimepicker({
                    format: 'M/D/YYYY h:mm A'
                }).on('change', function() {
                	$(this.el).find('input').value(this.value);
                }); 
    },
    update: function (value) {
        $(this.el).val(value).trigger('change')
    },
    unbind: function () {
        $(this.el).off().datetimepicker('destroy')
    }
})

Vue.component('edit-volunteer-group', require('./vue-components/EditVolunteerGroup.vue'));

const app = new Vue({
    el: '#app',

    data() {
    	return {
    		
    	}
    }
});
