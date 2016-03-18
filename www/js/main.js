$(function() {

    (function() {
        $('[data-confirm]').click(function(e) {
            var q = $(this).attr('data-confirm');
            var a = window.confirm(q);
            if (a === false) {
                e.preventDefault();
            }
        });
    })();

    lightbox.option({
        'wrapAround': true
    });

    $.nette.ext({
        load: function() {
            $('.flash, .form-error').find('.close').click(function() {
                $(this).parent().remove();
            });
        }
    });

    $.nette.init();

});
