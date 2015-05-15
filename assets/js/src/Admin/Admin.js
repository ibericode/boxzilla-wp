module.exports = (function($) {
	'use strict';

	var $optionControls = $("#stb-box-options-controls");
	var $manualTip = $optionControls.find('.stb-manual-tip');
	var EventEmitter = require('../EventEmitter.js');
	var events = new EventEmitter();
	var Option = require('./Option.js');
	var Designer = require('./Designer.js')($, Option, events);

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
				$valueInput.attr('placeholder', 'Leave empty to match anything or enter a comma-separated list of IDs or slugs');
				break;

			case 'everywhere':
				$valueInput.hide();
				break;

			case 'is_single':
				$valueInput.attr('placeholder', 'Leave empty to match any post or enter a comma-separated list of post IDs or slugs');
				break;

			case 'is_page':
				$valueInput.attr('placeholder', 'Leave empty to match any page or enter a comma-separated list of page IDs or slugs');
				break;

			case 'is_post_type':
				$valueInput.attr('placeholder', 'Leave empty to match any post type or enter a comma-separated list of post type names');
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
		$newRow.insertAfter($row).find(":input").val('').each(function () {
			this.name = this.name.replace(/\[(\d+)\]/, function (str, p1) {
				return '[' + (parseInt(p1, 10) + 1) + ']';
			});
		}).trigger('change');
		return false;
	}

	return {
		'Designer': Designer,
		'Option': Option,
		'events': events
	};

})(window.jQuery);