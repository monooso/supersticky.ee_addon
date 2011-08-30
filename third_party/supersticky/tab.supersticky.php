<?php if ( ! defined('BASEPATH')) exit('Direct script access is not permitted.');

/**
 * SuperSticky publish page tab.
 *
 * @author          Stephen Lewis (http://experienceinternet.co.uk/)
 * @copyright       Experience Internet
 * @package         Supersticky
 */

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
     * @param   Array       $params         Entry data.
     * @return  void
     */
    public function publish_data_db(Array $params)
    {
        
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
        $settings[] = array(
            'field_data'            => '',
            'field_fmt'             => '',
            'field_id'              => 'supersticky_criteria',
            'field_instructions'    => 'SuperSticky instructions.',
            'field_label'           => 'SuperSticky',
            'field_list_items'      => '',
            'field_pre_populate'    => 'n',
            'field_required'        => 'n',
            'field_show_fmt'        => 'n',
            'field_text_direction'  => 'ltr',
            'field_type'            => 'multi_select'
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
