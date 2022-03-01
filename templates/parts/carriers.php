<div class="sv_container">
    <div class="sv_py-3">
        <h4 class="sv_mb-3"><i class="fa fa-carriers"></i> Carriers</h4>
        <!-- set header -->

        <div class="sv_row">


            <!-- display list of all carriers -->
            <script>
                var sv_carrierSettings = <?php echo json_encode($this->carrier_settings); ?>;
                var sv_carriers = <?php echo json_encode($this->carriers); ?>;
            </script>

            <?php

            foreach ($this->carriers as $carrier => $list) { ?>

                <div class="sv_col-12 sv_col-md-6">
                    <div class="sv_card sv_p-0">
                        <div class="sv_card-header" style="line-height: 45px;">
                            <div class="sv_d-flex justify-content-middle">
                                <div class="sv_pr-2 sv_align-self-center">
                                    <img src="<?php echo  esc_html($this->carrierDetails[$carrier]['image']) ?>" style="height: 45px;" class=" mr-2">
                                </div>

                                <div class="sv_align-self-center mt-4 flex-fill">
                                    <div class="custom-control   custom-switch float-right">
                                        <input type="checkbox" onchange="shipvista_toggleCarrierOption('<?php echo $carrier; ?>')" <?php echo ($this->get_option( $carrier .'_enabled') == 'yes' ? 'checked' : '') ?> class="custom-control-input" id="<?php echo $carrier; ?>" name="<?php echo $carrier; ?>">
                                        <label class="custom-control-label" for="<?php echo $carrier; ?>"></label>
                                    </div>
                                    <!-- form  -->
                                    <textarea id="<?php echo esc_attr($this->fieldPrepend) ?><?php echo $carrier; ?>" name="<?php echo esc_attr($this->fieldPrepend) ?><?php echo $carrier; ?>" class="form-control sv_d-none"><?php echo  json_encode($this->carrier_settings[$carrier]) ?></textarea>
                                    <input id="<?php echo esc_attr($this->fieldPrepend) ?><?php echo $carrier; ?>_enabled" name="<?php echo esc_attr($this->fieldPrepend) ?><?php echo $carrier; ?>_enabled" value="<?php echo  esc_attr($this->get_option($carrier .'_enabled')) ?>" class="form-control sv_d-none">
                                    <!-- form -->
                                    <h6 class="m-0"><?php echo $this->carrierDetails[$carrier]['name'] ?></h6>
                                </div>
                            </div>
                        </div>

                        <div class="sv_card-body">
                            <?php
                            foreach ($list as $key =>  $option) {
                                $option = (array) $option;
                            ?>
                                <!-- load settings script -->

                                <!-- load settings script -->
                                <!-- option -->
                                <div class="sv_d-flex sv_mb-2 w-100">
                                    <div class="sv_flex-fill">
                                        <?php echo esc_html($option['carrier_option']) ?>
                                    </div>
                                    <div>
                                        <div class="custom-control custom-switch <?php echo $carrier ?>_options">
                                            <input type="checkbox" onchange="shipvista_carrierSelectOption('<?php echo esc_attr($carrier) ?>', '<?php echo esc_attr($key) ?>')" <?php echo esc_attr($option['checked'] == true ? 'checked' : '') ?> class="custom-control-input" data-shipvista-name="<?php echo esc_attr($option['carrier_option']) ?>" id="<?php echo esc_attr($carrier) .'_'. esc_attr($key) ?>" name="<?php echo  esc_attr($carrier) .'_'. esc_attr($key) ?>">
                                            <label class="custom-control-label" for="<?php echo  esc_attr($carrier) .'_'. esc_attr($key) ?>"> </label>
                                        </div>
                                    </div>
                                </div>
                                <!-- option -->

                            <?php } ?>




                        </div>

                    </div>

                </div>

            <?php } ?>


        </div>



    </div>
    <div class="sv_text-center sv_my-3">
        <button type="button" onclick="sv_WooSave()" class="sv_btn sv_btn-primary"> Save Settings</button>
    </div>
</div>