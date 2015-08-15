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
			
			console.log("currentTime : " + currentTime); 
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
		
		console.log("here" + mmIframeHeight);
		
		// Get playpops dimensions
		mmPlayPopsDisplay = document.getElementById("playpops").style.display
		mmPlayPopsTop = document.getElementById("playpops").style.top
		
		// Calcs			
		mmIframeAspect = mmIframeHeight / mmIframeWidth;
		
		mmPlayPopsWidth = mmIframeWidth - 20;
		mmPlayPopsHeight = mmIframeHeight * 0.6;
		
		// Correct Sizing for mobile devices
		/*
		if(mmPlayPopsWidth > 440)
		{
			if(mmPlayPopsHeight < 310)
			mmPlayPopsHeight = 310;
		}
		*/
		
		mmPlayPopsTop = (mmIframeHeight - mmPlayPopsHeight)/2;
		
		console.log("height : " + mmPlayPopsHeight);
		
		// Resize Popup : Get width
		//document.getElementById("playpops").style.width = mmPlayPopsWidth;
		//document.getElementById("playpops").style.height = mmPlayPopsHeight;
		
		// set Display
		if (mmPlayerStatus == YT.PlayerState.PAUSED)
			mmDisplayStatus = "display: block";
		else
			mmDisplayStatus = "display: none";
			
		// Resize popup : Define Width
		document.getElementById("playpops").setAttribute("style", "width: " + mmPlayPopsWidth + "px; height: " + mmPlayPopsHeight + "px; top: " + mmPlayPopsTop + "px;" + mmDisplayStatus + "; box-sizing: unset;");
		
		// Resize the facebook iframe if it is active
		//document.getElementById("playpops").setAttribute("display", mmPlayPopsDisplay);
		
		console.log(mmPlayPopsTop);	
			
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