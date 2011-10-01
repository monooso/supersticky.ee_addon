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

        $this->_ee->supersticky_model = $this->_get_mock('model');

        $this->_model   = $this->_ee->supersticky_model;
        $this->_subject = new Supersticky_ft();
    }


    public function test__display_field__loads_view_correctly()
    {
      $data = FALSE;
      $view_result = '<p>Display something.</p>';

      $this->_ee->load->expectOnce('view', array('ft', array(), TRUE));
      $this->_ee->load->setReturnValue('view', $view_result);

      $this->assertIdentical(
        $view_result, $this->_subject->display_field($data));
    }


    public function test__post_save__populates_supersticky_entry_with_entry_id()
    {
      $entry_id = 111;

      $entry_a = new Supersticky_entry(array('entry_id' => $entry_id));
      $entry_b = clone $entry_a;

      $entry_b->add_criterion(new Supersticky_criterion(array(
        'type'  => Supersticky_criterion::TYPE_MEMBER_GROUP,
        'value' => 100
      )));

      // The entry ID is stored in the Fieldtype's public $settings array.
      $this->_subject->settings = array_merge(
        $this->_subject->settings,
        array('entry_id' => $entry_id)
      );

      $this->_model->expectOnce('update_supersticky_entry_with_post_data',
        array($entry_a));

      $this->_model->setReturnValue('update_supersticky_entry_with_post_data',
        $entry_b);

      $this->_model->expectOnce('save_supersticky_entry', array($entry_b));

      $this->_subject->post_save(array());
    }


    public function test__post_save__logs_error_if_no_entry_id()
    {
      unset($this->_subject->settings['entry_id']);

      $this->_model->expectNever('update_supersticky_entry_with_post_data');
      $this->_model->expectNever('save_supersticky_entry');

      // Log the error.
      $error_message = 'Epic fail!';
      $error_data = print_r($this->_subject->settings, TRUE);

      $this->_ee->lang->expectOnce('line',
        array('error__post_save_missing_entry_id'));

      $this->_ee->lang->setReturnValue('line', $error_message,
        array('error__post_save_missing_entry_id'));

      $this->_model->expectOnce('log_message',
        array($error_message, 3, array(), $error_data));

      $this->_subject->post_save(array());
    }


    public function test__post_save__logs_error_if_invalid_entry_id()
    {
      $this->_subject->settings = array_merge(
        $this->_subject->settings,
        array('entry_id' => 0)
      );

      $this->_model->expectNever('update_supersticky_entry_with_post_data');
      $this->_model->expectNever('save_supersticky_entry');

      // Log the error.
      $error_message = 'Oh noes!';
      $error_data = print_r($this->_subject->settings, TRUE);

      $this->_ee->lang->expectOnce('line',
        array('error__post_save_missing_entry_id'));

      $this->_ee->lang->setReturnValue('line', $error_message,
        array('error__post_save_missing_entry_id'));

      $this->_model->expectOnce('log_message',
        array($error_message, 3, array(), $error_data));

      $this->_subject->post_save(array());
    }


}


/* End of file      : test.ft_supersticky.php */
/* File location    : third_party/supersticky/tests/test.ft_supersticky.php */
