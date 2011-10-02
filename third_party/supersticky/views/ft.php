<div id="supersticky_ft">
  <table class="mainTable" cellpadding="0" cellspacing="0">
    <thead>
      <tr>
        <th>Make Entry Sticky For</th>
        <th width="25%">From</th>
        <th width="25%">To</th>
        <th width="44px">&nbsp;</th>
      </tr>
    </thead>

    <tbody class="criterion_roland">
      <tr class="criterion_row">
        <td>

          <table cellspacing="0" cellpadding="0" class="member_groups">
            <tbody class="member_group_roland">
              <tr class="member_group_row">
                <td>
                  <?php echo form_dropdown(
                      'supersticky_criteria[0][member_group][]',
                      $member_groups
                    );
                  ?>
                </td>
                <td class="act">
                  <a class="member_group_remove_row btn" href="#">
                    <img height="17" src="/themes/third_party/supersticky/img/minus.png" width="16">
                  </a>
                  <a class="member_group_add_row btn" href="#">
                    <img height="17" src="/themes/third_party/supersticky/img/plus.png" width="16">
                  </a>
                </td>
              </tr>
            </tbody>
          </table>

        </td>
        
        <td>
            <input class="date_picker"
              id="supersticky_criteria[0][date_from]"
              name="supersticky_criteria[0][date_from]" type="text" />
        </td>

        <td>
            <input class="date_picker"
              id="supersticky_criteria[0][date_to]"
              name="supersticky_criteria[0][date_to]" type="text" />
        </td>

        <td class="act">
          <a class="criterion_remove_row btn" href="#">
            <img height="17" src="/themes/third_party/supersticky/img/minus.png" width="16">
          </a>
          <a class="criterion_add_row btn" href="#">
            <img height="17" src="/themes/third_party/supersticky/img/plus.png" width="16">
          </a>
        </td>
      </tr>
    </tbody>
  </table>

</div><!-- /#supersticky_ft -->
