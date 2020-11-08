"use strict";

document.addEventListener('DOMContentLoaded', function() {

    const deleteForm = document.querySelector('.delete-property');

    document.addEventListener('click', function (event) {
        const element = event.target;

        if (element.nodeName !== 'BUTTON') {
            return false;
        }
        const form = element.parentElement;
        event.preventDefault();

        const modalBox = new ModalBox(
            document.querySelector('main'),
            function () {
                form.submit()
            },
            'Confirmation Box',
            'Are you sure you want to delete '+form.dataset.target+'?',
            'Delete',
            'Cancel'
        );
        
    });

});

function ModalBox(
    domContainer,
    resolve,
    modalBoxTitle,
    boxMessage,
    positiveAnswer,
    negativeAnswer
) {
    const modelBoxElement = renderModalBox(); 

    domContainer.append(modelBoxElement);
    
    function removeModalBox()
    {
        modelBoxElement.remove();
    }

    function renderModalBox()
    {
        const wrapElement            = document.createElement('div');
        const modalBoxElement        = document.createElement('div');
        const modalBoxTitleElement   = document.createElement('span');
        const innerBoxElement        = document.createElement('div');
        const innerBoxMessageElement = document.createElement('span');
        const positiveAnswerElement  = document.createElement('span');
        const negativeAnswerElement  = document.createElement('span');

        negativeAnswerElement.classList = 'answer';
        negativeAnswerElement.innerHTML = negativeAnswer;

        negativeAnswerElement.addEventListener('click', function () {
            removeModalBox();
        });

        positiveAnswerElement.classList = 'answer';
        positiveAnswerElement.innerHTML = positiveAnswer;

        positiveAnswerElement.addEventListener('click', function () {
            resolve();
        });

        innerBoxMessageElement.classList = 'box-title';
        innerBoxMessageElement.innerHTML = boxMessage;

        modalBoxTitleElement.innerHTML   = modalBoxTitle;
        
        innerBoxElement.classList = 'inner-box';
        innerBoxElement.append(innerBoxMessageElement);
        innerBoxElement.append(positiveAnswerElement);
        innerBoxElement.append(negativeAnswerElement);

        modalBoxElement.classList = 'modal-box';
        modalBoxElement.append(modalBoxTitleElement);
        modalBoxElement.append(innerBoxElement);

        wrapElement.classList = 'modal-box-wrap';
        wrapElement.append(modalBoxElement);

        return wrapElement;
    }
}
