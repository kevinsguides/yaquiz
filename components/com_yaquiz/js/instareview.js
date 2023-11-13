document.addEventListener('DOMContentLoaded', function () {


    let quizData = Joomla.getOptions('quizData');
    let questionData = Joomla.getOptions('questionData');
    //turn questionData into an object
    let currQuestion = 0;

    let feedback_summary = '';

    const quizElem = document.getElementById('reviewquiz');
    const quizHeader = document.querySelector('#reviewquiz .card-header');
    const quizBody = document.querySelector('#reviewquiz .card-body');
    const quizFooter = document.querySelector('#reviewquiz.card-footer');
    const startBtn = document.getElementById('reviewquiz-btn-start');
    const nextBtn = document.getElementById('reviewquiz-btn-next');
    const pageCount = document.getElementById('pageCount');
    const btnFinalResults = document.getElementById('reviewquiz-btn-finish');


    let totalQuestions = Object.keys(questionData).length;

    startBtn.addEventListener('click', startQuiz);

    let total_points = 0;
    let points_acquired = 0;
    let html_section_count = 0;

    let questions_correct = 0;

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

    function fix_images(html){

        //find any img elements in html
        let img_regex = /<img[^>]+src="([^">]+)"/g;
        let img_matches = html.matchAll(img_regex);
        for (const match of img_matches) {
            //check if image resolves
            let img = new Image();
            img.src = match[1];

            //see if image exists
            img.addEventListener('load', function () {

                return(html);
            });

            //add a relative slash to img src if it doesn't have one at the beginning
            if(match[1].charAt(0) != '/'){
                html = html.replace(match[1], '/' + match[1]);
            }
            
            
        }
        return html;
    }

    function loadQuestion(questionId) {

        nextBtn.classList.add('hidden');

        let question = questionData[questionId];
        let questionTitle = question.question;
        let questionAnswers = question.answers;
        let questionCorrect = question.correct;
        let questionType = question.question_type;

        question.details = fix_images(question.details);

        let questionHtml = question.details ? question.details : '';




        if (questionType == 'multiple_choice') {

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

        if (questionType == 'true_false') {
            // same as multiple choice but only two answers
            let answerId = 'answer-' + questionId + '-0';
            let answerHtml = '<input type="radio" class="mchoice-rb" name="answer-' + questionId + '" id="' + answerId + '" value="0">';
            answerHtml += '<label class="form-check-label mchoice text-start mt-1" data-mchoiceAnsIndex="1" for="' + answerId + '">' + quizData.lang_true + '</label>';
            answerHtml += '<input type="radio" class="mchoice-rb" name="answer-' + questionId + '" id="' + answerId + '" value="1">';
            answerHtml += '<label class="form-check-label mchoice text-start mt-1" data-mchoiceAnsIndex="0" for="' + answerId + '">' + quizData.lang_false + '</label>';
            questionHtml += answerHtml;
        }

        if (questionType == 'fill_blank') {
            //create a text box for the answer
            let answerId = 'answer-' + questionId + '-0';
            let answerHtml = '<input type="text" class="form-control yaq_fill_blank_input" name="answer-' + questionId + '" id="' + answerId + '" value="" >';
            //and add submit button
            answerHtml += '<button type="button" class="btn btn-primary mt-3" id="submit-' + questionId + '">' + quizData.lang_submit + '</button>';
            questionHtml += answerHtml;


        }

        quizBody.innerHTML = '<h3>' + questionTitle + '</h3>' + questionHtml;
        if (questionType == 'multiple_choice' || questionType == 'true_false') {
            //listen for click any of the radio labels
            let mchoiceLabels = document.querySelectorAll('.mchoice');
            mchoiceLabels.forEach(function (label) {
                label.addEventListener('click', checkAnswer);
            });
        }

        if (questionType == 'fill_blank') {
            let answerId = 'answer-' + questionId + '-0';
            //listen for click on the submit button
            let submitBtn = document.getElementById('submit-' + questionId);
            submitBtn.addEventListener('click', checkAnswer);

            let answerBox = document.getElementById(answerId);
            //if user hits enter in the text box, submit the answer
            answerBox.addEventListener('keyup', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    checkAnswer(e);
                }
            });

        }

        //if question type html_section just add a next button
        if (questionType == 'html_section') {
            quizBody.innerHTML = questionHtml;
            nextBtn.classList.remove('hidden');
            html_section_count++;
        }

    }


    function checkAnswer(e) {

        let question = questionData[currQuestion];
        let gotItRight = false;
        let users_answer = '';

        if (question.question_type == 'multiple_choice') {
            users_answer = e.target.innerHTML;
            if (e.target.dataset.mchoiceansindex == question.correct) {
                gotItRight = true;
            }
        }

        if (question.question_type == 'true_false') {
            users_answer = e.target.innerHTML;
            if (e.target.dataset.mchoiceansindex == question.correct) {
                gotItRight = true;
            }
        }

        if (question.question_type == 'fill_blank') {
            //answer in text box can match any of the answers
            users_answer = document.getElementsByClassName('yaq_fill_blank_input')[0].value;
            let answers = question.answers;
            for (let i = 0; i < answers.length; i++) {
                if (users_answer.toLowerCase() == answers[i].toLowerCase()) {
                    gotItRight = true;
                    break;
                }
            }
        }


        clearInner();

        let feedback = '';



        if (gotItRight) {
            feedback = question.feedback_right;
            if (feedback == '' || !feedback) {
                feedback = quizData.default_correct_text;
                feedback = fix_images(feedback);
            }
        }
        else {
            feedback = question.feedback_wrong;
            if (feedback == '' || !feedback) {
                feedback = quizData.default_incorrect_text
                feedback = fix_images(feedback);
            }
        }

        if (quizData.display_correct == 1) {
            if (question.question_type == 'fill_blank') {
                //turn question.answers into a comma separated list
                let answers = question.answers;
                let answers_list = '';
                for (let i = 0; i < answers.length; i++) {
                    answers_list += answers[i] + ', ';
                }
                feedback += '<br/><strong>' + quizData.lang_was_correct_if_contained + '</strong> ' + answers_list;
            }
            if (question.question_type == 'multiple_choice') {

                //replace %s with in lang_s_was_correct with correct answer
                correct_ans_text = quizData.lang_s_was_correct.replace('%s', question.answers[question.correct]);
                feedback += '<br/><strong>' + correct_ans_text + '</strong>';
            }
            if (question.question_type == 'true_false') {
                if (question.correct == 0) {
                    feedback += '<br/><strong>' + quizData.lang_false_was_correct + '</strong>';
                }
                else {
                    feedback += '<br/><strong>' + quizData.lang_true_was_correct + '</strong>';
                }
            }
        }


        quizBody.innerHTML = '<h3>' + question.question + '</h3>' + question.details + '<br/> <strong>' + quizData.lang_youranswer + '</strong>' + users_answer + '<br>' + feedback;

        //update points
        total_points += parseInt(question.points);
        if (gotItRight) {
            points_acquired += parseInt(question.points);
            questions_correct++;
        }


        if (currQuestion < totalQuestions) {
            nextBtn.classList.remove('hidden');
        }

        if (currQuestion == totalQuestions) {
            //display view final results button
            btnFinalResults.classList.remove('hidden');
        }

        //feedback summary
        if (quizData.display_feedback == 1) {
            feedback_summary += currQuestion + ') ' + question.question + '<br/>' + question.details + '<br/>' + quizData.lang_youranswer + users_answer + '<br/>' + feedback + '<hr/>';

        }

    }

    nextBtn.addEventListener('click', function () {
        loadNextPage();
    });

    function loadNextPage(){
        currQuestion++;
        if (currQuestion > totalQuestions) {
            currQuestion = 1;
            console.log('resetting');
        }
        loadQuestion(currQuestion);
        pageCount.innerHTML = currQuestion + ' / ' + totalQuestions;
    }

    btnFinalResults.addEventListener('click', function () {

        totalQuestions -= html_section_count;

        let score = points_acquired + ' / ' + total_points;
        let percent = (points_acquired / total_points) * 100;
        let percentStr = percent.toFixed(2) + '%';

        clearInner();
        let scoreHtml = '';
        scoreHtml += '<p>' + quizData.lang_num_correct_of_total.replace('%s', questions_correct).replace('%s', totalQuestions) + '</p><br/>';

        if (quizData.use_point_system) {
            scoreHtml += '<p>' + quizData.lang_your_score + ': ' + score + ' ' + quizData.lang_points + '</p><br/>';
        }
        else {

            scoreHtml += '<p>' + quizData.lang_score_as_percent.replace('%s%%', percentStr) + '</p><br/>';
        }


        quizBody.innerHTML = scoreHtml;

        if (quizData.display_feedback == 1) {
            quizBody.innerHTML += '<hr/>' + feedback_summary;
        }

        btnFinalResults.classList.add('hidden');



    });


});