<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * SuperSticky tab tests.
 *
 * @author          Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/tab.supersticky.php';
require_once PATH_THIRD .'supersticky/tests/mocks/mock.supersticky_model.php';

class Test_supersticky_tab extends Testee_unit_test_case {

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
        $this->_subject = new Supersticky_tab();
    }


    public function test__publish_data_db__populates_supersticky_entry_with_entry_id()
    {
      $entry_id = 111;

      $entry_a = new Supersticky_entry(array('entry_id' => $entry_id));
      $entry_b = clone $entry_a;

      $entry_b->add_criterion(new Supersticky_criterion(array(
        'date_from'     => new DateTime('1935-06-14'),
        'date_to'       => new DateTime('1944-02-18'),
        'member_groups' => array(10, 20, 30)
      )));

      $params = array(
        'data'      => array(),
        'entry_id'  => $entry_id,
        'meta'      => array(),
        'mod_data'  => array()
      );

      $this->_model->expectOnce('update_supersticky_entry_with_post_data',
        array($entry_a));

      $this->_model->setReturnValue('update_supersticky_entry_with_post_data',
        $entry_b);

      $this->_model->expectOnce('save_supersticky_entry', array($entry_b));

      $this->_subject->publish_data_db($params);
    }


    public function test__publish_data_db__logs_error_if_no_entry_id()
    {
      $params = array(
        'data'      => array(),
        'meta'      => array(),
        'mod_data'  => array()
      );

      $this->_model->expectNever('update_supersticky_entry_with_post_data');
      $this->_model->expectNever('save_supersticky_entry');

      // Log the error.
      $error_message = 'Epic fail!';
      $error_data = print_r($params, TRUE);

      $this->EE->lang->expectOnce('line',
        array('error__publish_data_db_missing_entry_id'));

      $this->EE->lang->setReturnValue('line', $error_message,
        array('error__publish_data_db_missing_entry_id'));

      $this->_model->expectOnce('log_message',
        array($error_message, 3, array(), $error_data));

      $this->_subject->publish_data_db($params);
    }


    public function test__publish_data_db__logs_error_if_invalid_entry_id()
    {
      $params = array(
        'data'      => array(),
        'entry_id'  => 0,
        'meta'      => array(),
        'mod_data'  => array()
      );

      $this->_model->expectNever('update_supersticky_entry_with_post_data');
      $this->_model->expectNever('save_supersticky_entry');

      // Log the error.
      $error_message = 'Oh noes!';
      $error_data = print_r($params, TRUE);

      $this->EE->lang->expectOnce('line',
        array('error__publish_data_db_missing_entry_id'));

      $this->EE->lang->setReturnValue('line', $error_message,
        array('error__publish_data_db_missing_entry_id'));

      $this->_model->expectOnce('log_message',
        array($error_message, 3, array(), $error_data));

      $this->_subject->publish_data_db($params);
    }


    public function test__publish_tabs__works_without_saved_data()
    {
      $lang = $this->EE->lang;

      $channel_id         = 10;
      $field_instructions = 'Field instructions.';
      $field_label        = 'Field Label';

      $lang->setReturnValue('line', $field_instructions,
        array('supersticky_field_instructions'));

      $lang->setReturnValue('line', $field_label,
        array('supersticky_field_label'));

      $expected_result = array(
        array(
          'field_data'          => FALSE,
          'field_fmt'           => '',
          'field_id'            => 'supersticky_criteria',
          'field_instructions'  => $field_instructions,
          'field_label'         => $field_label,
          'field_list_items'    => '',
          'field_pre_populate'  => 'n',
          'field_required'      => 'n',
          'field_show_fmt'      => 'n',
          'field_text_direction' => 'ltr',
          'field_type'          => 'supersticky'
        )
      );
    
      $actual_result = $this->_subject->publish_tabs($channel_id);

      ksort($actual_result);
      ksort($expected_result);

      $this->assertIdentical($expected_result, $actual_result);
    }


    public function test__publish_tabs__works_with_saved_data()
    {
      $lang = $this->EE->lang;

      $channel_id         = 10;
      $entry_id           = 99;
      $field_instructions = 'Field instructions.';
      $field_label        = 'Field Label';

      $lang->expectCallCount('line', 2);

      $lang->setReturnValue('line', $field_instructions,
        array('supersticky_field_instructions'));

      $lang->setReturnValue('line', $field_label,
        array('supersticky_field_label'));

      $expected_result = array(
        array(
          'field_data'          => $entry_id,
          'field_fmt'           => '',
          'field_id'            => 'supersticky_criteria',
          'field_instructions'  => $field_instructions,
          'field_label'         => $field_label,
          'field_list_items'    => '',
          'field_pre_populate'  => 'n',
          'field_required'      => 'n',
          'field_show_fmt'      => 'n',
          'field_text_direction' => 'ltr',
          'field_type'          => 'supersticky'
        )
      );
    
      $actual_result = $this->_subject->publish_tabs($channel_id, $entry_id);

      ksort($actual_result);
      ksort($expected_result);

      $this->assertIdentical($expected_result, $actual_result);
    }


}


/* End of file      : test.tab_supersticky.php */
/* File location    : third_party/supersticky/tests/test.tab_supersticky.php */
