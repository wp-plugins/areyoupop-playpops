	// youtube iframe ready
	function onYouTubeIframeAPIReady() 
	{
  	
		// Create an object				
		mmPlayer = new YT.Player('mmPlayer', {
    	events: {
      	'onReady': onPlayerReady,
      	'onStateChange': onPlayerStateChange
    	}
  	});
		
		console.log(mmPlayer);


		// Set timer to save video play location
		// Get the player element by Id tag
		//mmPlayer = document.getElementById(mmPlayer);
		
		setInterval(function() 
		{
			currentTime = mmPlayer.getCurrentTime();
			
			console.log("currentTime : " + currentTime + " Trigger : " + mmTriggerLength); 
		}, 1000);
	}
	
	// 4. The API will call this function when the video player is ready.
	function onPlayerReady(event) 
	{
		// Play video on Autoplay
		// event.target.playVideo();
		mmPlayVideo();
	}

	// 5. The API calls this function when the player's state changes.
	//    The function indicates that when playing a video (state=1),
	//    the player should play for six seconds and then stop.
		
	function onPlayerStateChange(event) 
	{
		
		//4.Once the video is played the script should look for the $TriggerLength value. After the video has passed that length it should activate a trigger event.
		
		//The event will only get triggered if the user pauses the video or when the video reaches the end.
		if( ( event.data == YT.PlayerState.ENDED || event.data == YT.PlayerState.PAUSED ) && currentTime > mmTriggerLength)
			mmPlayPopUp("block");
		else 
			mmPlayPopUp("none"); 
			
		// Save player staturs in global variable
		mmPlayerStatus = event.data
	}
	
	// Stop video
	function mmStopVideo() 
	{
		mmPlayer.stopVideo();
	}
	
	// Play video
	function mmPlayVideo() 
	{
		mmPlayer.playVideo();
	}

	
	function mmPlayPopUp(display) 
	{
		console.log("playpopsup " + display);
				
		document.getElementById("playpops-overlay").style.display = display;
		document.getElementById("playpops").style.display = display;
	}
	
	function mmResizeGrid()
	{
		
		// Get iframe dimensions
		mmIframeWidth = document.getElementById("mmPlayer").offsetWidth;
		mmIframeHeight = document.getElementById("mmPlayer").offsetHeight;
		
		// Get playpops dimensions
		mmPlayPopsDisplay = document.getElementById("playpops").style.display
		mmPlayPopsTop = document.getElementById("playpops").style.top
		
		// Calcs			
		mmIframeAspect = mmIframeHeight / mmIframeWidth;
		
		mmPlayPopsWidth = mmIframeWidth - 20;
		mmPlayPopsHeight = mmIframeHeight * 0.6;
		
		mmPlayPopsTop = 36.4; //(mmIframeHeight - mmPlayPopsHeight)/2;
		
			
		// set Display
		if (mmPlayerStatus == YT.PlayerState.PAUSED)
			mmDisplayStatus = "display: block";
		else
			mmDisplayStatus = "display: none";
		
		// Keep the blosk visible	
		mmDisplayStatus = "display: block";
			
		// Keep the Like box centered
		//if(mmIframeWidth > 520)
			
		if(mmPlayPopsWidth < 554)
		{
			//mmPlayPopsWidth = 554;
			mmDisplayFBLarge = "display: none";
			mmDisplayFBSmall = "display: block";
		}
		else
		{
			mmDisplayFBLarge = "display: block";
			mmDisplayFBSmall = "display: none";
			
			// Keep the height fixed based on the height of the pagelike height	
			if(mmPlayPopsHeight < 270)
				mmPlayPopsHeight = 270;
		}
		
		console.log("youtube frame width : " + mmIframeWidth + ". playpops width : " + mmPlayPopsWidth);
		
		
			
		console.log("youtube frame height : " + mmIframeHeight + ". playpops height : " + mmPlayPopsHeight);
		
		mmFrameSmallTop = mmFrameLargeTop = 40;
		
		mmFrameLargeLeft = (mmPlayPopsWidth /2) - 250;	
		mmFrameSmallLeft = (mmPlayPopsWidth /2) - 100;
		
		console.log("left : " + mmFrameLargeLeft);
		
		
		// Resize Playpops
		document.getElementById("playpops").setAttribute("style", "width: " + mmPlayPopsWidth + "px; height: " + mmPlayPopsHeight + "px; top: " + mmPlayPopsTop + "px;" + mmDisplayStatus + "; box-sizing: unset;");
		
		// Resize fb Box Large
		document.getElementById("fbframelikeLarge").setAttribute("style", "margin-top:-10px; top:" + mmFrameLargeTop + "px; " + mmDisplayFBLarge);
		
		// Resize fb Box Small
		document.getElementById("fbframelikeSmall").setAttribute("style", "margin-top:-10px; top:" + mmFrameSmallTop + "px; " + mmDisplayFBSmall);
		         
			
	}
	
	// ---------------------------------------------------------------
	// Main
	// ---------------------------------------------------------------
	
	// JavaScript Document
	// 2. This code loads the IFrame Player API code asynchronously.]
	// -------------------------------------------------------------
	
	var tag = document.createElement('script');
	
	tag.src = "https://www.youtube.com/iframe_api";
	var firstScriptTag = document.getElementsByTagName('script')[0];
	firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
	
	
	// 3. This function creates an <iframe> (and YouTube player)
	//    after the API code downloads.
	
	// Create Youtube player after api has downloaded
	var mmPlayer;
	var done = false;
	var currentTime = 0;
	var mmPlayerStatus = 0;
	
	var mmTriggerLength = document.getElementById('mmTriggerLength').innerHTML;
	
	// Get the mmPlayVideo click
	document.getElementById('mmClosePop').addEventListener('click', function()
	{
				
		// Check if player status is paused
		if (mmPlayerStatus == YT.PlayerState.PAUSED)
		{
			// resume video
			mmPlayVideo();
			
			// hide popup
			mmPlayPopUp("none"); 
		}
			
	}, false);
		
	document.getElementById('mmSkipVideo').addEventListener('click', function()
	{
		// Check if player status is paused
		if (mmPlayerStatus == YT.PlayerState.PAUSED)
		{
			// resume video
			mmPlayVideo();
			
			// hide popup
			mmPlayPopUp("none"); 
		}
		
	}, false);
	
	
	// Window resize to adjust the width of the popup
	window.onload = function()
	{
		// Resize the popup to match original size
		mmResizeGrid();
	}
	
	// Resive event
	window.onresize = mmResizeGrid;