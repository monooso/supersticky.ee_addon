<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * SuperSticky publish page tab.
 *
 * @author          Stephen Lewis (http://experienceinternet.co.uk/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

require_once PATH_THIRD .'supersticky/classes/supersticky_entry.php';

class Supersticky_tab {

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
    $this->EE->load->model('supersticky_model');
    $this->_model = $this->EE->supersticky_model;
  }


  /**
   * Manipulate the submitted data after the main data entry has occurred.
   *
   * @access  public
   * @param   Array   $params   Associative array of entry data. Contains
   *                            four 'top-level' elements:
   *                            1. meta (Array).
   *                            2. data (Array).
   *                            3. mod_data (Array).
   *                            4. entry_id (Integer).
   * @return  void
   */
  public function publish_data_db(Array $params)
  {
    /**
     * It's all a matter of trust. For example, do we absolutely trust
     * that EllisLab will provide us with a valid entry ID, as documented?
     *
     * No, no we don't.
     */

    if ( ! isset($params['entry_id'])
      OR ! valid_int($params['entry_id'], 1)
    )
    {
      $this->_model->log_message(
        $this->EE->lang->line('error__publish_data_db_missing_entry_id'),
        3,
        array(),
        print_r($params, TRUE)
      );

      return;
    }

    $entry = new Supersticky_entry(array('entry_id' => $params['entry_id']));

    $this->_model->save_supersticky_entry(
      $this->_model->update_supersticky_entry_with_post_data($entry));
  }


  /**
   * Updates SuperSticky when an entry is deleted.
   *
   * @access  public
   * @param   Array       $params         Entry data.
   * @return  void
   */
  public function publish_data_delete_db(Array $params)
  {
    
  }


  /**
   * Creates the custom fields on the publish page.
   *
   * @access  public
   * @param   int|string      $channel_id     The channel ID.
   * @param   int|string      $entry_id       The entry ID.
   * @return  Array
   */
  public function publish_tabs($channel_id, $entry_id = FALSE)
  {
    $lang = $this->EE->lang;
    $lang->loadfile($this->_model->get_package_name());

    $settings[] = array(
      'field_data'            => $entry_id,   // Passed to the fieldtype.
      'field_fmt'             => '',
      'field_id'              => 'supersticky_criteria',
      'field_instructions'    => $lang->line('supersticky_field_instructions'),
      'field_label'           => $lang->line('supersticky_field_label'),
      'field_list_items'      => '',
      'field_pre_populate'    => 'n',
      'field_required'        => 'n',
      'field_show_fmt'        => 'n',
      'field_text_direction'  => 'ltr',
      'field_type'            => 'supersticky'
    );

    return $settings;
  }


  /**
   * Validates the SuperSticky data on submission.
   *
   * @access  public
   * @param   Array       $params         Entry data.
   * @return  bool
   */
  public function validate_publish(Array $params)
  {
    return FALSE;
  }


}


/* End of file      : tab.supersticky.php */
/* File location    : third_party/supersticky/tab.supersticky.php */
