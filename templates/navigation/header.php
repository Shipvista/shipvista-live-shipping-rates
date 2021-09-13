<style>
    p.submit {
        display: none;
    }
</style>

<div class="sv_shadow-lg my-4">
    <div class="sv_container-fluid  sv_sv_text-dark sv_rounded-top" style="background: url('<?php echo  esc_html(SHIPVISTA__PLUGIN_URL . 'assets/img/bg2.jpg') ?>')no-repeat top !important;background-size:cover; background-blend-mode:opacity; width: 100%;">

        <div class=" sv_row sv_border-bottom sv_d-100 sv_p-3 sv_sv_py-4">
            <div class="sv_col-12 sv_col-md-4 sv_text-center  sv_mb-3 sv_mb-md-0">
                <img src="<?php echo  esc_attr(SHIPVISTA__PLUGIN_URL . 'assets/img/shipvista_logo.png') ?>" style="height: 80px;" class=" mr-2">
                <h1 class="sv_text-dark" style="font-size: 28px;">
                    Anyone can ship. Everyone can save.
                </h1>
                <p>Woocommerce Live Shipping Rates</p>
            </div>

            <div class="sv_col-12 sv_col-md-5 sv_border-right sv_mb-3 sv_mb-md-0">
                <ul class="sv_list-unstyled">
                    <li>♦ <b>Ship instantly</b> <small>Access to a free online system used to process shipments instantly.</small></li>
                    <li>♦ <b>Shipping label</b> <small>Generate carrier approved BOL and AWB shipping labels.</small></li>
                    <li>♦ <b>LTL shipments</b> <small>Schedule LTL shipments from anywhere to anywhere in North America including Trans-Border.</small></li>
                    <li>♦ <b>Shipment Tracking</b> <small>Claims processing and statement of account with invoice details.</small></li>
                    <li>♦ <b>Real time</b> <small>Real time, user friendly access to very competitive LTL and courier rates.</small></li>
                    <li>♦ <b>Save time and money</b> <small>Save your business time and money. Fast and efficient process.</small></li>
                </ul>
            </div>


            <div class="sv_col-12 sv_col-md-3  sv_mb-3 sv_mb-md-0 sv_d-none sv_d-mb-block sv_d-lg-block">
                <ul class="sv_list-unstyled ">
                    <li>Quick Links</li>
                    <li>♦ <b><a target="_blank" href="https://shipvista.com/about">About Us</a></b></small></li>
                    <li>♦ <b><a target="_blank" href="https://shipvista.com/open-account-offer">Bonus Offers</a></b></small></li>
                    <li>♦ <b><a target="_blank" href="https://shipvista.com/trackmyshipment">Track Shipment</a></b></small></li>
                    <li>♦ <b><a target="_blank" href="https://shipvista.com/contact">Contact Us</a></b></small></li>
                </ul>
            </div>






        </div>
    </div>

    <div class="sv_container-fluid" style="width: 100%;">

        <!-- content body -->
        <div class="">
            <div class="sv_row">
                <!-- side bar -->
                <div class="sv_col-12 sv_col-md-3 sv_col-lg-2 p-0" style="background: #cecece99">
                    <div class="my-2 p-2">
                        <div class="float-right" onclick="svToggleClass('whipvistanavigation_menu')"><i class="fa fa-th"></i></div> <b>Navigation</b>
                    </div>

                    <ul class="sv_nav sv_d-none sv_d-md-block sv_flex-column" id="whipvistanavigation_menu">

                        <li class="sv_nav-item">
                            <a class="sv_nav-link  <?php echo esc_html($this->activePage == 'connect' ? ' sv_bg-dark sv_text-white ' : ' sv_text-dark ') ?>" href="<?php echo esc_html($this->pageLink . '&wcs_page=connect') ?>">Home</a>
                        </li>

                        <li class="sv_nav-item">
                            <a class="sv_nav-link  <?php echo esc_html($this->activePage == 'carriers' ? ' sv_bg-dark sv_text-white ' : ' sv_text-dark ') ?>" href="<?php echo esc_html($this->pageLink . '&wcs_page=carriers') ?>">Carriers</a>
                        </li>
                        <li class="sv_nav-item d-none">
                            <a class="sv_nav-link  <?php echo esc_html($this->activePage == 'sipping' ? ' sv_bg-dark sv_text-white ' : ' sv_text-dark ') ?>" href="<?php echo esc_html($this->pageLink . '&wcs_page=shipping') ?>">Labels</a>
                        </li>
                        <li class="sv_nav-item">
                            <a class="sv_nav-link  <?php echo esc_html(($this->activePage == 'settings' && (!isset($_GET['wcs_setting']) ||  $_GET['wcs_setting'] != 'feedback')) ? ' sv_bg-dark sv_text-white ' : ' sv_text-dark ') ?> " href="<?php echo esc_html($this->pageLink . '&wcs_page=settings') ?>">Settings</a>
                        </li>
                        <li class="sv_nav-item">
                            <a class="sv_nav-link  <?php echo esc_html((isset($_GET['wcs_setting']) == 'wc' && $_GET['wcs_setting'] == 'feedback') ? ' sv_bg-dark sv_text-white ' : ' sv_text-dark ') ?> " href="<?php echo esc_html($this->pageLink .  '&wcs_page=settings&wcs_setting=feedback')  ?>">Send Feedback</a>
                        </li>

                    </ul>

                </div>
                <!-- side bar -->

                <!-- body content -->
                <div class="sv_col-12 sv_col-md-9 sv_col-lg-10 sv_py-4" style="background: #f8f9fa !important">