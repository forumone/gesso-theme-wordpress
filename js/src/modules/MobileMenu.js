const MobileMenu = function() {
  const KEYCODE = {
    esc: 27,
    tab: 9,
  };
  const optionDefault = {
    toggleSubNav: true, // Enable subnav toggle
    navMenu: '.l-navigation .menu--main', // Selector for primary nav
    searchBlock: false, // Selector for search block
    utilityMenu: false, // Selector for utility nav
    header: '.l-header', // Selector for site header
    toggleButton: '.mobile-menu__button', //Selector for Menu toggle
    container: '.mobile-menu-container', // Selector Destination container for mobile nav
    mobileMenuClass: 'mobile-nav', // Classname for navigation section
    mobileSearchClass: 'mobile-search-block', // Classname for search section
    mobileUtilityMenuClass: 'mobile-account-menu', // Classname for utility section
    buttonClass: 'mobile-menu__button',
    closeButtonClass: 'mobile-menu__close', // Classname for generated close button
    overlayClass: 'mobile-menu', // Overlay Classname
  };

  let currOptions = {};
  let blockTypes = {};
  let container = false;
  let overlay = false;
  let prevFocused = false;

  function initUI(options = {}) {
    if (!document.body.classList.contains('mobile-menu-processed')) {
      currOptions = Object.assign({}, optionDefault, options);

      blockTypes = {
        search: currOptions.mobileSearchClass,
        menu: currOptions.mobileMenuClass,
        account: currOptions.mobileUtilityMenuClass,
      };

      const navMenu = document.querySelector(currOptions.navMenu);
      const searchBlock = document.querySelector(currOptions.searchBlock);
      const utilityMenu = document.querySelector(currOptions.utilityMenu);
      const header = document.querySelector(currOptions.header);
      const closeButton = document.createElement('button');
      let toggleButton = document.querySelector(currOptions.toggleButton);
      overlay = document.createElement('nav');
      container = document.querySelector(currOptions.container);

      // Multiple calls to classList.add() here because IE doesn't support multiple arguments. :(
      closeButton.classList.add(currOptions.buttonClass);
      closeButton.classList.add(currOptions.closeButtonClass);
      closeButton.innerHTML = 'X';
      closeButton.addEventListener('click', closeMenu);

      if (toggleButton === null) {
        toggleButton = document.createElement('button');
        toggleButton.classList.add(currOptions.buttonClass);
        toggleButton.innerHTML =
          '<span class="mobile-menu__icon mobile-menu__icon--menu">Menu</span>';
        header.insertAdjacentElement('beforeend', toggleButton);
      }
      toggleButton.addEventListener('click', function() {
        openMenu();
      });

      overlay.classList.add(currOptions.overlayClass);
      overlay.appendChild(closeButton);

      if (searchBlock) {
        overlay.appendChild(cloneMenu(searchBlock, 'search'));
      }
      overlay.appendChild(cloneMenu(navMenu));
      if (utilityMenu) {
        overlay.appendChild(cloneMenu(utilityMenu, 'account'));
      }

      if (container !== null) {
        container.appendChild(overlay);
      } else if (header !== null) {
        header.insertAdjacentElement('afterend', overlay);
      } else {
        document.body.insertAdjacentElement('afterbegin', container);
      }
      closeMenu();

      document.body.classList.add('mobile-menu-processed');
    }
  }

  function cloneMenu(menu, type = 'menu') {
    let menuClone = false;

    if (menu) {
      menuClone = menu.cloneNode(true);

      menuClone.className = '';
      const subNavClass = currOptions.toggleSubNav
        ? 'menu--toggle-subnav'
        : 'menu--show-subnav';
      menuClone.classList.add(blockTypes[type], 'menu', subNavClass);

      const links = menuClone.querySelectorAll('.menu__link');

      if (links.length) {
        links.forEach(function(item, index) {
          const nextElement = item.nextElementSibling;

          item.tabIndex = -1;

          if (
            currOptions.toggleSubNav &&
            item.classList.contains('has-subnav') &&
            nextElement &&
            nextElement.tagName === 'UL'
          ) {
            processLinks(item, nextElement, index);
          }
        });
      }

      if (menuClone) {
        return menuClone;
      }
    }
  }

  function closeMenu() {
    const menu = overlay.querySelector('ul');
    const links = [...menu.querySelectorAll('.menu__link')];

    setTabIndex(links, -1);

    //Remove Event Listeners
    document.removeEventListener('keydown', handleKeyDown);

    const btns = [
      ...overlay.getElementsByClassName(currOptions.closeButtonClass),
    ];

    btns.forEach(btn => {
      setTabIndex(btn, -1);
      btn.removeEventListener('click', closeMenu);
    });

    overlay.classList.remove('is-open');
    overlay.setAttribute('style', 'display: none;');

    prevFocused.focus();
  }

  function openMenu() {
    const menu = overlay.querySelector('ul');
    const links = [...menu.querySelectorAll('.menu__link')];

    setTabIndex(links, 0);
    // ....
    prevFocused = document.activeElement;

    overlay.classList.add('is-open');

    const btns = [
      ...overlay.getElementsByClassName(currOptions.closeButtonClass),
    ];
    btns.forEach(btn => {
      setTabIndex(btn, 0);
      btn.addEventListener('click', closeMenu);
    });
    overlay.setAttribute('style', 'display: block;');

    document.addEventListener('keydown', handleKeyDown);
  }

  function handleKeyDown(e) {
    // Select all focusable items
    const focusable = overlay.querySelectorAll(
      'button, [href], input, select, textarea,[tabindex]:not([tabindex="-1"]'
    );

    const numberFocusElements = focusable.length;
    const firstFocusableElement = focusable[0];
    const lastFocusableElement = focusable[numberFocusElements - 1];

    // Close modal
    if (e.keyCode === KEYCODE.esc) {
      closeMenu();
    }

    // Trap Tab
    if (e.keyCode === KEYCODE.tab && e.shiftKey) {
      if (document.activeElement === firstFocusableElement) {
        e.preventDefault();
        lastFocusableElement.focus();
      }
    } else if (e.keyCode === KEYCODE.tab) {
      if (document.activeElement === lastFocusableElement) {
        e.preventDefault();
        firstFocusableElement.focus();
      }
    }
  }

  function setTabIndex(elem, tabIndex) {
    if (Array.isArray(elem)) {
      elem.forEach(function(item) {
        item.tabIndex = tabIndex;
      });
    } else {
      elem.tabIndex = tabIndex;
    }
  }

  function cleanString(string) {
    return string.toLowerCase().replace(' ', '-');
  }

  function processLinks(elem, controlled, index) {
    const thisNode = elem;
    const toggleButton = document.createElement('button');
    const firstLink = [...controlled.querySelectorAll('.menu__link')];

    const elemID = cleanString(`menu-${elem.innerText}${index ? index : ''}`);

    controlled.setAttribute('id', elemID);

    toggleButton.setAttribute('aria-controls', elemID);
    toggleButton.setAttribute('aria-expanded', 'false');
    toggleButton.innerHTML =
      '<span class="visually-hidden">Toggle SubNav</span>';
    toggleButton.addEventListener('click', function(e) {
      if (toggleButton.getAttribute('aria-expanded') === 'false') {
        e.currentTarget.setAttribute('aria-expanded', 'true');
        controlled.setAttribute('style', 'display: block;');
        firstLink[0].focus();
      } else {
        e.currentTarget.setAttribute('aria-expanded', 'false');
        controlled.setAttribute('style', 'display: none;');
      }
    });

    thisNode.parentNode.insertBefore(toggleButton, controlled);
  }

  return {
    init: initUI,
    close: closeMenu,
    open: openMenu,
  };
};

export default MobileMenu;
