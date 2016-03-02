module.exports = (function($) {
	'use strict';

	var optionControls = document.getElementById('stb-box-options-controls');
	var $optionControls = $(optionControls);

	// sanity check, are we on the correct page?
	if( $optionControls.length === 0 ) {
		return;
	}

	var $manualTip = $optionControls.find('.stb-manual-tip');
	var EventEmitter = require('../_event-emitter.js');
	var events = new EventEmitter();
	var Option = require('./_option.js');
	var Designer = require('./_designer.js')($, Option, events);
	var rowTemplate = wp.template('rule-row-template');
	var i18n = stb_i18n;

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

	// call contextual helper method for each row
	$('.stb-rule-row').each(setContextualHelpers);

	function toggleTriggerOptions() {
		$optionControls.find('.stb-trigger-options').toggle( this.value !== '' );
	}

	function removeRule() {
		$(this).parents('tr').remove();
	}

	function setContextualHelpers() {

		var context = ( this.tagName.toLowerCase() === "tr" ) ? this : $(this).parents('tr').get(0);
		var condition = context.querySelector('.stb-rule-condition').value;
		var valueInput = context.querySelector('input.stb-rule-value');
		var betterInput = valueInput.cloneNode(true);
		var $betterInput = $(betterInput);

		// remove previously added helpers
		$(context.querySelectorAll('.stb-helper')).remove();

		// prepare better input
		betterInput.removeAttribute('name');
		betterInput.className += ' stb-helper';
		valueInput.parentNode.insertBefore(betterInput, valueInput.nextSibling);
		betterInput.style.display = 'block';
		$betterInput.change(function() {
			valueInput.value = this.value; //.val(this.value);
		});

		valueInput.style.display = 'none';
		$manualTip.hide();

		// change placeholder for textual help
		switch(condition) {
			default:
				$betterInput.attr('placeholder', i18n.enterCommaSeparatedValues);
				break;

			case '':
			case 'everywhere':
				valueInput.value = '';
				$betterInput.hide();
				break;

			case 'is_single':
			case 'is_post':
				$betterInput.attr('placeholder', i18n.enterCommaSeparatedPosts);
				$betterInput.suggest(ajaxurl + "?action=stb_autocomplete&type=post", {multiple:true, multipleSep: ","});
				break;

			case 'is_page':
				$betterInput.attr('placeholder', i18n.enterCommaSeparatedPages);
				$betterInput.suggest(ajaxurl + "?action=stb_autocomplete&type=page", {multiple:true, multipleSep: ","});
				break;

			case 'is_post_type':
				$betterInput.attr('placeholder', i18n.enterCommaSeparatedPostTypes );
				$betterInput.suggest(ajaxurl + "?action=stb_autocomplete&type=post_type", {multiple:true, multipleSep: ","});
				break;

			case 'is_url':
				$betterInput.attr('placeholder', i18n.enterCommaSeparatedRelativeUrls);
				break;

			case 'is_post_in_category':
				$betterInput.suggest(ajaxurl + "?action=stb_autocomplete&type=category", {multiple:true, multipleSep: ","});
				break;

			case 'manual':
				$betterInput.attr('placeholder', '');
				$manualTip.show();
				break;
		}
	}

	function addRuleFields() {
		var data = {
			'key': optionControls.querySelectorAll('.stb-rule-row').length
		};
		var html = rowTemplate(data);
		$(document.getElementById('stb-box-rules')).after(html);
		return false;
	}

	return {
		'Designer': Designer,
		'Option': Option,
		'events': events
	};

})(window.jQuery);