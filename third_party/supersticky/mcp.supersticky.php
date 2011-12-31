<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * SuperSticky module control panel.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

class Supersticky_mcp {

  private $EE;
  
  
  /* --------------------------------------------------------------
   * PUBLIC METHODS
   * ------------------------------------------------------------ */
  
  /**
   * Constructor.
   *
   * @access  public
   * @return  void
   */
  public function __construct()
  {
    $this->EE =& get_instance();
  }


}


/* End of file      : mcp.supersticky.php */
/* File location    : third_party/supersticky/mcp.supersticky.php */
