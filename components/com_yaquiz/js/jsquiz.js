console.log('jsquiz.js loaded');


//get every .jsquiz-questioncard
var questionCards = document.getElementsByClassName('jsquiz-questioncard');


//listen for click on jsquiz-btn-finish
var btnFinish = document.getElementById('jsquiz-btn-finish');
btnFinish.addEventListener('click', function() {
    console.log('i find...');

    let score = 0;
    let totalPossibleScore = 0;

    //loop through all questioncards
    for (var i = 0; i < questionCards.length; i++) {
        //get the question type
        let questionType = questionCards[i].getAttribute('data-qtype');
        let points = parseInt(questionCards[i].getAttribute('data-pointvalue'));
        totalPossibleScore += points;
        if(questionType == 'multiple_choice'){
            //the correct answer comes from the child form data-correctans value

            //get the first form element that's a child of questioncard
            

            let form = questionCards[i].querySelector('form');
            let correctAns = form.getAttribute('data-correctans');
            let selectedAns = form.querySelector('input[name="useranswer"]:checked').value;

            if(correctAns == selectedAns){
                score+= points;
            }
        
        }

        if(questionType == 'fill_blank'){
        
            
            let form = questionCards[i].querySelector('form');
            let correctAnswers = form.getAttribute('data-correctans');
            //parse json
            let correctAnswersArray = JSON.parse(correctAnswers);
            let caseSense = form.getAttribute('data-casesense');

            let selectedAns = form.querySelector('input[name="useranswer"]').value;

            //for each possible correct answer...
            for (var j = 0; j < correctAnswersArray.length; j++) {
                if(caseSense != '1'){
                    //if case insensitive, make both lowercase
                    correctAnswersArray[j] = correctAnswersArray[j].toLowerCase();
                    selectedAns = selectedAns.toLowerCase();
                    

                }
                if(correctAnswersArray[j] == selectedAns){
                    score+= points;
                }
            }
        }

        if(questionType == 'true_false'){
            let form = questionCards[i].querySelector('form');
            let correctAns = form.getAttribute('data-correctans');
            let selectedAns = form.querySelector('input[name="useranswer"]:checked').value;

            if(correctAns == selectedAns){
                score+= points;
            }
        }

    }

    console.log('score is: ' + score + ' out of ' + totalPossibleScore);


});