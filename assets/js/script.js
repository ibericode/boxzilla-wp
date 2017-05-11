(function () { var require = undefined; var module = undefined; var exports = undefined; var define = undefined; (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

(function () {
    'use strict';

    var Boxzilla = require('boxzilla');
    var options = window.boxzilla_options;

    // expose Boxzilla object to window
    window.Boxzilla = Boxzilla;

    function ready(fn) {
        if (document.readyState != 'loading') {
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    // helper function for setting CSS styles
    function css(element, styles) {
        if (styles.background_color) {
            element.style.background = styles.background_color;
        }

        if (styles.color) {
            element.style.color = styles.color;
        }

        if (styles.border_color) {
            element.style.borderColor = styles.border_color;
        }

        if (styles.border_width) {
            element.style.borderWidth = parseInt(styles.border_width) + "px";
        }

        if (styles.border_style) {
            element.style.borderStyle = styles.border_style;
        }

        if (styles.width) {
            element.style.maxWidth = parseInt(styles.width) + "px";
        }
    }

    function createBoxesFromConfig() {
        var isLoggedIn = document.body.className.indexOf('logged-in') > -1;

        // failsafe against including script twice.
        if (options.inited) {
            return;
        }

        // print message when test mode is enabled
        if (isLoggedIn && options.testMode) {
            console.log('Boxzilla: Test mode is enabled. Please disable test mode if you\'re done testing.');
        }

        // init boxzilla
        Boxzilla.init();

        // create boxes from options
        for (var i = 0; i < options.boxes.length; i++) {
            // get opts
            var boxOpts = options.boxes[i];
            boxOpts.testMode = isLoggedIn && options.testMode;

            // fix http:// links in box content....
            if (window.location.protocol === "https:" && window.location.host) {
                var o = "http://" + window.location.host;
                var n = o.replace('http://', 'https://');
                boxOpts.content = boxOpts.content.replace(o, n);
            }

            // create box
            var box = Boxzilla.create(boxOpts.id, boxOpts);

            // add box slug to box element as classname
            box.element.className = box.element.className + ' boxzilla-' + boxOpts.post.slug;

            // add custom css to box
            css(box.element, boxOpts.css);

            box.element.firstChild.firstChild.className += " first-child";
            box.element.firstChild.lastChild.className += " last-child";
        }

        /**
         * If a MailChimp for WordPress form was submitted, open the box containing that form (if any)
         *
         * TODO: Just set location hash from MailChimp for WP?
         */
        window.addEventListener('load', openMailChimpForWordPressBox);

        options.inited = true;

        // trigger "done" event.
        Boxzilla.trigger('done');
    }

    function openMailChimpForWordPressBox() {
        if (_typeof(window.mc4wp_forms_config) === "object" && window.mc4wp_forms_config.submitted_form) {
            var selector = '#' + window.mc4wp_forms_config.submitted_form.element_id;
            var boxes = Boxzilla.boxes;
            for (var boxId in boxes) {
                if (!boxes.hasOwnProperty(boxId)) {
                    continue;
                }
                var box = boxes[boxId];
                if (box.element.querySelector(selector)) {
                    box.show();
                    return;
                }
            }
        }
    }

    // create boxes as soon as document.ready fires
    ready(createBoxesFromConfig);
})();

},{"boxzilla":4}],2:[function(require,module,exports){
'use strict';

var duration = 320;

function css(element, styles) {
    for (var property in styles) {
        element.style[property] = styles[property];
    }
}

function initObjectProperties(properties, value) {
    var newObject = {};
    for (var i = 0; i < properties.length; i++) {
        newObject[properties[i]] = value;
    }
    return newObject;
}

function copyObjectProperties(properties, object) {
    var newObject = {};
    for (var i = 0; i < properties.length; i++) {
        newObject[properties[i]] = object[properties[i]];
    }
    return newObject;
}

/**
 * Checks if the given element is currently being animated.
 *
 * @param element
 * @returns {boolean}
 */
function animated(element) {
    return !!element.getAttribute('data-animated');
}

/**
 * Toggles the element using the given animation.
 *
 * @param element
 * @param animation Either "fade" or "slide"
 */
function toggle(element, animation, callbackFn) {
    var nowVisible = element.style.display != 'none' || element.offsetLeft > 0;

    // create clone for reference
    var clone = element.cloneNode(true);
    var cleanup = function cleanup() {
        element.removeAttribute('data-animated');
        element.setAttribute('style', clone.getAttribute('style'));
        element.style.display = nowVisible ? 'none' : '';
        if (callbackFn) {
            callbackFn();
        }
    };

    // store attribute so everyone knows we're animating this element
    element.setAttribute('data-animated', "true");

    // toggle element visiblity right away if we're making something visible
    if (!nowVisible) {
        element.style.display = '';
    }

    var hiddenStyles, visibleStyles;

    // animate properties
    if (animation === 'slide') {
        hiddenStyles = initObjectProperties(["height", "borderTopWidth", "borderBottomWidth", "paddingTop", "paddingBottom"], 0);
        visibleStyles = {};

        if (!nowVisible) {
            var computedStyles = window.getComputedStyle(element);
            visibleStyles = copyObjectProperties(["height", "borderTopWidth", "borderBottomWidth", "paddingTop", "paddingBottom"], computedStyles);
            css(element, hiddenStyles);
        }

        // don't show a scrollbar during animation
        element.style.overflowY = 'hidden';
        animate(element, nowVisible ? hiddenStyles : visibleStyles, cleanup);
    } else {
        hiddenStyles = { opacity: 0 };
        visibleStyles = { opacity: 1 };
        if (!nowVisible) {
            css(element, hiddenStyles);
        }

        animate(element, nowVisible ? hiddenStyles : visibleStyles, cleanup);
    }
}

function animate(element, targetStyles, fn) {
    var last = +new Date();
    var initialStyles = window.getComputedStyle(element);
    var currentStyles = {};
    var propSteps = {};

    for (var property in targetStyles) {
        // make sure we have an object filled with floats
        targetStyles[property] = parseFloat(targetStyles[property]);

        // calculate step size & current value
        var to = targetStyles[property];
        var current = parseFloat(initialStyles[property]);

        // is there something to do?
        if (current == to) {
            delete targetStyles[property];
            continue;
        }

        propSteps[property] = (to - current) / duration; // points per second
        currentStyles[property] = current;
    }

    var tick = function tick() {
        var now = +new Date();
        var timeSinceLastTick = now - last;
        var done = true;

        var step, to, increment, newValue;
        for (var property in targetStyles) {
            step = propSteps[property];
            to = targetStyles[property];
            increment = step * timeSinceLastTick;
            newValue = currentStyles[property] + increment;

            if (step > 0 && newValue >= to || step < 0 && newValue <= to) {
                newValue = to;
            } else {
                done = false;
            }

            // store new value
            currentStyles[property] = newValue;

            var suffix = property !== "opacity" ? "px" : "";
            element.style[property] = newValue + suffix;
        }

        last = +new Date();

        // keep going until we're done for all props
        if (!done) {
            window.requestAnimationFrame && requestAnimationFrame(tick) || setTimeout(tick, 32);
        } else {
            // call callback
            fn && fn();
        }
    };

    tick();
}

module.exports = {
    'toggle': toggle,
    'animate': animate,
    'animated': animated
};

},{}],3:[function(require,module,exports){
'use strict';

var defaults = {
    'animation': 'fade',
    'rehide': false,
    'content': '',
    'cookie': null,
    'icon': '&times',
    'screenWidthCondition': null,
    'position': 'center',
    'testMode': false,
    'trigger': false,
    'closable': true
},
    Boxzilla,
    Animator = require('./animator.js');

/**
 * Merge 2 objects, values of the latter overwriting the former.
 *
 * @param obj1
 * @param obj2
 * @returns {*}
 */
function merge(obj1, obj2) {
    var obj3 = {};
    for (var attrname in obj1) {
        obj3[attrname] = obj1[attrname];
    }
    for (var attrname in obj2) {
        obj3[attrname] = obj2[attrname];
    }
    return obj3;
}

/**
 * Get the real height of entire document.
 * @returns {number}
 */
function getDocumentHeight() {
    var body = document.body,
        html = document.documentElement;

    var height = Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight);

    return height;
}

// Box Object
var Box = function Box(id, config) {
    this.id = id;

    // store config values
    this.config = merge(defaults, config);

    // store ref to overlay
    this.overlay = document.getElementById('boxzilla-overlay');

    // state
    this.visible = false;
    this.dismissed = false;
    this.triggered = false;
    this.triggerHeight = 0;
    this.cookieSet = false;
    this.element = null;
    this.contentElement = null;
    this.closeIcon = null;

    // if a trigger was given, calculate values once and store
    if (this.config.trigger) {
        if (this.config.trigger.method === 'percentage' || this.config.trigger.method === 'element') {
            this.triggerHeight = this.calculateTriggerHeight();
        }

        this.cookieSet = this.isCookieSet();
    }

    // create dom elements for this box
    this.dom();

    // further initialise the box
    this.events();
};

// initialise the box
Box.prototype.events = function () {
    var box = this;

    // attach event to "close" icon inside box
    if (this.closeIcon) {
        this.closeIcon.addEventListener('click', this.dismiss.bind(this));
    }

    this.element.addEventListener('click', function (e) {
        if (e.target.tagName === 'A') {
            Boxzilla.trigger('box.interactions.link', [box, e.target]);
        }
    }, false);

    this.element.addEventListener('submit', function (e) {
        box.setCookie();
        Boxzilla.trigger('box.interactions.form', [box, e.target]);
    }, false);

    // maybe show box right away
    if (this.fits() && this.locationHashRefersBox()) {
        window.addEventListener('load', this.show.bind(this));
    }
};

// generate dom elements for this box
Box.prototype.dom = function () {
    var wrapper = document.createElement('div');
    wrapper.className = 'boxzilla-container boxzilla-' + this.config.position + '-container';

    var box = document.createElement('div');
    box.setAttribute('id', 'boxzilla-' + this.id);
    box.className = 'boxzilla boxzilla-' + this.id + ' boxzilla-' + this.config.position;
    box.style.display = 'none';
    wrapper.appendChild(box);

    var content = document.createElement('div');
    content.className = 'boxzilla-content';
    content.innerHTML = this.config.content;
    box.appendChild(content);

    // remove <script> from box content and append them to the document body
    var scripts = content.querySelectorAll('script');
    if (scripts.length) {
        for (var i = 0; i < scripts.length; i++) {
            var script = document.createElement('script');
            if (scripts[i].src) {
                script.src = scripts[i].src;
            }
            script.appendChild(document.createTextNode(scripts[i].text));
            scripts[i].parentNode.removeChild(scripts[i]);
            document.body.appendChild(script);
        }
    }

    if (this.config.closable && this.config.icon) {
        var closeIcon = document.createElement('span');
        closeIcon.className = "boxzilla-close-icon";
        closeIcon.innerHTML = this.config.icon;
        box.appendChild(closeIcon);
        this.closeIcon = closeIcon;
    }

    document.body.appendChild(wrapper);
    this.contentElement = content;
    this.element = box;
};

// set (calculate) custom box styling depending on box options
Box.prototype.setCustomBoxStyling = function () {

    // reset element to its initial state
    var origDisplay = this.element.style.display;
    this.element.style.display = '';
    this.element.style.overflowY = 'auto';
    this.element.style.maxHeight = 'none';

    // get new dimensions
    var windowHeight = window.innerHeight;
    var boxHeight = this.element.clientHeight;

    // add scrollbar to box and limit height
    if (boxHeight > windowHeight) {
        this.element.style.maxHeight = windowHeight + "px";
        this.element.style.overflowY = 'scroll';
    }

    // set new top margin for boxes which are centered
    if (this.config.position === 'center') {
        var newTopMargin = (windowHeight - boxHeight) / 2;
        newTopMargin = newTopMargin >= 0 ? newTopMargin : 0;
        this.element.style.marginTop = newTopMargin + "px";
    }

    this.element.style.display = origDisplay;
};

// toggle visibility of the box
Box.prototype.toggle = function (show) {

    // revert visibility if no explicit argument is given
    if (typeof show === "undefined") {
        show = !this.visible;
    }

    // is box already at desired visibility?
    if (show === this.visible) {
        return false;
    }

    // is box being animated?
    if (Animator.animated(this.element)) {
        return false;
    }

    // if box should be hidden but is not closable, bail.
    if (!show && !this.config.closable) {
        return false;
    }

    // set new visibility status
    this.visible = show;

    // calculate new styling rules
    this.setCustomBoxStyling();

    // trigger event
    Boxzilla.trigger('box.' + (show ? 'show' : 'hide'), [this]);

    // show or hide box using selected animation
    if (this.config.position === 'center') {
        this.overlay.classList.toggle('boxzilla-' + this.id + '-overlay');
        Animator.toggle(this.overlay, "fade");
    }

    Animator.toggle(this.element, this.config.animation, function () {
        if (this.visible) {
            return;
        }
        this.contentElement.innerHTML = this.contentElement.innerHTML;
    }.bind(this));

    return true;
};

// show the box
Box.prototype.show = function () {
    return this.toggle(true);
};

// hide the box
Box.prototype.hide = function () {
    return this.toggle(false);
};

// calculate trigger height
Box.prototype.calculateTriggerHeight = function () {
    var triggerHeight = 0;

    if (this.config.trigger.method === 'element') {
        var triggerElement = document.body.querySelector(this.config.trigger.value);
        if (triggerElement) {
            var offset = triggerElement.getBoundingClientRect();
            triggerHeight = offset.top;
        }
    } else if (this.config.trigger.method === 'percentage') {
        triggerHeight = this.config.trigger.value / 100 * getDocumentHeight();
    }

    return triggerHeight;
};

// checks whether window.location.hash equals the box element ID or that of any element inside the box
Box.prototype.locationHashRefersBox = function () {

    if (!window.location.hash || 0 === window.location.hash.length) {
        return false;
    }

    var elementId = window.location.hash.substring(1);
    if (elementId === this.element.id) {
        return true;
    } else if (this.element.querySelector('#' + elementId)) {
        return true;
    }

    return false;
};

Box.prototype.fits = function () {
    if (!this.config.screenWidthCondition || !this.config.screenWidthCondition.value) {
        return true;
    }

    switch (this.config.screenWidthCondition.condition) {
        case "larger":
            return window.innerWidth > this.config.screenWidthCondition.value;
        case "smaller":
            return window.innerWidth < this.config.screenWidthCondition.value;
    }

    // meh.. condition should be "smaller" or "larger", just return true.
    return true;
};

// is this box enabled?
Box.prototype.mayAutoShow = function () {

    if (this.dismissed) {
        return false;
    }

    // check if box fits on given minimum screen width
    if (!this.fits()) {
        return false;
    }

    // if trigger empty or error in calculating triggerHeight, return false
    if (!this.config.trigger) {
        return false;
    }

    // rely on cookie value (show if not set, don't show if set)
    return !this.cookieSet;
};

Box.prototype.mayRehide = function () {
    return this.config.rehide && this.triggered;
};

Box.prototype.isCookieSet = function () {
    // always show on test mode
    if (this.config.testMode) {
        return false;
    }

    // if either cookie is null or trigger & dismiss are both falsey, don't bother checking.
    if (!this.config.cookie || !this.config.cookie.triggered && !this.config.cookie.dismissed) {
        return false;
    }

    var cookieSet = document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + 'boxzilla_box_' + this.id + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1") === "true";
    return cookieSet;
};

// set cookie that disables automatically showing the box
Box.prototype.setCookie = function (hours) {
    var expiryDate = new Date();
    expiryDate.setHours(expiryDate.getHours() + hours);
    document.cookie = 'boxzilla_box_' + this.id + '=true; expires=' + expiryDate.toUTCString() + '; path=/';
};

Box.prototype.trigger = function () {
    var shown = this.show();
    if (!shown) {
        return;
    }

    this.triggered = true;
    if (this.config.cookie && this.config.cookie.triggered) {
        this.setCookie(this.config.cookie.triggered);
    }
};

/**
 * Dismisses the box and optionally sets a cookie.
 *
 * @param e The event that triggered this dismissal.
 * @returns {boolean}
 */
Box.prototype.dismiss = function (e) {
    // prevent default action
    e && e.preventDefault();

    // only dismiss box if it's currently open.
    if (!this.visible) {
        return false;
    }

    // hide box element
    this.hide();

    // set cookie
    if (this.config.cookie && this.config.cookie.dismissed) {
        this.setCookie(this.config.cookie.dismissed);
    }

    this.dismissed = true;
    Boxzilla.trigger('box.dismiss', [this]);
    return true;
};

module.exports = function (_Boxzilla) {
    Boxzilla = _Boxzilla;
    return Box;
};

},{"./animator.js":2}],4:[function(require,module,exports){
'use strict';

var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var EventEmitter = require('wolfy87-eventemitter'),
    Boxzilla = Object.create(EventEmitter.prototype),
    Box = require('./box.js')(Boxzilla),
    Timer = require('./timer.js'),
    boxes = [],
    overlay,
    scrollElement = window,
    exitIntentDelayTimer,
    exitIntentTriggered,
    siteTimer,
    pageTimer,
    pageViews;

function throttle(fn, threshhold, scope) {
    threshhold || (threshhold = 250);
    var last, deferTimer;
    return function () {
        var context = scope || this;

        var now = +new Date(),
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

// "keyup" listener
function onKeyUp(e) {
    if (e.keyCode == 27) {
        Boxzilla.dismiss();
    }
}

// check "pageviews" criteria for each box
function checkPageViewsCriteria() {

    // don't bother if another box is currently open
    if (isAnyBoxVisible()) {
        return;
    }

    boxes.forEach(function (box) {
        if (!box.mayAutoShow()) {
            return;
        }

        if (box.config.trigger.method === 'pageviews' && pageViews >= box.config.trigger.value) {
            box.trigger();
        }
    });
}

// check time trigger criteria for each box
function checkTimeCriteria() {
    // don't bother if another box is currently open
    if (isAnyBoxVisible()) {
        return;
    }

    boxes.forEach(function (box) {
        if (!box.mayAutoShow()) {
            return;
        }

        // check "time on site" trigger
        if (box.config.trigger.method === 'time_on_site' && siteTimer.time >= box.config.trigger.value) {
            box.trigger();
        }

        // check "time on page" trigger
        if (box.config.trigger.method === 'time_on_page' && pageTimer.time >= box.config.trigger.value) {
            box.trigger();
        }
    });
}

// check triggerHeight criteria for all boxes
function checkHeightCriteria() {

    var scrollY = scrollElement.hasOwnProperty('scrollY') ? scrollElement.scrollY : scrollElement.scrollTop;
    scrollY = scrollY + window.innerHeight * 0.75;

    boxes.forEach(function (box) {
        if (!box.mayAutoShow() || box.triggerHeight <= 0) {
            return;
        }

        if (scrollY > box.triggerHeight) {
            // don't bother if another box is currently open
            if (isAnyBoxVisible()) {
                return;
            }

            // trigger box
            box.trigger();
        } else if (box.mayRehide()) {
            box.hide();
        }
    });
}

// recalculate heights and variables based on height
function recalculateHeights() {
    boxes.forEach(function (box) {
        box.setCustomBoxStyling();
    });
}

function onOverlayClick(e) {
    var x = e.offsetX;
    var y = e.offsetY;

    // calculate if click was less than 40px outside box to avoid closing it by accident
    boxes.forEach(function (box) {
        var rect = box.element.getBoundingClientRect();
        var margin = 40;

        // if click was not anywhere near box, dismiss it.
        if (x < rect.left - margin || x > rect.right + margin || y < rect.top - margin || y > rect.bottom + margin) {
            box.dismiss();
        }
    });
}

function triggerExitIntent() {
    // do nothing if already triggered OR another box is visible.
    if (exitIntentTriggered || isAnyBoxVisible()) {
        return;
    }

    boxes.forEach(function (box) {
        if (box.mayAutoShow() && box.config.trigger.method === 'exit_intent') {
            box.trigger();
        }
    });

    exitIntentTriggered = true;
}

function onMouseLeave(e) {
    var delay = 400;

    // did mouse leave at top of window?
    if (e.clientY <= 0) {
        exitIntentDelayTimer = window.setTimeout(triggerExitIntent, delay);
    }
}

function isAnyBoxVisible() {

    for (var i = 0; i < boxes.length; i++) {
        var box = boxes[i];

        if (box.visible) {
            return true;
        }
    }

    return false;
}

function onMouseEnter() {
    if (exitIntentDelayTimer) {
        window.clearInterval(exitIntentDelayTimer);
        exitIntentDelayTimer = null;
    }
}

function onElementClick(e) {
    // find <a> element in up to 3 parent elements
    var el = e.target || e.srcElement;
    var depth = 3;
    for (var i = 0; i <= depth; i++) {
        if (!el || el.tagName === 'A') {
            break;
        }

        el = el.parentElement;
    }

    if (el && el.tagName === 'A' && el.getAttribute('href').toLowerCase().indexOf('#boxzilla-') === 0) {
        var boxId = el.getAttribute('href').toLowerCase().substring("#boxzilla-".length);
        Boxzilla.toggle(boxId);
    }
}

var timers = {
    start: function start() {
        try {
            var sessionTime = sessionStorage.getItem('boxzilla_timer');
            if (sessionTime) siteTimer.time = sessionTime;
        } catch (e) {}
        siteTimer.start();
        pageTimer.start();
    },
    stop: function stop() {
        sessionStorage.setItem('boxzilla_timer', siteTimer.time);
        siteTimer.stop();
        pageTimer.stop();
    }
};

// initialise & add event listeners
Boxzilla.init = function () {
    document.body.addEventListener('click', onElementClick, false);

    try {
        pageViews = sessionStorage.getItem('boxzilla_pageviews') || 0;
    } catch (e) {
        pageViews = 0;
    }

    siteTimer = new Timer(0);
    pageTimer = new Timer(0);

    // insert styles into DOM
    var styles = require('./styles.js');
    var styleElement = document.createElement('style');
    styleElement.setAttribute("type", "text/css");
    styleElement.innerHTML = styles;
    document.head.appendChild(styleElement);

    // add overlay element to dom
    overlay = document.createElement('div');
    overlay.style.display = 'none';
    overlay.id = 'boxzilla-overlay';
    document.body.appendChild(overlay);

    // event binds
    scrollElement.addEventListener('touchstart', throttle(checkHeightCriteria), true);
    scrollElement.addEventListener('scroll', throttle(checkHeightCriteria), true);
    window.addEventListener('resize', throttle(recalculateHeights));
    window.addEventListener('load', recalculateHeights);
    overlay.addEventListener('click', onOverlayClick);
    window.setInterval(checkTimeCriteria, 1000);
    window.setTimeout(checkPageViewsCriteria, 1000);
    document.documentElement.addEventListener('mouseleave', onMouseLeave);
    document.documentElement.addEventListener('mouseenter', onMouseEnter);
    document.addEventListener('keyup', onKeyUp);

    timers.start();
    window.addEventListener('focus', timers.start);
    window.addEventListener('beforeunload', function () {
        timers.stop();
        sessionStorage.setItem('boxzilla_pageviews', ++pageViews);
    });
    window.addEventListener('blur', timers.stop);

    Boxzilla.trigger('ready');
};

/**
 * Create a new Box
 *
 * @param string id
 * @param object opts
 *
 * @returns Box
 */
Boxzilla.create = function (id, opts) {

    // preserve backwards compat for minimumScreenWidth option
    if (typeof opts.minimumScreenWidth !== "undefined") {
        opts.screenWidthCondition = {
            condition: "larger",
            value: opts.minimumScreenWidth
        };
    }

    var box = new Box(id, opts);
    boxes.push(box);
    return box;
};

Boxzilla.get = function (id) {
    for (var i = 0; i < boxes.length; i++) {
        var box = boxes[i];
        if (box.id == id) {
            return box;
        }
    }

    throw new Error("No box exists with ID " + id);
};

// dismiss a single box (or all by omitting id param)
Boxzilla.dismiss = function (id) {
    // if no id given, dismiss all current open boxes
    if (typeof id === "undefined") {
        boxes.forEach(function (box) {
            box.dismiss();
        });
    } else if (_typeof(boxes[id]) === "object") {
        Boxzilla.get(id).dismiss();
    }
};

Boxzilla.hide = function (id) {
    if (typeof id === "undefined") {
        boxes.forEach(function (box) {
            box.hide();
        });
    } else {
        Boxzilla.get(id).hide();
    }
};

Boxzilla.show = function (id) {
    if (typeof id === "undefined") {
        boxes.forEach(function (box) {
            box.show();
        });
    } else {
        Boxzilla.get(id).show();
    }
};

Boxzilla.toggle = function (id) {
    if (typeof id === "undefined") {
        boxes.forEach(function (box) {
            box.toggle();
        });
    } else {
        Boxzilla.get(id).toggle();
    }
};

// expose each individual box.
Boxzilla.boxes = boxes;

// expose boxzilla object
window.Boxzilla = Boxzilla;

if (typeof module !== 'undefined' && module.exports) {
    module.exports = Boxzilla;
}

},{"./box.js":3,"./styles.js":5,"./timer.js":6,"wolfy87-eventemitter":7}],5:[function(require,module,exports){
"use strict";

var styles = "#boxzilla-overlay{position:fixed;background:rgba(0,0,0,.65);width:100%;height:100%;left:0;top:0;z-index:99999}.boxzilla-center-container{position:fixed;top:0;left:0;right:0;height:0;text-align:center;z-index:999999;line-height:0}.boxzilla-center-container .boxzilla{display:inline-block;text-align:left;position:relative;line-height:normal}.boxzilla{position:fixed;z-index:999999;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;background:#fff;padding:25px}.boxzilla.boxzilla-top-left{top:0;left:0}.boxzilla.boxzilla-top-right{top:0;right:0}.boxzilla.boxzilla-bottom-left{bottom:0;left:0}.boxzilla.boxzilla-bottom-right{bottom:0;right:0}.boxzilla-content>:first-child{margin-top:0;padding-top:0}.boxzilla-content>:last-child{margin-bottom:0;padding-bottom:0}.boxzilla-close-icon{position:absolute;right:0;top:0;text-align:center;padding:6px;cursor:pointer;-webkit-appearance:none;font-size:28px;font-weight:700;line-height:20px;color:#000;opacity:.5}.boxzilla-close-icon:focus,.boxzilla-close-icon:hover{opacity:.8}";
module.exports = styles;

},{}],6:[function(require,module,exports){
'use strict';

var Timer = function Timer(start) {
    this.time = start;
    this.interval = 0;
};

Timer.prototype.tick = function () {
    this.time++;
};

Timer.prototype.start = function () {
    if (!this.interval) {
        this.interval = window.setInterval(this.tick.bind(this), 1000);
    }
};

Timer.prototype.stop = function () {
    if (this.interval) {
        window.clearInterval(this.interval);
        this.interval = 0;
    }
};

module.exports = Timer;

},{}],7:[function(require,module,exports){
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
        var listenersMap = this.getListenersAsObject(evt);
        var listeners;
        var listener;
        var i;
        var key;
        var response;

        for (key in listenersMap) {
            if (listenersMap.hasOwnProperty(key)) {
                listeners = listenersMap[key].slice(0);
                i = listeners.length;

                while (i--) {
                    // If the listener returns true then it shall be removed from the event
                    // The function is executed either with a basic call or an apply if there is an args array
                    listener = listeners[i];

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

},{}]},{},[1]);
; })();