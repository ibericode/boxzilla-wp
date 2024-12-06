const defaults = {
  animation: 'fade',
  rehide: false,
  content: '',
  cookie: null,
  icon: '&times',
  screenWidthCondition: null,
  position: 'center',
  testMode: false,
  trigger: false,
  closable: true
}

const Animator = require('./animator.js')

/**
 * Merge 2 objects, values of the latter overwriting the former.
 *
 * @param obj1
 * @param obj2
 * @returns {*}
 */
function merge (obj1, obj2) {
  const obj3 = {}

  for (const attrname of Object.keys(obj1)) {
    obj3[attrname] = obj1[attrname]
  }

  for (const attrname of Object.keys(obj2)) {
    obj3[attrname] = obj2[attrname]
  }
  return obj3
}

/**
 * Get the real height of entire document.
 * @returns {number}
 */
function getDocumentHeight () {
  const body = document.body
  const html = document.documentElement
  return Math.max(body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight)
}

// Box Object
function Box (id, config, fireEvent) {
  this.id = id
  this.fireEvent = fireEvent

  // store config values
  this.config = merge(defaults, config)

  // add overlay element to dom and store ref to overlay
  this.overlay = document.createElement('div')
  this.overlay.setAttribute('aria-modal', true)
  this.overlay.style.display = 'none'
  this.overlay.id = 'boxzilla-overlay-' + this.id
  this.overlay.classList.add('boxzilla-overlay')
  document.body.appendChild(this.overlay)

  // state
  this.visible = false
  this.dismissed = false
  this.triggered = false
  this.triggerHeight = this.calculateTriggerHeight()
  this.cookieSet = this.isCookieSet()
  this.element = null
  this.contentElement = null
  this.closeIcon = null

  // create dom elements for this box
  this.dom()

  // further initialise the box
  this.events()
}

// initialise the box
Box.prototype.events = function () {
  const box = this

  // attach event to "close" icon inside box
  if (this.closeIcon) {
    this.closeIcon.addEventListener('click', (evt) => {
      evt.preventDefault()
      box.dismiss()
    })
  }

  this.element.addEventListener('click', (evt) => {
    if (evt.target.tagName === 'A' || evt.target.tagName === 'AREA') {
      box.fireEvent('box.interactions.link', [box, evt.target])
    }
  }, false)

  this.element.addEventListener('submit', (evt) => {
    box.setCookie()
    box.fireEvent('box.interactions.form', [box, evt.target])
  }, false)

  this.overlay.addEventListener('click', (evt) => {
    const x = evt.offsetX
    const y = evt.offsetY

    // calculate if click was less than 40px outside box to avoid closing it by accident
    const rect = box.element.getBoundingClientRect()
    const margin = 40

    // if click was not anywhere near box, dismiss it.
    if (x < (rect.left - margin) ||
      x > (rect.right + margin) ||
      y < (rect.top - margin) ||
      y > (rect.bottom + margin)) {
      box.dismiss()
    }
  })
}

// generate dom elements for this box
Box.prototype.dom = function () {
  const wrapper = document.createElement('div')
  wrapper.className = 'boxzilla-container boxzilla-' + this.config.position + '-container'

  const box = document.createElement('div')
  box.id = 'boxzilla-' + this.id
  box.className = 'boxzilla boxzilla-' + this.id + ' boxzilla-' + this.config.position
  box.style.display = 'none'
  wrapper.appendChild(box)

  let content
  if (typeof (this.config.content) === 'string') {
    content = document.createElement('div')
    content.innerHTML = this.config.content
  } else {
    content = this.config.content

    // make sure element is visible
    content.style.display = ''
  }
  content.className = 'boxzilla-content'
  box.appendChild(content)

  if (this.config.closable && this.config.icon) {
    const closeIcon = document.createElement('span')
    closeIcon.className = 'boxzilla-close-icon'
    closeIcon.innerHTML = this.config.icon
    closeIcon.setAttribute('aria-label', 'close')
    box.appendChild(closeIcon)
    this.closeIcon = closeIcon
  }

  document.body.appendChild(wrapper)
  this.contentElement = content
  this.element = box
}

// set (calculate) custom box styling depending on box options
Box.prototype.setCustomBoxStyling = function () {
  // reset element to its initial state
  const origDisplay = this.element.style.display
  this.element.style.display = ''
  this.element.style.overflowY = ''
  this.element.style.maxHeight = ''

  // get new dimensions
  const windowHeight = window.innerHeight
  const boxHeight = this.element.clientHeight

  // add scrollbar to box and limit height
  if (boxHeight > windowHeight) {
    this.element.style.maxHeight = windowHeight + 'px'
    this.element.style.overflowY = 'scroll'
  }

  // set new top margin for boxes which are centered
  if (this.config.position === 'center') {
    let newTopMargin = ((windowHeight - boxHeight) / 2)
    newTopMargin = newTopMargin >= 0 ? newTopMargin : 0
    this.element.style.marginTop = newTopMargin + 'px'
  }

  this.element.style.display = origDisplay
}

// toggle visibility of the box
Box.prototype.toggle = function (show, animate) {
  show = typeof (show) === 'undefined' ? !this.visible : show
  animate = typeof (animate) === 'undefined' ? true : animate

  // is box already at desired visibility?
  if (show === this.visible) {
    return false
  }

  // is box being animated?
  if (Animator.animated(this.element)) {
    return false
  }

  // if box should be hidden but is not closable, bail.
  if (!show && !this.config.closable) {
    return false
  }

  // set new visibility status
  this.visible = show

  // calculate new styling rules
  this.setCustomBoxStyling()

  // trigger event
  this.fireEvent('box.' + (show ? 'show' : 'hide'), [this])

  // show or hide box using selected animation
  if (this.config.position === 'center') {
    this.overlay.classList.toggle('boxzilla-' + this.id + '-overlay')

    if (animate) {
      Animator.toggle(this.overlay, 'fade')
    } else {
      this.overlay.style.display = show ? '' : 'none'
    }
  }

  if (animate) {
    Animator.toggle(this.element, this.config.animation, function () {
      if (this.visible) {
        return
      }
      this.contentElement.innerHTML = this.contentElement.innerHTML + ''
    }.bind(this))
  } else {
    this.element.style.display = show ? '' : 'none'
  }

  return true
}

// show the box
Box.prototype.show = function (animate) {
  return this.toggle(true, animate)
}

// hide the box
Box.prototype.hide = function (animate) {
  return this.toggle(false, animate)
}

// calculate trigger height
Box.prototype.calculateTriggerHeight = function () {
  let triggerHeight = 0

  if (this.config.trigger) {
    if (this.config.trigger.method === 'element') {
      const triggerElement = document.body.querySelector(this.config.trigger.value)

      if (triggerElement) {
        const offset = triggerElement.getBoundingClientRect()
        triggerHeight = offset.top
      }
    } else if (this.config.trigger.method === 'percentage') {
      triggerHeight = (this.config.trigger.value / 100 * getDocumentHeight())
    }
  }

  return triggerHeight
}

Box.prototype.fits = function () {
  if (!this.config.screenWidthCondition || !this.config.screenWidthCondition.value) {
    return true
  }

  switch (this.config.screenWidthCondition.condition) {
    case 'larger':
      return window.innerWidth > this.config.screenWidthCondition.value
    case 'smaller':
      return window.innerWidth < this.config.screenWidthCondition.value
  }

  // meh.. condition should be "smaller" or "larger", just return true.
  return true
}

Box.prototype.onResize = function () {
  this.triggerHeight = this.calculateTriggerHeight()
  this.setCustomBoxStyling()
}

// is this box enabled?
Box.prototype.mayAutoShow = function () {
  if (this.dismissed) {
    return false
  }

  // check if box fits on given minimum screen width
  if (!this.fits()) {
    return false
  }

  // if trigger empty or error in calculating triggerHeight, return false
  if (!this.config.trigger) {
    return false
  }

  // rely on cookie value (show if not set, don't show if set)
  return !this.cookieSet
}

Box.prototype.mayRehide = function () {
  return this.config.rehide && this.triggered
}

Box.prototype.isCookieSet = function () {
  // always show on test mode or when no auto-trigger is configured
  if (this.config.testMode || !this.config.trigger) {
    return false
  }

  // if either cookie is null or trigger & dismiss are both falsey, don't bother checking.
  if (!this.config.cookie || (!this.config.cookie.triggered && !this.config.cookie.dismissed)) {
    return false
  }

  return (new RegExp('(?:^|;)\\s{0,}boxzilla_box_' + String(this.id) + '=1\\s{0,}(?:;|$)')).test(document.cookie)
}

// set cookie that disables automatically showing the box
Box.prototype.setCookie = function (hours) {
  const expiryDate = new Date()
  expiryDate.setHours(expiryDate.getHours() + hours)
  document.cookie = 'boxzilla_box_' + this.id + '=1; expires=' + expiryDate.toUTCString() + '; path=/'
}

Box.prototype.trigger = function () {
  const shown = this.show()
  if (!shown) {
    return
  }

  this.triggered = true
  if (this.config.cookie && this.config.cookie.triggered) {
    this.setCookie(this.config.cookie.triggered)
  }
}

/**
 * Dismisses the box and optionally sets a cookie.
 * @param animate
 * @returns {boolean}
 */
Box.prototype.dismiss = function (animate) {
  // only dismiss box if it's currently open.
  if (!this.visible) {
    return false
  }

  // hide box element
  this.hide(animate)

  // set cookie
  if (this.config.cookie && this.config.cookie.dismissed) {
    this.setCookie(this.config.cookie.dismissed)
  }

  this.dismissed = true
  this.fireEvent('box.dismiss', [this])
  return true
}

module.exports = Box
