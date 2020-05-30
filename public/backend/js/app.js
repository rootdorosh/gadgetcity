var app = {
    getCsrfToken: function () {
        return $('meta[name="csrf-token"]').attr('content');
    },
    toggleSetValue: function toggleSetValue(wrap, value) {
        value = value.toString();

        $('.js-toggle-btn', wrap).each(function(){
            $(this).removeClass($(this).data('active-class'))
                .addClass('btn-default');
        })

        $('.js-toggle-btn', wrap).each(function () {
            if ($(this).attr('data-value') == value) {
                $(this).addClass($(this).data('active-class')).removeClass('btn-default');
            }
        })
        wrap.find('input[type="hidden"]').val(value);
    }
}

function showTabsError() {
    $('.tab-pane').each(function(){
        var self = $(this);
        var link = $('#nav-link-' + self.attr('id'));

        if (self.find('.has-error').length) {
            link.addClass('error');
        }
    })
}

var select2Options = {
    width: "100%",
    language: "ru-RU"
}

$(document).ready(function(){
    $('.select2').select2(select2Options);
    
    $('body').on('click', '.js-toggle-btn', function () {
        app.toggleSetValue($(this).closest('.btn-group'), $(this).data('value'))
    })
        
    showTabsError();
})