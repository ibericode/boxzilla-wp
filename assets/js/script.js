(function () { var require = undefined; var define = undefined; (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
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
},{"./_box.js":2,"./_event-emitter.js":3}],2:[function(require,module,exports){
module.exports = (function() {
	'use strict';

	var $ = window.jQuery,
		console  = window.console || { log: function() { }},
		isLoggedIn = $(document.body).hasClass('logged-in'),
		startTime = new Date().getTime();

	// Box Object
	var Box = function( config, events ) {
		this.id 		= config.id;
		this.title 		= config.title;
		this.element 	= config.element;
		this.$element 	= $(config.element);

		// store config values
		this.config = config;
		this.events = events;

		// store ref to overlay
		this.overlay = document.getElementById('stb-overlay');

		// state
		this.visible 	= false;
		this.closed 	= false;
		this.triggered 	= false;
		this.triggerHeight = 0;
		this.cookieSet = false;

		// if a trigger was given, calculate some values which might otherwise be expensive)
		if( this.config.autoShow && this.config.trigger !== '' ) {

			if( this.config.trigger === 'percentage' || this.config.trigger === 'element' ) {
				this.triggerHeight = this.calculateTriggerHeight( config.triggerPercentage, config.triggerElementSelector );
			}

			this.cookieSet = this.isCookieSet();
		}

		// further initialise the box
		this.init();
	};

	// initialise the box
	Box.prototype.init = function() {
		var box = this;

		// attach event to "close" icon inside box
		this.$element.find('.stb-close').click(box.dismiss.bind(this));

		// find all links & forms in this box
		this.$links = this.$element.find('a');
		this.$forms = this.$element.find('form');

		this.$links.click(function(e) {
			box.events.trigger('box.interactions.link', [ box, e.target ] );
		});

		this.$forms.submit(function(e) {
			box.setCookie();
			box.events.trigger('box.interactions.form', [ box, e.target ]);
		});

		// attach event to all links referring #stb-{box_id}
		$(document.body).on('click', 'a[href="#stb-' + box.id + '"]', function() {
			box.toggle();
			return false;
		});

		if( this.config.autoShow && this.config.trigger === 'instant' && ! this.cookieSet ) {
			$(window).load(this.show.bind(this));
		} else {
			// auto-show the box if box is referenced from URL
			if( this.locationHashRefersBox() ) {
				$(window).load(this.show.bind(this));
			}
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

		// add scrollbar to box and limit height
		if( boxHeight > windowHeight ) {
			this.element.style.maxHeight = windowHeight + "px";
			this.element.style.overflowY = 'scroll';
		}

		// set new top margin for boxes which are centered
		if( this.config.position === 'center' ) {
			var newTopMargin = ( ( windowHeight - boxHeight ) / 2 );
			newTopMargin = newTopMargin >= 0 ? newTopMargin : 0;
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

		// calculate custom styling for which CSS is "too stupid"
		this.setCustomBoxStyling();

		// fadein / fadeout the overlay if position is "center"
		if( this.config.position === 'center' ) {
			$(this.overlay).fadeToggle('slow');
		}

		// trigger event
		this.events.trigger('box.' + ( show ? 'show' : 'hide' ), [ this ] );

		// show or hide box using selected animation
		if( this.config.animation === 'fade' ) {
			this.$element.fadeToggle( 'slow' );
		} else {
			this.$element.slideToggle( 'slow' );
		}

		return true;
	};

	// show the box
	Box.prototype.show = function() {
		this.events.trigger('box.show', [ this ]);
		return this.toggle(true);
	};

	// hide the box
	Box.prototype.hide = function() {
		this.events.trigger('box.hide', [ this ]);
		return this.toggle(false);
	};

	// calculate trigger height
	Box.prototype.calculateTriggerHeight = function( triggerPercentage, triggerElementSelector ) {

		if( this.config.trigger === 'element' ) {
			var $triggerElement = $(triggerElementSelector).first();
			if( $triggerElement.length > 0 ) {
				// return top offset of element
				return $triggerElement.offset().top;
			} else {
				// element was not found, disable box.
				return 0;
			}
		}

		// calcate % of page height
		return ( triggerPercentage / 100 * $(document).height() );
	};

	// set cookie that disables automatically showing the box
	Box.prototype.setCookie = function() {
		// do nothing if cookieTime evaluates to false
		if(! this.config.cookieTime) {
			return;
		}

		var expiryDate = new Date();
		expiryDate.setDate( expiryDate.getDate() + this.config.cookieTime );
		document.cookie = 'stb_box_'+ this.id + '=true; expires='+ expiryDate.toUTCString() +'; path=/';
	};

	// checks whether window.location.hash equals the box element ID or that of any element inside the box
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
	Box.prototype.mayAutoShow = function() {

		// don't show if autoShow is disabled
		if( ! this.config.autoShow ) {
			return false;
		}

		// don't show if box was closed before
		if( this.closed ) {
			return false;
		}

		// check if box fits on given minimum screen width
		if( this.config.minimumScreenWidth > 0 && window.innerWidth < this.config.minimumScreenWidth ) {
			return false;
		}

		// don't show if page just loaded ( 500ms)
		// todo: make an option out of this
		var currentTime = new Date().getTime();
		if( ( startTime + 500 ) > currentTime ) {
			return false;
		}

		// rely on cookie value (show if not set, don't show if set)
		return ! this.cookieSet;
	};

	Box.prototype.mayAutoHide = function() {

		// check if autoHide was allowed from config
		if( ! this.config.autoHide ) {
			return false;
		}

		// only allow autoHide when box has been autoshown (triggered)
		return this.triggered;
	};

	Box.prototype.isCookieSet = function() {
		// always show on test mode
		if( isLoggedIn && this.config.testMode ) {
			return false;
		}

		// check for cookie
		if( this.config.cookieTime === 0 ) {
			return false;
		}

		var cookieSet = document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + 'stb_box_' + this.id + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1") === "true";
		return cookieSet;

	};

	// disable the box
	Box.prototype.dismiss = function() {
		this.hide();
		this.setCookie();
		this.closed = true;
		this.events.trigger('box.dismiss', [ this ]);
	};

	return Box;
})();
},{}],3:[function(require,module,exports){
/*!
 * EventEmitter v4.2.11 - git.io/ee
 * Unlicense - http://unlicense.org/
 * Oliver Caldwell - http://oli.me.uk/
 * @preserve
 */

;(function () {
	'use strict';

	/**
	 * Class for managing events.
	 * Can be extended to provide event functionality in other classes.
	 *
	 * @class EventEmitter Manages event registering and emitting.
	 */
	function EventEmitter() {}

	// Shortcuts to improve speed and size
	var proto = EventEmitter.prototype;
	var exports = this;
	var originalGlobalValue = exports.EventEmitter;

	/**
	 * Finds the index of the listener for the event in its storage array.
	 *
	 * @param {Function[]} listeners Array of listeners to search through.
	 * @param {Function} listener Method to look for.
	 * @return {Number} Index of the specified listener, -1 if not found
	 * @api private
	 */
	function indexOfListener(listeners, listener) {
		var i = listeners.length;
		while (i--) {
			if (listeners[i].listener === listener) {
				return i;
			}
		}

		return -1;
	}

	/**
	 * Alias a method while keeping the context correct, to allow for overwriting of target method.
	 *
	 * @param {String} name The name of the target method.
	 * @return {Function} The aliased method
	 * @api private
	 */
	function alias(name) {
		return function aliasClosure() {
			return this[name].apply(this, arguments);
		};
	}

	/**
	 * Returns the listener array for the specified event.
	 * Will initialise the event object and listener arrays if required.
	 * Will return an object if you use a regex search. The object contains keys for each matched event. So /ba[rz]/ might return an object containing bar and baz. But only if you have either defined them with defineEvent or added some listeners to them.
	 * Each property in the object response is an array of listener functions.
	 *
	 * @param {String|RegExp} evt Name of the event to return the listeners from.
	 * @return {Function[]|Object} All listener functions for the event.
	 */
	proto.getListeners = function getListeners(evt) {
		var events = this._getEvents();
		var response;
		var key;

		// Return a concatenated array of all matching events if
		// the selector is a regular expression.
		if (evt instanceof RegExp) {
			response = {};
			for (key in events) {
				if (events.hasOwnProperty(key) && evt.test(key)) {
					response[key] = events[key];
				}
			}
		}
		else {
			response = events[evt] || (events[evt] = []);
		}

		return response;
	};

	/**
	 * Takes a list of listener objects and flattens it into a list of listener functions.
	 *
	 * @param {Object[]} listeners Raw listener objects.
	 * @return {Function[]} Just the listener functions.
	 */
	proto.flattenListeners = function flattenListeners(listeners) {
		var flatListeners = [];
		var i;

		for (i = 0; i < listeners.length; i += 1) {
			flatListeners.push(listeners[i].listener);
		}

		return flatListeners;
	};

	/**
	 * Fetches the requested listeners via getListeners but will always return the results inside an object. This is mainly for internal use but others may find it useful.
	 *
	 * @param {String|RegExp} evt Name of the event to return the listeners from.
	 * @return {Object} All listener functions for an event in an object.
	 */
	proto.getListenersAsObject = function getListenersAsObject(evt) {
		var listeners = this.getListeners(evt);
		var response;

		if (listeners instanceof Array) {
			response = {};
			response[evt] = listeners;
		}

		return response || listeners;
	};

	/**
	 * Adds a listener function to the specified event.
	 * The listener will not be added if it is a duplicate.
	 * If the listener returns true then it will be removed after it is called.
	 * If you pass a regular expression as the event name then the listener will be added to all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to attach the listener to.
	 * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.addListener = function addListener(evt, listener) {
		var listeners = this.getListenersAsObject(evt);
		var listenerIsWrapped = typeof listener === 'object';
		var key;

		for (key in listeners) {
			if (listeners.hasOwnProperty(key) && indexOfListener(listeners[key], listener) === -1) {
				listeners[key].push(listenerIsWrapped ? listener : {
					listener: listener,
					once: false
				});
			}
		}

		return this;
	};

	/**
	 * Alias of addListener
	 */
	proto.on = alias('addListener');

	/**
	 * Semi-alias of addListener. It will add a listener that will be
	 * automatically removed after its first execution.
	 *
	 * @param {String|RegExp} evt Name of the event to attach the listener to.
	 * @param {Function} listener Method to be called when the event is emitted. If the function returns true then it will be removed after calling.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.addOnceListener = function addOnceListener(evt, listener) {
		return this.addListener(evt, {
			listener: listener,
			once: true
		});
	};

	/**
	 * Alias of addOnceListener.
	 */
	proto.once = alias('addOnceListener');

	/**
	 * Defines an event name. This is required if you want to use a regex to add a listener to multiple events at once. If you don't do this then how do you expect it to know what event to add to? Should it just add to every possible match for a regex? No. That is scary and bad.
	 * You need to tell it what event names should be matched by a regex.
	 *
	 * @param {String} evt Name of the event to create.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.defineEvent = function defineEvent(evt) {
		this.getListeners(evt);
		return this;
	};

	/**
	 * Uses defineEvent to define multiple events.
	 *
	 * @param {String[]} evts An array of event names to define.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.defineEvents = function defineEvents(evts) {
		for (var i = 0; i < evts.length; i += 1) {
			this.defineEvent(evts[i]);
		}
		return this;
	};

	/**
	 * Removes a listener function from the specified event.
	 * When passed a regular expression as the event name, it will remove the listener from all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to remove the listener from.
	 * @param {Function} listener Method to remove from the event.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.removeListener = function removeListener(evt, listener) {
		var listeners = this.getListenersAsObject(evt);
		var index;
		var key;

		for (key in listeners) {
			if (listeners.hasOwnProperty(key)) {
				index = indexOfListener(listeners[key], listener);

				if (index !== -1) {
					listeners[key].splice(index, 1);
				}
			}
		}

		return this;
	};

	/**
	 * Alias of removeListener
	 */
	proto.off = alias('removeListener');

	/**
	 * Adds listeners in bulk using the manipulateListeners method.
	 * If you pass an object as the second argument you can add to multiple events at once. The object should contain key value pairs of events and listeners or listener arrays. You can also pass it an event name and an array of listeners to be added.
	 * You can also pass it a regular expression to add the array of listeners to all events that match it.
	 * Yeah, this function does quite a bit. That's probably a bad thing.
	 *
	 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add to multiple events at once.
	 * @param {Function[]} [listeners] An optional array of listener functions to add.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.addListeners = function addListeners(evt, listeners) {
		// Pass through to manipulateListeners
		return this.manipulateListeners(false, evt, listeners);
	};

	/**
	 * Removes listeners in bulk using the manipulateListeners method.
	 * If you pass an object as the second argument you can remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
	 * You can also pass it an event name and an array of listeners to be removed.
	 * You can also pass it a regular expression to remove the listeners from all events that match it.
	 *
	 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to remove from multiple events at once.
	 * @param {Function[]} [listeners] An optional array of listener functions to remove.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.removeListeners = function removeListeners(evt, listeners) {
		// Pass through to manipulateListeners
		return this.manipulateListeners(true, evt, listeners);
	};

	/**
	 * Edits listeners in bulk. The addListeners and removeListeners methods both use this to do their job. You should really use those instead, this is a little lower level.
	 * The first argument will determine if the listeners are removed (true) or added (false).
	 * If you pass an object as the second argument you can add/remove from multiple events at once. The object should contain key value pairs of events and listeners or listener arrays.
	 * You can also pass it an event name and an array of listeners to be added/removed.
	 * You can also pass it a regular expression to manipulate the listeners of all events that match it.
	 *
	 * @param {Boolean} remove True if you want to remove listeners, false if you want to add.
	 * @param {String|Object|RegExp} evt An event name if you will pass an array of listeners next. An object if you wish to add/remove from multiple events at once.
	 * @param {Function[]} [listeners] An optional array of listener functions to add/remove.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.manipulateListeners = function manipulateListeners(remove, evt, listeners) {
		var i;
		var value;
		var single = remove ? this.removeListener : this.addListener;
		var multiple = remove ? this.removeListeners : this.addListeners;

		// If evt is an object then pass each of its properties to this method
		if (typeof evt === 'object' && !(evt instanceof RegExp)) {
			for (i in evt) {
				if (evt.hasOwnProperty(i) && (value = evt[i])) {
					// Pass the single listener straight through to the singular method
					if (typeof value === 'function') {
						single.call(this, i, value);
					}
					else {
						// Otherwise pass back to the multiple function
						multiple.call(this, i, value);
					}
				}
			}
		}
		else {
			// So evt must be a string
			// And listeners must be an array of listeners
			// Loop over it and pass each one to the multiple method
			i = listeners.length;
			while (i--) {
				single.call(this, evt, listeners[i]);
			}
		}

		return this;
	};

	/**
	 * Removes all listeners from a specified event.
	 * If you do not specify an event then all listeners will be removed.
	 * That means every event will be emptied.
	 * You can also pass a regex to remove all events that match it.
	 *
	 * @param {String|RegExp} [evt] Optional name of the event to remove all listeners for. Will remove from every event if not passed.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.removeEvent = function removeEvent(evt) {
		var type = typeof evt;
		var events = this._getEvents();
		var key;

		// Remove different things depending on the state of evt
		if (type === 'string') {
			// Remove all listeners for the specified event
			delete events[evt];
		}
		else if (evt instanceof RegExp) {
			// Remove all events matching the regex.
			for (key in events) {
				if (events.hasOwnProperty(key) && evt.test(key)) {
					delete events[key];
				}
			}
		}
		else {
			// Remove all listeners in all events
			delete this._events;
		}

		return this;
	};

	/**
	 * Alias of removeEvent.
	 *
	 * Added to mirror the node API.
	 */
	proto.removeAllListeners = alias('removeEvent');

	/**
	 * Emits an event of your choice.
	 * When emitted, every listener attached to that event will be executed.
	 * If you pass the optional argument array then those arguments will be passed to every listener upon execution.
	 * Because it uses `apply`, your array of arguments will be passed as if you wrote them out separately.
	 * So they will not arrive within the array on the other side, they will be separate.
	 * You can also pass a regular expression to emit to all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
	 * @param {Array} [args] Optional array of arguments to be passed to each listener.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.emitEvent = function emitEvent(evt, args) {
		var listeners = this.getListenersAsObject(evt);
		var listener;
		var i;
		var key;
		var response;

		for (key in listeners) {
			if (listeners.hasOwnProperty(key)) {
				i = listeners[key].length;

				while (i--) {
					// If the listener returns true then it shall be removed from the event
					// The function is executed either with a basic call or an apply if there is an args array
					listener = listeners[key][i];

					if (listener.once === true) {
						this.removeListener(evt, listener.listener);
					}

					response = listener.listener.apply(this, args || []);

					if (response === this._getOnceReturnValue()) {
						this.removeListener(evt, listener.listener);
					}
				}
			}
		}

		return this;
	};

	/**
	 * Alias of emitEvent
	 */
	proto.trigger = alias('emitEvent');

	/**
	 * Subtly different from emitEvent in that it will pass its arguments on to the listeners, as opposed to taking a single array of arguments to pass on.
	 * As with emitEvent, you can pass a regex in place of the event name to emit to all events that match it.
	 *
	 * @param {String|RegExp} evt Name of the event to emit and execute listeners for.
	 * @param {...*} Optional additional arguments to be passed to each listener.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.emit = function emit(evt) {
		var args = Array.prototype.slice.call(arguments, 1);
		return this.emitEvent(evt, args);
	};

	/**
	 * Sets the current value to check against when executing listeners. If a
	 * listeners return value matches the one set here then it will be removed
	 * after execution. This value defaults to true.
	 *
	 * @param {*} value The new value to check for when executing listeners.
	 * @return {Object} Current instance of EventEmitter for chaining.
	 */
	proto.setOnceReturnValue = function setOnceReturnValue(value) {
		this._onceReturnValue = value;
		return this;
	};

	/**
	 * Fetches the current value to check against when executing listeners. If
	 * the listeners return value matches this one then it should be removed
	 * automatically. It will return true by default.
	 *
	 * @return {*|Boolean} The current value to check for or the default, true.
	 * @api private
	 */
	proto._getOnceReturnValue = function _getOnceReturnValue() {
		if (this.hasOwnProperty('_onceReturnValue')) {
			return this._onceReturnValue;
		}
		else {
			return true;
		}
	};

	/**
	 * Fetches the events object and creates one if required.
	 *
	 * @return {Object} The events storage object.
	 * @api private
	 */
	proto._getEvents = function _getEvents() {
		return this._events || (this._events = {});
	};

	/**
	 * Reverts the global {@link EventEmitter} to its previous value and returns a reference to this version.
	 *
	 * @return {Function} Non conflicting EventEmitter class.
	 */
	EventEmitter.noConflict = function noConflict() {
		exports.EventEmitter = originalGlobalValue;
		return EventEmitter;
	};

	// Expose the class either via AMD, CommonJS or the global object
	if (typeof define === 'function' && define.amd) {
		define(function () {
			return EventEmitter;
		});
	}
	else if (typeof module === 'object' && module.exports){
		module.exports = EventEmitter;
	}
	else {
		exports.EventEmitter = EventEmitter;
	}
}.call(this));
},{}],4:[function(require,module,exports){
window.STB = require('./_box-manager.js');
},{"./_box-manager.js":1}]},{},[4]);
 })();