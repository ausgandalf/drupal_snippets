// Javascript for Admin Panel
document.addEventListener("DOMContentLoaded", function() {

  (function ($, Drupal, once) {
    Drupal.behaviors.onehouseBaseBehavior = {
      attach: function (context, settings) {

        once('onehouse_remove_tid', context).forEach(function (element) {
          //
          // Remove TID's onload.
          if (element.tagName == 'BODY') {
            OnehouseAdmin.remove_tid_all();

            // Remove TID's onchange.
            jQuery('body').find('.form-autocomplete').on('autocompleteclose', function() {
              console.log('on close');
              OnehouseAdmin.remove_tid(this);
            });
          }
        });

        once('onehouse_admin_class', context).forEach(function (element) {
          //
          OnehouseAdmin.applyClass(element);
        });

        once('onehouse_admin_event', context).forEach(function (element) {
          //
          OnehouseAdmin.applyEventHandlers(element);
        });

      }
    };
  })(jQuery, Drupal, once);

});

class OnehouseAdmin {

  static applyClass(elem) {
    // console.log('onehouse admin : class');
    // console.log(elem);
  }

  static applyEventHandlers(elem) {
    // console.log('onehouse admin : event');
    // console.log(elem);
  }

  static remove_tid_all() {
    let elems = jQuery('body').find('.form-autocomplete');
    for (let i=0; i<elems.length; i++) {
      this.remove_tid(elems[i]);
    }
  }

  static remove_tid(element) {
    let filters = [
      'field_zip_code',
      'field_neighborhood',
      'field_year_sold',
    ];
    let elem = jQuery(element);
    let namev = elem.attr('name');
    let isValid = false;
    for (let i=0; i<filters.length; i++) {
      if (namev.indexOf(filters[i] + '[') === 0) {
        isValid = true;
        break;
      }
    }
    if (!isValid) return;

    let pieces = elem.val().split(' ');
    if (pieces.length > 1) {
      if (pieces.slice(-1)[0].startsWith('(')) {
        let autocomplete_tid_stripped = pieces.slice(0, -1).join(' ');
        elem.val(autocomplete_tid_stripped);
      }
    }
  }

}
