import $ from 'jquery';

class Search {
    constructor() {
        this.addSearchHTML();
        this.openButton = $('.js-search-trigger');
        this.closeButton = $('.search-overlay__close');
        this.searchOverlay = $('.search-overlay');
        this.resultsContainer = $('#search-overlay__results');
        this.searchInput = $('#search-term');
        this.previousSearchTerm = null;
        this.isSpinnerVisible = false;
        this.isOverlayOpen = false;
        this.typingTimer = null;
        this.events();
    }

    events() {
        $(document).on('keydown', this.keyPressDispatcher.bind(this));
        this.openButton.on('click', this.openOverlay.bind(this));
        this.closeButton.on('click', this.closeOverlay.bind(this));
        this.searchInput.on('keyup', this.typingLogic.bind(this))
    }

    typingLogic() {
        if (this.previousSearchTerm !== this.searchInput.val()) {
            clearTimeout(this.typingTimer);

            if (this.searchInput.val()) {
                if (!this.isSpinnerVisible) {
                    this.resultsContainer.html('<div class="spinner-loader"></div>')
                    this.isSpinnerVisible = true;
                }

                this.typingTimer = setTimeout(this.getResults.bind(this), 500);
            } else {
                this.resultsContainer.html('');
                this.isSpinnerVisible = false;
            }

            this.previousSearchTerm = this.searchInput.val();
        }
    }

    getResults() {
        $.getJSON(`${universityData.root_url}/wp-json/university/v1/search?q=${this.searchInput.val()}`, (results) => {
            this.resultsContainer.html(`
                <div class="row">
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">General Information!!</h2>
                        ${results.general_info.length ? `<ul class="link-list min-list">` : `<p>No general information matches that search</p>`}
                            ${results.general_info.map(post => (`<li><a href="${post?.permalink}">${post?.title}</a> ${post.author_name ? `by ${post.author_name}` : ''}</li>`)).join('')}
                        ${results.general_info.length ? `</ul>` : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Programs</h2>
                        ${results.programs.length ? `<ul class="link-list min-list">` : `<p>No programs match that search.<a href="${universityData.root_url}/programs"> See all Programs</a></p>`}
                            ${results.programs.map(program => (`<li><a href="${program?.permalink}">${program?.title}</a></li>`)).join('')}
                        ${results.programs.length ? `</ul>` : ''}
                        
                        <h2 class="search-overlay__section-title">Professors</h2>
                        ${results.professors.length ? `<ul class="professor-cards">` : `<p>No professors match that search.</p>`}
                            ${results.professors.map(professor => (`
                                <li class="professor-card__list-item">
                                    <a class="professor-card" href="${professor?.permalink}">
                                        <img src="${professor?.image}" alt=""
                                             class="professor-card__image">
                                        <span class="professor-card__name">${professor?.title}</span>
                                    </a>
                                </li>
                            `)).join('')}
                        ${results.professors.length ? `</ul>` : ''}
                    </div>
                    <div class="one-third">
                        <h2 class="search-overlay__section-title">Campuses</h2>
                        ${results.campuses.length ? `<ul class="link-list min-list">` : `<p>No campuses match that search.<a href="${universityData.root_url}/campuses"> See all Campuses</a></p>`}
                            ${results.campuses.map(post => (`<li><a href="${post?.permalink}">${post?.title}</a></li>`)).join('')}
                        ${results.campuses.length ? `</ul>` : ''}
                        
                        <h2 class="search-overlay__section-title">Events</h2>
                        ${results.events.length ? '' : `<p>No events match that search.<a href="${universityData.root_url}/events"> See all Events</a></p>`}
                            ${results.events.map(event => (`
                                <div class="event-summary">
                                    <a class="event-summary__date event-summary__date t-center" href="${event?.permalink}">
                                        <span class="event-summary__month">${event.month}</span>
                                        <span class="event-summary__day">${event.day}</span>
                                    </a>
                                    <div class="event-summary__content">
                                        <h5 class="event-summary__title headline headline--tiny">
                                            <a href="${event?.permalink}">${event?.title}</a>
                                        </h5>
                                        <p>
                                            ${event?.description}
                                            <a href="${event?.permalink}" class="nu gray">Read more</a>
                                        </p>
                                    </div>
                                </div>
                            `)).join('')}
                        ${results.events.length ? `</ul>` : ''}
                    </div>
                </div>
            `);
            this.isSpinnerVisible = false;
        })
    }

    keyPressDispatcher(e) {
        if ((e.key === 's' || e.key === 'S') && !this.isOverlayOpen && !$('input, textarea').is(':focus')) {
            this.openOverlay();
        }

        if (e.key === 'Escape' && this.isOverlayOpen) {
            this.closeOverlay();
        }
    }

    openOverlay() {
        $('body').addClass('body-no-scroll')
        this.searchOverlay.addClass('search-overlay--active');
        this.isOverlayOpen = true;
        this.searchInput.val('');
        setTimeout(() => this.searchInput.focus(), 315);
    }

    closeOverlay() {
        this.searchOverlay.removeClass('search-overlay--active');
        $('body').removeClass('body-no-scroll');
        this.isOverlayOpen = false;
    }

    addSearchHTML() {
        $('body').append(`
            <div class="search-overlay">
                <div class="search-overlay__top">
                    <div class="container">
                        <i class="fa fa-search search-overlay__icon" aria-hidden="true"></i>
                        <input id="search-term" class="search-term" type="text" placeholder="What are you looking for?"
                               autocomplete="off">
                        <i class="fa fa-window-close search-overlay__close" aria-hidden="true"></i>
                    </div>
                </div>
                <div class="container">
                    <div id="search-overlay__results">
            
                    </div>
                </div>
            </div>
        `)
    }
}

export default Search