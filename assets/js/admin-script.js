window.STBAdmin = (function($) {
	'use strict';

	var $context = $("#stb-options"),
		$manualTip = $context.find('.stb-manual-tip'),
		$editor, $innerEditor;

	// prevent jumping of body
	$(document.body).css('overflow-y', 'scroll');

	// make sure options are visible
	$context.find(".inside").show();

	// events
	$context.find('input.stb-color-field').wpColorPicker({ change: applyStyles, clear: applyStyles });
	$context.find(":input").not(".stb-color-field").change(applyStyles);
	$context.on('click', ".stb-add-rule", addRuleFields);
	$context.on('click', ".stb-remove-rule", removeRule);
	$context.on('change', ".stb-rule-condition", setContextualHelpers);
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
		var $row = $context.find(".stb-rule-row").last();
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
	function getPxValue($el, retval) {
		if($el.val()) {
			return parseInt($el.val());
		} else {
			return (retval !== undefined) ? retval + "px" : 0;
		}
	}

	function getColor($el, retval) {
		if($el.val().length > 0) {
			return $el.wpColorPicker('color');
		} else {
			return (retval !== undefined) ? retval : '';
		}
	}

	function initTinyMCE() {
		// find tinymce elements
		$editor = $("#content_ifr").contents().find('html');
		$innerEditor = $editor.find('#tinymce');

		// make sure we're showing on a white background
		$editor.css({
			'background': 'white'
		});

		// add global class
		$innerEditor.addClass('stb').addClass('scroll-triggered-box');

		// add padding
		$innerEditor.get(0).style.cssText += ';padding: 25px !important;';

		$innerEditor.css({
			'border-style': 'solid',
			'display': "inline-block",
			'height': 'auto',
			'min-width': '200px'
		});

		applyStyles();

		$(document).trigger('editorInit.stb');
	}

	function applyStyles() {
		$innerEditor.css({
			'background-color': getColor($("#stb-background-color")),
			'border-color': getColor($("#stb-border-color")),
			'border-width': getPxValue($("#stb-border-width")),
			'width': getPxValue($("#stb-width"), 'auto'),
			'color': getColor($("#stb-color"))
		});

		// remove top & bottom margin of first and last child
		$innerEditor.children().first().css({
			'margin-top': 0
		});
		$innerEditor.children().last().css({
			'margin-bottom': 0
		});
	}

	return {
		onTinyMceInit: initTinyMCE
	}


})(window.jQuery);