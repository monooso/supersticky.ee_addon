<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * SuperSticky module.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

class Supersticky {
    
    private $_ee;
    private $_model;
    public $return_data = '';
    
    
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
        $this->_ee =& get_instance();
        $this->_ee->load->model('supersticky_model');
        $this->_model = $this->_ee->supersticky_model;
    }
    
    
    /* --------------------------------------------------------------
     * ACTION METHODS
     * ------------------------------------------------------------ */
    
    /**
     * 
     *
     * @access  public
     * @return  void
     */
    public function ()
    {
        error_log('Running the  action.');
    }

    
    /* --------------------------------------------------------------
     * TEMPLATE TAG METHODS
     * ------------------------------------------------------------ */
    
    /**
     * SuperSticky "channel entries" tag pair.
     *
     * @access  public
     * @return  string
     */
    public function entries()
    {
        return $this->return_data = 'exp:supersticky:entries output';
    }

    
}


/* End of file      : mod.supersticky.php */
/* File location    : third_party/supersticky/mod.supersticky.php */
