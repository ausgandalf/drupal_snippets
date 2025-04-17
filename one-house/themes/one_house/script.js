/*
 * custom js
 */

document.addEventListener("DOMContentLoaded", function() {
  initDrupalHelper();
});

function initDrupalHelper () {
  (function ($, window, Drupal, drupalSettings) {
    /**
     * Creates a new Snowman progress indicator, which really is full screen.
     */
    if (!Drupal || !Drupal.Ajax) return;
    Drupal.Ajax.prototype.setProgressIndicatorFullscreen = function () {
      this.progress.element = $(
        `<div class="ajax-progress ajax-progress--fullscreen loading">
          <div class="spinner__curtain">
            <div class="spinner__wrapper">
              <div class="spinner spinner--4">
                <div class="bar bar-top"></div>
                <div class="bar bar-right"></div>
                <div class="bar bar-bottom"></div>
                <div class="bar bar-left"></div>
              </div>
            </div>
          </div>
        </div>`
      );
      // Add fullscreen indicator
      $('body').append(this.progress.element);
    };

    Drupal.Ajax.prototype.setProgressIndicatorThrobber = function () {
      this.progress.element = $(
        `<div class="ajax-progress ajax-progress--throbber loading">
          <div class="spinner__curtain">
            <div class="spinner__wrapper">
              <div class="spinner spinner--4">
                <div class="bar bar-top"></div>
                <div class="bar bar-right"></div>
                <div class="bar bar-bottom"></div>
                <div class="bar bar-left"></div>
              </div>
            </div>
          </div>
        </div>`
      );
      // Add throbber
      if (this['$form']) {
        $(this['$form'][0]).append(this.progress.element);
      } else {
        $(this.element).after(this.progress.element);
      }
    };

  })(jQuery, window, Drupal, drupalSettings);
}