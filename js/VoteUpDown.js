/**
 * @file
 * Vote Up/Down behavior.
 */
(function ($, Drupal) {

  Drupal.behaviors.voteUpDown = {
    attach: function(context, settings) {
      $('.vud-widget .active').click(function (e) {
        e.preventDefault();
        return false;
      });
      $('.up.inactive').unbind('click');
      $('.up.inactive, .up.inactive a').click(function(e) {
        var operation = 'up';
        var routeUrl = $('a.up').attr('href');
        voteUpDownService.vote(routeUrl, operation, settings.basePath);
        e.preventDefault();
        return false;
      });
      $('.down.inactive').unbind('click');
      $('.down.inactive, .down.inactive a').click(function(e) {
        var operation = 'down';
        var routeUrl = $('a.down').attr('href');
        voteUpDownService.vote(routeUrl, operation, settings.basePath);
        e.preventDefault();
        return false;
      });
      $('.reset').unbind('click');
      $('.reset, .reset a').click(function(e) {
        var operation = 'reset';
        var routeUrl = $('a.reset').attr('href');
        voteUpDownService.vote(routeUrl, operation, settings.basePath);
        e.preventDefault();
        return false;
      });
    }
  };

})(jQuery, Drupal);
