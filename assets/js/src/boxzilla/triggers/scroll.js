const throttle = require('../util.js').throttle

module.exports = function (boxes) {
  // check triggerHeight criteria for all boxes
  function checkHeightCriteria () {
    // eslint-disable-next-line no-prototype-builtins
    let scrollY = window.hasOwnProperty('pageYOffset') ? window.pageYOffset : window.scrollTop
    scrollY = scrollY + window.innerHeight * 0.9

    boxes.forEach((box) => {
      if (!box.mayAutoShow() || box.triggerHeight <= 0) {
        return
      }

      if (scrollY > box.triggerHeight) {
        box.trigger()
      } else if (box.mayRehide() && scrollY < (box.triggerHeight - 5)) {
        // if box may auto-hide and scrollY is less than triggerHeight (with small margin of error), hide box
        box.hide()
      }
    })
  }

  window.addEventListener('touchstart', throttle(checkHeightCriteria), true)
  window.addEventListener('scroll', throttle(checkHeightCriteria), true)
}
