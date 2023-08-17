document.addEventListener('DOMContentLoaded', function () {

    const timerDisplay = document.getElementById('yaqTimerTime');
    const timerBox = document.getElementById('yaquizTimer');

    //page should load with time left in seconds given by timerDisplay data-initialtime
    let timeLeft = timerDisplay.dataset.initialtime;

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
    
            //if time runs out, submit form
            if (timeLeft <= 0) {
                clearInterval(timeLeft = 0);
                forceSubmit();
            }

            //when there is less than 60 seconds left, add .warning class to timerBox
            if (timeLeft <= 60) {
                timerBox.classList.add('warning');
            }
    

        }
        , 1000);



    
    function forceSubmit() {
        document.getElementById('yaQuizForm').submit();
    }
    


});