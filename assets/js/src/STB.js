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
		hideAllBoxes: hideAllBoxes,
		disableAllBoxes: disableAllBoxes
	}

})(window.jQuery);