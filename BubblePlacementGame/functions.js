function delegate(parent, type, selector, handler) {
  parent.addEventListener(type, function (event) {
    const targetElement = event.target.closest(selector);

    if (this.contains(targetElement)) {
      handler(event, targetElement);
    }
  });
}

/**
 * Add .hidden class to the element to not display it.
 * @param {*} elem DOM element
 */
function hide(elem) {
  elem.classList.add("hidden");
}
/**
 * Remove .hidden class to the element to display it.
 * @param {*} elem DOM element
 */
function show(elem) {
  elem.classList.remove("hidden");
}

function permaSaveJSON(name, value) {
  window.localStorage.setItem(name, JSON.stringify(value));
}

function permaLoadJSON(name) {
  data = window.localStorage.getItem(name);
  if (data) {
    return JSON.parse(data);
  } else {
    return [];
  }
}