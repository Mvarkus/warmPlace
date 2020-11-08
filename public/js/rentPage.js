"use strict";
document.addEventListener("DOMContentLoaded", function (event) {
    let temp;

    const paginator = new Paginator(
        document.querySelector('.pagination ul'),
    );
    const filter = new Filter(
        document.querySelector('.filters')
    );
    filter.setPaginator(paginator);
    paginator.setFilter(filter);

    temp = document.querySelector('.search-result-amount');
    paginator.setPagesAmount(
        +temp.dataset.perPage,
        +temp.innerHTML
    );
    temp = null;

    paginator.setCurrentPage(1);
    paginator.renderPaginator();

    Slider.areaHandler(document.querySelector('.block-container'));
});

/**
 * The class is responsible for element's state changing.
 */
function State(){}
/**
 * Changes state to NOT active
 *
 * @param element {object} DOM element for which you want to change state
 */
State.deactivate = function(element) {
    element.style.color = '#d2d0d0';
};
/**
 * Changes state to active
 *
 * @param element {object} DOM element for which you want to change state
 */
State.activate = function(element) {
    element.style.color = '#444444';
};
/**
 * Adds and removes loader circle
 */
function Loader(){}
/**
 * Add loader circle to given container
 *
 * @param loadContainer {HTML element} element where you want to add loader
 */
Loader.showLoader = function(loaderContainer) {
    loaderContainer.innerHTML = '<div class="loader"></div>';
};
/**
 * Find and remove loader circle
 */
Loader.removeLoader = function() {
    document.querySelector('.loader').remove();
};
/**
 * Responsible for pagination item rendering and its functionality
 *
 * @param {object} DOM element to which functionality should be bound
 */
function Paginator(domElement)
{
    let pagesAmount,
        currentPage,
        margin,
        filter,
        paginatorIsActive = true;

    const defaultMargin  = 4;
    const delimiter      = ' ... ';

    /**
     * Calculate and set total pages amount
     *
     * @param showPerPage {int} How many items should be displayed per each page
     * @param totalItemsAmount {int} How many items exist in total
     */
    this.setPagesAmount = function(showPerPage, totalItemsAmount) {
        if (showPerPage < 1) {
            showPerPage = 1;
        }
        if (showPerPage > totalItemsAmount) {
            showPerPage = totalItemsAmount;
        }

        pagesAmount = Math.ceil(totalItemsAmount / showPerPage)
    }
    this.activate = function() {
        State.activate(domElement);
        paginatorIsActive = true;
    };
    this.deactivate = function() {
        State.deactivate(domElement);
        paginatorIsActive = false;
    };
    /**
     * Set filter object
     *
     * @param filterObj {object}
     */
    this.setFilter = function(filterObj) {
        if (filterObj instanceof Filter) {
            filter = filterObj;
        }
    };
    this.getFilter = function() {
        return filter;
    };
    this.getCurrentPage = function() {
        return currentPage;
    };
    /**
     * Render DOM pagination element
     */
    this.renderPaginator = function () {
        let li = '';
        // Clear pagination
        domElement.innerHTML = '';
        for (let i = 1; i <= pagesAmount; i++) {
            // Decrease margin when current page is far from first and last pages
            if (currentPage - defaultMargin < 0 || currentPage+defaultMargin-1 > pagesAmount) {
                margin = defaultMargin;
            } else {
                margin = defaultMargin-1;
            }
            // Add item if item is first, last and in margin range
            if (i === 1 || i === pagesAmount || i > (currentPage-margin) && i < (currentPage+margin)) {
                if (currentPage === i) {
                    li += ' <li data-page="'+i+'"><span class="active-page">'+i+'</span></li>';
                } else {
                    li += ' <li data-page="'+i+'"><span>'+i+'</span></li>';
                }
            // When page does fit in allowed range, add delimiter, but it should be before last page or after first
            } else if (i == 2 || i == pagesAmount-1) {
                li += delimiter;
            }
        }
        // Add pagination to DOM
        domElement.innerHTML = li;
    };
    /**
     * Set current page. Check if page number does not go out of range.
     *
     * @param pageNumber {int} Page number to set
     * @return currentPage {int}
     */
    this.setCurrentPage = function(pageNumber) {

        if (isNaN(pageNumber)) {
            return currentPage = 1;
        }
        if (pageNumber > pagesAmount) {
            return currentPage = pageNumber;
        } else if (pageNumber < 1) {
            return currentPage = 1
        }
        return currentPage = pageNumber;
    };

    domElement.addEventListener('click', (event) => {
        const li = event.target.parentElement;
        if (li.nodeName !== 'LI' || li.dataset.page == currentPage || !paginatorIsActive) {
            return;
        }
        this.setCurrentPage(+li.dataset.page);

        this.deactivate();
        filter.deactivate();
        Loader.showLoader(document.querySelector('.block-container'));

        TicketLoader.LoadTicketsFromServer(
            filter,
            this,
            false
        );
    });
}

/**
 * Class is responsible for storing tickets data, rendering and parsing tickets.
 *
 * @param ticketData {array}
 */
function Ticket(ticketData)
{
    this.images       = ticketData['images'];
    this.pricePerWeek = ticketData['price_per_week'];
    this.type         = ticketData['type_title'];
    this.bedrooms     = ticketData['available_bedrooms']+'/'+ticketData['total_bedrooms'];
    this.address      = ticketData['address'];
    this.propertyId   = ticketData['property_id'];
    this.condition    = ticketData['condition'];

    // DOM elements
    this.ticketDOM = Ticket.parseRawHtmlToDom(
        Ticket.renderRawHtmlTicket(this)
    );
    this.sliderDOM = this.ticketDOM.querySelector('.slider');
}
/**
 * Convert html string to DOM
 *
 * @param htmlString {string}
 * @return {object} DOM
 */
Ticket.parseRawHtmlToDom = function(htmlString) {
    const domParser = new DOMParser();
    const doc = domParser.parseFromString(htmlString, 'text/html');

    return doc.body.firstChild;
}
/**
 * Builds html string ticket using ticket object
 *
 * @param ticket {object} object created from Ticket class
 * @return {string} html string
 */
Ticket.renderRawHtmlTicket = function(ticket) {
    let ticketHtml;

    function renderSlider(ticket) {
        let slider, i;
        slider = '<div class="slider">'+
                 makeSlide(
                     ticket.images[0],
                     'current-slide',
                     1,
                     ticket.images.length,
                     ticket.propertyId
                 );

        for (i = 1; i < ticket.images.length; i++) {
            slider += makeSlide(
                          ticket.images[i],
                          'slide',
                          i+1,
                          ticket.images.length,
                          ticket.propertyId
                      );
        }

        slider += '<div class="left-slider-button slider-button">'+
                    '<span>&#10094;</span>'+
                  '</div>'+
                  '<div class="right-slider-button slider-button">'+
                    '<span>&#10095;</span>'+
                  '</div>'+
                  '</div>'

        function makeSlide(image, slideType, slideNumber, totalSlides, propertyId) {
            return '<div class="'+slideType+'">'+
                   '<a href="/property/'+propertyId+'"><img src="'+image['image_path']+'" alt="'+image['image_title']+'"></a>'+
                   '<div class="slide-number">'+
                   '<span class="current">'+slideNumber+'</span>/<span class="total">'+totalSlides+'</span>'+
                   '</div>'+
                   '<div class="slide-caption">'+image['image_title']+'</div>'+
                   '</div>';
        }

        return slider;
    }

    // Check if user is in admin panel and add proper button to slides
    let button = '';
    let uri = window.location.pathname;

    const urlParts = uri.split('/');
    const type = urlParts[urlParts.length-1];
    
    if (type === 'delete') {
        button = '<a class="action-button" href="http://nhl.v/a05022019pdmi/delete/property/'+ticket.propertyId+'">Delete</a>';
    } else if (type === 'update') {
        button = '<a class="action-button" href="http://nhl.v/a05022019pdmi/update/property/'+ticket.propertyId+'">Update</a>';
    }

    ticketHtml = '<div class="ticket content-block">'+
                 button+
                 '<div class="flow">'+
                 renderSlider(ticket)+
                 '<div class="ticket-info">'+
                 '<div>'+
                   '<span class="ticket-info-title"><img title="Price" src="/images/icons/wallet.png" alt="Wallet"></span>'+
                   '<span>'+ticket.pricePerWeek+'£ p/w</span>'+
                 '</div>'+
                 '<div>'+
                   '<span class="ticket-info-title"><img title="Type" src="/images/icons/home.png" alt="House"></span>'+
                   '<h2>'+ticket.type+'</h2>'+
                 '</div>'+
                 '<div>'+
                   '<span class="ticket-info-title"><img title="Beds" src="/images/icons/bed.png" alt="bed"></span>'+
                   '<span>'+ticket.bedrooms+'</span>'+
                 '</div>'+
                 '<div>'+
                   '<span class="ticket-info-title"><img title="Condition" src="/images/icons/check.png" alt="Checklist sheet"></span>'+
                   '<span>'+ticket.condition+'</span>'+
                 '</div>'+
                 '</div>'+
                 '</div>'+
                 '<a target="_blank"'+
                   'rel="noopener"'+
                   'title="google"'+
                   'draggable="false"'+
                   'href="https://www.google.com/maps/search/?api=1&query='+encodeURIComponent(ticket.address)+'">'+
                 '<div class="adress">'+
                   '<span>'+ticket.address+'</span>'+
                 '</div>'+
                 '</a>'+
                 '</div>';

    return ticketHtml;
};

function TicketLoader(){}
/**
 * Loads tickets data from server
 *
 * @param filter {object}
 * @param paginator {object}
 * @param resetPaginator {boolean}
 */
TicketLoader.LoadTicketsFromServer = function (filter, paginator, resetPaginator) {
    let page;

    if (resetPaginator) {
        page = 1;
    } else {
        page = paginator.getCurrentPage();
    }

    let requestString = '/properties'+filter.getStringify()+'&page='+page;

    makeAjaxRequest('GET', requestString).then(
        (response) => {
            let tickets,
                i,
                renderedTickets = [];

            const ticketsData = JSON.parse(response);
            const ticketsAmount = ticketsData[1];
            const showPerPage = ticketsData[2];

            tickets = ticketsData[0];

            for (i = 0; i < tickets.length; i++) {
                renderedTickets.push(new Ticket(tickets[i]));
            }
            Loader.removeLoader();

            const amountDomContainer  = document.querySelector('.search-result-amount');
            const ticketsDomContainer = document.querySelector('.block-container');

            if (ticketsAmount > 0) {
                TicketLoader.loadTicketsIntoDOM(
                    renderedTickets,
                    ticketsDomContainer,
                    ticketsAmount,
                    amountDomContainer
                );
                scrollTo(filter.DOM);
            } else {
                TicketLoader.setMessage(
                    'Nothing was found',
                    document.querySelector('.block-container'),
                    document.querySelector('.search-result-amount')
                );
            }

            filter.activate();
            paginator.activate();

            if (resetPaginator) {
                paginator.setPagesAmount(showPerPage, ticketsAmount);
                paginator.setCurrentPage(1);
                paginator.renderPaginator();
            } else {
                paginator.renderPaginator();
            }
        },
        () => {
            TicketLoader.setMessage(
              'Something went wrong, please refresh the page',
              document.querySelector('.block-container'),
              document.querySelector('.search-result-amount')
            );
        }
    );
};

/**
 * Put tickets into DOM
 *
 * @param tickets {array} array of Ticket objects
 * @param ticketsDomContainer {object} DOM element where tickets should be added
 * @param ticketsAmount {int} general amount of tickets found
 */
TicketLoader.loadTicketsIntoDOM = function(
      tickets,
      ticketsDomContainer,
      ticketsAmount,
      amountDomContainer
    ) {

    let i, amount, offset;
    // Clear container from old tickets
    ticketsDomContainer.innerHTML = '';
    // Load tickets
    for (i = 0; i < tickets.length; i++) {
        ticketsDomContainer.append(tickets[i].ticketDOM);
    }
    // update Tickets amount
    amountDomContainer.innerHTML = ticketsAmount;
};
/**
 * Shows message instead of tickets
 *
 * @param message {string} message which should be displayed
 * @param ticketsDomContainer {object} DOM element which holds tickets
 * @param amountDomContainer {object} DOM element which holds total tickets amount
 */
TicketLoader.setMessage = function(message, ticketsDomContainer, amountDomContainer) {
    ticketsDomContainer.innerHTML = '<div class="empty-result">'+message+'</div>';
    amountDomContainer.innerHTML = 0;
};

/**
 * Manipulates filter data, adds higlighting, button functionality.
 *
 * @param {HTML Element} filter which is displayed in DOM tree.
 */
function Filter(filterElement)
{
    let filterExtended = false,
        filterActive = true,
        filterValues = [],
        paginator;

    // Used for scroll to
    this.DOM = filterElement;

    // Extend/hide button
    const extendButton = filterElement.querySelector('.extend-button img');
    // Reset button
    const resetButton = filterElement.querySelector('.reset-button');
    // Search button
    const searchButton = filterElement.querySelector('.search-button');
    // Filter extension part
    const filterExtension = filterElement.querySelector('.filter-extension');

    const defaultFilterValues = [0, 'newest', 0, 0];
    const defaultActiveLiElements = filterElement.querySelectorAll('.active-filter');

    setFilterValues(defaultActiveLiElements, defaultFilterValues);
    addResetButtonFunctionality.call(this);
    addSearchButtonFunctionality.call(this);
    addExtendButtonFunctionality();
    addHighlightFunctionality.call(this);;

    /**
     * Set default filter values
     */
    function setFilterValues(activeLiElements, filterValuesData)
    {
        filterValues = {
            'type':      {'filterElement': activeLiElements[0], 'value': filterValuesData[0]},
            'order-by':  {'filterElement': activeLiElements[1], 'value': filterValuesData[1]},
            'location':  {'filterElement': activeLiElements[2], 'value': filterValuesData[2]},
            'bedrooms':  {'filterElement': activeLiElements[3], 'value': filterValuesData[3]}
        };
    }

    /**
     * Reset filter to default state
     */
    function resetFilter()
    {
        // Remove active class from current li elements
        for (let key of Object.keys(filterValues)) {
            filterValues[key].filterElement.classList.remove('active-filter');
        }
        setFilterValues(defaultActiveLiElements, defaultFilterValues);
        // Set highlighting to active li elements
        for (let key of Object.keys(filterValues)) {
            filterValues[key].filterElement.classList.add('active-filter');
        }
    }
    /**
     * Add event listener to extend button. Toggles filter extension
     */
    function addExtendButtonFunctionality()
    {
        extendButton.onclick = function () {
            if (filterExtended) {
                hideExtension();
            } else {
                extend();
            }
        };
    }
    /**
     * Add event listener to search button.
     * Makes ajax request if filters DOM was not changed, if it was, reset it to default state.
     */
    function addSearchButtonFunctionality()
    {
        searchButton.onclick = () => {
            let elementsData = [],
                elements = filterElement.querySelectorAll('.active-filter');

            for (let i = 0; i < elements.length; i++) {
                elementsData.push(elements[i].dataset['filterValue']);
            }

            setFilterValues(elements, elementsData);

            if (validate()) {
                if (filterActive) {

                    Loader.showLoader(document.querySelector('.block-container'));
                    this.deactivate();
                    paginator.deactivate();

                    TicketLoader.LoadTicketsFromServer(
                        this,
                        paginator,
                        true
                    );
                }
            } else {
                resetFilter();
                console.log("Filter's DOM has been changed, please reset the page");
            }
        };
    }
    /**
     * Add event listener to reset button.
     * Resets filter when button is pressed.
     */
    function addResetButtonFunctionality()
    {
        resetButton.addEventListener('click', () => {
            if (filterActive) {
                resetFilter();
                Loader.showLoader(document.querySelector('.block-container'));
                this.deactivate();
                paginator.deactivate();

                TicketLoader.LoadTicketsFromServer(
                    this,
                    paginator,
                    true
                );
            }
        });
    }
    /**
     * Highlights pressed filter value.
     * Changes filter values in array.
     */
    function addHighlightFunctionality()
    {
        filterElement.addEventListener('click', (event) => {
            const li = event.target;
            const ul = li.parentElement;

            if ( li.nodeName !== 'LI' ||
                !filterActive ||
                filterValues[ul.dataset['filterType']].filterElement === li) {
                return;
            }


            // In case if user changes DOM element`` data-set and those indexes do not exist in filterValues array
            try {
                // Remove active highlight from old element
                filterValues[ul.dataset['filterType']].filterElement.classList.remove('active-filter');

                // Highligh current filter value
                li.classList.add('active-filter');

                // Add highlited element to array
                filterValues[ul.dataset['filterType']].filterElement = li;
            }
            catch(error) {
                resetFilter();
                alert('Do not change DOM, refresh page to return filter as it was');
            }

            // If filter is not extended and passes validation, load tickets
            if (!filterExtended) {

                if (validate()) {
                    // Set filter value
                    filterValues[ul.dataset['filterType']].value = li.dataset['filterValue'];

                    Loader.showLoader(document.querySelector('.block-container'));
                    this.deactivate();
                    paginator.deactivate();

                    TicketLoader.LoadTicketsFromServer(
                        this,
                        paginator,
                        true
                    );
                } else {
                    alert('Do not change DOM, refresh page to return filter as it was');
                }

            }
        });
    }

    /**
     * Validates filter. Checks whether it was changed in DOM or not.
     *
     * @return {boolean} check result.
     */
    function validate()
    {
        const orderByValues = [
            'newest',
            'oldest',
            'lowest-price',
            'highest-price'
        ];

        if (isNaN(filterValues['type'].value) ||
            (filterValues['type'].value < 0 ||
            filterValues['type'].value > 5)
        ) {

            return false;
        }

        if (!~orderByValues.indexOf(filterValues['order-by'].value)) {
            return false;
        }

        if (isNaN(filterValues['location'].value) ||
            filterValues['location'].value < 0 ||
            filterValues['location'].value > 6) {

            return false;
        }

        if (isNaN(filterValues['bedrooms'].value) ||
            filterValues['bedrooms'].value < 0 ||
            filterValues['bedrooms'].value > 5) {

            return false;
        }

        return true;
    }

    /**
     * Show filter extension section
     */
    function extend() {
        filterElement.classList.add('expand');
        extendButton.classList.add('close-button');

        // Let filter to finish its expand animation
        setTimeout(() => {
            filterExtension.style.display = 'block';
        }, 300);

        filterExtended = true;
    }

    /**
     * Hide filter extension section
     */
    function hideExtension() {
        extendButton.classList.remove('close-button');
        filterElement.classList.remove('expand');

        filterExtension.style.display = 'none';
        filterExtended = false;
    }
    /**
     * Fitler GET encode
     *
     * @return {string} GET filter values string
     */
    this.getStringify = function () {
        return '?filter[type]='+filterValues['type'].value+
               '&filter[order-by]='+filterValues['order-by'].value+
               '&filter[location]='+filterValues['location'].value+
               '&filter[bedrooms]='+filterValues['bedrooms'].value;
    };
    this.deactivate = function() {
        State.deactivate(filterElement);
        filterActive = false;
    };
    this.activate = function() {
        State.activate(filterElement);
        filterActive = true;
    };
    this.setPaginator = function(paginatorObj) {
        if (paginatorObj instanceof Paginator) {
            paginator = paginatorObj;
        }
    };
    this.getPaginator = function() {
        return paginator;
    };
}
/**
 * Scroll to given element
 *
 * @param {object} DOM element
 */
function scrollTo(element) {
  window.scroll({
    behavior: 'smooth',
    left: 0,
    top: element.offsetTop-20
  });
}
