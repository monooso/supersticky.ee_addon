<div id="supersticky_ft">

  <table border="0" cellpadding="0" cellspacing="0">
    <tbody class="roland">
      <tr class="row">
        <td width="30%">
          <select name="supersticky_criteria[0][type]">
            <option value="">Select a criterion type...</option>
            <option value="date_range">Date is between</option>
            <option value="member_group">Visitor's member group is</option>
          </select>
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
            <select name="supersticky_criteria[0][member_group]">
              <option value="0">Select a member group...</option>
              <option value="1">SuperAdmin</option>
              <option value="2">Pending</option>
              <option value="3">Banned</option>
              <option value="4">Members</option>
              <option value="5">Custom Member Group</option>
            </select>
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
    </tbody>
  </table>
</div><!-- /#supersticky_ft -->
