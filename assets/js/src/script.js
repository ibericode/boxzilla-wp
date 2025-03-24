(function () {
  const Boxzilla = require('./boxzilla/boxzilla.js')
  const options = window.boxzilla_options

  // helper function for setting CSS styles
  function css (element, styles) {
    if (styles.background_color) {
      element.style.background = styles.background_color
    }

    if (styles.color) {
      element.style.color = styles.color
    }

    if (styles.border_color) {
      element.style.borderColor = styles.border_color
    }

    if (styles.border_width) {
      element.style.borderWidth = parseInt(styles.border_width) + 'px'
    }

    if (styles.border_style) {
      element.style.borderStyle = styles.border_style
    }

    if (styles.width) {
      element.style.maxWidth = parseInt(styles.width) + 'px'
    }
  }

  function createBoxesFromConfig () {
    // failsafe against including script twice.
    if (options.inited) {
      return
    }

    // create boxes from options
    for (var key in options.boxes) {
      // get opts
      var boxOpts = options.boxes[key]
      boxOpts.testMode = isLoggedIn && options.testMode

      // find box content element, bail if not found
      var boxContentElement = document.getElementById('boxzilla-box-' + boxOpts.id + '-content')
      if (!boxContentElement) {
        continue
      }

      // use element as content option
      boxOpts.content = boxContentElement

      // create box
      var box = Boxzilla.create(boxOpts.id, boxOpts)

      // add box slug to box element as classname
      box.element.className = box.element.className + ' boxzilla-' + boxOpts.post.slug

      // add custom css to box
      css(box.element, boxOpts.css)

      try {
        box.element.firstChild.firstChild.className += ' first-child'
        box.element.firstChild.lastChild.className += ' last-child'
      } catch (e) {}

      // maybe show box right away
      if (box.fits() && locationHashRefersBox(box)) {
        box.show()
      }
    }

    // set flag to prevent initialising twice
    options.inited = true

    // trigger "done" event.
    Boxzilla.trigger('done')

    // maybe open box with MC4WP form in it
    maybeOpenMailChimpForWordPressBox()
  }

  function locationHashRefersBox (box) {
    if (!window.location.hash || window.location.hash.length === 0) {
      return false
    }

    // parse "boxzilla-{id}" from location hash
    const match = window.location.hash.match(/[#&](boxzilla-\d+)/)
    if (!match || typeof (match) !== 'object' || match.length < 2) {
      return false
    }

    const elementId = match[1]
    if (elementId === box.element.id) {
      return true
    } else if (box.element.querySelector('#' + elementId)) {
      return true
    }

    return false
  }

  function maybeOpenMailChimpForWordPressBox () {
    if ((typeof (window.mc4wp_forms_config) !== 'object' || !window.mc4wp_forms_config.submitted_form) &&
            (typeof (window.mc4wp_submitted_form) !== 'object')) {
      return
    }

    const form = window.mc4wp_submitted_form || window.mc4wp_forms_config.submitted_form
    const selector = '#' + form.element_id
    Boxzilla.boxes.forEach(box => {
      if (box.element.querySelector(selector)) {
        box.show()
      }
    })
  }

  // print message when test mode is enabled
  const isLoggedIn = document.body && document.body.className && document.body.className.indexOf('logged-in') > -1
  if (isLoggedIn && options.testMode) {
    console.log('Boxzilla: Test mode is enabled. Please disable test mode if you\'re done testing.')
  }

  // init boxzilla
  Boxzilla.init()

  document.addEventListener('DOMContentLoaded', () => {
    // create JS objects for each box
    createBoxesFromConfig()

    // fire all events queued up during DOM load
    window.boxzilla_queue.forEach((q) => {
      const [method, args] = q
      Boxzilla[method].apply(null, args)
    })
  })
})()
