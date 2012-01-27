<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * SuperSticky module update tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/upd.supersticky.php';

class Test_supersticky_upd extends Testee_unit_test_case {

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

        Mock::generate('Supersticky_model', get_class($this) .'_mock_model');
        $this->EE->supersticky_model = $this->_get_mock('model');

        $this->_model   = $this->EE->supersticky_model;
        $this->_subject = new Supersticky_upd();
    }


    public function test__install__success()
    {
        $this->_model->expectOnce('install_module');
        $this->_model->setReturnValue('install_module', 'wibble');  // Should just be passed along.
        $this->assertIdentical('wibble', $this->_subject->install());
    }


    public function test__uninstall__success()
    {
        $this->_model->expectOnce('uninstall_module');
        $this->_model->setReturnValue('uninstall_module', 'wibble');  // Should just be passed along.
        $this->assertIdentical('wibble', $this->_subject->uninstall());
    }


    public function test__update__success()
    {
        $installed_version  = '1.0.0';
        $return_value       = 'Huzzah!';        // Should just be passed along.

        $this->_model->expectOnce('update_module', array($installed_version));
        $this->_model->setReturnValue('update_module', $return_value);
        $this->assertIdentical($return_value, $this->_subject->update($installed_version));
    }


}


/* End of file      : test.upd_supersticky.php */
/* File location    : third_party/supersticky/tests/test.upd_supersticky.php */
