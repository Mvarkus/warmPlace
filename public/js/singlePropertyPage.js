document.addEventListener('DOMContentLoaded', function() {
	Slider.areaHandler(document.querySelector('.house-photos'));
	document.querySelector('.tab-titles').addEventListener('click', function (event) {
		const currentTabTitle = event.target;

		if (currentTabTitle.classList.contains('active-tab')||
			!currentTabTitle.classList.contains('tab-title')) {

			return;
		}
		const previousTabTitle = currentTabTitle.parentElement.querySelector('.active-tab');
		const tabs             = currentTabTitle.parentElement.nextElementSibling;

		const previousTab = tabs.querySelector(
			'div[data-tab-id="'+previousTabTitle.dataset.targetId+'"]'
		);
		const currentTab  = tabs.querySelector(
			'div[data-tab-id="'+currentTabTitle.dataset.targetId+'"]'
		);

		previousTabTitle.classList = 'tab-title';
		currentTabTitle.classList  = 'tab-title active-tab';

		previousTab.style.display = 'none';
		currentTab.style.display  = 'block';
	});
});
