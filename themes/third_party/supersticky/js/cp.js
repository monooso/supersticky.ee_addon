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
      $wrapper.find('.criterion_roland')
        .roland({
          addRowClass     : 'criterion_add_row',
          removeRowClass  : 'criterion_remove_row',
          rowClass        : 'criterion_row'
        })
        .find('.criterion_row').each(function() {
          var $iniRow = $(this);
          
          iniCriterion.apply($iniRow[0]);

          $iniRow.find('.criterion_add_row')
            .bind('postAddRow', function(event, eventData) {
              console.log(eventData.newRow.html())
              iniCriterion.apply(eventData.newRow[0]);
            });
        });

      $wrapper.find('.member_group_roland')
        .roland({
          addRowClass     : 'member_group_add_row',
          removeRowClass  : 'member_group_remove_row',
          rowClass        : 'member_group_row'
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

      $row.find('.member_group_row').slice(1).remove();
      
      var $datePickers = $row
        .find('[id$="[date_from]"], [id$="[date_to]"]')
        .datepicker('destroy')
        .datepicker({
          changeMonth     : true,
          dateFormat      : 'yy-mm-dd',
          defaultDate     : '+1w',
          numberOfMonths  : 2,
          onSelect        : function(selectedDate) {
            // Ensure that the end date cannot be before the
            // start date, and vice-versa.
            var option = this.id.match(/\[date_from\]$/)
              ? 'minDate' : 'maxDate';

            var instance = $(this).data('datepicker');

            var date = $.datepicker.parseDate(
              instance.settings.dateFormat || $.datepicker._defaults.dateFormat,
              selectedDate,
              instance.settings
            );

            console.log('Date Picker ID: ' + this.id);

            $datePickers.not(this).datepicker('option', option, date);
          },
          showAnim        : 'fadeIn'
        });
    };


    // Superstar DJ, here we go...
    iniRoland();

  });

})(jQuery)


/* End of file      : cp.js */
/* File location    : themes/third_party/supersticky/js/cp.js */
