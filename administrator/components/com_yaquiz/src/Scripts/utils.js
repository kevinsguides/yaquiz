document.addEventListener('DOMContentLoaded', function () {
    const doubleCheckBtns = document.querySelectorAll('.doublecheckdialog');
    //add click listeners
    doubleCheckBtns.forEach((btn) => {

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            let confirmMessage = btn.getAttribute('data-confirm');
            //show confirm
            if (!confirmMessage) {
                confirmMessage = 'Are you sure?';
            }
            const confirm = window.confirm(confirmMessage);
            if (confirm) {
                //go to href from btn
                let goto = btn.getAttribute('href');
                window.location.href = goto;
            }
        });
    });


    //end of DOMContentLoaded
});
