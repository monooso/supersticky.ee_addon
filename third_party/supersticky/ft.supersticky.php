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
    $this->EE->load->helper('form');

    $lang       = $this->EE->lang;
	  $theme_url	= $this->_model->get_package_theme_url();

    $this->EE->cp->add_to_foot('<script src="' .$theme_url
      .'js/libs/jquery.roland.js"></script>');

    $this->EE->cp->add_to_foot('<script src="' .$theme_url
      .'js/cp.js"></script>');

    $this->EE->cp->add_to_head('<link rel="stylesheet" href="'
      .$theme_url .'css/cp.css" />');

    if (($post_criteria = $this->EE->input->post('supersticky_criteria', TRUE))
      && is_array($post_criteria)
    )
    {
      /**
       * We're dealing with a failed save. This is typically called by a required 
       * field being left blank.
       */

      $entry = new Supersticky_entry();

      foreach ($post_criteria AS $post_criterion)
      {
        $criterion = new Supersticky_criterion(array(
          'date_from'     => date_create($post_criterion['date_from']),
          'date_to'       => date_create($post_criterion['date_to']),
          'member_groups' => $post_criterion['member_groups']
        ));

        $entry->add_criterion($criterion);
      }
    }
    else
    {
      // First time caller, or previously-saved entry?
      $entry = valid_int($saved_data, 1)
        ? $this->_model->get_supersticky_entry_by_id($saved_data)
        : FALSE;
    }

    $view_vars = array(
      'entry'         => $entry,
      'member_groups' => $this->_model->get_member_group_options()
    );

    return $this->EE->load->view('ft', $view_vars, TRUE);
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
