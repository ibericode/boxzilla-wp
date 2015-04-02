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
		var boxID = document.getElementById('post_ID').value || 0,
			$editor,
			$innerEditor,
			optionElements = {
				borderColor: document.getElementById('stb-border-color'),
				borderWidth: document.getElementById('stb-border-width'),
				borderStyle: document.getElementById('stb-border-style'),
				backgroundColor: document.getElementById('stb-background-color'),
				width: document.getElementById('stb-width'),
				color: document.getElementById('stb-color'),
				manualCSS: document.getElementById('stb-manual-css')
			},
			manualStyleEl;

		// functions
		function init() {
			$editor = $("#content_ifr").contents().find('html');
			$innerEditor = $editor.find('#tinymce');

			// make sure we're showing on a white background
			$editor.css({
				'background': 'white'
			});

			// add global class
			$innerEditor.addClass('stb scroll-triggered-box stb-content stb-' + boxID);

			// add padding
			$innerEditor.get(0).style.cssText += ';padding: 25px !important;';

			$innerEditor.css({
				'display': "inline-block",
				'height': 'auto',
				'min-width': '200px'
			});

			// create <style> element in <head>
			manualStyleEl = document.createElement('style');
			manualStyleEl.setAttribute('type','text/css');
			manualStyleEl.id = 'stb-manual-css';
			$(manualStyleEl).appendTo($editor.find('head'));

			applyStyles();
			$(document).trigger('editorInit.stb');
		}

		function getColorValue(option,fallbackValue) {
			if( optionElements[option].value.length > 0 ) {
				return $(optionElements[option]).wpColorPicker('color');
			}

			return (typeof(fallbackValue) !== "undefined") ? fallbackValue : '';
		}

		function getPxValue(option, fallbackValue) {
			if( optionElements[option].value.length > 0 ) {
				return parseInt( optionElements[option].value ) + "px";
			}

			return (typeof(fallbackValue) !== "undefined") ? fallbackValue : 0;
		}

		function getValue( option ) {
			if( optionElements[option].value.length > 0 ) {
				return optionElements[option].value;
			}

			return (typeof(fallbackValue) !== "undefined") ? fallbackValue : '';
		}

		/**
		 * Applies the styles from the options to the TinyMCE Editor
		 */
		function applyStyles() {
			// add manual CSS to <head>
			manualStyleEl.innerHTML = optionElements.manualCSS.value;

			// apply styles from CSS editor
			$innerEditor.css({
				'border-color': getColorValue( 'borderColor', '' ),
				'border-width': getPxValue( 'borderWidth', '' ),
				'border-style': getValue( 'borderStyle', '' ),
				'background-color': getColorValue( 'backgroundColor', ''),
				'width': getPxValue( 'width', 'auto' ),
				'color': getColorValue( 'color', '' )
			});

			$(document).trigger('applyBoxStyles.stb');
		}

		function resetStyles() {
			optionElements.borderColor.value = '';
			optionElements.borderStyle.value = '';
			optionElements.borderWidth.value = '';
			optionElements.backgroundColor.value = '';
			optionElements.color.value = '';
			optionElements.manualCSS.value = '';
			applyStyles();

			$(document).trigger('resetBoxStyles.stb');
		}

		// init

		// event binders
		$appearanceControls.find('input.stb-color-field').wpColorPicker({ change: applyStyles, clear: applyStyles });
		$appearanceControls.find(":input").not(".stb-color-field").change(applyStyles);

		return {
			init: init,
			resetStyles: resetStyles
		};

	})();

	return {
		'Designer': Designer
	}




})(window.jQuery);