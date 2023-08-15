document.addEventListener('DOMContentLoaded', function () {

    const timerDisplay = document.getElementById('yaqTimerTime');

    //page should load with time left in seconds
    let timeLeft = timerDisplay.innerHTML;

    timeLeft = timeLeft - 15;

    //update timer every second with minutes and seconds
    setInterval(function () {
            
            let minutes = Math.floor(timeLeft / 60);
            let seconds = timeLeft % 60;
    
            if (seconds < 10) {
                seconds = "0" + seconds;
            }
    
            if (minutes < 10) {
                minutes = "0" + minutes;
            }
    
            timerDisplay.innerHTML = minutes + ":" + seconds;
    
            timeLeft = timeLeft - 1;
    
            if (timeLeft <= 0) {
                clearInterval(timeLeft = 0);
                forceSubmit();
            }
    
        }
        , 1000);



    
    function forceSubmit() {
        document.getElementById('yaQuizForm').submit();
    }
    


});