<div id="supersticky_ft">

  <table border="0" cellpadding="0" cellspacing="0">
    <tbody class="roland">
    <?php
      if ($entry):
      foreach ($entry->get_criteria() AS $criterion):
    ?>
      <tr class="row">
        <td width="30%">
        <?php
          echo form_dropdown(
            'supersticky_criteria[0][type]',
            $criterion_types,
            $criterion->get_type()
          );
        ?>
        </td>

        <td>
          <div class="ss_criterion_options ss_criterion_options_date_range">
            <input class="date_picker"
              id="supersticky_criteria[0][date_range_from]"
              name="supersticky_criteria[0][date_range_from]" type="text" />
            and
            <input class="date_picker"
              id="supersticky_criteria[0][date_range_to]"
              name="supersticky_criteria[0][date_range_to]" type="text" />
          </div>

          <div class="ss_criterion_options ss_criterion_options_member_group">
          <?php
            $member_group = ($criterion->get_type() == Supersticky_criterion::TYPE_MEMBER_GROUP)
              ? $criterion->get_value()
              : '';

            echo form_dropdown(
              'supersticky_criteria[0][member_group]',
              $member_groups,
              $member_group
            );
          ?>
          </div>
        </td>

        <td class="act">
          <a class="remove_row btn" href="#">
            <img height="17" src="/themes/third_party/supersticky/img/minus.png" width="16">
          </a>
          <a class="add_row btn" href="#">
            <img height="17" src="/themes/third_party/supersticky/img/plus.png" width="16">
          </a>
        </td>
      </tr>

    <?php
      endforeach;
      else:
    ?>
      <tr class="row">
        <td width="30%">
        <?php
          echo form_dropdown('supersticky_criteria[0][type]', $criterion_types);
        ?>
        </td>

        <td>
          <div class="ss_criterion_options ss_criterion_options_date_range">
            <input class="date_picker"
              id="supersticky_criteria[0][date_range_from]"
              name="supersticky_criteria[0][date_range_from]" type="text" />
            and
            <input class="date_picker"
              id="supersticky_criteria[0][date_range_to]"
              name="supersticky_criteria[0][date_range_to]" type="text" />
          </div>

          <div class="ss_criterion_options ss_criterion_options_member_group">
          <?php
            echo form_dropdown(
              'supersticky_criteria[0][member_group]',
              $member_groups
            );
          ?>
          </div>
        </td>

        <td class="act">
          <a class="remove_row btn" href="#">
            <img height="17" src="/themes/third_party/supersticky/img/minus.png" width="16">
          </a>
          <a class="add_row btn" href="#">
            <img height="17" src="/themes/third_party/supersticky/img/plus.png" width="16">
          </a>
        </td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div><!-- /#supersticky_ft -->
