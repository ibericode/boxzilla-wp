const Timer = function () {
  this.time = 0
  this.interval = 0
}

Timer.prototype.tick = function () {
  this.time++
}

Timer.prototype.start = function () {
  if (!this.interval) {
    this.interval = window.setInterval(this.tick.bind(this), 1000)
  }
}

Timer.prototype.stop = function () {
  if (this.interval) {
    window.clearInterval(this.interval)
    this.interval = 0
  }
}

module.exports = Timer
