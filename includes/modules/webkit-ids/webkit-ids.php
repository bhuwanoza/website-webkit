
<?php

class MWW_Webkit_Ids
{
    function __construct()
    {
        $this->mww_webkit_ids_init();
        $this->mww_init_hook();

    }


    function mww_init_hook()
    {
        add_action('enable_post_types',array($this,'mww_add_post_type_enabled'));
        add_action('admin_enqueue_scripts',array($this, 'webkit_ids_script_enqueue'));
    }
    function webkit_ids_script_enqueue(){
        wp_enqueue_style('website-webkit-style', esc_url(MWW()->plugin_url()) . '/assets/css/style.css');
        wp_enqueue_script('website-webkit-js', esc_url(MWW()->plugin_url()) . '/assets/js/website-webkit.js');

    }

    function mww_add_post_type_enabled()
    {

        if (isset($_POST) && isset($_POST['webkit_ids_nonce']) && isset($_POST['mww_all_types_list'])){
            if (wp_verify_nonce($_POST['webkit_ids_nonce'],'webkit-ids-nonce')) {
                $allTypes = $_POST['mww_all_types_list'];
                update_option('mww_enable_all_post_taxonomies_users_media_types', $allTypes);
                do_action('my-success-notice','Webkit id setting updated.');
            }else{
                do_action('my-error-notice','Nonce not verified.');
            }
        }

    }


    public function mww_webkit_ids_init()
    {

        $settings = mww_get_active_modules();
        $checkTypeEnabled = get_option('mww_enable_all_post_taxonomies_users_media_types');
        if ($checkTypeEnabled == null){
            $checkTypeEnabled = array();
        }
            // For Media Management
            if( is_array( $settings ) && in_array( 'webkit-ids', $settings ) && in_array('media',$checkTypeEnabled)) {

                add_action( 'manage_media_columns', array( $this, 'mww_webkit_ids_column' ) );
                add_filter( 'manage_media_custom_column', array( $this, 'mww_webkit_ids_value' ) , 10 , 3 );
            }
            // For Link Management
            add_action( 'manage_link_custom_column', array( $this, 'mww_webkit_ids_value' ), 10, 2 );
            add_filter( 'manage_link-manager_columns', array( $this, 'mww_webkit_ids_column' ) );

            // For Category Management
            add_action( 'manage_edit-link-categories_columns', array( $this, 'mww_webkit_ids_column' ) );
            add_filter( 'manage_link_categories_custom_column', array( $this, 'mww_webkit_ids_return_value' ), 10, 3 );

            // For Category, Tags and other custom taxonomies Management
            foreach( get_taxonomies() as $taxonomy ) {
                if( is_array( $settings ) && in_array( 'webkit-ids', $settings ) && in_array($taxonomy,$checkTypeEnabled) ) {
                    add_action( "manage_edit-${taxonomy}_columns" ,  array( $this, 'mww_webkit_ids_column' ) );
                    add_filter( "manage_${taxonomy}_custom_column" , array( $this, 'mww_webkit_ids_return_value' ) , 10 , 3 );
                    if( version_compare($GLOBALS['wp_version'], '3.0.999', '>') ) {
                        add_filter( "manage_edit-${taxonomy}_sortable_columns" , array( $this, 'mww_webkit_ids_column' ) );
                    }
                }
            }

            foreach( get_post_types() as $ptype ) {
                if( is_array( $settings ) && in_array( 'webkit-ids', $settings ) && in_array($ptype,$checkTypeEnabled)){
                    add_action( "manage_edit-${ptype}_columns" ,array( $this, 'mww_webkit_ids_column' ) );
                    add_action( "manage_${ptype}_posts_custom_column", array( $this, 'mww_webkit_ids_value' ), 10, 2 );
                    add_filter( "manage_${ptype}_posts_custom_column" , array( $this, 'mww_webkit_ids_value' ) , 10 , 3 );
                    if( version_compare($GLOBALS['wp_version'], '3.0.999', '>') ) {
                        add_filter( "manage_edit-${ptype}_sortable_columns" , array( $this, 'mww_webkit_ids_column' ) );
                    }
                }
            }

            // For User Management
            if( is_array( $settings ) && in_array( 'webkit-ids', $settings ) && in_array('users',$checkTypeEnabled)){
                add_action( 'manage_users_columns', array( $this, 'mww_webkit_ids_column' ) );
                add_action( 'manage_users_custom_column', array( $this, 'mww_webkit_ids_value' ), 10, 3 );
                add_filter( 'manage_users_custom_column', array( $this, 'mww_webkit_ids_return_value' ), 10, 3 );
                if( version_compare($GLOBALS['wp_version'], '3.0.999', '>') ) {
                    add_filter( "manage_users_sortable_columns" , array( $this, 'mww_webkit_ids_column' ) );
                }
            }

            // For Comment Management
            if( is_array( $settings ) && in_array( 'webkit-ids', $settings ) && in_array('comments',$checkTypeEnabled)){
                add_action( 'manage_edit-comments_columns', array( $this, 'mww_webkit_ids_column' ) );
                add_action( 'manage_comments_custom_column', array( $this, 'mww_webkit_ids_value' ), 10, 3 );
                if( version_compare($GLOBALS['wp_version'], '3.0.999', '>') ) {
                    add_filter( "manage_edit-comments_sortable_columns" , array( $this, 'mww_webkit_ids_column' ) );
                }
            }
        }


    function mww_webkit_ids_column($cols) {
        $column_id = array( 'mww_webkit_ids' => __( 'Id', 'website-webkit' ) );
        $cols      = array_slice( $cols, 0, 1, true ) + $column_id + array_slice( $cols, 1, NULL, true );
        return $cols;
    }
    function mww_webkit_ids_value($column_name, $id) {
        if ( 'mww_webkit_ids' == $column_name ) {
            echo $id;
        }
    }



    function mww_webkit_ids_return_value($value, $column_name, $id)
    {
        if ('mww_webkit_ids' == $column_name) {
            $value .= $id;
        }
        return $value;
    }


}


new MWW_Webkit_Ids();

