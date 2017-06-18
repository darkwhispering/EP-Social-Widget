<?php
/*
Plugin Name: EP Social Widget
Plugin URI: http://www.darkwhispering.com
Description: Very small and easy to use widget and shortcode to display social icons on your site. Facebook, Twitter, Flickr, Google, Google Plus, Youtube, LinkedIn, Soundcloud, Pinterest, Vimeo, Tumblr, Instagram, Github and RSS feed.
Author: Mattias Hedman
Author URI: http://www.darkwhispering.com
Version: 1.5.1
*/
define( 'EPS_VERSION','1.5.0' );

add_action( 'init', 'epSocialWidgetVersion', 1 );
function epSocialWidgetVersion()
{
	if ( get_option( 'ep-social-widget-version' ) != EPS_VERSION )
	{
		update_option( 'ep-social-widget-old-version', get_option( 'ep-social-widget-version' ) );
		update_option( 'ep-social-widget-version', EPS_VERSION );
	}
}

// ====================
// = Plugin shortcode =
// ====================

function epsw_shortcode( $args )
{
	// User uploaded icon url
	$wp_upload_dir = wp_upload_dir();
	$iconurl       = $wp_upload_dir['baseurl'] . '/epsocial_icons/';
	$icondir       = $wp_upload_dir['basedir'] . '/epsocial_icons/';

	// Plugin path
	$plugin_path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) );

	$html = '<ul class="ep_social_widget" id="epSW_shortcode">';
	foreach ( $args as $network => $link )
	{
		if ( $network === 'rss' )
		{
			if ( $link === '1' )
			{
				$html .= '<li>';
					$html .= '<a href="' . get_bloginfo( "rss2_url" ) . '" target="_blank" title="RSS"><img src="' . plugins_url( "icons/rss.svg", __FILE__ ) . '" alt="RSS" width="26" height="26" /></a>';
				$html .= '</li>';
			}
		}
		else
		{
			$pattern1 = '/^http:\/\//';
			$pattern2 = '/^https:\/\//';

			$l = strip_tags( $link );
			if ( preg_match( $pattern1, $l ) || preg_match( $pattern2, $l ) )
			{
				$link = $l;
			}
			else
			{
				$link = 'http://'.$l;
			}

			$html .= '<li>';

			if ( file_exists( $plugin_path . '/icons/' . $network . '.svg' ) )
			{
				$html .= '<a href="' . $link . '" target="_blank" title="' . $network . '"><img src="'.plugins_url( "icons/" . $network . ".svg", __FILE__ ).'" alt="' . $network . '" width="26" height="26" /></a>';
			}
			else
			{
				if ( ! file_exists( $icondir ) )
				{
					$icons = NULL;
				}
				else
				{
					$icons = scandir( $icondir );
				}

				if ( $icons )
				{
					foreach ( $icons as $icon )
					{
						$ext  = pathinfo( $icon, PATHINFO_EXTENSION );
						$name = str_replace( 'icon-', '', str_replace( '.' . $ext, '', $icon ) );

						if ( $name == $network )
						{
							$html .= '<a href="' . $link . '" target="_blank" title="' . $network . '"><img src="' . $iconurl . 'icon-' . $network . '.' . $ext . '" alt="' . $network . '" width="26" height="26" /></a>';
						}
					}
				}
			}

			$html .= '</li>';

		}
	}
	$html .= '</ul>';

	return $html;
}
add_shortcode( 'ep-social-widget', 'epsw_shortcode' );


// =================
// = Plugin widget =
// =================
// Load stylesheet and widget
add_action( 'wp_head','epSocialWidgetCss' );
add_action( 'widgets_init','load_epSocialWidget' );

// Register the widget
function load_epSocialWidget()
{
	register_widget( 'epSocialWidget' );
}

// Widget stylesheet
function epSocialWidgetCss()
{
	echo '<link href="' . plugins_url( 'style.css', __FILE__ ) . '" type="text/css" rel="stylesheet" media="screen" />';
}

class epSocialWidget extends WP_Widget {

	function epSocialWidget()
	{
		//Settings
		$widget_ops = array(
			'classname' => 'epsocialwidget',
			'description' => __( 'Display social icons on your site.', 'epsocialwidget' )
		);

		//Controll settings
		$control_ops = array( 'id_base' => 'epsocialwidget' );

		//Create widget
		$this->__construct( 'epsocialwidget', __( 'EP Social Widget' ), $widget_ops, $control_ops );

		// Plugin path
		$this->plugin_path = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . str_replace( basename( __FILE__ ), '', plugin_basename( __FILE__ ) );

		// User uploaded icon url
		$wp_upload_dir = wp_upload_dir();
		$this->iconurl = $wp_upload_dir['baseurl'] . '/epsocial_icons/';
		$this->icondir = $wp_upload_dir['basedir'] . '/epsocial_icons/';
	}

	// Widget frontend
	function widget( $args, $instance )
	{
		extract( $args );

		/* If we just upgraded from v1.0.2 or lower to v1.1.0 we need to update the instance array */
		if ( get_option( 'ep-social-widget-old-version' ) <= '1.1.0' )
		{
			$v_upgrade = get_option( 'ep-social-widget-1.0.2to1.1.0' );
			if ( ! $v_upgrade )
			{
				$title = $instance['title'];
				$rss   = $instance['rss'];
				unset( $instance['title'] );
				unset( $instance['rss'] );

				$count_networks = count( $instance );
				foreach ( $instance as $network => $url )
				{
					$link = $url;
					$instance[ $network ] = '';
					$instance[ $network ]['link'] = $link;
				}

				$icons = $this->get_icons();
				if ( $icons )
				{
					foreach ( $icons as $icon )
					{
						$ext  = pathinfo( $icon, PATHINFO_EXTENSION );
						$name = str_replace( 'icon-', '', str_replace( '.' . $ext, '', $icon ) );
						$instance[ $name ]['icon'] = $icon;
					}
				}

				$instance['title'] = $title;
				$instance['rss']   = $rss;
				$i++;
			}
		}

		//User selected settings
		$title = $instance['title'];
		unset( $instance['title'] );

		echo $before_widget;
		?>

		<div class="ep_social_widget">

			<?php echo $before_title . $title . $after_title; ?>

			<?php
				foreach ( $instance as $network => $data )
				{
					if ( $network === 'rss' )
					{
						if ( $data === '1' )
						{
							echo '<a href="' . get_bloginfo( "rss2_url" ) . '" target="_blank" title="RSS"><img src="' . plugins_url( "icons/rss.svg", __FILE__ ) . '" alt="RSS" width="26" height="26" /></a>';
						}
					}
					else
					{
						if ( ! empty( $data['link'] ) )
						{
							if ( ! isset( $data['icon'] ) )
							{
								echo '<a href="' . $data['link'] . '" target="_blank" title="' . $network . '"><img src="' . plugins_url( "icons/" . $network . ".svg", __FILE__ ) . '" alt="' . $network . '" width="26" height="26" /></a>';
							}
							else
							{
								if ( ! file_exists( $this->icondir . $data['icon'] ) )
								{
									unset( $instance[$network] );
								}
								else
								{
									echo '<a href="' . $data['link'] . '" target="_blank" title="' . $network . '"><img src="' . $this->iconurl . $data['icon'] . '" alt="' . $network . '" width="26" height="26" /></a>';
								}
							}
						}
					}
				}
			?>
		</div>

		<?php
		echo $after_widget;
	}

	// Widget update
	function update( $new_instance, $instance )
	{
		/* If we just upgraded from v1.0.2 or lower to v1.1.0 we need to update the instance array */
		if ( get_option( 'ep-social-widget-old-version' ) <= '1.1.0' )
		{
			$v_upgrade = get_option( 'ep-social-widget-1.0.2to1.1.0' );
			if ( ! $v_upgrade )
			{
				$title = $instance['title'];
				$rss   = $instance['rss'];
				unset( $instance['title'] );
				unset( $instance['rss'] );

				$count_networks = count( $instance );
				foreach ( $instance as $network => $url )
				{
					$link                         = $url;
					$instance[ $network ]         = '';
					$instance[ $network ]['link'] = $link;
				}
				$instance['title'] = $title;
				$instance['rss']   = $rss;
				$i++;
			}
		}


		$pattern1 = '/^http:\/\//'; //
		$pattern2 = '/^https:\/\//';

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['rss'] = strip_tags( $new_instance['rss'] );

		unset( $new_instance['title'] );
		unset( $new_instance['rss'] );

		foreach( $new_instance as $key => $new )
		{
			$link = strip_tags( $new );
			if ( ! empty( $link ) )
			{
				if ( preg_match( $pattern1, $link ) || preg_match( $pattern2, $link ) )
				{
					$instance[ $key ]['link'] = $link;
				}
				else
				{
					$instance[ $key ]['link'] = 'http://' . $link;
				}

				if ( file_exists( $this->icondir . 'icon-' . $key . '.png' ) )
				{
					$instance[ $key ]['icon'] = 'icon-' . $key . '.png';
				}
				elseif ( file_exists( $this->icondir . 'icon-' . $key . '.jpg' ) )
				{
					$instance[ $key ]['icon'] = 'icon-' . $key . '.jpg';
				}
				elseif ( file_exists( $this->icondir . 'icon-' . $key . '.gif' ) )
				{
					$instance[ $key ]['icon'] = 'icon-' . $key . '.gif';
				}

			} else {
				$instance[ $key ]['link'] = NULL;
			}
		}

		$v_upgrade = get_option( 'ep-social-widget-1.0.2to1.1.0' );
		if ( ! $v_upgrade ) update_option( 'ep-social-widget-1.0.2to1.1.0', 'true' );

		return $instance;
	}

	// Widget backend
	function form( $instance )
	{
		/* If we just upgraded from v1.0.2 or lower to v1.1.0 we need to update the instance array */
		if ( get_option( 'ep-social-widget-old-version' ) <= '1.1.0' )
		{
			$v_upgrade = get_option( 'ep-social-widget-1.0.2to1.1.0' );
			if ( ! $v_upgrade )
			{
				$title = $instance['title'];
				$rss   = $instance['rss'];
				unset( $instance['title'] );
				unset( $instance['rss'] );

				$count_networks = count( $instance );
				foreach ( $instance as $network => $url )
				{
					$link                         = $url;
					$instance[ $network ]         = '';
					$instance[ $network ]['link'] = $link;
				}
				$instance['title'] = $title;
				$instance['rss']   = $rss;
				$i++;
			}
		}

		$default = array(
			'title' 		=> '',
			'rss'			=> '',
			'twitter'		=> array( 'link' => '' ),
			'facebook' 		=> array( 'link' => '' ),
			'flickr' 		=> array( 'link' => '' ),
			'gplus' 		=> array( 'link' => '' ),
			'youtube' 		=> array( 'link' => '' ),
			'linkedin' 		=> array( 'link' => '' ),
			'soundcloud' 	=> array( 'link' => '' ),
			'pinterest'		=> array( 'link' => '' ),
			'vimeo'			=> array( 'link' => '' ),
			'tumblr'		=> array( 'link' => '' ),
			'google'		=> array( 'link' => '' ),
			'instagram'		=> array( 'link' => '' ),
			'github'		=> array( 'link' => '' )
		);

		$instance = wp_parse_args( (array) $instance, $default );
		$icons    = $this->get_icons();

		unset( $icons[0] );
		unset( $icons[1] );

		if ( $icons )
		{
			foreach ( $icons as $icon )
			{
				$ext        = pathinfo( $icon, PATHINFO_EXTENSION );
				$name       = str_replace( 'icon-', '', str_replace( '.' . $ext, '', $icon ) );
				$networks[] = $name;
			}
		}
	?>
		<!-- TITLE -->
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
			<br />
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>

		<!-- RSS -->
		<p>
			<label for="<?php echo $this->get_field_id( 'rss' ); ?>"><?php _e( 'Display rss link:' ); ?></label>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'rss' ); ?>" name="<?php echo $this->get_field_name( 'rss' ); ?>" <?php if ( $instance['rss'] == 1 ): ?> checked="checked" <?php endif; ?> value="1" /> <?php _e( 'Yes' ); ?>
			&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" id="<?php echo $this->get_field_id( 'rss' ); ?>" name="<?php echo $this->get_field_name( 'rss' ); ?>" <?php if ( $instance['rss'] == 0 ): ?> checked="checked" <?php endif; ?> value="0" /> <?php _e( 'No' ); ?>
		</p>

		<?php if ( $networks ) : ?>
		<div class="ep-social-user-networks">
			<a href="#" class="show-hide"><?php _e( 'Show/Hide user added networks' ); ?></a>
			<div class="ep-social-content">
				<h4><?php _e( 'User added networks' ); ?></h4>
				<?php
				foreach ( $networks as $network ) :
				?>
					<p>
						<label for="<?php echo $this->get_field_id( $network ); ?>"><?php _e( str_replace( '_', ' ', $network ) . ' profile link:' ); ?></label>
						<br />
						<input type="text" id="<?php echo $this->get_field_id( $network ); ?>" name="<?php echo $this->get_field_name( $network ); ?>" value="<?php echo $instance[ $network ]['link']; ?>" class="widefat" />
					</p>
				<?php
				unset( $instance[ $network ] );
				endforeach;
				?>
			</div>
		</div>
		<?php
		endif;

		unset( $instance['title'] );
		unset( $instance['rss'] );
		unset( $instance['0'] );
		?>


		<div class="ep-social-default-networks">
			<a href="#" class="show-hide"><?php _e( 'Show/Hide default networks' ); ?></a>
			<div class="ep-social-content">
				<h4><?php _e( 'Default networks' ); ?></h4>

				<?php
				foreach ( $instance as $network => $link ) :
					if ( file_exists( $this->plugin_path . '/icons/' . $network . '.svg' ) ) :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $network ); ?>"><?php _e( $network . ' profile link:' ); ?></label>
						<br />
						<input type="text" id="<?php echo $this->get_field_id( $network ); ?>" name="<?php echo $this->get_field_name( $network ); ?>" value="<?php echo $link['link']; ?>" class="widefat" />
					</p>
					<?php
					endif;
				endforeach;
				?>
			</div>
		</div>
	<?php
	}
	private function get_icons() {
		if ( ! file_exists( $this->icondir ) )
		{
			$icons = NULL;
		}
		else
		{
			$icons = scandir( $this->icondir );
		}

		return $icons;
	}
}

function eps_widget_settings()
{
?>
<style>
	.ep-social-user-networks,
	.ep-social-default-networks {
		padding: 5px 0;
	}

	.ep-social-user-networks .ep-social-content,
	.ep-social-default-networks .ep-social-content {
		display: none;
	}
</style>
<script>
	jQuery(document).ready(function($) {
		$('body .widgets-holder-wrap').on('click', '.ep-social-default-networks .show-hide', function(){
			$('.widgets-holder-wrap .ep-social-default-networks .ep-social-content').toggle('slide');
		});

		$('body .widgets-holder-wrap').on('click', '.ep-social-user-networks .show-hide', function(){
			$('.widgets-holder-wrap .ep-social-user-networks .ep-social-content').toggle('slide');
		});
	});
</script>
<?php
}
add_action( "admin_head", "eps_widget_settings" );

// ========================
// = Plugin settings page =
// ========================

include( 'ep_social_settings.php' );

?>
