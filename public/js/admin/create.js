"use strict";

document.addEventListener('DOMContentLoaded', function() {
	const addFieldButton = document.querySelector('.more-button .add-field');
	const removeFieldButton = document.querySelector('.more-button .remove-field');
	const imageFields = document.querySelector('.image-fields');

	let fieldCounter = 0,
		removingResult = false;

	addFieldButton.addEventListener('click', function (event) {
		addImageAdditionField(imageFields, ++fieldCounter);
	});

	removeFieldButton.addEventListener('click', function (event) {
		console.dir(imageFields.lastChild);
		removingResult = removeImageAdditionField(imageFields);
		removingResult ? fieldCounter-- : fieldCounter;
	});
});

function addImageAdditionField(parentElement, fieldId)
{
	const imageField = document.createElement('div');
	const label      = document.createElement('label');
	const inputTitle = document.createElement('input');
	const inputFile  = document.createElement('input');

	imageField.classList = 'image-field';

	label.htmlFor   = 'image'+fieldId;
	label.innerHTML = '-new- Image nr. '+fieldId+' *';

	inputTitle.id          = 'image'+fieldId;
	inputTitle.placeholder = 'Image title';
	inputTitle.name        = 'images[new_'+fieldId+'][title]';
	inputTitle.required    = true;

	inputFile.type   = 'file';
	inputFile.accept = 'images/*';
	inputFile.name   = 'images[new_'+fieldId+']';
	inputFile.required    = true;

	imageField.append(label);
	imageField.append(inputTitle);
	imageField.append(inputFile);

	parentElement.append(imageField);
}

function removeImageAdditionField(parentElement)
{
	if (parentElement.lastChild.nodeType === 1) {

		parentElement.lastChild.remove();
		return true;
	}

	return false;
}
