<?php
/**
 * Envato Theme Setup Wizard Class
 *
 * Takes new users through some basic steps to setup their theme.
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Envato_Theme_Setup_Wizard' ) ) {
	
	class Envato_Theme_Setup_Wizard {

		
		protected $version = '1.3.0';

		protected $theme_name = '';

		protected $envato_username = '';

	
		protected $oauth_script = '';

		
		protected $step = '';

		protected $steps = array();

		
		protected $plugin_path = '';

		
		protected $plugin_url = '';

		
		protected $page_slug;

		
		protected $tgmpa_instance;

		
		protected $tgmpa_menu_slug = 'tgmpa-install-plugins';

		
		protected $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

		
		protected $page_parent;

		
		protected $page_url;

		
		public $site_styles = array();

		
		private static $instance = null;

		
		public static function get_instance() {
			if ( ! self::$instance ) {
				self::$instance = new self;
			}

			return self::$instance;
		}


		
		public function __construct() {
			$this->init_globals();
			$this->init_actions();
		}

		
		
		
		public function init_globals() {
			$current_theme         = wp_get_theme();
			$this->page_slug       = apply_filters( $this->theme_name . '_theme_setup_wizard_page_slug', $this->theme_name . '-setup' );
			$this->parent_slug     = apply_filters( $this->theme_name . '_theme_setup_wizard_parent_slug', '' );

			// create an images/styleX/ folder for each style here.
			$this->site_styles = array(
                'style1' => 'Style 1',
                'style2' => 'Style 2',
                'style3' => 'Style 3',
            );

			//If we have parent slug - set correct url
			if ( $this->parent_slug !== '' ) {
				$this->page_url = 'admin.php?page=' . $this->page_slug;
			} else {
				$this->page_url = 'themes.php?page=' . $this->page_slug;
			}
			$this->page_url = apply_filters( $this->theme_name . '_theme_setup_wizard_page_url', $this->page_url );

			//set relative plugin path url
			$this->plugin_path = trailingslashit( $this->cleanFilePath( dirname( __FILE__ ) ) );
			$relative_url      = str_replace( $this->cleanFilePath( get_template_directory() ), '', $this->plugin_path );
			$this->plugin_url  = trailingslashit( get_template_directory_uri() . $relative_url );
		}

		
		
		
		
		public function init_actions() {
			if ( apply_filters( $this->theme_name . '_enable_setup_wizard', true ) && current_user_can( 'manage_options' ) ) {
				add_action( 'after_switch_theme', array( $this, 'switch_theme' ) );

				if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
					add_action( 'init', array( $this, 'get_tgmpa_instanse' ), 30 );
					add_action( 'init', array( $this, 'set_tgmpa_url' ), 40 );
				}

				add_action( 'admin_menu', array( $this, 'admin_menus' ) );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
				add_action( 'admin_init', array( $this, 'admin_redirects' ), 30 );
				add_action( 'admin_init', array( $this, 'init_wizard_steps' ), 30 );
				add_action( 'admin_init', array( $this, 'setup_wizard' ), 30 );
				add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
				add_action( 'wp_ajax_envato_setup_plugins', array( $this, 'ajax_plugins' ) );
				add_action( 'wp_ajax_envato_setup_content', array( $this, 'ajax_content' ) );
                
                add_action( 'wp_ajax_listingpro_theme_options', array( $this, 'listingpro_theme_options' ) );
				add_action( 'wp_ajax_setup_content', array( $this, 'setup_content' ) );
				add_action( 'wp_ajax_listingpro_menu', array( $this, 'listingpro_menu' ) );
				add_action( 'wp_ajax_listingpro_homepage', array( $this, 'listingpro_homepage' ) );				
				add_action( 'wp_ajax_listingpro_save_logo', array( $this, 'listingpro_save_logo' ) );
			}
			
			add_action( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 2 );
		}

		/**
		 * After a theme update we clear the setup_complete option. This prompts the user to visit the update page again.
		 */
		public function upgrader_post_install( $return, $theme ) {
			if ( is_wp_error( $return ) ) {
				return $return;
			}
			if ( $theme != get_stylesheet() ) {
				return $return;
			}
			update_option( 'envato_setup_complete', false );

			return $return;
		}

		/**
		 * We determine if the user already has theme content installed. This can happen if swapping from a previous theme or updated the current theme. We change the UI a bit when updating / swapping to a new theme.
		 *
		*/
		public function is_possible_upgrade() {
			return false;
		}

		public function enqueue_scripts() {
		}

		public function tgmpa_load( $status ) {
			return is_admin() || current_user_can( 'install_themes' );
		}

		public function switch_theme() {
			set_transient( '_' . $this->theme_name . '_activation_redirect', 1 );
		}

		public function admin_redirects() {
			if ( ! get_transient( '_' . $this->theme_name . '_activation_redirect' ) || get_option( 'envato_setup_complete', false ) ) {
				return;
			}
			delete_transient( '_' . $this->theme_name . '_activation_redirect' );
			wp_redirect( admin_url( $this->page_url ) );
			exit();
		}

		public function get_default_theme_style() {
			return 'style1';
		}

		/**
		 * Get configured TGMPA instance
		 *
		 * @access public
		 * @since 1.1.2
		 */
		public function get_tgmpa_instanse() {
			$this->tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		}

		/**
		 * Update $tgmpa_menu_slug and $tgmpa_parent_slug from TGMPA instance
		 *
		 * @access public
		 * @since 1.1.2
		 */
		public function set_tgmpa_url() {

			$this->tgmpa_menu_slug = ( property_exists( $this->tgmpa_instance, 'menu' ) ) ? $this->tgmpa_instance->menu : $this->tgmpa_menu_slug;
			$this->tgmpa_menu_slug = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_menu_slug', $this->tgmpa_menu_slug );

			$tgmpa_parent_slug = ( property_exists( $this->tgmpa_instance, 'parent_slug' ) && $this->tgmpa_instance->parent_slug !== 'themes.php' ) ? 'admin.php' : 'themes.php';

			$this->tgmpa_url = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_url', $tgmpa_parent_slug . '?page=' . $this->tgmpa_menu_slug );

		}

		/**
		 * Add admin menus/screens.
		 */
		public function admin_menus() {

			if ( $this->is_submenu_page() ) {
				//prevent Theme Check warning about "themes should use add_theme_page for adding admin pages"
				$add_subpage_function = 'add_submenu' . '_page';
				$add_subpage_function( $this->parent_slug, esc_html__( 'Setup Wizard', 'listingpro' ), esc_html__( 'Setup Wizard', 'listingpro' ), 'manage_options', $this->page_slug, array(
					$this,
					'setup_wizard',
				) );
			} else {
				add_theme_page( esc_html__( 'Setup Wizard', 'listingpro' ), esc_html__( 'Setup Wizard', 'listingpro' ), 'manage_options', $this->page_slug, array(
					$this,
					'setup_wizard',
				) );
			}

		}


		/**
		 * Setup steps.
		 *
		 * @since 1.1.1
		 * @access public
		 * @return array
		 */
		public function init_wizard_steps() {

			$this->steps = array(
				'introduction' => array(
					'name'    => esc_html__( 'Introduction', 'listingpro' ),
					'view'    => array( $this, 'envato_setup_introduction' ),
					'handler' => array( $this, '' ),
				),
			);
            $this->steps['editor_platform'] = array(
                'name'    => esc_html__( 'Editor', 'listingpro' ),
                'view'    => array( $this, 'editor_plateform' ),
                'handler' => array( $this, '' ),
			);
			if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
				$this->steps['default_plugins'] = array(
					'name'    => esc_html__( 'Plugins', 'listingpro' ),
					'view'    => array( $this, 'envato_setup_default_plugins' ),
					'handler' => '',
				);
			}			

			if( count($this->site_styles) > 1 ) {
				$this->steps['style'] = array(
					'name'    => esc_html__( 'Demos', 'listingpro' ),
					'view'    => array( $this, 'envato_setup_color_style' ),
					'handler' => array( $this, 'envato_setup_color_style_save' ),
				);
			}
			$this->steps['default_content'] = array(
				'name'    => esc_html__( 'Content' , 'listingpro'),
				'view'    => array( $this, 'envato_setup_default_content' ),
				'handler' => '',
			);			
			$this->steps['next_steps']      = array(
				'name'    => esc_html__( 'Ready!', 'listingpro' ),
				'view'    => array( $this, 'envato_setup_ready' ),
				'handler' => '',
			);

			$this->steps = apply_filters( $this->theme_name . '_theme_setup_wizard_steps', $this->steps );

		}

		/**
		 * Show the setup wizard
		 */
		public function setup_wizard() {
			if ( empty( $_GET['page'] ) || $this->page_slug !== $_GET['page'] ) {
				return;
			}

			$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

			wp_register_script( 'jquery-blockui', $this->plugin_url . 'js/jquery.blockUI.js', array( 'jquery' ), '2.70', true );
			
			wp_register_script( 'envato-setup', $this->plugin_url . 'js/envato-setup.js', array(
				'jquery',
				'jquery-blockui',
			), $this->version );
			wp_localize_script( 'envato-setup', 'envato_setup_params', array(
				'tgm_plugin_nonce' => array(
					'update'  => wp_create_nonce( 'tgmpa-update' ),
					'install' => wp_create_nonce( 'tgmpa-install' ),
				),
				'tgm_bulk_url'     => admin_url( $this->tgmpa_url ),
				'ajaxurl'          => admin_url( 'admin-ajax.php' ),
				'wpnonce'          => wp_create_nonce( 'envato_setup_nonce' ),
				'verify_text'      => '',
			) );

			//wp_enqueue_style( 'envato_wizard_admin_styles', $this->plugin_url . '/css/admin.css', array(), $this->version );
			wp_enqueue_style( 'envato-setup', $this->plugin_url . 'css/envato-setup.css', array(
				'wp-admin',
				'dashicons',
				'install',
			), $this->version );
			
			//enqueue style for admin notices
			wp_enqueue_style( 'wp-admin' );

			wp_enqueue_media();
			wp_enqueue_script( 'media' );

			ob_start();
			$this->setup_wizard_header();
			$this->setup_wizard_steps();
			$show_content = true;
			echo '<div class="envato-setup-content">';
			if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
				$show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
			}
			if ( $show_content ) {
				$this->setup_wizard_content();
			}
			echo '</div>';
			$this->setup_wizard_footer();
			exit;
		}

		public function get_step_link( $step ) {
			return add_query_arg( 'step', $step, admin_url( 'admin.php?page=' . $this->page_slug ) );
		}

		public function get_next_step_link() {
			$keys = array_keys( $this->steps );

			return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ], remove_query_arg( 'translation_updated' ) );
		}

		/**
		 * Setup Wizard Header
		 */
	public function setup_wizard_header() {
		?>
		<!DOCTYPE html>
		<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
		<head>
			<meta name="viewport" content="width=device-width"/>
			<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
			<?php
			// avoid theme check issues.
			echo '<t';
			echo 'itle>' . esc_html__( 'Listingpro &rsaquo; Setup Wizard', 'listingpro' ) . '</ti' . 'tle>'; ?>
			<?php wp_print_scripts( 'envato-setup' ); ?>
			<?php do_action( 'admin_print_styles' ); ?>
			<?php do_action( 'admin_print_scripts' ); ?>
			<?php do_action( 'admin_head' ); ?>
		</head>
		<body class="envato-setup wp-core-ui">
		<h1 id="wc-logo">
			<a href="http://themeforest.net/user/cridiostudio/portfolio" target="_blank">				
				<img src="<?php echo get_template_directory_uri(); ?>/assets/images/cs-logo.png" alt="Cridio Studio" />
			</a>
		</h1>
		<?php
		}

		/**
		 * Setup Wizard Footer
		 */
		public function setup_wizard_footer() {
			?>
			<?php if ( 'next_steps' === $this->step ) : ?>
				<a class="wc-return-to-dashboard"
				   href="<?php echo esc_url( admin_url() ); ?>"><?php esc_html_e( 'Return to the WordPress Dashboard', 'listingpro' ); ?></a>
			<?php endif; ?>
			</body>
			<?php
			@do_action( 'admin_footer' ); // this was spitting out some errors in some admin templates. quick @ fix until I have time to find out what's causing errors.
			do_action( 'admin_print_footer_scripts' );
			?>
			</html>
			<?php
		}

		/**
		 * Output the steps
		 */
		public function setup_wizard_steps() {
			$ouput_steps = $this->steps;
			array_shift( $ouput_steps );
			?>
			<ol class="envato-setup-steps">
				<?php foreach ( $ouput_steps as $step_key => $step ) : ?>
					<li class="<?php
					$show_link = false;
					if ( $step_key === $this->step ) {
						echo 'active';
					} elseif ( array_search( $this->step, array_keys( $this->steps ) ) > array_search( $step_key, array_keys( $this->steps ) ) ) {
						echo 'done';
						$show_link = true;
					}
					?>"><?php
						if ( $show_link ) {
							?>
							<a href="<?php echo esc_url( $this->get_step_link( $step_key ) ); ?>"><?php echo esc_html( $step['name'] ); ?></a>
							<?php
						} else {
							echo esc_html( $step['name'] );
						}
						?></li>
				<?php endforeach; ?>
			</ol>
			<?php
		}

		/**
		 * Output the content for the current step
		 */
		public function setup_wizard_content() {
			isset( $this->steps[ $this->step ] ) ? call_user_func( $this->steps[ $this->step ]['view'] ) : false;
		}

		/**
		 * Introduction step
		 */
		public function envato_setup_introduction() {

			if ( $this->is_possible_upgrade() ) {
				?>
				<h1><?php printf( esc_html__( 'Welcome to the Easy Setup Assistant for %s.', 'listingpro' ), wp_get_theme() ); ?></h1>
				<p><?php esc_html_e( 'It looks like you may have recently upgraded to this theme. Great! This setup wizard will help ensure all the default settings are correct. It will also show some information about your new website and support options.', 'listingpro' ); ?></p>
				<p class="envato-setup-actions step">
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
					   class="button-primary button button-large button-next"><?php esc_html_e( 'Let\'s Go!', 'listingpro' ); ?></a>
					<a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>"
					   class="button button-large"><?php esc_html_e( 'Not right now', 'listingpro' ); ?></a>
				</p>
				<?php
			} else if ( get_option( 'envato_setup_complete', false ) ) {
				?>
				
				<div class="envato-setup-first-step-container envato-setup-first-step-containerwelcome">
					
					<h1><?php printf( esc_html__( 'Welcome to the Easy Setup Assistant! for %s.', 'listingpro' ), wp_get_theme() ); ?></h1>
					<p><?php printf( esc_html__( 'It looks like you have already run the setup wizard.', 'listingpro' ), wp_get_theme() ); ?></p>
				</div>
				<img style="width:100%;"src="<?php echo get_template_directory_uri().'/assets/images/setup/setup1.jpg' ?>" />
				
				
				
				<p class="envato-setup-actions step" style="margin-top:-90px;">
					
					<a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>"
					   class="button button-large"><?php esc_html_e( 'LATER', 'listingpro' ); ?></a>
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
					class="button-primary button button-next button-large"><?php esc_html_e( 'Run Setup Wizard Again', 'listingpro' ); ?></a>   
				</p>
				<?php
			} else {
				?>
				
				<div class="envato-setup-first-step-container">
					<h1><?php printf( esc_html__( 'Easy Setup! Click! Click! Click!', 'listingpro' ), wp_get_theme() ); ?></h1>
					<p><?php printf( esc_html__( 'This wizard will help you kickstart with all the necessary tools. ', 'listingpro' ), wp_get_theme() ); ?></p>
				</div>
				<img style="width:100%;"src="<?php echo get_template_directory_uri().'/assets/images/setup/setup1.jpg' ?>"/>
				<h1 class="firs-step-h1"><?php printf( esc_html__( 'Ready to begin the setup?', 'listingpro' ), wp_get_theme() ); ?></h1>
				
				<p class="envato-setup-actions step text-center">
					<a href="<?php echo esc_url( wp_get_referer() && ! strpos( wp_get_referer(), 'update.php' ) ? wp_get_referer() : admin_url( '' ) ); ?>"
					   class="button button-large"><?php esc_html_e( 'LATER', 'listingpro' ); ?></a>
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
					   class="button-primary button button-large button-next"><?php esc_html_e( 'START', 'listingpro' ); ?></a>
					
				</p>
				<?php
			}
		}

		public function editor_plateform(  ) {
            update_option('lp_update_compatible', true);
			?>
                <img style="width:100%;"src="<?php echo get_template_directory_uri().'/assets/images/setup/editor.jpg' ?>"/>
                <p class="first-step-platform">
                    <select id="setup_platform">
                        <option>WP Bakery</option>
                        <option>Elementor</option>
                    </select>
                </p>
				<p class="envato-setup-actions step text-center step-editor-platform">
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
					   class="button-primary button button-large button-next"><?php esc_html_e( 'NEXT', 'listingpro' ); ?>
                    </a>
					
				</p>
            <?php
		}
        
        public function filter_options( $options ) {
			return $options;
		}




		private function _get_plugins() {
			$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
			$plugins  = array(
				'all'      => array(), // Meaning: all plugins which still have open actions.
				'install'  => array(),
				'update'   => array(),
				'activate' => array(),
			);

			foreach ( $instance->plugins as $slug => $plugin ) {
				if ( class_exists('Redux') && false === $instance->does_plugin_have_update( $slug ) ) {
					// No need to display plugins if they are installed, up-to-date and active.
					continue;
				} else {
					$plugins['all'][ $slug ] = $plugin;

					if ( ! $instance->is_plugin_installed( $slug ) ) {
						$plugins['install'][ $slug ] = $plugin;
					} else {
						if ( false !== $instance->does_plugin_have_update( $slug ) ) {
							$plugins['update'][ $slug ] = $plugin;
						}

						if ( $instance->can_plugin_activate( $slug ) ) {
							$plugins['activate'][ $slug ] = $plugin;
						}
					}
				}
			}

			return $plugins;
		}

		/**
		 * Page setup
		 */
		public function envato_setup_default_plugins() {

			tgmpa_load_bulk_installer();
			// install plugins with TGM.
			if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
				die( 'Failed to find TGM' );
			}
			$url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'envato-setup' );
			$plugins = $this->_get_plugins();

			// copied from TGM

			$method = ''; // Leave blank so WP_Filesystem can populate it as necessary.
			$fields = array_keys( $_POST ); // Extra fields to pass to WP_Filesystem.

			if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
				return true; // Stop the normal page form from displaying, credential request form will be shown.
			}

			// Now we have some credentials, setup WP_Filesystem.
			if ( ! WP_Filesystem( $creds ) ) {
				// Our credentials were no good, ask the user for them again.
				request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );

				return true;
			}

			/* If we arrive here, we have the filesystem */

			?>
			
			<div class="envato-setup-first-step-container envato-setup-first-step-containerwelcome plugins-container-heading">
					
				<h1><?php printf( esc_html__( "First, let us help you unpack.", 'listingpro' ), wp_get_theme() ); ?></h1>
				
			</div>
			<form method="post" class="plugins-container">

				<?php
				$plugins = $this->_get_plugins();
				if ( count( $plugins['all'] ) ) {
					?>
						<img style="width:100%;"src="<?php echo get_template_directory_uri().'/assets/images/setup/setup2.jpg' ?>" />
					
				<div class="plugins-container-inner">
					
						<ul class="envato-wizard-plugins">
							<?php foreach ( $plugins['all'] as $slug => $plugin ) { ?>
								<li data-slug="<?php echo esc_attr( $slug ); ?>">
								<span>
										<?php
										$keys = array();
										if ( isset( $plugins['install'][ $slug ] ) ) {
											$keys[] = '';
										}
										if ( isset( $plugins['update'][ $slug ] ) ) {
											$keys[] = '';
										}
										if ( isset( $plugins['activate'][ $slug ] ) ) {
											$keys[] = '';
										}
										echo implode( ' and ', $keys ) . ' ';
										?>
									</span>
								<?php echo esc_html( $plugin['name'] ); ?>
									
									<div class="spinner"></div>
								</li>
							<?php } ?>
						</ul>
						<?php
					} else {
						echo '<div class="sss">';
						echo '<img style="width:100%;"src="'.get_template_directory_uri().'/assets/images/setup/setup2.jpg">';	
						echo '<h1 class="firs-step-h1"  style="margin-top:-90px;">';
						printf( esc_html__( 'Good news! All plugins are already installed and up to date. Please continue.', 'listingpro' ) ); 
						echo '</h1>';
						echo '</div>';
					} ?>

					
					<h1 class="firs-step-h1"><?php printf( esc_html__( 'Stay tight while we install all necessary tools for your new directory.', 'listingpro' ) ); ?></h1>
				
					<p class="envato-setup-actions step">
						
						<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
						   class="button button-large button-next"><?php esc_html_e( 'LATER', 'listingpro' ); ?></a>
						<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
						   class="button-primary button button-large button-next"
						   data-callback="install_plugins"><?php esc_html_e( 'Yes! i am ready.','listingpro' ); ?></a>   
						<?php wp_nonce_field( 'envato-setup' ); ?>
					</p>
				</div>
			</form>
			<?php
		}


		public function ajax_plugins() {
			if ( ! check_ajax_referer( 'envato_setup_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
				wp_send_json_error( array( 'error' => 1, 'message' => esc_html__( 'No Slug Found', 'listingpro' ) ) );
			}
			$json = array();
			// send back some json we use to hit up TGM
			$plugins = $this->_get_plugins();
			// what are we doing with this plugin?
			foreach ( $plugins['activate'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => admin_url( $this->tgmpa_url ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-activate',
						'action2'       => - 1,
						'message'       => '',
					);
					break;
				}
			}
			foreach ( $plugins['update'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => admin_url( $this->tgmpa_url ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-update',
						'action2'       => - 1,
						'message'       => '',
					);
					break;
				}
			}
			foreach ( $plugins['install'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => admin_url( $this->tgmpa_url ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-install',
						'action2'       => - 1,
						'message'       => '',
					);
					break;
				}
			}

			if ( $json ) {
				$json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
				wp_send_json( $json );
			} else {
				wp_send_json( array( 'done' => 1, 'message' => '' ) );
			}
			exit;

		}


		public function envato_setup_default_content() {
			?>
				
			
			<div class="envato-setup-first-step-container envato-setup-first-step-containerwelcome lp-step2">
					
				<h1><?php printf( esc_html__( "Let us help you jump start with dummy content.", 'listingpro' ), wp_get_theme() ); ?></h1>
				
			</div>
			<img style="width:100%;"src="<?php echo get_template_directory_uri().'/assets/images/setup/step2.jpg' ?>"/>
			<form method="post">
				<?php if ( $this->is_possible_upgrade() ) { ?>
					
				<?php } else { ?>
					
				<?php } ?>
				
				<?php
				
				 
					
					//set_demo_data( $solitaire_content );
				?>
				<div class="content-importer-response content-importer-response-outer">
					<div id="importer-response" class="clear pos-relative">
						<span class="res-text"></span>
						<img class="loadinerSearch" width="30px" src="<?php echo get_template_directory_uri().'/assets/images/ajax-load.gif' ?>">
						<img class="checkImg" width="30px" src="<?php echo get_template_directory_uri().'/assets/images/check-img.png' ?>">
					</div>
					<div id="importer-response-menu" class="clear pos-relative">
						<span class="res-text"></span>
						<img class="loadinerSearch" width="30px" src="<?php echo get_template_directory_uri().'/assets/images/ajax-load.gif' ?>">
						<img class="checkImg" width="30px" src="<?php echo get_template_directory_uri().'/assets/images/check-img.png' ?>">
					</div>
					<div id="importer-response-homepage" class="clear pos-relative">
						<span class="res-text"></span>
						<img class="loadinerSearch" width="30px" src="<?php echo get_template_directory_uri().'/assets/images/ajax-load.gif' ?>">
						<img class="checkImg" width="30px" src="<?php echo get_template_directory_uri().'/assets/images/check-img.png' ?>">
					</div>
					<div id="importer-response-themeoptions" class="clear pos-relative">
						<span class="res-text"></span>
						<img class="loadinerSearch" width="30px" src="<?php echo get_template_directory_uri().'/assets/images/ajax-load.gif' ?>">
						<img class="checkImg" width="30px" src="<?php echo get_template_directory_uri().'/assets/images/check-img.png' ?>">
					</div>
					<div class="clear"></div>
				</div>
				<h1 class="firs-step-h1"><?php printf( esc_html__( 'Are you ready to import the dummy content?', 'listingpro' ) ); ?></h1>
				<p class="envato-setup-actions step">
					
					<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
					   class="button button-large button-next button-next-skip"><?php esc_html_e( 'Skip this step', 'listingpro' ); ?></a>
					<a href="#" class="listingpro-import-content button button-large"><?php esc_html_e( 'Import Content', 'listingpro' ); ?></a>
					<?php wp_nonce_field( 'envato-setup' ); ?>
				</p>
			</form>
			<?php
		}
		
        public function listingpro_theme_options() {
			
			$themeSelectedStyle = get_theme_mod('dtbwp_site_style',$this->get_default_theme_style());
			$file = get_template_directory().'/include/setup/content/'.$themeSelectedStyle.'/themeOptions.json';
			$data = file_get_contents( $file );
			$data = json_decode( $data, true );
            
			$theme_option_name       =  'listingpro_options';
			 
			if ( is_array( $data ) && !empty( $data ) ) {
			  $data = apply_filters( 'solitaire_theme_import_theme_options', $data );
			  update_option($theme_option_name, $data);
			  die('Theme Options imported');
			} else {
				die('Error in theme option');
			}

		}
        
        
		public function setup_content() {
            
        $themeSelectedStyle = get_theme_mod('dtbwp_site_style',$this->get_default_theme_style());
        $file = get_template_directory().'/include/setup/content/'.$themeSelectedStyle.'/content.xml';
        $lp_editor_platform =   'WP Bakery';
        if(isset($_COOKIE['lp_editor_platform'])) {
            $lp_editor_platform =   $_COOKIE['lp_editor_platform'];
        }
        if($lp_editor_platform == 'WP Bakery'){
            $file = get_template_directory().'/include/setup/content/'.$themeSelectedStyle.'/wp-bakery-content.xml';
        }else{
            $file = get_template_directory().'/include/setup/content/'.$themeSelectedStyle.'/elementor-content.xml';
        }
      if ( !defined('WP_LOAD_IMPORTERS') ) define('WP_LOAD_IMPORTERS', true);

		require_once ABSPATH . 'wp-admin/includes/import.php';

		$importer_error = false;

		if ( !class_exists( 'WP_Importer' ) ) {

			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
  
			if ( file_exists( $class_wp_importer ) ){

				require_once($class_wp_importer);

			} else {

				$importer_error = true;

			}

		}

		if ( !class_exists( 'WP_Import' ) ) {

			$class_wp_import = get_template_directory().'/include/setup/importer/importer/wordpress-importer.php';

			if ( file_exists( $class_wp_import ) ) 
				require_once($class_wp_import);
			else
				$importer_error = true;

		}

		if($importer_error){
			ob_start();
			$msg = "Error on import";
			$msg = ob_get_contents();
			ob_end_clean();
			
			die($msg);

		} 
		else {
	  
				if(!is_file( $file )){

					ob_start();
					$msg = "Something went wrong";
					$msg = ob_get_contents();
					ob_end_clean();
					die($msg);

				} else {

				   $wp_import = new WP_Import();
				   $wp_import->fetch_attachments = true;
				   ob_start();
					$res=$wp_import->import( $file );
					$res = ob_get_contents();
					ob_end_clean();
					$msg = 'Content imported success';
				   die($msg);
				}

			}

		}
		
		public function listingpro_menu(){
			
				/*
					$themeSelectedStyle = get_theme_mod('dtbwp_site_style',$this->get_default_theme_style());
					$file = get_template_directory().'/include/setup/content/'.$themeSelectedStyle.'/content.xml';
				*/

				$top_bar_menu = get_term_by('name', 'Top', 'nav_menu');
				$primary_menu = get_term_by('name', 'Main', 'nav_menu');
				$footer_menu = get_term_by('name', 'Footer', 'nav_menu');
				$inner_menu = get_term_by('name', 'Inner', 'nav_menu');
				set_theme_mod( 'nav_menu_locations', array(
						'top_menu' => $top_bar_menu->term_id,
						'primary' => $primary_menu->term_id,
						'primary_inner' => $inner_menu->term_id,
						'footer_menu' => $footer_menu->term_id,
					)
				);
				die('Menu setup successfull');
		}
		
		
		public function listingpro_homepage(){

			/*
				$themeSelectedStyle = get_theme_mod('dtbwp_site_style',$this->get_default_theme_style());
				$file = get_template_directory().'/include/setup/content/'.$themeSelectedStyle.'/content.xml';
			*/

			$homepage = get_page_by_title( 'Home' );
            
            if( $homepage != NULL ) {
				$homepageID = $homepage->ID;
                update_option( 'show_on_front', 'page' );
                update_option( 'page_on_front', $homepageID );
                update_option( 'page_for_posts', 35 );
                
            }
			
			$automotiveIcon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAACxElEQVRoge2YK28bURCFAwIKCgwCDKzKUgMK3CggICBgQUBBQX9CYEBAQcECg0oFBfkBaVVQUrWgoMDAMKAgICCgaio1wJUCFhgYFBQYfAU7q0x2r/dh38f2cSRL9tzZ2TPeO7P3zNraf/zDAHaBn9zgF/AkNK/GAF5RxLvQvBoBGADXQn4AbMr3KbATml8lgG3gVD2BqVqbKPtZqxICDoAxEAPPpA4AEuAY6CvfLvBSPam5XBNLjMNQSfQVcY0XwHrJdevAUBLRmAMPfOaQEfogBMbAG+CiSVcCHskWO5EYAGOXnE0kNtU/ub3AZw8YSYFP5Xu0bDwnADrAldz4G9DNrT81bJ1s+wxzvneBz7Ke5GP5SKarkjlQ9j0hPJda6MknVslFyj9SSfivESFxKiR2lW0ktqHBfyhrI2XbEduZL94FAN+FxH1luxTblsF/S9Yule2e2Ca+eBdQkcjA4P9Q1r4qW5bID0+0i6jYWrHBPy7ZWue+eOdJLSr2aMlivyZAx7pD2nbB3H6zojbhec63A5yrWB2fidR5IUbUfyH2q+I5A8UjykfgcYPr9yXGiaor+0cUKhQeiw+NhfeHIfYRvg6NwFsDyU85n0NuH+MzchOxbSjfDfHJamsuPtkx/shFEkspPNJWeqESn6i1K2X/woJ6sZWAFYVHet5KDH/EDNh3QdyZwqPG1rSVhFOFR7rNErWeICcAbI6KCKjwrD0tAio8bI6KCKDwcDUqwrHCw+eoCEcKD9+jItJ9CbffwJmtZ/DvyZreFh2DzW8jsZzITH77byQ4UHj4biQ4VHglse2OivCg8PAxKsKDwsNRIzHd3KnCs5zIrIyIU4WHz1ERjhQeoUdFWFB4tGlUxAoKj7aNilhBM9CmURElCq/GtX/GqKgOaPuoaFnQllGRLeB7VOQSwGtD/bwPzasxVmkkfxV+AwP2xIZE9n1cAAAAAElFTkSuQmCC';
			$hotelIcon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAACW0lEQVRoge2VoVMjMRjFEYiKCgSi4kRFBaICgThxAoE4eQKBqEAgEAgEAoFgBnGioqICUXHiBAJRwR+ARCArEIgTiBMnEBUViN+JfJ2GXBI2u5u5zE7eTKed7719ydt8X7q29gGAc6Dl4QdAz8N/BnY9fAc49vDrwMVH+/QC2ADmwJFHMwNGHv4WmHr4K+AXsO7g94EF0Anb/erhS+AehRdgCJzIG9yVk7oRfgGMgFOgB2wDZ8AYeBPNtdS2RXMqz7wKPwUugK/yAo+B7xIS4EH2tB8SZEsz0HEmfEeMTUykFVpyEibugLZoRhb+CfgkaxxpL2GJ30A/9FR6hsnQ4Nuotlvi3uLxqPHPGO1jCds2+EuDDwshJl+0DbwBNwa/KfyLBJpZPP5on7kj6ILV6W8Z/Fg7KYBvZYIMgEP53QfGBr+H6ukWqtV+6m8U1efX8r2BaiVzoxOgi2q1c2DPEmRHfh/gud18Qbqy0Iz/jxnwA+iGhjjgff+ngjkwCDmJOWouxpQZsJqBuniGEmZBkZNBtRMYM5ECWA3/pIh4ORN9rTb1HPetxSOKHnXpgOWGtAUBwFZzweURQ+/yCApSRBu7noPoNRdcHjH0VYMkMeyVg6SEHERqzWgtzyJW05j6WoIU0cau5yB6zQWXRwx91SDNGPaUkINIrRmt5VnEahpTX0uQItrY9RxEr7ng8oihrxqkGcOeEnIQqTWjtTyLWE1j6msJUkQbu56D6DUX6tpwEf+qQUKHNzRImv8joUHq8C4vLOGRg5Tx8PRqUigSxDd4qeCfCyYjI8OPvzFqu2/pZoJGAAAAAElFTkSuQmCC';
			$realestateIcon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAADmwAAA5sBPN8HMQAAABl0RVh0U29mdHdhcmUAd3d3Lmlua3NjYXBlLm9yZ5vuPBoAAAPFSURBVGiB7dhPaJd1HAfw32+mqQkbhijSXKIuxC7tUFnzJjQED8FWdKmg6CxGeOjSqSjqEHTIW3ksMEqhwDrUzHktm0JZukRTGQMT1vwzXx1+n6d9tz3bfvv9nu23tb3hgYfn8/f9/fv5PKXSMpYoUMbzOBvPcyg3Oq9ZAT3oNxn96Gl0fjMCj+J4kvgAXornz+T7cexqdL6TgG34DPci0at4DSsTnZXx7Wro3AubbY3MPUuuGe9gOJIbwQdYH/Iyno2nHN8eDJ2RsBnG22huBIE1eAs3I5k7OIzNiU5PbPIMZ9P9gc1hcyfkN8PnmvkgUMYL+CNJ8AQeS3R24qtEfjGeDF9iZ6LfET4y/B4x5uaEixE+lwTsxZOJfKsp9omp98fWxH53+MydwSIIdOJUEuBX7E/kLfgQt0I+HEtkXY6vdSHL9tStsG1OdPZHjAyn0FkPgVZ8gtFwOIQ3sDrkK/AqLiej/Dm2V+F7e+hms3c5fK0I+eqINRTy0cjloVqIDISTEbwvTqKQ7cMvyaj9iN01xHhqwmyfwb5Evj5iZyfcxVqIZGhLvnXgu0T2G7rVsTFVDpDu8JXhW3QkOm2ZoGYi8b4Dx5JAV/AimmolkBOvKXxeSeIcw46J+dREZAJu4BDWFkUgJ+4DEePvvARqcdib46dtZstikC6nBN/X67S20agT08UtbE03GkueyF+lUumVUum/4zIrCPvjfXF0fqLcUClX+nI2YR+enoO4xe5NPIKjSeKX8HI8l5LvR9FeYNxiiOBhlSo1q7eumbrzuxY6o2FT91FdNxGsxZvGLqXb+AgbQt6kUlZ0i9sdG0LndtjcCB81X541E8H9KrfqYDK6h9Ga6EzsS84ZX9a3hk02i4Phc9W8EEEXfk4S7JP0AWjHF4l8wFiFLGTtiX4nTifyn9A1Z0SwJyfg3kS+CUeSEb5ucud3PZnBI9iU2O8NnxlOY09hRGIJpAkO4kC2BLAKrxtrbu7iY2zM8bVRZTndDd0hHJzg64DxS/ZTMzRK1RLJlsU/eBctiawb55NR/FoVP9RUftB9k9idR3cib8F7EZMZGqVqiWRIG6cncDKRncEzMxHI8d1lfAfZi8cTeVs1Sc6KSLzvMv6XzAV1lh7GSpkLid8T2cwWTmQCCm+cVO6kQ+F7Eoogktc4bSmKQE68LTnxpm2UqiJSl0GNmG2c6fSXfD+y4LBMZKFhmchCw/+GyH2NTiBFPXfXYpyRH/I+LqgZKZfLNReli3FGcjHtjMxHvVUUppqRk/OYQ+6aX8Zix7+4yt1QC9VhFQAAAABJRU5ErkJggg==';
			$restaurantIcon = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADIAAAAyCAYAAAAeP4ixAAADfUlEQVRoge2ZIVDjQBSGTyAqKioqKhAVFQgEAoFAVCAQiAoEAoFAIE6cQCAqOnMCgUAgKpEIBOLECQSiouIE4kQFAlFRgUAgEBXfifyZ2wvJZneTwDHTnck03ex7+7683fdet1++LNqiLdqnaUANWAcOgIGufWCjDOVNoFmCnbY51oAL4ImoPQJ3uibACzAuBASMgHvgW4m2x7oPgV8y/g7YA2op45rAEJgDOyETHQHPmnBeFgywqjf8ApwBK45yp8A0DdYm1BTEib7vlgED7AjgDmh7ytaAV6DrI7QHzEz6ojBAT/KnwFKgjnH8cl0FzoCfKf1BMEBbHj71kUvRcwkMfAR+AMOMZ94wChqjUE8YerxBfgN9y3NnGKCjyLTmbEC2rhFw7CNwneURY4wTDNAHHpwnt+t69grBwDFw7zAuF0bL4dJ58mw924pa7gka2NJyyBXKgwG+A7ceNmfNcw1c+QrViMoD6/IyxmfCKKE+FtnoymuvwHaI8IZPWZAFAzRkRM/biL86joFpqHycT6ZAy3H8GxigLo/cBNrQUHJ2T4QpSmoKeZMQGEGMZIhTTZWib6j53WusDEX1AjAPBSGOpKcbIp+msAhMUOiVR+fAYYi8TXERmPNEf0v55U0UApaBGwWIciGMSQrDCGKiIDLX/aWukfLXhBJKmiph4o07kp6WQmsMcgZsVgqQMKwIzAyoV22jcwuE6RH9QjzPH/2OTUvj1RNm87+CSSS78aeESWZs12VGVPzFBxofC5OESPRbl5lA9o3vHwNjgajpc1XPp0RHPo+Cu8Yo44lOFwcfApMFoWcjQcTJbkx0dnugSDUzYTRu3ZB/HxiLJ5aJKuQlA2KUzBPaR//AqL+HDjkqh8nxxBDo2iBsMJJrGWOqgbF4ooOOPF0gbDDq7wNHlcDkeOIr0TmuM4QNRtFsyQga5cDkeKKre28IG4z6r1BpXxgmxxNbRMVfMIQNxvhsy0PhMFKc5ol4DReGsMGo/yae34DxOiqND+a6if5O6J4IhdGzrjyzS5RY265K+8DE+L4MXOi+dIg8GO2Zhu6fgF1XhcdE5UW8Vus45omqYIznz7ge8hH9PTyP94MxQaUQiblm2iMdo3+gfdLwUXYimLjwA7itGsKYf4Xon+S5gCYU+Ed3XW/hHNirwN68+WtEBedQdnTypRZt0RYtq/0BT5ivO4NOzvMAAAAASUVORK5CYII=';
			$servicesIcon = 'data:image/png;base64,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';
			
			
			update_term_meta( 14, 'lp_category_image', $automotiveIcon );
			update_term_meta( 12, 'lp_category_image', $hotelIcon );
			update_term_meta( 16, 'lp_category_image', $realestateIcon );
			update_term_meta( 37, 'lp_category_image', $restaurantIcon );
			update_term_meta( 40, 'lp_category_image', $servicesIcon );			
			
			
			die('Home page setup successfully');
		}
		
		
		
		
		public $logs = array();

		public function log( $message ) {
			$this->logs[] = $message;
		}

		public $errors = array();

		public function error( $message ) {
			$this->logs[] = 'ERROR!!!! ' . $message;
		}


		/* for demo styles */
		public function envato_setup_color_style() {

			?>
			
			<div class="envato-setup-first-step-container envato-setup-first-step-containerwelcome plugins-container-heading">
					
				<h1><?php printf( esc_html__( "Which Pre-Built Demo Style You Like?", 'listingpro' ), wp_get_theme() ); ?></h1>
				
			</div>
			<img style="width:100%;"src="<?php echo get_template_directory_uri().'/assets/images/setup/setup3.jpg' ?>"/>
           
            <form method="post" class="lp-demo-content-import-container">
                <div class="plugins-container-inner">
					
					
					<div class="theme-presets lp-select-demo-outer">
						<ul>
							<?php
							$current_style = get_theme_mod( 'dtbwp_site_style', $this->get_default_theme_style() );
							$stylecls = '';
							foreach ( $this->site_styles as $style_name => $style_data ) {
								?>
								<li class="lp-imp-demo">
									
									<div class="">
										<div class="lp-select-demo">
											<div class="lp-select-demo-image">
												<a href="#" data-style="<?php echo esc_attr( $style_name ); ?>"></a>
												<img style="width:100%;" src="<?php echo get_template_directory_uri().'/include/setup/content/demos/'.$style_name.'/style.jpeg' ?>"/>
												<?php if('style1' == $style_name){ 
													$stylecls = 'opacity:1;visibility:visible';			
												}else{
													$stylecls = '';
												} ?>
												<div class="lp-select-demo-image-overlay" style="<?php echo $stylecls; ?>"></div>
												<div class="lp-select-demo-image-overlay-link"  style="<?php echo $stylecls; ?>">
												
													<a href="" class="lp_current_demo"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAVnSURBVHhe7ZxbiFVVHMZnHPOCSjWZSUYQYgkiBYUEXSQMKShECpIuYkj1EGEPItVDYATRDXyQCAp8qCiKoAbqoSKipIcKghQiiyi6iFgkFmmmM/3+Z38HZppZe6+5rJk54/eDj4XnfN/ee31H5pyz99qnyxhjjDHGGGOMMcYYY4wxxhjTQQwMDHSje/v7+/eiA6gPXaenzWRD+Xt4QYbAY6cYtshiJguKv7N6CYbDc0fRIllNaSh7CfpN/Y8Iz98kuykNZb+h3pPg2SC7KQlFb1TnSfAcR+cpYkpByb3ooHpPgudpRUxJKHrYp6r/g+c7tEARUwpKvkGdJ8ETrFXElIKSF6Ef1XsSPM8rYkpC0c+p8yTxgsULp4gpBSWvRf3qPQmWGxUxpaDk+ehbdZ4Ezx5FTEko+ll1ngTPQdSriCkFJa9BJ9V7EjwbFTGloOS5aL86r+N1RUxJeDF2qvAkeA6jJYqYUlDypeiEek+C5w5FTCnoeTb6otV4DbwYfYqYktD1Q1XltRzhBVmmiCkFRa+k6GNV52nwbFXElIKeZ1H03qryNHjeZ+hWzJSCordVlafB8ye6SBFTiig5ylbvSfA8oIgpBT13U/QHVeVp8HzCMEsxUwqK3lpVngbPMXSxIqYUlLyMvo9UtafBt0MRUxKK7lPnSfB8ztCjiCkFRd9eVZ4Gzz9otSKmFJQcqw4Pq/ckeB5VxJSEol9T50nwfMUwRxFTCorOWXX4L8PliphSUPLZlP1Lq/Ua8DypiCkJReesOvyaYZ4iphQUvR7VLuXh6bjR5mpFTCkoeSFl56w63KWIKQlF71bnSfB8z7BQkc6Cg9+M9qFT6Gf0OJOZlh8RObZrUNOfqmCdIp0FB75d8xgCj8fZ0MWyTQs4plh1eKA6wjR4XlCks+DYz+Lgj1fTGE5MHq2QfcrhWJ7SoSXB8xPDmYp0Fhx8zv0RcUpiyj+pcBy5qw4798ZMjv/6ahr1MMm4frBJsUmHQ5jD/uPURy14XlKkM2ECcdPKH5pPLfiChxWdVNhvzqrDQ+gcRToXJnEXii9QWeB9keEMxYvD/lajnFWHtyrS+TCZ21Dj+qU2eN9jKP7GyT56UM6qwzcVmTkwqatQ4zWFNnj3MVyoeBHYx45qb2nw/M6wVJGZBZNbgRo/57fB+ytDkdPabPsSlLPqcLMiMxPmuJhJNq74a4P3L3Sz4hMCm41Vh/HFtBY87yoys2Gu85hs41W4NnhPoglbdBbb0qaT4Ilf57lAkZkPc44FZ09U088D/y6Gca3oYBu5qw7vU+T0gonfg+ISaBZ430Jj+hkK4vGfID7B1YLnQ4bTd4E0BcTFoKNVHc3g/Yxh1J98yOWsOoz3rOWKnL5QQnxBixN3WeD9Aa1SvBG856PGswZ4tiliVNqX6iaHuDMp67oEvpxVh58yeNXhYCgkLp++02ooA7wn0N2KjwjPb5I9CZ74TrJSETMYiumhoMYfbxkM/rgKOeyNmMfPRTmrDh9RxKSgpO1oNCcmX0FzFW/Bv1/V03XE+azZipg6KPQW9HfVWzN4P0at3xFh3KCHk+CJM72XtXZm8qC0K9GhqsJm8Mal4StQzqrDndqNGQ0UtxzFKsEs8Db+qcOzHw35E2dGAeX10uNHVZ3jg23F+bE12rQZK/E/Gr2sXscM23hGmzTjhT7jnNRjVbWjh2y8x8zX5sxEQbdbKLbxevhg8AfXahNmoqHcdfTceGdsG/y7FTWloORVKGe1eng6c4F0p0HRSyk8bkkeEZ4L1stuJgMKX4De1mswBB73vRxTAd3HickHUXySCr5B9/O4fyLJGGOMMcYYY4wxxhhjzLSkq+s/G8ELdx2zjB4AAAAASUVORK5CYII="></a>
												</div>
												
											</div>
											<div class="lp-ad-price-content text-center">
											<?php if('style1' == $style_name){ ?>
												<h5>Classic</h5>
												
												
												<a href="https://classic.listingprowp.com/" target="_blank"><?php printf( esc_html__( 'preview', 'listingpro' ) ); ?></a>
											<?php } ?>
											<?php if('style2' == $style_name){ ?>
												<h5>PlacesPro</h5>
												
												
												<a href="http://placespro.listingprowp.com/" target="_blank"><?php printf( esc_html__( 'preview', 'listingpro' ) ); ?></a>
											<?php } ?>	
											<?php if('style3' == $style_name){ ?>
												<h5>Restaurantpro</h5>
												
												
												<a href="http://restaurantpro.listingprowp.com/" target="_blank"><?php printf( esc_html__( 'preview', 'listingpro' ) ); ?></a>
											<?php } ?>	
												
											</div>
										</div>
									</div>	
								</li>
							<?php } ?>
						</ul>
					</div>
					<div class="clearfix"></div>
					<input type="hidden" name="new_style" id="new_style" value="style1">

					<p class="envato-setup-actions step">
					   
						<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>"
						   class="button button-large button-next"><?php esc_html_e( 'SKIP' ); ?></a>
						<?php wp_nonce_field( 'envato-setup' ); ?>
						 <input type="submit" class="button-primary button button-large button-next"
							   value="<?php esc_attr_e( 'ACCEPT' ); ?>" name="save_step"/>
					</p>
				</div>
            </form>
			<?php
		}

		public function envato_setup_color_style_save() {
			check_admin_referer( 'envato-setup' );

			$new_style = isset( $_POST['new_style'] ) ? $_POST['new_style'] : false;
			if ( $new_style ) {
				set_theme_mod( 'dtbwp_site_style', $new_style );
			}

			wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
			exit;
		}


		/* end for demo styles */



		public function envato_setup_ready() {

			update_option( 'envato_setup_complete', time() );
            
			update_option( 'dtbwp_update_notice', strtotime('-4 days') );
			?>
			
			<img src="<?php echo get_template_directory_uri().'/assets/images/setup/setup4.jpg' ?>"/>
			
			<h1 class="firs-step-h1 lp-setip-last-step" style="font-weight:700;"><?php printf( esc_html__( 'Now you will be automatically redirected to WordPress dashboard...', 'listingpro' ) ); ?></h1>
			
			

			<?php
			$welcomePargeURL = admin_url('admin.php?page=lp-cc-license');
			header("refresh:3;url=$welcomePargeURL");
			
		}

		

		private static $_current_manage_token = false;

		
		public function ajax_notice_handler() {
			check_ajax_referer( 'dtnwp-ajax-nonce', 'security' );
			// Store it in the options table
			update_option( 'dtbwp_update_notice', time() );
		}

		
		
		private function _array_merge_recursive_distinct( $array1, $array2 ) {
			$merged = $array1;
			foreach ( $array2 as $key => &$value ) {
				if ( is_array( $value ) && isset( $merged [ $key ] ) && is_array( $merged [ $key ] ) ) {
					$merged [ $key ] = $this->_array_merge_recursive_distinct( $merged [ $key ], $value );
				} else {
					$merged [ $key ] = $value;
				}
			}

			return $merged;
		}


		public static function cleanFilePath( $path ) {
			$path = str_replace( '', '', str_replace( array( '\\', '\\\\', '//' ), '/', $path ) );
			if ( $path[ strlen( $path ) - 1 ] === '/' ) {
				$path = rtrim( $path, '/' );
			}

			return $path;
		}

		public function is_submenu_page() {
			return ( $this->parent_slug == '' ) ? false : true;
		}
	}

}// if !class_exists

/**
 * Loads the main instance of Envato_Theme_Setup_Wizard to have
 * ability extend class functionality
 *
 * @since 1.1.1
 * @return object Envato_Theme_Setup_Wizard
 */
add_action( 'after_setup_theme', 'envato_theme_setup_wizard', 10 );
if ( ! function_exists( 'envato_theme_setup_wizard' ) ) :
	function envato_theme_setup_wizard() {
		Envato_Theme_Setup_Wizard::get_instance();
	}
endif;