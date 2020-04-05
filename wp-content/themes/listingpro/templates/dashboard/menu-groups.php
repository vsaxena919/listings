<div class="user-recent-listings-inner tab-pane fade in active" id="announcements">
	<div class="tab-header">
		<h3><?php echo esc_html__('Menu Types', 'listingpro'); ?></h3>
	</div>
    <?php
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $menu_groups_data    =   get_user_meta( $user_id, 'user_menu_groups' );


    ?>
	<div class="row lp-list-page-list">
        <div class="announcements-wrap page-innner-container padding-40 lp-border lp-border-radius-8">
            <?php
            if( $menu_groups_data && is_array( $menu_groups_data ) && !empty( $menu_groups_data ) ):
                $menu_groups_data    =   $menu_groups_data[0]
            ?>
            <div class="panel-recent-activity">
                <div class="section-title">
                    <h3><?php echo esc_html__('Recent Menu Groups', 'listingpro'); ?></h3>
                </div>
                <ul class="lp-dash-ann-list">
                    <?php
                    foreach ( $menu_groups_data as $group_data ):
                    ?>
                        <li><?php echo $group_data['group']; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
            <div class="add-new-announcement">
                <div class="section-title">
                    <h3><?php echo esc_html__('Add Menu Group', 'listingpro'); ?></h3>
                </div>
                <form class="form-horizontal">
                    <div class="form-group">
                        <div class="col-sm-4">
                            <input type="text" class="form-control" name="menu-group" id="menu-group" placeholder="<?php echo esc_html__('menu group', 'listingpro'); ?>" />
                        </div>
                        <div class="clearfix"></div>
                    </div>
                    <button id="lp-save-menu-group" data-uid="<?php echo $user_id; ?>" class="dash-add-menu-group add-btn-v2 lp-review-btn btn-second-hover"><?php echo esc_html__('Add', 'listingpro'); ?></button>
                    <div class="ann-err-msg"><?php echo esc_html__('group text is required', 'listingpro'); ?></div>
                </form>
            </div>
        </div>
	</div>
</div>
												