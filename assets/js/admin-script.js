STB = (function($) {
	// prevent jumping of body
	$("body").css('overflow-y', 'scroll');

	// make sure options are visible
	$("#stb-options .inside").show();

	// events

	$('#stb-options input.stb-color-field').wpColorPicker({ change: applyStyles, clear: applyStyles });
	$("#stb-options :input").not(".stb-color-field").change(applyStyles);
	$("#stb-options").delegate(".stb-add-rule", 'click', addRuleFields);
	$("#stb-options").delegate(".stb-remove-rule", 'click', function() { $(this).parents('tr').remove(); });
	$("#stb-options").delegate(".stb-rule-condition", 'change', setContextualHelpers);
	$("#stb_trigger").change(function() {
		$(this).parents('tr').find('input').hide().end().find('input.stb-trigger-' + $(this).val()).show();
	});

	function setContextualHelpers()
	{
		$context = $(this).parents('tr');
		$("tr.stb-manual-tip").hide();
		$valueInput = $context.find('.stb-rule-value');
		$valueInput.show();

		// change placeholder for textual help
		switch($(this).val()) {
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

			case 'is_page':
				$valueInput.attr('placeholder', 'Leave empty to match any page or enter a comma-separated list of page IDs or slugs')
			break;

			case 'is_post_type':
				$valueInput.attr('placeholder', 'Leave empty to match any post type or enter a comma-separated list of post type names')
			break;

			case 'manual':
				$valueInput.attr('placeholder', 'Example: is_single(1, 3)');
				$('tr.stb-manual-tip').show();
			break;
		}
	}

	function addRuleFields()
	{
		var $row = $("#stb-options .stb-rule-row").last();
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
	function getPxValue($el, retval) 
	{
		if($el.val()) {
			return parseInt($el.val());
		} else {
			return (retval !== undefined) ? retval + "px" : 0;
		}
	}

	function getColor($el, retval)
	{
		if($el.val().length > 0) {
			return $el.wpColorPicker('color');
		} else {
			return (retval !== undefined) ? retval : '';
		}
	}

	function applyStyles() 
	{		
		var $editor = $("#content_ifr").contents().find('html');

		$editor.css({
			'background': 'white'
		});

		$editor.find("#tinymce").css({
			'padding': '25px',
			'background-color': getColor($("#stb-background-color")),
			'border-color': getColor($("#stb-border-color")),
			'border-width': getPxValue($("#stb-border-width")),
			'border-style': 'solid',
			'display': "inline-block",
			'width': getPxValue($("#stb-width"), 'auto'),
			'color': getColor($("#stb-color")),
			'height': 'auto',
			'min-width': '200px'
		});
	}

	return { 
		onTinyMceInit: function() {
			applyStyles();
		}
	}


})(jQuery)