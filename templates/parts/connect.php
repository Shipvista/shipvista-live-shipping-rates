<style>
    label[for="woocommerce_shipvista_enabled"] {
        display: none !important;
    }

    fieldset>label[for="woocommerce_shipvista_enabled"] {
        display: block !important;
    }
</style>

<div class="sv_container">
    <div class="sv_row">

        <?php if ($this->content['postcode'] == '') { ?>
            <!-- sv_alert missing shipper -->
            <div class="sv_col-12 sv_mb-3">
                <div class="sv_alert sv_alert-warning">
                    <a class="float-right" href="<?php echo $this->pageLink . '&wcs_page=settings&wcs_setting=shipper' ?>">Edit Address</a>
                    Please update your Shippers address to display live rates.
                </div>

            </div>
            <!-- sv_alert missing shipper -->
        <?php } ?>

        <?php if (count($this->content['user']) > 0) { ?>
            <!-- <div class="sv_col-12 sv_mb-1 sv_pt-2">
                <h4 class="sv_text-dark"><span class="fa fa-user"></span> My account </h4>
            </div> -->

            <div class="sv_col-12 sv_col-md-6 sv_mb-3 sv_align-self-center sv_border-right">

                <div class="sv_alert sv_alert-warning sv_mb-3">
                    <div class="sv_card-body">
                        <div class="sv_media">
                            <img src="<?php echo SHIPVISTA__PLUGIN_URL; ?>assets/img/yellow_alert.png" style="height: 50px;width:50px;" class="sv_mr-3" />
                            <div class="sv_media-body">
                                <h5>Notice</h5>
                                <p class="sv_m-0">
                                    This plugin is made by shipvista.com on behalf of our mutual customers. This plugin is currently in BETA stage (early release) and has been tested with over 10 different themes. If you experience any problems using this plugin, please Send a detailed <a href="<?php echo $this->pageLink . '&wcs_page=settings&wcs_setting=feedback'  ?>"> <b>Feedback</b> </a> to let us know what the issue is so we can fix it immediately. We normally reply within 24 to 72 hours.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="py-3">
                    <div class="sv_mb-2">
                        <h5>Quick Setup</h5>
                        <small>Please complete the following steps to get started.</small>
                    </div>
                    <div class="sv_row">
                        <div class="sv_col-12 sv_col-md-6">
                            <div class="sv_card">
                                <div class="sv_card-body">
                                    <h5 style="font-size:12px;"><b>SETUP CARRIERS</b></h5>
                                    <p>Turn on carriers and select shipping methods</p>
                                    <div>
                                        <div class="sv_float-right"><a href="<?php echo $this->pageLink . '&wcs_page=carriers' ?>"> <small> <i class="dashicons dashicons-arrow-right-alt"></i></small> </a></div>
                                        <?php echo ($this->get_option('carrier_canada_post_enabled') == 'yes' ?  '<img src="' . SHIPVISTA__PLUGIN_URL . 'assets/img/check_on.png" style="height:15px;width:15px;"> <small class="sv_text-success">Active</small>' : '<img src="' . SHIPVISTA__PLUGIN_URL . 'assets/img/check_off.png" style="height:15px;width:15px;"> <small class="sv_text-grey">Pending</small>') ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sv_col-12 sv_col-md-6">
                            <div class="sv_card">
                                <div class="sv_card-body">
                                    <h5 style="font-size:12px;"><b>SHIPPER SETTINGS</b></h5>
                                    <p>Setup your shipping from address to use to obtain rates.</p>
                                    <div>
                                        <div class="sv_float-right"><a href="<?php echo $this->pageLink . '&wcs_page=settings&wcs_setting=shipper' ?>"> <small> <i class="dashicons dashicons-arrow-right-alt"></i></small> </a></div>
                                        <?php echo ($this->content['postcode'] == '' ? '<img src="' . SHIPVISTA__PLUGIN_URL . 'assets/img/check_off.png" style="height:15px;width:15px;"> <small class="sv_text-grey">Pending</small>' : '<img src="' . SHIPVISTA__PLUGIN_URL . 'assets/img/check_on.png" style="height:15px;width:15px;"> <small class="sv_text-success">Active</small>') ?>
                                    </div>
                                </div>
                            </div>
                        </div>




                    </div>
                </div>


            </div>

            <!-- list account details -->
            <div class="sv_col-12 sv_col-md-6 sv_align-self-center">
                <!-- <div class="table-responsive sv_mb-3">
                    <table class="table table-borderless">
                        <tbody>

                            <tr class="sv_border-0">
                                <td><i class="fa fa-money"></i> Account Balance </td>
                                <td><b><?php echo $this->content['user']['balance'] ?></b></td>
                            </tr>


                            <tr class="sv_border-0">
                                <td><i class="fa fa-envelope"></i> Email </td>
                                <td><?php echo $this->content['user']['email'] ?></td>
                            </tr>


                            <tr>
                                <td><span class="dashicons dashicons-shield"></span> Api Token</td>
                                <td><?php echo $this->content['user']['token'] ?></td>
                            </tr>

                            <tr>
                                <td><span class="dashicons dashicons-clock"></span>Token Refresh Date</td>
                                <td><?php echo $this->content['user']['expires'] ?></td>
                            </tr>
                        </tbody>
                    </table>

                </div> -->

                <div class="sv_p-3 shadow-none sv_border-0 sv_text-center">
                    <div class="sv_card-body sv_mb-3">
                        <img src="<?php echo  SHIPVISTA__PLUGIN_URL . 'assets/img/avatar.png' ?>" alt="<?php echo $this->content['user']['name'] ?> " style="height: 150px;" class="sv_mb-2">
                        <h3><?php echo $this->content['user']['name'] ?></h3>
                        <p>You are successfully connected to your shipvista account.</p>

                    </div>
                    <div class="sv_py-2">
                        <fieldset class="sv_d-none">
                            <legend class="screen-reader-text"><span>Shipvista shipping</span></legend>
                            <label for="woocommerce_shipvista_enabled">
                                <input class="" type="checkbox" name="woocommerce_shipvista_enabled" id="woocommerce_shipvista_enabled" style="" value="1" <?php echo ($this->content['enabled'] ? 'checked' : '') ?>> Enable shipping</label><br>
                        </fieldset>
                        <div class="sv_py-2  sv_text-center  sv_col-12 w-100">
                            <button class="sv_btn sv_btn-danger sv_btn-sm" onclick="shipvista_unlinkAccount()" type="button"><span class="dashicons dashicons-admin-users"></span> Unlink Account</button>
                        </div>

                    </div>

                    <?php if ($this->content['enabled'] != 'yes') { ?>
                        <div class="sv_alert sv_alert-danger shadow-sm">
                            <i class="fas fa-exclamation-tiangle"></i> Shipvista is not activated, you will not be able to get live shipping rates. Enable shipvista to continue using our live rates and lable printing from wordpres.
                        </div>
                    <?php } ?>
                </div>



            </div>
            <!-- list account details -->

            <!-- add action link script -->
            <script>
                sv_action_link = "<?php echo  $this->pageLink . '&wcs_action=' ?>";
            </script>
            <!-- add action link script -->


        <?php } else { ?>
            <div class="sv_col-12 sv_mb-1 sv_pt-2">
                <h4><i class="fa fa-link"></i> Link your Shipvista account. </h4>
            </div>

            <div class="sv_col-12 sv_col-md-6 sv_border-right sv_py-0 sv_mb-3" id="_loginFormCont">
                <div class="sv_py-3 sv_border-0 shadow-none">
                    <div class="sv_card-body sv_pt-0">
                        <!-- hidden options form -->
                        <?php echo $this->content['form'] ?>
                        <!-- hidden options form -->
                        <div style="margin-top: -145px;">
                            <div style="font-size:12px;line-height:12px;" class="sv_mb-3">
                                By clicking "Connect Store", you agree to our Terms of Service and to share certain data and settings with WordPress.com and/or third parties.
                            </div>

                            <div class="sv_mb-3">
                                <button class="sv_btn sv_btn-block sv_btn-success" type="button" onclick="shipvista_ConnectStore()"><i class="fa fa-refresh" aria-hidden="true"></i> CONNECT STORE</button>
                            </div>
                        </div>


                    </div>
                </div>
            </div>

            <div class="sv_col-12 sv_col-md-6 sv_align-self-center sv_mb-3">
                <div class=" sv_border-0 sv_pt-0 shadow-none sv_text-center">

                    <div class="sv_card-body  <?php echo  esc_attr((!empty($this->content['signup_email']) ? '' : 'sv_d-none')); ?>" id="_verifyCont">
                        <div class="sv_text-center" id="_createAccountPending">
                            <?php if ($this->content['signup_verify'] == true) {  ?>
                                <div class="sv_display-4 sv_pt-0"><i class="fa fa-user"></i></div>
                                <h4>Congratulations!!</h4>
                                <p class="sv_border-bottom sv_py-3">Hi <b id="_verifyName"> <?php echo $this->content['signup_names'] ?></b>, your account has been verified successfully. Please login to start getting live rates for your customers.</b> </p>
                        </div>

                        <!-- add action link script -->
                        <script>
                            setCookie('shipvista_user_email', '', -1);
                            setCookie('shipvista_user_names', '', -1);
                        </script>
                        <!-- add action link script -->
                    <?php } else { ?>
                        <div class="sv_display-4 sv_pt-0"><i class="fa fa-user-times"></i></div>
                        <h4>Verify Account</h4>
                        <p class="sv_border-bottom sv_py-3">Hi <b id="_verifyName"> <?php echo esc_html($this->content['signup_names']) ?></b>, To verify your account, click on the verification link sent to your email address <b id="_verifyEmail"> <?php echo esc_html($this->content['signup_email']); ?></b> </p>
                        <p><small>Not your email? Signup with a different email address.</small></p>
                        <div class="sv_mt-3"><a class="sv_btn sv_btn-danger sv_text-white" onclick="changeSignupDetails()">Sign Up?</a></div>
                    </div>
                <?php } ?>
                </div>


                <div class="sv_card-body sv_pt-0 <?php echo  esc_attr((empty($this->content['signup_email']) ? '' : 'sv_d-none')); ?> " id="_signupCont">
                    <div class="sv_display-4 sv_pt-0"><i class="fa fa-user"></i></div>
                    <h4>Create Account</h4>
                    <p id="_createAccount">Don't have a shipvista account?
                        Signup today for a free account in less than 1 minute.
                    </p>




                    <div class="sv_py-2 sv_text-left sv_d-none sv_mb-2" id="_setupAccount">
                        <input type="hidden" class="sv_form-control" id="woocommerce_shipvista_shipvista_create_user_link" value="<?php echo esc_attr($this->content['verificationLink']); ?>" name="woocommerce_shipvista_shipvista_create_user_link">
                        <div class="sv_mb-3">
                            <label for="">Names</label>
                            <input type="text" class="sv_form-control" id="woocommerce_shipvista_shipvista_create_user_names" placeholder="Enter full names." name="woocommerce_shipvista_shipvista_create_user_names">
                        </div>
                        <div class="sv_row">
                            <div class="sv_col-12 sv_col-md-6 sv_mb-3">
                                <label for="">Email address</label>
                                <input type="email" class="sv_form-control" id="woocommerce_shipvista_shipvista_create_user_email" placeholder="Enter email address." name="woocommerce_shipvista_shipvista_create_user_email">
                            </div>

                            <div class="sv_col-12 sv_col-md-6 sv_mb-3">
                                <label for="">Phone Number</label>
                                <input type="tel" class="sv_form-control" id="woocommerce_shipvista_shipvista_create_user_phone" placeholder="Enter phone number." name="woocommerce_shipvista_shipvista_create_user_phone">
                            </div>

                            <div class="sv_col-12 sv_mb-3">
                                <label for="">Password</label>
                                <input type="password" class="sv_form-control" id="woocommerce_shipvista_shipvista_create_user_password" placeholder="Enter password." name="woocommerce_shipvista_shipvista_create_user_password">
                                <small><i class="fa fa-exclamation-sign"></i> Create a password of atleast 8 characters.</small>
                            </div>

                            <div class="sv_col-12 sv_mb-2 sv_d-none">
                                <!-- <a class="sv_btn sv_btn-block sv_btn-success" href="https://shipvista.com/registration" target="_blank" type="link">CREATE ACCOUNT</button> -->
                            </div>
                        </div>
                    </div>
                    <a href="https://shipvista.com/registration" target="_blank" class="sv_btn sv_btn-primary"> REGISTER NOW</a>
                </div>
            </div>
    </div>

<?php } ?>
</div>
</div>