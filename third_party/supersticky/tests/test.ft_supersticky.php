<?php if ( ! defined('EXT')) exit('Invalid file request.');

/**
 * SuperSticky fieldtype tests.
 *
 * @author          Stephen Lewis <stephen@experienceinternet.co.uk>
 * @copyright       Experience Internet
 * @package         Supersticky
 */

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


}


/* End of file      : test.ft_supersticky.php */
/* File location    : third_party/supersticky/tests/test.ft_supersticky.php */
