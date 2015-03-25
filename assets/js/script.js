(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
module.exports = (function() {
	'use strict';

	var $ = window.jQuery,
		console  = window.console || { log: function() { }},
		isLoggedIn = $(document.body).hasClass('logged-in');

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

		if( this.enabled && this.locationHashRefersBox() ) {
			window.setTimeout(this.show.bind(this), 300);
		}

		this.setCustomBoxStyling();
	};

	// set (calculate) custom box styling depending on box options
	Box.prototype.setCustomBoxStyling = function() {
		if( this.position === 'center' ) {
			this.element.style.marginTop = ( ( window.innerHeight - this.$element.outerHeight() ) / 2 ) + "px";
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

		// fadein / fadeout the overlay if position is "center"
		if( this.position === 'center' ) {
			$(this.overlay).fadeToggle( 'slow' );
		}

		// show or hide box using selected animation
		if( this.animation === 'fade' ) {
			this.$element.fadeToggle( 'slow' );
		} else {
			this.$element.slideToggle( 'slow' );
		}

		this.visible = show;
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
				return $triggerElement.offset().top;
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

		if( ! window.location.hash || window.location.hash.length === 0 ) {
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

		if( this.minimumScreenWidth > 0 && window.innerWidth < this.minimumScreenWidth ) {
			return false;
		}

		if( isLoggedIn && this.testMode ) {
			console.log( 'Scroll Triggered Boxes: Test mode is enabled. Please disable test mode if you\'re done testing.' );
			return true;
		}

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
		scrollTimer = 0;

	var Box = require('./Box.js');

	// Functions

	// initialise & add event listeners
	function init() {
		$(".scroll-triggered-box").each(createBoxFromDOM);
		$(window).bind('scroll.stb', onScroll);
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

	// "scroll" listener
	function onScroll() {
		if( scrollTimer ) {
			window.clearTimeout(scrollTimer);
		}

		scrollTimer = window.setTimeout(checkBoxCriterias, 100);
	}

	// "keyup" listener
	function onKeyUp(e) {
		if (e.keyCode == 27) {
			hideAllBoxes();
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
		var scrollHeight = scrollY + windowHeight;

		for( var boxId in boxes ) {

			if( ! boxes.hasOwnProperty( boxId ) ) {
				continue;
			}

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

	// init on window.load
	jQuery(window).load(init);

	return {
		boxes: boxes,
		showBox: function(id) { boxes[id].show(); },
		hideBox: function(id) { boxes[id].hide(); },
		toggleBox: function(id) { boxes[id].toggle(); },
		hideAllBoxes: hideAllBoxes
	}

})(window.jQuery);
},{"./Box.js":1}],3:[function(require,module,exports){
window.STB = require('./STB.js');
},{"./STB.js":2}]},{},[1,2,3]);
