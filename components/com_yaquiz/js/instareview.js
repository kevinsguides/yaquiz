document.addEventListener('DOMContentLoaded', function() {

    let currQuestion = 0;
    let questionData = question_array; //really a json object

    const quizElem = document.getElementById('reviewquiz');
    const quizHeader = document.querySelector('#reviewquiz .card-header');
    const quizBody = document.querySelector('#reviewquiz .card-body');
    const quizFooter = document.querySelector('#reviewquiz.card-footer');
    const startBtn = document.getElementById('reviewquiz-btn-start');
    const nextBtn = document.getElementById('reviewquiz-btn-next');
    const pageCount = document.getElementById('pageCount');

    const lang_correct = default_correct_text;
    const lang_incorrect = default_incorrect_text;

    const totalQuestions = Object.keys(questionData).length;

    startBtn.addEventListener('click', startQuiz);

    function startQuiz() {

        startBtn.classList.add('hidden');
        clearInner();
        currQuestion = 1;
        loadQuestion(currQuestion);

        pageCount.innerHTML = currQuestion + ' / ' + totalQuestions;

    }


    function clearInner() {
            quizBody.innerHTML = '';
    }

    console.log(questionData);



    function loadQuestion(questionId) {

        nextBtn.classList.add('hidden');

        let question = questionData[questionId];
        let questionTitle = question.question;
        let questionAnswers = question.answers;
        let questionCorrect = question.correct;
        let questionType = question.question_type;
        let questionHtml = question.details ? question.details : '';



        if (questionType == 'multiple_choice'){

            //loop through possible answers and print them
            for (let i = 0; i < questionAnswers.length; i++) {
                let answer = questionAnswers[i];
                let answerId = 'answer-' + questionId + '-' + i;
                let answerHtml = '<input type="radio" class="mchoice-rb" name="answer-' + questionId + '" id="' + answerId + '" value="' + i + '">';
                answerHtml += '<label class="form-check-label mchoice text-start mt-1" data-mchoiceAnsIndex="'
                    + i + 
                '" for="' + answerId + '">' + answer + '</label>';
                questionHtml += answerHtml;
            }

        }

        if (questionType == 'true_false'){
            // same as multiple choice but only two answers
            let answerId = 'answer-' + questionId + '-0';
            let answerHtml = '<input type="radio" class="mchoice-rb" name="answer-' + questionId + '" id="' + answerId + '" value="0">';
            answerHtml += '<label class="form-check-label mchoice text-start mt-1" data-mchoiceAnsIndex="0" for="' + answerId + '">' + lang_true + '</label>';
            answerHtml += '<input type="radio" class="mchoice-rb" name="answer-' + questionId + '" id="' + answerId + '" value="1">';
            answerHtml += '<label class="form-check-label mchoice text-start mt-1" data-mchoiceAnsIndex="1" for="' + answerId + '">' + lang_false + '</label>';
            questionHtml += answerHtml;
        }

        if (questionType == 'fill_blank'){
            //create a text box for the answer
            let answerId = 'answer-' + questionId + '-0';
            let answerHtml = '<input type="text" class="form-control" name="answer-' + questionId + '" id="' + answerId + '" value="">';
            //and add submit button
            answerHtml += '<button type="button" class="btn btn-primary mt-3" id="submit-' + questionId + '">' + lang_submit + '</button>';
            questionHtml += answerHtml;


        }

        quizBody.innerHTML = '<h3>' + questionTitle + '</h3>' + questionHtml;
        if (questionType == 'multiple_choice' || questionType == 'true_false'){
                //listen for click any of the radio labels
                let mchoiceLabels = document.querySelectorAll('.mchoice');
                mchoiceLabels.forEach(function(label) {
                    label.addEventListener('click', checkAnswer);
                });
        }

        if (questionType == 'fill_blank'){
            let answerId = 'answer-' + questionId + '-0';
            //listen for click on the submit button
            let submitBtn = document.getElementById('submit-' + questionId);
            submitBtn.addEventListener('click', checkAnswer);

            //listen for enter key
            let answerBox = document.getElementById(answerId);
            answerBox.addEventListener('keyup', function(e){
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitBtn.click();
                }
            });
        }

        //if question type html_section just add a next button
        if (questionType == 'html_section'){
            console.log('html section');
            quizBody.innerHTML = questionHtml;
            nextBtn.classList.remove('hidden');
        }

    }


    function checkAnswer(e){
        console.log('checking answer');

        let question = questionData[currQuestion];
        let gotItRight = false;
        let users_answer = '';

        if (question.question_type == 'multiple_choice'){
            users_answer = e.target.innerHTML;
            if (e.target.dataset.mchoiceansindex == question.correct){
                gotItRight = true;
            }
        }

        if (question.question_type == 'true_false'){
            users_answer = e.target.innerHTML;
            if (e.target.dataset.mchoiceansindex == question.correct){
                gotItRight = true;
            }
        }

        if (question.question_type == 'fill_blank'){
            //answer in text box can match any of the answers
            users_answer = e.target.previousElementSibling.value;
            let answers = question.answers;
            for (let i = 0; i < answers.length; i++) {
                if (users_answer.toLowerCase() == answers[i].toLowerCase()){
                    gotItRight = true;
                    break;
                }
            }
        }


        clearInner();

        let feedback = '';

        

        if (gotItRight){
            feedback = question.feedback_right;
            if (feedback == '' || !feedback){ 
                feedback = lang_correct;
            }
        }
        else{
            feedback = question.feedback_wrong;
            if (feedback == '' || !feedback){ 
                feedback = lang_incorrect;
            }
        }

        quizBody.innerHTML = '<h3>' + question.question + '</h3>' + question.details + '<br/> <strong>' + lang_youranswer + '</strong>' + users_answer + '<p>' + feedback + '</p>';


        if (currQuestion < totalQuestions){
            nextBtn.classList.remove('hidden');
        }

    }

    nextBtn.addEventListener('click', function(){
        currQuestion++;
        if (currQuestion > totalQuestions){
            currQuestion = 1;
        }
        loadQuestion(currQuestion);
        pageCount.innerHTML = currQuestion + ' / ' + totalQuestions;
        
    });
    

});