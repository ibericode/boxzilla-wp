/*
 * ATTENTION: The "eval" devtool has been used (maybe by default in mode: "development").
 * This devtool is neither made for production nor for readable output files.
 * It uses "eval()" calls to create a separate source file in the browser devtools.
 * If you are trying to read the output file, select a different devtool (https://webpack.js.org/configuration/devtool/)
 * or disable the default devtool with "devtool: false".
 * If you are looking for production-ready output files, see mode: "production" (https://webpack.js.org/configuration/mode/).
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./assets/js/src/preload.js":
/*!**********************************!*\
  !*** ./assets/js/src/preload.js ***!
  \**********************************/
/***/ (() => {

eval("var boxzilla_queue = [];\n\n// create a temporary global Boxzilla object\n// this allows these methods to be called before the Boxzilla script itself has loaded\nvar Boxzilla = {};\n['on', 'off', 'toggle', 'show'].forEach(m => {\n  Boxzilla[m] = function () {\n    boxzilla_queue.push([m, arguments]);\n  };\n});\nwindow.Boxzilla = Boxzilla;\nwindow.boxzilla_queue = boxzilla_queue;\n\n//# sourceURL=webpack://boxzilla-wp/./assets/js/src/preload.js?");

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module can't be inlined because the eval devtool is used.
/******/ 	var __webpack_exports__ = {};
/******/ 	__webpack_modules__["./assets/js/src/preload.js"]();
/******/ 	
/******/ })()
;