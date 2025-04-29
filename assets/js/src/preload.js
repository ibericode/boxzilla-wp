var boxzilla_queue = []

// create a temporary global Boxzilla object
// this allows these methods to be called before the Boxzilla script itself has loaded
var Boxzilla = {};
['on', 'off', 'toggle', 'show'].forEach((m) => {
  Boxzilla[m] = function () {
    boxzilla_queue.push([m, arguments])
  }
})

window.Boxzilla = Boxzilla
window.boxzilla_queue = boxzilla_queue
