define(['jquery'], function($) {
  var Backend = {};

  /**
   * @param {string} id
   */
  Backend.generatePassword = function(id) {
    var input = $('#' + id),
      label = $('#' + id + '-label');

    label.click(function() {
      input.val(new Array(30).join().replace(/(.|$)/g, function() {
        return ((Math.random() * 36)|0).toString(36);
      })).trigger('change');
    });

    if (input.val() === '') {
      label.trigger('click');

      // Wait until form initialization is finished to trigger change event
      var interval = window.setInterval(function() {
        if (input.attr('data-formengine-input-initialized')) {
          input.trigger('change');

          window.clearInterval(interval);
        }
      }, 100);
    }
  };

  return Backend;
});
