(function() {
	'use strict';

	var $ = window.jQuery;
	var Option = require('./_option.js');
	var optionControls = document.getElementById('boxzilla-box-options-controls');
	var $optionControls = $(optionControls);

	// sanity check, are we on the correct page?
	if( $optionControls.length === 0 ) {
		return;
	}

	var EventEmitter = require('wolfy87-eventemitter');
	var events = new EventEmitter();
	var Designer = require('./_designer.js')($, Option, events);
	var rowTemplate = wp.template('rule-row-template');
	var i18n = boxzilla_i18n;

	// events
	$optionControls.on('click', ".boxzilla-add-rule", addRuleFields);
	$optionControls.on('click', ".boxzilla-remove-rule", removeRule);
	$optionControls.on('change', ".boxzilla-rule-condition", setContextualHelpers);
	$optionControls.find('.boxzilla-auto-show-trigger').on('change', toggleTriggerOptions );

	$(window).load(function() {
		if( typeof(window.tinyMCE) === "undefined" ) {
			document.getElementById('notice-notinymce').style.display = '';
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
		var valueInput = context.querySelector('.boxzilla-rule-value');
		var qualifierInput = context.querySelector('.boxzilla-rule-qualifier');
		var betterInput = valueInput.cloneNode(true);
		var $betterInput = $(betterInput);

		// remove previously added helpers
		$(context.querySelectorAll('.boxzilla-helper')).remove();

		// prepare better input
		betterInput.removeAttribute('name');
		betterInput.className = betterInput.className + ' boxzilla-helper';
		valueInput.parentNode.insertBefore(betterInput, valueInput.nextSibling);
		$betterInput.change(function() { valueInput.value = this.value; });

		betterInput.style.display = '';
		valueInput.style.display = 'none';
		qualifierInput.style.display = '';

		// change placeholder for textual help
		switch(condition) {
			default:
				betterInput.placeholder = i18n.enterCommaSeparatedValues;
				break;

			case '':
			case 'everywhere':
				qualifierInput.value = '1';
				valueInput.value = '';
				betterInput.style.display = 'none';
				qualifierInput.style.display = 'none';
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

			case 'is_post_with_tag':
				$betterInput.suggest(ajaxurl + "?action=boxzilla_autocomplete&type=post_tag", {multiple:true, multipleSep: ","});
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

	module.exports = {
		'Designer': Designer,
		'Option': Option,
		'events': events
	};
})();