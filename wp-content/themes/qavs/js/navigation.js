/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	// Return early if the navigation don't exist.
	if ( ! siteNavigation ) {
		return;
	}

	const button = document.querySelector( '.menu-toggle' );

	// Return early if the button don't exist.
	if ( 'undefined' === typeof button ) {
		return;
  }

	const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	if ( ! menu.classList.contains( 'nav-menu' ) ) {
		menu.classList.add( 'nav-menu' );
	}

	// Toggle the .toggled class and the aria-expanded value each time the button is clicked.
	button.addEventListener( 'click', function() {
    siteNavigation.classList.toggle( 'toggled' );
    console.log("why")

		if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
			button.setAttribute( 'aria-expanded', 'false' );
		} else {
			button.setAttribute( 'aria-expanded', 'true' );
		}
	} );

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target ) || event.target == button;

		if ( ! isClickInside ) {
			siteNavigation.classList.remove( 'toggled' );
			button.setAttribute( 'aria-expanded', 'false' );
		}
	} );

	// Get all the link elements within the menu.
	const links = menu.getElementsByTagName( 'a' );

	// Get all the link elements with children within the menu.
	const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

	// Toggle focus each time a menu link is focused or blurred.
	for ( const link of links ) {
		link.addEventListener( 'focus', toggleFocus, true );
		link.addEventListener( 'blur', toggleFocus, true );
	}

	// Toggle focus each time a menu link with children receive a touch event.
	for ( const link of linksWithChildren ) {
		link.addEventListener( 'touchstart', toggleFocus, false );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		if ( event.type === 'focus' || event.type === 'blur' ) {
			let self = this;
			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( ! self.classList.contains( 'nav-menu' ) ) {
				// On li elements toggle the class .focus.
				if ( 'li' === self.tagName.toLowerCase() ) {
					self.classList.toggle( 'focus' );
				}
				self = self.parentNode;
			}
		}

		if ( event.type === 'touchstart' ) {
			const menuItem = this.parentNode;
			event.preventDefault();
			for ( const link of menuItem.parentNode.children ) {
				if ( menuItem !== link ) {
					link.classList.remove( 'focus' );
				}
			}
			menuItem.classList.toggle( 'focus' );
		}
  }

  var Website = {
    init: function() {
      this.__analyticsInitialized = false;
      this.__communicationsInitialized = false;

      this.checkCookies();
      this.handleCookiesSettings();
    },
    checkCookies: function() {
      var analyticsConsent = Cookies.get('given_analytics_cookies_consent');
      var communicationsConsent = Cookies.get('given_communications_cookies_consent');

      if (analyticsConsent === 'yes') {
        this.initializeAnalytics();
      }

      if (communicationsConsent === 'yes') {
        this.initializeCommunications();
      }
    },
    initializeAnalytics: function() {

    },
    initializeCommunications: function() {

    },
    handleCookiesSettings: function() {
      var radiosAnalytics = document.querySelectorAll('input[name="cookies-analytics"]');
      var radiosCommunications = document.querySelectorAll('input[name="cookies-communications"]');
      var saveButton = document.querySelector('.save-cookies');

      if (!saveButton) {
        return;
      }

      var analyticsConsent = Cookies.get('given_analytics_cookies_consent');
      var communicationsConsent = Cookies.get('given_communications_cookies_consent');

      if (analyticsConsent === 'yes') {
        document.getElementById('cookies-analytics-yes').checked = true;
      } else {
        document.getElementById('cookies-analytics-no').checked = true;
      }

      if (communicationsConsent === 'yes') {
        document.getElementById('cookies-communications-yes').checked = true;
      } else {
        document.getElementById('cookies-communications-no').checked = true;
      }

      saveButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var analyticsValue = document.querySelector('input[name="cookies-analytics"]:checked').value;
        var communicationsValue = document.querySelector('input[name="cookies-communications"]:checked').value;

        Cookies.set('given_analytics_cookies_consent', analyticsValue, { expires: 365 });
        Cookies.set('given_communications_cookies_consent', communicationsValue, { expires: 365 });
        Cookies.set('given_general_cookie_consent', 'yes', { expires: 365 });

        var existingMessage = document.querySelector('.save-cookie-message');

        if (existingMessage) {
          existingMessage.parentNode.removeChild(existingMessage);
        }

        saveButton.blur();

        var message = document.createElement('p');
        message.classList.add('save-cookie-message');
        message.setAttribute('role', 'alert');
        message.innerHTML = 'Your cookie preferences were successfully saved. These preferences will be valid for 1 year.';
        saveButton.insertAdjacentElement('afterend', message);
      })
    }
  }

  Website.init();
  
  var generalConsent = Cookies.get('given_general_cookie_consent');
  var cookieBanner = document.querySelector('.cookie-banner');
  var acceptedMessage = cookieBanner.querySelector('.accepted-message');
  var rejectedMessage = cookieBanner.querySelector('.rejected-message');
  var acceptAll = document.querySelector('.accept-additional-cookies');
  var rejectAll = document.querySelector('.reject-additional-cookies');
  var hideMessage = document.querySelectorAll('.hide-cookie-banner');

  if (typeof generalConsent === 'undefined') {
    cookieBanner.classList.remove('hidden');
  }

  acceptAll.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();

    Cookies.set('given_general_cookie_consent', 'yes', { expires: 365 });
    Cookies.set('given_analytics_cookies_consent', 'yes', { expires: 365 });
    Cookies.set('given_communications_cookies_consent', 'yes', { expires: 365 });

    cookieBanner.querySelector(".initial-content").classList.add("hidden");
    acceptedMessage.classList.remove("hidden");

    Website.checkCookies();
  });

  rejectAll.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();

    Cookies.set('given_general_cookie_consent', 'yes', { expires: 365 });
    Cookies.set('given_analytics_cookies_consent', 'no', { expires: 365 });
    Cookies.set('given_communications_cookies_consent', 'no', { expires: 365 });

    cookieBanner.querySelector(".initial-content").classList.add("hidden");
    rejectedMessage.classList.remove("hidden");
  });

  Array.from(hideMessage).forEach(function(e) {
    e.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      cookieBanner.classList.add('hidden');
    });
  });

  var mediaQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
  var controlButton = document.querySelector('.control-home-animation');
  var homeAnimationVideo = document.querySelector('.home-animation video');

  if (homeAnimationVideo) {
    // Check if the media query matches or is not available.
    if (!mediaQuery || mediaQuery.matches) {
      
    } else {
      var homeAnimationState = Cookies.get("home_animation_state");

      if (homeAnimationState !== 'pause') {
        controlButton.innerHTML = 'Pause animation';
        controlButton.classList.remove('hidden');
        homeAnimationVideo.play();
      }
    }

    // Ads an event listener to check for changes in the media query's value.
    mediaQuery.addEventListener("change", () => {
      if (mediaQuery.matches) {
        controlButton.classList.add('hidden');
        homeAnimationVideo.pause();
      } else {
        var homeAnimationState = Cookies.get("home_animation_state");

        if (homeAnimationState !== 'pause') {
          controlButton.innerHTML = 'Pause animation';
          controlButton.classList.remove('hidden');
          homeAnimationVideo.play();
        }
      }
    });

    controlButton.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      if (homeAnimationVideo.paused) {
        homeAnimationVideo.play();
        controlButton.innerHTML = "Play animation";
        Cookies.set('home_animation_state', 'play', { expires: 365 });
      } else {
        homeAnimationVideo.pause();
        controlButton.innerHTML = "Pause animation";
        Cookies.set('home_animation_state', 'pause', { expires: 365 });
      }
    });
  }

  var chairVideo = document.querySelector('.chair-video video');
  if (chairVideo) {
    var chairVideoButton = document.createElement('button');

    chairVideoButton.innerHTML = 'Play';
    chairVideo.insertAdjacentElement('afterend', chairVideoButton);
    chairVideoButton.addEventListener('click', function(e) {
      e.preventDefault()
      e.stopImmediatePropagation()
      chairVideoButton.classList.add('hidden')
      chairVideo.play()
      chairVideo.setAttribute("controls", "controls")
    })
  }
}() );
