var Designer = function($, Option, events) {

	// vars
	var boxId = document.getElementById('post_ID').value || 0,
		$editor, $editorFrame,
		$innerEditor,
		options = {},
		visualEditorInitialised = false;

	var $appearanceControls = $("#boxzilla-box-appearance-controls");

	// create Option objects
	options.borderColor = new Option('border-color');
	options.borderWidth = new Option('border-width');
	options.borderStyle = new Option('border-style');
	options.backgroundColor = new Option('background-color');
	options.width = new Option('width');
	options.color = new Option('color');

	// functions
	function init() {

		// Only run if TinyMCE has actually inited
		if( typeof( window.tinyMCE ) !== "object" || tinyMCE.get('content') === null ) {
			return;
		}

		// add classes to TinyMCE <html>
		$editorFrame = $("#content_ifr");
		$editor = $editorFrame.contents().find('html');
		$editor.css({
			'background': 'white'
		});

		// add content class and padding to TinyMCE <body>
		$innerEditor = $editor.find('#tinymce');
		$innerEditor.addClass('boxzilla boxzilla-' + boxId);
		$innerEditor.css({
			'margin': 0,
			'background': 'white',
			'display': 'inline-block',
			'width': 'auto',
			'min-width': '240px',
			'position': 'relative'
		});
		$innerEditor.get(0).style.cssText += ';padding: 25px !important;';

		visualEditorInitialised = true;

		/* @since 2.0.3 */
		events.trigger('editor.init');
	}

	/**
	 * Applies the styles from the options to the TinyMCE Editor
	 *
	 * @return bool
	 */
	function applyStyles() {

		if( ! visualEditorInitialised ) {
			return false;
		}

		// apply styles from CSS editor
		$innerEditor.css({
			'border-color': options.borderColor.getColorValue(), //getColorValue( 'borderColor', '' ),
			'border-width': options.borderWidth.getPxValue(), //getPxValue( 'borderWidth', '' ),
			'border-style': options.borderStyle.getValue(), //getValue('borderStyle', '' ),
			'background-color': options.backgroundColor.getColorValue(), //getColorValue( 'backgroundColor', ''),
			'width': options.width.getPxValue(), //getPxValue( 'width', 'auto' ),
			'color': options.color.getColorValue() // getColorValue( 'color', '' )
		});

		/* @since 2.0.3 */
		events.trigger('editor.styles.apply');

		return true;
	}

	function resetStyles() {
		for( var key in options ) {
			if( key.substring(0,5) === 'theme' ) {
				continue;
			}

			options[key].clear();
		}
		applyStyles();

		/* @since 2.0.3 */
		events.trigger('editor.styles.reset');
	}

	// event binders
	$appearanceControls.find('input.boxzilla-color-field').wpColorPicker({ change: applyStyles, clear: applyStyles });
	$appearanceControls.find(":input").not(".boxzilla-color-field").change(applyStyles);
	events.on('editor.init', applyStyles);

	// public methods
	return {
		'init': init,
		'resetStyles': resetStyles,
		'options': options
	};

};

module.exports = Designer;