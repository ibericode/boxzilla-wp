'use strict';

function throttle(fn, threshhold, scope) {
	threshhold || (threshhold = 250);
	var last,
		deferTimer;
	return function () {
		var context = scope || this;

		var now = +new Date,
			args = arguments;
		if (last && now < last + threshhold) {
			// hold on to it
			clearTimeout(deferTimer);
			deferTimer = setTimeout(function () {
				last = now;
				fn.apply(context, args);
			}, threshhold);
		} else {
			last = now;
			fn.apply(context, args);
		}
	};
}

var Manager = (function($) {

	// Global Variables
	var boxes = {},
		inited = false,
		windowHeight = window.innerHeight,
		overlay = document.createElement('div'),
		options = window.boxzilla_options,
		EventEmitter = require('wolfy87-eventemitter'),
		events = new EventEmitter;

	var Box = require('./_box.js');

	// initialise & add event listeners
	function init() {
		// make sure we only init once
		if( inited ) return;

		// add overlay element to dom
		overlay.id = 'boxzilla-overlay';
		document.body.appendChild(overlay);

		// create Box object for each box
		options.boxes.forEach(createBox);

		// event binds
		$(window).bind('scroll.boxzilla', throttle(checkBoxCriterias));
		$(window).bind('resize.boxzilla', throttle(recalculateHeights));
		$(window).bind('load', onLoad );
		$(document).keyup(onKeyUp);
		$(overlay).click(onOverlayClick);

		// print message when test mode is enabled
		if( options.testMode ) {
			console.log( 'Boxzilla: Test mode is enabled. Please disable test mode if you\'re done testing.' );
		}

		inited = true;

		events.trigger('ready');
	}

	// create a Box object from the DOM
	function createBox(boxOptions) {
		boxOptions.testMode = options.testMode;
		boxes[boxOptions.id] = new Box(boxOptions, events);
	}

	// "window.load" listener
	function onLoad() {
		recalculateHeights();
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
		'boxes': boxes,
		'showBox': showBox,
		'hideBox': hideBox,
		'toggleBox': toggleBox,
		'showAllBoxes': showAllBoxes,
		'hideAllBoxes': hideAllBoxes,
		'dismissAllBoxes': dismissAllBoxes,
		'dismiss': dismiss,
		'events': events
	}

})(window.jQuery);

module.exports = Manager;