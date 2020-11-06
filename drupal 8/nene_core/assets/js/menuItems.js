(function ($, Drupal) {
    Drupal.behaviors.myDashboard = {
        attach: function (context, settings) {
            $('.nene-parents-menu a', context).click(function(e) {
                e.preventDefault();
                $('a').removeClass('active');
                $(this).addClass('active');
            });
        }
    };
})(jQuery, Drupal);
