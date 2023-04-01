console.log('jsquiz.js loaded');


//get every .jsquiz-questioncard
var questionCards = document.getElementsByClassName('jsquiz-questioncard');

let quiz_showfeedback = document.getElementById('quiz_showfeedback').value;
let quiz_feedback_showcorrect = document.getElementById('quiz_feedback_showcorrect').value;


//listen for click on jsquiz-btn-finish
var btnFinish = document.getElementById('jsquiz-btn-finish');

//submit quiz and grade it
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
                questionCards[i].setAttribute('data-iscorrect', '1');
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
                    //set data-iscorrect to 1 for the questionCard
                    questionCards[i].setAttribute('data-iscorrect', '1');
                }
            }
        }

        if(questionType == 'true_false'){
            let form = questionCards[i].querySelector('form');
            let correctAns = form.getAttribute('data-correctans');
            let selectedAns = form.querySelector('input[name="useranswer"]:checked').value;

            if(correctAns == selectedAns){
                score+= points;
                questionCards[i].setAttribute('data-iscorrect', '1');
            }
        }

    }

    scoreAsPercent = (score / totalPossibleScore) * 100;
    
    let passingScore = document.getElementById('passing_score').value;
    if (scoreAsPercent >= passingScore) {
        document.getElementById('jsquiz-feedback-passed').classList.remove('d-none');
    } else {
        document.getElementById('jsquiz-feedback-failed').classList.remove('d-none');
    }

    //scoreAsPercent only to 1 decimal
    scoreAsPercent = scoreAsPercent.toFixed(1);

    document.getElementById('jsquiz-score').innerHTML = score + ' /' + totalPossibleScore + ' (' + scoreAsPercent + '%)';

    //add d-none to currpage
    document.querySelector('[data-jsquiz-page="' + $currpage + '"]').classList.add('d-none');

    //show results page
    document.getElementById('jsquiz-results').classList.remove('d-none');

    if(quiz_showfeedback == 'true'){
        showAllFeedback();
    }



    console.log('score is: ' + score + ' out of ' + totalPossibleScore);


});


//takes all the question cards, makes the forms readonly, and shows if each answer was correct or not
function showAllFeedback(){

    let questionCards = document.querySelectorAll('.jsquiz-questioncard');

    //loop through all questioncards
    for (var i = 0; i < questionCards.length; i++) {
        //make every input readonly
        let inputs = questionCards[i].querySelectorAll('input');
        for (var j = 0; j < inputs.length; j++) {
            //if this is a textbox
            if(inputs[j].getAttribute('type') == 'text'){
                inputs[j].setAttribute('readonly', 'readonly');
            }
            //if it's a radio button
            else if(inputs[j].getAttribute('type') == 'radio'){
                inputs[j].setAttribute('disabled', 'disabled');
            }
        }

        //change the classlist of the i element in each card h3 to show if the answer was correct or not
        let isCorrect = questionCards[i].getAttribute('data-iscorrect');
        let h3 = questionCards[i].querySelector('h3');
        let icon = h3.querySelector('i');
        if(isCorrect == '1'){
            icon.classList.remove('fa-question-circle');
            icon.classList.add('fa-check-circle');
            icon.classList.add('text-success');
            questionCards[i].querySelector('.jsquiz-question-feedback-correct').classList.remove('d-none');
        }
        else{
            icon.classList.remove('fa-question-circle');
            icon.classList.add('fa-times-circle');
            icon.classList.add('text-danger');
            questionCards[i].querySelector('.jsquiz-question-feedback-incorrect').classList.remove('d-none');
        }

        //remove the card-footer from all cards
        let cardFooter = questionCards[i].querySelector('.card-footer');
        cardFooter.classList.add('d-none');

        //make the card visible again
        questionCards[i].classList.remove('d-none');


    }

}

$currpage = 0;

//nav
//someone clicks jsquiz-btn-start
var btnStart = document.getElementById('jsquiz-btn-start');
btnStart.addEventListener('click', function() {
    document.getElementById('jsquiz-intro').classList.add('d-none');
    $currpage = 1;
    let nextPage = document.querySelector('[data-jsquiz-page="' + $currpage + '"]');
    nextPage.classList.remove('d-none');

});

//someone clicks anything with .jsquiz-btn-next
var btnNext = document.getElementsByClassName('jsquiz-btn-next');
for (var i = 0; i < btnNext.length; i++) {
    btnNext[i].addEventListener('click', function() {
        let currPage = document.querySelector('[data-jsquiz-page="' + $currpage + '"]');
        currPage.classList.add('d-none');
        $currpage++;
        let nextPage = document.querySelector('[data-jsquiz-page="' + $currpage + '"]');
        nextPage.classList.remove('d-none');
    });
}

//do opposite for .jsquiz-btn-prev
var btnPrev = document.getElementsByClassName('jsquiz-btn-prev');
for (var i = 0; i < btnPrev.length; i++) {
    btnPrev[i].addEventListener('click', function() {
        let currPage = document.querySelector('[data-jsquiz-page="' + $currpage + '"]');
        currPage.classList.add('d-none');
        $currpage--;
        let nextPage = document.querySelector('[data-jsquiz-page="' + $currpage + '"]');
        nextPage.classList.remove('d-none');
    });
}
