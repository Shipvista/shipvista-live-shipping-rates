<div class="sv_container">
    <div class="sv_pt-3">
        <h4 class="sv_mb-3"><i class="fa fa-cog"></i> Settings</h4>
        <!-- set header -->

        <nav class="sv_navbar  sv_p-0 sv_navbar-expand-lg sv_navbar-light sv_bg-light sv_text-dark sv_m-0">

            <div class="sv_m-0 " id="sv_navbarNav">
                <ul class="sv_d-flex sv_m-0 sv_p-0 sv_flex-wrap">
                    <li class="sv_nav-item sv_border-right sv_m-0  <?php echo (($this->settingTabs == 'basic' || $this->settingTabs == '')  ? 'sv_active sv_bg-dark  sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo ($this->settingTabs == 'basic' ? 'sv_text-white' : 'sv_text-dark') ?>" href="<?php echo $this->pageLink . '&wcs_page=settings&wcs_setting=basic' ?>">Basic</a>
                    </li>
                    <li class="sv_nav-item sv_border-right sv_m-0  <?php echo ($this->settingTabs == 'shipper' ? 'sv_active sv_bg-dark  sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo ($this->settingTabs == 'shipper' ? 'sv_text-white' : 'sv_text-dark') ?>" href="<?php echo $this->pageLink . '&wcs_page=settings&wcs_setting=shipper' ?>">Shipper Settings</a>
                    </li>
                    <li class="sv_nav-item sv_border-right  sv_m-0 <?php echo ($this->settingTabs == 'dimension' ? 'sv_active sv_bg-dark  sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo ($this->settingTabs == 'dimension' ? 'sv_text-white' : 'sv_text-dark') ?>" href="<?php echo $this->pageLink . '&wcs_page=settings&wcs_setting=dimension' ?>">Dimension </a>
                    </li>
                    <li class="sv_nav-item sv_border-right sv_m-0 <?php echo ($this->settingTabs == 'restrict' ? 'sv_active sv_bg-dark sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo ($this->settingTabs == 'restrict' ? 'sv_text-white' : 'sv_text-dark') ?>" href="<?php echo $this->pageLink . '&wcs_page=settings&wcs_setting=restrict' ?>">Restrictions </a>
                    </li>

                    <li class="sv_nav-item  sv_m-0 <?php echo ($this->settingTabs == 'apis' ? 'sv_active sv_bg-dark  sv_text-white' : '') ?>">
                        <a class="sv_nav-link <?php echo ($this->settingTabs == 'apis' ? 'sv_text-white' : 'sv_text-dark') ?>" href="<?php echo $this->pageLink . '&wcs_page=settings&wcs_setting=apis' ?>">Third Party APIs </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- end set headers -->


    <div class=" sv_table-responsive sv_mt-0">

        <table class="sv_table sv_border-0">
            <thead class="sv_border-0">
                <tr class="sv_bg-dark sv_border-0 sv_text-white">
                    <th>#</th>
                    <th>Values</th>
                </tr>
            </thead>
            <?php if ($this->settingTabs == 'feedback') { ?>
                <tbody>
                    <tr>
                        <td colspan="2">
                            <h4>FeedBack</h4>
                            <p class="sv_mb-3">
                                Having errors using this plugin? We would love to get your feedback on this plugin and how we can make it better for the community.
                            </p>

                            <!-- thank you note -->

                            <div class="mb-3">
                                <textarea class="sv_form-control" style="min-height: 80px;" rows="20" name="shipvista_feedback" id="shipvista_feedback" minlength="3" placeholder="Enter feedback"></textarea>
                            </div>

                            <div calss="sv_mb-3">
                                <button type="button" class="sv_btn sv_btn-info" onclick="sv_WooSave()"> Submit Feedback</button>
                            </div>

                        </td>
                    </tr>

                <tbody>
                <?php } elseif ($this->settingTabs == 'restrict') { ?>
                <tbody>

                    <tr>
                        <td colspan="2">
                            <h4>Restrict Postal Codes/Zip Codes</h4>
                            <p>Use this option to set restriction to locations where shipping discounts will not apply.</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Locations <br>
                            <p>Sample Format: Country_Code_1: POSTCODES_1,POSTCODE_2, etc.. | Country_Code_2 ....</p>
                            <small>Separate each country (only 2 characters) with a pipe( | ),<br> Separate country code with restricted postcodes with a colon( : ), <br>Separate each restricted postal code with a comma( , ).</small> <br>
                            <small><b>All postal codes beginning with that set in here will be restricted. E.g. You can put one or more letters to restrict all postal codes beginning with that letter. </b></small>
                        </td>
                        <td>
                            <textarea class="sv_form-control" class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_restricted_locations" cols="20" rows="15" style="min-width: 300px;min-height:200px" placeholder="Enter locations" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_restricted_locations"><?php echo ($this->get_option('shipvista_restricted_locations')); ?></textarea>
                        </td>
                    </tr>

                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_3">
                                <button type="button" onclick="sv_WooSave()" class="sv_btn sv_btn-primary"> Save Settings</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>

            <?php } elseif ($this->settingTabs == 'shipper') { ?>


                <!-- shipper setting -->
                <tbody>

                    <tr>
                        <td colspan="2">
                            <h4>Shippers Settings</h4>
                            <p>Use this option to set your ship from location.</p>
                        </td>
                    </tr>
                    <tr>
                        <td>Origin Country Code* <br>
                            <small>Shipping from country code. Country code must be 2 characters long e.g. US, CA...</small>
                        </td>
                        <td>
                            <input type="text" required minlength="2" maxlength="2" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_country" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_country" value="<?php echo  esc_attr($this->get_option('shipvista_origin_country') ??  $this->wooCountry[0]  ?? 'US') ?>" class="sv_form-control" />
                        </td>
                    </tr>
                    <tr>
                        <td>Origin State* <br>
                            <small>Shipping from State Code. State Code must be 2 characters long.</small>
                        </td>
                        <td>
                            <input type="text" required minlength="2" minlength="2" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_state" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_state" value="<?php echo  esc_attr($this->get_option('shipvista_origin_state') ??  @$this->wooCountry[1] ?? '')  ?>" class="sv_form-control" />
                        </td>
                    </tr>
                    <tr>
                        <td>Origin City* <br>
                            <small>Enter full Shipping from city name.</small>
                        </td>
                        <td>
                            <input type="text" required id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_city" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_city" value="<?php echo  esc_attr($this->get_option('shipvista_origin_city')  ?? $this->get_option('woocommerce_store_city'))  ?>" class="sv_form-control" />
                        </td>
                    </tr>


                    <tr>
                        <td>Origin Postal/Zip Code* <br>
                            <small>Shipping from postal/zip code.</small>
                        </td>
                        <td>
                            <input type="text" required id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_postcode" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_postcode" value="<?php echo  esc_attr($this->get_option('shipvista_origin_postcode') ?? $this->get_option('woocommerce_store_postcode')) ?>" class="sv_form-control" />
                        </td>
                    </tr>
                    <tr>
                        <td>Origin Address <br>
                            <small>Shipping address (Shipping from address).</small>
                        </td>
                        <td>
                            <input type="text" required minlength="3" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_address" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_address" value="<?php echo  esc_attr($this->get_option('shipvista_origin_address') ?? $this->get_option('woocommerce_store_address')) ?>" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>
                        <td>Origin Address line 2<br>
                            <small>Shipping address line 2.</small>
                        </td>
                        <td>
                            <input type="text" minlength="3" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_address_2" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_address_2" value="<?php echo  esc_attr($this->get_option('shipvista_origin_address_2') ??  $this->get_option('woocommerce_store_address_2')) ?>" class="sv_form-control" />
                        </td>
                    </tr>
                    <tr>
                        <td>Email Address<br>
                            <small>Shipping address (Shipping from address).</small>
                        </td>
                        <td>
                            <input type="text" required minlength="5" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_user_email" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_user_email" value="<?php echo  esc_attr(($this->get_option('shipvista_user_email') ??  $this->get_option('woocommerce_store_email_address'))) ?>" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>
                        <td>Origin Phone Number <br>
                            <small>Your store contact phone number.</small>
                        </td>
                        <td>
                            <input type="tel" minlength="7" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_phone_number" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_origin_phone_number" value="<?php echo  esc_attr($this->get_option('shipvista_origin_phone_number')) ?>" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>
                        <td>Currency*<br>
                            <small>Which currency should shipping cost be returned in to you.</small>
                        </td>
                        <td>
                            <input type="text" minlength="3" maxlength="3" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_user_currency" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_user_currency" value="<?php echo  esc_attr($this->get_option('shipvista_user_currency') ?: $this->get_option('woocommerce_currency')) ?>" class="sv_form-control" />
                        </td>
                    </tr>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_3">
                                <button type="button" onclick="sv_WooSave()" class="sv_btn sv_btn-primary"> Save Settings</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>

            <?php } elseif ($this->settingTabs == 'apis') { ?>

                <tbody>

                    <tr>
                        <th colspan="2">
                            <h4>APIs</h4>
                            <p>Connect your shipping with third-party APIs to provide more functionalities to your customers</p>
                        </th>
                    </tr>

                    <tr>
                        <td>
                            Enable Google Places Api <br>
                            <small>Connect to your google places API to enable your customers to find and insert their address faster with Google Places API.</small> <br>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo  esc_attr(($this->get_option('shipvista_google_places_api') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api"></label>
                            </div>
                        </td>
                    </tr>

                    <tr>


                    <tr>
                        <td>Google Places Api Key <br>
                            <small>Copy and paste you Google Places Api Key. <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/places-autocomplete">Learn more</a> on how to get your Google Places API key.</small>
                        </td>
                        <td>
                            <input type="text" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api_key" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_google_places_api_key" value="<?php echo  esc_attr($this->get_option('shipvista_google_places_api_key') ?: '') ?>" class="sv_form-control" />
                        </td>
                    </tr>


                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_my-3">
                                <button type="button" class="sv_btn sv_btn-primary" onclick="sv_WooSave()"> Save Settings</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            <?php } elseif ($this->settingTabs == 'dimension') { ?>

                <!-- shipper setting -->
                <tbody>
                    <tr>
                        <th colspan="2">
                            <h4>Dimensions</h4>
                            <p>Manage fallback dimensions and weight to use for products without dimensions/weight.</p>
                        </th>
                    </tr>
                    <tr>
                        <td>Length <br>
                            <small>Default shipping length.</small>
                        </td>
                        <td>
                            <input type="nubmer" min="0.01" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_length" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_length" value="<?php echo  esc_attr($this->get_option('shipvista_dimension_length') ?: '2.5') ?>" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>


                    <tr>
                        <td>Width <br>
                            <small>Default shipping width.</small>
                        </td>
                        <td>
                            <input type="nubmer" min="0.01" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_width" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_width" value="<?php echo  esc_attr($this->get_option('shipvista_dimension_width') ?: '2') ?>" class="sv_form-control" />
                        </td>
                    </tr>


                    <tr>
                        <td>Height <br>
                            <small>Default shipping height.</small>
                        </td>
                        <td>
                            <input type="nubmer" min="0.01" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_height" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_height" value="<?php echo  esc_attr($this->get_option('shipvista_dimension_height') ?: '1') ?>" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>

                    <tr>
                        <td>Size unit <br>
                            <small>Default shipping size unit</small>
                        </td>
                        <td>
                            <select name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_size_unit" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_size_unit" class="custom-select">
                                <option value="cm" <?php echo  esc_attr(($this->get_option('shipvista_dimension_size_unit') == 'cm' ? 'selected' : '')); ?>>cm</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td>Weight <br>
                            <small>Default shipping weight.</small>
                        </td>
                        <td>
                            <input type="nubmer" min="0.01" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_weight" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_weight" value="<?php echo  esc_attr($this->get_option('shipvista_dimension_weight') ?: 1) ?>" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>

                    <tr>
                        <td>Weight unit <br>
                            <small>Default shipping weighing unit</small>
                        </td>
                        <td>
                            <select name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_weight_unit" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_dimension_weight_unit" class="custom-select">
                                <option value="kg" <?php echo  esc_attr(($this->get_option('shipvista_dimension_weight_unit') == 'kg' ? 'selected' : '')); ?>>kg</option>
                                <!-- <option value="lbs" <?php echo  esc_attr(($this->get_option('shipvista_dimension_weight_unit') == 'lbs' ? 'selected' : '')); ?>>lbs</option> -->
                            </select>
                        </td>
                    </tr>

                    <tr>


                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_my-3">
                                <button type="button" class="sv_btn sv_btn-primary" onclick="sv_WooSave()"> Save Settings</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            <?php } else { ?>
                <!-- basic settings -->
                <tbody>
                    <!-- <tr class="sv_d-none">
                        <td>Tax Status
                        </td>
                        <td>
                            <select name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_tax_status" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_tax_status" class="custom-select">
                                <option value="taxable" <?php echo  esc_attr(($this->get_option('shipvista_tax_status') == 'taxable' ? 'selected' : '')); ?>>Taxable</option>
                                <option value="none">None</option>
                            </select>
                        </td>
                    </tr> -->

                    <tr class="sv_d-none">
                        <td>
                            Auto print labels <br>
                            <small>Once customers order is received print label after 15 minutes.</small>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo  esc_attr(($this->get_option('shipvista_auto_labels') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_auto_labels" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_auto_labels">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_auto_labels"></label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Fallback Rate <br>
                            <small>This cost will be added for every unit of products or the total order value if no shipping rates are obtained from carrier.</small>
                        </td>
                        <td>
                            <input type="number" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_fallback_rate" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_fallback_rate" class="sv_form-control" value="<?php echo $this->get_option('shipvista_fallback_rate') ?: 50; ?>" />
                        </td>
                    </tr>

                    <tr>
                        <td>Fallback Rate On <br>
                            <small>This cost will be added for every unit of the product if no rule is applied to it</small>
                        </td>
                        <td>
                            <select name="<?php echo  esc_attr($this->fieldPrepend) ?>fullback_rate_on" id="<?php echo  esc_attr($this->fieldPrepend) ?>fullback_rate_on" class="custom-select">
                                <option value="per_unit_quantity" <?php echo  esc_attr(($this->get_option('shipvista_fallback_rate_on') == 'per_unit_quantity' ? 'selected' : '')) ?>>Per Unit Quantity</option>
                                <option value="total_order" <?php echo  esc_attr(($this->get_option('shipvista_fallback_rate_on') == 'total_order' ? 'selected' : '')) ?>>Total Order</option>
                            </select>
                        </td>
                    </tr>



                    <tr>
                        <td>Shipping Margin (%) <br>
                            <small>Add a margin on shipping rates to be applied before displaying to customers. This is a percentage added to the total shipping cost before displaying it to the customer.<?php echo $this->get_option('shipvista_rate_margin') ?> </small>
                        </td>
                        <td>
                            <input type="number" max="100" min="0" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_rate_margin" value="<?php echo  esc_attr($this->get_option('shipvista_rate_margin')) ?>" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_rate_margin" class="sv_form-control" />
                        </td>
                    </tr>

                    <tr>
                        <td>Handling time in days <br>
                            <small>How long does it take to get the items ready for pickup? </small>
                        </td>
                        <td>
                            <input type="number" max="100" min="0" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_handling_time" value="<?php echo  esc_attr($this->get_option('shipvista_handling_time')) ?>" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_handling_time" class="sv_form-control" />
                        </td>
                    </tr>



                    <tr class="">
                        <td>
                            Free shipping <br>
                            <small>Enable free shipping option for customers base on pre-defined calculation. </small>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo  esc_attr(($this->get_option('shipvista_free_shipping') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping"></label>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>Free shipping Max Amount <br>
                            <small>This is the maximum amount offered for free shipping. If a shipping cost from the carrier is above this amount the balance will be displayed to customers for them to pay. Free shipping is applied to regular shipments at 100% and other shipping options at 10%.</small>
                        </td>
                        <td>
                            <input type="number" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_max_amount" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_max_amount" class="sv_form-control" value="<?php echo  esc_attr($this->get_option('shipvista_free_max_amount') ?: ''); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>Free shipping days <br>
                            <small>Additional handling time due to free shipping. This number of days will be added to the expected number of days of delivery displayed to the customer. </small>
                        </td>
                        <td>
                            <input type="number" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping_days" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_free_shipping_days" class="sv_form-control" value="<?php echo  esc_attr($this->get_option('shipvista_free_shipping_days') ?: ''); ?>" />
                        </td>
                    </tr>



                    <tr class="">
                        <td>
                            Enable Pickup <br>
                            <small>Enable in-store pickup.</small>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo  esc_attr(($this->get_option('shipvista_pickup') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup"></label>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>Pickup Note<br>
                            <small>Enter pickup instructions to display to customers which they have to follow in order to come for in-store pickup.</small>
                        </td>
                        <td>
                            <textarea class="sv_form-control" class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup_note" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup_note"><?php echo  esc_attr($this->get_option('shipvista_pickup_note')); ?></textarea>
                        </td>
                    </tr>

                    <tr>
                        <td>Pickup address<br>
                            <small>Enter shop locations to display to customers for pickup.</small>
                        </td>
                        <td>
                            <input type="text" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup_address" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_pickup_address" value="<?php echo  esc_attr($this->get_option('shipvista_pickup_address')) ?>" class="sv_form-control" />

                        </td>
                    </tr>


                    <tr class="">
                        <td>
                            Enable API Logs <br>
                            <small>Turn this option on to log every request made by this plugin to shipvista. Logs are found in the plugin directory */assets/logs </small>
                        </td>
                        <td>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" <?php echo esc_attr(($this->get_option('shipvista_log_status') == 'yes' ? 'checked' : '')) ?> class="custom-control-input" id="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_log_status" name="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_log_status">
                                <label class="custom-control-label" for="<?php echo  esc_attr($this->fieldPrepend) ?>shipvista_log_status"></label>
                            </div>
                        </td>
                    </tr>

                </tbody>

                <tfoot>
                    <tr>
                        <td colspan="2">
                            <div class="sv_text-center sv_3">
                                <button type="button" class="sv_btn sv_btn-primary" onclick="sv_WooSave()"> Save Settings</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>
                <!-- basic settings -->
            <?php } ?>

        </table>
    </div>
</div>