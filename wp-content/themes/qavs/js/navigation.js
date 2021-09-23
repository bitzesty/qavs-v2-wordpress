/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	var siteNavigation = document.getElementById( 'site-navigation' );

	// Return early if the navigation don't exist.
	if ( ! siteNavigation ) {
		return;
	}

	var button = document.querySelector( '.menu-toggle' );

	// Return early if the button don't exist.
	if ( 'undefined' === typeof button ) {
		return;
  }

	var menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

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

		if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
			button.setAttribute( 'aria-expanded', 'false' );
		} else {
			button.setAttribute( 'aria-expanded', 'true' );
		}
	} );

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		var isClickInside = siteNavigation.contains( event.target ) || event.target == button;

		if ( ! isClickInside ) {
			siteNavigation.classList.remove( 'toggled' );
			button.setAttribute( 'aria-expanded', 'false' );
		}
	} );

	// Get all the link elements within the menu.
	var links = menu.getElementsByTagName( 'a' );

	// Get all the link elements with children within the menu.
	var linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

	// Toggle focus each time a menu link is focused or blurred.
	Array.prototype.slice.call(links).forEach(function(link) {
		link.addEventListener( 'focus', toggleFocus, true );
		link.addEventListener( 'blur', toggleFocus, true );
	});

	// Toggle focus each time a menu link with children receive a touch event.
	Array.prototype.slice.call(linksWithChildren).forEach(function(link) {
		link.addEventListener( 'touchstart', toggleFocus, false );
	});

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		if ( event.type === 'focus' || event.type === 'blur' ) {
			var self = this;
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
			var menuItem = this.parentNode;
			event.preventDefault();
			Array.prototype.slice.call(menuItem.parentNode.children).forEach(function(link) {
				if ( menuItem !== link ) {
					link.classList.remove( 'focus' );
				}
			});
			menuItem.classList.toggle( 'focus' );
		}
  }

  var Website = {
    init: function() {
      this.handleCookiesSettings();
    },
    handleCookiesSettings: function() {
      var saveButton = document.querySelector('.save-cookies');
      var cookieForm = document.querySelector('.cookie-save-form');

      if (!saveButton) {
        return;
      }

      var analyticsConsent = Cookies.get('analytics_cookies_consent_status');
      // var communicationsConsent = Cookies.get('communications_cookies_consent_status');
      var preferenceConsent = Cookies.get('communications_cookies_consent_status');

      if (analyticsConsent === 'yes') {
        document.getElementById('cookies-analytics-yes').checked = true;
      } else if (analyticsConsent == 'no') {
        document.getElementById('cookies-analytics-no').checked = true;
      }

      // if (communicationsConsent === 'yes') {
      //   document.getElementById('cookies-communications-yes').checked = true;
      // } else if (communicationsConsent == 'no') {
      //   document.getElementById('cookies-communications-no').checked = true;
      // }

      if (preferenceConsent === 'yes') {
        document.getElementById('cookies-preference-yes').checked = true;
      } else if (preferenceConsent === 'no') {
        document.getElementById('cookies-preference-no').checked = true;
      }

      cookieForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var analyticsRadio = document.querySelector('input[name="cookies-analytics"]:checked');
        var analyticsValue = analyticsRadio ? analyticsRadio.value : null;

        // var communicationsRadio = document.querySelector('input[name="cookies-communications"]:checked');
        // var communicationsValue = communicationsRadio ? communicationsRadio.value : null;

        var preferenceRadio = document.querySelector('input[name="cookies-preference"]:checked');
        var preferenceValue = preferenceRadio ? preferenceRadio.value : null;

        if (analyticsValue !== null) {
          Cookies.set('analytics_cookies_consent_status', analyticsValue, { expires: 365 });

          gtag('consent', 'update', {
            'analytics_storage': analyticsValue === 'yes' ? 'granted' : 'denied'
          });
        }
        // if (communicationsValue !== null) {
        //   Cookies.set('communications_cookies_consent_status', communicationsValue, { expires: 365 });
        // }
        if (preferenceValue !== null) {
          Cookies.set('preference_cookies_consent_status', preferenceValue, { expires: 365 });
        }
        Cookies.set('general_cookie_consent_status', 'yes', { expires: 365 });

        var existingMessage = document.querySelector('.save-cookie-message');

        if (existingMessage) {
          existingMessage.parentNode.removeChild(existingMessage);
        }

        var message = document.createElement('p');
        message.classList.add('save-cookie-message');
        message.setAttribute('role', 'alert');
        message.innerHTML = 'Your cookie preferences were successfully saved. These preferences will be valid for 1 year.';
        saveButton.insertAdjacentElement('afterend', message);
      })
    }
  }

  Website.init();

  function isInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)

    );
  }

  var generalConsent = Cookies.get('general_cookie_consent_status');
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

    Cookies.set('general_cookie_consent_status', 'yes', { expires: 365 });
    Cookies.set('analytics_cookies_consent_status', 'yes', { expires: 365 });
    // Cookies.set('communications_cookies_consent_status', 'yes', { expires: 365 });
    Cookies.set('preference_cookies_consent_status', 'yes', { expires: 365 });

    gtag('consent', 'update', {
      'analytics_storage': 'granted'
    });

    cookieBanner.querySelector(".initial-content").classList.add("hidden");
    acceptedMessage.classList.remove("hidden");
  });

  rejectAll.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();

    Cookies.set('general_cookie_consent_status', 'yes', { expires: 365 });
    // Cookies.set('analytics_cookies_consent_status', 'no', { expires: 365 });
    // Cookies.set('communications_cookies_consent_status', 'no', { expires: 365 });
    // Cookies.set('preference_cookies_consent_status', 'no', { expires: 365 });

    gtag('consent', 'update', {
      'analytics_storage': 'denied'
    });

    cookieBanner.querySelector(".initial-content").classList.add("hidden");
    rejectedMessage.classList.remove("hidden");
  });

  Array.prototype.slice.call(hideMessage).forEach(function(e) {
    e.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      cookieBanner.classList.add('hidden');
    });
  });

  var mediaQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
  var controlButton = document.querySelector('.control-home-animation');
  var homeAnimationVideo = document.querySelector('.home-animation-video');

  var hasPlayed = false;

  var checkAndPlayAnimation = function() {

    var homeAnimationState = Cookies.get("home_animation_state");

    if (homeAnimationState !== 'pause') {
      document.addEventListener('scroll', function(e) {
        if (hasPlayed) {
          return;
        }
        if (isInViewport(homeAnimationVideo)) {
          hasPlayed = true;
          homeAnimationVideo.play();
        }
      }, {
        passive: true
      });
    }
  }

  if (homeAnimationVideo) {
    // Check if the media query matches or is not available.
    if (!mediaQuery || mediaQuery.matches) {

    } else {
      checkAndPlayAnimation();
    }

    homeAnimationVideo.addEventListener('play', function() {
      controlButton.classList.add("playing");
      controlButton.classList.remove("paused");
    });

    homeAnimationVideo.addEventListener('ended', function() {
      controlButton.classList.remove("playing");
      controlButton.classList.add("paused");
    });

    homeAnimationVideo.addEventListener('pause', function() {
      controlButton.classList.remove("playing");
      controlButton.classList.add("paused");
    });

    // Ads an event listener to check for changes in the media query's value.
    try {
      mediaQuery.addEventListener("change", function() {
        if (mediaQuery.matches) {
          homeAnimationVideo.pause();
        } else {
          checkAndPlayAnimation();
        }
      });
    } catch (e) {

    }

    controlButton.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      if (homeAnimationVideo.paused) {
        homeAnimationVideo.play();
        if (Cookies.get('preference_cookies_consent_status') === 'yes') {
          Cookies.set('home_animation_state', 'play', { expires: 365 })
        }
      } else {
        homeAnimationVideo.pause();
        if (Cookies.get('preference_cookies_consent_status') === 'yes') {
          Cookies.set('home_animation_state', 'pause', { expires: 365 })
        }
      }
    });

    controlButton.classList.remove('hidden');
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


  // external links
  Array.prototype.slice.call(document.getElementsByTagName('a')).forEach(function(link) {
    var a = new RegExp('/' + window.location.host + '/');
    if (!a.test(link.href)) {
      link.setAttribute("target", "_blank");
      link.setAttribute("rel", "noopener nofollow");
    }
  });

  // videos
  Array.prototype.slice.call(document.querySelectorAll('.px-video-container')).forEach(function(videoContainer) {
    new InitPxVideo({
      "videoId": videoContainer.id,
      "captionsOnDefault": true,
      "seekInterval": 20,
      "videoTitle": videoContainer.dataset.title
    });
  });
}() );
