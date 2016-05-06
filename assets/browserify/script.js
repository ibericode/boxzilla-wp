'use strict';

var Boxzilla = require('boxzilla');
var options = window.boxzilla_options;
var isLoggedIn = document.body.className.indexOf('logged-in') > -1;

// print message when test mode is enabled
if( isLoggedIn && options.testMode ) {
    console.log( 'Boxzilla: Test mode is enabled. Please disable test mode if you\'re done testing.' );
}

// init boxzilla
Boxzilla.init();

// create boxes from options
for( var i=0; i < options.boxes.length; i++ ) {
    // get opts
    var boxOpts = options.boxes[i];
    boxOpts.testMode = isLoggedIn && options.testMode;

    // create box
    var box = Boxzilla.create( boxOpts.id, boxOpts);
    
    // add custom css to box
    css(box.element, boxOpts.css);
}

function css(element, styles) {
    if( styles.background_color ) {
        element.style.background = styles.background_color;
    }

    if( styles.color ) {
        element.style.color = styles.color;
    }

    if( styles.border_color ) {
        element.style.borderColor = styles.border_color;
    }

    if( styles.border_width ) {
        element.style.borderWidth = parseInt(styles.border_width) + "px";
    }

    if( styles.border_style ) {
        element.style.borderStyle = styles.border_style;
    }

    if( styles.width ) {
        element.style.maxWidth = parseInt(styles.width) + "px";
    }
}

window.Boxzilla = Boxzilla;