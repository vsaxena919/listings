<?php

do_action('lp_enqueue_print_script');

do_action( 'lp_pdf_enqueue_scripts');

$currencyCode = listingpro_currency_sign();

$planID = '';

$latestInvoice = '';

$latestDate = '';

$latestAmount = '';

$latestTax = '';

$latestMethod = '';

$latestPlan = '';

$latestDuration = '';

$latestonlyPlanPrice = '';

global $user_id, $listingpro_options;
$user_fname = get_the_author_meta('first_name', $user_id);
$user_lname = get_the_author_meta('last_name', $user_id);
// User contact meta
$user_address = get_the_author_meta('address', $user_id);
$user_phone = get_the_author_meta('phone', $user_id);
$user_email = get_the_author_meta('user_email', $user_id);

$currency_position = lp_theme_option('pricingplan_currency_position');
$resultsall = get_invoices_list($user_id, '', '');
$results1 = get_invoices_list($user_id, '', 'success');
$results2 = get_invoices_list($user_id, '', 'expired');
$results3 = get_invoices_list($user_id, '', 'pending');
//$results = (object)array_merge((array)$results1,(array)$results2,(array)$results3);
//$resultsinArray = (array) $results;


if(!empty($resultsall)){
        
    ?>
    <div class="tab-pane fade in active lp-new-invoices" id="lp-listings">
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-9 lp-left-panel-height">
            <div class="panel-heading">
                <h5 class="margin-bottom-20"><?php esc_html_e('All Invoices', 'listingpro'); ?></h5>
                <ul class="nav nav-tabs">
                     <?php
                    if(!empty($resultsall)){
                        ?>
                    <li class="active"><a href="#tab0default" data-toggle="tab"><?php echo esc_html__('All', 'listingpro'); ?></a></li>
                    <?php
                    }
                    ?>
                    <?php
                    if(!empty($results1)){
                        ?>
                    <li><a href="#tab1default" data-toggle="tab"><?php echo esc_html__('Active', 'listingpro'); ?></a></li>
                    <?php
                    }
                    ?>
                    <?php
                    if(!empty($results3)){
                        ?>
                         <li><a href="#tab2default" data-toggle="tab"><?php echo esc_html__('pending', 'listingpro'); ?></a></li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
            <div class="panel-body pos-relative" id="lp-new-invoices">
                <div class="lp-main-title clearfix">
                    <div class="col-md-3"><p><?php esc_html_e('Reciept / Invoice','listingpro'); ?></p></div>
                    <div class="col-md-3"><p><?php esc_html_e('date','listingpro'); ?></p></div>
                    <div class="col-md-3"><p><?php esc_html_e('Status','listingpro'); ?></p></div>
                    <div class="col-md-3"><p><?php esc_html_e('amount','listingpro'); ?></p></div>

                </div>
                <div class="tab-content clearfix">
                    
                    <?php                            
                        if(!empty($resultsall)){
                            
                    ?>
                    <div class="tab-pane fade in active" id="tab0default">
                        <?php
                            $lpCount = 1;
                            foreach( $resultsall as $data ){
                                $plan_priceORG = '';
                                $plan_price = '';
                                $plan_title = '';
                                if( isset($data->plan_id) && !empty($data->plan_id) ){
                                    $planID = $data->plan_id;
                                    $plan_title = get_the_title($planID);
                                    $plan_price = get_post_meta($planID, 'plan_price', true);
                                    $plan_price = $data->price;
                                }
                                $plan_priceORG = $plan_price;
                                if(!empty($plan_price)){
                                    if($currency_position=='right'){
                                        $plan_price .=$currencyCode;
                                    }else{
                                        $plan_price =$currencyCode.$plan_price;
                                    }
                                }
                                $invoiceno = '';
                                if( isset($data->order_id) && !empty($data->order_id) ){
                                    $invoiceno = $data->order_id;
                                }
                                $invdate = '';
                                if( isset($data->date) && !empty($data->date) ){
                                    $invdate = $data->date;
                                    $invdate = date(get_option('date_format'), strtotime($invdate));
                                }
                                $listId='';
                                if(isset($data->post_id) && !empty($data->post_id)){
                                    $listId=$data->post_id;
                                    $listTitle =  get_the_title($listId);
                                }
                                $pmethod = '';
                                if( isset($data->payment_method) && !empty($data->payment_method) ){
                                    $pmethod = $data->payment_method;
                                }
                                $pstatus = '';
                                $discounted = get_post_meta($listId, 'discounted', true);
                                if( isset($data->status) && !empty($data->status) && $discounted == '' ){
                                    $pstatus = $data->status;
                                }else if($discounted == 'yes'){
                                    $pstatus = $data->status.esc_html__(' (100% Discounted)', 'listingpro');
                                }
                                $pdays = '';
                                if( isset($data->days) && !empty($data->days) ){
                                    $pdays = $data->days;
                                }
                                if(empty($pdays)){
                                    $pdays = esc_html__('Unlimited', 'listingpro');
                                }
                                $taxPrice = 0;
                                $onlyPlanPrice = '';
                                if(isset($data->tax)){
                                    if(!empty($data->tax)){
                                        $taxPrice = $data->tax;
                                    }
                                    $onlyPlanPrice = $plan_priceORG - $taxPrice;
                                    $onlyPlanPrice = round($onlyPlanPrice, 2);
                                }
                                /* if price saved in meta */

                                $lp_purchase_price = listing_get_metabox_by_ID('lp_purchase_price', $listId);
                                $lp_purchase_tax = listing_get_metabox_by_ID('lp_purchase_tax', $listId);
                                if(!empty($lp_purchase_price)){
                                    $onlyPlanPrice = round($lp_purchase_price, 2);
                                    $plan_priceORG = $onlyPlanPrice;
                                }
                                if(!empty($lp_purchase_tax)){
                                    $taxPrice = $lp_purchase_tax;
                                }
                                /* end if price saved in meta */
                                $checked = '';
                                if($lpCount==1){
                                    $listTitle = get_the_title($listId);
                                    $latestInvoice = $invoiceno;
                                    $latestDate = $invdate;
                                    $latestAmount = $plan_price;
                                    $latestPlanPriceORG = $plan_priceORG;
                                    $latestTax = $taxPrice;
                                    $latestMethod = $pmethod;
                                    $latestPlan = $plan_title;
                                    $latestDuration = $pdays;
                                    $invoicestatus = $pstatus;
                                    $checked = 'checked';
                                    if(!empty($lp_purchase_price)){
                                        $latestonlyPlanPrice = round($lp_purchase_price, 2);
                                        $latestPlanPriceORG = $latestonlyPlanPrice;
                                    }else{
                                        $latestonlyPlanPrice = $latestPlanPriceORG - $latestTax;
                                        $latestonlyPlanPrice = round($latestonlyPlanPrice, 2);
                                    }
                                }
                                $dataAttass = 'data-inoviceno="'.$invoiceno.'" data-listtitle="'.$listTitle.'" data-date="'.$invdate.'" data-amount="'.$plan_price.'" data-tax="'.$taxPrice.'" data-method="'.$pmethod.'" data-plan="'. $plan_title.'" data-duration="'.$pdays.'" data-status="'.$pstatus.'" data-duration="'.$pdays.'" data-orprice="'.$onlyPlanPrice.'"  ';
    
                            if($data->payment_method == 'paypal' && !empty($data->token)) {
                            ?>
                                <div class="lp-listing-outer-container clearfix" <?php echo $dataAttass; ?>>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-number lp-listing-form">
                                            <label>
                                                <p><?php echo $invoiceno; ?></p>
                                                <div class="radio radio-danger lp_right_preview_this_invoice">
                                                    <input id="<?php echo $invoiceno; ?>" class="radio_checked" type="radio" name="method" value="<?php echo $invoiceno; ?>" <?php echo $checked; ?>>
                                                    <label for="<?php echo $invoiceno; ?>">
                                                    </label>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-date">
                                            <p><?php echo $invdate; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-status">
                                            <?php
                                                   echo $pstatus;
                                                ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-price clerarfix">
                                            <p><?php echo $plan_price; ?></p>
                                            <a class="lp_preview_this_invoice" href="<?php echo $invoiceno; ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php esc_html_e('View','listingpro'); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $lpCount++;
                            }elseif ($data->payment_method != 'paypal') {
                                ?>
                                <div class="lp-listing-outer-container clearfix" <?php echo $dataAttass; ?>>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-number lp-listing-form">

                                            <label>
                                                <p><?php echo $invoiceno; ?></p>
                                                <div class="radio radio-danger lp_right_preview_this_invoice">
                                                    <input id="<?php echo $invoiceno; ?>" class="radio_checked" type="radio" name="method" value="<?php echo $invoiceno; ?>" <?php echo $checked; ?>>
                                                    <label for="<?php echo $invoiceno; ?>">

                                                    </label>
                                                </div>
                                            </label>

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-date">
                                            <p><?php echo $invdate; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-status">
                                           <?php
                                                   echo $pstatus;
                                                ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-price clerarfix">
                                            <p><?php echo $plan_price; ?></p>
                                            <a class="lp_preview_this_invoice" href="<?php echo $invoiceno; ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php esc_html_e('View','listingpro'); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $lpCount++;
                            }
                        }
                        ?>
                    </div>
                    <?php                            
                    }       
                    if(!empty($results1)){
                            
                    ?>
                    <div class="tab-pane fade" id="tab1default">
                        <?php
                            foreach( $results1 as $data ){
                                $plan_priceORG = '';
                                $plan_price = '';
                                $plan_title = '';
                                if( isset($data->plan_id) && !empty($data->plan_id) ){
                                    $planID = $data->plan_id;
                                    $plan_title = get_the_title($planID);
                                    $plan_price = get_post_meta($planID, 'plan_price', true);
                                    $plan_price = $data->price;
                                }
                                $plan_priceORG = $plan_price;
                                if(!empty($plan_price)){
                                    if($currency_position=='right'){
                                        $plan_price .=$currencyCode;
                                    }else{
                                        $plan_price =$currencyCode.$plan_price;
                                    }
                                }
                                $invoiceno = '';
                                if( isset($data->order_id) && !empty($data->order_id) ){
                                    $invoiceno = $data->order_id;
                                }
                                $invdate = '';
                                if( isset($data->date) && !empty($data->date) ){
                                    $invdate = $data->date;
                                    $invdate = date(get_option('date_format'), strtotime($invdate));
                                }
                                $listId='';
                                if(isset($data->post_id) && !empty($data->post_id)){
                                    $listId=$data->post_id;
                                    $listTitle =  get_the_title($listId);
                                }
                                $pmethod = '';
                                if( isset($data->payment_method) && !empty($data->payment_method) ){
                                    $pmethod = $data->payment_method;
                                }
                                $pstatus = '';
                                $discounted = get_post_meta($listId, 'discounted', true);
                                if( isset($data->status) && !empty($data->status) && $discounted == '' ){
                                    $pstatus = $data->status;
                                }else if($discounted == 'yes'){
                                    $pstatus = $data->status.esc_html__(' (100% Discounted)', 'listingpro');
                                }
                                $pdays = '';
                                if( isset($data->days) && !empty($data->days) ){
                                    $pdays = $data->days;
                                }
                                if(empty($pdays)){
                                    $pdays = esc_html__('Unlimited', 'listingpro');
                                }
                                $taxPrice = 0;
                                $onlyPlanPrice = '';
                                if(isset($data->tax)){
                                    if(!empty($data->tax)){
                                        $taxPrice = $data->tax;
                                    }
                                    $onlyPlanPrice = $plan_priceORG - $taxPrice;
                                    $onlyPlanPrice = round($onlyPlanPrice, 2);
                                }
                                /* if price saved in meta */

                                $lp_purchase_price = listing_get_metabox_by_ID('lp_purchase_price', $listId);
                                $lp_purchase_tax = listing_get_metabox_by_ID('lp_purchase_tax', $listId);
                                if(!empty($lp_purchase_price)){
                                    $onlyPlanPrice = round($lp_purchase_price, 2);
                                    $plan_priceORG = $onlyPlanPrice;
                                }
                                if(!empty($lp_purchase_tax)){
                                    $taxPrice = $lp_purchase_tax;
                                }
                                /* end if price saved in meta */
                                $checked = '';
                                $dataAttass = 'data-inoviceno="'.$invoiceno.'" data-listtitle="'.$listTitle.'" data-date="'.$invdate.'" data-amount="'.$plan_price.'" data-tax="'.$taxPrice.'" data-method="'.$pmethod.'" data-plan="'. $plan_title.'" data-duration="'.$pdays.'" data-status="'.$pstatus.'" data-orprice="'.$onlyPlanPrice.'"  ';
    
                            if($data->payment_method == 'paypal' && !empty($data->token)) {
                            ?>
                                <div class="lp-listing-outer-container clearfix" <?php echo $dataAttass; ?>>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-number lp-listing-form">
                                            <label>
                                                <p><?php echo $invoiceno; ?></p>
                                                <div class="radio radio-danger lp_right_preview_this_invoice">
                                                    <input id="<?php echo $invoiceno; ?>" class="radio_checked" type="radio" name="method" value="<?php echo $invoiceno; ?>" <?php echo $checked; ?>>
                                                    <label for="<?php echo $invoiceno; ?>">
                                                    </label>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-date">
                                            <p><?php echo $invdate; ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-status">
                                            <?php
                                                   echo $pstatus;
                                                ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-price clerarfix">
                                            <p><?php echo $plan_price; ?></p>
                                            <a class="lp_preview_this_invoice" href="<?php echo $invoiceno; ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php esc_html_e('View','listingpro'); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }elseif ($data->payment_method != 'paypal') {
                                ?>
                                <div class="lp-listing-outer-container clearfix" <?php echo $dataAttass; ?>>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-number lp-listing-form">

                                            <label>
                                                <p><?php echo $invoiceno; ?></p>
                                                <div class="radio radio-danger lp_right_preview_this_invoice">
                                                    <input id="<?php echo $invoiceno; ?>" class="radio_checked" type="radio" name="method" value="<?php echo $invoiceno; ?>" <?php echo $checked; ?>>
                                                    <label for="<?php echo $invoiceno; ?>">

                                                    </label>
                                                </div>
                                            </label>

                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-date">
                                            <p><?php echo $invdate; ?></p>
                                        </div>
                                    </div>
                                      <div class="col-md-3">
                                        <div class="lp-invoice-status">
                                            <?php
                                                echo $pstatus;
                                            ?>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="lp-invoice-price clerarfix">
                                            <p><?php echo $plan_price; ?></p>
                                            <a class="lp_preview_this_invoice" href="<?php echo $invoiceno; ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php esc_html_e('View','listingpro'); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <?php                            
                    }
                    if(!empty($results3)){                           
                        ?>
                        <div class="tab-pane fade" id="tab2default">
                            <?php  
                                foreach( $results3 as $data ){
                                    $plan_priceORG = '';
                                    $plan_price = '';
                                    $plan_title = '';
                                    if( isset($data->plan_id) && !empty($data->plan_id) ){
                                        $planID = $data->plan_id;
                                        $plan_title = get_the_title($planID);
                                        $plan_price = get_post_meta($planID, 'plan_price', true);
                                        $plan_price = $data->price;
                                    }
                                    $plan_priceORG = $plan_price;
                                    if(!empty($plan_price)){
                                        if($currency_position=='right'){
                                            $plan_price .=$currencyCode;
                                        }else{
                                            $plan_price =$currencyCode.$plan_price;
                                        }
                                    }
                                    $invoiceno = '';
                                    if( isset($data->order_id) && !empty($data->order_id) ){
                                        $invoiceno = $data->order_id;
                                    }
                                    $invdate = '';
                                    if( isset($data->date) && !empty($data->date) ){
                                        $invdate = $data->date;
                                        $invdate = date(get_option('date_format'), strtotime($invdate));
                                    }
                                    $listId='';
                                    if(isset($data->post_id) && !empty($data->post_id)){
                                        $listId=$data->post_id;
                                        $listTitle =  get_the_title($listId);
                                    }
                                    $pmethod = '';
                                    if( isset($data->payment_method) && !empty($data->payment_method) ){
                                        $pmethod = $data->payment_method;
                                    }
                                    $pstatus = '';
                                    $discounted = get_post_meta($listId, 'discounted', true);
                                    if( isset($data->status) && !empty($data->status) && $discounted == '' ){
                                        $pstatus = $data->status;
                                    }else if($discounted == 'yes'){
                                        $pstatus = $data->status.esc_html__(' (100% Discounted)', 'listingpro');
                                    }
                                    $pdays = '';
                                    if( isset($data->days) && !empty($data->days) ){
                                        $pdays = $data->days;
                                    }
                                    if(empty($pdays)){
                                        $pdays = esc_html__('Unlimited', 'listingpro');
                                    }
                                    $taxPrice = 0;
                                    $onlyPlanPrice = '';
                                    if(isset($data->tax)){
                                        if(!empty($data->tax)){
                                            $taxPrice = $data->tax;
                                        }
                                        $onlyPlanPrice = $plan_priceORG - $taxPrice;
                                        $onlyPlanPrice = round($onlyPlanPrice, 2);
                                    }
                                    /* if price saved in meta */

                                    $lp_purchase_price = listing_get_metabox_by_ID('lp_purchase_price', $listId);
                                    $lp_purchase_tax = listing_get_metabox_by_ID('lp_purchase_tax', $listId);
                                    if(!empty($lp_purchase_price)){
                                        //$onlyPlanPrice = round($lp_purchase_price, 2);
                                        $plan_priceORG = $onlyPlanPrice;
                                    }
                                    if(!empty($lp_purchase_tax)){
                                        $taxPrice = $lp_purchase_tax;
                                    }
                                    /* end if price saved in meta */
                                    $checked = '';
                                    
                                    $dataAttass = 'data-inoviceno="'.$invoiceno.'" data-listtitle="'.$listTitle.'" data-date="'.$invdate.'" data-amount="'.$plan_price.'" data-tax="'.$taxPrice.'" data-method="'.$pmethod.'" data-plan="'. $plan_title.'" data-duration="'.$pdays.'" data-status="'.$pstatus.'" data-orprice="'.$onlyPlanPrice.'"  ';
    
                                if($data->payment_method == 'paypal' && !empty($data->token)) {
                                    ?>
                                    <div class="lp-listing-outer-container clearfix" <?php echo $dataAttass; ?>>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-number lp-listing-form">

                                                <label>
                                                    <p><?php echo $invoiceno; ?></p>
                                                    <div class="radio radio-danger lp_right_preview_this_invoice">
                                                        <input id="<?php echo $invoiceno; ?>" class="radio_checked" type="radio" name="method" value="<?php echo $invoiceno; ?>" <?php echo $checked; ?>>
                                                        <label for="<?php echo $invoiceno; ?>">

                                                        </label>
                                                    </div>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-date">
                                                <p><?php echo $invdate; ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-status">
                                               <?php
                                                   echo $pstatus;
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-price clerarfix">
                                                <p><?php echo $plan_price; ?></p>
                                                <a class="lp_preview_this_invoice" href="<?php echo $invoiceno; ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php esc_html_e('View','listingpro'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                } elseif ($data->payment_method != 'paypal') {
                                    ?>
                                    <div class="lp-listing-outer-container clearfix" <?php echo $dataAttass; ?>>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-number lp-listing-form">

                                                <label>
                                                    <p><?php echo $invoiceno; ?></p>
                                                    <div class="radio radio-danger lp_right_preview_this_invoice">
                                                        <input id="<?php echo $invoiceno; ?>" class="radio_checked" type="radio" name="method" value="<?php echo $invoiceno; ?>" <?php echo $checked; ?>>
                                                        <label for="<?php echo $invoiceno; ?>">

                                                        </label>
                                                    </div>
                                                </label>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-date">
                                                <p><?php echo $invdate; ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-status">
                                                <?php
                                                   echo $pstatus;
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="lp-invoice-price clerarfix">
                                                <p><?php echo $plan_price; ?></p>
                                                <a class="lp_preview_this_invoice" href="<?php echo $invoiceno; ?>"><i class="fa fa-eye" aria-hidden="true"></i> <?php esc_html_e('View','listingpro'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <!--popup for preview -->
                <div class="lp_popup_preview_invoice">
                    <div id="listing-invoices-popup" class="listing-invoices-popup">
                        <div class="popup-dialog">
                            <div class="md-content">
                                <div class="modal-header">
                                    <button type="button" class="close close_invoice_prev" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <!--leftside-->
                                        <div class="col-md-6 text-left">
                                            <div class="lp-invoice-leftinfo">
                                                <div class="margin-bottom-20">
                                                    <img class="img-responsive" src="<?php echo lp_theme_option_url('invoice_logo'); ?>" alt="<?php echo esc_attr('logo'); ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="lp-invoice-rightinfo">
                                                <div class="margin-bottom-20">
                                                    <span class="lp-infoice-label">
                                                        <?php echo esc_html__('PAID', 'listingpro'); ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!--leftside-->
                                        <div class="col-md-6 text-left">
                                            <div class="lp-invoice-leftinfo">
                                                <div class="margin-bottom-40">
                                                    <h3 class="modal-titl"><?php echo esc_html__('Invoice#', 'listingpro'); ?> <span class="lppopinvoice"></span></h3>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="lp-invoice-rightinfo">
                                                <div class="margin-bottom-40">
                                                    <p class="lp-invoice-popup-date"><span><?php echo esc_html__('Date: ','listingpro'); ?></span><span class="lppopdate"></span></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!--leftside-->
                                        <div class="col-md-6 text-left">
                                            <div class="lp-invoice-leftinfo">
                                                <span>
                                                    <?php echo esc_html__('Invoice To: ', 'listingpro'); ?>
                                                </span>
                                                <span class="spanblock graycolor">
                                                    <?php echo $user_fname.' '.$user_lname; ?>
                                                </span>
                                                <span class="spanblock maxwidth130">
                                                    <?php echo $user_address; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="lp-invoice-rightinfo">
                                                <span>
                                                    <?php echo esc_html__('Pay To: ', 'listingpro'); ?>
                                                </span>
                                                <span class="spanblock graycolor">
                                                    <?php echo esc_html__('Business Name','listingpro'); ?>
                                                </span>
                                                <span class="spanblock">
                                                    <?php echo lp_theme_option('invoice_company_name'); ?>
                                                </span>
                                                <span class="spanblock">
                                                    <?php echo lp_theme_option('invoice_address'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!--leftside-->
                                        <div class="col-md-6 text-left">
                                            <div class="lp-invoice-leftinfo">
                                                <span class="spanblock">
                                                    <?php echo $user_phone; ?>
                                                </span>
                                                <span class="spanblock">
                                                    <?php echo $user_email; ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <div class="lp-invoice-rightinfo">
                                                <span class="spanblock graycolor">
                                                        <?php echo esc_html__('Call Us For Help','listingpro'); ?>
                                                </span>
                                                <span class="spanblock">
                                                        <?php echo lp_theme_option('invoice_phone'); ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="lp-invoice-description-title">
                                                <div class="clearfix lp-invoice-description-title-inner">
                                                    <h3>
                                                        <span><?php echo esc_html__('Invoice Details', 'listingpro'); ?></span>
                                                    </h3>
                                                </div>
                                                <ul class="clearfix lp-invoice-planinfo-inner">
                                                    <li class="clearfix">
                                                        <div class="col-md-2 lp-in-trns-id padding-left-0">
                                                            <span class=""><?php echo esc_html__('Transaction ID', 'listingpro'); ?></span>
                                                            <p><?php echo $latestInvoice; ?></p>
                                                        </div>
                                                        <div class="col-md-2 lp-in-trns-id padding-left-0">
                                                            <span class=""><?php echo esc_html__(' Listing Name ', 'listingpro'); ?></span>
                                                            <p class="lppoplist"></p>
                                                        </div>
                                                        <div class="col-md-2 lp-in-trns-id padding-left-0">
                                                            <span class=""><?php echo esc_html__('Pricing Plan', 'listingpro'); ?></span>
                                                            <p class="lp-plan-name lppopplan"></p>
                                                        </div>
                                                        <div class="col-md-2 lp-in-trns-id padding-left-0">
                                                            <span class=""><?php echo esc_html__(' Duration ', 'listingpro'); ?></span>
                                                            <p class="lppopduration"></p>
                                                        </div>
                                                        <div class="col-md-4 lp-in-trns-id padding-0 text-right">
                                                            <span class=""><?php echo esc_html__(' Amount ', 'listingpro'); ?></span>
                                                            <p class="lppopamount"></p>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="lp-invoices-other-details margin-bottom-30">
                                                <ul class="clearfix">
                                                    <li><?php echo esc_html__('Tax', 'listingpro'); ?> <span class="lppoptaxprice"></span></li>
                                                    <li><?php echo esc_html__('Plan Price', 'listingpro'); ?> <span class="lppopplanprice"></span></li>                                                        
                                                    <li class="lp-invoice-total-amount"><?php echo esc_html__('Total', 'listingpro'); ?> <span class="lppopamount"></span></li>
                                                </ul>
                                            </div>
                                            <p class="text-right lp-pay-with"><?php esc_html_e('Paid with','listingpro'); ?><br />
                                                <img data-srcwire="<?php echo get_template_directory_uri().'/assets/images/wire.png' ?>"
                                                        data-srcpaypal="<?php echo get_template_directory_uri().'/assets/images/paypal.png' ?>"
                                                        data-srcstripe="<?php echo get_template_directory_uri().'/assets/images/stripe.png' ?>"
                                                        data-srcpaystack="<?php echo plugins_url('paystack-for-listingpro/assets/images/paystack.png'); ?>"
                                                        data-srcrazorpay="<?php echo plugins_url('razorpay-for-listingpro/assets/images/logo.png'); ?>"
                                                        src="" />
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="md-content">
                        <div class="modal-footer clearfix">
                            <div class="row">
                                <div class="col-md-6 text-left">
                                    <button type="button" class="downloadpdffullinv"><i class="fa fa-download" aria-hidden="true"></i> <?php esc_html_e('Download PDF','listingpro'); ?></button>
                                </div>
                                <div class="col-md-6 text-right">
                                    <button type="button" class="pull-right printthisinvoice"><?php esc_html_e('Print','listingpro'); ?> <i class="fa fa-print" aria-hidden="true"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 padding-right-0 lp-right-panel-height">
            <div class="lp-ad-click-outer">
                <div class="lp-general-section-title-outer lp_right_preview_invoice">
                    <p class="clarfix lp-general-section-title comment-reply-title active"> <?php echo esc_html__('Details', 'listingpro');?> <i class="fa fa-angle-right" aria-hidden="true"></i></p>
                    <div class="lp-ad-click-inner" id="lp-ad-click-inner">
                        <ul class="lp-invoices-all-stats clearfix">
                            <li>
                                <h5><?php echo esc_html__('INVOICE#', 'listingpro');?>  <span class="lppopinvoice"><?php echo $latestInvoice; ?></span></h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('Date', 'listingpro');?>  <span class="lppopdate"><?php echo $latestDate; ?></span></h5>
                            </li>
                            <li>
                            <h5><?php echo esc_html__('Status', 'listingpro');?>
                                <span class="lppopstatus">
                                    <?php
                                    echo $invoicestatus;
                                    ?>
                                </span>
                            </h5>
                            </li>
                            <?php
                            if(!empty($latestTax)){
                                ?>
                                <li>
                                    <h5><?php echo esc_html__('Tax Price', 'listingpro');?>  <span class="lppoptaxprice"><?php echo $latestTax; ?></span></h5>
                                </li>
                                <li>
                                    <h5><?php echo esc_html__('Plan Price', 'listingpro');?>  <span class="lppopplanprice"><?php echo $latestonlyPlanPrice; ?></span></h5>
                                </li>
                                <?php
                            }
                            ?>                                
                            <li>
                                <h5><?php echo esc_html__('Total', 'listingpro');?>  <span class="lppopamount"><?php echo $latestAmount; ?></span></h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('Method', 'listingpro');?>  <span class="lppopmethod"><?php echo $latestMethod; ?></span></h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('plan', 'listingpro');?>  <span class="lppopplan"><?php echo $latestPlan; ?></span></h5>
                            </li>
                            <li>
                                <h5><?php echo esc_html__('Duration', 'listingpro');?>  <span class="lppopduration"><?php echo $latestDuration; ?></span></h5>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
}else{
    ?>
    <div class="lp-blank-section">
        <div class="col-md-12 blank-left-side">
            <img src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
            <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
            <p><?php echo esc_html__('You must be here for the first time. You will see Listing invoices here.', 'listingpro'); ?></p>
        </div>
    </div>
    <?php
}
?>
<div id="lpinvoiceforpdf" style="display:none">
    <h4><?php echo esc_html__('Invoice#', 'listingpro'); ?> <span class="lppopinvoice"></span></h4>
    <p class="lp-invoice-popup-date"><span><?php echo esc_html__('Date: ','listingpro'); ?></span><span class="lppopdate"></span></p>
    <p class="margin-bottom-10"><?php echo esc_html__('Billed To: ','listingpro'); ?></p>
    <p><?php echo $user_fname; ?> <?php echo $user_lname; ?></p>
    <p><?php echo $user_phone; ?></p>
    <p class="lp-invoice-email"><?php echo $user_email; ?></p>
    <p><?php echo $user_address; ?></p>
    <p><?php echo esc_html__('List Name : ','listingpro'); ?><span class="lllistname"><?php echo $listTitle; ?></span></p>
    <p class="lp-bill-bold"><?php echo lp_theme_option('invoice_company_name'); ?></p>
    <p><?php echo lp_theme_option('invoice_address'); ?> </p>
    <p class="lp-invoice-email"><?php echo get_option('admin_url'); ?> </p>
    <p>
        <span><?php echo esc_html__('Amount', 'listingpro'); ?></span>
    </p>
    <p class="lp-invoice-total-amount"><?php echo esc_html__('Tax Price', 'listingpro'); ?> <span class="lppopamountqqq"></span></p>    
    <p class="lp-invoice-total-amount"><?php echo esc_html__('Total', 'listingpro'); ?> <span class="lppopamountwww"></span></p>
    <p class="lp-pay-with"><?php echo esc_html__('Paid with','listingpro'); ?><br />
        <span class="lppopmethod"></span>
    </p>
</div>