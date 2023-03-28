//when content loaded
document.addEventListener("DOMContentLoaded", function(event) {

    let questionType = document.getElementById('jform_question_type').value;
    let correctAns = document.getElementById('jform_correct').value;

    const toast = document.getElementById('simplequiz-toast');
    const toastMessage = document.getElementById('simplequiz-toast-message');

    console.log('i find question type is ' + questionType);

    //if question type is multiple choice
    if(questionType == 'multiple_choice') {
        //show multiple choice options
        const addBtn = document.getElementById('mchoice-add-btn');
        const answerTemplate = document.getElementById('mchoice-answer-template');
        const answerList = document.getElementById('mchoice-answer-list');

        addBtn.addEventListener('click', function() {
            //max 10 answers
            if(countMchoiceAnswers() == 10) {
                toasty('Limit 10 answers', 3000);
                return;
            }


            let answer = answerTemplate.cloneNode(true);
            answer.removeAttribute('id');
            answerList.appendChild(answer);
            reindexMchoiceAnswers();
        });

        document.addEventListener('click', function(e) {
            if(e.target && e.target.classList.contains('mchoice-correct-btn')) {
                e.preventDefault();
                //get the answer id (the nearest data-ansid attribute)

                let ansId = e.target.closest('.mchoice-answer').getAttribute('data-ansid');

                //set the correct answer field to the answer id
                document.getElementById('jform_correct').value = ansId;
                //remove the correct class from all answers
                let answers = document.getElementsByClassName('mchoice-answer');
                for(let i = 0; i < answers.length; i++) {
                    answers[i].classList.remove('correct');
                }
                //add the correct class to the clicked answer
                e.target.closest('.mchoice-answer').classList.add('correct');
            }

            if(e.target && e.target.classList.contains('mchoice-delete-btn')) {
                e.preventDefault();
                //dont let them delete the last answer
                if(countMchoiceAnswers() == 2) {
                    toasty('You must have at least two possible answers', 3000);
                    return;
                }


                //get the answer id (the nearest data-ansid attribute)
                let ansId = e.target.closest('.mchoice-answer').getAttribute('data-ansid');


                //remove the answer
                e.target.closest('.mchoice-answer').remove();

                document.getElementById('jform_correct').value = 0;
                //remove correct class from all answers
                let answers = document.getElementsByClassName('mchoice-answer');
                for(let i = 0; i < answers.length-1; i++) {
                    answers[i].classList.remove('correct');
                }

                reindexMchoiceAnswers();


                //add correct class to the first answer
                answers[0].classList.add('correct');

                toasty('Answer deleted, you may need to reset correct answer', 3000);

            }
        });

        
    }


    function countMchoiceAnswers(){
        let mchoiceAnswers = document.getElementsByClassName('mchoice-answer');
        console.log('i count ' + mchoiceAnswers.length + ' answers');
        return mchoiceAnswers.length-1;
    }

    function toasty(message, delay){
        //show a bootstrap toast message
        //set the toast message
        toastMessage.innerHTML = message;
        toast.classList.add('show');
        setTimeout(function() {
            toast.classList.remove('show');
        }
        , delay);
    }

    document.getElementById('simplequiz-toast-close').addEventListener('click', function() {
        toast.classList.remove('show');
    });

    function reindexMchoiceAnswers(){
        let answers = document.getElementsByClassName('mchoice-answer');
        //re-index all the answers
        for(let i = 0; i < answers.length-1; i++) {
            answers[i].setAttribute('data-ansid', i);
        }
    }


    if(questionType == 'fill_blank'){
        const addBtn = document.getElementById('fill-blank-add-btn');
        const answerTemplate = document.getElementById('fill-blank-answer-template');
        const answerList = document.getElementById('fill-blank-answer-list');

        addBtn.addEventListener('click', function() {
        //max 20 answers
            if(countFillBlankAnswers() == 20) {
                toasty('Limit 20 possible answers', 3000);
                return;
            }

            //add new answer li to the list
            let answer = answerTemplate.cloneNode(true);
            answer.removeAttribute('id');
            answerList.appendChild(answer);
            reindexFillBlankAnswers();

        });

        document.addEventListener('click', function(e) {

            if(e.target && e.target.classList.contains('fill-blank-delete-btn')) {
                e.preventDefault();
                //dont let them delete the last answer
                if(countFillBlankAnswers() == 1) {
                    toasty('You must have at least one answer', 3000);
                    return;
                }

                //get the answer id (the nearest data-ansid attribute)
                let ansId = e.target.closest('.fill-blank-answer').getAttribute('data-ansid');

                //remove the answer
                e.target.closest('.fill-blank-answer').remove();

                reindexFillBlankAnswers();

                toasty('Answer deleted', 3000);

            }
        });
        

    }

    function countFillBlankAnswers(){
        let fillBlankAnswers = document.getElementsByClassName('fill-blank-answer');
        console.log('i count ' + fillBlankAnswers.length + ' answers');
        return fillBlankAnswers.length-1;
    }

    function reindexFillBlankAnswers(){
        let answers = document.getElementsByClassName('fill-blank-answer');
        //re-index all the answers
        for(let i = 0; i < answers.length-1; i++) {
            answers[i].setAttribute('data-ansid', i);
        }
    }




    
});