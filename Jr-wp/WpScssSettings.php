<?php

if (!class_exists('WpScssSettings')){ class WpScssSettings extends MainSettingsPage {
	

	private $options;# Holds the values to be used in the fields callbacks

	public function addScssOptionsPage() {
		add_action('admin_menu', /*add_plugin_page*/function(){
			// This page will be under "Settings"
			add_options_page('Settings Admin', 'WP-SCSS',
			  'manage_options', 'wpscss_options', /*create_admin_page*/ function(){
				/**
				 * Add options page 
				 */
				// Set class property
				$this->options = get_option('wpscss_options');
				?>
				<div class="wrap">
					<?php get_screen_icon();// Screen icons are no longer used as of WordPress 3.8. ?>
					<h2>WP-SCSS Settings</h2>   
					<p>
					  <span class="version">Version <em><?php echo get_option('wpscss_version');?></em>
					  <br/>
					  <span class="author">By: <a href="http://connectthink.com" target="_blank">Connect Think</a></span>
					  <br/>
					  <span class="repo">Help & Issues: <a href="https://github.com/ConnectThink/WP-SCSS" target="_blank">Github</a></span>
					</p>
					<form method="post" action="options.php">
					<?php
						// This prints out all hidden setting fields
						settings_fields('wpscss_options_group');
						do_settings_sections('wpscss_options');
						submit_button();
					?>
					</form>
				</div>
				<?php
			});
		});
		add_action('admin_init', function/* page_init*/() {
			/**
			 * Register and add settings (formerly page_init())
			 */
			// register_setting($option_group, $option_name, $sanitize_cb);
			// add_settings_section($id, $title, $cb, $page);
			// add_settings_field($id, $title, $cb, $page, $section, $args);

			register_setting('wpscss_options_group','wpscss_options',array($this, 'sanitize' ));

			// Paths to Directories
			add_settings_section('wpscss_paths_section','Configure Paths',
				array($this, 'print_paths_info'),'wpscss_options');

			add_settings_field('wpscss_scss_dir', 'Scss Location',
				array($this, 'textfield'),'wpscss_options','wpscss_paths_section',
				array('opt_name' => 'wpscss_options', 'param' => 'scss_dir'));
			
			add_settings_field('wpscss_css_dir','CSS Location',
				array($this, 'textfield'),'wpscss_options','wpscss_paths_section',
				array('opt_name' => 'wpscss_options', 'param' => 'css_dir',));

			// Compiling Options
			// add_settings_section($id, $title, $cb, $page);
			add_settings_section('wpscss_compile_section','Compiling Options',
				array($this, 'print_compile_info'),'wpscss_options');

			add_settings_field('Compiling Mode','Compiling Mode',
				array($this, 'dropdown'),'wpscss_options','wpscss_compile_section',
				array(
					'opt_name' => 'wpscss_options',
					'param' => 'compiling_options',
					'type' => apply_filters('wp_scss_compiling_modes',
						array(
							'Leafo\ScssPhp\Formatter\Expanded'   => 'Expanded',
							'Leafo\ScssPhp\Formatter\Nested'     => 'Nested',
							'Leafo\ScssPhp\Formatter\Compressed' => 'Compressed',
							'Leafo\ScssPhp\Formatter\Compact'    => 'Compact',
							'Leafo\ScssPhp\Formatter\Crunched'   => 'Crunched',
							'Leafo\ScssPhp\Formatter\Debug'      => 'Debug'
						)
					)
				)
			);
			add_settings_field('Error Display','Error Display',
				array($this, 'dropdown'),'wpscss_options','wpscss_compile_section',
				array(
					'opt_name' => 'wpscss_options',
					'param' => 'errors',
					'type' => apply_filters('wp_scss_error_diplay',
						array(
							'show'           => 'Show in Header',
							'show-logged-in' => 'Show to Logged In Users',
							'log'            => 'Print to Log')
					)
				)
			);
			// Enqueuing Options
			add_settings_section('wpscss_enqueue_section','Enqueuing Options',
				array($this, 'print_enqueue_info'),'wpscss_options');

			add_settings_field('Enqueue Stylesheets','Enqueue Stylesheets',
				array($this, 'checkbox'),'wpscss_options','wpscss_enqueue_section',
				array('opt_name' => 'wpscss_options', 'param' => 'enqueue'));
		});
	}

		/**
		 * Sanitize each setting field as needed
		 *
		 * @param array $input Contains all settings fields as array keys
		 */
		public function sanitize($input ) {
			foreach(['scss_dir', 'css_dir'] as $dir ){
				if(!empty($input[$dir] ) ) {
					$input[$dir] = sanitize_text_field($input[$dir]);

					// Add a trailing slash if not already present
					if(substr($input[$dir], -1) != '/'){
						$input[$dir] .= '/';
					}
				}
			}

			return $input;
		}

		/** 
		 * Print the Section text
		 */
		public function print_paths_info() {
			print 'Add the paths to your directories below. Paths should start with the root of your theme. example: "/library/scss/"';
		}
		public function print_compile_info() {
			print 'Choose how you would like SCSS to be compiled and how you would like the plugin to handle errors';
		}
		public function print_enqueue_info() {
			print 'WP-SCSS can enqueue your css stylesheets in the header automatically.';
		}


		public function textfield($a) {
				echo "<input type='$a[param]' id='$a[param]' name='$a[opt_name]' value='"
				.$this->options[$a['param']]."' />";
		}

		public function dropdown($a ) {
			$opts = get_option($a['opt_name']);
			$html = "<select id='$a[param]'' name='$a[opt_name][$a[param]]'>\n";
			foreach($a['type'] as $v => $t ) {
				$html .= "  <option value=\"$v\"".selected($opts[$a['param']],$v,false).">$t</option>\n";
			}
			echo "$html</select>\n";
		}

		public function checkbox($a) {
			$opts = get_option($a['opt_name']);
			$html = "<input type='checkbox' id='$a[param]' name='$a[opt_name][$a[param]]' value='1'";
			$html.= checked(1, isset($opts[$a['param']]) ? $opts[$a['param']] : 0, false ) . "/>\n";
			echo "$html<label for='$a[param]'></label>\n";
		}
}} # close class then include guard