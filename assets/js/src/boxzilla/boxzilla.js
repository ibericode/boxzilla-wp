const Box = require('./box.js')
const throttle = require('./util.js').throttle
const ExitIntent = require('./triggers/exit-intent.js')
const Scroll = require('./triggers/scroll.js')
const Pageviews = require('./triggers/pageviews.js')
const Time = require('./triggers/time.js')

let initialised = false
const boxes = []
const listeners = {}

function onKeyUp (evt) {
  if (evt.key === 'Escape' || evt.key === 'Esc') {
    dismiss()
  }
}

function recalculateHeights () {
  boxes.forEach(box => box.onResize())
}

function onElementClick (evt) {
  // bubble up to <a> or <area> element
  let el = evt.target
  for (let i = 0; i <= 3; i++) {
    if (!el || el.tagName === 'A' || el.tagName === 'AREA') {
      break
    }

    el = el.parentElement
  }

  if (!el || (el.tagName !== 'A' && el.tagName !== 'AREA') || !el.href) {
    return
  }

  const match = el.href.match(/[#&]boxzilla-(.+)/i)
  if (match && match.length > 1) {
    toggle(match[1])
  }
}

function trigger (event, args) {
  listeners[event] && listeners[event].forEach(f => f.apply(null, args))
}

function on (event, fn) {
  listeners[event] = listeners[event] || []
  listeners[event].push(fn)
}

function off (event, fn) {
  listeners[event] && listeners[event].filter(f => f !== fn)
}

// initialise & add event listeners
function init () {
  if (initialised) {
    return
  }

  // init triggers
  ExitIntent(boxes)
  Pageviews(boxes)
  Scroll(boxes)
  Time(boxes)

  document.body.addEventListener('click', onElementClick, true)
  window.addEventListener('resize', throttle(recalculateHeights))
  window.addEventListener('load', recalculateHeights)
  document.addEventListener('keyup', onKeyUp)

  trigger('ready')
  initialised = true // ensure this function doesn't run again
}

function create (id, opts) {
  // preserve backwards compat for minimumScreenWidth option
  if (typeof (opts.minimumScreenWidth) !== 'undefined') {
    opts.screenWidthCondition = {
      condition: 'larger',
      value: opts.minimumScreenWidth
    }
  }

  id = String(id)
  const box = new Box(id, opts, trigger)
  boxes.push(box)
  return box
}

function get (id) {
  id = String(id)
  for (let i = 0; i < boxes.length; i++) {
    if (boxes[i].id === id) {
      return boxes[i]
    }
  }

  throw new Error('No box exists with ID ' + id)
}

// dismiss a single box (or all by omitting id param)
function dismiss (id, animate) {
  if (id) {
    get(id).dismiss(animate)
  } else {
    boxes.forEach(box => box.dismiss(animate))
  }
}

function hide (id, animate) {
  if (id) {
    get(id).hide(animate)
  } else {
    boxes.forEach(box => box.hide(animate))
  }
}

function show (id, animate) {
  if (id) {
    get(id).show(animate)
  } else {
    boxes.forEach(box => box.show(animate))
  }
}

function toggle (id, animate) {
  if (id) {
    get(id).toggle(animate)
  } else {
    boxes.forEach(box => box.toggle(animate))
  }
}

// expose boxzilla object
const Boxzilla = { off, on, get, init, create, trigger, show, hide, dismiss, toggle, boxes }
window.Boxzilla = Boxzilla

if (typeof module !== 'undefined' && module.exports) {
  module.exports = Boxzilla
}
