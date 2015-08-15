<?php

	class Settings_API_Tabs_Wpytp_Plugin 
	{

		/*
		 For easier overriding we declared the keys
		 * here as well as our tabs array which is populated
		 * when registering settings
		 */
	
		private $general_settings_key = 'playpops_general_settings';
		private $plugin_options_key = 'playpops_plugin_options';
		private $plugin_settings_tabs = array();
		
		/*
		Fired during plugins_loaded (very very early),
		* so don't miss-use this, only actions and filters,
		* current ones speak for themselves.
		*/

		function __construct() 
		{
		
			add_action( 'init', array( &$this, 'mmLoad_Settings' ) );
			
			// Register a section in Settings
			add_action( 'admin_init', array( &$this, 'mmRegister_general_settings' ) );
			add_action('admin_notices', array( &$this, 'mmPlaypops_Admin_Notice'));
			add_action( 'admin_menu', array( &$this, 'mmAdd_admin_menus' ) );
		}
		
		public function mmValidateKey($mmPlaypopsKey)
		{
						
			$validateString = "";
			
			// define the url
			$keyurl = "https://www.googleapis.com/youtube/v3/videos?id=FNQowwwwYa0&part=contentDetails&key=" . $mmPlaypopsKey;
			
			// Extract the results
			//Using cURL php extension to make the request to youtube API
			$ch = curl_init();
	
			curl_setopt($ch, CURLOPT_URL, $keyurl);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
			//$feed holds a rss feed xml returned by youtube API
			$json_response = curl_exec($ch);
	
			curl_close($ch);
	
			//$entry = $xml->entry[0];
	
			$responseObj = json_decode($json_response, true);
			
			if (isset($responseObj['error']['errors'][0]['reason']))
				$validateString = $responseObj['error']['errors'][0]['reason'];
			
						
			if($validateString == "keyInvalid")
				return false;
			else
				return true;

		}
	
		// Add administrations notices
		function mmPlaypops_Admin_Notice()
		{
			// Get settings
			$settings = get_option('playpops_general_settings');
			
			// --------------------------------------
			// Check for key
			// --------------------------------------
			// Check if variable is empty first to avoid notices
			if(isset($settings['playpops_youtube_key'])) 
			{
				
				$mmPlaypopsKey = $settings['playpops_youtube_key'];
			
				// Check if the key is valid
				if(!$this->mmValidateKey($mmPlaypopsKey))
					echo '<div class="error">
					<p>PlayPops for Wordpress : <b>Invalid Key</b>. Obtain your <b>Playpops Key</b> by signing up at <a href="http://areyoupop.com/playpops/getkeys/">Playpops Keys</a></p>
					</div>';
			}
			else
			{
				
				// $defaultSettings['playpops_header_color'] = "#6495ED";
				echo '<div class="error">
					<p>PlayPops for Wordpress : Obtain your <b>Playpops Key</b> by signing up at <a href="http://areyoupop.com/playpops/getkeys/">Playpops Keys</a></p>
					</div>';
			}
			
			
			// --------------------------------------
			// Check for facebook url
			// --------------------------------------
			// Check if variable is empty first to avoid notices
			if(empty($settings['playpops_url'])) 
			{
				
				// $defaultSettings['playpops_header_color'] = "#6495ED";
				echo '<div class="error">
					<p>PlayPops for Wordpress : Enter your <b>Facebook Page URL</b> before enabling PlayPops in your Page or Post</p>
					</div>';
			}
			
			// --------------------------------------
			// Check for Custom header Length
			// --------------------------------------
			// Check if variable is empty first to avoid notices
			if(isset($settings['playpops_header'])) 
			{
				
				$playpops_header = $settings['playpops_header'];
				
				if (strlen($playpops_header) > 60)
				{
					// $defaultSettings['playpops_header_color'] = "#6495ED";
					echo '<div class="error">
					<p>PlayPops for Wordpress : Your <b>Custom Header</b> is longer than 52 characters. Please, reduce it to accordingly.</p>
					</div>';
				}
			}
		}

		/*
		 Loads both the general and advanced settings from
		 * the database into their respective arrays. Uses
		 * array_merge to merge with default values if they're
		 * missing.
		 */

		function mmLoad_Settings() 
		{
	
			$this->general_settings = (array) get_option( $this->general_settings_key );
			
			// Merge with defaults
			$this->general_settings = array_merge( array(
	
				'general_option' => 'General value'
	
			), $this->general_settings );
			
		}
	
	
		/*
		Registers the general settings via the Settings API,
		* appends the setting to the tabs array of the object.
		*/

		function mmRegister_general_settings() 
		{
	
			$this->plugin_settings_tabs[$this->general_settings_key] = __('General', 'wpytp');
	
			register_setting( 
				$this->general_settings_key, // A settings group name. Must exist prior to the register_setting call. This must match the group name in settings_fields()
				$this->general_settings_key // The name of an option to sanitize and save. 
			);
		
			// register the section
			add_settings_section( 
				'section_general',															// ID to identify this section and to register options
				__('PlayPops for WordPress Settings', 'wpytp'), // Title to be displayed on the administration page
				array( &$this, 'mmSection_General_Desc' ), 			// Callback used to render the description of the section
				$this->general_settings_key );									// Page on which to add this section of options
			
			// -------------------------------------------
			// Fields
			// --------------------------------------------
			// youtube api secret
			add_settings_field( 
				'playpops_youtube_key', 													// id: name of the array index
				__('Playpops API Key:', 'wpytp'), 									// title: label
				array( &$this, 'mmField_Playpops_Key' ), 					// callback: funtion
				$this->general_settings_key, 'section_general' ); // page: The menu page on which to display this field.
				
			// facebook page url
			add_settings_field( 
				'playpops_url', 																	// name of the array index
				__('Facebook Page Url:', 'wpytp'), 								// title label
				array( &$this, 'mmField_Playpops_Url' ), 					// callback funtion
				$this->general_settings_key, 'section_general' ); // The menu page on which to display this field.
	
			// header custom color
			add_settings_field( 
				'playpops_header_color', 
				__('Custom Header Color:', 'wpytp'), 
				array( &$this, 'mmField_Playpops_Header_Color' ), 
				$this->general_settings_key, 'section_general' );
	
			// custom header
			add_settings_field( 
				'playpops_header', 
				__('Custom Header:', 'wpytp') , 
				array( &$this, 'mmField_Playpops_Header' ), 
				$this->general_settings_key, 'section_general' );
	
			// skip message
			add_settings_field( 
				'playpops_skip_message', 
				__('Custom Skip Message:', 'wpytp') , 
				array( &$this, 'mmField_Playpops_Skip_Message' ), 
				$this->general_settings_key, 'section_general' );
	
			// transparency
			add_settings_field( 
				'playpops_transparency', 
				__('Popup Transparency:','wpytp') , 
				array( &$this, 'mmField_Playpops_Transparency' ), 
				$this->general_settings_key, 'section_general' );
				
			// popup type
			add_settings_field( 
				'playpops_popuptype', 
				__('Popup Type:', 'wpytp') , 
				array( &$this, 'mmField_Playpops_Popuptype' ), 
				$this->general_settings_key, 'section_general' );
				
			// skip message
			add_settings_field( 
				'playpops_sharemsg', 
				__('Custom Share Message:', 'wpytp') , 
				array( &$this, 'mmField_Playpops_Sharemsg' ), 
				$this->general_settings_key, 'section_general' );
	
			// like or share
			
		}

		/* 
		------------------------------------------------------------------------------------------------
		* The following methods provide descriptions
		* for their respective sections, used as callbacks
		* with add_settings_section
		---------------------------------------------------------------------------------------------------
		*/
		function mmSection_General_Desc() 
		{
			//echo 'General section description goes here.';
		}

		/*
		General Option field callback, renders a
		* text input, note the name and value.
		*/
		
		function mmField_Playpops_Key() 
		{
		
			?>
		
			<input type="text" name="<?php echo $this->general_settings_key; ?>[playpops_youtube_key]" value="<?php echo (isset($this->general_settings['playpops_youtube_key'])?esc_attr($this->general_settings['playpops_youtube_key']):''); ?>" />
		
			<?php
		
		}

		function mmField_Playpops_Url() 
		{
		
			?>
		
			<input type="text" name="<?php echo $this->general_settings_key; ?>[playpops_url]" value="<?php echo (isset($this->general_settings['playpops_url'])?esc_attr($this->general_settings['playpops_url']):''); ?>" />
		
			<?php
		
		}

		/* 
			General Option field callback, renders a
			* text input, note the name and value.
		*/

		function mmField_Playpops_Header_Color() 
		{
		
			?>
			<input type="text" name="<?php echo $this->general_settings_key; ?>[playpops_header_color]" id="playpops_header_color" data-default-color="#6495ED" value="<?php echo (isset($this->general_settings['playpops_header_color'])?$this->general_settings['playpops_header_color']:'#6495ED'); ?>" />
			<?php
		}

		/*
		General Option field callback, renders a
		* text input, note the name and value.
		*/

		function mmField_Playpops_Header() 
		{
			?>
			<input type="text" name="<?php echo $this->general_settings_key; ?>[playpops_header]" value="<?php echo (isset($this->general_settings['playpops_header'])?esc_attr($this->general_settings['playpops_header']):'Like Us in Facebook to Stay in Touch with Our Latest Update'); ?>" />
			<?php
		}

		/*
		General Option field callback, renders a
		* text input, note the name and value.
		*/

		function mmField_Playpops_Skip_Message() 
		{
			?>
			<input type="text" name="<?php echo $this->general_settings_key; ?>[playpops_skip_message]" value="<?php echo (isset($this->general_settings['playpops_skip_message'])?esc_attr($this->general_settings['playpops_skip_message']):'Skip this Step and Continue Watching'); ?>" />
			<?php
		}

		/*
		General Option field callback, renders a
		* text input, note the name and value.
		*/

		function mmField_Playpops_Transparency() 
		{
			?>
			<input type="text" name="<?php echo $this->general_settings_key; ?>[playpops_transparency]" value="<?php echo (isset($this->general_settings['playpops_transparency'])?esc_attr($this->general_settings['playpops_transparency']):'0.7'); ?>" /> <em>(opacity)</em>
			<?php
		}
		
		/*
		General Option field callback, renders a
		* radio input, note the name and value.
		*/

		function mmField_Playpops_Popuptype() 
		{
			$valuetype = "";
			
			?>
			<!--
      <input type="text" name="<?php // echo $this->general_settings_key; ?>[playpops_transparency]" value="<?php // echo (isset($this->general_settings['playpops_transparency'])?esc_attr($this->general_settings['playpops_transparency']):'0.7'); ?>" /> <em>(opacity)</em>
			-->
		<?php
      
		if (isset($this->general_settings['playpops_popuptype']))
			$valuetype = esc_attr($this->general_settings['playpops_popuptype']);
			
		// ** lite version only
		$valuetype = "Lite";
			
		// Check the correct value of the radio			
		if ($valuetype == "Share")
		{
			$optionshare = "checked";
			$optionlike = "";
		}
		else
		{
			$optionshare = "";
			$optionlike = "checked";
		}
		
		?>
      <input type="radio" name="<?php echo $this->general_settings_key; ?>[playpops_popuptype]" value="Like" <?php echo $optionlike; ?>>Like
			<br>
			<input type="radio" name="<?php echo $this->general_settings_key; ?>[playpops_popuptype]" value="Share" <?php echo $optionshare; ?>>Share <i>(This option is not available in Lite version)</i>
			<?php
		}
		
		/*
		General Option field callback, renders a
		* text input, note the name and value.
		*/

		function mmField_Playpops_Sharemsg() 
		{
			?>
			<input type="text" name="<?php echo $this->general_settings_key; ?>[playpops_sharemsg]" value="<?php echo (isset($this->general_settings['playpops_sharemsg'])?esc_attr($this->general_settings['playpops_sharemsg']):'Share This Video with Your Friends'); ?>" />
			<?php
		}

		/*
		---------------------------------------------------------------------
		* Called during admin_menu, adds an options
		* page under Settings called My Settings, rendered
		* using the mmPlugin_options_page method.
		---------------------------------------------------------------------
		*/

		function mmAdd_admin_menus() 
		{
			
			add_options_page(
				__('PlayPops for WordPress Settings', 'wpytp'), // The text to be displayed in the title tags of the page when the menu is selected 
				__('PlayPops for WordPress', 'wpytp'), // The text to be used for the menu 
				'manage_options', 
				$this->plugin_options_key, 
				array( &$this, 'mmPlugin_options_page' ) 
				);
		}

	
		/*
		Plugin Options page rendering goes here, checks
		* for active tab and replaces key with the related
		* settings key. Uses the mmPlugin_options_tabs method
		* to render the tabs.
		*/

		function mmPlugin_options_page() 
		{
		
			$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
			
			?>
		
			<div class="wrap">
		
				<?php $this->mmPlugin_options_tabs(); ?>
		
				<form method="post" action="options.php">
		
					<?php wp_nonce_field( 'update-options' ); ?>
		
					<?php settings_fields( $tab ); ?>
		
					<?php do_settings_sections( $tab ); ?>
		
					<?php submit_button(); ?>
		
				</form>
		
			</div>
		
			<?php
		
		}

	
		/*
		Renders our tabs in the plugin options page,
		* walks through the object's tabs array and prints
		* them one by one. Provides the heading for the
		* mmPlugin_options_page method.
		*/

		function mmPlugin_options_tabs() {
		
			$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
		
			screen_icon();
		
			echo '<h2 class="nav-tab-wrapper">';
		
			foreach ( $this->plugin_settings_tabs as $tab_key => $tab_caption ) {
		
				$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
		
				echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin_options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';	
		
			}
		
			echo '</h2>';
		
		}

	};
	// end of object



// Initialize the plugin
add_action( 'plugins_loaded', create_function( '', '$settings_api_tabs_playpops_plugin = new Settings_API_Tabs_Wpytp_Plugin;' ) );