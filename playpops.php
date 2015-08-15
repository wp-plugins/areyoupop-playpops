<?php

/*
 @package playpops for wordpress
 * @author Victor Mendez / Gowri Sankar.R / Vanleurth / AreYouPop
 * Requirements : None

	Plugin Name: Areyoupop PlayPops for WordPress Lite
	Plugin URL: http://areyoupop.com/playpops/
	Description: Get More Likes to Your Facebook pages by combining the power of YouTube and Wordpress Posts. Add a video and get more likes
	Version: 2.0.2 lite
	Author: AreYouPop
	Author URI: http://areyoupop.com/

*/

	// --------------------------------------------------
	// --------------------------------------------------
	// Comment this Error Debugging Section When Move to Production
	
	//ini_set('display_errors', true);
	
	// ** Turn off error reporting
	//error_reporting(0);
	
	// Report runtime errors
	//error_reporting(E_ALL | E_NOTICE | E_USER_NOTICE);
	
	// Report all errors
	//error_reporting(E_ALL);
	
	// Same as error_reporting(E_ALL);
	//ini_set("error_reporting", E_ALL);
	
	// Report all errors except E_NOTICE
	//error_reporting(E_ALL & ~E_NOTICE);
	
	// --------------------------------------------------
	// --------------------------------------------------

	if ( ! defined( "PATH_SEPARATOR" ) ) 
	{ 

		if ( strpos( $_ENV[ "OS" ], "Win" ) !== false ) 
			define( "PATH_SEPARATOR", ";" ); 
		else 
			define( "PATH_SEPARATOR", ":" ); 
	}

		
	//echo $path;
	// Determine path
	$path = dirname(__FILE__);
	set_include_path(get_include_path() . PATH_SEPARATOR . $path);
	
	require_once (dirname(__FILE__) . '/classes/settings.php');
	require_once (dirname(__FILE__) . '/classes/playpops_meta.php');
	
	define('PLAYPOPS_PLUGIN_NAME', plugin_basename(__FILE__));
	define('PLAYPOPS_PLUGIN_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);
	define('PLAYPOPS_PLUGIN_URL', plugins_url('/', __FILE__));
	
					
	if (!class_exists('PlayPops')) 
	{

		class PlayPops
		{
	
			// @var PlayPops
			static private $_instance = null;
	
			// Get PlayPops object
			// @return PlayPops
	
			static public function getInstance()
			{
				if (self::$_instance == null) 
				{
					self::$_instance = new PlayPops();
				}
	
				return self::$_instance;
			}
	
			// Construct  ---------------------------------------
			
			private function __construct()
			{
				
				register_activation_hook(PLAYPOPS_PLUGIN_NAME, array(&$this, 'mmPluginActivate'));
				register_deactivation_hook(PLAYPOPS_PLUGIN_NAME, array(&$this, 'mmPluginDeactivate'));
				register_uninstall_hook(PLAYPOPS_PLUGIN_NAME, array(PLAYPOPS_PLUGIN_DIR, 'mmPluginUninstall'));    
				
									
				//
				
				if (is_admin())
				{
									
					add_action('admin_enqueue_scripts', array(&$this, 'mmScriptsAdmin'));
				}
				else
				{
					add_action('wp_enqueue_scripts', array(&$this, 'mmScriptsPlaypops'));
					add_action('wp_enqueue_scripts', array(&$this, 'mmStylesPlaypops'));			
					add_action('wp_footer', array(&$this, 'mmPlayPopsBody'));
				}
				
			}
	
			// ------------------------------------------------------------
			// Functions
			// ------------------------------------------------------------
			public function mmPlayPopsBody()
			{
				?>
        		<script>
					window.fbAsyncInit = function() {
						FB.init({
						appId      : '682547871766419',
						xfbml      : true,
						version    : 'v2.3'
						});
					};
					
					(function(d, s, id){
						var js, fjs = d.getElementsByTagName(s)[0];
						if (d.getElementById(id)) {return;}
						js = d.createElement(s); js.id = id;
						js.src = "//connect.facebook.net/en_US/sdk.js";
						fjs.parentNode.insertBefore(js, fjs);
					}(document, 'script', 'facebook-jssdk'));
				</script>
        <script type="text/javascript" src="https://www.youtube.com/iframe_api"></script>
        <?php
			}
			
			// Loading Scripts and Styles
			public function mmScriptsAdmin()
			{
				
				// Enqueue the color picker
				wp_enqueue_style( 'wp-color-picker' );
								
				wp_enqueue_script(
					'PlayPops-admin-script',
					plugin_dir_url(__FILE__) . 'js/playpops-admin-script.js', 
					array( 'wp-color-picker' ), 
					false, 
					true
				);
			}
	
			public function mmStylesPlaypops()
			{
				// Register the style like this for a plugin:
				wp_register_style( 'playpops-style', plugins_url( '/css/playpops-style.css', __FILE__ ), array(), '20150402', 'all' );
				wp_enqueue_style( 'playpops-style' );
			}
			
				
			public function mmScriptsPlaypops()
			{
				
				// Attach javascript to the bottom of body
				wp_enqueue_script(
					'playpopsbody',  // $handle
					plugin_dir_url(__FILE__) . 'js/playpops_events.js', // $src
					array(), // $deps
					false, // $ver
					true // $in_footer
				);
			}    
	
			// Plugin Activation and Deactivation
			// Activate plugin
			// * @return void
			// 
			
			public function mmPluginActivate()
			{

				$defaultSettings = array();

				$settings = get_option('playpops_general_settings');

				// header color
				if(!isset($settings['playpops_youtube_key']))
					$defaultSettings['playpops_youtube_key'] = "";
					
				// header color
				if(!isset($settings['playpops_header_color']))
					$defaultSettings['playpops_header_color'] = "#6495ED";
				
				// header text
				if(!isset($settings['playpops_header']))
					$defaultSettings['playpops_header'] = "Like Us in Facebook to Stay in Touch with Our Latest Update";
				
				// skip message text
				if(!isset($settings['PlayPops_skip_message']))
					$defaultSettings['PlayPops_skip_message'] = "Skip this Step and Continue Watching";
				
				// Dialog Transparency
				if(!isset($settings['PlayPops_transparency']))
					$defaultSettings['PlayPops_transparency'] = "0.7";
				
				// save options
				update_option('playpops_general_settings', $defaultSettings);       	
			}

			// Deactivate plugin
			// @return void
			public function mmPluginDeactivate()
			{
			}

			// Uninstall plugin
			// @return void
				
			static public function mmPluginUninstall()
			{
			}

					
		}
		// End Class
	}
	// End if
	
	//instantiate the class
	if (class_exists('PlayPops')) 
	{
		$PlayPops =  PlayPops::getInstance();
	}