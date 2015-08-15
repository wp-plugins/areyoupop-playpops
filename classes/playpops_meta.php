<?php

class mmPlayPops_Meta
{

	private $_video_length = array('25'=>'25%','50'=>'50%','75'=>'75%'); 
	//public $prepare_switch = true;   

  // Construct
	public function __construct()
	{

		$plugin_b = plugin_basename(__FILE__);
		$plugin_b = "playpops.php";

		// exit();
		add_filter('plugin_action_links_' . $plugin_b, 'mmPlayPopsFb_ActionLinks');
		add_action('add_meta_boxes', array( $this, 'mmPlayPopsFb_AddMetaBox'));
		add_action('save_post', array( $this, 'mmPlayPopsFb_Save'));
		add_filter('the_content', array($this, 'mmPlayPopsFb_ConvertPlayer'), 20);

	}



	// -------------------------------------------------------------------------------------------

	// -------------------------------------------------------------------------------------------

	// Functions

	// -------------------------------------------------------------------------------------------

	// -------------------------------------------------------------------------------------------

	
	public function mmGetShareButtons($urlshare, $playpops_skip_message)
	{
		/*
		<!-- Reddit -->
		<a href="http://reddit.com/submit?url=$urlshare&title=areyoupop Share Buttons" target="_blank">
			<img src="$dirImages/reddit.png" alt="Reddit" />
		</a>
		-->
		*/
		
		// ** This function is not available in Lite
		
		return $sharebuttons;
		
	}

	public function mmGetVideoLength($video_id, $playpops_key)
	{

		// If playpops key is not defined then exit
		if (empty($playpops_key))
			return;
		
		// Define url for google api calls
		$url = 'https://www.googleapis.com/youtube/v3/videos?id=' . $video_id . '&part=contentDetails&key=' . $playpops_key;
		// AIzaSyDUUjO-exXUFhEIlDXfbm4N6ocMGA-ZFnk
		
		//Using cURL php extension to make the request to youtube API
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//$feed holds a rss feed xml returned by youtube API
		$json_response = curl_exec($ch);

		curl_close($ch);

 		//$entry = $xml->entry[0];

		$responseObj = json_decode($json_response, true);

		//print_r($responseObj);
				
		// duration	
		$duration = $responseObj['items'][0]['contentDetails']['duration'];

		// Convert to seconds
		preg_match_all('/(\d+)/', $duration, $parts);

		$hours = floor($parts[0][0] / 60);
		$minutes = $parts[0][0] % 60;
		$seconds = $parts[0][1];
		$totalseconds = ($hours * 3600) + ($minutes * 60) + $seconds; 

		return $totalseconds;		

		

	}

	

	// Get current url

	public function mmCurPageURL() 

	{

 		$pageURL = 'http';

 
		if (isset($_SERVER["HTTPS"]))
			if ($_SERVER["HTTPS"] == "on") 
			{
				$pageURL .= "s";
			}

 		

		$pageURL .= "://";

 

 		if ($_SERVER["SERVER_PORT"] != "80") 

			$pageURL .= $_SERVER["SERVER_NAME"].":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];

 		else 

			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

			

			 	

		return $pageURL;

	}

	

	// -------------------------------------------

	// functions : Social Functions

	// -------------------------------------------

	public function mmFacebookLike($fb_pageurl, $playpops_skip_message)
	{
		
		// Define the popup skip message		
		$popskipmsg = '<div style="clear:both;"><a id="mmSkipVideo" href="javascript:void(0); ">' . $playpops_skip_message . '</a></div>';

		// define the small box
		$iframe_box320 = '<div id="fbframelikeSmall" style="left:60px; top:2px; display:none; position:absolute;">
            
            <div class="fb-page" data-href="' . $fb_pageurl . '" data-width="180" data-height="70" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false" data-show-posts="false"></div>
            
        </div>';
		
		// define the bigger size frame		
		$iframe_box600 = '<div id="fbframelikeLarge" style="left:10px; top:2px; display:block; ">
            
            <div class="fb-page" data-href="' . $fb_pageurl . '" data-width="500" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="true" data-show-posts="false"></div>
    
		</div>';
		
		$iframe_box = $iframe_box320 . $iframe_box600 . $popskipmsg;

		return $iframe_box;

	}

	

	// -------------------------------------------

	// functions : Admin Functions

	// -------------------------------------------

	

	// Add Settings Link

  public function mmPlayPops_ActionLinks($links)

	{

		$links[] = '<a href="' . get_admin_url(null, 'options-general.php?page=playpops_plugin_options') . '">Settings</a>';

		

		return $links;

	}

	

	

	//

	function mmRearrangeVideos($videos_iframes)

	{

		

		// Open an array empty

		$new_videos = array();

		

		// reorder the videos dims

		for ($j = 0; $j < count($videos_iframes[0]); $j++)

		{

			// define the index re-order

			if ($j == 1)

				$row = 3;

			elseif ($j == 2)

				$row = 1;

			elseif ($j == 3)

				$row = 2;

			else

				$row = $j;



			// Re-organize the array

			$new_videos[0][$row] = $videos_iframes[0][$j];

		}

		

		return $new_videos;



	}



	//

	function mmPlayPopsFb_ArrayConcatenate($videos_frm, $videos_obj)

	{

		

		// Open an array empty

		$new_videos = array();

		

		// Determine which array is bigger and save count as max value

		if (count($videos_frm[0]) > count($videos_obj[0])){

			$videos_max = count($videos_frm[0]);

		}

		else{

			$videos_max = count($videos_obj[0]); 

		

				

		// Determine how many videos need to be added

		$videos_total = count($videos_frm[0]) + count($videos_obj[0]);

		$video_counter = 0;

		$video_counter_j = 0;

		}

		for ($i = 0; $i < count($videos_frm); $i++)

		{

			

			// reorder the videos dims

			for ($j = 0; $j < count($videos_frm[0]); $j++)

			{	

				//echo $i . " " . $video_counter_j . " | " . $i . " " . $j . "<br />";

				//echo $videos_frm[$i][$j] . "<br />";

				$new_videos[$i][$video_counter_j] = $videos_frm[$i][$j];

				

				//$video_counter = $video_counter + 1;

				$video_counter_j = $video_counter_j + 1;

			}

		

			

			for ($j = 0; $j < count($videos_obj[0]); $j++)

			{	

				//echo $i . " " . $video_counter_j . " | " . $i . " " . $j . "<br />";

				//echo $videos_obj[$i][$j] . "<br />";

				$new_videos[$i][$video_counter_j] = $videos_obj[$i][$j];

				

				//$video_counter = $video_counter + 1;

				$video_counter_j = $video_counter_j + 1;

			}

			

			// reset the counter

			//$video_counter = 0;

			$video_counter_j = 0;

			

		}

		

		return $new_videos;



	}

	// http://stackoverflow.com/a/5831191/430112



	function mmGetiFrames($text) 

	{

		

		// Start with an empty array

		$matches = array();

		

		// Find all iframe embedded youtube players with the link first

		

		// width height source

		$pattern[0] = '~<iframe[\w\W\s\S].*width=["\'](\d+)["\'"].*height=["\'](\d+)["\'].*src="\/\/www.youtube.com\/embed\/([^"?]+).*<\/iframe>~';

		$pattern[1] = '~<iframe.*width="(\d+)".*height="(\d+)".*youtube[.]com\/embed\/([^"?]+).*<\/iframe>~';

		$pattern[2] = '~<iframe.+?width="(\d+)".*height="(\d+)" src="\/\/www.youtube-nocookie.com\/embed\/([a-zA-Z0-9_-]{11}).+?[^>]+?><\/iframe>~';

		$pattern[3] = '~<iframe.*width="(\d+)".*height="(\d+)" src="http:\/\/www.youtube.com\/embed\/([a-zA-Z0-9_-]{11}).+?[^>]+?>.*<\/iframe>~';

		$pattern[4] = '~<iframe.*width="(\d+)".*height="(\d+)"\s*https?:\/\/www.youtube(?:-nocookie)?.com\/(?:v|embed)\/([a-zA-Z0-9_-]+).*<\/iframe>~';

		$pattern[5] = '~<iframe.+?width:(\d+).*height:(\d+).*src="\/\/www.youtube.com\/embed\/([a-zA-Z0-9_-]{11}).+?[^>]+?><\/iframe>~';

		

		// src width height

		$pattern[6] = '~<iframe.+?src="\/\/www.youtube-nocookie.com\/embed\/([a-zA-Z0-9_-]{11}).+?width="(\d+)" height="(\d+)" [^>]+?><\/iframe>~';

		$pattern[7] = '~<iframe.+?src="\/\/.*youtube[.]com\/embed\/([^"?]+)[\w\W\s\S]*width=["\'](\d+)["\'"].*height=["\'](\d+)["\'][\w\W\s\S]*<\/iframe>~';	

		$pattern[8] = '~<iframe.+?src="http:\/\/www.youtube.com\/embed\/([a-zA-Z0-9_-]{11}).+?width="(\d+)" height="(\d+)" [^>]+?><\/iframe>~';

			

		// Find all patterns for iframe

		for ($x = 0; $x <= count($pattern) - 1; $x++) 

		{

			// If regex search finds a match then exit and process

			if(preg_match_all($pattern[$x], $text, $matches, PREG_SET_ORDER))

				break;

		}

		

		// Re-arrange the array if the pattern was a found to be pattern #2

		if($x === 6 || $x === 7 || $x === 8)

			$matches_processed = $this->mmRearrangeVideos($matches);	

		else

			$matches_processed = $matches;

			

		// Return the array of iframes		

		return $matches_processed;



	}   



	// http://stackoverflow.com/a/5831191/430112



	// Adds the meta box container

	public function mmPlayPopsFb_AddMetaBox($postType) 

	{

		

		$types = array('post', 'page');



	  if(in_array($postType, $types))

		{



			add_meta_box(



			 'playpops_meta_box',

			 __( 'PlayPops for WordPress Options', 'wpytp' ),

			 array( $this, 'mmPlayPopsFb_RenderMetaBoxContent' ),

			 $postType,

			 'advanced',

			 'high');

		}

	}



	// Save the meta when the post is saved.

	// @param int $post_id The ID of the post being saved.

	

	public function mmPlayPopsFb_Save($post_id) 

	{



		if(isset($post_id) && count($_POST))

		{



      if ( 'post' == $_POST['post_type'] || 'page' == $_POST['post_type'] ) 

			{

	    

				// get post variables				

				$playpops_enabled = ( $_POST['playpops_enabled'] );

				$playpops_video_length = ( $_POST['playpops_video_length'] );

				$playpops_share_element = ( $_POST['playpops_share_element'] );

				

				// update post meta and save variables individually

				update_post_meta( $post_id, '_playpops_enabled', ($playpops_enabled) );

				update_post_meta( $post_id, '_playpops_video_length', ($playpops_video_length) );

				update_post_meta( $post_id, '_playpops_share_element', ($playpops_share_element) );

			}

		}

	}





	// Render Meta Box content.

	// @param WP_Post $post The post object.

	public function mmPlayPopsFb_RenderMetaBoxContent( $post ) 

	{

 

		// Use get_post_meta to retrieve an existing value from the database.

		$playpops_enabled = get_post_meta( $post->ID, '_playpops_enabled', true );

		$playpops_video_length = get_post_meta( $post->ID, '_playpops_video_length', true );

		$playpops_share_element = get_post_meta( $post->ID, '_playpops_share_element', true );

		

		// Determine with radio option is on

		if($playpops_share_element == "video")

		{

			$share_video = "checked";

			$share_post = "";

		}

		else

		{

			$share_video = "";

			$share_post = "checked";

		}

		

		?>

    <ul>

    	<li>

      	

      	<!-- Display the form, using the current value. -->

        <input type="checkbox" id="playpops_enabled" name="playpops_enabled" value="yes" <?php echo checked('yes', esc_attr( $playpops_enabled ), false ); ?>/>

        <label for="playpops_enabled"><?php echo __(' Add PlayPops to the embedded video in this ' . $post->post_type, 'wpytp'); ?></label>

      </li>

      <li>

      	<!-- // Display the form, using the current value. -->

        <select name="playpops_video_length" id="playpops_video_length">

        	<option value=""><?php echo __( 'Select Video Length', 'wpytp' ); ?></option>

          	

            <?php

            foreach($this->_video_length as $key=>$value):

							?>

              <option value = "<?php echo $key; ?>" <?php echo selected($key, $playpops_video_length, false ); ?>><?php echo __($value, 'wpytp' ); ?></option>

              

          	<?php

						endforeach;



						unset($value, $value);



						?>

    		</select>

			

      	<label for="playpops_video_length"><?php echo __( ' Choose video length percent to trigger popup', 'wpytp' ); ?></label>



    	</li>

      <li>

      	<label for="playpops_share_element"><?php echo __( 'When Share option is enabled, choose what to share', 'wpytp' ); ?></label> : <input <?php echo $share_video; ?> name="playpops_share_element" type="radio" value="video" />Video

<input <?php echo $share_post; ?> name="playpops_share_element" type="radio" value="post" />Page / Post

				

      </li>

      

		</ul>

    

    <?php

	}    

	

	// Add create a newplayer from the original player

	public function mmCreatePlayer($videos_iframes)

	{

		

		// Look for iframe ids

		preg_match_all('/.*?id=(\d+).*/', $videos_iframes[0][0], $matches, PREG_SET_ORDER);

		

		// Add the id to the player				

		if(!count($matches) > 0)

		{

			// $newpl = str_replace("<iframe", "<iframe id=\"mmPlayer\"", $videos_iframes[0][0]);

			$newpl = "<iframe id=\"mmPlayer\" width=\"" . $videos_iframes[0][1] . "\" height=\"" . $videos_iframes[0][2] . "\" src=\"https://www.youtube.com/embed/" . $videos_iframes[0][3] . "?enablejsapi=1\" frameborder=\"0\" allowfullscreen></iframe>";

			

			// Return the new player

			return $newpl;

		}

	}

	

	

	// Add the new player to the page

	public function mmPlayPopsFb_AddPop($pop_video, $newpl)
	{

		$playpops_transparency = "";
		$playpops_header_color = "";
		$playpops_header = "";
		$playpops_popuptype = "";		
		$playpops_sharemsg = "";

		$width = $pop_video[0][1];

		$height = $pop_video[0][2];

		$video_id = $pop_video[0][3];

		

		// Pop up parameters

		$padding = 20;

		$border = 10;

		$borderradius = 22;

		$boxwidth = $width - (2 * $border);

		$boxheight = 200;

		// --------------------------------------
		// Options: Read the array with all options saved in db
		// --------------------------------------
		
		$playpops_settings = get_option('playpops_general_settings');

		// Restore variable values
		if (isset($playpops_settings['playpops_youtube_key']))
			$playpops_key = $playpops_settings['playpops_youtube_key'];
		
		if (isset($playpops_settings['playpops_url']))
			$playpops_url = $playpops_settings['playpops_url'];
		else
			$playpops_url = "http://www.facebook.com/areyoupop";
			
		if (isset($playpops_settings['playpops_header_color']))
			$playpops_header_color = $playpops_settings['playpops_header_color'];
		else
			$playpops_header_color = "#6495ED";
			
		if (isset($playpops_settings['playpops_header']))
			$playpops_header = $playpops_settings['playpops_header'];
		else
			$playpops_header = "Like Us in Facebook to Stay in Touch with Our Latest Update";
			
		if (isset($playpops_settings['playpops_skip_message']))
			$playpops_skip_message = $playpops_settings['playpops_skip_message'];
		else
			$playpops_skip_message = "Skip this Step and Continue Watching";
			
		if (isset($playpops_settings['playpops_transparency']))
			$playpops_transparency = $playpops_settings['playpops_transparency'];
			
		if (isset($playpops_settings['playpops_popuptype']))
			$playpops_popuptype = $playpops_settings['playpops_popuptype'];
		else
			$playpops_popuptype = "Like";
			
		if (isset($playpops_settings['playpops_sharemsg']))
			$playpops_sharemsg = $playpops_settings['playpops_sharemsg'];
		else
			$playpops_sharemsg = "Share Video with Your Friends";

		//1.Determine the length of any YouTube Video

		//$videoEntry = $yt->getVideoEntry($video_id);

		$mmVideoLength = $this->mmGetVideoLength($video_id, $playpops_key);

		//3. Inside the script there should be a variable named; $TriggerLength which will have one of the following values

		// assigned; 25, 50 or 75.

		$mmTriggerPercentSaved = get_post_meta(get_the_ID(), '_playpops_video_length', true );

		

		// Determine the TriggerLength

		if(isset($mmTriggerPercentSaved))

			$mmTriggerPercent = $mmTriggerPercentSaved;

		else 

			$mmTriggerPercent = 25;

			

		// Calculate the Trigger Length

		$mmTriggerLength = $mmVideoLength * ($mmTriggerPercent / 100);

		

		// Get post id

		$post_id = get_the_ID();

		$playpops_share_element = get_post_meta( $post_id, '_playpops_share_element', true );

				
		// ------------------------------------------------------------
		// Define the new player string
		// ------------------------------------------------------------

		$playpopsoverlay =  '<div id="playpops-overlay" style="display:none;

			background-color:#777;

			opacity:' . $playpops_transparency . ';

			-moz-opacity:'.$playpops_transparency.';

			-webkit-opacity:'.$playpops_transparency.';

			filter:alpha(opacity=' . ($playpops_transparency * 100) . ');

			-ms-filter:progid:DXImageTransform.Microsoft.Alpha(Opacity=' . ($playpops_transparency * 100) . ');

			-khtml-opacity:' . $playpops_transparency . ';

			cursor:pointer;

			height:' . $height . 'px;

			position:absolute;

			z-index:1100;"></div>';

			

		// PlayPops wrapper Begins Here

		$playpopswrapper = '<div id="playpops-wrapper">';

				

		

		// Define next div		

		$playpopsdialog_header = '<div id="playpops" style="display:none; width: 100%; height: auto; top: 25px; box-sizing:unset;">';

			

		$playpopsbox_closebtn = '<a id="mmClosePop" href="javascript:void(0); ">



						<img class="close" src="' . PLAYPOPS_PLUGIN_URL . 'images/close.png" alt="close" style="width:24px;height:24px;float:right;" />



					</a>';

					

		// This variable holds the popup header 						

		$playpopsbox_title = '<div class="header" style="font-weight: bold; 



						background: none repeat scroll 0% 0% ' . $playpops_header_color . '; 

						padding: 10px; 

						color: white; 

						border-style : solid;

						border-top-left-radius:' . ($borderradius / 2) . 'px;

						border-top-right-radius:' . ($borderradius / 2) . 'px;

						border-bottom-right-radius:0px;

						border-bottom-left-radius:0px; 

						margin-bottom: 10px;">' . $playpops_header . '</div>';



		// Begin div for facebook widget			
		$socialpop_header = 	'<div class="facebookwidget">';

	
		// ** Remove this one other social network options are implemented
		$socialnet = "facebook";
		
		// ** This value is fixed in Lite version
		$playpops_popuptype = "Like";

		// Add Social popup depending on network
		switch ($socialnet) 
		{

    		case "facebook":

				// Determine the popup type and choose like or share
				if($playpops_popuptype == "Like")
				{
	
					// Define default facebook page
					if (empty($playpops_url))
						$playpops_url = "http://www.facebook.com/areyoupop";
						
					// Popup the facebook like box
					$iframe_box = $this->mmFacebookLike($playpops_url, $playpops_sharemsg);
						
				}
				else
				{

					// Else, add the facebook share box instead
					// Determine if the video or the page needs to be shared
					if($playpops_share_element == "video")
					{
						
						// Define the video url to share
						$urlsharevideo = "https://www.youtube.com/watch?v=" . $video_id;
						$share_element = $this->mmGetShareButtons($urlsharevideo, $playpops_sharemsg);
						
					}
					else
					{

						// Build the share post/page element
						$share_element = $this->mmGetShareButtons(htmlspecialchars($this->mmCurPageURL()), $playpops_sharemsg);

          			}
					// end if

					// Attach the appropriate video or post share code.
					$iframe_box = '<div class="sharemessage">' . $playpops_sharemsg . '</div>' . $share_element;
					
				}
				// end if

				break;

    	
			case "twitter":

        echo "i is bar";

        break;

    

			case "gplus":

        echo "i is cake";

        break;

		}

		// end social popups strings 

		$socialpop_footer = '</div><!-- social pop footer -->'; 

		// Add the string with facebook popup to the newPlayer string
		$break = '<br />';

		// Define the string for the javascript player
		$playpopsdialog_footer = '</div></iframe>

		<div id="mmTriggerLength" style="display:none">' . $mmTriggerLength . '</div></div>';

		// Attach the scrip to the player
		$newplayer = 

		$playpopsoverlay . 
		$playpopswrapper .

			$newpl .
			
			// Dialog Popup
			$playpopsdialog_header .	
				$playpopsbox_closebtn .

				$playpopsbox_title .
				
				// Social Popup
				$socialpop_header . 
					$iframe_box. 
				$socialpop_footer . 
				
			$playpopsdialog_footer;			

		// -----------------------------------------------
		// End of newPlayer String
		// -----------------------------------------------

		return $newplayer;

	}



	// -----------------------------------------------------------------

	// Main Function: Get Youtube ID

	// -----------------------------------------------------------------      

	public function mmPlayPopsFb_ConvertPlayer($content)
	{

		// Get post id
		$post_id = get_the_ID();

		// Get meta variable from Post iD. Returns Yes or No string
		$playpops_enabled = get_post_meta( $post_id, '_playpops_enabled', true );

		// If the post has activated PlayPops Embedding then modify content output
		if($playpops_enabled != 'yes')

			return $content;

		// Get trigger length percent value
		$playpops_video_length = get_post_meta( $post_id, '_playpops_video_length', true );

		// Get the share varibles from post meta
		$playpops_share_element = get_post_meta($post_id, '_playpops_share_element', true);

		// Find iframes: Save youtube iframes only
		$videos_iframes = $this->mmGetiFrames($content);

		// If the iframe doesn't have an id then add one
		$newpl = $this->mmCreatePlayer($videos_iframes);

		// Pass the array to create the header for the player
		$newplayer = $this->mmPlayPopsFb_AddPop($videos_iframes, $newpl);

		// Replace the iframe with the new iframe		
		$content = str_replace($videos_iframes[0][0], $newplayer, $content);

		// end if
		return $content;	

	}

	// end function

	

}

// End Class



$mmPlayPops_Meta = new mmPlayPops_Meta();