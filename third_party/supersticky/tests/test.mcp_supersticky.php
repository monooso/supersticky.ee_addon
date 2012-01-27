<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * SuperSticky module control panel tests.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/mcp.supersticky.php';
require_once PATH_THIRD .'supersticky/models/supersticky_model.php';

class Test_supersticky_mcp extends Testee_unit_test_case {
    
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
    $this->_subject = new Supersticky_mcp();
  }
  

}


/* End of file      : test.mcp_supersticky.php */
/* File location    : third_party/supersticky/tests/test.mcp_supersticky.php */
