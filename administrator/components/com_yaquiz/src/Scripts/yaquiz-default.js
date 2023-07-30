//this is script for the quiz default view with all questions and such
//mainly handles drag drop feature for ordering questions

document.addEventListener('DOMContentLoaded', function () {


    //toasts
    const toast = document.getElementById('toasty');
    const toastBody = document.getElementById('toasty-body');
    const toastTitle = document.getElementById('toasty-title');




    // .drag-item can be dragged to change the order
    // .drag-dropzone are the dropzones
    // every .drag-item has a data-order attribute with the current order starting from 1
    // every .drag-dropzone has a data-order attribute with the current order starting from 0


    const dragItems = document.querySelectorAll('.drag-item');
    const dropZones = document.querySelectorAll('.drag-dropzone');

    //when someone starts dragging an item
    dragItems.forEach((item) => {
        item.addEventListener('dragstart', (e) => {
            //add a class to the item being dragged
            item.classList.add('dragging');
            //add .visible to all dropzones
            dropZones.forEach((zone) => {
                zone.classList.add('visible');
            }
            );
        });
        item.addEventListener('dragend', (e) => {
            //remove the class from the item being dragged
            item.classList.remove('dragging');
            //remove .visible from all dropzones
            dropZones.forEach((zone) => {
                zone.classList.remove('visible');
            }
            );
        });

        //when it is actively being dragged, check if we need to scroll
        item.addEventListener('drag', (e) => {

            //get the mouse position
            const mouseY = e.clientY;

            //the top of the window is 0
            //the bottom of the window is window.innerHeight

            //if mouseY is less than 100, scroll up
            if (mouseY < 100) {
                window.scrollBy(0, -15);
            }

            //if mouseY is greater than window.innerHeight - 100, scroll down
            if (mouseY > window.innerHeight - 100) {
                window.scrollBy(0, 15);
            }




        });

    });

    //when someone drags an item over a dropzone
    dropZones.forEach((zone) => {
        zone.addEventListener('dragover', (e) => {
            //prevent default to allow drop
            e.preventDefault();
            //add .over to the dropzone
            zone.classList.add('over');
        });
        zone.addEventListener('dragleave', (e) => {
            //remove .over from the dropzone
            zone.classList.remove('over');
        });
        zone.addEventListener('drop', (e) => {
            //remove .over from the dropzone
            zone.classList.remove('over');
            //get the item being dragged
            const item = document.querySelector('.drag-item.dragging');
            //get the order of the item being dragged
            const itemOrder = item.getAttribute('data-order');
            //get the order of the dropzone
            const zoneOrder = zone.getAttribute('data-order');

            //if the item was dragged up, we are inserting before
            if (itemOrder > zoneOrder) {
                zone.before(item);
            }
            //if the item was dragged down, we are inserting after
            else {
                zone.after(item);
            }

            //reorder items from top to bottom starting at 1 and update data-order attribute
            const items = document.querySelectorAll('.drag-item');
            items.forEach((item, index) => {
                item.setAttribute('data-order', index + 1);
                const orderBadge = item.querySelector('.orderbadge');
                orderBadge.innerText = COM_YAQUIZ_ORDER + ': ' + (index + 1);
            });

            fixDropzones();
            submitNewOrdering();

        });

    });




    //a function to fix dropzones
    function fixDropzones() {
        
        let dropzones = document.querySelectorAll('.drag-dropzone');

        let items = document.querySelectorAll('.drag-item');

        //put dropzones[0] in front of items[0]
        items[0].before(dropzones[0]);

        for(let i = 0; i < items.length; i++){
            items[i].after(dropzones[i+1]);
        }



    }

    function submitNewOrdering() {

        //submit a json string with new order of question ids
        let questionIds = [];
        let items = document.querySelectorAll('.drag-item');
        items.forEach((item) => {
            questionIds.push(item.getAttribute('data-questionid'));
        }
        );

        const urlParams = new URLSearchParams(window.location.search);
        const quizId = urlParams.get('id');

        const data = {
            'quizId': quizId,
            'questionIds': questionIds,
        };

        const url = 'index.php?option=com_yaquiz&task=ScriptAction.saveQuestionOrdering&format=json';

        //send fetch request
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
            
        })
            .then(response => response.json())
            .then(data => {
                toastTitle.innerText = COM_YAQUIZ_SUCCESS;
                toastBody.innerText = COM_YAQUIZ_ORDER_SAVED;
                toast.classList.add('show');
                setTimeout(function () {
                    toast.classList.remove('show');
                }
                    , 3000);

            }
            )
            .catch((error) => {
                toastTitle.innerText = COM_YAQUIZ_ERROR;
                toastBody.innerText = COM_YAQUIZ_ORDER_NOT_SAVED;
                toast.classList.add('show');
                setTimeout(function () {
                    toast.classList.remove('show');
                }

                    , 3000);

            }
            );

       

    }


});
