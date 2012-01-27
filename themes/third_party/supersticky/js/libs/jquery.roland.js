/**
 * Add and delete 'rows' to any container element.
 *
 * @author      Stephen Lewis (http://github.com/experience/)
 * @copyright   Experience Internet
 * @version     1.1.5
 */

(function($) {

  $.fn.roland = function(options) {
    var opts = $.extend({}, $.fn.roland.defaults, options);

    return this.each(function() {
      var $container = $(this);

      if (opts.autoUpdateIndexes === true) {
        $.fn.roland.updateIndexes($container, opts);
      }

      if (opts.autoUpdateNav === true) {
        $.fn.roland.updateNav($container, opts);
      }

      // Adds a row.
      $container.find('.' + opts.addRowClass).bind('click', function(e) {
        e.preventDefault();

        var $link       = $(this),
            $parentRow  = $link.closest('.' + opts.rowClass),
            $lastRow    = $container.find('.' + opts.rowClass + ':last'),
            $cloneRow   = $lastRow.clone(true),
            eventData   = {};

        // Reset the field values.
        $cloneRow.find('input').each(function() {
          var type = $(this).attr('type');

          switch (type.toLowerCase()) {
            case 'checkbox':
            case 'radio':
              $(this).attr('checked', false);
              break;

            case 'email':
            case 'password':
            case 'search':
            case 'text':
              $(this).val('');
              break;
          }
        });

        $cloneRow.find('select').val('');

        // Pre-add event. Only checks return value from last listener.
        if ($container.data('events') !== undefined
          && $container.data('events').preAddRow !== undefined) {

          eventData = {container : $container, options : opts, newRow : $cloneRow};
          $cloneRow = $container.triggerHandler('preAddRow', [eventData]);

          // Returning FALSE prevents the row being added.
          if ($cloneRow === false) {
            return;
          }
        }

        // If the 'add row' link lives inside a row, insert the new row after it.
        // Otherwise, just tag it on the end.
        typeof $parentRow === 'object'
          ? $parentRow.after($cloneRow) : $lastRow.append($cloneRow);

        // Update everything.
        if (opts.autoUpdateIndexes === true) {
          $.fn.roland.updateIndexes($container, opts);
        }

        if (opts.autoUpdateNav === true) {
          $.fn.roland.updateNav($container, opts);
        }

        // Post-add event.
        if ($container.data('events') !== undefined
          && $container.data('events').postAddRow !== undefined) {
          
          eventData = {container : $container, options : opts, newRow : $cloneRow};
          $container.triggerHandler('postAddRow', [eventData]);
        }
      });


      // Removes a row.
      $container.find('.' + opts.removeRowClass).bind('click', function(e) {
        e.preventDefault();

        var $row = $(this).closest('.' + opts.rowClass);

        // Can't remove the last row.
        if ($row.siblings().length == 0) {
          return false;
        }

        $row.remove();

        // Update everything.
        $.fn.roland.updateIndexes($container, opts);
        $.fn.roland.updateNav($container, opts);
      });
    });
  };


  // Defaults.
  $.fn.roland.defaults = {
    rowClass        : 'row',
    addRowClass     : 'add_row',
    removeRowClass  : 'remove_row',
    autoUpdateIndexes : true,
    autoUpdateNav   : true
  };


  /* ------------------------------------------
   * PUBLIC METHODS
   * -----------------------------------------*/

  // Updates the indexes of any form elements.
  $.fn.roland.updateIndexes = function($container, opts) {
    $container.find('.' + opts.rowClass).each(function(rowCount) {
      var regex = /^([a-z_]+)\[(?:[0-9]+)\](.*)$/;

      $(this).find('input, select, textarea').each(function(fieldCount) {
        var $field = $(this),
            fieldId,
            fieldName;

        if ($field.attr('id')) {
          fieldId = $field.attr('id')
            .replace(regex, '$1[' + rowCount + ']$2');

          $field.attr('id', fieldId);
        }

        if ($field.attr('name')) {
          fieldName = $field.attr('name')
            .replace(regex, '$1[' + rowCount + ']$2');

          $field.attr('name', fieldName);
        }
      });
    });
  };

  // Updates the navigation buttons.
  $.fn.roland.updateNav = function($container, opts) {
    var $remove = $container.find('.' + opts.removeRowClass),
        $rows   = $container.find('.' + opts.rowClass);

    $rows.size() == 1 ? $remove.hide() : $remove.show();
  };


})(jQuery);


/* End of file      : jquery.roland.js */
