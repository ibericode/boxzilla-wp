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
		this.cookieTime 	= data.cookie;
		this.testMode 	= data.testMode;
		this.autoHide 	= data.autoHide;
		this.triggerElementSelector = data.triggerElementSelector;
		this.triggerPercentage = data.triggerPercentage;
		this.animation 	= data.animation;
		this.visible = false;

		// calculate triggerHeight
		this.triggerHeight = this.calculateTriggerHeight();
		this.enabled = 	this.isBoxEnabled();

		this.init();

	};

	Box.prototype.init = function() {
		// attach event to "close" icon inside box
		this.$element.find('.stb-close').click(this.disable.bind(this));

		// attach event to all links referring #stb-{box_id}
		$('a[href="#' + this.$element.attr('id') +'"]').click(function() { this.toggle(); return false;}.bind(this));

		// shows the box when window hash refers an element inside the box
		if(window.location.hash && window.location.hash.length > 0) {

			var hash = window.location.hash;
			var $element;

			if( hash.substring(1) === this.element.id || ( ( $element = this.$element.find( hash ) ) && $element.length > 0 ) ) {
				window.setTimeout(this.show.bind(this), 300);
			}
		}
	};

	Box.prototype.toggle = function(show) {

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

		// show box
		if( this.animation === 'fade' ) {
			this.$element.fadeToggle( 'slow' );
		} else {
			this.$element.slideToggle( 'slow' );
		}

		this.visible = show;
		return true;
	};
	Box.prototype.show = function() {
		return this.toggle(true);
	};
	Box.prototype.hide = function() {
		return this.toggle(false);
	};
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

	// is this box enabled?
	Box.prototype.isBoxEnabled = function() {

		if( isLoggedIn && this.testMode ) {
			console.log( 'Scroll Triggered Boxes: Test mode is enabled. Please disable test mode if you\'re done testing.' );
			return true;
		}

		if( this.cookieTime === 0 ) {
			return true;
		}

		var isDisabledByCookie = document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + 'stb_box_' + id + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1") === "true";
		return ( ! isDisabledByCookie );
	};

	Box.prototype.disable = function() {
		this.hide();
		this.enabled = false;
		this.setCookie();
	};

	return Box;
})();