<?php

class MWW_Webkit_Widgets
{


    function __construct()
    {
        add_action('widgets_init',array($this,'mww_register_widget'));

    }

    function mww_register_widget()
    {
        require_once 'widget-functions.php';
        require_once 'widget-block.php';
        register_widget('MWW_Widget_Block');
    }


}

new MWW_Webkit_Widgets();
