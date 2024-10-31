<?php

	if ($_POST['action'] && $_POST['action'] == 'update' ) {
		// Did this $_POST come from our form?
		check_admin_referer('ph4wp_save_options');
		// OK, now we can process data submitted from our form safely
		if ( isset($_POST['option_haml'])) {
			// Ticked!
			update_option( 'ph4wp_option_haml', true );
		} else {
			// Unticked!
			update_option( 'ph4wp_option_haml', false );
		}

		if ( isset($_POST['option_sass'])) {
			// Ticked!
			update_option( 'ph4wp_option_sass', true );
		} else {
			// Unticked!
			update_option( 'ph4wp_option_sass', false );
		}
		// If we've updated settings, show a message
		echo '<div id="message" class="updated fade"><p><strong>' . __( 'Settings saved.', 'PHPHaml4WP' ) . '</strong></p></div>';

	}
	
	// Let's get some variables for multiple instances
	$checked = ' checked="checked"';

	?>

	<div class="wrap">
		<h2><?php _e( 'PHPHaml 4 WP', 'PHPHaml4WP' ) ?></h2>

		<form name="ph4wp_options_update" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<?php wp_nonce_field('ph4wp_save_options'); echo "\n"; // Very important. Makes sure form data is submitted from this page. Do not fool with this. ?>

			<table class="form-table" summary="<?php _e( 'PHPHaml4WP Options', 'PHPHaml4WP' ) ?>">

				<tr valign="top">
					<th scope="row"><?php _e( 'Check for HAML templates', 'PHPHaml4WP' ) ?></th>
					<td>
						<fieldset>
							<legend class="hidden"><?php _e( 'Parse HAML', 'PHPHaml4WP' ) ?></legend>
							<input id="option_haml" name="option_haml" type="checkbox" value="option_haml" <?php if ( get_option('ph4wp_option_haml') == true ) echo $checked; ?> />
							<label for="option_haml"><?php _e( 'Check for and parse HAML templates', 'PHPHaml4WP' ) ?></label>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Check for SASS/SCSS templates', 'PHPHaml4WP' ) ?></th>
					<td>
						<fieldset>
							<legend class="hidden"><?php _e( 'Parse SASS', 'PHPHaml4WP' ) ?></legend>
							<input id="option_sass" name="option_sass" type="checkbox" value="option_sass" <?php if ( get_option('ph4wp_option_sass') == true ) echo $checked; ?> />
							<label for="option_sass"><?php _e( 'Check for and parse SASS/SCSS stylesheets', 'PHPHaml4WP' ) ?></label>
						</fieldset>
					</td>
				</tr>

			</table>
		
			<p class="submit">
				<!-- You can use the access key S to save options -->
				<input id="update" name="update" type="submit" value="<?php _e( 'Save Changes', 'PHPHaml4WP' ) ?>" accesskey="S" />
				<input name="action" type="hidden" value="update" />
				<input name="page_options" type="hidden" value="option_haml,option_sass" />
			</p>
		</form>
	</div>
<!-- } -->
