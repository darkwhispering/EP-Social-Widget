<?php

class epSocialSettings {

	function __construct()
	{
		// Folder for user uploaded icons
		$wp_upload_dir = wp_upload_dir();
		$this->icondir = $wp_upload_dir['basedir'] . '/epsocial_icons/';
		$this->iconurl = $wp_upload_dir['baseurl'] . '/epsocial_icons/';
	}

	function epsocial_panel()
	{
		if ( ! current_user_can( 'manage_options' ) )
		{
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		?>
		<div class="wrap ep-social">
			<h2><?php _e( 'EP Social Widget settings' ); ?></h2>

			<?php if ( ! empty( $_POST ) ) : ?>
				<?php
					if ( ! empty( $_POST['submit'] ) )
					{
						$response = $this->epsocial_save( $_POST );
					}
					elseif ( ! empty( $_POST['delete'] ) )
					{
						$response = $this->epsocial_delete( $_POST );
					}
				?>
				<div class="<?php echo $response['status']; ?>">
					<ul>
					<?php foreach ( $response['msg'] as $msg ) : ?>
						<li><?php echo $msg; ?></li>
					<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<?php

			?>

			<h3><?php _e( 'Add new network' ); ?></h3>
			<p>
				<?php _e( 'The default icon is 26x26 pixels. The upload does <strong>NOT</strong> resize your images, however, the plugin sets a fixed size of 26x26 when rendering the widget.' ); ?>
			</p>
			<form method="post" enctype="multipart/form-data">
				<table class="form-table abc-settings">
                    <tbody>
                        <tr valign="top">
                            <th scope="row">
                            	<label for="abc_title"><?php _e( 'Network name' ); ?>:</label>
                            </th>
                            <td>
								<input type="text" name="network_name" />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                            	<label for="abc_message"><?php _e( 'Icon' ); ?>:</label>
                            </th>
                            <td>
								<input type="file" name="icon" />
								<p class="description"><?php _e( 'Only .png, .jpg and .gif is support.' ); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row">
                            	<input type="submit" name="submit" value="Save" class="button-primary save" />
                            </th>
                        </tr>
                    </tbody>
                </table>
			</form>

			<h3><?php _e( 'Your added networks' ); ?></h3>
			<div id="ep-social-networks">
				<table class="wp-list-table widefat">
					<thead>
						<th width="20%"><?php _e( 'Network name' ); ?></th>
						<th width="80%"><?php _e( 'Icon' ); ?></th>
						<th></th>
					</thead>
					<?php
						$networks = $this->get_user_networks();
						if ( $networks ) :
							foreach ( $networks as $network ) :
							?>
								<tr>
									<td><?php echo $network['name']; ?></td>
									<td><img src="<?php echo $this->iconurl; ?><?php echo $network['icon']; ?>" alt="<?php echo $network['name']; ?>" width="26" height="26"></td>
									<td>
										<div class="row-actions">
											<span class="delete">
												<form method="post">
													<input type="hidden" name="icon" value="<?php echo $network['icon']; ?>">
													<input type="submit" value="delete" name="delete" class="button-secondary">
												</form>
											</span>
										</div>
									</td>
								</tr>
							<?php
							endforeach;
						else :
						?>

							<tr>
								<td><?php _e( 'No networks added' ); ?></td>
							</tr>

						<?php
						endif;
					?>
				</table>
			</div>
		</div>
	<?php
	}

	private function get_user_networks()
	{
		if ( ! file_exists( $this->icondir ) )
		{
			return NULL;
		}

		$icons = scandir( $this->icondir );

		unset( $icons[0] );
		unset( $icons[1] );

		foreach ( $icons as $icon )
		{
			$ext  = pathinfo( $icon, PATHINFO_EXTENSION );
			$name = str_replace( 'icon-', '', str_replace( '.' . $ext, '', $icon ) );

			$networks[] = array(
				'name' => $name,
				'icon' => $icon
			);
		}

		return $networks;
	}

	private function epsocial_delete( $data )
	{
		$icon = $data['icon'];

		if ( unlink( $this->icondir . $icon ) )
		{
			return array(
				'status' => __( 'updated' ),
				'msg'    => array(
					0 => __( 'Your network is deleted.' )
				)
			);
		}
		else
		{
			return array(
				'status' => __( 'error' ),
				'msg'    => array(
					0 => __( 'Could not delete the network.' )
				)
			);
		}
	}

	private function epsocial_save( $data )
	{
		// Icon
		$icon = $_FILES['icon'];

		// Validate if the icon is gif, png or jpg and not larger then 2MB in size
		if ( ! preg_match( '![a-z0-9\-\.\/]+\.(?:gif|png|jpg)!Ui', $icon['name'] ) )
		{
			$error[] = __( 'Only gif, png, jpg images are allowed' );
		}

		if ( $icon['size'] > 2000000 )
		{
			$error[] = __( 'Maximum size allowed is 2MB' );
		}

		if ( empty( $data['network_name'] ) )
		{
			$error[] = __( 'You must enter a network name' );
		}

		// Check if we have any error and if so, return them and stop the script, else, continue
		if ( count( $error ) > 0 )
		{
			return array(
				'status' => __( 'error' ),
				'msg'    => $error
			);
		}
		else
		{
			if ( ! is_dir( $this->icondir ) )
			{
				mkdir( $this->icondir );
				chmod( $this->icondir, 0755 );
			}
			else
			{
				chmod( $this->icondir, 0755 );
			}

			// Clean network name
			$network 	= $this->get_slug( $data['network_name'] );
			$ext 		= pathinfo( $icon['name'], PATHINFO_EXTENSION );
			$new_name 	= 'icon-' . $network . '.' . $ext;
			$uploadfile = $this->icondir . basename( $new_name );
			$movefile 	= move_uploaded_file( $icon['tmp_name'], $uploadfile );

			if ( $movefile )
			{
				return array(
					'status' => __( 'updated' ),
					'msg'    => array(
						0 => __( 'Your network is added.' )
					)
				);
			}
		}
	}

	private function get_slug( $str, $replace = array(), $delimiter = '_' )
	{
		setlocale( LC_ALL, 'sv_SE.UTF8' );
		if ( ! empty( $replace ) )
		{
			$str = str_replace( (array) $replace, ' ', $str );
		}

		$clean = iconv( 'UTF-8', 'ASCII//TRANSLIT', $str );
		$clean = preg_replace( '/[^a-zA-Z0-9\/_|+ -]/', '', $clean );
		$clean = strtolower( trim( $clean, '-' ) );
		$clean = preg_replace( '/[\/_|+ -]+/', $delimiter, $clean );

		return $clean;
	}
}

function epsocial_settings()
{
	$settings_panel = new epSocialSettings;
	return $settings_panel->epsocial_panel();
}

function epsocial_menu()
{
	add_submenu_page(
		'options-general.php',
		'EP Social Widget Settings',
		'EP Social Widget',
		'manage_options',
		'ep-social-widget',
		'epsocial_settings'
	);
}
add_action( 'admin_menu','epsocial_menu' );
