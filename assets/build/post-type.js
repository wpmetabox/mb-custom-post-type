/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./app/SettingsContext.js":
/*!********************************!*\
  !*** ./app/SettingsContext.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   SettingsContext: () => (/* binding */ SettingsContext),
/* harmony export */   SettingsProvider: () => (/* binding */ SettingsProvider)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);


const SettingsContext = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.createContext)();
const removeHtml = text => text.replace(/<.*?>/g, '').replace(/(&lt;|&gt;)/g, '');
const SettingsProvider = ({
  children,
  value
}) => {
  const [settings, setSettings] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)(value);
  const updateSettings = data => {
    // Remove HTML for labels.
    if (data.hasOwnProperty('labels')) {
      let labels = data.labels;

      // Fix labels is [] when empty.
      if (typeof labels !== 'object' || Array.isArray(labels) || labels === null) {
        labels = {};
      }
      Object.keys(labels).forEach(key => labels[key] = removeHtml(labels[key]));
      data.labels = labels;
    }
    setSettings(prev => ({
      ...prev,
      ...data
    }));
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(SettingsContext.Provider, {
    value: {
      settings,
      updateSettings
    }
  }, children);
};

/***/ }),

/***/ "./app/code.js":
/*!*********************!*\
  !*** ./app/code.js ***!
  \*********************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   checkboxList: () => (/* binding */ checkboxList),
/* harmony export */   general: () => (/* binding */ general),
/* harmony export */   labels: () => (/* binding */ labels),
/* harmony export */   outputSettingObject: () => (/* binding */ outputSettingObject),
/* harmony export */   spaces: () => (/* binding */ spaces),
/* harmony export */   text: () => (/* binding */ text),
/* harmony export */   translatableText: () => (/* binding */ translatableText)
/* harmony export */ });
/* harmony import */ var dot_prop__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! dot-prop */ "./node_modules/.pnpm/dot-prop@5.3.0/node_modules/dot-prop/index.js");
/* harmony import */ var dot_prop__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(dot_prop__WEBPACK_IMPORTED_MODULE_0__);

const maxKeyLength = object => Math.max.apply(null, Object.keys(object).map(key => key.length));
const spaces = (settings, key) => ' '.repeat(maxKeyLength(settings) - key.length);
const checkText = (settings, key) => {
  let value = dot_prop__WEBPACK_IMPORTED_MODULE_0___default().get(settings, key, '').replace(/\\/g, '\\\\');
  value = value.replace(/\'/g, '\\\'');
  return value;
};
const text = (settings, key) => `'${key}'${spaces(settings, key)} => '${checkText(settings, key)}'`;
const translatableText = (settings, key) => `'${key}'${spaces(settings, key)} => esc_html__( '${checkText(settings, key)}', '${settings.text_domain || 'your-textdomain'}' )`;
const checkboxList = (settings, key, defaultValue) => `'${key}'${spaces(settings, key)} => ${dot_prop__WEBPACK_IMPORTED_MODULE_0___default().get(settings, key, []).length ? `['${dot_prop__WEBPACK_IMPORTED_MODULE_0___default().get(settings, key, []).join("', '")}']` : defaultValue}`;
const general = (settings, key) => {
  let value = dot_prop__WEBPACK_IMPORTED_MODULE_0___default().get(settings, key);
  if (['', undefined].includes(value)) {
    value = "''";
  }
  return `'${key}'${spaces(settings, key)} => ${value}`;
};
const outputSettingObject = (settings, key, indent = 1) => {
  const setting = dot_prop__WEBPACK_IMPORTED_MODULE_0___default().get(settings, key);
  if (!isPlainObjectWithKeys(setting)) {
    return '';
  }
  return `'${key}'${spaces(settings, key)} => ${outputObject(setting, indent)}`;
};
const outputObject = (obj, indent = 1) => {
  const indentString = "\t".repeat(indent);
  const bracketIndentString = "\t".repeat(indent - 1);
  const entries = Object.entries(obj).map(([k, v]) => `${indentString}'${k}'${spaces(obj, k)} => '${v}',`);
  return `[\n${entries.join("\n")}\n${bracketIndentString}]`;
};
const isPlainObjectWithKeys = obj => Object.prototype.toString.call(obj) === '[object Object]' && Object.keys(obj).length > 0;
const labels = settings => {
  const {
    labels
  } = settings;
  let keys = Object.keys(labels);
  // Create a temporary labels object with text_domain for translation purposes
  const tempLabels = {
    ...labels
  };
  tempLabels.text_domain = dot_prop__WEBPACK_IMPORTED_MODULE_0___default().get(settings, 'text_domain', 'your-textdomain');
  return keys.map(key => translatableText(tempLabels, key)).join(",\n\t\t");
};


/***/ }),

/***/ "./app/components/Upgrade.js":
/*!***********************************!*\
  !*** ./app/components/Upgrade.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_icons__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/icons */ "./node_modules/.pnpm/@wordpress+icons@10.7.0_react@18.3.1/node_modules/@wordpress/icons/build-module/library/external.js");




const Upgrade = () => MBCPT.upgrade && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Tooltip, {
  delay: 0,
  text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Get access to premium features like creating custom fields, conditional logic, custom table, frontend forms, settings pages, and more.', 'mb-custom-post-type')
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
  variant: "link",
  href: "https://metabox.io/aio/?utm_source=header&utm_medium=link&utm_campaign=cpt",
  target: "_blank",
  icon: _wordpress_icons__WEBPACK_IMPORTED_MODULE_3__["default"],
  iconPosition: "right",
  iconSize: 18,
  text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('Upgrade', 'mb-custom-post-type')
}));
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Upgrade);

/***/ }),

/***/ "./app/controls/Checkbox.js":
/*!**********************************!*\
  !*** ./app/controls/Checkbox.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _Tooltip__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Tooltip */ "./app/controls/Tooltip.js");



const Checkbox = ({
  label,
  name,
  description = '',
  update,
  checked,
  required = false,
  tooltip = ''
}) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-field"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
  className: "mb-cpt-label",
  htmlFor: name
}, label, required && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
  className: "mb-cpt-required"
}, "*"), tooltip && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Tooltip__WEBPACK_IMPORTED_MODULE_2__["default"], {
  id: name,
  content: tooltip
})), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-input"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
  checked: checked,
  label: description,
  onChange: value => update(name, value)
})));
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Checkbox);

/***/ }),

/***/ "./app/controls/CheckboxList.js":
/*!**************************************!*\
  !*** ./app/controls/CheckboxList.js ***!
  \**************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var dot_prop__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! dot-prop */ "./node_modules/.pnpm/dot-prop@5.3.0/node_modules/dot-prop/index.js");
/* harmony import */ var dot_prop__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(dot_prop__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _SettingsContext__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../SettingsContext */ "./app/SettingsContext.js");





const CheckboxList = ({
  name,
  options,
  description
}) => {
  const {
    settings,
    updateSettings
  } = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useContext)(_SettingsContext__WEBPACK_IMPORTED_MODULE_4__.SettingsContext);
  const saved = dot_prop__WEBPACK_IMPORTED_MODULE_3___default().get(settings, name, []);
  const update = (value, checked) => {
    let newSaved = [...saved];
    if (checked) {
      newSaved.push(value);
    } else {
      newSaved = newSaved.filter(option => option !== value);
    }
    updateSettings({
      [name]: newSaved
    });
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-field"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-input"
  }, description && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", {
    className: "mb-cpt-description"
  }, description), Object.entries(options).map(([value, label]) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
    key: value,
    checked: saved.includes(value),
    label: label,
    onChange: checked => update(value, checked)
  }))));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (CheckboxList);

/***/ }),

/***/ "./app/controls/Control.js":
/*!*********************************!*\
  !*** ./app/controls/Control.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var dot_prop__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! dot-prop */ "./node_modules/.pnpm/dot-prop@5.3.0/node_modules/dot-prop/index.js");
/* harmony import */ var dot_prop__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(dot_prop__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var slugify__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! slugify */ "./node_modules/.pnpm/slugify@1.6.6/node_modules/slugify/slugify.js");
/* harmony import */ var slugify__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(slugify__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _SettingsContext__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../SettingsContext */ "./app/SettingsContext.js");
/* harmony import */ var _Checkbox__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ./Checkbox */ "./app/controls/Checkbox.js");
/* harmony import */ var _Fontawesome__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ./Fontawesome */ "./app/controls/Fontawesome.js");
/* harmony import */ var _Icon__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./Icon */ "./app/controls/Icon.js");
/* harmony import */ var _Input__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ./Input */ "./app/controls/Input.js");
/* harmony import */ var _Select__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ./Select */ "./app/controls/Select.js");
/* harmony import */ var _Slug__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./Slug */ "./app/controls/Slug.js");
/* harmony import */ var _Textarea__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./Textarea */ "./app/controls/Textarea.js");
/* harmony import */ var _Toggle__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./Toggle */ "./app/controls/Toggle.js");













const ucfirst = str => str.length ? str[0].toUpperCase() + str.slice(1) : str;
const normalizeBool = value => {
  if ('true' === value) {
    value = true;
  } else if ('false' === value) {
    value = false;
  }
  return value;
};
const Control = ({
  field,
  autoFills = []
}) => {
  const {
    settings,
    updateSettings
  } = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useContext)(_SettingsContext__WEBPACK_IMPORTED_MODULE_4__.SettingsContext);
  const isDisplay = field => {
    const {
      dependency
    } = field;
    if (!dependency) {
      return true;
    }
    const conditions = dependency.split('&&').map(c => c.trim());
    for (let condition of conditions) {
      const match = condition.match(/([^:]+):([^:\s]+)/);
      if (!match) {
        continue;
      }
      const depName = match[1];
      const depValue = normalizeBool(match[2]);
      const currentValue = dot_prop__WEBPACK_IMPORTED_MODULE_2___default().get(settings, depName);
      if (currentValue !== depValue) {
        return false;
      }
    }
    return true;
  };
  const autofill = (newSettings, name, value) => {
    const placeholder = name.replace('labels.', '');
    autoFills.forEach(f => {
      let newValue;
      if ('slug' === f.name) {
        // Only generate slug when it's not manually changed.
        if (newSettings._slug_changed) {
          return;
        }
        newValue = slugify__WEBPACK_IMPORTED_MODULE_3___default()(value, {
          lower: true
        });
      } else {
        newValue = ucfirst(f.default.replace(`%${placeholder}%`, value).replace(`%${placeholder}_lowercase%`, value.toLowerCase()));
      }
      dot_prop__WEBPACK_IMPORTED_MODULE_2___default().set(newSettings, f.name, newValue);
    });
    return newSettings;
  };
  const update = e => {
    const name = e.target.name;
    let value = 'checkbox' === e.target.type ? dot_prop__WEBPACK_IMPORTED_MODULE_2___default().get(e.target, 'checked', false) : e.target.value;
    value = normalizeBool(value);
    value = name === 'menu_position' ? parseFloat(value) || '' : value;
    let newSettings = {
      ...settings
    };
    dot_prop__WEBPACK_IMPORTED_MODULE_2___default().set(newSettings, name, value);
    autofill(newSettings, name, value);
    updateSettings(newSettings);
  };
  const updateCheckbox = (name, value) => {
    let newSettings = {
      ...settings
    };
    dot_prop__WEBPACK_IMPORTED_MODULE_2___default().set(newSettings, name, value);
    updateSettings(newSettings);
  };
  const _value = dot_prop__WEBPACK_IMPORTED_MODULE_2___default().get(settings, field.name, field.default || '');
  if (!isDisplay(field)) {
    return '';
  }
  switch (field.type) {
    case 'text':
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Input__WEBPACK_IMPORTED_MODULE_8__["default"], {
        ...field,
        value: _value,
        update: update
      });
    case 'textarea':
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Textarea__WEBPACK_IMPORTED_MODULE_11__["default"], {
        ...field,
        value: _value,
        update: update
      });
    case 'toggle':
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Toggle__WEBPACK_IMPORTED_MODULE_12__["default"], {
        ...field,
        checked: _value,
        update: updateCheckbox
      });
    case 'checkbox':
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Checkbox__WEBPACK_IMPORTED_MODULE_5__["default"], {
        ...field,
        checked: _value,
        update: updateCheckbox
      });
    case 'icon':
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Icon__WEBPACK_IMPORTED_MODULE_7__["default"], {
        ...field,
        value: _value,
        update: update
      });
    case 'fontawesome':
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Fontawesome__WEBPACK_IMPORTED_MODULE_6__["default"], {
        ...field,
        value: _value,
        update: update
      });
    case 'select':
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Select__WEBPACK_IMPORTED_MODULE_9__["default"], {
        ...field,
        value: _value,
        update: update
      });
    case 'slug':
      return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Slug__WEBPACK_IMPORTED_MODULE_10__["default"], {
        ...field,
        value: _value,
        update: update,
        settings: settings,
        updateSettings: updateSettings
      });
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Control);

/***/ }),

/***/ "./app/controls/Fontawesome.js":
/*!*************************************!*\
  !*** ./app/controls/Fontawesome.js ***!
  \*************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Tooltip__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Tooltip */ "./app/controls/Tooltip.js");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);



const Fontawesome = ({
  label,
  name,
  update,
  value,
  required = false,
  tooltip = '',
  description = ''
}) => {
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-field"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    className: "mb-cpt-label",
    htmlFor: name
  }, label, required && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "mb-cpt-required"
  }, "*"), tooltip && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Tooltip__WEBPACK_IMPORTED_MODULE_1__["default"], {
    id: name,
    content: tooltip
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-input"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-icon-selected"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: `icon-fontawesome ${value}`
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "text",
    name: name,
    value: value,
    onChange: update
  })), description && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-description"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.RawHTML, null, description))));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Fontawesome);

/***/ }),

/***/ "./app/controls/Icon.js":
/*!******************************!*\
  !*** ./app/controls/Icon.js ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _Tooltip__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Tooltip */ "./app/controls/Tooltip.js");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);




const getIconLabel = icon => {
  let label = icon.replace(/-/g, ' ').trim();
  const startsText = ['admin', 'controls', 'editor', 'format', 'image', 'media', 'welcome'];
  startsText.forEach(text => {
    if (label.startsWith(text)) {
      label = label.replace(text, '');
    }
  });
  const endsText = ['alt', 'alt2', 'alt3'];
  endsText.forEach(text => {
    if (label.endsWith(text)) {
      label = label.replace(text, `(${text})`);
    }
  });
  label = label.trim();
  const specialText = {
    businessman: 'business man',
    aligncenter: 'align center',
    alignleft: 'align left',
    alignright: 'align right',
    customchar: 'custom character',
    distractionfree: 'distraction free',
    removeformatting: 'remove formatting',
    strikethrough: 'strike through',
    skipback: 'skip back',
    skipforward: 'skip forward',
    leftright: 'left right',
    screenoptions: 'screen options'
  };
  label = specialText[label] || label;
  return label.trim().toLowerCase();
};
const Icon = ({
  label,
  name,
  update,
  value,
  required = false,
  tooltip = ''
}) => {
  const [query, setQuery] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useState)("");
  let data = MBCPT.icons.map(icon => [icon, getIconLabel(icon)]).filter(item => query === '' || item[1].includes(query.toLowerCase()));
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-field mb-cpt-field--radio"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    className: "mb-cpt-label",
    htmlFor: name
  }, label, required && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "mb-cpt-required"
  }, "*"), tooltip && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Tooltip__WEBPACK_IMPORTED_MODULE_2__["default"], {
    id: name,
    content: tooltip
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-input"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-icon-selected"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: `dashicons ${value}`
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "text",
    className: "mb-cpt-search",
    placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Search...', 'mb-custom-post-type'),
    value: query,
    onChange: event => setQuery(event.target.value)
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-items"
  }, data.map(([icon, label]) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    key: icon,
    className: "mb-cpt-item"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    className: "mb-cpt-icon"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "radio",
    name: name,
    value: `dashicons-${icon}`,
    checked: `dashicons-${icon}` === value,
    onChange: update
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: `dashicons dashicons-${icon}`
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "mb-cpt-item__text"
  }, label))))));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Icon);

/***/ }),

/***/ "./app/controls/Input.js":
/*!*******************************!*\
  !*** ./app/controls/Input.js ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _Tooltip__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Tooltip */ "./app/controls/Tooltip.js");



const Input = ({
  label,
  name,
  value,
  update,
  tooltip = '',
  description = '',
  required = false,
  placeholder = '',
  datalist = []
}) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-field"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
  className: "mb-cpt-label",
  htmlFor: name
}, label, required && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
  className: "mb-cpt-required"
}, "*"), tooltip && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Tooltip__WEBPACK_IMPORTED_MODULE_2__["default"], {
  id: name,
  content: tooltip
})), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-input"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
  type: "text",
  required: required,
  id: name,
  name: name,
  value: value,
  onChange: update,
  placeholder: placeholder,
  list: `${name}-list`
}), description && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-description"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.RawHTML, null, description)), datalist.length > 0 && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("datalist", {
  id: `${name}-list`
}, datalist.map(item => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("option", {
  key: item,
  value: item
})))));
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Input);

/***/ }),

/***/ "./app/controls/Select.js":
/*!********************************!*\
  !*** ./app/controls/Select.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Tooltip__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Tooltip */ "./app/controls/Tooltip.js");


const Select = ({
  label,
  name,
  update,
  description = '',
  options,
  value,
  required = false,
  tooltip = ''
}) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-field"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
  className: "mb-cpt-label",
  htmlFor: name
}, label, required && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
  className: "mb-cpt-required"
}, "*"), tooltip && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Tooltip__WEBPACK_IMPORTED_MODULE_1__["default"], {
  id: name,
  content: tooltip
})), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-input"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("select", {
  id: name,
  name: name,
  value: value,
  onChange: update
}, options.map(option => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("option", {
  key: option.value,
  value: option.value
}, option.label))), description && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-description"
}, description)));
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Select);

/***/ }),

/***/ "./app/controls/Slug.js":
/*!******************************!*\
  !*** ./app/controls/Slug.js ***!
  \******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _Tooltip__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./Tooltip */ "./app/controls/Tooltip.js");




const Slug = ({
  label,
  name,
  value,
  update,
  tooltip = '',
  description = '',
  required = false,
  limit = 20,
  settings,
  updateSettings
}) => {
  const isReservedTerm = MBCPT.reservedTerms.includes(value);
  const isTooLong = value.length > limit;
  const error = isReservedTerm ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('ERROR: the slug must not be one of WordPress <a href="https://codex.wordpress.org/Reserved_Terms" target="_blank" rel="noopener noreferrer">reserved terms</a>', 'mb-custom-post-type') : isTooLong ? sprintf((0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_2__.__)('ERROR: the slug must not exceed %d characters.', 'mb-custom-post-type'), limit) : '';
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.useEffect)(() => {
    document.querySelector('.mb-cpt-submit').disabled = !!error;
  }, [value]);
  const setSlugChanged = () => {
    const newSettings = {
      ...settings
    };
    newSettings._slug_changed = true;
    updateSettings(newSettings);
  };
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-field"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
    className: "mb-cpt-label",
    htmlFor: name
  }, label, required && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
    className: "mb-cpt-required"
  }, "*"), tooltip && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Tooltip__WEBPACK_IMPORTED_MODULE_3__["default"], {
    id: name,
    content: tooltip
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-input"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "text",
    required: required,
    id: name,
    name: name,
    value: value,
    onChange: update,
    onBlur: setSlugChanged
  }), description && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-description"
  }, description), error && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.RawHTML, {
    className: "mb-cpt-error"
  }, error)));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Slug);

/***/ }),

/***/ "./app/controls/Textarea.js":
/*!**********************************!*\
  !*** ./app/controls/Textarea.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _Tooltip__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Tooltip */ "./app/controls/Tooltip.js");


const Textarea = ({
  label,
  name,
  placeholder,
  value,
  update,
  description = '',
  required = false,
  tooltip = ''
}) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-field"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("label", {
  className: "mb-cpt-label",
  htmlFor: name
}, label, required && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
  className: "mb-cpt-required"
}, "*"), tooltip && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Tooltip__WEBPACK_IMPORTED_MODULE_1__["default"], {
  id: name,
  content: tooltip
})), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-input"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("textarea", {
  id: name,
  name: name,
  placeholder: placeholder,
  value: value,
  onChange: update
}), description && (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-description"
}, description)));
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Textarea);

/***/ }),

/***/ "./app/controls/Toggle.js":
/*!********************************!*\
  !*** ./app/controls/Toggle.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);


const Toggle = ({
  label,
  name,
  description,
  update,
  checked
}) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-field"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
  className: "mb-cpt-input"
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
  checked: checked,
  label: label,
  help: description,
  onChange: value => update(name, value)
})));
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Toggle);

/***/ }),

/***/ "./app/controls/Tooltip.js":
/*!*********************************!*\
  !*** ./app/controls/Tooltip.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);

const {
  Tooltip: T,
  Dashicon
} = wp.components;
const Tooltip = ({
  content
}) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(T, {
  text: content
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("span", {
  className: "mb-cpt-tooltip-icon",
  tabIndex: -1
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Dashicon, {
  icon: "editor-help"
})));
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Tooltip);

/***/ }),

/***/ "./app/controls/logo.svg":
/*!*******************************!*\
  !*** ./app/controls/logo.svg ***!
  \*******************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   ReactComponent: () => (/* binding */ SvgLogo),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
var _rect, _path;
function _extends() { return _extends = Object.assign ? Object.assign.bind() : function (n) { for (var e = 1; e < arguments.length; e++) { var t = arguments[e]; for (var r in t) ({}).hasOwnProperty.call(t, r) && (n[r] = t[r]); } return n; }, _extends.apply(null, arguments); }

var SvgLogo = function SvgLogo(props) {
  return /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("svg", _extends({
    xmlns: "http://www.w3.org/2000/svg",
    fill: "none",
    viewBox: "0 0 46 46"
  }, props), _rect || (_rect = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("rect", {
    width: 46,
    height: 46,
    fill: "#000",
    rx: 3
  })), _path || (_path = /*#__PURE__*/react__WEBPACK_IMPORTED_MODULE_0__.createElement("path", {
    fill: "#fff",
    d: "M8.688 35v-2.563l2.271-.44V16.69l-2.27-.439v-2.578h8.598l5.816 14.868h.087l5.625-14.868h8.614v2.578l-2.285.44v15.307l2.285.44V35h-8.833v-2.563l2.387-.44V27.91l.074-9.624-.088-.015-6.372 16.436h-3.238l-6.518-16.304-.088.015.249 9.053v4.526l2.52.44V35z"
  })));
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ("data:image/svg+xml;base64,PHN2ZyB2aWV3Qm94PSIwIDAgNDYgNDYiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSI0NiIgaGVpZ2h0PSI0NiIgcng9IjMiIGZpbGw9ImJsYWNrIi8+CjxwYXRoIGQ9Ik04LjY4ODQ4IDM1VjMyLjQzNjVMMTAuOTU5IDMxLjk5NzFWMTYuNjg5NUw4LjY4ODQ4IDE2LjI1VjEzLjY3MTlIMTAuOTU5SDE3LjI4NzFMMjMuMTAyNSAyOC41NEgyMy4xOTA0TDI4LjgxNTQgMTMuNjcxOUgzNy40Mjg3VjE2LjI1TDM1LjE0MzYgMTYuNjg5NVYzMS45OTcxTDM3LjQyODcgMzIuNDM2NVYzNUgyOC41OTU3VjMyLjQzNjVMMzAuOTgzNCAzMS45OTcxVjI3LjkxMDJMMzEuMDU2NiAxOC4yODYxTDMwLjk2ODggMTguMjcxNUwyNC41OTY3IDM0LjcwN0gyMS4zNTk0TDE0Ljg0MDggMTguNDAzM0wxNC43NTI5IDE4LjQxOEwxNS4wMDIgMjcuNDcwN1YzMS45OTcxTDE3LjUyMTUgMzIuNDM2NVYzNUg4LjY4ODQ4WiIgZmlsbD0id2hpdGUiLz4KPC9zdmc+Cg==");

/***/ }),

/***/ "./app/post-type/MainTabs.js":
/*!***********************************!*\
  !*** ./app/post-type/MainTabs.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_icons__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/icons */ "./node_modules/.pnpm/@wordpress+icons@10.7.0_react@18.3.1/node_modules/@wordpress/icons/build-module/library/code.js");
/* harmony import */ var _SettingsContext__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! ../SettingsContext */ "./app/SettingsContext.js");
/* harmony import */ var _components_Upgrade__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../components/Upgrade */ "./app/components/Upgrade.js");
/* harmony import */ var _controls_CheckboxList__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ../controls/CheckboxList */ "./app/controls/CheckboxList.js");
/* harmony import */ var _controls_Control__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! ../controls/Control */ "./app/controls/Control.js");
/* harmony import */ var _controls_logo_svg__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! ../controls/logo.svg */ "./app/controls/logo.svg");
/* harmony import */ var _Result__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./Result */ "./app/post-type/Result.js");
/* harmony import */ var _constants_Data__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! ./constants/Data */ "./app/post-type/constants/Data.js");












const tabs = [{
  name: 'general',
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('General', 'mb-custom-post-type')
}, {
  name: 'labels',
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Labels', 'mb-custom-post-type')
}, {
  name: 'advanced',
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Advanced', 'mb-custom-post-type')
}, {
  name: 'supports',
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Supports', 'mb-custom-post-type')
}, {
  name: 'taxonomies',
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Taxonomies', 'mb-custom-post-type')
}, {
  name: 'features',
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Features', 'mb-custom-post-type')
}, {
  name: 'code',
  icon: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Icon, {
    icon: _wordpress_icons__WEBPACK_IMPORTED_MODULE_4__["default"]
  }),
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Get PHP Code', 'mb-custom-post-type'),
  className: 'mb-cpt-code components-button is-small has-icon'
}];
let autoFills = [..._constants_Data__WEBPACK_IMPORTED_MODULE_11__.LabelControls, ..._constants_Data__WEBPACK_IMPORTED_MODULE_11__.BasicControls];
autoFills.push({
  name: 'label',
  default: '%name%',
  updateFrom: 'labels.name'
});

// Panels
const panels = {
  general: _constants_Data__WEBPACK_IMPORTED_MODULE_11__.BasicControls.map((field, key) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls_Control__WEBPACK_IMPORTED_MODULE_8__["default"], {
    key: key,
    field: field,
    autoFills: autoFills.filter(f => f.updateFrom === field.name)
  })),
  labels: _constants_Data__WEBPACK_IMPORTED_MODULE_11__.LabelControls.map((field, key) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls_Control__WEBPACK_IMPORTED_MODULE_8__["default"], {
    key: key,
    field: field
  })),
  advanced: _constants_Data__WEBPACK_IMPORTED_MODULE_11__.AdvancedControls.map((field, key) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls_Control__WEBPACK_IMPORTED_MODULE_8__["default"], {
    key: key,
    field: field
  })),
  supports: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls_CheckboxList__WEBPACK_IMPORTED_MODULE_7__["default"], {
    name: "supports",
    options: _constants_Data__WEBPACK_IMPORTED_MODULE_11__.SupportControls,
    description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Core features the post type supports:', 'mb-custom-post-type')
  }),
  taxonomies: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls_CheckboxList__WEBPACK_IMPORTED_MODULE_7__["default"], {
    name: "taxonomies",
    options: MBCPT.taxonomies,
    description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Taxonomies that will be registered for the post type:', 'mb-custom-post-type')
  }),
  features: _constants_Data__WEBPACK_IMPORTED_MODULE_11__.FeatureControls.map((field, key) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls_Control__WEBPACK_IMPORTED_MODULE_8__["default"], {
    key: key,
    field: field
  })),
  code: (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, _constants_Data__WEBPACK_IMPORTED_MODULE_11__.CodeControls.map((field, key) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls_Control__WEBPACK_IMPORTED_MODULE_8__["default"], {
    key: key,
    field: field
  })), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_Result__WEBPACK_IMPORTED_MODULE_10__["default"], null))
};
const MainTabs = () => {
  const {
    settings
  } = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_2__.useContext)(_SettingsContext__WEBPACK_IMPORTED_MODULE_5__.SettingsContext);
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Flex, {
    className: "mb-header"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Flex, {
    gap: 2,
    expanded: false
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Tooltip, {
    text: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Back to all post types', 'mb-custom-post-type'),
    position: 'bottom right'
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("a", {
    className: "mb-header__logo",
    href: MBCPT.url
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_controls_logo_svg__WEBPACK_IMPORTED_MODULE_9__.ReactComponent, null))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("h1", null, MBCPT.action === 'add' ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Add Post Type', 'mb-custom-post-type') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Edit Post Type', 'mb-custom-post-type'))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Flex, {
    gap: 1,
    expanded: false
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_components_Upgrade__WEBPACK_IMPORTED_MODULE_6__["default"], null), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "submit",
    "data-status": "publish",
    className: "mb-cpt-submit components-button is-primary",
    value: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_3__.__)('Save Changes', 'mb-custom-post-type')
  }))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-body mb-body"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-body__inner"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-main"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "wp-header-end"
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TabPanel, {
    className: "mb-box",
    tabs: tabs
  }, tab => panels[tab.name])))), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "hidden",
    name: "post_title",
    value: settings.labels.singular_name
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "hidden",
    name: "content",
    value: JSON.stringify(settings)
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "hidden",
    name: "post_status",
    value: MBCPT.status
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("input", {
    type: "hidden",
    name: "messages",
    value: ""
  }));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (MainTabs);

/***/ }),

/***/ "./app/post-type/Result.js":
/*!*********************************!*\
  !*** ./app/post-type/Result.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/compose */ "@wordpress/compose");
/* harmony import */ var _wordpress_compose__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var react_codemirror2__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! react-codemirror2 */ "./node_modules/.pnpm/react-codemirror2@7.3.0_codemirror@5.65.20_react@18.3.1/node_modules/react-codemirror2/index.js");
/* harmony import */ var _SettingsContext__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! ../SettingsContext */ "./app/SettingsContext.js");
/* harmony import */ var _constants_PhpCode__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! ./constants/PhpCode */ "./app/post-type/constants/PhpCode.js");








const Result = () => {
  const {
    settings
  } = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useContext)(_SettingsContext__WEBPACK_IMPORTED_MODULE_6__.SettingsContext);
  const Button = (0,_wordpress_compose__WEBPACK_IMPORTED_MODULE_2__.withState)({
    hasCopied: false
  })(({
    hasCopied,
    setState
  }) => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ClipboardButton, {
    className: "button",
    text: (0,_constants_PhpCode__WEBPACK_IMPORTED_MODULE_7__["default"])(settings),
    onCopy: () => setState({
      hasCopied: true
    }),
    onFinishCopy: () => setState({
      hasCopied: false
    })
  }, hasCopied ? (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Copied!', 'meta-box-builder') : (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Copy', 'meta-box-builder')));
  return (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-result"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("p", null, (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_4__.__)('Copy and paste the following code into your theme\'s functions.php file.', 'mb-custom-post-type')), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)("div", {
    className: "mb-cpt-result__body"
  }, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(react_codemirror2__WEBPACK_IMPORTED_MODULE_5__.UnControlled, {
    value: (0,_constants_PhpCode__WEBPACK_IMPORTED_MODULE_7__["default"])(settings),
    options: {
      mode: 'php',
      lineNumbers: true
    }
  }), (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(Button, null)));
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Result);

/***/ }),

/***/ "./app/post-type/constants/Data.js":
/*!*****************************************!*\
  !*** ./app/post-type/constants/Data.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   AdvancedControls: () => (/* binding */ AdvancedControls),
/* harmony export */   BasicControls: () => (/* binding */ BasicControls),
/* harmony export */   CodeControls: () => (/* binding */ CodeControls),
/* harmony export */   FeatureControls: () => (/* binding */ FeatureControls),
/* harmony export */   LabelControls: () => (/* binding */ LabelControls),
/* harmony export */   SupportControls: () => (/* binding */ SupportControls)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);

const BasicControls = [{
  type: 'text',
  name: 'labels.name',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Plural name', 'mb-custom-post-type'),
  required: true,
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('General name for the post type, usually plural', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.singular_name',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Singular name', 'mb-custom-post-type'),
  required: true,
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Name for one object of this post type', 'mb-custom-post-type')
}, {
  type: 'slug',
  name: 'slug',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Slug', 'mb-custom-post-type'),
  required: true,
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Post type key. Must not exceed 20 characters and may only contain lowercase alphanumeric characters, dashes, and underscores', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'public',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Public', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether a post type is intended for use publicly either via the admin interface or by front-end users.', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'hierarchical',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Hierarchical', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether the post type is hierarchical (e.g. like page).', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'show_in_rest',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Enable block editor?', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Enable this option will also expose this post type in the REST API.', 'mb-custom-post-type')
}, {
  type: 'select',
  name: 'show_in_menu',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show in admin menu', 'mb-custom-post-type'),
  options: MBCPT.show_in_menu_options
}, {
  type: 'select',
  name: 'menu_position',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show post type menu after', 'mb-custom-post-type'),
  options: MBCPT.menu_position_options,
  dependency: 'show_in_menu:true'
}, {
  type: 'select',
  name: 'icon_type',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Menu icon type', 'mb-custom-post-type'),
  options: MBCPT.icon_type,
  dependency: 'show_in_menu:true'
}, {
  type: 'icon',
  name: 'icon',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Menu icon', 'mb-custom-post-type'),
  dependency: 'show_in_menu:true && icon_type:dashicons'
}, {
  type: 'text',
  name: 'icon_svg',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Menu icon SVG', 'mb-custom-post-type'),
  dependency: 'show_in_menu:true && icon_type:svg',
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Must be in base64 encoded format.', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'icon_custom',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Menu icon URL', 'mb-custom-post-type'),
  dependency: 'show_in_menu:true && icon_type:custom'
}, {
  type: 'fontawesome',
  name: 'font_awesome',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Menu icon', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Enter <a href="https://fontawesome.com/search?o=r&m=free">FontAwesome</a> icon class here. Supports FontAwesome free version only.', 'mb-custom-post-type'),
  dependency: 'show_in_menu:true && icon_type:font_awesome'
}];
const CodeControls = [{
  type: 'text',
  name: 'function_name',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Function name', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Your function name that registers the post type', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'text_domain',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Text domain', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Required for multilingual website. Used in the exported code only.', 'mb-custom-post-type')
}];
const LabelControls = [
// Name
// Singular name
{
  type: 'text',
  name: 'labels.add_new',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add new', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add New', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for adding a new singular item', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.add_new_item',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add new item', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add New %singular_name%', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for adding a new singular item', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.edit_item',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Edit item', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Edit %singular_name%', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for editing a singular item', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.new_item',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('New item', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('New %singular_name%', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the new item page title', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.view_item',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('View item', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('View %singular_name%', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for viewing a singular item', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.view_items',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('View items', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('View %name%', 'mb-custom-post-type'),
  updateFrom: 'labels.name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for viewing post type archives', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.search_items',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Search items', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Search %name%', 'mb-custom-post-type'),
  updateFrom: 'labels.name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for searching items', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.not_found',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Not found', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('No %name_lowercase% found.', 'mb-custom-post-type'),
  updateFrom: 'labels.name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label used when no items are found', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.not_found_in_trash',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Not found in Trash', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('No %name_lowercase% found in Trash.', 'mb-custom-post-type'),
  updateFrom: 'labels.name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label used when no items are in the Trash', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.parent_item_colon',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Parent items', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Parent %singular_name%:', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label used to prefix parents of hierarchical items. Not used on non-hierarchical post types', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.all_items',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('All items', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('All %name%', 'mb-custom-post-type'),
  updateFrom: 'labels.name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label to signify all items in a submenu link', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.archives',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Nav menus archives', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%singular_name% Archives', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for archives in nav menus', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.attributes',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Attributes meta box', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%singular_name% Attributes', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the attributes meta box', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.insert_into_item',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Media frame button', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Insert into %singular_name_lowercase%', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the media frame button', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.uploaded_to_this_item',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Media frame filter', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Uploaded to this %singular_name_lowercase%', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the media frame filter', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.featured_image',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Featured image meta box', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Featured image', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the featured image meta box title', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.set_featured_image',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Setting the featured image', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Set featured image', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for setting the featured image', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.remove_featured_image',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Removing the featured image', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove featured image', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for removing the featured image', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.use_featured_image',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Used as featured image', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Use as featured image', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label in the media frame for using a featured image', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.menu_name',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Menu name', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%name%', 'mb-custom-post-type'),
  updateFrom: 'labels.name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the menu name', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.filter_items_list',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Table filter hidden heading', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Filter %name_lowercase% list', 'mb-custom-post-type'),
  updateFrom: 'labels.name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the table views hidden heading', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.filter_by_date',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Table date filter hidden heading', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Filter by date', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the date filter in list tables', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.items_list_navigation',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Table pagination hidden heading', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%name% list navigation', 'mb-custom-post-type'),
  updateFrom: 'labels.name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the table pagination hidden heading', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.items_list',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Table hidden heading', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%name% list', 'mb-custom-post-type'),
  updateFrom: 'labels.name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label for the table hidden heading', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.item_published',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Item published', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%singular_name% published.', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label used when an item is published', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.item_published_privately',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Item published privately', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%singular_name% published privately.', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label used when an item is published with private visibility', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.item_reverted_to_draft',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Item switched to draft', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%singular_name% reverted to draft.', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label used when an item is switched to a draft', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.item_scheduled',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Item scheduled', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%singular_name% scheduled.', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label used when an item is scheduled for publishing', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'labels.item_updated',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Item updated', 'mb-custom-post-type'),
  default: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('%singular_name% updated.', 'mb-custom-post-type'),
  updateFrom: 'labels.singular_name',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Label used when an item is updated', 'mb-custom-post-type')
}];
const SupportControls = {
  title: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Title', 'mb-custom-post-type'),
  editor: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Editor', 'mb-custom-post-type'),
  excerpt: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Excerpt', 'mb-custom-post-type'),
  author: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Author', 'mb-custom-post-type'),
  thumbnail: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Thumbnail', 'mb-custom-post-type'),
  trackbacks: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Trackbacks', 'mb-custom-post-type'),
  'custom-fields': (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Custom fields', 'mb-custom-post-type'),
  comments: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Comments', 'mb-custom-post-type'),
  revisions: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Revisions', 'mb-custom-post-type'),
  'page-attributes': (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Page attributes', 'mb-custom-post-type'),
  'post-formats': (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Post formats', 'mb-custom-post-type')
};
const CapabilityControls = [{
  value: 'post',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Post', 'mb-custom-post-type')
}, {
  value: 'page',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Page', 'mb-custom-post-type')
}, {
  value: 'custom',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Custom', 'mb-custom-post-type')
}];
const AdvancedControls = [{
  type: 'textarea',
  name: 'description',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Description', 'mb-custom-post-type'),
  placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('A short descriptive summary of what the post type is', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('A short descriptive summary of what the post type is', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'exclude_from_search',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Exclude from search', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether to exclude posts with this post type from frontend search results.', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether to exclude posts with this post type from front end search results', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'publicly_queryable',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Publicly queryable', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether queries can be performed on the frontend.', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether queries can be performed on the front end for the post type as part of parse_request()', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'show_ui',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show UI', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether to generate a default UI for managing this post type in the admin.', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether to generate and allow a UI for managing this post type in the admin', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'show_in_nav_menus',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show in nav menus', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether post type is available for selection in navigation menus.', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Makes this post type available for selection in navigation menus', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'show_in_admin_bar',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Show in admin bar', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether to make this post type available in the WordPress admin bar.', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Makes this post type available via the admin bar', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'rest_base',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('REST API base slug', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Leave empty to use the post type slug.', 'mb-custom-post-type'),
  placeholder: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Slug to use in REST API URL', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Custom base URL of REST API route', 'mb-custom-post-type')
}, {
  type: 'select',
  name: 'capability_type',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Capability type', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('If select custom capability, make sure to add capabilities to admin or other roles to add or edit posts of this type (using a plugin like Members or User Role Editor).', 'mb-custom-post-type'),
  options: CapabilityControls,
  default: 'post',
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('The string to use to build the read, edit, and delete capabilities', 'mb-custom-post-type')
},
// map_meta_cap
// supports
// taxonomies
{
  type: 'checkbox',
  name: 'has_archive',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Has archive', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Enables post type archives.', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether there should be post type archives. Will generate the proper rewrite rules if the rewrite settings is enabled', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'archive_slug',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Custom archive slug', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Default is the post type slug.', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('The custom archive slug', 'mb-custom-post-type')
}, {
  type: 'text',
  name: 'rewrite.slug',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Custom rewrite slug', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Leave empty to use the post type slug.', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Customize the permastruct slug', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'rewrite.with_front',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Prepended permalink structure', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Example: if your permalink structure is /blog/, then your links will be: false -> /news/, true -> /blog/news/.', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether the custom permastruct should be prepended', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'query_var',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Query var', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Enables request the post via URL: example.com/?post_type=slug', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Sets the custom query var key for this post type', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'can_export',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Can export', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Can this post type be exported', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether to allow this post type to be exported', 'mb-custom-post-type')
}, {
  type: 'checkbox',
  name: 'delete_with_user',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Delete with user', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether to move posts to Trash when deleting a user', 'mb-custom-post-type'),
  tooltip: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Whether to delete posts of this type when deleting a user', 'mb-custom-post-type')
}];
const FeatureControls = [{
  type: 'toggle',
  name: 'order',
  label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Re-Order Posts', 'mb-custom-post-type'),
  description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Order posts of this post type using a drag and drop interface.', 'mb-custom-post-type')
}];
if (MBCPT.mbb) {
  FeatureControls.push({
    type: 'toggle',
    name: 'status_column',
    label: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add toggle status column', 'mb-custom-post-type'),
    description: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add a column to quickly toggle post status between published and draft.', 'mb-custom-post-type')
  });
}
;


/***/ }),

/***/ "./app/post-type/constants/DefaultSettings.js":
/*!****************************************************!*\
  !*** ./app/post-type/constants/DefaultSettings.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/i18n */ "@wordpress/i18n");
/* harmony import */ var _wordpress_i18n__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__);

const DefaultSettings = {
  // Custom attributes.
  slug: '',
  function_name: 'your_prefix_register_post_type',
  text_domain: 'your-textdomain',
  label: '',
  labels: {
    name: '',
    singular_name: '',
    add_new: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Add New', 'mb-custom-post-type'),
    add_new_item: '',
    edit_item: '',
    new_item: '',
    view_item: '',
    view_items: '',
    search_items: '',
    not_found: '',
    not_found_in_trash: '',
    parent_item_colon: '',
    all_items: '',
    archives: '',
    attributes: '',
    insert_into_item: '',
    uploaded_to_this_item: '',
    featured_image: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Featured image', 'mb-custom-post-type'),
    set_featured_image: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Set featured image', 'mb-custom-post-type'),
    remove_featured_image: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Remove featured image', 'mb-custom-post-type'),
    use_featured_image: (0,_wordpress_i18n__WEBPACK_IMPORTED_MODULE_0__.__)('Use as featured image', 'mb-custom-post-type'),
    menu_name: '',
    filter_items_list: '',
    filter_by_date: '',
    items_list_navigation: '',
    items_list: '',
    item_published: '',
    item_published_privately: '',
    item_reverted_to_draft: '',
    item_scheduled: '',
    item_updated: ''
  },
  description: '',
  public: true,
  hierarchical: false,
  exclude_from_search: false,
  publicly_queryable: true,
  show_ui: true,
  show_in_menu: true,
  show_in_nav_menus: true,
  show_in_admin_bar: true,
  show_in_rest: true,
  rest_base: '',
  menu_position: '',
  icon_type: 'dashicons',
  icon: 'dashicons-admin-generic',
  capability_type: 'post',
  supports: ['title', 'editor', 'thumbnail'],
  taxonomies: [],
  has_archive: true,
  archive_slug: '',
  // Custom attribute.
  rewrite: {
    slug: '',
    with_front: false
  },
  query_var: true,
  can_export: true,
  delete_with_user: true
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (DefaultSettings);

/***/ }),

/***/ "./app/post-type/constants/PhpCode.js":
/*!********************************************!*\
  !*** ./app/post-type/constants/PhpCode.js ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _code__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../../code */ "./app/code.js");

const advanced = settings => {
  const ignore = ['slug', 'function_name', 'text_domain', 'label', 'labels', 'description', 'rest_base', 'show_in_menu', 'menu_icon', 'menu_position', 'capability_type', 'has_archive', 'archive_slug', 'rewrite', 'supports', 'taxonomies', 'icon_type', 'icon', 'icon_svg', 'icon_custom', 'font_awesome'];
  let keys = Object.keys(settings).filter(key => !ignore.includes(key));
  return keys.map(key => (0,_code__WEBPACK_IMPORTED_MODULE_0__.general)(settings, key)).join(",\n\t\t");
};
const showInMenu = settings => {
  let value = settings.show_in_menu;
  value = [true, false].includes(value) ? value : `'${value}'`;
  let code = `'show_in_menu'${(0,_code__WEBPACK_IMPORTED_MODULE_0__.spaces)(settings, 'show_in_menu')} => ${value},`;
  if (value === true) {
    code += `\n\t\t${(0,_code__WEBPACK_IMPORTED_MODULE_0__.general)(settings, 'menu_position')},`;
  }
  return code;
};
const menu_icon = settings => {
  let value_type = settings.icon_type ? `'${settings.icon_type}'` : settings.icon_type;
  let value = settings.icon ? `'${settings.icon}'` : settings.icon;
  if (value_type == `'dashicons'`) {
    value = settings.icon ? `'${settings.icon}'` : settings.icon;
  } else if (value_type == `'svg'`) {
    value = settings.icon_svg ? `'${settings.icon_svg}'` : settings.icon_svg;
  } else if (value_type == `'custom'`) {
    value = settings.icon_custom ? `'${settings.icon_custom}'` : settings.icon_custom;
  } else if (value_type == `'font_awesome'`) {
    value = settings.font_awesome ? `'${settings.font_awesome}'` : settings.font_awesome;
  }
  return `'menu_icon'${(0,_code__WEBPACK_IMPORTED_MODULE_0__.spaces)(settings, 'menu_icon')} => ${value}`;
};
const archive = settings => {
  let value = settings.archive_slug ? `'${settings.archive_slug}'` : settings.has_archive;
  return `'has_archive'${(0,_code__WEBPACK_IMPORTED_MODULE_0__.spaces)(settings, 'has_archive')} => ${value}`;
};
const rewrite = settings => {
  let value = [];
  if (settings.rewrite.slug) {
    value.push((0,_code__WEBPACK_IMPORTED_MODULE_0__.text)(settings.rewrite, 'slug'));
  }
  value.push((0,_code__WEBPACK_IMPORTED_MODULE_0__.general)(settings.rewrite, 'with_front'));
  return `'rewrite'${(0,_code__WEBPACK_IMPORTED_MODULE_0__.spaces)(settings, 'rewrite')} => [
			${value.join(",\n\t\t\t")},
		]`;
};
const PhpCode = settings => {
  return `<?php
add_action( 'init', '${settings.function_name || DefaultSettings.function_name}' );
function ${settings.function_name || DefaultSettings.function_name}() {
	$labels = [
		${(0,_code__WEBPACK_IMPORTED_MODULE_0__.labels)(settings)},
	];
	$args = [
		${(0,_code__WEBPACK_IMPORTED_MODULE_0__.translatableText)(settings, 'label')},
		'labels'${(0,_code__WEBPACK_IMPORTED_MODULE_0__.spaces)(settings, 'labels')} => $labels,
		${(0,_code__WEBPACK_IMPORTED_MODULE_0__.text)(settings, 'description')},
		${advanced(settings)},
		${archive(settings)},
		${(0,_code__WEBPACK_IMPORTED_MODULE_0__.text)(settings, 'rest_base')},
		${showInMenu(settings)}
		${menu_icon(settings)},
		${(0,_code__WEBPACK_IMPORTED_MODULE_0__.text)(settings, 'capability_type')},
		${(0,_code__WEBPACK_IMPORTED_MODULE_0__.checkboxList)(settings, 'supports', false)},
		${(0,_code__WEBPACK_IMPORTED_MODULE_0__.checkboxList)(settings, 'taxonomies', '[]')},
		${rewrite(settings)},
	];

	register_post_type( '${settings.slug.replace(/\\/g, '\\\\').replace(/\'/g, '\\\'')}', $args );
}`;
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (PhpCode);

/***/ }),

/***/ "./node_modules/.pnpm/@wordpress+icons@10.7.0_react@18.3.1/node_modules/@wordpress/icons/build-module/library/code.js":
/*!****************************************************************************************************************************!*\
  !*** ./node_modules/.pnpm/@wordpress+icons@10.7.0_react@18.3.1/node_modules/@wordpress/icons/build-module/library/code.js ***!
  \****************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/primitives */ "@wordpress/primitives");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js");
/**
 * WordPress dependencies
 */


const code = /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_0__.SVG, {
  viewBox: "0 0 24 24",
  xmlns: "http://www.w3.org/2000/svg",
  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_0__.Path, {
    d: "M20.8 10.7l-4.3-4.3-1.1 1.1 4.3 4.3c.1.1.1.3 0 .4l-4.3 4.3 1.1 1.1 4.3-4.3c.7-.8.7-1.9 0-2.6zM4.2 11.8l4.3-4.3-1-1-4.3 4.3c-.7.7-.7 1.8 0 2.5l4.3 4.3 1.1-1.1-4.3-4.3c-.2-.1-.2-.3-.1-.4z"
  })
});
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (code);
//# sourceMappingURL=code.js.map

/***/ }),

/***/ "./node_modules/.pnpm/@wordpress+icons@10.7.0_react@18.3.1/node_modules/@wordpress/icons/build-module/library/external.js":
/*!********************************************************************************************************************************!*\
  !*** ./node_modules/.pnpm/@wordpress+icons@10.7.0_react@18.3.1/node_modules/@wordpress/icons/build-module/library/external.js ***!
  \********************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/primitives */ "@wordpress/primitives");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! react/jsx-runtime */ "./node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js");
/**
 * WordPress dependencies
 */


const external = /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_0__.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_1__.jsx)(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_0__.Path, {
    d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"
  })
});
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (external);
//# sourceMappingURL=external.js.map

/***/ }),

/***/ "./node_modules/.pnpm/dot-prop@5.3.0/node_modules/dot-prop/index.js":
/*!**************************************************************************!*\
  !*** ./node_modules/.pnpm/dot-prop@5.3.0/node_modules/dot-prop/index.js ***!
  \**************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

const isObj = __webpack_require__(/*! is-obj */ "./node_modules/.pnpm/is-obj@2.0.0/node_modules/is-obj/index.js");

const disallowedKeys = [
	'__proto__',
	'prototype',
	'constructor'
];

const isValidPath = pathSegments => !pathSegments.some(segment => disallowedKeys.includes(segment));

function getPathSegments(path) {
	const pathArray = path.split('.');
	const parts = [];

	for (let i = 0; i < pathArray.length; i++) {
		let p = pathArray[i];

		while (p[p.length - 1] === '\\' && pathArray[i + 1] !== undefined) {
			p = p.slice(0, -1) + '.';
			p += pathArray[++i];
		}

		parts.push(p);
	}

	if (!isValidPath(parts)) {
		return [];
	}

	return parts;
}

module.exports = {
	get(object, path, value) {
		if (!isObj(object) || typeof path !== 'string') {
			return value === undefined ? object : value;
		}

		const pathArray = getPathSegments(path);
		if (pathArray.length === 0) {
			return;
		}

		for (let i = 0; i < pathArray.length; i++) {
			if (!Object.prototype.propertyIsEnumerable.call(object, pathArray[i])) {
				return value;
			}

			object = object[pathArray[i]];

			if (object === undefined || object === null) {
				// `object` is either `undefined` or `null` so we want to stop the loop, and
				// if this is not the last bit of the path, and
				// if it did't return `undefined`
				// it would return `null` if `object` is `null`
				// but we want `get({foo: null}, 'foo.bar')` to equal `undefined`, or the supplied value, not `null`
				if (i !== pathArray.length - 1) {
					return value;
				}

				break;
			}
		}

		return object;
	},

	set(object, path, value) {
		if (!isObj(object) || typeof path !== 'string') {
			return object;
		}

		const root = object;
		const pathArray = getPathSegments(path);

		for (let i = 0; i < pathArray.length; i++) {
			const p = pathArray[i];

			if (!isObj(object[p])) {
				object[p] = {};
			}

			if (i === pathArray.length - 1) {
				object[p] = value;
			}

			object = object[p];
		}

		return root;
	},

	delete(object, path) {
		if (!isObj(object) || typeof path !== 'string') {
			return false;
		}

		const pathArray = getPathSegments(path);

		for (let i = 0; i < pathArray.length; i++) {
			const p = pathArray[i];

			if (i === pathArray.length - 1) {
				delete object[p];
				return true;
			}

			object = object[p];

			if (!isObj(object)) {
				return false;
			}
		}
	},

	has(object, path) {
		if (!isObj(object) || typeof path !== 'string') {
			return false;
		}

		const pathArray = getPathSegments(path);
		if (pathArray.length === 0) {
			return false;
		}

		// eslint-disable-next-line unicorn/no-for-loop
		for (let i = 0; i < pathArray.length; i++) {
			if (isObj(object)) {
				if (!(pathArray[i] in object)) {
					return false;
				}

				object = object[pathArray[i]];
			} else {
				return false;
			}
		}

		return true;
	}
};


/***/ }),

/***/ "./node_modules/.pnpm/is-obj@2.0.0/node_modules/is-obj/index.js":
/*!**********************************************************************!*\
  !*** ./node_modules/.pnpm/is-obj@2.0.0/node_modules/is-obj/index.js ***!
  \**********************************************************************/
/***/ ((module) => {

"use strict";


module.exports = value => {
	const type = typeof value;
	return value !== null && (type === 'object' || type === 'function');
};


/***/ }),

/***/ "./node_modules/.pnpm/react-codemirror2@7.3.0_codemirror@5.65.20_react@18.3.1/node_modules/react-codemirror2/index.js":
/*!****************************************************************************************************************************!*\
  !*** ./node_modules/.pnpm/react-codemirror2@7.3.0_codemirror@5.65.20_react@18.3.1/node_modules/react-codemirror2/index.js ***!
  \****************************************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";


function _extends() {
  _extends = Object.assign || function(target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];
      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }
    return target;
  };
  return _extends.apply(this, arguments);
}

function _typeof(obj) {
  "@babel/helpers - typeof";
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }
  return _typeof(obj);
}

var __extends = void 0 && (void 0).__extends || function() {
  var _extendStatics = function extendStatics(d, b) {
    _extendStatics = Object.setPrototypeOf || {
      __proto__: []
    }
    instanceof Array && function(d, b) {
      d.__proto__ = b;
    } || function(d, b) {
      for (var p in b) {
        if (b.hasOwnProperty(p)) d[p] = b[p];
      }
    };

    return _extendStatics(d, b);
  };

  return function(d, b) {
    _extendStatics(d, b);

    function __() {
      this.constructor = d;
    }

    d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
  };
}();

Object.defineProperty(exports, "__esModule", ({
  value: true
}));
exports.UnControlled = exports.Controlled = void 0;

var React = __webpack_require__(/*! react */ "react");

var SERVER_RENDERED = typeof navigator === 'undefined' || typeof __webpack_require__.g !== 'undefined' && __webpack_require__.g['PREVENT_CODEMIRROR_RENDER'] === true;
var cm;

if (!SERVER_RENDERED) {
  cm = __webpack_require__(/*! codemirror */ "codemirror");
}

var Helper = function() {
  function Helper() {}

  Helper.equals = function(x, y) {
    var _this = this;

    var ok = Object.keys,
      tx = _typeof(x),
      ty = _typeof(y);

    return x && y && tx === 'object' && tx === ty ? ok(x).length === ok(y).length && ok(x).every(function(key) {
      return _this.equals(x[key], y[key]);
    }) : x === y;
  };

  return Helper;
}();

var Shared = function() {
  function Shared(editor, props) {
    this.editor = editor;
    this.props = props;
  }

  Shared.prototype.delegateCursor = function(position, scroll, focus) {
    var doc = this.editor.getDoc();

    if (focus) {
      this.editor.focus();
    }

    scroll ? doc.setCursor(position) : doc.setCursor(position, null, {
      scroll: false
    });
  };

  Shared.prototype.delegateScroll = function(coordinates) {
    this.editor.scrollTo(coordinates.x, coordinates.y);
  };

  Shared.prototype.delegateSelection = function(ranges, focus) {
    var doc = this.editor.getDoc();
    doc.setSelections(ranges);

    if (focus) {
      this.editor.focus();
    }
  };

  Shared.prototype.apply = function(props) {
    if (props && props.selection && props.selection.ranges) {
      this.delegateSelection(props.selection.ranges, props.selection.focus || false);
    }

    if (props && props.cursor) {
      this.delegateCursor(props.cursor, props.autoScroll || false, this.editor.getOption('autofocus') || false);
    }

    if (props && props.scroll) {
      this.delegateScroll(props.scroll);
    }
  };

  Shared.prototype.applyNext = function(props, next, preserved) {
    if (props && props.selection && props.selection.ranges) {
      if (next && next.selection && next.selection.ranges && !Helper.equals(props.selection.ranges, next.selection.ranges)) {
        this.delegateSelection(next.selection.ranges, next.selection.focus || false);
      }
    }

    if (props && props.cursor) {
      if (next && next.cursor && !Helper.equals(props.cursor, next.cursor)) {
        this.delegateCursor(preserved.cursor || next.cursor, next.autoScroll || false, next.autoCursor || false);
      }
    }

    if (props && props.scroll) {
      if (next && next.scroll && !Helper.equals(props.scroll, next.scroll)) {
        this.delegateScroll(next.scroll);
      }
    }
  };

  Shared.prototype.applyUserDefined = function(props, preserved) {
    if (preserved && preserved.cursor) {
      this.delegateCursor(preserved.cursor, props.autoScroll || false, this.editor.getOption('autofocus') || false);
    }
  };

  Shared.prototype.wire = function(props) {
    var _this = this;

    Object.keys(props || {}).filter(function(p) {
      return /^on/.test(p);
    }).forEach(function(prop) {
      switch (prop) {
        case 'onBlur': {
          _this.editor.on('blur', function(cm, event) {
            _this.props.onBlur(_this.editor, event);
          });
        }
        break;

      case 'onContextMenu': {
        _this.editor.on('contextmenu', function(cm, event) {
          _this.props.onContextMenu(_this.editor, event);
        });

        break;
      }

      case 'onCopy': {
        _this.editor.on('copy', function(cm, event) {
          _this.props.onCopy(_this.editor, event);
        });

        break;
      }

      case 'onCursor': {
        _this.editor.on('cursorActivity', function(cm) {
          _this.props.onCursor(_this.editor, _this.editor.getDoc().getCursor());
        });
      }
      break;

      case 'onCursorActivity': {
        _this.editor.on('cursorActivity', function(cm) {
          _this.props.onCursorActivity(_this.editor);
        });
      }
      break;

      case 'onCut': {
        _this.editor.on('cut', function(cm, event) {
          _this.props.onCut(_this.editor, event);
        });

        break;
      }

      case 'onDblClick': {
        _this.editor.on('dblclick', function(cm, event) {
          _this.props.onDblClick(_this.editor, event);
        });

        break;
      }

      case 'onDragEnter': {
        _this.editor.on('dragenter', function(cm, event) {
          _this.props.onDragEnter(_this.editor, event);
        });
      }
      break;

      case 'onDragLeave': {
        _this.editor.on('dragleave', function(cm, event) {
          _this.props.onDragLeave(_this.editor, event);
        });

        break;
      }

      case 'onDragOver': {
        _this.editor.on('dragover', function(cm, event) {
          _this.props.onDragOver(_this.editor, event);
        });
      }
      break;

      case 'onDragStart': {
        _this.editor.on('dragstart', function(cm, event) {
          _this.props.onDragStart(_this.editor, event);
        });

        break;
      }

      case 'onDrop': {
        _this.editor.on('drop', function(cm, event) {
          _this.props.onDrop(_this.editor, event);
        });
      }
      break;

      case 'onFocus': {
        _this.editor.on('focus', function(cm, event) {
          _this.props.onFocus(_this.editor, event);
        });
      }
      break;

      case 'onGutterClick': {
        _this.editor.on('gutterClick', function(cm, lineNumber, gutter, event) {
          _this.props.onGutterClick(_this.editor, lineNumber, gutter, event);
        });
      }
      break;

      case 'onInputRead': {
        _this.editor.on('inputRead', function(cm, EditorChangeEvent) {
          _this.props.onInputRead(_this.editor, EditorChangeEvent);
        });
      }
      break;

      case 'onKeyDown': {
        _this.editor.on('keydown', function(cm, event) {
          _this.props.onKeyDown(_this.editor, event);
        });
      }
      break;

      case 'onKeyHandled': {
        _this.editor.on('keyHandled', function(cm, key, event) {
          _this.props.onKeyHandled(_this.editor, key, event);
        });
      }
      break;

      case 'onKeyPress': {
        _this.editor.on('keypress', function(cm, event) {
          _this.props.onKeyPress(_this.editor, event);
        });
      }
      break;

      case 'onKeyUp': {
        _this.editor.on('keyup', function(cm, event) {
          _this.props.onKeyUp(_this.editor, event);
        });
      }
      break;

      case 'onMouseDown': {
        _this.editor.on('mousedown', function(cm, event) {
          _this.props.onMouseDown(_this.editor, event);
        });

        break;
      }

      case 'onPaste': {
        _this.editor.on('paste', function(cm, event) {
          _this.props.onPaste(_this.editor, event);
        });

        break;
      }

      case 'onRenderLine': {
        _this.editor.on('renderLine', function(cm, line, element) {
          _this.props.onRenderLine(_this.editor, line, element);
        });

        break;
      }

      case 'onScroll': {
        _this.editor.on('scroll', function(cm) {
          _this.props.onScroll(_this.editor, _this.editor.getScrollInfo());
        });
      }
      break;

      case 'onSelection': {
        _this.editor.on('beforeSelectionChange', function(cm, data) {
          _this.props.onSelection(_this.editor, data);
        });
      }
      break;

      case 'onTouchStart': {
        _this.editor.on('touchstart', function(cm, event) {
          _this.props.onTouchStart(_this.editor, event);
        });

        break;
      }

      case 'onUpdate': {
        _this.editor.on('update', function(cm) {
          _this.props.onUpdate(_this.editor);
        });
      }
      break;

      case 'onViewportChange': {
        _this.editor.on('viewportChange', function(cm, from, to) {
          _this.props.onViewportChange(_this.editor, from, to);
        });
      }
      break;
      }
    });
  };

  return Shared;
}();

var Controlled = function(_super) {
  __extends(Controlled, _super);

  function Controlled(props) {
    var _this = _super.call(this, props) || this;

    if (SERVER_RENDERED) return _this;
    _this.applied = false;
    _this.appliedNext = false;
    _this.appliedUserDefined = false;
    _this.deferred = null;
    _this.emulating = false;
    _this.hydrated = false;

    _this.initCb = function() {
      if (_this.props.editorDidConfigure) {
        _this.props.editorDidConfigure(_this.editor);
      }
    };

    _this.mounted = false;
    return _this;
  }

  Controlled.prototype.hydrate = function(props) {
    var _this = this;

    var _options = props && props.options ? props.options : {};

    var userDefinedOptions = _extends({}, cm.defaults, this.editor.options, _options);

    var optionDelta = Object.keys(userDefinedOptions).some(function(key) {
      return _this.editor.getOption(key) !== userDefinedOptions[key];
    });

    if (optionDelta) {
      Object.keys(userDefinedOptions).forEach(function(key) {
        if (_options.hasOwnProperty(key)) {
          if (_this.editor.getOption(key) !== userDefinedOptions[key]) {
            _this.editor.setOption(key, userDefinedOptions[key]);

            _this.mirror.setOption(key, userDefinedOptions[key]);
          }
        }
      });
    }

    if (!this.hydrated) {
      this.deferred ? this.resolveChange(props.value) : this.initChange(props.value || '');
    }

    this.hydrated = true;
  };

  Controlled.prototype.initChange = function(value) {
    this.emulating = true;
    var doc = this.editor.getDoc();
    var lastLine = doc.lastLine();
    var lastChar = doc.getLine(doc.lastLine()).length;
    doc.replaceRange(value || '', {
      line: 0,
      ch: 0
    }, {
      line: lastLine,
      ch: lastChar
    });
    this.mirror.setValue(value);
    doc.clearHistory();
    this.mirror.clearHistory();
    this.emulating = false;
  };

  Controlled.prototype.resolveChange = function(value) {
    this.emulating = true;
    var doc = this.editor.getDoc();

    if (this.deferred.origin === 'undo') {
      doc.undo();
    } else if (this.deferred.origin === 'redo') {
      doc.redo();
    } else {
      doc.replaceRange(this.deferred.text, this.deferred.from, this.deferred.to, this.deferred.origin);
    }

    if (value && value !== doc.getValue()) {
      var cursor = doc.getCursor();
      doc.setValue(value);
      doc.setCursor(cursor);
    }

    this.emulating = false;
    this.deferred = null;
  };

  Controlled.prototype.mirrorChange = function(deferred) {
    var doc = this.editor.getDoc();

    if (deferred.origin === 'undo') {
      doc.setHistory(this.mirror.getHistory());
      this.mirror.undo();
    } else if (deferred.origin === 'redo') {
      doc.setHistory(this.mirror.getHistory());
      this.mirror.redo();
    } else {
      this.mirror.replaceRange(deferred.text, deferred.from, deferred.to, deferred.origin);
    }

    return this.mirror.getValue();
  };

  Controlled.prototype.componentDidMount = function() {
    var _this = this;

    if (SERVER_RENDERED) return;

    if (this.props.defineMode) {
      if (this.props.defineMode.name && this.props.defineMode.fn) {
        cm.defineMode(this.props.defineMode.name, this.props.defineMode.fn);
      }
    }

    this.editor = cm(this.ref, this.props.options);
    this.shared = new Shared(this.editor, this.props);
    this.mirror = cm(function() {}, this.props.options);
    this.editor.on('electricInput', function() {
      _this.mirror.setHistory(_this.editor.getDoc().getHistory());
    });
    this.editor.on('cursorActivity', function() {
      _this.mirror.setCursor(_this.editor.getDoc().getCursor());
    });
    this.editor.on('beforeChange', function(cm, data) {
      if (_this.emulating) {
        return;
      }

      data.cancel();
      _this.deferred = data;

      var phantomChange = _this.mirrorChange(_this.deferred);

      if (_this.props.onBeforeChange) _this.props.onBeforeChange(_this.editor, _this.deferred, phantomChange);
    });
    this.editor.on('change', function(cm, data) {
      if (!_this.mounted) {
        return;
      }

      if (_this.props.onChange) {
        _this.props.onChange(_this.editor, data, _this.editor.getValue());
      }
    });
    this.hydrate(this.props);
    this.shared.apply(this.props);
    this.applied = true;
    this.mounted = true;
    this.shared.wire(this.props);

    if (this.editor.getOption('autofocus')) {
      this.editor.focus();
    }

    if (this.props.editorDidMount) {
      this.props.editorDidMount(this.editor, this.editor.getValue(), this.initCb);
    }
  };

  Controlled.prototype.componentDidUpdate = function(prevProps) {
    if (SERVER_RENDERED) return;
    var preserved = {
      cursor: null
    };

    if (this.props.value !== prevProps.value) {
      this.hydrated = false;
    }

    if (!this.props.autoCursor && this.props.autoCursor !== undefined) {
      preserved.cursor = this.editor.getDoc().getCursor();
    }

    this.hydrate(this.props);

    if (!this.appliedNext) {
      this.shared.applyNext(prevProps, this.props, preserved);
      this.appliedNext = true;
    }

    this.shared.applyUserDefined(prevProps, preserved);
    this.appliedUserDefined = true;
  };

  Controlled.prototype.componentWillUnmount = function() {
    if (SERVER_RENDERED) return;

    if (this.props.editorWillUnmount) {
      this.props.editorWillUnmount(cm);
    }
  };

  Controlled.prototype.shouldComponentUpdate = function(nextProps, nextState) {
    return !SERVER_RENDERED;
  };

  Controlled.prototype.render = function() {
    var _this = this;

    if (SERVER_RENDERED) return null;
    var className = this.props.className ? 'react-codemirror2 ' + this.props.className : 'react-codemirror2';
    return React.createElement('div', {
      className: className,
      ref: function ref(self) {
        return _this.ref = self;
      }
    });
  };

  return Controlled;
}(React.Component);

exports.Controlled = Controlled;

var UnControlled = function(_super) {
  __extends(UnControlled, _super);

  function UnControlled(props) {
    var _this = _super.call(this, props) || this;

    if (SERVER_RENDERED) return _this;
    _this.applied = false;
    _this.appliedUserDefined = false;
    _this.continueChange = false;
    _this.detached = false;
    _this.hydrated = false;

    _this.initCb = function() {
      if (_this.props.editorDidConfigure) {
        _this.props.editorDidConfigure(_this.editor);
      }
    };

    _this.mounted = false;

    _this.onBeforeChangeCb = function() {
      _this.continueChange = true;
    };

    return _this;
  }

  UnControlled.prototype.hydrate = function(props) {
    var _this = this;

    var _options = props && props.options ? props.options : {};

    var userDefinedOptions = _extends({}, cm.defaults, this.editor.options, _options);

    var optionDelta = Object.keys(userDefinedOptions).some(function(key) {
      return _this.editor.getOption(key) !== userDefinedOptions[key];
    });

    if (optionDelta) {
      Object.keys(userDefinedOptions).forEach(function(key) {
        if (_options.hasOwnProperty(key)) {
          if (_this.editor.getOption(key) !== userDefinedOptions[key]) {
            _this.editor.setOption(key, userDefinedOptions[key]);
          }
        }
      });
    }

    if (!this.hydrated) {
      var doc = this.editor.getDoc();
      var lastLine = doc.lastLine();
      var lastChar = doc.getLine(doc.lastLine()).length;
      doc.replaceRange(props.value || '', {
        line: 0,
        ch: 0
      }, {
        line: lastLine,
        ch: lastChar
      });
    }

    this.hydrated = true;
  };

  UnControlled.prototype.componentDidMount = function() {
    var _this = this;

    if (SERVER_RENDERED) return;
    this.detached = this.props.detach === true;

    if (this.props.defineMode) {
      if (this.props.defineMode.name && this.props.defineMode.fn) {
        cm.defineMode(this.props.defineMode.name, this.props.defineMode.fn);
      }
    }

    this.editor = cm(this.ref, this.props.options);
    this.shared = new Shared(this.editor, this.props);
    this.editor.on('beforeChange', function(cm, data) {
      if (_this.props.onBeforeChange) {
        _this.props.onBeforeChange(_this.editor, data, _this.editor.getValue(), _this.onBeforeChangeCb);
      }
    });
    this.editor.on('change', function(cm, data) {
      if (!_this.mounted || !_this.props.onChange) {
        return;
      }

      if (_this.props.onBeforeChange) {
        if (_this.continueChange) {
          _this.props.onChange(_this.editor, data, _this.editor.getValue());
        }
      } else {
        _this.props.onChange(_this.editor, data, _this.editor.getValue());
      }
    });
    this.hydrate(this.props);
    this.shared.apply(this.props);
    this.applied = true;
    this.mounted = true;
    this.shared.wire(this.props);
    this.editor.getDoc().clearHistory();

    if (this.props.editorDidMount) {
      this.props.editorDidMount(this.editor, this.editor.getValue(), this.initCb);
    }
  };

  UnControlled.prototype.componentDidUpdate = function(prevProps) {
    if (this.detached && this.props.detach === false) {
      this.detached = false;

      if (prevProps.editorDidAttach) {
        prevProps.editorDidAttach(this.editor);
      }
    }

    if (!this.detached && this.props.detach === true) {
      this.detached = true;

      if (prevProps.editorDidDetach) {
        prevProps.editorDidDetach(this.editor);
      }
    }

    if (SERVER_RENDERED || this.detached) return;
    var preserved = {
      cursor: null
    };

    if (this.props.value !== prevProps.value) {
      this.hydrated = false;
      this.applied = false;
      this.appliedUserDefined = false;
    }

    if (!prevProps.autoCursor && prevProps.autoCursor !== undefined) {
      preserved.cursor = this.editor.getDoc().getCursor();
    }

    this.hydrate(this.props);

    if (!this.applied) {
      this.shared.apply(prevProps);
      this.applied = true;
    }

    if (!this.appliedUserDefined) {
      this.shared.applyUserDefined(prevProps, preserved);
      this.appliedUserDefined = true;
    }
  };

  UnControlled.prototype.componentWillUnmount = function() {
    if (SERVER_RENDERED) return;

    if (this.props.editorWillUnmount) {
      this.props.editorWillUnmount(cm);
    }
  };

  UnControlled.prototype.shouldComponentUpdate = function(nextProps, nextState) {
    var update = true;
    if (SERVER_RENDERED) update = false;
    if (this.detached && nextProps.detach) update = false;
    return update;
  };

  UnControlled.prototype.render = function() {
    var _this = this;

    if (SERVER_RENDERED) return null;
    var className = this.props.className ? 'react-codemirror2 ' + this.props.className : 'react-codemirror2';
    return React.createElement('div', {
      className: className,
      ref: function ref(self) {
        return _this.ref = self;
      }
    });
  };

  return UnControlled;
}(React.Component);

exports.UnControlled = UnControlled;

/***/ }),

/***/ "./node_modules/.pnpm/react@18.3.1/node_modules/react/cjs/react-jsx-runtime.development.js":
/*!*************************************************************************************************!*\
  !*** ./node_modules/.pnpm/react@18.3.1/node_modules/react/cjs/react-jsx-runtime.development.js ***!
  \*************************************************************************************************/
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {

"use strict";
/**
 * @license React
 * react-jsx-runtime.development.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */



if (true) {
  (function() {
'use strict';

var React = __webpack_require__(/*! react */ "react");

// ATTENTION
// When adding new symbols to this file,
// Please consider also adding to 'react-devtools-shared/src/backend/ReactSymbols'
// The Symbol used to tag the ReactElement-like types.
var REACT_ELEMENT_TYPE = Symbol.for('react.element');
var REACT_PORTAL_TYPE = Symbol.for('react.portal');
var REACT_FRAGMENT_TYPE = Symbol.for('react.fragment');
var REACT_STRICT_MODE_TYPE = Symbol.for('react.strict_mode');
var REACT_PROFILER_TYPE = Symbol.for('react.profiler');
var REACT_PROVIDER_TYPE = Symbol.for('react.provider');
var REACT_CONTEXT_TYPE = Symbol.for('react.context');
var REACT_FORWARD_REF_TYPE = Symbol.for('react.forward_ref');
var REACT_SUSPENSE_TYPE = Symbol.for('react.suspense');
var REACT_SUSPENSE_LIST_TYPE = Symbol.for('react.suspense_list');
var REACT_MEMO_TYPE = Symbol.for('react.memo');
var REACT_LAZY_TYPE = Symbol.for('react.lazy');
var REACT_OFFSCREEN_TYPE = Symbol.for('react.offscreen');
var MAYBE_ITERATOR_SYMBOL = Symbol.iterator;
var FAUX_ITERATOR_SYMBOL = '@@iterator';
function getIteratorFn(maybeIterable) {
  if (maybeIterable === null || typeof maybeIterable !== 'object') {
    return null;
  }

  var maybeIterator = MAYBE_ITERATOR_SYMBOL && maybeIterable[MAYBE_ITERATOR_SYMBOL] || maybeIterable[FAUX_ITERATOR_SYMBOL];

  if (typeof maybeIterator === 'function') {
    return maybeIterator;
  }

  return null;
}

var ReactSharedInternals = React.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED;

function error(format) {
  {
    {
      for (var _len2 = arguments.length, args = new Array(_len2 > 1 ? _len2 - 1 : 0), _key2 = 1; _key2 < _len2; _key2++) {
        args[_key2 - 1] = arguments[_key2];
      }

      printWarning('error', format, args);
    }
  }
}

function printWarning(level, format, args) {
  // When changing this logic, you might want to also
  // update consoleWithStackDev.www.js as well.
  {
    var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;
    var stack = ReactDebugCurrentFrame.getStackAddendum();

    if (stack !== '') {
      format += '%s';
      args = args.concat([stack]);
    } // eslint-disable-next-line react-internal/safe-string-coercion


    var argsWithFormat = args.map(function (item) {
      return String(item);
    }); // Careful: RN currently depends on this prefix

    argsWithFormat.unshift('Warning: ' + format); // We intentionally don't use spread (or .apply) directly because it
    // breaks IE9: https://github.com/facebook/react/issues/13610
    // eslint-disable-next-line react-internal/no-production-logging

    Function.prototype.apply.call(console[level], console, argsWithFormat);
  }
}

// -----------------------------------------------------------------------------

var enableScopeAPI = false; // Experimental Create Event Handle API.
var enableCacheElement = false;
var enableTransitionTracing = false; // No known bugs, but needs performance testing

var enableLegacyHidden = false; // Enables unstable_avoidThisFallback feature in Fiber
// stuff. Intended to enable React core members to more easily debug scheduling
// issues in DEV builds.

var enableDebugTracing = false; // Track which Fiber(s) schedule render work.

var REACT_MODULE_REFERENCE;

{
  REACT_MODULE_REFERENCE = Symbol.for('react.module.reference');
}

function isValidElementType(type) {
  if (typeof type === 'string' || typeof type === 'function') {
    return true;
  } // Note: typeof might be other than 'symbol' or 'number' (e.g. if it's a polyfill).


  if (type === REACT_FRAGMENT_TYPE || type === REACT_PROFILER_TYPE || enableDebugTracing  || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || enableLegacyHidden  || type === REACT_OFFSCREEN_TYPE || enableScopeAPI  || enableCacheElement  || enableTransitionTracing ) {
    return true;
  }

  if (typeof type === 'object' && type !== null) {
    if (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_PROVIDER_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || // This needs to include all possible module reference object
    // types supported by any Flight configuration anywhere since
    // we don't know which Flight build this will end up being used
    // with.
    type.$$typeof === REACT_MODULE_REFERENCE || type.getModuleId !== undefined) {
      return true;
    }
  }

  return false;
}

function getWrappedName(outerType, innerType, wrapperName) {
  var displayName = outerType.displayName;

  if (displayName) {
    return displayName;
  }

  var functionName = innerType.displayName || innerType.name || '';
  return functionName !== '' ? wrapperName + "(" + functionName + ")" : wrapperName;
} // Keep in sync with react-reconciler/getComponentNameFromFiber


function getContextName(type) {
  return type.displayName || 'Context';
} // Note that the reconciler package should generally prefer to use getComponentNameFromFiber() instead.


function getComponentNameFromType(type) {
  if (type == null) {
    // Host root, text node or just invalid type.
    return null;
  }

  {
    if (typeof type.tag === 'number') {
      error('Received an unexpected object in getComponentNameFromType(). ' + 'This is likely a bug in React. Please file an issue.');
    }
  }

  if (typeof type === 'function') {
    return type.displayName || type.name || null;
  }

  if (typeof type === 'string') {
    return type;
  }

  switch (type) {
    case REACT_FRAGMENT_TYPE:
      return 'Fragment';

    case REACT_PORTAL_TYPE:
      return 'Portal';

    case REACT_PROFILER_TYPE:
      return 'Profiler';

    case REACT_STRICT_MODE_TYPE:
      return 'StrictMode';

    case REACT_SUSPENSE_TYPE:
      return 'Suspense';

    case REACT_SUSPENSE_LIST_TYPE:
      return 'SuspenseList';

  }

  if (typeof type === 'object') {
    switch (type.$$typeof) {
      case REACT_CONTEXT_TYPE:
        var context = type;
        return getContextName(context) + '.Consumer';

      case REACT_PROVIDER_TYPE:
        var provider = type;
        return getContextName(provider._context) + '.Provider';

      case REACT_FORWARD_REF_TYPE:
        return getWrappedName(type, type.render, 'ForwardRef');

      case REACT_MEMO_TYPE:
        var outerName = type.displayName || null;

        if (outerName !== null) {
          return outerName;
        }

        return getComponentNameFromType(type.type) || 'Memo';

      case REACT_LAZY_TYPE:
        {
          var lazyComponent = type;
          var payload = lazyComponent._payload;
          var init = lazyComponent._init;

          try {
            return getComponentNameFromType(init(payload));
          } catch (x) {
            return null;
          }
        }

      // eslint-disable-next-line no-fallthrough
    }
  }

  return null;
}

var assign = Object.assign;

// Helpers to patch console.logs to avoid logging during side-effect free
// replaying on render function. This currently only patches the object
// lazily which won't cover if the log function was extracted eagerly.
// We could also eagerly patch the method.
var disabledDepth = 0;
var prevLog;
var prevInfo;
var prevWarn;
var prevError;
var prevGroup;
var prevGroupCollapsed;
var prevGroupEnd;

function disabledLog() {}

disabledLog.__reactDisabledLog = true;
function disableLogs() {
  {
    if (disabledDepth === 0) {
      /* eslint-disable react-internal/no-production-logging */
      prevLog = console.log;
      prevInfo = console.info;
      prevWarn = console.warn;
      prevError = console.error;
      prevGroup = console.group;
      prevGroupCollapsed = console.groupCollapsed;
      prevGroupEnd = console.groupEnd; // https://github.com/facebook/react/issues/19099

      var props = {
        configurable: true,
        enumerable: true,
        value: disabledLog,
        writable: true
      }; // $FlowFixMe Flow thinks console is immutable.

      Object.defineProperties(console, {
        info: props,
        log: props,
        warn: props,
        error: props,
        group: props,
        groupCollapsed: props,
        groupEnd: props
      });
      /* eslint-enable react-internal/no-production-logging */
    }

    disabledDepth++;
  }
}
function reenableLogs() {
  {
    disabledDepth--;

    if (disabledDepth === 0) {
      /* eslint-disable react-internal/no-production-logging */
      var props = {
        configurable: true,
        enumerable: true,
        writable: true
      }; // $FlowFixMe Flow thinks console is immutable.

      Object.defineProperties(console, {
        log: assign({}, props, {
          value: prevLog
        }),
        info: assign({}, props, {
          value: prevInfo
        }),
        warn: assign({}, props, {
          value: prevWarn
        }),
        error: assign({}, props, {
          value: prevError
        }),
        group: assign({}, props, {
          value: prevGroup
        }),
        groupCollapsed: assign({}, props, {
          value: prevGroupCollapsed
        }),
        groupEnd: assign({}, props, {
          value: prevGroupEnd
        })
      });
      /* eslint-enable react-internal/no-production-logging */
    }

    if (disabledDepth < 0) {
      error('disabledDepth fell below zero. ' + 'This is a bug in React. Please file an issue.');
    }
  }
}

var ReactCurrentDispatcher = ReactSharedInternals.ReactCurrentDispatcher;
var prefix;
function describeBuiltInComponentFrame(name, source, ownerFn) {
  {
    if (prefix === undefined) {
      // Extract the VM specific prefix used by each line.
      try {
        throw Error();
      } catch (x) {
        var match = x.stack.trim().match(/\n( *(at )?)/);
        prefix = match && match[1] || '';
      }
    } // We use the prefix to ensure our stacks line up with native stack frames.


    return '\n' + prefix + name;
  }
}
var reentry = false;
var componentFrameCache;

{
  var PossiblyWeakMap = typeof WeakMap === 'function' ? WeakMap : Map;
  componentFrameCache = new PossiblyWeakMap();
}

function describeNativeComponentFrame(fn, construct) {
  // If something asked for a stack inside a fake render, it should get ignored.
  if ( !fn || reentry) {
    return '';
  }

  {
    var frame = componentFrameCache.get(fn);

    if (frame !== undefined) {
      return frame;
    }
  }

  var control;
  reentry = true;
  var previousPrepareStackTrace = Error.prepareStackTrace; // $FlowFixMe It does accept undefined.

  Error.prepareStackTrace = undefined;
  var previousDispatcher;

  {
    previousDispatcher = ReactCurrentDispatcher.current; // Set the dispatcher in DEV because this might be call in the render function
    // for warnings.

    ReactCurrentDispatcher.current = null;
    disableLogs();
  }

  try {
    // This should throw.
    if (construct) {
      // Something should be setting the props in the constructor.
      var Fake = function () {
        throw Error();
      }; // $FlowFixMe


      Object.defineProperty(Fake.prototype, 'props', {
        set: function () {
          // We use a throwing setter instead of frozen or non-writable props
          // because that won't throw in a non-strict mode function.
          throw Error();
        }
      });

      if (typeof Reflect === 'object' && Reflect.construct) {
        // We construct a different control for this case to include any extra
        // frames added by the construct call.
        try {
          Reflect.construct(Fake, []);
        } catch (x) {
          control = x;
        }

        Reflect.construct(fn, [], Fake);
      } else {
        try {
          Fake.call();
        } catch (x) {
          control = x;
        }

        fn.call(Fake.prototype);
      }
    } else {
      try {
        throw Error();
      } catch (x) {
        control = x;
      }

      fn();
    }
  } catch (sample) {
    // This is inlined manually because closure doesn't do it for us.
    if (sample && control && typeof sample.stack === 'string') {
      // This extracts the first frame from the sample that isn't also in the control.
      // Skipping one frame that we assume is the frame that calls the two.
      var sampleLines = sample.stack.split('\n');
      var controlLines = control.stack.split('\n');
      var s = sampleLines.length - 1;
      var c = controlLines.length - 1;

      while (s >= 1 && c >= 0 && sampleLines[s] !== controlLines[c]) {
        // We expect at least one stack frame to be shared.
        // Typically this will be the root most one. However, stack frames may be
        // cut off due to maximum stack limits. In this case, one maybe cut off
        // earlier than the other. We assume that the sample is longer or the same
        // and there for cut off earlier. So we should find the root most frame in
        // the sample somewhere in the control.
        c--;
      }

      for (; s >= 1 && c >= 0; s--, c--) {
        // Next we find the first one that isn't the same which should be the
        // frame that called our sample function and the control.
        if (sampleLines[s] !== controlLines[c]) {
          // In V8, the first line is describing the message but other VMs don't.
          // If we're about to return the first line, and the control is also on the same
          // line, that's a pretty good indicator that our sample threw at same line as
          // the control. I.e. before we entered the sample frame. So we ignore this result.
          // This can happen if you passed a class to function component, or non-function.
          if (s !== 1 || c !== 1) {
            do {
              s--;
              c--; // We may still have similar intermediate frames from the construct call.
              // The next one that isn't the same should be our match though.

              if (c < 0 || sampleLines[s] !== controlLines[c]) {
                // V8 adds a "new" prefix for native classes. Let's remove it to make it prettier.
                var _frame = '\n' + sampleLines[s].replace(' at new ', ' at '); // If our component frame is labeled "<anonymous>"
                // but we have a user-provided "displayName"
                // splice it in to make the stack more readable.


                if (fn.displayName && _frame.includes('<anonymous>')) {
                  _frame = _frame.replace('<anonymous>', fn.displayName);
                }

                {
                  if (typeof fn === 'function') {
                    componentFrameCache.set(fn, _frame);
                  }
                } // Return the line we found.


                return _frame;
              }
            } while (s >= 1 && c >= 0);
          }

          break;
        }
      }
    }
  } finally {
    reentry = false;

    {
      ReactCurrentDispatcher.current = previousDispatcher;
      reenableLogs();
    }

    Error.prepareStackTrace = previousPrepareStackTrace;
  } // Fallback to just using the name if we couldn't make it throw.


  var name = fn ? fn.displayName || fn.name : '';
  var syntheticFrame = name ? describeBuiltInComponentFrame(name) : '';

  {
    if (typeof fn === 'function') {
      componentFrameCache.set(fn, syntheticFrame);
    }
  }

  return syntheticFrame;
}
function describeFunctionComponentFrame(fn, source, ownerFn) {
  {
    return describeNativeComponentFrame(fn, false);
  }
}

function shouldConstruct(Component) {
  var prototype = Component.prototype;
  return !!(prototype && prototype.isReactComponent);
}

function describeUnknownElementTypeFrameInDEV(type, source, ownerFn) {

  if (type == null) {
    return '';
  }

  if (typeof type === 'function') {
    {
      return describeNativeComponentFrame(type, shouldConstruct(type));
    }
  }

  if (typeof type === 'string') {
    return describeBuiltInComponentFrame(type);
  }

  switch (type) {
    case REACT_SUSPENSE_TYPE:
      return describeBuiltInComponentFrame('Suspense');

    case REACT_SUSPENSE_LIST_TYPE:
      return describeBuiltInComponentFrame('SuspenseList');
  }

  if (typeof type === 'object') {
    switch (type.$$typeof) {
      case REACT_FORWARD_REF_TYPE:
        return describeFunctionComponentFrame(type.render);

      case REACT_MEMO_TYPE:
        // Memo may contain any component type so we recursively resolve it.
        return describeUnknownElementTypeFrameInDEV(type.type, source, ownerFn);

      case REACT_LAZY_TYPE:
        {
          var lazyComponent = type;
          var payload = lazyComponent._payload;
          var init = lazyComponent._init;

          try {
            // Lazy may contain any component type so we recursively resolve it.
            return describeUnknownElementTypeFrameInDEV(init(payload), source, ownerFn);
          } catch (x) {}
        }
    }
  }

  return '';
}

var hasOwnProperty = Object.prototype.hasOwnProperty;

var loggedTypeFailures = {};
var ReactDebugCurrentFrame = ReactSharedInternals.ReactDebugCurrentFrame;

function setCurrentlyValidatingElement(element) {
  {
    if (element) {
      var owner = element._owner;
      var stack = describeUnknownElementTypeFrameInDEV(element.type, element._source, owner ? owner.type : null);
      ReactDebugCurrentFrame.setExtraStackFrame(stack);
    } else {
      ReactDebugCurrentFrame.setExtraStackFrame(null);
    }
  }
}

function checkPropTypes(typeSpecs, values, location, componentName, element) {
  {
    // $FlowFixMe This is okay but Flow doesn't know it.
    var has = Function.call.bind(hasOwnProperty);

    for (var typeSpecName in typeSpecs) {
      if (has(typeSpecs, typeSpecName)) {
        var error$1 = void 0; // Prop type validation may throw. In case they do, we don't want to
        // fail the render phase where it didn't fail before. So we log it.
        // After these have been cleaned up, we'll let them throw.

        try {
          // This is intentionally an invariant that gets caught. It's the same
          // behavior as without this statement except with a better message.
          if (typeof typeSpecs[typeSpecName] !== 'function') {
            // eslint-disable-next-line react-internal/prod-error-codes
            var err = Error((componentName || 'React class') + ': ' + location + ' type `' + typeSpecName + '` is invalid; ' + 'it must be a function, usually from the `prop-types` package, but received `' + typeof typeSpecs[typeSpecName] + '`.' + 'This often happens because of typos such as `PropTypes.function` instead of `PropTypes.func`.');
            err.name = 'Invariant Violation';
            throw err;
          }

          error$1 = typeSpecs[typeSpecName](values, typeSpecName, componentName, location, null, 'SECRET_DO_NOT_PASS_THIS_OR_YOU_WILL_BE_FIRED');
        } catch (ex) {
          error$1 = ex;
        }

        if (error$1 && !(error$1 instanceof Error)) {
          setCurrentlyValidatingElement(element);

          error('%s: type specification of %s' + ' `%s` is invalid; the type checker ' + 'function must return `null` or an `Error` but returned a %s. ' + 'You may have forgotten to pass an argument to the type checker ' + 'creator (arrayOf, instanceOf, objectOf, oneOf, oneOfType, and ' + 'shape all require an argument).', componentName || 'React class', location, typeSpecName, typeof error$1);

          setCurrentlyValidatingElement(null);
        }

        if (error$1 instanceof Error && !(error$1.message in loggedTypeFailures)) {
          // Only monitor this failure once because there tends to be a lot of the
          // same error.
          loggedTypeFailures[error$1.message] = true;
          setCurrentlyValidatingElement(element);

          error('Failed %s type: %s', location, error$1.message);

          setCurrentlyValidatingElement(null);
        }
      }
    }
  }
}

var isArrayImpl = Array.isArray; // eslint-disable-next-line no-redeclare

function isArray(a) {
  return isArrayImpl(a);
}

/*
 * The `'' + value` pattern (used in in perf-sensitive code) throws for Symbol
 * and Temporal.* types. See https://github.com/facebook/react/pull/22064.
 *
 * The functions in this module will throw an easier-to-understand,
 * easier-to-debug exception with a clear errors message message explaining the
 * problem. (Instead of a confusing exception thrown inside the implementation
 * of the `value` object).
 */
// $FlowFixMe only called in DEV, so void return is not possible.
function typeName(value) {
  {
    // toStringTag is needed for namespaced types like Temporal.Instant
    var hasToStringTag = typeof Symbol === 'function' && Symbol.toStringTag;
    var type = hasToStringTag && value[Symbol.toStringTag] || value.constructor.name || 'Object';
    return type;
  }
} // $FlowFixMe only called in DEV, so void return is not possible.


function willCoercionThrow(value) {
  {
    try {
      testStringCoercion(value);
      return false;
    } catch (e) {
      return true;
    }
  }
}

function testStringCoercion(value) {
  // If you ended up here by following an exception call stack, here's what's
  // happened: you supplied an object or symbol value to React (as a prop, key,
  // DOM attribute, CSS property, string ref, etc.) and when React tried to
  // coerce it to a string using `'' + value`, an exception was thrown.
  //
  // The most common types that will cause this exception are `Symbol` instances
  // and Temporal objects like `Temporal.Instant`. But any object that has a
  // `valueOf` or `[Symbol.toPrimitive]` method that throws will also cause this
  // exception. (Library authors do this to prevent users from using built-in
  // numeric operators like `+` or comparison operators like `>=` because custom
  // methods are needed to perform accurate arithmetic or comparison.)
  //
  // To fix the problem, coerce this object or symbol value to a string before
  // passing it to React. The most reliable way is usually `String(value)`.
  //
  // To find which value is throwing, check the browser or debugger console.
  // Before this exception was thrown, there should be `console.error` output
  // that shows the type (Symbol, Temporal.PlainDate, etc.) that caused the
  // problem and how that type was used: key, atrribute, input value prop, etc.
  // In most cases, this console output also shows the component and its
  // ancestor components where the exception happened.
  //
  // eslint-disable-next-line react-internal/safe-string-coercion
  return '' + value;
}
function checkKeyStringCoercion(value) {
  {
    if (willCoercionThrow(value)) {
      error('The provided key is an unsupported type %s.' + ' This value must be coerced to a string before before using it here.', typeName(value));

      return testStringCoercion(value); // throw (to help callers find troubleshooting comments)
    }
  }
}

var ReactCurrentOwner = ReactSharedInternals.ReactCurrentOwner;
var RESERVED_PROPS = {
  key: true,
  ref: true,
  __self: true,
  __source: true
};
var specialPropKeyWarningShown;
var specialPropRefWarningShown;
var didWarnAboutStringRefs;

{
  didWarnAboutStringRefs = {};
}

function hasValidRef(config) {
  {
    if (hasOwnProperty.call(config, 'ref')) {
      var getter = Object.getOwnPropertyDescriptor(config, 'ref').get;

      if (getter && getter.isReactWarning) {
        return false;
      }
    }
  }

  return config.ref !== undefined;
}

function hasValidKey(config) {
  {
    if (hasOwnProperty.call(config, 'key')) {
      var getter = Object.getOwnPropertyDescriptor(config, 'key').get;

      if (getter && getter.isReactWarning) {
        return false;
      }
    }
  }

  return config.key !== undefined;
}

function warnIfStringRefCannotBeAutoConverted(config, self) {
  {
    if (typeof config.ref === 'string' && ReactCurrentOwner.current && self && ReactCurrentOwner.current.stateNode !== self) {
      var componentName = getComponentNameFromType(ReactCurrentOwner.current.type);

      if (!didWarnAboutStringRefs[componentName]) {
        error('Component "%s" contains the string ref "%s". ' + 'Support for string refs will be removed in a future major release. ' + 'This case cannot be automatically converted to an arrow function. ' + 'We ask you to manually fix this case by using useRef() or createRef() instead. ' + 'Learn more about using refs safely here: ' + 'https://reactjs.org/link/strict-mode-string-ref', getComponentNameFromType(ReactCurrentOwner.current.type), config.ref);

        didWarnAboutStringRefs[componentName] = true;
      }
    }
  }
}

function defineKeyPropWarningGetter(props, displayName) {
  {
    var warnAboutAccessingKey = function () {
      if (!specialPropKeyWarningShown) {
        specialPropKeyWarningShown = true;

        error('%s: `key` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://reactjs.org/link/special-props)', displayName);
      }
    };

    warnAboutAccessingKey.isReactWarning = true;
    Object.defineProperty(props, 'key', {
      get: warnAboutAccessingKey,
      configurable: true
    });
  }
}

function defineRefPropWarningGetter(props, displayName) {
  {
    var warnAboutAccessingRef = function () {
      if (!specialPropRefWarningShown) {
        specialPropRefWarningShown = true;

        error('%s: `ref` is not a prop. Trying to access it will result ' + 'in `undefined` being returned. If you need to access the same ' + 'value within the child component, you should pass it as a different ' + 'prop. (https://reactjs.org/link/special-props)', displayName);
      }
    };

    warnAboutAccessingRef.isReactWarning = true;
    Object.defineProperty(props, 'ref', {
      get: warnAboutAccessingRef,
      configurable: true
    });
  }
}
/**
 * Factory method to create a new React element. This no longer adheres to
 * the class pattern, so do not use new to call it. Also, instanceof check
 * will not work. Instead test $$typeof field against Symbol.for('react.element') to check
 * if something is a React Element.
 *
 * @param {*} type
 * @param {*} props
 * @param {*} key
 * @param {string|object} ref
 * @param {*} owner
 * @param {*} self A *temporary* helper to detect places where `this` is
 * different from the `owner` when React.createElement is called, so that we
 * can warn. We want to get rid of owner and replace string `ref`s with arrow
 * functions, and as long as `this` and owner are the same, there will be no
 * change in behavior.
 * @param {*} source An annotation object (added by a transpiler or otherwise)
 * indicating filename, line number, and/or other information.
 * @internal
 */


var ReactElement = function (type, key, ref, self, source, owner, props) {
  var element = {
    // This tag allows us to uniquely identify this as a React Element
    $$typeof: REACT_ELEMENT_TYPE,
    // Built-in properties that belong on the element
    type: type,
    key: key,
    ref: ref,
    props: props,
    // Record the component responsible for creating this element.
    _owner: owner
  };

  {
    // The validation flag is currently mutative. We put it on
    // an external backing store so that we can freeze the whole object.
    // This can be replaced with a WeakMap once they are implemented in
    // commonly used development environments.
    element._store = {}; // To make comparing ReactElements easier for testing purposes, we make
    // the validation flag non-enumerable (where possible, which should
    // include every environment we run tests in), so the test framework
    // ignores it.

    Object.defineProperty(element._store, 'validated', {
      configurable: false,
      enumerable: false,
      writable: true,
      value: false
    }); // self and source are DEV only properties.

    Object.defineProperty(element, '_self', {
      configurable: false,
      enumerable: false,
      writable: false,
      value: self
    }); // Two elements created in two different places should be considered
    // equal for testing purposes and therefore we hide it from enumeration.

    Object.defineProperty(element, '_source', {
      configurable: false,
      enumerable: false,
      writable: false,
      value: source
    });

    if (Object.freeze) {
      Object.freeze(element.props);
      Object.freeze(element);
    }
  }

  return element;
};
/**
 * https://github.com/reactjs/rfcs/pull/107
 * @param {*} type
 * @param {object} props
 * @param {string} key
 */

function jsxDEV(type, config, maybeKey, source, self) {
  {
    var propName; // Reserved names are extracted

    var props = {};
    var key = null;
    var ref = null; // Currently, key can be spread in as a prop. This causes a potential
    // issue if key is also explicitly declared (ie. <div {...props} key="Hi" />
    // or <div key="Hi" {...props} /> ). We want to deprecate key spread,
    // but as an intermediary step, we will use jsxDEV for everything except
    // <div {...props} key="Hi" />, because we aren't currently able to tell if
    // key is explicitly declared to be undefined or not.

    if (maybeKey !== undefined) {
      {
        checkKeyStringCoercion(maybeKey);
      }

      key = '' + maybeKey;
    }

    if (hasValidKey(config)) {
      {
        checkKeyStringCoercion(config.key);
      }

      key = '' + config.key;
    }

    if (hasValidRef(config)) {
      ref = config.ref;
      warnIfStringRefCannotBeAutoConverted(config, self);
    } // Remaining properties are added to a new props object


    for (propName in config) {
      if (hasOwnProperty.call(config, propName) && !RESERVED_PROPS.hasOwnProperty(propName)) {
        props[propName] = config[propName];
      }
    } // Resolve default props


    if (type && type.defaultProps) {
      var defaultProps = type.defaultProps;

      for (propName in defaultProps) {
        if (props[propName] === undefined) {
          props[propName] = defaultProps[propName];
        }
      }
    }

    if (key || ref) {
      var displayName = typeof type === 'function' ? type.displayName || type.name || 'Unknown' : type;

      if (key) {
        defineKeyPropWarningGetter(props, displayName);
      }

      if (ref) {
        defineRefPropWarningGetter(props, displayName);
      }
    }

    return ReactElement(type, key, ref, self, source, ReactCurrentOwner.current, props);
  }
}

var ReactCurrentOwner$1 = ReactSharedInternals.ReactCurrentOwner;
var ReactDebugCurrentFrame$1 = ReactSharedInternals.ReactDebugCurrentFrame;

function setCurrentlyValidatingElement$1(element) {
  {
    if (element) {
      var owner = element._owner;
      var stack = describeUnknownElementTypeFrameInDEV(element.type, element._source, owner ? owner.type : null);
      ReactDebugCurrentFrame$1.setExtraStackFrame(stack);
    } else {
      ReactDebugCurrentFrame$1.setExtraStackFrame(null);
    }
  }
}

var propTypesMisspellWarningShown;

{
  propTypesMisspellWarningShown = false;
}
/**
 * Verifies the object is a ReactElement.
 * See https://reactjs.org/docs/react-api.html#isvalidelement
 * @param {?object} object
 * @return {boolean} True if `object` is a ReactElement.
 * @final
 */


function isValidElement(object) {
  {
    return typeof object === 'object' && object !== null && object.$$typeof === REACT_ELEMENT_TYPE;
  }
}

function getDeclarationErrorAddendum() {
  {
    if (ReactCurrentOwner$1.current) {
      var name = getComponentNameFromType(ReactCurrentOwner$1.current.type);

      if (name) {
        return '\n\nCheck the render method of `' + name + '`.';
      }
    }

    return '';
  }
}

function getSourceInfoErrorAddendum(source) {
  {
    if (source !== undefined) {
      var fileName = source.fileName.replace(/^.*[\\\/]/, '');
      var lineNumber = source.lineNumber;
      return '\n\nCheck your code at ' + fileName + ':' + lineNumber + '.';
    }

    return '';
  }
}
/**
 * Warn if there's no key explicitly set on dynamic arrays of children or
 * object keys are not valid. This allows us to keep track of children between
 * updates.
 */


var ownerHasKeyUseWarning = {};

function getCurrentComponentErrorInfo(parentType) {
  {
    var info = getDeclarationErrorAddendum();

    if (!info) {
      var parentName = typeof parentType === 'string' ? parentType : parentType.displayName || parentType.name;

      if (parentName) {
        info = "\n\nCheck the top-level render call using <" + parentName + ">.";
      }
    }

    return info;
  }
}
/**
 * Warn if the element doesn't have an explicit key assigned to it.
 * This element is in an array. The array could grow and shrink or be
 * reordered. All children that haven't already been validated are required to
 * have a "key" property assigned to it. Error statuses are cached so a warning
 * will only be shown once.
 *
 * @internal
 * @param {ReactElement} element Element that requires a key.
 * @param {*} parentType element's parent's type.
 */


function validateExplicitKey(element, parentType) {
  {
    if (!element._store || element._store.validated || element.key != null) {
      return;
    }

    element._store.validated = true;
    var currentComponentErrorInfo = getCurrentComponentErrorInfo(parentType);

    if (ownerHasKeyUseWarning[currentComponentErrorInfo]) {
      return;
    }

    ownerHasKeyUseWarning[currentComponentErrorInfo] = true; // Usually the current owner is the offender, but if it accepts children as a
    // property, it may be the creator of the child that's responsible for
    // assigning it a key.

    var childOwner = '';

    if (element && element._owner && element._owner !== ReactCurrentOwner$1.current) {
      // Give the component that originally created this child.
      childOwner = " It was passed a child from " + getComponentNameFromType(element._owner.type) + ".";
    }

    setCurrentlyValidatingElement$1(element);

    error('Each child in a list should have a unique "key" prop.' + '%s%s See https://reactjs.org/link/warning-keys for more information.', currentComponentErrorInfo, childOwner);

    setCurrentlyValidatingElement$1(null);
  }
}
/**
 * Ensure that every element either is passed in a static location, in an
 * array with an explicit keys property defined, or in an object literal
 * with valid key property.
 *
 * @internal
 * @param {ReactNode} node Statically passed child of any type.
 * @param {*} parentType node's parent's type.
 */


function validateChildKeys(node, parentType) {
  {
    if (typeof node !== 'object') {
      return;
    }

    if (isArray(node)) {
      for (var i = 0; i < node.length; i++) {
        var child = node[i];

        if (isValidElement(child)) {
          validateExplicitKey(child, parentType);
        }
      }
    } else if (isValidElement(node)) {
      // This element was passed in a valid location.
      if (node._store) {
        node._store.validated = true;
      }
    } else if (node) {
      var iteratorFn = getIteratorFn(node);

      if (typeof iteratorFn === 'function') {
        // Entry iterators used to provide implicit keys,
        // but now we print a separate warning for them later.
        if (iteratorFn !== node.entries) {
          var iterator = iteratorFn.call(node);
          var step;

          while (!(step = iterator.next()).done) {
            if (isValidElement(step.value)) {
              validateExplicitKey(step.value, parentType);
            }
          }
        }
      }
    }
  }
}
/**
 * Given an element, validate that its props follow the propTypes definition,
 * provided by the type.
 *
 * @param {ReactElement} element
 */


function validatePropTypes(element) {
  {
    var type = element.type;

    if (type === null || type === undefined || typeof type === 'string') {
      return;
    }

    var propTypes;

    if (typeof type === 'function') {
      propTypes = type.propTypes;
    } else if (typeof type === 'object' && (type.$$typeof === REACT_FORWARD_REF_TYPE || // Note: Memo only checks outer props here.
    // Inner props are checked in the reconciler.
    type.$$typeof === REACT_MEMO_TYPE)) {
      propTypes = type.propTypes;
    } else {
      return;
    }

    if (propTypes) {
      // Intentionally inside to avoid triggering lazy initializers:
      var name = getComponentNameFromType(type);
      checkPropTypes(propTypes, element.props, 'prop', name, element);
    } else if (type.PropTypes !== undefined && !propTypesMisspellWarningShown) {
      propTypesMisspellWarningShown = true; // Intentionally inside to avoid triggering lazy initializers:

      var _name = getComponentNameFromType(type);

      error('Component %s declared `PropTypes` instead of `propTypes`. Did you misspell the property assignment?', _name || 'Unknown');
    }

    if (typeof type.getDefaultProps === 'function' && !type.getDefaultProps.isReactClassApproved) {
      error('getDefaultProps is only used on classic React.createClass ' + 'definitions. Use a static property named `defaultProps` instead.');
    }
  }
}
/**
 * Given a fragment, validate that it can only be provided with fragment props
 * @param {ReactElement} fragment
 */


function validateFragmentProps(fragment) {
  {
    var keys = Object.keys(fragment.props);

    for (var i = 0; i < keys.length; i++) {
      var key = keys[i];

      if (key !== 'children' && key !== 'key') {
        setCurrentlyValidatingElement$1(fragment);

        error('Invalid prop `%s` supplied to `React.Fragment`. ' + 'React.Fragment can only have `key` and `children` props.', key);

        setCurrentlyValidatingElement$1(null);
        break;
      }
    }

    if (fragment.ref !== null) {
      setCurrentlyValidatingElement$1(fragment);

      error('Invalid attribute `ref` supplied to `React.Fragment`.');

      setCurrentlyValidatingElement$1(null);
    }
  }
}

var didWarnAboutKeySpread = {};
function jsxWithValidation(type, props, key, isStaticChildren, source, self) {
  {
    var validType = isValidElementType(type); // We warn in this case but don't throw. We expect the element creation to
    // succeed and there will likely be errors in render.

    if (!validType) {
      var info = '';

      if (type === undefined || typeof type === 'object' && type !== null && Object.keys(type).length === 0) {
        info += ' You likely forgot to export your component from the file ' + "it's defined in, or you might have mixed up default and named imports.";
      }

      var sourceInfo = getSourceInfoErrorAddendum(source);

      if (sourceInfo) {
        info += sourceInfo;
      } else {
        info += getDeclarationErrorAddendum();
      }

      var typeString;

      if (type === null) {
        typeString = 'null';
      } else if (isArray(type)) {
        typeString = 'array';
      } else if (type !== undefined && type.$$typeof === REACT_ELEMENT_TYPE) {
        typeString = "<" + (getComponentNameFromType(type.type) || 'Unknown') + " />";
        info = ' Did you accidentally export a JSX literal instead of a component?';
      } else {
        typeString = typeof type;
      }

      error('React.jsx: type is invalid -- expected a string (for ' + 'built-in components) or a class/function (for composite ' + 'components) but got: %s.%s', typeString, info);
    }

    var element = jsxDEV(type, props, key, source, self); // The result can be nullish if a mock or a custom function is used.
    // TODO: Drop this when these are no longer allowed as the type argument.

    if (element == null) {
      return element;
    } // Skip key warning if the type isn't valid since our key validation logic
    // doesn't expect a non-string/function type and can throw confusing errors.
    // We don't want exception behavior to differ between dev and prod.
    // (Rendering will throw with a helpful message and as soon as the type is
    // fixed, the key warnings will appear.)


    if (validType) {
      var children = props.children;

      if (children !== undefined) {
        if (isStaticChildren) {
          if (isArray(children)) {
            for (var i = 0; i < children.length; i++) {
              validateChildKeys(children[i], type);
            }

            if (Object.freeze) {
              Object.freeze(children);
            }
          } else {
            error('React.jsx: Static children should always be an array. ' + 'You are likely explicitly calling React.jsxs or React.jsxDEV. ' + 'Use the Babel transform instead.');
          }
        } else {
          validateChildKeys(children, type);
        }
      }
    }

    {
      if (hasOwnProperty.call(props, 'key')) {
        var componentName = getComponentNameFromType(type);
        var keys = Object.keys(props).filter(function (k) {
          return k !== 'key';
        });
        var beforeExample = keys.length > 0 ? '{key: someKey, ' + keys.join(': ..., ') + ': ...}' : '{key: someKey}';

        if (!didWarnAboutKeySpread[componentName + beforeExample]) {
          var afterExample = keys.length > 0 ? '{' + keys.join(': ..., ') + ': ...}' : '{}';

          error('A props object containing a "key" prop is being spread into JSX:\n' + '  let props = %s;\n' + '  <%s {...props} />\n' + 'React keys must be passed directly to JSX without using spread:\n' + '  let props = %s;\n' + '  <%s key={someKey} {...props} />', beforeExample, componentName, afterExample, componentName);

          didWarnAboutKeySpread[componentName + beforeExample] = true;
        }
      }
    }

    if (type === REACT_FRAGMENT_TYPE) {
      validateFragmentProps(element);
    } else {
      validatePropTypes(element);
    }

    return element;
  }
} // These two functions exist to still get child warnings in dev
// even with the prod transform. This means that jsxDEV is purely
// opt-in behavior for better messages but that we won't stop
// giving you warnings if you use production apis.

function jsxWithValidationStatic(type, props, key) {
  {
    return jsxWithValidation(type, props, key, true);
  }
}
function jsxWithValidationDynamic(type, props, key) {
  {
    return jsxWithValidation(type, props, key, false);
  }
}

var jsx =  jsxWithValidationDynamic ; // we may want to special case jsxs internally to take advantage of static children.
// for now we can ship identical prod functions

var jsxs =  jsxWithValidationStatic ;

exports.Fragment = REACT_FRAGMENT_TYPE;
exports.jsx = jsx;
exports.jsxs = jsxs;
  })();
}


/***/ }),

/***/ "./node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js":
/*!***************************************************************************!*\
  !*** ./node_modules/.pnpm/react@18.3.1/node_modules/react/jsx-runtime.js ***!
  \***************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


if (false) // removed by dead control flow
{} else {
  module.exports = __webpack_require__(/*! ./cjs/react-jsx-runtime.development.js */ "./node_modules/.pnpm/react@18.3.1/node_modules/react/cjs/react-jsx-runtime.development.js");
}


/***/ }),

/***/ "./node_modules/.pnpm/slugify@1.6.6/node_modules/slugify/slugify.js":
/*!**************************************************************************!*\
  !*** ./node_modules/.pnpm/slugify@1.6.6/node_modules/slugify/slugify.js ***!
  \**************************************************************************/
/***/ (function(module) {


;(function (name, root, factory) {
  if (true) {
    module.exports = factory()
    module.exports["default"] = factory()
  }
  /* istanbul ignore next */
  else // removed by dead control flow
{}
}('slugify', this, function () {
  var charMap = JSON.parse('{"$":"dollar","%":"percent","&":"and","<":"less",">":"greater","|":"or","¢":"cent","£":"pound","¤":"currency","¥":"yen","©":"(c)","ª":"a","®":"(r)","º":"o","À":"A","Á":"A","Â":"A","Ã":"A","Ä":"A","Å":"A","Æ":"AE","Ç":"C","È":"E","É":"E","Ê":"E","Ë":"E","Ì":"I","Í":"I","Î":"I","Ï":"I","Ð":"D","Ñ":"N","Ò":"O","Ó":"O","Ô":"O","Õ":"O","Ö":"O","Ø":"O","Ù":"U","Ú":"U","Û":"U","Ü":"U","Ý":"Y","Þ":"TH","ß":"ss","à":"a","á":"a","â":"a","ã":"a","ä":"a","å":"a","æ":"ae","ç":"c","è":"e","é":"e","ê":"e","ë":"e","ì":"i","í":"i","î":"i","ï":"i","ð":"d","ñ":"n","ò":"o","ó":"o","ô":"o","õ":"o","ö":"o","ø":"o","ù":"u","ú":"u","û":"u","ü":"u","ý":"y","þ":"th","ÿ":"y","Ā":"A","ā":"a","Ă":"A","ă":"a","Ą":"A","ą":"a","Ć":"C","ć":"c","Č":"C","č":"c","Ď":"D","ď":"d","Đ":"DJ","đ":"dj","Ē":"E","ē":"e","Ė":"E","ė":"e","Ę":"e","ę":"e","Ě":"E","ě":"e","Ğ":"G","ğ":"g","Ģ":"G","ģ":"g","Ĩ":"I","ĩ":"i","Ī":"i","ī":"i","Į":"I","į":"i","İ":"I","ı":"i","Ķ":"k","ķ":"k","Ļ":"L","ļ":"l","Ľ":"L","ľ":"l","Ł":"L","ł":"l","Ń":"N","ń":"n","Ņ":"N","ņ":"n","Ň":"N","ň":"n","Ō":"O","ō":"o","Ő":"O","ő":"o","Œ":"OE","œ":"oe","Ŕ":"R","ŕ":"r","Ř":"R","ř":"r","Ś":"S","ś":"s","Ş":"S","ş":"s","Š":"S","š":"s","Ţ":"T","ţ":"t","Ť":"T","ť":"t","Ũ":"U","ũ":"u","Ū":"u","ū":"u","Ů":"U","ů":"u","Ű":"U","ű":"u","Ų":"U","ų":"u","Ŵ":"W","ŵ":"w","Ŷ":"Y","ŷ":"y","Ÿ":"Y","Ź":"Z","ź":"z","Ż":"Z","ż":"z","Ž":"Z","ž":"z","Ə":"E","ƒ":"f","Ơ":"O","ơ":"o","Ư":"U","ư":"u","ǈ":"LJ","ǉ":"lj","ǋ":"NJ","ǌ":"nj","Ș":"S","ș":"s","Ț":"T","ț":"t","ə":"e","˚":"o","Ά":"A","Έ":"E","Ή":"H","Ί":"I","Ό":"O","Ύ":"Y","Ώ":"W","ΐ":"i","Α":"A","Β":"B","Γ":"G","Δ":"D","Ε":"E","Ζ":"Z","Η":"H","Θ":"8","Ι":"I","Κ":"K","Λ":"L","Μ":"M","Ν":"N","Ξ":"3","Ο":"O","Π":"P","Ρ":"R","Σ":"S","Τ":"T","Υ":"Y","Φ":"F","Χ":"X","Ψ":"PS","Ω":"W","Ϊ":"I","Ϋ":"Y","ά":"a","έ":"e","ή":"h","ί":"i","ΰ":"y","α":"a","β":"b","γ":"g","δ":"d","ε":"e","ζ":"z","η":"h","θ":"8","ι":"i","κ":"k","λ":"l","μ":"m","ν":"n","ξ":"3","ο":"o","π":"p","ρ":"r","ς":"s","σ":"s","τ":"t","υ":"y","φ":"f","χ":"x","ψ":"ps","ω":"w","ϊ":"i","ϋ":"y","ό":"o","ύ":"y","ώ":"w","Ё":"Yo","Ђ":"DJ","Є":"Ye","І":"I","Ї":"Yi","Ј":"J","Љ":"LJ","Њ":"NJ","Ћ":"C","Џ":"DZ","А":"A","Б":"B","В":"V","Г":"G","Д":"D","Е":"E","Ж":"Zh","З":"Z","И":"I","Й":"J","К":"K","Л":"L","М":"M","Н":"N","О":"O","П":"P","Р":"R","С":"S","Т":"T","У":"U","Ф":"F","Х":"H","Ц":"C","Ч":"Ch","Ш":"Sh","Щ":"Sh","Ъ":"U","Ы":"Y","Ь":"","Э":"E","Ю":"Yu","Я":"Ya","а":"a","б":"b","в":"v","г":"g","д":"d","е":"e","ж":"zh","з":"z","и":"i","й":"j","к":"k","л":"l","м":"m","н":"n","о":"o","п":"p","р":"r","с":"s","т":"t","у":"u","ф":"f","х":"h","ц":"c","ч":"ch","ш":"sh","щ":"sh","ъ":"u","ы":"y","ь":"","э":"e","ю":"yu","я":"ya","ё":"yo","ђ":"dj","є":"ye","і":"i","ї":"yi","ј":"j","љ":"lj","њ":"nj","ћ":"c","ѝ":"u","џ":"dz","Ґ":"G","ґ":"g","Ғ":"GH","ғ":"gh","Қ":"KH","қ":"kh","Ң":"NG","ң":"ng","Ү":"UE","ү":"ue","Ұ":"U","ұ":"u","Һ":"H","һ":"h","Ә":"AE","ә":"ae","Ө":"OE","ө":"oe","Ա":"A","Բ":"B","Գ":"G","Դ":"D","Ե":"E","Զ":"Z","Է":"E\'","Ը":"Y\'","Թ":"T\'","Ժ":"JH","Ի":"I","Լ":"L","Խ":"X","Ծ":"C\'","Կ":"K","Հ":"H","Ձ":"D\'","Ղ":"GH","Ճ":"TW","Մ":"M","Յ":"Y","Ն":"N","Շ":"SH","Չ":"CH","Պ":"P","Ջ":"J","Ռ":"R\'","Ս":"S","Վ":"V","Տ":"T","Ր":"R","Ց":"C","Փ":"P\'","Ք":"Q\'","Օ":"O\'\'","Ֆ":"F","և":"EV","ء":"a","آ":"aa","أ":"a","ؤ":"u","إ":"i","ئ":"e","ا":"a","ب":"b","ة":"h","ت":"t","ث":"th","ج":"j","ح":"h","خ":"kh","د":"d","ذ":"th","ر":"r","ز":"z","س":"s","ش":"sh","ص":"s","ض":"dh","ط":"t","ظ":"z","ع":"a","غ":"gh","ف":"f","ق":"q","ك":"k","ل":"l","م":"m","ن":"n","ه":"h","و":"w","ى":"a","ي":"y","ً":"an","ٌ":"on","ٍ":"en","َ":"a","ُ":"u","ِ":"e","ْ":"","٠":"0","١":"1","٢":"2","٣":"3","٤":"4","٥":"5","٦":"6","٧":"7","٨":"8","٩":"9","پ":"p","چ":"ch","ژ":"zh","ک":"k","گ":"g","ی":"y","۰":"0","۱":"1","۲":"2","۳":"3","۴":"4","۵":"5","۶":"6","۷":"7","۸":"8","۹":"9","฿":"baht","ა":"a","ბ":"b","გ":"g","დ":"d","ე":"e","ვ":"v","ზ":"z","თ":"t","ი":"i","კ":"k","ლ":"l","მ":"m","ნ":"n","ო":"o","პ":"p","ჟ":"zh","რ":"r","ს":"s","ტ":"t","უ":"u","ფ":"f","ქ":"k","ღ":"gh","ყ":"q","შ":"sh","ჩ":"ch","ც":"ts","ძ":"dz","წ":"ts","ჭ":"ch","ხ":"kh","ჯ":"j","ჰ":"h","Ṣ":"S","ṣ":"s","Ẁ":"W","ẁ":"w","Ẃ":"W","ẃ":"w","Ẅ":"W","ẅ":"w","ẞ":"SS","Ạ":"A","ạ":"a","Ả":"A","ả":"a","Ấ":"A","ấ":"a","Ầ":"A","ầ":"a","Ẩ":"A","ẩ":"a","Ẫ":"A","ẫ":"a","Ậ":"A","ậ":"a","Ắ":"A","ắ":"a","Ằ":"A","ằ":"a","Ẳ":"A","ẳ":"a","Ẵ":"A","ẵ":"a","Ặ":"A","ặ":"a","Ẹ":"E","ẹ":"e","Ẻ":"E","ẻ":"e","Ẽ":"E","ẽ":"e","Ế":"E","ế":"e","Ề":"E","ề":"e","Ể":"E","ể":"e","Ễ":"E","ễ":"e","Ệ":"E","ệ":"e","Ỉ":"I","ỉ":"i","Ị":"I","ị":"i","Ọ":"O","ọ":"o","Ỏ":"O","ỏ":"o","Ố":"O","ố":"o","Ồ":"O","ồ":"o","Ổ":"O","ổ":"o","Ỗ":"O","ỗ":"o","Ộ":"O","ộ":"o","Ớ":"O","ớ":"o","Ờ":"O","ờ":"o","Ở":"O","ở":"o","Ỡ":"O","ỡ":"o","Ợ":"O","ợ":"o","Ụ":"U","ụ":"u","Ủ":"U","ủ":"u","Ứ":"U","ứ":"u","Ừ":"U","ừ":"u","Ử":"U","ử":"u","Ữ":"U","ữ":"u","Ự":"U","ự":"u","Ỳ":"Y","ỳ":"y","Ỵ":"Y","ỵ":"y","Ỷ":"Y","ỷ":"y","Ỹ":"Y","ỹ":"y","–":"-","‘":"\'","’":"\'","“":"\\\"","”":"\\\"","„":"\\\"","†":"+","•":"*","…":"...","₠":"ecu","₢":"cruzeiro","₣":"french franc","₤":"lira","₥":"mill","₦":"naira","₧":"peseta","₨":"rupee","₩":"won","₪":"new shequel","₫":"dong","€":"euro","₭":"kip","₮":"tugrik","₯":"drachma","₰":"penny","₱":"peso","₲":"guarani","₳":"austral","₴":"hryvnia","₵":"cedi","₸":"kazakhstani tenge","₹":"indian rupee","₺":"turkish lira","₽":"russian ruble","₿":"bitcoin","℠":"sm","™":"tm","∂":"d","∆":"delta","∑":"sum","∞":"infinity","♥":"love","元":"yuan","円":"yen","﷼":"rial","ﻵ":"laa","ﻷ":"laa","ﻹ":"lai","ﻻ":"la"}')
  var locales = JSON.parse('{"bg":{"Й":"Y","Ц":"Ts","Щ":"Sht","Ъ":"A","Ь":"Y","й":"y","ц":"ts","щ":"sht","ъ":"a","ь":"y"},"de":{"Ä":"AE","ä":"ae","Ö":"OE","ö":"oe","Ü":"UE","ü":"ue","ß":"ss","%":"prozent","&":"und","|":"oder","∑":"summe","∞":"unendlich","♥":"liebe"},"es":{"%":"por ciento","&":"y","<":"menor que",">":"mayor que","|":"o","¢":"centavos","£":"libras","¤":"moneda","₣":"francos","∑":"suma","∞":"infinito","♥":"amor"},"fr":{"%":"pourcent","&":"et","<":"plus petit",">":"plus grand","|":"ou","¢":"centime","£":"livre","¤":"devise","₣":"franc","∑":"somme","∞":"infini","♥":"amour"},"pt":{"%":"porcento","&":"e","<":"menor",">":"maior","|":"ou","¢":"centavo","∑":"soma","£":"libra","∞":"infinito","♥":"amor"},"uk":{"И":"Y","и":"y","Й":"Y","й":"y","Ц":"Ts","ц":"ts","Х":"Kh","х":"kh","Щ":"Shch","щ":"shch","Г":"H","г":"h"},"vi":{"Đ":"D","đ":"d"},"da":{"Ø":"OE","ø":"oe","Å":"AA","å":"aa","%":"procent","&":"og","|":"eller","$":"dollar","<":"mindre end",">":"større end"},"nb":{"&":"og","Å":"AA","Æ":"AE","Ø":"OE","å":"aa","æ":"ae","ø":"oe"},"it":{"&":"e"},"nl":{"&":"en"},"sv":{"&":"och","Å":"AA","Ä":"AE","Ö":"OE","å":"aa","ä":"ae","ö":"oe"}}')

  function replace (string, options) {
    if (typeof string !== 'string') {
      throw new Error('slugify: string argument expected')
    }

    options = (typeof options === 'string')
      ? {replacement: options}
      : options || {}

    var locale = locales[options.locale] || {}

    var replacement = options.replacement === undefined ? '-' : options.replacement

    var trim = options.trim === undefined ? true : options.trim

    var slug = string.normalize().split('')
      // replace characters based on charMap
      .reduce(function (result, ch) {
        var appendChar = locale[ch];
        if (appendChar === undefined) appendChar = charMap[ch];
        if (appendChar === undefined) appendChar = ch;
        if (appendChar === replacement) appendChar = ' ';
        return result + appendChar
          // remove not allowed characters
          .replace(options.remove || /[^\w\s$*_+~.()'"!\-:@]+/g, '')
      }, '');

    if (options.strict) {
      slug = slug.replace(/[^A-Za-z0-9\s]/g, '');
    }

    if (trim) {
      slug = slug.trim()
    }

    // Replace spaces with replacement character, treating multiple consecutive
    // spaces as a single space.
    slug = slug.replace(/\s+/g, replacement);

    if (options.lower) {
      slug = slug.toLowerCase()
    }

    return slug
  }

  replace.extend = function (customMap) {
    Object.assign(charMap, customMap)
  }

  return replace
}))


/***/ }),

/***/ "@wordpress/components":
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["components"];

/***/ }),

/***/ "@wordpress/compose":
/*!*********************************!*\
  !*** external ["wp","compose"] ***!
  \*********************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["compose"];

/***/ }),

/***/ "@wordpress/element":
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["element"];

/***/ }),

/***/ "@wordpress/i18n":
/*!******************************!*\
  !*** external ["wp","i18n"] ***!
  \******************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["i18n"];

/***/ }),

/***/ "@wordpress/primitives":
/*!************************************!*\
  !*** external ["wp","primitives"] ***!
  \************************************/
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["primitives"];

/***/ }),

/***/ "codemirror":
/*!********************************!*\
  !*** external "wp.CodeMirror" ***!
  \********************************/
/***/ ((module) => {

"use strict";
module.exports = wp.CodeMirror;

/***/ }),

/***/ "react":
/*!************************!*\
  !*** external "React" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = window["React"];

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/global */
/******/ 	(() => {
/******/ 		__webpack_require__.g = (function() {
/******/ 			if (typeof globalThis === 'object') return globalThis;
/******/ 			try {
/******/ 				return this || new Function('return this')();
/******/ 			} catch (e) {
/******/ 				if (typeof window === 'object') return window;
/******/ 			}
/******/ 		})();
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!******************************!*\
  !*** ./app/post-type/App.js ***!
  \******************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! react */ "react");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _SettingsContext__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ../SettingsContext */ "./app/SettingsContext.js");
/* harmony import */ var _constants_DefaultSettings__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./constants/DefaultSettings */ "./app/post-type/constants/DefaultSettings.js");
/* harmony import */ var _MainTabs__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./MainTabs */ "./app/post-type/MainTabs.js");





const App = () => (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_SettingsContext__WEBPACK_IMPORTED_MODULE_2__.SettingsProvider, {
  value: MBCPT.settings || _constants_DefaultSettings__WEBPACK_IMPORTED_MODULE_3__["default"]
}, (0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(_MainTabs__WEBPACK_IMPORTED_MODULE_4__["default"], null));
const container = document.getElementById('poststuff');
container.classList.add('mb-cpt');
container.id = 'mb-cpt-app';

// Use React 17 to make the rendering synchronous to make sure WordPress's JS (like detecting #submitdiv or .wp-header-end)
// runs after the app is rendered.
(0,_wordpress_element__WEBPACK_IMPORTED_MODULE_1__.render)((0,react__WEBPACK_IMPORTED_MODULE_0__.createElement)(App, null), container);
})();

/******/ })()
;
//# sourceMappingURL=post-type.js.map