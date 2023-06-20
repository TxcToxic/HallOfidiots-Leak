var songs = ["song1.mp3", "song2.mp3", "song3.mp3", "song4.mp3", "song5.mp3"];
var currentSongIndex = 0;
var audioPlayer;

function playNextSong() {
    event.preventDefault();
    const button = document.getElementById("music-start")
    button.disabled = true;
    if (currentSongIndex >= songs.length) {
        currentSongIndex = 0;
    }
    var song = songs[currentSongIndex];
    audioPlayer.src = song;
    audioPlayer.play();
    currentSongIndex++;
}

window.onload = function() {
    audioPlayer = new Audio();
    audioPlayer.addEventListener('ended', playNextSong);
    v_slider = document.getElementById("music-volume");
    v_slider.addEventListener('input', function() {
        var volume = v_slider.value / 100;
        audioPlayer.volume = volume;
    });
};