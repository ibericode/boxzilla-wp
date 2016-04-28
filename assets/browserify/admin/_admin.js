module.exports = (function($) {
	'use strict';

	var optionControls = document.getElementById('boxzilla-box-options-controls');
	var $optionControls = $(optionControls);

	// sanity check, are we on the correct page?
	if( $optionControls.length === 0 ) {
		return;
	}

	// TODO: Handle this using NPM
	var EventEmitter = require('../_event-emitter.js');
	var events = new EventEmitter();
	var Option = require('./_option.js');
	var Designer = require('./_designer.js')($, Option, events);
	var rowTemplate = wp.template('rule-row-template');
	var i18n = boxzilla_i18n;
	var manualHintElement = optionControls.querySelector('.boxzilla-manual-hint');

	// events
	$optionControls.on('click', ".boxzilla-add-rule", addRuleFields);
	$optionControls.on('click', ".boxzilla-remove-rule", removeRule);
	$optionControls.on('change', ".boxzilla-rule-condition", setContextualHelpers);
	$optionControls.find('.boxzilla-auto-show-trigger').on('change', toggleTriggerOptions );

	$(window).load(function() {
		if( typeof(window.tinyMCE) === "undefined" ) {
			document.getElementById('notice-notinymce').style.display = 'block';
		}
	});

	// call contextual helper method for each row
	$('.boxzilla-rule-row').each(setContextualHelpers);

	function toggleTriggerOptions() {
		$optionControls.find('.boxzilla-trigger-options').toggle( this.value !== '' );
	}

	function removeRule() {
		$(this).parents('tr').remove();
	}

	function setContextualHelpers() {

		var context = ( this.tagName.toLowerCase() === "tr" ) ? this : $(this).parents('tr').get(0);
		var condition = context.querySelector('.boxzilla-rule-condition').value;
		var valueInput = context.querySelector('input.boxzilla-rule-value');
		var betterInput = valueInput.cloneNode(true);
		var $betterInput = $(betterInput);

		// remove previously added helpers
		$(context.querySelectorAll('.boxzilla-helper')).remove();

		// prepare better input
		betterInput.removeAttribute('name');
		betterInput.className += ' boxzilla-helper';
		valueInput.parentNode.insertBefore(betterInput, valueInput.nextSibling);
		betterInput.style.display = 'block';
		$betterInput.change(function() {
			valueInput.value = this.value; //.val(this.value);
		});

		valueInput.style.display = 'none';
		manualHintElement.style.display = 'none';

		// change placeholder for textual help
		switch(condition) {
			default:
				betterInput.placeholder = i18n.enterCommaSeparatedValues;
				break;

			case '':
			case 'everywhere':
				valueInput.value = '';
				betterInput.style.display = 'none';
				break;

			case 'is_single':
			case 'is_post':
				betterInput.placeholder = i18n.enterCommaSeparatedPosts;
				$betterInput.suggest(ajaxurl + "?action=boxzilla_autocomplete&type=post", {multiple:true, multipleSep: ","});
				break;

			case 'is_page':
				betterInput.placeholder = i18n.enterCommaSeparatedPages;
				$betterInput.suggest(ajaxurl + "?action=boxzilla_autocomplete&type=page", {multiple:true, multipleSep: ","});
				break;

			case 'is_post_type':
				betterInput.placeholder = i18n.enterCommaSeparatedPostTypes;
				$betterInput.suggest(ajaxurl + "?action=boxzilla_autocomplete&type=post_type", {multiple:true, multipleSep: ","});
				break;

			case 'is_url':
				betterInput.placeholder = i18n.enterCommaSeparatedRelativeUrls;
				break;

			case 'is_post_in_category':
				$betterInput.suggest(ajaxurl + "?action=boxzilla_autocomplete&type=category", {multiple:true, multipleSep: ","});
				break;

			case 'manual':
				betterInput.placeholder = '';
				manualHintElement.style.display = '';
				break;
		}
	}

	function addRuleFields() {
		var data = {
			'key': optionControls.querySelectorAll('.boxzilla-rule-row').length
		};
		var html = rowTemplate(data);
		$(document.getElementById('boxzilla-box-rules')).after(html);
		return false;
	}

	return {
		'Designer': Designer,
		'Option': Option,
		'events': events
	};

})(window.jQuery);