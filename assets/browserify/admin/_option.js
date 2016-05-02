'use strict';

var $ = window.jQuery;

var Option = function( element ) {

	// find corresponding element
	if( typeof(element) == "string" ) {
		element = document.getElementById('boxzilla-' + element);
	}

	if( ! element ) {
		console.error("Unable to find option element.");
	}

	this.element = element;
};

Option.prototype.getColorValue = function() {
	if( this.element.value.length > 0 ) {
		if( $(this.element).hasClass('wp-color-field')) {
			return $(this.element).wpColorPicker('color');
		} else {
			return this.element.value;
		}
	}

	return '';
};

Option.prototype.getPxValue = function( fallbackValue ) {
	if( this.element.value.length > 0 ) {
		return parseInt( this.element.value ) + "px";
	}

	return fallbackValue || '';
};

Option.prototype.getValue = function( fallbackValue ) {

	if( this.element.value.length > 0 ) {
		return this.element.value;
	}

	return fallbackValue || '';
};

Option.prototype.clear = function() {
	this.element.value = '';
};

Option.prototype.setValue = function(value) {
	this.element.value = value;
};

module.exports = Option;