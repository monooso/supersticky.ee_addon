<?php if ( ! defined('BASEPATH')) exit('Direct script access not permitted.');

/**
 * SuperSticky fieldtype.
 *
 * @author          Stephen Lewis (http://experienceinternet.co.uk/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/classes/supersticky_entry.php';

class Supersticky_ft extends EE_Fieldtype {

  private $_model;

  /**
   * Annoyingly, this can't be set in the constructor, which means
   * hard-coding the Fieldtype name and version (as opposed to pulling
   * the information from a language file and the model, respectively).
   *
   * Bad EllisLab, no biscuit.
   */

  public $info = array(
    'name'    => 'SuperSticky',
    'version' => '0.1.0'
  );


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
    parent::__construct();

    $this->EE->load->model('supersticky_model');
    $this->_model = $this->EE->supersticky_model;
  }


  /**
   * Displays the SuperSticky fieldtype on the Publish page.
   *
   * @access  public
   * @param   mixed    $saved_data    Previously-saved field data.
   * @return  string
   */
  public function display_field($saved_data)
  {
    $this->EE->load->library('table');

	  $theme_url	= $this->_model->get_package_theme_url();

    $this->EE->cp->add_to_foot('<script src="' .$theme_url
      .'js/libs/jquery.roland.js"></script>');

    $this->EE->cp->add_to_foot('<script src="' .$theme_url
      .'js/cp.js"></script>');

    $this->EE->cp->add_to_head('<link rel="stylesheet" href="'
      .$theme_url .'css/cp.css" />');

    return $this->EE->load->view('ft', array(), TRUE);
  }


  /**
   * Renders the SuperSticky fieldtype template tag. Doesn't really make a
   * whole lot of sense to use this, but it could be useful for general
   * debugging purposes, so we play along.
   *
   * @access  public
   * @param   mixed     $saved_data     The field data.
   * @param   Array     $tag_params     An array of tag parameters.
   * @param   string    $tagdata        Only applies to tag pairs (not used).
   * @return  string
   */
  public function replace_tag(
    $saved_data,
    Array $tag_params = array(),
    $tagdata = ''
  )
  {
    return '<p>SuperSticky saved data.</p>';
  }


}


/* End of file      : ft.supersticky.php */
/* File location    : third_party/supersticky/ft.supersticky.php */
