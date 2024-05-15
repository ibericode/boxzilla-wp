function throttle (fn, threshold, scope) {
  threshold || (threshold = 800)
  let last
  let deferTimer

  return function () {
    const context = scope || this
    const now = +new Date()
    const args = arguments
    if (last && now < last + threshold) {
      // hold on to it
      clearTimeout(deferTimer)
      deferTimer = setTimeout(function () {
        last = now
        fn.apply(context, args)
      }, threshold)
    } else {
      last = now
      fn.apply(context, args)
    }
  }
}

module.exports = { throttle }
