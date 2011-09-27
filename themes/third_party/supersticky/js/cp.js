/**
 * SuperSticky control panel behaviours.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

(function($) {

  $(document).ready(function() {
    var $wrapper = $('#supersticky_ft');

    // Initialises Roland.
    function iniRoland() {
      $wrapper.find('.roland')
        .roland()
        .find('.row').each(function() {
          iniCriterion($(this));
        })
    }


    // Initialises a single criterion.
    function iniCriterion($row) {
      $row.find('.ss_criterion_options').hide();

      $row.find('.add_row').bind('preAddRow', function(event, eventData) {
          iniCriterion(eventData.newRow);
          return eventData.newRow;
        });

      /*
      rgb = 'rgb(' + Math.round(Math.random() * 255) + ', '
          + Math.round(Math.random() * 255) + ', '
          + Math.round(Math.random() * 255) + ')';

      $row.find('select[name$="[type]"]').css('border', '10px solid ' + rgb);
      */

      $row.find('select[name$="[type]"]').change(function() {
        var criterionType         = this.value;
        var criterionOptionsClass = '.ss_criterion_options_' + criterionType;
        var $criterionOptions     = $row.find(criterionOptionsClass);

        $criterionOptions
          .fadeIn()
          .siblings('.ss_criterion_options').hide();
      });
    }


    // Superstar DJ, here we go...
    iniRoland();

    /**
     * @todo Unbind change handlers when row is deleted (updates to Roland).
     */
  });

})(jQuery)


/* End of file      : cp.js */
/* File location    : themes/third_party/supersticky/js/cp.js */
