/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ({

/***/ "./css/style.scss":
/*!************************!*\
  !*** ./css/style.scss ***!
  \************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ "./js/adminbar.js":
/*!************************!*\
  !*** ./js/adminbar.js ***!
  \************************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _plugins__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./plugins */ "./js/plugins.js");

var EDIT_CLASS = "simplecms--edit";
var editButton = document.querySelector(".simplecms__adminbar__button--edit");
var saveButton = document.querySelector(".simplecms__adminbar__button--save");
editButton.addEventListener("click", function () {
  document.body.classList.add(EDIT_CLASS);
  editButton.style.display = "none";
  saveButton.style.display = "block";
  _plugins__WEBPACK_IMPORTED_MODULE_0__["default"].enable();
});
saveButton.addEventListener("click", function () {
  document.body.classList.remove(EDIT_CLASS);
  editButton.style.display = "block";
  saveButton.style.display = "none";
  _plugins__WEBPACK_IMPORTED_MODULE_0__["default"].disable();
});

/***/ }),

/***/ "./js/app.js":
/*!*******************!*\
  !*** ./js/app.js ***!
  \*******************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _css_style_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../css/style.scss */ "./css/style.scss");
/* harmony import */ var _css_style_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_css_style_scss__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _adminbar__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./adminbar */ "./js/adminbar.js");



/***/ }),

/***/ "./js/plugins.js":
/*!***********************!*\
  !*** ./js/plugins.js ***!
  \***********************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var kontrl_popups_style_dist_style_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! kontrl-popups/style/dist/style.css */ "./node_modules/kontrl-popups/style/dist/style.css");
/* harmony import */ var kontrl_popups_style_dist_style_css__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(kontrl_popups_style_dist_style_css__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var kontrl_popups__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! kontrl-popups */ "./node_modules/kontrl-popups/dist/popups.js");
/* harmony import */ var kontrl_popups__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(kontrl_popups__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _repo__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./repo */ "./js/repo.js");
function _createForOfIteratorHelper(o, allowArrayLike) { var it; if (typeof Symbol === "undefined" || o[Symbol.iterator] == null) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = o[Symbol.iterator](); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it["return"] != null) it["return"](); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }




var _isEditing = false;
var context = {
  updateSnippet: _repo__WEBPACK_IMPORTED_MODULE_2__["updateSnippet"],
  uploadAnyImage: _repo__WEBPACK_IMPORTED_MODULE_2__["uploadAnyImage"],
  uploadSnippetImage: _repo__WEBPACK_IMPORTED_MODULE_2__["uploadSnippetImage"],
  debounce: function debounce(func, wait, immediate) {
    var timeout;
    return function () {
      var context = this,
          args = arguments;

      var later = function later() {
        timeout = null;
        if (!immediate) func.apply(context, args);
      };

      var callNow = immediate && !timeout;
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
      if (callNow) func.apply(context, args);
    };
  },
  popups: kontrl_popups__WEBPACK_IMPORTED_MODULE_1___default.a,
  isEditing: function isEditing() {
    return _isEditing;
  }
};
var plugins = [];

var _iterator = _createForOfIteratorHelper(__KONTRL_ADMIN_PLUGINS),
    _step;

try {
  for (_iterator.s(); !(_step = _iterator.n()).done;) {
    var pluginClass = _step.value;
    plugins.push(new pluginClass(context));
  }
} catch (err) {
  _iterator.e(err);
} finally {
  _iterator.f();
}

/* harmony default export */ __webpack_exports__["default"] = ({
  enable: function enable() {
    plugins.forEach(function (p) {
      return p.enable();
    });
    _isEditing = true;
  },
  disable: function disable() {
    plugins.forEach(function (p) {
      return p.disable();
    });
    _isEditing = false;
  }
});

/***/ }),

/***/ "./js/repo.js":
/*!********************!*\
  !*** ./js/repo.js ***!
  \********************/
/*! exports provided: updateSnippet, uploadSnippetImage, uploadAnyImage, default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "updateSnippet", function() { return updateSnippet; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "uploadSnippetImage", function() { return uploadSnippetImage; });
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "uploadAnyImage", function() { return uploadAnyImage; });
function updateSnippet(name, value) {
  var page = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : PAGE_NAME;
  return fetch('/simplecms/update', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      page: page,
      name: name,
      value: value
    })
  });
}
function uploadSnippetImage(name, imgFile) {
  var page = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : PAGE_NAME;
  return new Promise(function (resolve) {
    var formData = new FormData();
    formData.append('file', imgFile);
    formData.append('page', page);
    formData.append('name', name);
    fetch("/simplecms/upload", {
      method: "POST",
      body: formData
    }).then(function (response) {
      response.json().then(function (response) {
        resolve(response.url);
      });
    })["catch"](function (err) {// TODO: Show an error or something
    });
  });
}
function uploadAnyImage(imgFile, purpose) {
  return new Promise(function (resolve) {
    var formData = new FormData();
    formData.append('file', imgFile);
    fetch("/simplecms/upload/".concat(purpose), {
      method: "POST",
      body: formData
    }).then(function (response) {
      response.json().then(function (response) {
        resolve(response.url);
      });
    })["catch"](function (err) {// TODO: Show an error or smthng
    });
  });
}
/* harmony default export */ __webpack_exports__["default"] = ({
  uploadSnippetImage: uploadSnippetImage,
  uploadAnyImage: uploadAnyImage,
  updateSnippet: updateSnippet
});

/***/ }),

/***/ "./node_modules/kontrl-popups/dist/Popup.js":
/*!**************************************************!*\
  !*** ./node_modules/kontrl-popups/dist/Popup.js ***!
  \**************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var KontrlPopup = /** @class */ (function () {
    function KontrlPopup(params) {
        if (params === void 0) { params = {}; }
        this.eventHandler = null;
        this.id = params.id != null ? params.id : this.generateId();
    }
    KontrlPopup.prototype.show = function (eventHandler) {
        this.eventHandler = eventHandler;
        this.handleShow();
    };
    KontrlPopup.prototype.getId = function () {
        return this.id;
    };
    KontrlPopup.prototype.throwEvent = function (name, context) {
        if (this.eventHandler == null)
            return;
        this.eventHandler(name, context);
    };
    /**
     * Generates a random ID to uniquely identify this popup
     */
    KontrlPopup.prototype.generateId = function () {
        return (Math.random() * 100000000).toString();
    };
    return KontrlPopup;
}());
exports.default = KontrlPopup;
//# sourceMappingURL=Popup.js.map

/***/ }),

/***/ "./node_modules/kontrl-popups/dist/default_template/DefaultTemplate.js":
/*!*****************************************************************************!*\
  !*** ./node_modules/kontrl-popups/dist/default_template/DefaultTemplate.js ***!
  \*****************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var DefaultTemplate = /** @class */ (function () {
    function DefaultTemplate(params, onClose) {
        this.templateEl = document.createElement("div");
        this.templateEl.classList.add("kontrl-popup__default-template");
        if (params.withBG !== false) {
            var bgEl = document.createElement("div");
            bgEl.classList.add("kontrl-popup__default-template__bg");
            bgEl.addEventListener("click", function () { return onClose(); });
            this.templateEl.appendChild(bgEl);
        }
        var boxEl = document.createElement("div");
        boxEl.classList.add("kontrl-popup__default-template__box");
        this.templateEl.appendChild(boxEl);
        var closeButtonEl = document.createElement("div");
        closeButtonEl.classList.add("kontrl-popup__default-template__close");
        boxEl.appendChild(closeButtonEl);
        closeButtonEl.addEventListener("click", function () { return onClose(); });
        var titleEl = document.createElement("div");
        titleEl.classList.add("kontrl-popup__default-template__title");
        titleEl.appendChild(document.createTextNode(params.title));
        boxEl.appendChild(titleEl);
        if (params.subTitle) {
            var subTitleEl = document.createElement("div");
            subTitleEl.classList.add("kontrl-popup__default-template__sub-title");
            subTitleEl.appendChild(document.createTextNode(params.subTitle));
            boxEl.appendChild(subTitleEl);
        }
        this.contentEl = document.createElement("div");
        this.contentEl.classList.add("kontrl-popup__default-template__content");
        boxEl.appendChild(this.contentEl);
    }
    DefaultTemplate.prototype.getTemplateEl = function () {
        return this.templateEl;
    };
    DefaultTemplate.prototype.getContentEl = function () {
        return this.contentEl;
    };
    DefaultTemplate.prototype.show = function () {
        this.templateEl.classList.add("kontrl-popup__default-template--active");
    };
    DefaultTemplate.prototype.hide = function () {
        this.templateEl.classList.remove("kontrl-popup__default-template--active");
    };
    return DefaultTemplate;
}());
exports.default = DefaultTemplate;
//# sourceMappingURL=DefaultTemplate.js.map

/***/ }),

/***/ "./node_modules/kontrl-popups/dist/generic_popup/GenericPopup.js":
/*!***********************************************************************!*\
  !*** ./node_modules/kontrl-popups/dist/generic_popup/GenericPopup.js ***!
  \***********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var DefaultTemplate_1 = __importDefault(__webpack_require__(/*! ../default_template/DefaultTemplate */ "./node_modules/kontrl-popups/dist/default_template/DefaultTemplate.js"));
var Popup_1 = __importDefault(__webpack_require__(/*! ../Popup */ "./node_modules/kontrl-popups/dist/Popup.js"));
var GenericPopup = /** @class */ (function (_super) {
    __extends(GenericPopup, _super);
    function GenericPopup(params) {
        var _this = _super.call(this) || this;
        _this.params = params;
        _this.items = params.items || [];
        _this.template = new DefaultTemplate_1.default({ title: params.title, subTitle: params.subTitle, withBG: params.withBG }, function () {
            _this.throwEvent("finish");
            _this.hide();
        });
        return _this;
    }
    GenericPopup.prototype.build = function () {
        var _this = this;
        for (var _i = 0, _a = this.items; _i < _a.length; _i++) {
            var item = _a[_i];
            var rowEl = document.createElement("div");
            rowEl.classList.add("kontlr-popup__generic__row");
            var itemEl = item.build();
            rowEl.appendChild(itemEl);
            this.template.getContentEl().appendChild(rowEl);
        }
        var footerEl = document.createElement("footer");
        footerEl.classList.add("kontrl-popup__generic__footer");
        this.template.getContentEl().appendChild(footerEl);
        var submitEl = document.createElement("button");
        submitEl.classList.add("kontrl-popup__generic__button");
        submitEl.addEventListener("click", function () {
            _this.throwEvent("finish", { content: _this.getFullContent() });
            _this.hide();
        });
        submitEl.innerText = this.params.submitText || "Submit";
        footerEl.appendChild(submitEl);
        return this.template.getTemplateEl();
    };
    GenericPopup.prototype.handleShow = function () {
        this.template.show();
    };
    GenericPopup.prototype.hide = function () {
        this.template.hide();
    };
    GenericPopup.prototype.addItem = function (item) {
        this.items.push(item);
    };
    GenericPopup.prototype.getFullContent = function () {
        var content = {};
        for (var _i = 0, _a = this.items; _i < _a.length; _i++) {
            var item = _a[_i];
            content[item.getName()] = item.getContent();
        }
        return content;
    };
    return GenericPopup;
}(Popup_1.default));
exports.default = GenericPopup;
//# sourceMappingURL=GenericPopup.js.map

/***/ }),

/***/ "./node_modules/kontrl-popups/dist/generic_popup/GenericPopupItem.js":
/*!***************************************************************************!*\
  !*** ./node_modules/kontrl-popups/dist/generic_popup/GenericPopupItem.js ***!
  \***************************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

Object.defineProperty(exports, "__esModule", { value: true });
var GenericPopupItem = /** @class */ (function () {
    function GenericPopupItem(params) {
        this.params = params;
    }
    GenericPopupItem.prototype.build = function () {
        var itemEl = document.createElement("div");
        itemEl.classList.add("kontrl-popup__generic__item");
        var labelEl = document.createElement("label");
        labelEl.classList.add("kontrl-popup__generic__label");
        labelEl.innerText = this.params.label;
        labelEl.setAttribute("for", this.params.id);
        itemEl.appendChild(labelEl);
        var itemContentEl = this.buildContent();
        itemEl.appendChild(itemContentEl);
        return itemEl;
    };
    GenericPopupItem.prototype.getLabel = function () {
        return this.params.label;
    };
    GenericPopupItem.prototype.getId = function () {
        return this.params.id;
    };
    GenericPopupItem.prototype.getName = function () {
        return this.params.name;
    };
    return GenericPopupItem;
}());
exports.default = GenericPopupItem;
//# sourceMappingURL=GenericPopupItem.js.map

/***/ }),

/***/ "./node_modules/kontrl-popups/dist/generic_popup/ImageItem.js":
/*!********************************************************************!*\
  !*** ./node_modules/kontrl-popups/dist/generic_popup/ImageItem.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var GenericPopupItem_1 = __importDefault(__webpack_require__(/*! ./GenericPopupItem */ "./node_modules/kontrl-popups/dist/generic_popup/GenericPopupItem.js"));
var ImageItem = /** @class */ (function (_super) {
    __extends(ImageItem, _super);
    function ImageItem(params) {
        var _this = _super.call(this, {
            id: params.name,
            name: params.name,
            label: params.label
        }) || this;
        _this.value = null;
        _this.previewEl = null;
        _this.imageParams = params;
        if (params.value) {
            _this.value = params.value;
        }
        return _this;
    }
    ImageItem.prototype.buildContent = function () {
        var _this = this;
        var dropboxEl = document.createElement("div");
        dropboxEl.classList.add("kontrl-popup__generic__image-box");
        dropboxEl.id = this.getId();
        if (this.imageParams.aspectRatio) {
            var heightPercentage = 100 / this.imageParams.aspectRatio;
            dropboxEl.style.paddingTop = heightPercentage + "%";
        }
        var textEl = document.createElement("p");
        textEl.appendChild(document.createTextNode("Change image"));
        textEl.classList.add("kontrl-popup__generic__image-text");
        dropboxEl.appendChild(textEl);
        this.previewEl = document.createElement("img");
        this.previewEl.classList.add("kontrl-popup__generic__image-preview");
        dropboxEl.appendChild(this.previewEl);
        if (this.value != null) {
            this.previewImage(this.value);
        }
        var inputEl = document.createElement("input");
        inputEl.type = "file";
        inputEl.classList.add("kontrl-popup__generic__image-input");
        inputEl.addEventListener("change", function () {
            var files = inputEl.files;
            if (files == null || files.length === 0)
                return;
            _this.value = files[0];
            _this.previewImage(_this.value);
        });
        dropboxEl.addEventListener("click", function () { return inputEl.click(); });
        return dropboxEl;
    };
    ImageItem.prototype.getContent = function () {
        return this.value;
    };
    ImageItem.prototype.setValue = function (val) {
        this.value = val;
        this.previewImage(val);
    };
    ImageItem.prototype.previewImage = function (img) {
        return __awaiter(this, void 0, void 0, function () {
            var imgSrc;
            return __generator(this, function (_a) {
                switch (_a.label) {
                    case 0:
                        if (this.previewEl == null)
                            return [2 /*return*/];
                        if (!(img instanceof File)) return [3 /*break*/, 2];
                        return [4 /*yield*/, new Promise(function (resolve) {
                                var reader = new FileReader();
                                reader.onload = function () {
                                    resolve(reader.result);
                                };
                                reader.readAsDataURL(img);
                            })];
                    case 1:
                        imgSrc = _a.sent();
                        this.previewEl.src = imgSrc;
                        return [3 /*break*/, 3];
                    case 2:
                        this.previewEl.src = img;
                        _a.label = 3;
                    case 3:
                        this.previewEl.classList.add("kontrl-popup__generic__image-preview--active");
                        return [2 /*return*/];
                }
            });
        });
    };
    return ImageItem;
}(GenericPopupItem_1.default));
exports.default = ImageItem;
//# sourceMappingURL=ImageItem.js.map

/***/ }),

/***/ "./node_modules/kontrl-popups/dist/generic_popup/InputItem.js":
/*!********************************************************************!*\
  !*** ./node_modules/kontrl-popups/dist/generic_popup/InputItem.js ***!
  \********************************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __extends = (this && this.__extends) || (function () {
    var extendStatics = function (d, b) {
        extendStatics = Object.setPrototypeOf ||
            ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
            function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
        return extendStatics(d, b);
    };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
var GenericPopupItem_1 = __importDefault(__webpack_require__(/*! ./GenericPopupItem */ "./node_modules/kontrl-popups/dist/generic_popup/GenericPopupItem.js"));
var TextItem = /** @class */ (function (_super) {
    __extends(TextItem, _super);
    function TextItem(params) {
        var _this = _super.call(this, {
            name: params.name,
            id: params.name,
            label: params.label
        }) || this;
        _this.textItemParams = params;
        _this.inputEl = document.createElement("input");
        return _this;
    }
    TextItem.prototype.buildContent = function () {
        this.inputEl.classList.add("kontrl-popup__generic__input");
        this.inputEl.name = this.textItemParams.name;
        this.inputEl.id = this.getId();
        if (this.textItemParams.value) {
            this.inputEl.value = this.textItemParams.value;
        }
        if (this.textItemParams.placeholder) {
            this.inputEl.placeholder = this.textItemParams.placeholder;
        }
        if (this.textItemParams.type) {
            this.inputEl.type = this.textItemParams.type;
        }
        return this.inputEl;
    };
    TextItem.prototype.getContent = function () {
        return this.inputEl.value;
    };
    return TextItem;
}(GenericPopupItem_1.default));
exports.default = TextItem;
//# sourceMappingURL=InputItem.js.map

/***/ }),

/***/ "./node_modules/kontrl-popups/dist/popups.js":
/*!***************************************************!*\
  !*** ./node_modules/kontrl-popups/dist/popups.js ***!
  \***************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

"use strict";

var __importDefault = (this && this.__importDefault) || function (mod) {
    return (mod && mod.__esModule) ? mod : { "default": mod };
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.ImageItem = exports.InputItem = exports.GenericPopup = exports.destroy = exports.showPopup = exports.build = exports.isBuilt = void 0;
var GenericPopup_1 = __importDefault(__webpack_require__(/*! ./generic_popup/GenericPopup */ "./node_modules/kontrl-popups/dist/generic_popup/GenericPopup.js"));
var InputItem_1 = __importDefault(__webpack_require__(/*! ./generic_popup/InputItem */ "./node_modules/kontrl-popups/dist/generic_popup/InputItem.js"));
var ImageItem_1 = __importDefault(__webpack_require__(/*! ./generic_popup/ImageItem */ "./node_modules/kontrl-popups/dist/generic_popup/ImageItem.js"));
function isBuilt(popup) {
    var item = document.getElementById(popup.getId());
    return item != null;
}
exports.isBuilt = isBuilt;
function build(popup) {
    if (isBuilt(popup))
        return;
    var wrapper = document.createElement("div");
    wrapper.id = popup.getId();
    var builtPopup = popup.build();
    wrapper.appendChild(builtPopup);
    document.body.appendChild(wrapper);
}
exports.build = build;
function showPopup(popup) {
    return new Promise(function (resolve) {
        build(popup);
        popup.show(function (name, context) {
            if (name === "finish") {
                resolve(context);
            }
        });
    });
}
exports.showPopup = showPopup;
function destroy(popup) {
    var wrapper = document.getElementById(popup.getId());
    if (wrapper) {
        wrapper.remove();
    }
}
exports.destroy = destroy;
exports.GenericPopup = GenericPopup_1.default;
exports.InputItem = InputItem_1.default;
exports.ImageItem = ImageItem_1.default;
exports.default = {
    isBuilt: isBuilt,
    build: build,
    showPopup: showPopup,
    destroy: destroy,
    InputItem: InputItem_1.default,
    GenericPopup: GenericPopup_1.default,
    ImageItem: ImageItem_1.default
};
//# sourceMappingURL=popups.js.map

/***/ }),

/***/ "./node_modules/kontrl-popups/style/dist/style.css":
/*!*********************************************************!*\
  !*** ./node_modules/kontrl-popups/style/dist/style.css ***!
  \*********************************************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

// extracted by mini-css-extract-plugin

/***/ }),

/***/ 0:
/*!*************************!*\
  !*** multi ./js/app.js ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

module.exports = __webpack_require__(/*! ./js/app.js */"./js/app.js");


/***/ })

/******/ });
//# sourceMappingURL=app.js.map