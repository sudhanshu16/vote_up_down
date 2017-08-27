/**
 * @file
 * Vote Up/Down behavior.
 */
(function ($, Drupal) {

  Drupal.behaviors.voteUpDown = {
    attach: function (context, settings) {
      $('.vud-widget a').click(function (e) {
        e.preventDefault();
        var baseWidget = $(this).parents('.vud-widget');
        var routeUrl = $(this).is('a') ? $(this).attr('href') : $(this).parent().attr('href');
        var operation;
        if($(this).is('a.up.inactive'))
          operation = 'up';
        else if($(this).is('a.down.inactive'))
          operation = 'down';
        else
          operation = 'reset';
        voteUpDownService.vote(baseWidget, routeUrl, operation, settings.basePath, settings.points, settings.uservote);
      });
    }
  };

})(jQuery, Drupal);
