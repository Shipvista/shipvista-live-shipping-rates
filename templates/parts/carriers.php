<div class="sv_container">
    <div class="sv_py-3">
        <h4 class="sv_mb-3"><i class="fa fa-carriers"></i> Carriers</h4>
        <!-- set header -->

        <div class="sv_row">
            <div class="sv_col-12 sv_col-md-6">
                <div class="sv_card sv_p-0">
                    <div class="sv_card-header" style="line-height: 45px;">
                        <div class="sv_d-flex justify-content-middle">
                            <div class="sv_pr-2 sv_align-self-center">
                                <img src="<?php echo  SHIPVISTA__PLUGIN_URL . '/assets/img/canada_post.jpg' ?>" style="height: 45px;" class=" mr-2">
                            </div>
                            <div class="sv_align-self-center mt-4 flex-fill">
                                <div class="custom-control   custom-switch float-right">
                                    <input type="checkbox" onchange="shipvista_toggleCarrierOption('carrier_canada_post')" <?php echo ($this->get_option('carrier_canada_post_enabled') == 'yes' ? 'checked' : '') ?> class="custom-control-input" id="carrier_canada_post" name="carrier_canada_post">
                                    <label class="custom-control-label" for="carrier_canada_post"></label>
                                </div>
                                <!-- form  -->
                                <textarea id="<?php echo $this->fieldPrepend ?>carrier_canada_post" name="<?php echo $this->fieldPrepend ?>carrier_canada_post" value="<?php echo $this->get_option('carrier_canada_post') ?>" class="form-control sv_d-none"></textarea>
                                <input id="<?php echo $this->fieldPrepend ?>carrier_canada_post_enabled" name="<?php echo $this->fieldPrepend ?>carrier_canada_post_enabled" value="<?php echo $this->get_option('carrier_canada_post_enabled') ?>" class="form-control sv_d-none">
                                <!-- form -->
                                <h6 class="m-0">Canada post</h6>
                            </div>
                        </div>
                    </div>

                    <div class="sv_card-body">
                        <?php
                        foreach ($this->carrier_settings['carrier_canada_post'] as $key =>  $option) {
                            $option = (array) $option;
                        ?>
                            <!-- load settings script -->
                            <script>
                                sv_carrierSettings = <?php echo json_encode($this->carrier_settings); ?>;
                            </script>
                            <!-- load settings script -->
                            <!-- option -->
                            <div class="sv_d-flex sv_mb-2 w-100">
                                <div class="sv_flex-fill">
                                    <?php echo $option['name'] ?>
                                </div>
                                <div>
                                    <div class="custom-control custom-switch carrier_canada_post_options">
                                        <input type="checkbox" onchange="shipvista_carrierSelectOption('carrier_canada_post', '<?php echo $key ?>')" <?php echo ($option['checked'] == 1 ? 'checked' : '') ?> class="custom-control-input" data-shipvista-name="<?php echo $option['name'] ?>" id="<?php echo $key ?>" name="<?php echo $key ?>">
                                        <label class="custom-control-label" for="<?php echo $key ?>"> </label>
                                    </div>
                                </div>
                            </div>
                            <!-- option -->

                        <?php } ?>




                    </div>

                </div>

            </div>

        </div>



    </div>
    <div class="sv_text-center sv_my-3">
        <button type="button" onclick="sv_WooSave()" class="sv_btn sv_btn-primary"> Save Settings</button>
    </div>
</div>