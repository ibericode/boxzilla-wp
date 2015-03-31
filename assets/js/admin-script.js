window.STBAdmin = (function($) {
	'use strict';

	var $appearanceControls = $("#stb-box-appearance"),
		$optionControls = $("#stb-box-options"),
		$manualTip = $optionControls.find('.stb-manual-tip');

	// events
	$optionControls.on('click', ".stb-add-rule", addRuleFields);
	$optionControls.on('click', ".stb-remove-rule", removeRule);
	$optionControls.on('change', ".stb-rule-condition", setContextualHelpers);
	$("#stb_trigger").change(function() {
		$(this).parents('tr').find('input').hide().end().find('input.stb-trigger-' + $(this).val()).show();
	});

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

	// Designer
	var Designer = {};
	Designer.init = function() {

		var boxID = document.getElementById('post_ID').value || 0;

		// cache tinymce elements
		Designer.$editor = $("#content_ifr").contents().find('html');
		Designer.$innerEditor = Designer.$editor.find('#tinymce');

		// make sure we're showing on a white background
		Designer.$editor.css({
			'background': 'white'
		});

		// add global class
		Designer.$innerEditor.addClass('stb').addClass('scroll-triggered-box').addClass('stb-' + boxID);

		// add padding
		Designer.$innerEditor.get(0).style.cssText += ';padding: 25px !important;';

		Designer.$innerEditor.css({
			'display': "inline-block",
			'height': 'auto',
			'min-width': '200px'
		});

		// create <style> element in <head>
		Designer.styleEl = document.createElement('style');
		Designer.styleEl.id = 'stb-manual-css';
		$(Designer.styleEl).appendTo(Designer.$editor.find('head'));

		// apply styles
		Designer.applyStyles();
		$(document).trigger('editorInit.stb');
	};
	Designer.fields = {
		borderColor: document.getElementById('stb-border-color'),
		borderWidth: document.getElementById('stb-border-width'),
		borderStyle: document.getElementById('stb-border-style'),
		backgroundColor: document.getElementById('stb-background-color'),
		width: document.getElementById('stb-width'),
		color: document.getElementById('stb-color'),
		manualCSS: document.getElementById('stb-manual-css')
	};
	Designer.getColor = function( field, fallbackValue ) {
		if( Designer.fields[field].value.length > 0 ) {
			return $(Designer.fields[field]).wpColorPicker('color');
		}

		return (typeof(fallbackValue) !== "undefined") ? fallbackValue : '';
	};
	Designer.getPxValue = function( field, fallbackValue ) {
		if( Designer.fields[field].value.length > 0 ) {
			return parseInt( Designer.fields[field].value ) + "px";
		}

		return (typeof(fallbackValue) !== "undefined") ? fallbackValue : 0;
	};
	Designer.getValue = function( field, fallbackValue ) {
		if( Designer.fields[field].value.length > 0 ) {
			return Designer.fields[field].value;
		}

		return (typeof(fallbackValue) !== "undefined") ? fallbackValue : '';
	};
	Designer.applyStyles = function() {

		// add manual CSS to <head>
		Designer.styleEl.innerHTML = Designer.fields['manualCSS'].value;


		Designer.$innerEditor.css({
			'border-color': Designer.getColor( 'borderColor', 'initial' ),
			'border-width': Designer.getPxValue( 'borderWidth', '' ),
			'border-style': Designer.getValue( 'borderStyle', '' ),
			'background-color': Designer.getColor( 'backgroundColor', 'inherit '),
			'width': Designer.getPxValue( 'width', 'auto' ),
			'color': Designer.getColor( 'color', 'inherit ')
		});

		$(document).trigger('applyBoxStyles.stb');
	};

	// event binders
	$appearanceControls.find('input.stb-color-field').wpColorPicker({ change: Designer.applyStyles, clear: Designer.applyStyles });
	$appearanceControls.find(":input").not(".stb-color-field").change(Designer.applyStyles);


	return {
		onTinyMceInit: Designer.init
	}


})(window.jQuery);