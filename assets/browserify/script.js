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

for( var i=0; i < options.boxes.length; i++ ) {
    var boxOpts = options.boxes[i];
    boxOpts.testMode = isLoggedIn && options.testMode;
    Boxzilla.create( boxOpts.id, boxOpts);
}
// // init on document.ready OR in 5 seconds in case event pipeline is broken
// $(document).ready(init);
// window.setTimeout(init, 5000);

Boxzilla.create( 'custom-box', {
    content: "Well hello",
    trigger: "percentage",
    triggerPercentage: 50,
    position: "top-right"
});

window.Boxzilla = Boxzilla;