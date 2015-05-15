var Option = function( element ) {

	var $ = window.jQuery;

	// find corresponding element
	if( typeof(element) == "string" ) {
		element = document.getElementById('stb-' + element);
	}
	this._element = element;

	// helper methods
	this.getColorValue = function() {
		if( this._element.value.length > 0 ) {
			if( $(this._element).hasClass('wp-color-field')) {
				return $(this._element).wpColorPicker('color');
			} else {
				return this._element.value;
			}
		}

		return '';
	};

	this.getPxValue = function( fallbackValue ) {
		if( this._element.value.length > 0 ) {
			return parseInt( this._element.value ) + "px";
		}

		return fallbackValue || '';
	};

	this.getValue = function( fallbackValue ) {

		if( this._element.value.length > 0 ) {
			return this._element.value;
		}

		return fallbackValue || '';
	};

	this.clear = function() {
		this._element.value = '';
	};

	this.setValue = function(value) {
		this._element.value = value;
	};
};

module.exports = Option;