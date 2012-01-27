/**
 * SuperSticky control panel behaviours.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

(function($) {

  $(document).ready(function() {
    var $wrapper      = $('#supersticky_ft'),
        criterionOpts = {
          addRowClass     : 'criterion_add_row',
          removeRowClass  : 'criterion_remove_row',
          rowClass        : 'criterion_row'
        },
        memberOpts    = {
          autoUpdateIndexes : false,
          addRowClass     : 'member_group_add_row',
          removeRowClass  : 'member_group_remove_row',
          rowClass        : 'member_group_row'
        };


    /**
     * Initialises a single criterion.
     *
     * @access  private
     * @return  void
     */
    function iniCriterion() {
      var $criterion = $(this);

      // Remove extraneous member group rows.
      $criterion.find('.member_group_row').slice(1).remove();

      // Remove click handlers.
      $criterion.find('.member_group_add_row').unbind('click');
      $criterion.find('.member_group_remove_row').unbind('click');

      // Re-Roland the member groups.
      $criterion.find('.member_group_roland').roland(memberOpts);
    };


    /**
     * Initialises a criterion's date pickers.
     *
     * @access  private
     * @return  void
     */
    function iniDatePickers() {
      var $row = $(this);

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

            $datePickers.not(this).datepicker('option', option, date);
          },
          showAnim        : 'fadeIn'
        });
    };


    /**
     * Initialises Roland.
     *
     * @access  private
     * @return  void
     */
    function iniRoland() {
      $wrapper.find('.criterion_roland')
        .roland(criterionOpts)
        .bind('postAddRow', function(event, eventData) {
          iniCriterion.apply(eventData.newRow[0]);
          iniDatePickers.apply(eventData.newRow[0]);
        })
        .find('.' + criterionOpts.rowClass).each(function() {
          iniDatePickers.apply(this);
        });

      $wrapper.find('.member_group_roland').roland(memberOpts);
    };


    // Superstar DJ, here we go...
    iniRoland();

  });

})(jQuery)


/* End of file      : cp.js */
/* File location    : themes/third_party/supersticky/js/cp.js */
