<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * SuperSticky fieldtype tests.
 *
 * @author          Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright       Experience Internet
 * @package         Supersticky
 */

// TRICKY: Need to manually require the EE_Fieldtype class.
require_once PATH_FT .'EE_Fieldtype.php';
require_once PATH_THIRD .'supersticky/ft.supersticky.php';
require_once PATH_THIRD .'supersticky/tests/mocks/mock.supersticky_model.php';

class Test_supersticky_ft extends Testee_unit_test_case {

  private $_model;
  private $_subject;


  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */

  /**
   * Constructor.
   *
   * @access  public
   * @return  void
   */
  public function setUp()
  {
    parent::setUp();

    Mock::generate('Mock_supersticky_model',
      get_class($this) .'_mock_model');

    $this->EE->supersticky_model = $this->_get_mock('model');
    $this->_model   = $this->EE->supersticky_model;
    $this->_subject = new Supersticky_ft();
  }


  public function test__display_field__works_without_saved_entry_id()
  {
    $data = FALSE;
    $view_result = '<p>Display something.</p>';

    // Build the view variables.
    $member_groups = array('d' => 'D', 'e' => 'E', 'f' => 'F');

    $this->_model->expectOnce('get_member_group_options');
    $this->_model->setReturnValue('get_member_group_options', $member_groups);

    $this->_model->expectNever('get_supersticky_entry_by_id');

    $view_vars = array(
      'entry'         => FALSE,
      'member_groups' => $member_groups
    );

    $this->EE->load->expectOnce('view', array('ft', $view_vars, TRUE));
    $this->EE->load->setReturnValue('view', $view_result);

    $this->assertIdentical(
      $view_result, $this->_subject->display_field($data));
  }


  public function test__display_field__works_with_saved_entry_id()
  {
    $data = 101;
    $view_result = '<p>Display something.</p>';

    // Build the view variables.
    $entry = new Supersticky_entry(array('entry_id' => $data));
    $member_groups = array('d' => 'D', 'e' => 'E', 'f' => 'F');

    $this->_model->expectOnce('get_member_group_options');
    $this->_model->setReturnValue('get_member_group_options', $member_groups);

    $this->_model->expectOnce('get_supersticky_entry_by_id', array($data));
    $this->_model->setReturnValue('get_supersticky_entry_by_id', $entry);

    $view_vars = array(
      'entry'         => $entry,
      'member_groups' => $member_groups
    );

    $this->EE->load->expectOnce('view', array('ft', $view_vars, TRUE));
    $this->EE->load->setReturnValue('view', $view_result);

    $this->assertIdentical(
      $view_result, $this->_subject->display_field($data));
  }


  public function test__display_field__restores_field_settings_after_failed_save()
  {
    $data = array(
      'entry_id'          => '0',
      'channel_id'        => '2',
      'autosave_entry_id' => '0',
      'filter'            => '',
      'layout_preview'    => '1',
      'title'             => 'My Lovely Title',
      'url_title'         => 'my_lovely_title',
      'entry_date'        => '2012-01-27 02:36 PM',
      'expiration_date'   => '',
      'new_channel'       => '2',
      'status'            => 'open',
      'author'            => '1',
      'field_id_3'        => '',
      'supersticky_criteria' => array(
        array(
          'member_groups' => array('1'),
          'date_from'     => '2012-02-01',
          'date_to'       => '2012-04-01'
        ),
        array(
          'member_groups' => array('2', '3', '4'),
          'date_from'     => '2012-03-01',
          'date_to'       => '2012-04-01'
        )
      ),
      'submit' => 'Submit'
    );

    $view_result  = '<p>Display something.</p>';
    $entry        = new Supersticky_entry();

    // Check for submitted data.
    $this->EE->input->expectOnce('post', array('supersticky_criteria', TRUE));
    $this->EE->input->setReturnValue('post', $data['supersticky_criteria'],
      array('supersticky_criteria', TRUE));

    // Build the view variables.
    foreach ($data['supersticky_criteria'] AS $criterion_data)
    {
      $criterion = new Supersticky_criterion(array(
        'date_from'     => date_create($criterion_data['date_from']),
        'date_to'       => date_create($criterion_data['date_to']),
        'member_groups' => $criterion_data['member_groups']
      ));

      $entry->add_criterion($criterion);
    }

    // Retrieve the member group.
    $member_groups = array('d' => 'D', 'e' => 'E', 'f' => 'F');

    $this->_model->expectOnce('get_member_group_options');
    $this->_model->setReturnValue('get_member_group_options', $member_groups);

    // Don't attempt to retrieve any saved entries.
    $this->_model->expectNever('get_supersticky_entry_by_id');

    $view_vars = array(
      'entry'         => $entry,
      'member_groups' => $member_groups
    );

    $this->EE->load->expectOnce('view', array('ft', $view_vars, TRUE));
    $this->EE->load->setReturnValue('view', $view_result);

    $this->assertIdentical(
      $view_result, $this->_subject->display_field($data));
  }


}


/* End of file      : test.ft_supersticky.php */
/* File location    : third_party/supersticky/tests/test.ft_supersticky.php */
