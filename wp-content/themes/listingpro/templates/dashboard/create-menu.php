<div class="user-recent-listings-inner tab-pane fade in active" id="announcements">
	<div class="tab-header">
		<h3><?php echo esc_html__('Create Menu', 'listingpro'); ?></h3>
	</div>
    <?php
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $args   =   array(
        'post_type' => 'listing',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'author' => $user_id,
    );
    $user_listings  =   new WP_Query($args);
    $count_listings =    $user_listings->found_posts;

    $m_args =   array(
        'post_type' => 'listing',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'author' => $user_id,
        'meta_key' => 'lp-listing-menu',
        'meta_compare' => 'EXISTS'
    );
    $m_listings             =   new WP_Query($m_args);
    $count_m_listings       =   $m_listings->found_posts;

    $menu_groups_data    =   get_user_meta( $user_id, 'user_menu_groups' );
    $menu_types_data     =   get_user_meta( $user_id, 'user_menu_types' );

    ?>
	<div class="row lp-list-page-list">
        <div class="announcements-wrap page-innner-container padding-40 lp-border lp-border-radius-8">
            <?php
            if( $m_listings->have_posts() ):

            ?>
                <div class="panel-recent-activity">
                    <div class="section-title">
                        <h3><?php echo esc_html__('Menus', 'listingpro'); ?></h3>
                    </div>
                    <ul class="lp-menus-list">
                        <?php
                        while ( $m_listings->have_posts() ): $m_listings->the_post();
                            $lp_listing_menus   =   get_post_meta( get_the_ID(), 'lp-listing-menu', true );
                            if( $lp_listing_menus && is_array( $lp_listing_menus ) && !empty( $lp_listing_menus ) ):
                                foreach ( $lp_listing_menus as $menu_type => $lp_listing_menu ):
                                    foreach ( $lp_listing_menu as $menu_group => $listing_menu ):
                                        foreach ( $listing_menu as $k => $lp_menu ):
                                            $menu_id    =   str_replace( ' ', '-', $menu_type ).'_'.str_replace( ' ', '-', $menu_group ).'_'.$k;
                                            ?>
                                            <li>
                                                <div class="col-md-9">
                                                    <div class="row">
                                                        <p><?php echo $lp_menu['mTitle']; ?></p>
                                                        <form class="form-horizontal" id="menu-update-<?php echo $menu_id; ?>">
                                                            <div class="form-group">
                                                                <div class="col-sm-8">
                                                                    <label for="menu-title-<?php echo $menu_id; ?>"><?php echo esc_html__('Title', 'listingpro'); ?></label>
                                                                    <input type="text" class="form-control" name="menu-title-<?php echo $menu_id; ?>" id="menu-title-<?php echo $menu_id; ?>" value="<?php echo $lp_menu['mTitle']; ?>" />
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <label for="menu-old-price-<?php echo $menu_id; ?>"><?php echo esc_html__('Old Price', 'listingpro'); ?></label>
                                                                    <input type="text" class="form-control" name="menu-old-price-<?php echo $menu_id; ?>" id="menu-old-price-<?php echo $menu_id; ?>" value="<?php echo $lp_menu['mOldPrice']; ?>" />
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-sm-8">
                                                                    <label for="menu-detail-<?php echo $menu_id; ?>"><?php echo esc_html__('description', 'listingpro'); ?></label>
                                                                    <input type="text" class="form-control" name="menu-detail-<?php echo $menu_id; ?>" id="menu-detail-<?php echo $menu_id; ?>" value="<?php echo $lp_menu['mDetail']; ?>" />
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <label for="menu-new-price-<?php echo $menu_id; ?>"><?php echo esc_html__('New Price', 'listingpro'); ?></label>
                                                                    <input type="text" class="form-control" name="menu-new-price-<?php echo $menu_id; ?>" id="menu-new-price-<?php echo $menu_id; ?>" value="<?php echo $lp_menu['mNewPrice']; ?>" />
                                                                </div>
                                                                <div class="clearfix"></div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-sm-8">
                                                                    <label for="menu-listing"><?php echo esc_html__('Listing', 'listingpro'); ?></label>
                                                                    <input class="form-control" type="text" value="<?php echo get_the_title( $lp_menu['mListing'] ); ?>" disabled>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <label for="menu-link-<?php echo $menu_id; ?>"><?php echo esc_html__('Link', 'listingpro'); ?></label>
                                                                    <input type="text" class="form-control" name="menu-link-<?php echo $menu_id; ?>" id="menu-link-<?php echo $menu_id; ?>" value="<?php echo $lp_menu['mLink']; ?>" />
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-sm-4">
                                                                    <label><?php echo esc_html__('Menu Type', 'listingpro'); ?></label>
                                                                    <input class="form-control" value="<?php echo $menu_type ; ?>" type="text" disabled>
                                                                </div>
                                                                <div class="col-sm-4">
                                                                    <label for="menu-group"><?php echo esc_html__('Menu group', 'listingpro'); ?></label>
                                                                    <input class="form-control" value="<?php echo $menu_group ; ?>" type="text" disabled>
                                                                </div>
                                                            </div>
                                                            <button class="lp-edit-menu add-btn-v2 lp-review-btn btn-second-hover" data-LID="<?php echo $lp_menu['mListing']; ?>" data-menuID="<?php echo $menu_id; ?>" data-uid="<?php echo $user_id; ?>"><?php echo esc_html__('Save', 'listingpro'); ?></button>
                                                            <button class="dash-cancel-save add-btn-v2 lp-review-btn btn-second-hover"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                                                            <div class="ann-err-msg ann-err-msg-<?php echo $k; ?>"><?php echo esc_html__('Message & listing are required fields', 'listingpro'); ?></div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="row">
                                                        <div class="lp-rigt-icons lp-list-view-content-bottom lp-all-listing-action-btns">
                                                            <ul class="lp-list-view-edit list-style-none aliceblue" style="padding-top: 0;">
                                                                <li><a href="#" class="menu-edit" data-menuID="<?php echo $menu_id; ?>" data-uid="<?php echo $user_id; ?>"><i class="fa fa-pencil"></i><span><?php echo esc_html__('Edit', 'listingpro'); ?></span></a></li>
                                                                <li><a href="#" class="menu-del del-this" data-LID="<?php echo $lp_menu['mListing']; ?>" data-targetid="<?php echo $menu_id; ?>" data-uid="<?php echo $user_id; ?>"><i class="fa fa-close"></i><span><?php echo esc_html__('Remove', 'listingpro'); ?></span></a></li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="clearfix"></div>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                        <?php endif; endwhile;  ?>
                    </ul>
                </div>
            <?php endif;  ?>
            <div class="add-new-menu">
                <div class="section-title">
                    <h3><?php echo esc_html__('Add Menu', 'listingpro'); ?></h3>
                </div>
                <form class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-8">
                            <label for="menu-title"><?php echo esc_html__('Title', 'listingpro'); ?></label>
                            <input type="text" class="form-control" name="menu-title" id="menu-title" placeholder="<?php echo esc_html__('Menu title', 'listingpro'); ?>" />
                        </div>
                        <div class="col-sm-4">
                            <label for="menu-old-price"><?php echo esc_html__('Old Price', 'listingpro'); ?></label>
                            <input type="text" class="form-control" name="menu-old-price" id="menu-old-price" placeholder="<?php echo esc_html__('old price', 'listingpro'); ?>" />
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8">
                            <label for="menu-detail"><?php echo esc_html__('Description', 'listingpro'); ?></label>
                            <input type="text" class="form-control" name="menu-detail" id="menu-detail" placeholder="<?php echo esc_html__('menu detail', 'listingpro'); ?>" />
                        </div>
                        <div class="col-sm-4">
                            <label for="menu-new-price"><?php echo esc_html__('New Price', 'listingpro'); ?></label>
                            <input type="text" class="form-control" name="menu-new-price" id="menu-new-price" placeholder="<?php echo esc_html__('new price', 'listingpro'); ?>" />
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-8 select2-dash">
                            <label for="menu-listing"><?php echo esc_html__('Listing', 'listingpro'); ?></label>
                            <select class="form-control select2" name="menu-listing" id="menu-listing">
                                <option value=""><?php echo esc_html__( 'Select Listing', 'listingpro' ); ?></option>
                                <?php
                                if( $user_listings->have_posts() ): while ($user_listings->have_posts()): $user_listings->the_post();
                                    echo '<option value="'. get_the_ID() .'">'. get_the_title() .'</option>';
                                endwhile; wp_reset_postdata(); endif;
                                ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label for="menu-link"><?php echo esc_html__('Link', 'listingpro'); ?></label>
                            <input type="text" class="form-control" name="menu-link" id="menu-link" placeholder="<?php echo esc_html__('link', 'listingpro'); ?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4 select2-dash">
                            <label for="menu-type"><?php echo esc_html__('Menu Type', 'listingpro'); ?></label>
                            <?php
                            if( $menu_types_data && is_array( $menu_types_data ) && !empty( $menu_types_data ) ):
                                $menu_types_data    =   $menu_types_data[0];
                            ?>
                                <select class="form-control select2" name="menu-type" id="menu-type">
                                    <option value="0"><?php echo esc_html__( 'select type', 'listingpro' ); ?></option>
                                    <?php
                                    foreach ( $menu_types_data as $menu_type ):
                                    ?>
                                        <option><?php echo $menu_type['type']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <a href=""><?php echo esc_html__( 'Please add types', 'listingpro' ); ?></a>
                            <?php endif; ?>
                        </div>
                        <div class="col-sm-4 select2-dash">
                            <label for="menu-group"><?php echo esc_html__('Menu group', 'listingpro'); ?></label>
                            <?php
                            if( $menu_groups_data && is_array( $menu_groups_data ) && !empty( $menu_groups_data ) ):
                                $menu_groups_data   =   $menu_groups_data[0];
                            ?>
                                <select name="menu-group" id="menu-group" class="form-control select2">
                                    <option value="0"><?php echo esc_html__( 'select group', 'listingpro' ); ?></option>
                                    <?php
                                    foreach ( $menu_groups_data as $menu_group ):
                                    ?>
                                        <option><?php echo $menu_group['group']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php else: ?>
                                <a href=""><?php echo esc_html__( 'Please add groups', 'listingpro' ); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <label><?php echo esc_html__('Upload Photo', 'listingpro'); ?></label>
                            <div class="upload-field">
                                <?php echo do_shortcode('[frontend-button]'); ?>
                            </div>
                        </div>
                    </div>
                    <button id="lp-save-menu" data-uid="<?php echo $user_id; ?>" class="dash-add-menu add-btn-v2 lp-review-btn btn-second-hover"><?php echo esc_html__('Add', 'listingpro'); ?></button>
                    <div class="ann-err-msg"><?php echo esc_html__('title, listing, & type are required', 'listingpro'); ?></div>
                </form>
            </div>
            <div class="clearfix"></div>
        </div>
	</div>
</div>
												