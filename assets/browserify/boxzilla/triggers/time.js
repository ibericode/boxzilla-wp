const Timer = require('../timer.js')

module.exports = function (boxes) {
  const siteTimer = new Timer()
  const pageTimer = new Timer()

  const timers = {
    start: function () {
      try {
        const sessionTime = parseInt(sessionStorage.getItem('boxzilla_timer'))
        if (sessionTime) {
          siteTimer.time = sessionTime
        }
      } catch (e) {}
      siteTimer.start()
      pageTimer.start()
    },
    stop: function () {
      sessionStorage.setItem('boxzilla_timer', siteTimer.time)
      siteTimer.stop()
      pageTimer.stop()
    }
  }

  // start timers
  timers.start()

  // stop timers when leaving page or switching to other tab
  document.addEventListener('visibilitychange', function () {
    document.hidden ? timers.stop() : timers.start()
  })

  window.addEventListener('beforeunload', function () {
    timers.stop()
  })

  window.setInterval(() => {
    boxes.forEach((box) => {
      if (box.config.trigger.method === 'time_on_site' && siteTimer.time > box.config.trigger.value && box.mayAutoShow()) {
        box.trigger()
      } else if (box.config.trigger.method === 'time_on_page' && pageTimer.time > box.config.trigger.value && box.mayAutoShow()) {
        box.trigger()
      }
    })
  }, 1000)
}
