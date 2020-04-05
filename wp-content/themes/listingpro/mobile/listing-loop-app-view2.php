<?php
	
			$output = null;
			global $listingpro_options;
			$lp_review_switch = $listingpro_options['lp_review_switch'];

			if(!isset($postGridCount)){
				$postGridCount = '0';
			}
			global $postGridCount;			
			$postGridCount++;
			
			$deafaultFeatImg = lp_default_featured_image_listing();
			
			$listing_style = '';
				$listing_style = $listingpro_options['listing_style'];
				if(isset($_GET['list-style']) && !empty($_GET['list-style'])){
					$listing_style = esc_html($_GET['list-style']);
				}
				if(is_front_page()){
					$listing_style = 'col-md-4 col-sm-6';
					$postGridnumber = 3;
				}else{
					if($listing_style == '1'){
						$listing_style = 'col-md-4 col-sm-6';
						$postGridnumber = 3;
					}elseif($listing_style == '3' && !is_page()){
						$listing_style = 'col-md-6 col-sm-12';
						$postGridnumber = 2;
					}else{
						$listing_style = 'col-md-4 col-sm-6';
						$postGridnumber =3;
					}
				}
				if(is_page_template('template-favourites.php')){
					$listing_style = 'col-md-4 col-sm-6';
					$postGridnumber =3;
				}
				$latitude = listing_get_metabox('latitude');
				$longitude = listing_get_metabox('longitude');
				$gAddress = listing_get_metabox('gAddress');
				$isfavouriteicon = listingpro_is_favourite_grids(get_the_ID(),$onlyicon=true);
				$isfavouritetext = listingpro_is_favourite_grids(get_the_ID(),$onlyicon=false);

				$adStatus = get_post_meta( get_the_ID(), 'campaign_status', true );
				$CHeckAd = '';
				$adClass = '';
				if($adStatus == 'active'){
					$CHeckAd = '<span class="listing-pro">'.esc_html__('Ad','listingpro').'</span>';
					$adClass = 'promoted';
				}
				$claimed_section = listing_get_metabox('claimed_section');

				$claim = '';
				$claimStatus = '';
				
				if($claimed_section == 'claimed') {
					if(is_singular( 'listing' ) ){
						$claimStatus = esc_html__('Claimed', 'listingpro');
					}
					$claim = '<span class="verified simptip-position-top simptip-movable" data-tooltip="'. esc_html__('Claimed', 'listingpro').'"><i class="fa fa-check"></i> '.$claimStatus.'</span>';

				}elseif($claimed_section == 'not_claimed') {
					$claim = '';

				}
				$listing_layout = $listingpro_options['listing_views'];
                ?>
					<div class="list_view <?php echo esc_attr($adClass); ?> card1 lp-grid-box-contianer1">
						<div class="lp-grid-box lp-border">
                            <div class="lp-grid-box-thumb">
                                <div class="show-img">
                                    <?php
                                        if ( has_post_thumbnail()) {
                                            $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-thumb4' );
                                                if(!empty($image[0])){
                                                    echo "<a href='".get_the_permalink()."' >
                                                            <img src='" . $image[0] . "' />
                                                        </a>";
                                                }else {
                                                    echo '
                                                    <a href="'.get_the_permalink().'" >
                                                        <img src="'.esc_html__('https://via.placeholder.com/272x270', 'listingpro').'" alt="">
                                                    </a>';
                                                }
                                        }elseif(!empty($deafaultFeatImg)){
											echo "<a href='".get_the_permalink()."' >
													<img src='".$deafaultFeatImg."' />
												</a>";
										}else {
                                            echo '
                                            <a href="'.get_the_permalink().'" >
                                                <img src="'.esc_html__('https://via.placeholder.com/272x270', 'listingpro').'" alt="">
                                            </a>';
                                        }
                                    ?>
                                </div>
                                <div class="hide-img listingpro-list-thumb">
                                    <?php
                                        if ( has_post_thumbnail()) {
                                            $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'listingpro-author-thumb' );
                                                if(!empty($image[0])){
                                                    echo "<a href='".get_the_permalink()."' >
                                                           <img src='" . $image[0] . "' /> 
                                                        </a>";
                                                }else {
                                                    echo '
                                                    <a href="'.get_the_permalink().'" >
                                                        <img src="'.esc_html__('https://via.placeholder.com/63x63', 'listingpro').'" alt="">
                                                    </a>';
                                                }
                                        }elseif(!empty($deafaultFeatImg)){
											echo "<a href='".get_the_permalink()."' >
													<img src='".$deafaultFeatImg."' />
												</a>";
										}else {
                                            echo '
                                            <a href="'.get_the_permalink().'" >
                                                <img src="'.esc_html__('https://via.placeholder.com/63x63', 'listingpro').'" alt="">
                                            </a>';
                                        }
                                    ?>
                                </div>
                            </div>
							<div class="lp-grid-desc-container clearfix">
								<div class="lp-grid-box-description">
                                    <h4 class="lp-h4">
                                        <a href="<?php echo get_the_permalink(); ?>">
                                            <?php echo $CHeckAd; ?>
                                            <?php echo substr(get_the_title(), 0, 10); ?>
                                            <?php echo $claim; ?>
                                        </a>
                                    </h4>
									<div class="clearfix lp-grid-box-bottom-app-view">
										<div class="lp-grid-box-left pull-left">
											<ul>
												<li>
													<?php
														$NumberRating = listingpro_ratings_numbers($post->ID);
														if($NumberRating != 0){
															if($NumberRating <= 1){
																$review = esc_html__('Rating', 'listingpro');
															}else{
																$review = esc_html__('Ratings', 'listingpro');
															}
															echo lp_cal_listing_rate(get_the_ID());
														}else{
															echo lp_cal_listing_rate(get_the_ID());
														}
													?>
												</li>
												<li class="middle">
														<?php echo listingpro_price_dynesty_text(get_the_ID()); ?>
												</li>
												<?php
												$cats = get_the_terms( get_the_ID(), 'listing-category' );
												if( !empty( $cats ) ){
													$catCount = 1;	
													?>
													<li class="grid-view-hide">
														<?php
														foreach ( $cats as $cat ) {
															if($catCount==1){
															$category_image = listing_get_tax_meta($cat->term_id,'category','image');
															if(!empty($category_image)){
																echo '<span class="cat-icon"><img class="icon icons8-Food" src="'.$category_image.'" alt="cat-icon"></span>';
															}
															$term_link = get_term_link( $cat );
															echo '
															<a href="'.$term_link.'">
																'.$cat->name.'
															</a>';
															}
															$catCount++;
														}
														?>
													</li>
												<?php } ?>
												
												
												<?php
												$locs = get_the_terms( get_the_ID(), 'location' );
												if(!empty($locs)){
													$locCount = 1;	
													?>
													<li>
														<?php
														foreach ( $locs as $loc ) {
															if($locCount==1){
															echo '<span class="cat-icon"><i class="fa fa-map-marker" aria-hidden="true"></i></span>';
															$term_link = get_term_link( $loc );
															echo '
															<a href="'.$term_link.'">
																'.$loc->name.'
															</a>';
															}
															$locCount++;
														}
														?>
													</li>
												<?php } ?>
											</ul>
										</div>
									</div>
								</div>
							</div>
                            <div class="clearfix"></div>
						</div>
					</div>