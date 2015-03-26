(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
module.exports = (function() {
	'use strict';

	var $ = window.jQuery,
		console  = window.console || { log: function() { }},
		isLoggedIn = $(document.body).hasClass('logged-in'),
		startTime = new Date().getTime();

	// Box Object
	var Box = function( data ) {
		this.id 		= data.id;
		this.element 	= data.element;
		this.$element 	= $(data.element);
		this.position 	= data.position;
		this.trigger 	= data.trigger;
		this.cookieTime = data.cookie;
		this.testMode 	= data.testMode;
		this.autoHide 	= data.autoHide;
		this.triggerElementSelector = data.triggerElementSelector;
		this.triggerPercentage = data.triggerPercentage;
		this.animation 	= data.animation;
		this.visible 	= false;
		this.minimumScreenWidth = data.minimumScreenWidth;
		this.overlay = document.getElementById('stb-overlay');

		// calculate triggerHeight
		this.triggerHeight = this.calculateTriggerHeight();
		this.enabled = 	this.isBoxEnabled();

		// further initialise the box
		this.init();
	};

	// initialise the box
	Box.prototype.init = function() {
		// attach event to "close" icon inside box
		this.$element.find('.stb-close').click(this.disable.bind(this));

		// attach event to all links referring #stb-{box_id}
		$('a[href="#' + this.$element.attr('id') +'"]').click(function() { this.toggle(); return false;}.bind(this));

		// auto-show the box if box is referenced from URL
		if( this.enabled && this.locationHashRefersBox() ) {
			window.setTimeout(this.show.bind(this), 300);
		}
	};


	// set (calculate) custom box styling depending on box options
	Box.prototype.setCustomBoxStyling = function() {

		// reset element to its initial state
		this.element.style.overflowY = 'auto';
		this.element.style.maxHeight = 'none';

		// get new dimensions
		var windowHeight = window.innerHeight;
		var boxHeight = this.$element.outerHeight();

		// does box + margin fit on screen?
		if( ( boxHeight + 40 ) > windowHeight ) {

			// add scrollbar to box and limit height
			this.element.style.maxHeight = ( windowHeight - 40 ) + "px";
			this.element.style.overflowY = 'scroll';

		}

		// set new top margin for boxes which are centered
		if( this.position === 'center' ) {
			var newTopMargin = ( ( windowHeight - boxHeight ) / 2 );
			if( newTopMargin < 20 ) newTopMargin = 20;
			this.element.style.marginTop = newTopMargin + "px";
		}

	};

	// toggle visibility of the box
	Box.prototype.toggle = function(show) {

		// revert visibility if no explicit argument is given
		if( typeof( show ) === "undefined" ) {
			show = ! this.visible;
		}

		// do nothing if element is being animated
		if( this.$element.is(':animated') ) {
			return false;
		}

		// is box already at desired visibility?
		if( show === this.visible ) {
			return false;
		}

		// set new visibility status
		this.visible = show;

		// fadein / fadeout the overlay if position is "center"
		this.setCustomBoxStyling();

		if( this.position === 'center' ) {
			$(this.overlay).fadeToggle('slow');
		}

		// show or hide box using selected animation
		if( this.animation === 'fade' ) {
			this.$element.fadeToggle( 'slow' );
		} else {
			this.$element.slideToggle( 'slow' );
		}

		return true;
	};

	// show the box
	Box.prototype.show = function() {
		return this.toggle(true);
	};

	// hide the box
	Box.prototype.hide = function() {
		return this.toggle(false);
	};

	// calculate trigger height
	Box.prototype.calculateTriggerHeight = function() {

		if( this.trigger === 'element' ) {
			var $triggerElement = $(this.triggerElementSelector).first();
			if( $triggerElement.length > 0 ) {
				// return top offset of element
				return $triggerElement.offset().top;
			} else {
				// element was not found, disable box.
				return 0;
			}
		}

		// calcate % of page height
		return ( this.triggerPercentage / 100 * $(document).height() );
	};

	// set cookie that disables automatically showing the box
	Box.prototype.setCookie = function() {
		if(this.cookieTime > 0) {
			var expiryDate = new Date();
			expiryDate.setDate( expiryDate.getDate() + this.cookieTime );
			document.cookie = 'stb_box_'+ id + '=true; expires='+ expiryDate.toUTCString() +'; path=/';
		}
	};

	// checks whether window.location.hash equals the box element ID of that of any
	Box.prototype.locationHashRefersBox = function() {

		if( ! window.location.hash || 0 === window.location.hash.length ) {
			return false;
		}

		var elementId = window.location.hash.substring(1);
		if( elementId === this.element.id ) {
			return true;
		} else if( this.element.querySelector('#' + elementId) ) {
			return true;
		}

		return false;
	};

	// is this box enabled?
	Box.prototype.isBoxEnabled = function() {

		// don't show if triggerHeight is 0 (element not found or percentage set to 0)
		if( this.triggerHeight === 0 ) {
			return false;
		}

		// check if box fits on width
		if( this.minimumScreenWidth > 0 && window.innerWidth < this.minimumScreenWidth ) {
			return false;
		}

		// always show on test mode
		if( isLoggedIn && this.testMode ) {
			console.log( 'Scroll Triggered Boxes: Test mode is enabled for box #'+ this.id +'. Please disable test mode if you\'re done testing.' );
			return true;
		}

		// don't show if page just loaded (2 seconds)
		//// todo: make an option out of this
		//var currentTime = new Date().getTime();
		//if( ( startTime + 2000 ) > currentTime ) {
		//	return false;
		//}

		// check for cookie
		if( this.cookieTime === 0 ) {
			return true;
		}

		var isDisabledByCookie = document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + 'stb_box_' + this.id + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1") === "true";
		return ( ! isDisabledByCookie );
	};

	// disable the box
	Box.prototype.disable = function() {
		this.hide();
		this.enabled = false;
		this.setCookie();
	};

	return Box;
})();
},{}],2:[function(require,module,exports){
module.exports = (function($) {
	'use strict';

	// Global Variables
	var boxes = {},
		windowHeight = window.innerHeight,
		scrollTimer = 0,
		resizeTimer = 0;

	var Box = require('./Box.js');

	// Functions

	// initialise & add event listeners
	function init() {
		$(".scroll-triggered-box").each(createBoxFromDOM);
		$(window).bind('scroll.stb', onScroll);
		$(window).bind('resize.stb', onWindowResize);
		$(document).keyup(onKeyUp);
	}

	// create a Box object from the DOM
	function createBoxFromDOM() {
		var $box = $(this);
		var id = parseInt(this.id.substring(4));
		var options = STB_Options[id];
		options.element = this;
		options.$element = $box;
		boxes[options.id] = new Box(options);
	}

	function onWindowResize() {
		resizeTimer && clearTimeout(resizeTimer);
		resizeTimer = window.setTimeout(recalculateHeights, 100);
	}

	// "scroll" listener
	function onScroll() {
		scrollTimer && clearTimeout(scrollTimer);
		scrollTimer = window.setTimeout(checkBoxCriterias, 100);
	}

	// "keyup" listener
	function onKeyUp(e) {
		if (e.keyCode == 27) {
			disableAllBoxes();
		}
	}

	// hide and disable all registered boxes
	function disableAllBoxes() {
		for( var boxId in boxes ) {
			boxes[boxId].disable();
		}
	}

	// hide all registered boxes
	function hideAllBoxes() {
		for( var boxId in boxes ) {
			boxes[boxId].hide();
		}
	}

	// check criteria for all registered boxes
	function checkBoxCriterias() {

		var scrollY = $(window).scrollTop();
		var scrollHeight = scrollY + ( windowHeight * 0.9 );

		for( var boxId in boxes ) {
			var box = boxes[boxId];

			// don't show if box is disabled (by cookie)
			if( ! box.enabled ) {
				continue;
			}

			if( scrollHeight > box.triggerHeight ) {
				box.show();
			} else if( box.autoHide ) {
				box.hide();
			}
		}
	}

	// recalculate heights and variables based on height
	function recalculateHeights() {
		for( var boxId in boxes ) {
			var box = boxes[boxId];
			box.setCustomBoxStyling();
		}
	}

	// init on window.load
	$(window).load(init);

	// expose a simple API to control all registered boxes
	return {
		boxes: boxes,
		showBox: function(id) { boxes[id].show(); },
		hideBox: function(id) { boxes[id].hide(); },
		toggleBox: function(id) { boxes[id].toggle(); },
		hideAllBoxes: hideAllBoxes,
		disableAllBoxes: disableAllBoxes
	}

})(window.jQuery);
},{"./Box.js":1}],3:[function(require,module,exports){
window.STB = require('./STB.js');
},{"./STB.js":2}]},{},[1,2,3]);
