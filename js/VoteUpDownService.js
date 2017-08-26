/**
 * @file
 * Vote Up/Down behavior service
 */
(function ($, Drupal) {

  window.voteUpDownService = (function() {
    function voteUpDownService() {}
    voteUpDownService.vote = function(url, operation, basePath) {
      $('.vud-widget').append('<img class="throbber" src="' + drupalSettings.path.baseUrl + '/' + basePath + '/img/status-active.gif">');
      $.ajax({
        type: "GET",
        url: url,
        success: function(response) {
          $('.throbber').remove();
          $('.reset').addClass('element-invisible');
          $('.up.active').removeClass('active').addClass('inactive')
            .parent().removeClass('active').addClass('inactive');
          $('.down.active').removeClass('active').addClass('inactive')
            .parent().removeClass('active').addClass('inactive');
          if(operation !== 'reset') {
            $('.' + operation).removeClass('inactive').addClass('active')
              .parent().removeClass('inactive').addClass('active');
            $('.reset').removeClass('element-invisible');
          }
          // $('.region.region-highlighted').html("<div class='messages__wrapper layout-container'><div class='messages messages--" + response.message_type + " role='contentinfo'>" + response.message + "</div></div>");
        }
      });
    };
    return voteUpDownService;
  })();

})(jQuery, Drupal);
