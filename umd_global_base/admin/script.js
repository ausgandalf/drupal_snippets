// Javascript for Admin Panel
document.addEventListener("DOMContentLoaded", function() {

  (function ($, Drupal, once) {
    Drupal.behaviors.umd_globalBaseBehavior = {
      attach: function (context, settings) {

        once('umd_global_remove_tid', context).forEach(function (element) {
          //
          if (element.tagName == 'BODY') {

          }
        });
      }
    };
  })(jQuery, Drupal, once);

});

class umd_globalAdmin {

  static test(elem) {

  }

}
