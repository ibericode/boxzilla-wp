jQuery(window).load(function() {

	window.STB = (function($) {

		var windowHeight = $(window).height(),
			isLoggedIn = $("body").hasClass('logged-in'),
			$boxes = [],
			console = window.console || { log: function() { } };

		// remove top and bottom margin from all boxes
		$(".stb-content").children().first().css({
			"margin-top": 0,
			"padding-top": 0
		}).end().last().css({
			'margin-bottom': 0,
			'padding-bottom': 0
		});

		function toggleBox( id, show ) {

			var $box = $boxes[id];

			if( $box === undefined ) {
				console.log( "Scroll Triggered Boxes: Box #" + id + " is not present in the current page." );
				return;
			}

			// don't do anything if box is undergoing an animation
			if( $box.is( ":animated" ) ) {
				return false;
			}

			// is box already at desired visibility?
			if( ( show === true && $box.is( ":visible" ) ) || ( show === false && $box.is( ":hidden" ) ) ) {
				return false;
			}

			// show box
			var animation = $box.data('animation');

			if( animation === 'fade' ) {
				$box.fadeToggle( 'slow' );
			} else {
				$box.slideToggle( 'slow' );
			}

			return show;
		}

		// loop through boxes
		$(".scroll-triggered-box").each(function() {

			// vars
			var $box = $(this);
			var triggerMethod = $box.data('trigger');
			var animation = $box.data('animation');
			var timer = 0;
			var testMode = (parseInt($box.data('test-mode')) === 1);
			var id = $box.data('box-id');
			var autoHide = (parseInt($box.data('auto-hide')) === 1);
			var boxCookieTime = parseInt( $box.data('cookie') );

			// add box to global boxes array
			$boxes[id] = $box;


			if(triggerMethod == 'element') {
				var selector = $box.data('trigger-element');
				var $triggerElement = $(selector);

				// can't find trigger element, abandon.
				if( $triggerElement.length == 0 ) {
					console.info( 'Scroll Triggered Boxes: Can\'t find element "'+ selector +'". Not showing box.' );
					return;
				}

				var triggerHeight = $triggerElement.offset().top;
			} else {
				var triggerPercentage = ( triggerMethod == 'percentage' ) ? ( parseInt( $box.data('trigger-percentage'), 10 ) / 100 ) : 0.8;
				var triggerHeight = ( triggerPercentage * $(document).height() );
			}

			// functions
			var checkBoxCriteria = function() {
				if (timer) {
					clearTimeout(timer);
				}

				timer = window.setTimeout(function () {
					var scrollY = $(window).scrollTop();
					var triggered = ((scrollY + windowHeight) >= triggerHeight);

					// show box when criteria for this box is matched
					if (triggered) {

						// remove listen event if box shouldn't be hidden again
						if (!autoHide) {
							$(window).unbind('scroll', checkBoxCriteria);
						}

						toggleBox(id, true);
					} else {
						toggleBox(id, false);
					}

				}, 100);
			};

			// show box if cookie not set or if in test mode
			var cookieSet = document.cookie.replace(new RegExp("(?:(?:^|.*;)\\s*" + 'stb_box_' + id + "\\s*\\=\\s*([^;]*).*$)|^.*$"), "$1") === "true";
			var addBoxListener = ( cookieSet === false || boxCookieTime === 0 );

			if ( true === isLoggedIn && true === testMode ) {
				addBoxListener = true;
				console.log( 'Scroll Triggered Boxes: Test mode is enabled. Please disable test mode if you\'re done testing.' );
			}

			if( addBoxListener ) {
				$(window).bind( 'scroll', checkBoxCriteria );

				// init, check box criteria once
				checkBoxCriteria();

				// shows the box when hash refers an element inside the box
				if(window.location.hash && window.location.hash.length > 0) {

					var hash = window.location.hash;
					var $element;

					if( hash.substring(1) === $box.attr( 'id' ) || ( ( $element = $box.find( hash ) ) && $element.length > 0 ) ) {
						setTimeout(function() {
							toggleBox( id, true );
						}, 100);
					}
				}
			}		

			$box.find(".stb-close").click(function() {

				// hide box
				toggleBox( id, false );

				// unbind 
				$(window).unbind( 'scroll', checkBoxCriteria );

				// set cookie
				if(boxCookieTime > 0) {
					var expiryDate = new Date();
					expiryDate.setDate( expiryDate.getDate() + boxCookieTime );
					document.cookie = 'stb_box_'+ id + '=true; expires='+ expiryDate.toUTCString() +'; path=/';
				}
				
			});
			
			// add link listener for this box
			$('a[href="#' + $box.attr('id') +'"]').click(function() { toggleBox(id, true); return false; });

		});

	return {
		show: function( box_id ) {
			return toggleBox( box_id, true );
		},
		hide: function( box_id ) {
			return toggleBox( box_id, false );
		}
	}

	})(window.jQuery);

});

