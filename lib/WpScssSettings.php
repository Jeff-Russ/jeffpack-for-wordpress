<?php

if ( ! class_exists('WpScssSettings') ) {

	class WpScssSettings extends MainSettingsPage
	{
		private $options; # Holds the values to be used in the fields callbacks

		public function addScssOptionsPage() {
			add_action( 'admin_menu', /*add_plugin_page*/function(){
			    // This page will be under "Settings"
			    add_options_page( 'Settings Admin', 'WP-SCSS',
			      'manage_options', 'wpscss_options', /*create_admin_page*/ function(){
			        /**
			         * Add options page 
			         */
			        
			        // Set class property
			        $this->options = get_option( 'wpscss_options' );
			        ?>
			        <div class="wrap">
			            <?php screen_icon(); ?>
			            <h2>WP-SCSS Settings</h2>   
			            <p>
			              <span class="version">Version <em><?php echo get_option('wpscss_version'); ?></em>
			              <br/>
			              <span class="author">By: <a href="http://connectthink.com" target="_blank">Connect Think</a></span>
			              <br/>
			              <span class="repo">Help & Issues: <a href="https://github.com/ConnectThink/WP-SCSS" target="_blank">Github</a></span>
			            </p>        
			            <form method="post" action="options.php">
			            <?php
			                // This prints out all hidden setting fields
			                settings_fields( 'wpscss_options_group' );   
			                do_settings_sections( 'wpscss_options' );
			                submit_button(); 
			            ?>
			            </form>
			        </div>
			        <?php
			    });
			});
			add_action( 'admin_init', function/* page_init*/() {
			    /**
			     * Register and add settings (formerly page_init())
			     */
			    register_setting(
			        'wpscss_options_group',    // Option group
			        'wpscss_options',          // Option name
			        array( $this, 'sanitize' ) // Sanitize
			    );

			    // Paths to Directories
			    add_settings_section(
			        'wpscss_paths_section',             // ID
			        'Configure Paths',                  // Title
			        array( $this, 'print_paths_info' ), // Callback
			        'wpscss_options'                    // Page
			    );  

			    add_settings_field(
			        'wpscss_scss_dir',                     // ID
			        'Scss Location',                       // Title 
			        array( $this, 'input_text_callback' ), // Callback
			        'wpscss_options',                      // Page
			        'wpscss_paths_section',                // Section
			        array(                                 // args
			            'name' => 'scss_dir',
			        )
			    );      

			    add_settings_field(
			        'wpscss_css_dir',                       // ID
			        'CSS Location',                         // Title 
			        array( $this, 'input_text_callback' ),  // Callback
			        'wpscss_options',                       // Page
			        'wpscss_paths_section',                 // Section
			        array(                                  // args
			            'name' => 'css_dir',
			        )
			    );

			    // Compiling Options
			    add_settings_section(
			        'wpscss_compile_section',             // ID
			        'Compiling Options',                  // Title
			        array( $this, 'print_compile_info' ), // Callback
			        'wpscss_options'                      // Page
			    );  

			    add_settings_field(
			        'Compiling Mode',                        // ID
			        'Compiling Mode',                        // Title
			        array( $this, 'input_select_callback' ), // Callback
			        'wpscss_options',                        // Page
			        'wpscss_compile_section',                // Section
			        array(                                   // args
			            'name' => 'compiling_options',
			            'type' => apply_filters( 'wp_scss_compiling_modes',
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

			    add_settings_field(
			        'Error Display',                         // ID
			        'Error Display',                         // Title
			        array( $this, 'input_select_callback' ), // Callback
			        'wpscss_options',                        // Page
			        'wpscss_compile_section',                // Section   
			        array(                                   // args
			            'name' => 'errors',
			            'type' => apply_filters( 'wp_scss_error_diplay',
			                array(
			                    'show'           => 'Show in Header',
			                    'show-logged-in' => 'Show to Logged In Users',
			                    'hide'           => 'Print to Log',
			                )                               
			            )
			        )
			    );            

			    // Enqueuing Options
			    add_settings_section(
			        'wpscss_enqueue_section',             // ID
			        'Enqueuing Options',                  // Title
			        array( $this, 'print_enqueue_info' ), // Callback
			        'wpscss_options'                      // Page
			    );  

			    add_settings_field(
			        'Enqueue Stylesheets',                     // ID
			        'Enqueue Stylesheets',                     // Title
			        array( $this, 'input_checkbox_callback' ), // Callback
			        'wpscss_options',                          // Page
			        'wpscss_enqueue_section',                  // Section      
			        array(                                     // args
			            'name' => 'enqueue'
			        )
			    );
			});
		}

		    /**
		     * Sanitize each setting field as needed
		     *
		     * @param array $input Contains all settings fields as array keys
		     */
		    public function sanitize( $input ) {
		        foreach( ['scss_dir', 'css_dir'] as $dir ){
		            if( !empty( $input[$dir] ) ) {
		                $input[$dir] = sanitize_text_field( $input[$dir] );

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

		    /** 
			 * Text Fields' Callback
		     */
		    public function input_text_callback( $args ) {
		        printf(
		            '<input type="text" id="%s" name="wpscss_options[%s]" value="%s" />',
		            esc_attr( $args['name'] ), esc_attr( $args['name'] ), esc_attr( $this->options[$args['name']])
		        );
		    }

		    /** 
		     * Select Boxes' Callbacks
		     */
		    public function input_select_callback( $args ) {
		        $this->options = get_option( 'wpscss_options' );  
		        
		        $html = sprintf( '<select id="%s" name="wpscss_options[%s]">', esc_attr( $args['name'] ), esc_attr( $args['name'] ) );  
		            foreach( $args['type'] as $value => $title ) {
		                $html .= '<option value="' . esc_attr( $value ) . '"' . selected( $this->options[esc_attr( $args['name'] )], esc_attr( $value ), false) . '>' . esc_attr( $title ) . '</option>';
		            }
		        $html .= '</select>';  
		      
		        echo $html;  
		    }

		    /** 
		     * Checkboxes' Callbacks
		     */
		    public function input_checkbox_callback( $args ) {  
		        $this->options = get_option( 'wpscss_options' );  
		        
		        $html = '<input type="checkbox" id="' . esc_attr( $args['name'] ) . '" name="wpscss_options[' . esc_attr( $args['name'] ) . ']" value="1"' . checked( 1, isset( $this->options[esc_attr( $args['name'] )] ) ? $this->options[esc_attr( $args['name'] )] : 0, false ) . '/>';   
		        $html .= '<label for="' . esc_attr( $args['name'] ) . '"></label>';
		      
		        echo $html;  
		    } 



		/// OLD /////


		public function add_scss_settings_page_old() {

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
}