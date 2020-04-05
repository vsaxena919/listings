<div class="user-recent-listings-inner tab-pane fade in active" id="announcements">
	<div class="tab-header">
		<h3><?php echo esc_html__('Menu Types', 'listingpro'); ?></h3>
	</div>
    <?php
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $menu_types_data    =   get_user_meta( $user_id, 'user_menu_types' );


    ?>
	<div class="row lp-list-page-list">
        <div class="announcements-wrap page-innner-container padding-40 lp-border lp-border-radius-8">
            <?php
            if( $menu_types_data && is_array( $menu_types_data ) && !empty( $menu_types_data ) ):
                $menu_types_data    =   $menu_types_data[0]
            ?>
            <div class="panel-recent-activity">
                <div class="section-title">
                    <h3><?php echo esc_html__('Recent Menu Types', 'listingpro'); ?></h3>
                </div>
                <ul class="lp-dash-ann-list">
                    <?php
                    foreach ( $menu_types_data as $menu_data ):
                    ?>
                        <li><?php echo $menu_data['type']; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <div class="add-new-announcement">
                <div class="section-title">
                    <h3><?php echo esc_html__('Add Menu Type', 'listingpro'); ?></h3>
                </div>
                <form class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="menu-type" id="menu-type" placeholder="<?php echo esc_html__('menu type', 'listingpro'); ?>" />
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <button id="lp-save-menu-type" data-uid="<?php echo $user_id; ?>" class="dash-add-menu-type add-btn-v2 lp-review-btn btn-second-hover"><?php echo esc_html__('Add', 'listingpro'); ?></button>
                    <div class="ann-err-msg"><?php echo esc_html__('Type text is required', 'listingpro'); ?></div>
                </form>
            </div>
        </div>
	</div>
</div>
												