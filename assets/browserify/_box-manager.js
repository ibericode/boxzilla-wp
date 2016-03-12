module.exports = (function($) {
	'use strict';

	// Global Variables
	var boxes = {},
		inited = false,
		windowHeight = window.innerHeight,
		scrollTimer = 0,
		resizeTimer = 0,
		overlay = document.getElementById('stb-overlay'),
		options = window.STB_Global_Options || {},
		EventEmitter = require('./_event-emitter.js'),
		events = new EventEmitter;

	var Box = require('./_box.js');

	// Functions

	// initialise & add event listeners
	function init() {
		// make sure we only init once
		if( inited ) return;

		$(".scroll-triggered-box").each(createBoxFromDOM);

		// event binds
		$(window).bind('scroll.stb', onScroll);
		$(window).bind('resize.stb', onWindowResize);
		$(window).bind('load', onLoad );
		$(document).keyup(onKeyUp);
		$(overlay).click(onOverlayClick);

		// print message when test mode is enabled
		if( options.testMode ) {
			console.log( 'Scroll Triggered Boxes: Test mode is enabled. Please disable test mode if you\'re done testing.' );
		}

		inited = true;

		events.trigger('ready');
	}

	function onLoad() {
		recalculateHeights();
	}

	// create a Box object from the DOM
	function createBoxFromDOM() {
		var $box = $(this);
		var id = parseInt(this.id.substring(4));
		var boxOptions = STB_Box_Options[id];
		boxOptions.element = this;
		boxOptions.$element = $box;
		boxOptions.testMode = options.testMode;
		boxes[boxOptions.id] = new Box(boxOptions, events);
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
			dismissAllBoxes();
		}
	}

	// hide and disable all registered boxes
	function dismissAllBoxes() {
		for( var boxId in boxes ) {
			var box = boxes[boxId];
			if( box.visible && ! box.config.unclosable ) {
				box.dismiss();
			}
		}
	}

	// show all registered boxes
	function showAllBoxes() {
		for( var boxId in boxes ) {
			var box = boxes[boxId];
			if( ! box.visible ) {
				box.show();
			}
		}
	}

	// hide all registered boxes
	function hideAllBoxes() {
		for( var boxId in boxes ) {
			var box = boxes[boxId];
			if( box.visible ) {
				box.hide();
			}
		}
	}

	// check criteria for all registered boxes
	// todo: refactor part of this into box object?
	function checkBoxCriterias() {

		var scrollY = $(window).scrollTop();
		var scrollHeight = scrollY + ( windowHeight * 0.9 );

		for( var boxId in boxes ) {
			var box = boxes[boxId];

			if( ! box.mayAutoShow() ) {
				continue;
			}

			if( box.triggerHeight <= 0 ) {
				continue;
			}

			if( scrollHeight > box.triggerHeight ) {
				if( ! box.visible ) {
					box.show();
					box.triggered = true;
				}
			} else if( box.mayAutoHide() ) {
				if( box.visible ) {
					box.hide();
				}
			}
		}
	}

	// recalculate heights and variables based on height
	function recalculateHeights() {
		windowHeight = window.innerHeight;

		for( var boxId in boxes ) {
			var box = boxes[boxId];
			box.setCustomBoxStyling();
		}
	}

	// dismiss a single box (or all by omitting id param)
	function dismiss(id) {
		// if no id given, dismiss all current open boxes
		if( typeof(id) === "undefined" ) {
			dismissAllBoxes();
		} else if( typeof( boxes[id] ) === "object" ) {
			boxes[id].dismiss();
		}
	}

	function hideBox(id) {
		if( typeof( boxes[id] ) === "object" ) {
			boxes[id].hide();
		}
	}

	function showBox(id) {
		if( typeof( boxes[id] ) === "object" ) {
			boxes[id].show();
		}
	}

	function toggleBox(id) {
		if( typeof( boxes[id] ) === "object" ) {
			boxes[id].toggle();
		}
	}

	function onOverlayClick(e) {
		var x = e.offsetX;
		var y = e.offsetY;

		// calculate if click was near a box to avoid closing it (click error margin)
		for(var boxId in boxes ) {
			var box = boxes[boxId];
			if( ! box.visible || box.config.unclosable ) { continue; }

			var rect = box.element.getBoundingClientRect();
			var margin = 100 + ( window.innerWidth * 0.05 );

			// if click was not anywhere near box, dismiss it.
			if( x < ( rect.left - margin ) || x > ( rect.right + margin ) || y < ( rect.top - margin ) || y > ( rect.bottom + margin ) ) {
				box.dismiss();
			}
		}
	}

	// init on document.ready OR in 5 seconds in case event pipeline is broken
	$(document).ready(init);
	window.setTimeout(init, 5000);

	// expose a simple API to control all registered boxes
	return {
		boxes: boxes,
		showBox: showBox,
		hideBox: hideBox,
		toggleBox: toggleBox,
		showAllBoxes: showAllBoxes,
		hideAllBoxes: hideAllBoxes,
		dismissAllBoxes: dismissAllBoxes,
		dismiss: dismiss,
		events: events
	}

})(window.jQuery);