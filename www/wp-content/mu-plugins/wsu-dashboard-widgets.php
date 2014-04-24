<?php
/*
Plugin Name: WSUWP Dashboard
Plugin URI: http://web.wsu.edu/
Description: Modifications to the WordPress Dashboard.
Author: washingtonstateuniversity, jeremyfelt
Version: 0.1
*/

class WSUWP_WordPress_Dashboard {

	/**
	 * Add our hooks.
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widgets' ) );
		add_action( 'wp_network_dashboard_setup', array( $this, 'remove_network_dashboard_widgets' ) );
		add_filter( 'update_footer', array( $this, 'update_footer_text' ), 11 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 11 );
		add_action( 'in_admin_footer', array( $this, 'display_shield_in_footer' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_dashboard_stylesheet' ) );
	}

	/**
	 * Enqueue styles specific to the network admin dashboard.
	 */
	public function enqueue_dashboard_stylesheet() {
		if ( 'dashboard-network' === get_current_screen()->id ) {
			wp_enqueue_style( 'wsuwp-dashboard-style', plugins_url( '/css/dashboard-network.css', __FILE__ ), array(), wsuwp_global_version() );
		}
	}

	/**
	 * Remove all of the dashboard widgets and panels when a user logs
	 * in except for the Right Now area.
	 */
	public function remove_dashboard_widgets() {
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_incoming_links' , 'dashboard', 'normal' );
		remove_meta_box( 'tribe_dashboard_widget'   , 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_plugins'        , 'dashboard', 'normal' );
		remove_meta_box( 'dashboard_primary'        , 'dashboard', 'side'   );
		remove_meta_box( 'dashboard_secondary'      , 'dashboard', 'side'   );
		remove_meta_box( 'dashboard_quick_press'    , 'dashboard', 'side'   );
		remove_meta_box( 'dashboard_recent_drafts'  , 'dashboard', 'side'   );

		remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}

	/**
	 * Remove all default widgets from the network dashboard.
	 */
	public function remove_network_dashboard_widgets() {
		remove_meta_box( 'dashboard_plugins'          , 'dashboard-network', 'normal' );
		remove_meta_box( 'dashboard_primary'          , 'dashboard-network', 'side'   );
		remove_meta_box( 'dashboard_secondary'        , 'dashboard-network', 'side'   );

		wp_add_dashboard_widget( 'dashboard_wsuwp_counts', 'WSUWP Global Data', array( $this, 'network_dashboard_counts' ) );
	}

	/**
	 * Provide a widget that displays the counts for networks, sites, and users
	 * when viewing the network administration dashboard.
	 */
	public function network_dashboard_counts() {
		if ( wsuwp_get_current_network()->id == wsuwp_get_primary_network_id() ) {
			?>
			<h4>Global Data:</h4>
			<ul class="wsuwp-global-counts">
				<li>Networks: <?php echo wsuwp_network_count(); ?></li>
				<li>Sites: <?php echo wsuwp_global_site_count(); ?></li>
				<li>Users: <?php echo wsuwp_global_user_count(); ?></li>
			</ul>
			<?php
		}
		?>
		<h4>Network Data:</h4>
		<ul class="wsuwp-network-counts">
			<li>Sites: <?php echo esc_html( get_site_option( 'blog_count' ) ); ?></li>
			<li>Users: <?php echo wsuwp_network_user_count( wsuwp_get_current_network()->id ); ?></li>
		</ul>
		<?php
	}

	/**
	 * Customize the update footer text a bit.
	 *
	 * @param $text
	 *
	 * @return mixed|string
	 */
	public function update_footer_text() {
		global $wsuwp_global_version, $wsuwp_wp_changeset;

		$version = ltrim( get_bloginfo( 'version' ), '(' );
		$version = rtrim( $version, ')' );
		$version = explode( '-', $version );

		$text = 'WSUWP Platform <a target=_blank href="https://github.com/washingtonstateuniversity/WSUWP-Platform/tree/v' . $wsuwp_global_version . '">' . $wsuwp_global_version . '</a> | ';
		$text .= 'WordPress ' . $version[0];

		if ( isset( $version[1] ) ) {
			$text .= ' ' . ucwords( $version[1] );
		}

		$text .= ' [<a target=_blank href="https://core.trac.wordpress.org/changeset/' . $wsuwp_wp_changeset . '">' . $wsuwp_wp_changeset . '</a>]';

		return $text;
	}

	/**
	 * Customize the general footer text in the admin.
	 *
	 * @return string
	 */
	public function admin_footer_text() {
		$wp_text = sprintf( __( 'Thank you for creating with <a href="%s">WordPress</a> at <a href="%s">Washington State University</a>.' ), __( 'https://wordpress.org/' ), 'http://wsu.edu' );
		$text = '<span id="footer-thankyou">' . $wp_text . '</span>';

		return $text;
	}

	/**
	 * Display the WSU shield in the footer.
	 */
	public function display_shield_in_footer() {
		echo '<img style="float:left; margin-right:5px;" height="20" src="' . plugins_url( '/images/wsu-shield.png', WPMU_PLUGIN_DIR . '/wsu-dashboard-widgets.php' ) . '" />';
	}
}
$wsuwp_wordpress_dashboard = new WSUWP_WordPress_Dashboard();