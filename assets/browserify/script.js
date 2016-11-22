(function() {
    'use strict';

    var Boxzilla = require('boxzilla');
    var options = window.boxzilla_options;

    // expose Boxzilla object to window
    window.Boxzilla = Boxzilla;

    function ready(fn) {
        if (document.readyState != 'loading'){
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    }

    // helper function for setting CSS styles
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

    function createBoxesFromConfig() {
        var isLoggedIn = document.body.className.indexOf('logged-in') > -1;

        // failsafe against including script twice.
        if( options.inited ) {
            return;
        }

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

            // fix http:// links in box content....
            if( window.location.protocol === "https:" && window.location.host ) {
                var o = "http://" + window.location.host;
                var n = o.replace('http://', 'https://');
                boxOpts.content = boxOpts.content.replace(o, n);
            }

            // create box
            var box = Boxzilla.create(boxOpts.id, boxOpts);

            // add box slug to box element as classname
            box.element.className = box.element.className + ' boxzilla-' + boxOpts.post.slug;

            // add custom css to box
            css(box.element, boxOpts.css);

            box.element.firstChild.firstChild.className += " first-child";
            box.element.firstChild.lastChild.className += " last-child";
        }

        /**
         * If a MailChimp for WordPress form was submitted, open the box containing that form (if any)
         *
         * TODO: Just set location hash from MailChimp for WP?
         */
        window.addEventListener('load', openMailChimpForWordPressBox);

        options.inited = true;

        // trigger "done" event.
        Boxzilla.trigger('done');
    }

    function openMailChimpForWordPressBox() {
        if( typeof(window.mc4wp_forms_config) === "object" && window.mc4wp_forms_config.submitted_form ) {
            var selector = '#' + window.mc4wp_forms_config.submitted_form.element_id;
            var boxes = Boxzilla.boxes;
            for( var boxId in boxes ) {
                if(!boxes.hasOwnProperty(boxId)) { continue; }
                var box = boxes[boxId];
                if( box.element.querySelector(selector)) {
                    box.show();
                    return;
                }
            }
        }
    }

    // create boxes as soon as document.ready fires
    ready(createBoxesFromConfig);
})();