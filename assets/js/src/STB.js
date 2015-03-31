module.exports = (function($) {
	'use strict';

	// Global Variables
	var boxes = {},
		windowHeight = window.innerHeight,
		scrollTimer = 0,
		resizeTimer = 0,
		options = window.STB_Global_Options || {};

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
		var boxOptions = STB_Box_Options[id];
		boxOptions.element = this;
		boxOptions.$element = $box;
		boxOptions.testMode = options.testMode;
		boxes[boxOptions.id] = new Box(boxOptions);
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