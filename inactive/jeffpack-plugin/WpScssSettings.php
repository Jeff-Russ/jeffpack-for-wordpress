<?php

class WpScssSettings extends MainSettingsPage
{
	private $options; # Holds the values to be used in the fields callbacks
	public function add_scss_settings_page() {

		add_action( 'admin_menu', function ()
		{
			add_options_page( 'Settings Admin', 'SCSS Settings','manage_options','wpscss_options', function ()
			{
				$this->options = get_option( 'wpscss_options' );
				?>
				<div class="wrap">
					<?php screen_icon(); ?>
					<h2>SCSS Settings</h2><hr>
					<form method="post" action="options.php">
						<?php
						settings_fields( 'wpscss_options_group' );
						do_settings_sections( 'wpscss_options' );
						submit_button();
						?>
					</form>
				</div>
				<?php
			});
		});
		add_action( 'admin_init',function (){
			register_setting('wpscss_options_group', 'wpscss_options',
				function ( $input ) { # sanitize
					if( !empty( $input['wpscss_scss_dir'] ) )
						$input['wpscss_scss_dir'] = sanitize_text_field( $input['wpscss_scss_dir'] );
					if( !empty( $input['wpscss_css_dir'] ) )
						$input['wpscss_css_dir'] = sanitize_text_field( $input['wpscss_css_dir'] );
					return $input;
				}
			);
			// Paths to Directories
			add_settings_section('scss_section', 'Configure Paths', function(){},'wpscss_options' );

			add_settings_field('wpscss_scss_dir', 'Scss Path (starting / as theme root)', 
				function () { # scss_dir_callback
					printf(
						'<input type="text" id="scss_dir" name="wpscss_options[scss_dir]" value="%s" />',
						esc_attr( $this->options['scss_dir'])
						);
				}, 
				'wpscss_options', 'scss_section' 
			);
			add_settings_field('wpscss_css_dir','CSS Path (starting / as theme root)',
				function() { # css_dir_callback
					printf(
						'<input type="text" id="css_dir" name="wpscss_options[css_dir]" value="%s" />',
						esc_attr( $this->options['css_dir'])
						);
				},
				'wpscss_options','scss_section'
			);
			// Compiling Options

			add_settings_field('Compiling Mode','Compiling Mode',
				function () { # compiling_mode_callback
					$this->options = get_option( 'wpscss_options' );
					$html = '<select id="compiling_options" name="wpscss_options[compiling_options]">';
					$html .= '<option value="scss_formatter"' . selected( $this->options['compiling_options'], 'scss_formatter', false) . '>Expanded</option>';
					$html .= '<option value="scss_formatter_nested"' . selected( $this->options['compiling_options'], 'scss_formatter_nested', false) . '>Nested</option>';
					$html .= '<option value="scss_formatter_compressed"' . selected( $this->options['compiling_options'], 'scss_formatter_compressed', false) . '>Compressed</option>';
					$html .= '<option value="scss_formatter_minified"' . selected( $this->options['compiling_options'], 'scss_formatter_minified', false) . '>Minified</option>';
					$html .= '</select>';
					echo $html;
				}, 
				'wpscss_options', 'scss_section' 
			);
			add_settings_field('Error Display', 'Error Display',
				function() { # errors_callback
					$this->options = get_option( 'wpscss_options' );
					$html = '<select id="errors" name="wpscss_options[errors]">';
					$html .= '<option value="show"' . selected( $this->options['errors'], 'show', false) . '>Show in Header</option>';
					$html .= '<option value="show-logged-in"' . selected( $this->options['errors'], 'show-logged-in', false) . '>Show to Logged In Users</option>';
					$html .= '<option value="log"' . selected( $this->options['errors'], 'hide', false) . '>Print to Log</option>';
					$html .= '</select>';
					echo $html;
				}, 
				'wpscss_options', 'scss_section' 
			);
			add_settings_field('Compiling Mode', 'Compiling Mode',
				function () { # compiling_mode_callback
					$this->options = get_option( 'wpscss_options' );
					$html = '<select id="compiling_options" name="wpscss_options[compiling_options]">';
					$html .= '<option value="scss_formatter"' . selected( $this->options['compiling_options'], 'scss_formatter', false) . '>Expanded</option>';
					$html .= '<option value="scss_formatter_nested"' . selected( $this->options['compiling_options'], 'scss_formatter_nested', false) . '>Nested</option>';
					$html .= '<option value="scss_formatter_compressed"' . selected( $this->options['compiling_options'], 'scss_formatter_compressed', false) . '>Compressed</option>';
					$html .= '<option value="scss_formatter_minified"' . selected( $this->options['compiling_options'], 'scss_formatter_minified', false) . '>Minified</option>';
					$html .= '</select>';
					echo $html;
				},
				'wpscss_options', 'scss_section' 
			);
			add_settings_field('Error Display','Error Display',
				function() { # errors_callback
					$this->options = get_option( 'wpscss_options' );
					$html = '<select id="errors" name="wpscss_options[errors]">';
					$html .= '<option value="show"' . selected( $this->options['errors'], 'show', false) . '>Show in Header</option>';
					$html .= '<option value="show-logged-in"' . selected( $this->options['errors'], 'show-logged-in', false) . '>Show to Logged In Users</option>';
					$html .= '<option value="log"' . selected( $this->options['errors'], 'hide', false) . '>Print to Log</option>';
					$html .= '</select>';
					echo $html;
				},
				'wpscss_options', 'scss_section' 
			);

			add_settings_field('Auto-Enqueue CSS','Auto-Enqueue CSS',
				function () { # enqueue_callback
					$this->options = get_option( 'wpscss_options' );
					$html = '<input type="checkbox" id="enqueue" name="wpscss_options[enqueue]" value="1"' . checked( 1, isset($this->options['enqueue']) ? $this->options['enqueue'] : 0, false ) . '/>';
					$html .= '<label for="enqueue"></label>';
					echo $html;
				}, 
				'wpscss_options', 'scss_section' 
			);
		});
	}
}