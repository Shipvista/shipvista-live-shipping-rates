<?php

/**
 * 
 */
trait SLSR_ShipvistaRenderPage
{
    public $header = SHIPVISTA__PLUGIN_DIR . '/templates/navigation/header.php';
    public $footer = SHIPVISTA__PLUGIN_DIR . '/templates/navigation/footer.php';
    public $pageLink = ''; //  menu_page_url( SHIPVISTA__PLUGIN_SLUG, false ) .'&tab=shipping&section=shipvista';

    public function render($page, $dir = SHIPVISTA__PLUGIN_DIR . '/templates/parts/')
    {
        // check if file exist
        $link = $dir . $page;
        if (!file_exists($link)) {
            // load error home page
            $this->content['Error'] = 'Sorry the option you navigated to does not exist.';
            $link = $dir . 'index.php';
        }

        require_once $this->header;
        require_once $link;
        require_once $this->footer;
    }
}
