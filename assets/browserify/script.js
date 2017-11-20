(function() {
    'use strict';

    var Boxzilla = require('boxzilla');
    var options = window.boxzilla_options;

    // expose Boxzilla object to window
    window.Boxzilla = Boxzilla;

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

            // remove <script> from box content and append them to the document body
            var scripts = box.element.querySelectorAll('script');
            handleScriptElements(scripts);

            // add box slug to box element as classname
            box.element.className = box.element.className + ' boxzilla-' + boxOpts.post.slug;

            // add custom css to box
            css(box.element, boxOpts.css);

            box.element.firstChild.firstChild.className += " first-child";
            box.element.firstChild.lastChild.className += " last-child";

            // maybe show box right away
            if( box.fits() && locationHashRefersBox(box) ) {
              window.addEventListener('load', box.show.bind(box));
            }
        }

        // set flag to prevent initialising twice
        options.inited = true;

        // trigger "done" event.
        Boxzilla.trigger('done');
    }

    function locationHashRefersBox(box) {
        if( ! window.location.hash || 0 === window.location.hash.length ) {
          return false;
        }

        var elementId = window.location.hash.substring(1);

        // only attempt on strings looking like an ID 
        var regex = /^[a-zA-Z\-\_0-9]+$/;
        if( ! regex.test(elementId) ) {
          return false;
        }

        if( elementId === box.element.id ) {
          return true;
        } else if( box.element.querySelector('#' + elementId) ) {
          return true;
        }

        return false;
    }

    function handleScriptElements(scripts) {
        let handle = function() {
            const script = document.createElement('script');

            if(this.src) {
              script.src = this.src;
            }
            script.appendChild(document.createTextNode(this.text));
            this.parentNode.removeChild(this);
            document.body.appendChild(script);

            if( scripts.length ) {
                script.addEventListener('load', handle.bind(scripts.shift()));
            }
        }

        scripts = Array.from(scripts);
        if(scripts.length) { 
            window.setTimeout(handle.bind(scripts.shift()), 1); 
        }
    }

    function openMailChimpForWordPressBox() {
        if( typeof(window.mc4wp_forms_config) !== "object" || ! window.mc4wp_forms_config.submitted_form ) {
            return;
        }

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



    window.addEventListener('load', openMailChimpForWordPressBox);
    window.setTimeout(createBoxesFromConfig, 1);
})();
