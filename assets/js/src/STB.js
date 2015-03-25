module.exports = (function($) {
	'use strict';

	// Global Variables
	var boxes = {},
		windowHeight = window.innerHeight,
		scrollTimer = 0;

	var Box = require('./Box.js');

	// Functions
	function init() {
		$(".scroll-triggered-box").each(createBoxFromDOM);
		$(window).bind('scroll.stb', onScroll);
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

	// schedule a check of all box criterias in 100ms
	function onScroll() {
		if( scrollTimer ) {
			window.clearTimeout(scrollTimer);
		}

		scrollTimer = window.setTimeout(checkBoxCriterias, 100);
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
		toggleBox: function(id) { boxes[id].toggle(); }
	}

})(window.jQuery);