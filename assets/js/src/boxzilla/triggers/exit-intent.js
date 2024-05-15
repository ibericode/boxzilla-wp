module.exports = function (boxes) {
  let timeout = null
  let touchStart = {}

  function trigger () {
    document.documentElement.removeEventListener('mouseleave', onMouseLeave)
    document.documentElement.removeEventListener('mouseenter', onMouseEnter)
    document.documentElement.removeEventListener('click', clearTimeout)
    window.removeEventListener('touchstart', onTouchStart)
    window.removeEventListener('touchend', onTouchEnd)

    // show boxes with exit intent trigger
    boxes.forEach(box => {
      if (box.mayAutoShow() && box.config.trigger.method === 'exit_intent') {
        box.trigger()
      }
    })
  }

  function clearTimeout () {
    if (timeout === null) {
      return
    }

    window.clearTimeout(timeout)
    timeout = null
  }

  function onMouseEnter () {
    clearTimeout()
  }

  function getAddressBarY () {
    if (document.documentMode || /Edge\//.test(navigator.userAgent)) {
      return 5
    }

    return 0
  }

  function onMouseLeave (evt) {
    clearTimeout()

    // did mouse leave at top of window?
    // add small exception space in the top-right corner
    if (evt.clientY <= getAddressBarY() && evt.clientX < (0.8 * window.innerWidth)) {
      timeout = window.setTimeout(trigger, 600)
    }
  }

  function onTouchStart () {
    clearTimeout()
    touchStart = {
      timestamp: performance.now(),
      scrollY: window.scrollY,
      windowHeight: window.innerHeight
    }
  }

  function onTouchEnd (evt) {
    clearTimeout()

    // did address bar appear?
    if (window.innerHeight > touchStart.windowHeight) {
      return
    }

    // allow a tiny tiny margin for error, to not fire on clicks
    if ((window.scrollY + 20) > touchStart.scrollY) {
      return
    }

    if ((performance.now() - touchStart.timestamp) > 300) {
      return
    }

    if (['A', 'INPUT', 'BUTTON'].indexOf(evt.target.tagName) > -1) {
      return
    }

    timeout = window.setTimeout(trigger, 800)
  }

  window.addEventListener('touchstart', onTouchStart)
  window.addEventListener('touchend', onTouchEnd)
  document.documentElement.addEventListener('mouseenter', onMouseEnter)
  document.documentElement.addEventListener('mouseleave', onMouseLeave)
  document.documentElement.addEventListener('click', clearTimeout)
}
