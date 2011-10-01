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

    /**
     * Initialises Roland.
     *
     * @access  private
     * @return  void
     */
    function iniRoland() {
      $wrapper.find('.roland')
        .roland()
        .find('.row').each(function() {
          var $iniRow = $(this);
          
          iniCriterion.apply($iniRow[0]);

          /**
           * 'Criterion type' change handler. This is automatically
           * copied to new rows, so there's no need to include it
           * in the 'iniCriterion' function.
           *
           * Also, we _must not_ use $iniRow in the change handler, as
           * it refers to the original row object.
           *
           * The previous two paragraphs; two hours of my life.
           */

          $iniRow.find('select[name$="[type]"]').change(function() {
            var $row                  = $(this).closest('tr');
            var criterionType         = this.value;
            var criterionOptionsClass = '.ss_criterion_options_' + criterionType;
            var $criterionOptions     = $row.find(criterionOptionsClass);

            /**
             * The jQuery UI DatePicker is a complete pain. Any attempts
             * to initialise it when the row is created fail, as the
             * date picker is _activated_ on the correct field, but then
             * proceeds to _populate_ the original row.
             */

            $criterionOptions.find('id$="[date_range_from]", id$="[date_range_to]"')
              .datepicker('destroy')
              .datepicker({
                changeMonth     : true,
                defaultDate     : '+1w',
                numberOfMonths  : 3
              });

            $criterionOptions
              .fadeIn()
              .siblings('.ss_criterion_options').hide();
          }).change();

          // Same deal as the change handler.
          $iniRow.find('.add_row')
            .bind('preAddRow', function(event, eventData) {
              iniCriterion.apply(eventData.newRow[0]);
              return eventData.newRow;
            });
        });
    };


    /**
     * Initialises a single criterion.
     *
     * @access  private
     * @return  void
     */
    function iniCriterion() {
      var $row = $(this);
      $row.find('.ss_criterion_options').hide();
    };


    // Superstar DJ, here we go...
    iniRoland();

    /**
     * @todo Unbind change handlers when row is deleted (updates to Roland).
     */
  });

})(jQuery)


/* End of file      : cp.js */
/* File location    : themes/third_party/supersticky/js/cp.js */
