<?php if ( ! defined('BASEPATH')) exit('Direct script access not permitted.');

/**
 * SuperSticky fieldtype.
 *
 * @author          Stephen Lewis (http://experienceinternet.co.uk/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

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
    return '<p>SuperSticky Fieldtype</p>';
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