window.STB_Admin = (function($) {
	'use strict';

	var $appearanceControls = $("#stb-box-appearance-controls"),
		$optionControls = $("#stb-box-options-controls"),
		$manualTip = $optionControls.find('.stb-manual-tip');

	// events
	$optionControls.on('click', ".stb-add-rule", addRuleFields);
	$optionControls.on('click', ".stb-remove-rule", removeRule);
	$optionControls.on('change', ".stb-rule-condition", setContextualHelpers);
	$optionControls.find('.stb-auto-show-trigger').on('change', toggleTriggerOptions );
	$(window).load(function() {
		if( typeof(window.tinyMCE) === "undefined" ) {
			document.getElementById('notice-notinymce').style.display = 'block';
		}
	});

	function toggleTriggerOptions() {
		$optionControls.find('.stb-trigger-options').toggle( this.value !== '' );
	}

	function removeRule() {
		$(this).parents('tr').remove();
	}

	function setContextualHelpers() {
		var $context = $(this).parents('tr');
		var $valueInput = $context.find('.stb-rule-value');
		$manualTip.hide();

		$valueInput.show();

		// change placeholder for textual help
		switch(this.value) {
			case '':
			default:
				$valueInput.attr('placeholder', 'Leave empty to match anything or enter a comma-separated list of IDs or slugs')
			break;

			case 'everywhere':
				$valueInput.hide();
			break;

			case 'is_single':
				$valueInput.attr('placeholder', 'Leave empty to match any post or enter a comma-separated list of post IDs or slugs')
			break;

			case 'is_page':
				$valueInput.attr('placeholder', 'Leave empty to match any page or enter a comma-separated list of page IDs or slugs')
			break;

			case 'is_post_type':
				$valueInput.attr('placeholder', 'Leave empty to match any post type or enter a comma-separated list of post type names')
			break;

			case 'manual':
				$valueInput.attr('placeholder', 'Example: is_single(1, 3)');
				$manualTip.show();
			break;
		}
	}

	function addRuleFields() {
		var $row = $optionControls.find(".stb-rule-row").last();
		var $newRow = $row.clone();
		$newRow.find('th > label').text("Or");
		$newRow.insertAfter($row).find(":input").val('').each(function(){
            this.name = this.name.replace(/\[(\d+)\]/, function(str,p1){
                return '[' + (parseInt(p1, 10) + 1) + ']';
            });
        }).trigger('change');
		return false;
	}

	// functions

	var Designer = (function() {

		// vars
		var boxId = document.getElementById('post_ID').value || 0,
			$editor, $editorFrame, $pseudoElement,
			$innerEditor,
			options = {},
			manualStyleEl;

			// create Option objects
			options.borderColor = new Option('border-color');
			options.borderWidth = new Option('border-width');
			options.borderStyle = new Option('border-style');
			options.backgroundColor = new Option('background-color');
			options.width = new Option('width');
			options.color = new Option('color');
			options.manualCSS = new Option('manual-css');


		// functions
		function init() {
			// add classes to TinyMCE <html>
			$editorFrame = $("#content_ifr");
			$editor = $editorFrame.contents().find('html');
			$editor.css({
				'background': 'white'
			});

			// add content class and padding to TinyMCE <body>
			$innerEditor = $editor.find('#tinymce');
			$innerEditor.addClass('scroll-triggered-box stb stb-' + boxId);
			$innerEditor.css({
				'margin': 0,
				'background': 'white',
				'display': 'inline-block',
				'width': 'auto',
				'min-width': '240px',
				'position': 'relative'
			});
			$innerEditor.get(0).style.cssText += ';padding: 25px !important;';

			// create <style> element in <head>
			manualStyleEl = document.createElement('style');
			manualStyleEl.setAttribute('type','text/css');
			manualStyleEl.id = 'stb-manual-css';
			$(manualStyleEl).appendTo($editor.find('head'));

			applyStyles();
			$(document).trigger('editorInit.stb');
		}


		/**
		 * Applies the styles from the options to the TinyMCE Editor
		 */
		function applyStyles() {
			// add manual CSS to <head>
			manualStyleEl.innerHTML = options.manualCSS.getValue();

			// apply styles from CSS editor
			$innerEditor.css({
				'border-color': options.borderColor.getColorValue(), //getColorValue( 'borderColor', '' ),
				'border-width': options.borderWidth.getPxValue(), //getPxValue( 'borderWidth', '' ),
				'border-style': options.borderStyle.getValue(), //getValue('borderStyle', '' ),
				'background-color': options.backgroundColor.getColorValue(), //getColorValue( 'backgroundColor', ''),
				'width': options.width.getPxValue(), //getPxValue( 'width', 'auto' ),
				'color': options.color.getColorValue() // getColorValue( 'color', '' )
			});

			$(document).trigger('applyBoxStyles.stb');
		}

		function resetStyles() {
			for( var key in options ) {
				if( key.substring(0,5) === 'theme' ) {
					continue;
				}

				options[key].clear();
			}
			applyStyles();
			$(document).trigger('resetBoxStyles.stb');
		}

		// event binders
		$appearanceControls.find('input.stb-color-field').wpColorPicker({ change: applyStyles, clear: applyStyles });
		$appearanceControls.find(":input").not(".stb-color-field").change(applyStyles);

		return {
			init: init,
			resetStyles: resetStyles,
			'options': options
		};

	})();

	// Option model
	function Option( element ) {

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
	}


	return {
		'Designer': Designer,
		'Option': Option
	};

})(window.jQuery);