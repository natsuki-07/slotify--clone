<?php
$songQuery = mysqli_query($con, "SELECT id FROM songs ORDER BY RAND() LIMIT 10");

$resultArray = array();

while($row = mysqli_fetch_array($songQuery)) {
	array_push($resultArray, $row['id']);
}

$jsonArray = json_encode($resultArray);
?>
<script>

$(document).ready(function() {
  
	let newPlaylist = <?php echo $jsonArray; ?>;
	audioElement = new Audio();
  setTrack(newPlaylist[0], newPlaylist, false);
  updateVolumeProgressBar(audioElement.audio);


  $("#nowPlayingBarContainer").on("mousedown touchstart mousemove touchmove",function(){
      e.preventDefaults();
  });

  $('.playbackBar .progressBar').mousedown(function() {
    mouseDown = true;
  });
  $('.playbackBar .progressBar').mousemove(function(e) {
    if(mouseDown) {
      timeFromOffset(e,this);
    }
  });
  $('.playbackBar .progressBar').mouseup(function(e) {
    timeFromOffset(e,this);
  });
   $(document).mouseup(function() {
     mouseDown = false;
   });

   $('.volumeBar .progressBar').mousedown(function() {
    mouseDown = true;
  });
  $('.volumeBar .progressBar').mousemove(function(e) {
    if(mouseDown) {
      let percentage = e.offsetX/ $(this).width();

      if(percentage>=0 && percentage<=1){
        audioElement.audio.volume = parcentage;
      }

    }
  });
  $('.volumeBar .progressBar').mouseup(function(e) {
      let percentage = e.offsetX/ $(this).width();

      if(percentage>=0 && percentage<=1){
        audioElement.audio.volume = parcentage;
      }
  });
   $(document).mouseup(function() {
     mouseDown = false;
   });
});
let repeat = false;
function timeFromOffset(mouse,progressBar) {
  let pacentage = mouse.offsetX/$(progressBar).width()*100;
  let seconds = audioElement.audio.duration * (percentage/100);
  audioElement.setTIme(seconds);
}
function prevSong() {
  if(audioElemenet.audio.currentTime >=3 || currentIndex ==0) {
    audioElemenet.setTIme(0);
  } else {
    currentIndex--;
    setTrack(currentPlaylist[currentIndex],currentPlaylist,true);
  }
}
function nextSong() {
  if(repeat){
    audioElement.setTime= setTime(0);
    playSong();
    return;
  }
  if(currentIndex == currentPlaylist.length -1) {
    currentIndex = 0;
  } else{
    currentIndex++;
  }
  let trackToPlay = shuffle ? shufflePlaylist[currentIndex]:currentPlaylist[currentIndex];
  setTrack(trackToPlay,currentPlaylist,true);
}

function setRepeat(){
  repeat = !repeat;
  let imageName = repeat ? "repeat-active.png" : "repeat.png";
  $('.controlButton.repeat img').attr("src","assets/images/icons/" + imageName);
}

function setMute(){
  audioElement.audio.muted = !audioElement.audio.muted;
  let imageName = audioElement.audio.muted ? "volume-mute.png" : "volume.png";
  $('.controlButton.volume img').attr("src","assets/images/icons/" + imageName);
}
function setShuffle(){
 shuffle=!shuffle;
  let imageName = shuffle ? "shuffle-active.png" : "shuffle.png";
  $('.controlButton.shuffle img').attr("src","assets/images/icons/" + imageName);

  if(shuffle) {
    shuffleArray(shufflePlaylist);
    currentIndex = shufflePlaylist.indexOf(audioElement.currentPlaying.id);
  } else {
    currentIndex = currentPlaylist.indexOf(audioElement.currentPlaying.id);
  }
}

function shuffleArray(a) {
  let j,x,i;
  for(i=a.length;i;i--) {
    j = Math.floor(Math.random() *i);
    x =a[i-1];
    a[i-1] = a[j];
    a[j] =x;
  }
}

function setTrack(trackId, newPlaylist, play) {
  if(newPlaylist !=currentPlaylist) {
    currentPlaylist = newPlaylist;
    shufflePlaylist = currentPlaylist.slice();
    shuffleArray(shufflePlaylist);
  }
  if(shuffle) {
    currentIndex = shufflePlaylist.indexOf(trackId);
  } else {
    currentIndex = currentPlaylist.indexOf(trackId);
  }

  currentIndex = currentPlaylist.indexOf(trackId);
  pauseSong();

  $.post("includes/handlers/ajax/getSongJson.php",{songId: trackId},function(data) {

   let track = JSON.parse(data); 

   $(".trackName span").text(track.title);

   $.post("includes/handlers/ajax/getArtistJson.php",{artistId: track.artist},function(data) {
    let artist = JSON.parse(data);
    $(".trackInfo .artistName span").text(artist.name);
    $(".trackInfo .artistName span").attr("onclick","openPage('artist.php?id="+artist.id +"')");
    });

   $.post("includes/handlers/ajax/getAlbumJson.php",{albumId: track.album},function(data) {
    let album = JSON.parse(data);
    $(".content .albumLink img").attr("src",album.artworkPath);
    $(".content .albumLink img").attr("onclick","openPage('album.php?id="+artist.id +"')");
    $(".trackInfo .trackName span").attr("onclick","openPage('album.php?id="+artist.id +"')");
    });

   audioElement.setTrack(track);
  
	  if(play) {
	 	   playSong();
  	}
  });
}

function playSong() {

  if(audioElement.audio.currentTime == 0){
    $.post("includes/handlers/ajax/updatePlays.php",{ songId:audioElement.currentlyPlaying.id});
  } 
	$(".controlButton.play").hide();
	$(".controlButton.pause").show();
	audioElement.play();
}

function pauseSong() {
	$(".controlButton.play").show();
	$(".controlButton.pause").hide();
	audioElement.pause();
}

</script>

<div id="nowPlayingBarContainer">
   
   <div id="nowPlayingBar">

     <div id="nowPlayingLeft">
       <div class="content">
          <span class="albumLink">
            <img class="albumArtwork" role="link" tableindex="0">
          </span>

          <div class="trackInfo">
            <span class="trackName">
              <span role="link" tableindex="0"></span>
            </span>

            <span class="artistName">
              <span role="link" tableindex="0"></span>
            </span>

          </div>

       </div>
     </div>

     <div id="nowPlayingCenter">
        <div class="content playerControls">

           <div class="buttons">
               <button class="controlButton shuffle" title="shuffle button">
                 <img src="assets/images/icons/shuffle.png" alt="Shuffle" onclick="setShuffle()">
               </button>

               <button class="controlButton previous" title="previous button">
                 <img src="assets/images/icons/previous.png" alt="previous" onclick="prevSong()">
               </button>

               <button class="controlButton play" title="play button" onclick="playSong()">
                 <img src="assets/images/icons/play.png" alt="play">
               </button>

               <button class="controlButton pause" title="pause button" style="display:none;" onclick="pauseSong()">
                 <img src="assets/images/icons/pause.png" alt="pause">
               </button>

               <button class="controlButton next" title="shuffle button">
                 <img src="assets/images/icons/next.png" alt="next" onclick="nextSong()">
               </button>

               <button class="controlButton repeat" title="shuffle button">
                 <img src="assets/images/icons/repeat.png" alt="repeat" onclick="setRepeat()">
               </button>
           </div>

           <div class="playbackBar">
               <span class="progressTime current">0.00</span>
               
               <div class="progressBar">
                 <div class="progressBarBg">
                   <div class="progress"></div>
                 </div>
               </div>

               <span class="progressTime remaining">0.00</span>
           </div>

        </div>
     </div>

       <div id="nowPlayingRight">
         <div class="volumeBar">

           <button class="controlButton volume" title="Volume button" onclick="setMute()">
             <i class="fas fa-volume-up"></i>
           </button>

           <div class="progressBar">
                 <div class="progressBarBg">
                   <div class="progress"></div>
                 </div>
               </div>
           
         </div>
       </div>

   </div>
 </div>