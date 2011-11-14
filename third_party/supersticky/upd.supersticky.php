<?php if ( ! defined('BASEPATH')) exit('Invalid file request.');

/**
 * SuperSticky module installer and updater.
 *
 * @author          Stephen Lewis (http://github.com/experience/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

class Supersticky_upd {
    
  private $EE;
  private $_model;
  public $version;
  
  
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
    $this->EE->load->add_package_path(PATH_THIRD .'supersticky/');

    $this->EE->load->model('supersticky_model');
    $this->_model = $this->EE->supersticky_model;
    
    $this->version = $this->_model->get_package_version();
  }
  
  
  /**
   * Installs the module.
   *
   * @access  public
   * @return  bool
   */
  public function install()
  {
    return $this->_model->install_module();
  }


  /**
   * Uninstalls the module.
   *
   * @access  public
   * @return  bool
   */
  public function uninstall()
  {
    return $this->_model->uninstall_module();
  }


  /**
   * Updates the module.
   *
   * @access  public
   * @param   string      $installed_version      The installed version.
   * @return  bool
   */
  public function update($installed_version = '')
  {
    return $this->_model->update_module($installed_version);
  }


}


/* End of file      : upd.supersticky.php */
/* File location    : third_party/supersticky/upd.supersticky.php */
