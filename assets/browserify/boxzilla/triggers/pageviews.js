module.exports = function (boxes) {
  let pageviews

  try {
    pageviews = sessionStorage.getItem('boxzilla_pageviews') || 0
    sessionStorage.setItem('boxzilla_pageviews', ++pageviews)
  } catch (e) {
    pageviews = 0
  }

  window.setTimeout(() => {
    boxes.forEach((box) => {
      if (box.config.trigger.method === 'pageviews' && pageviews > box.config.trigger.value && box.mayAutoShow()) {
        box.trigger()
      }
    })
  }, 1000)
}
