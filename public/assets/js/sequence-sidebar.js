/**
 * Sequence Dashboard Sidebar JavaScript
 * Handles menu navigation, active states, and submenu toggling
 */
(function () {
  'use strict';

  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar);
  } else {
    initSidebar();
  }

  function initSidebar() {
    console.log('Sequence Sidebar: Initializing...');

    // Clear all active/open states on init
    clearAllStates();
    
    // Set active menu based on current URL
    setActiveMenuFromURL();
    
    initMenuToggle();

    console.log('Sequence Sidebar: Initialized successfully');
  }

  /**
   * Clear all active and open states
   */
  function clearAllStates() {
    const menuItems = document.querySelectorAll('.menu-inner .menu-item');
    menuItems.forEach(item => {
      item.classList.remove('active', 'open');
    });
    
    // Hide all submenus
    document.querySelectorAll('.menu-sub').forEach(submenu => {
      submenu.style.display = 'none';
    });
  }

  /**
   * Set active menu item based on current URL
   */
  function setActiveMenuFromURL() {
    const currentPath = window.location.pathname;
    const menuLinks = document.querySelectorAll('.menu-inner .menu-link');

    let bestMatch = null;
    let bestMatchLength = 0;

    menuLinks.forEach(link => {
      const href = link.getAttribute('href');

      if (!href || href === '#' || href === 'javascript:void(0);') {
        return;
      }

      // Get pathname from href
      let linkPath;
      try {
        const url = new URL(href, window.location.origin);
        linkPath = url.pathname;
      } catch (e) {
        linkPath = href.startsWith('/') ? href : '/' + href;
      }

      // Normalize paths (remove trailing slashes)
      const normalizedCurrent = currentPath.replace(/\/$/, '') || '/';
      const normalizedLink = linkPath.replace(/\/$/, '') || '/';

      // Check for EXACT match first - this is highest priority
      if (normalizedCurrent === normalizedLink) {
        // Exact match - prioritize longer paths (more specific)
        if (normalizedLink.length > bestMatchLength) {
          bestMatch = link;
          bestMatchLength = normalizedLink.length;
        }
      }
    });

    // Activate the best matching menu item
    if (bestMatch) {
      activateMenuItem(bestMatch);
    }
  }

  /**
   * Activate a menu item and its parent if in submenu
   */
  function activateMenuItem(link) {
    const menuItem = link.closest('.menu-item');

    if (!menuItem) return;

    // Add active class to menu item
    menuItem.classList.add('active');

    // If inside submenu, also open and activate parent
    const parentSubmenu = menuItem.closest('.menu-sub');
    if (parentSubmenu) {
      const parentMenuItem = parentSubmenu.closest('.menu-item');

      if (parentMenuItem) {
        parentMenuItem.classList.add('active', 'open');

        // Show the submenu
        parentSubmenu.style.display = 'block';

        // Recursively check for nested submenus
        const grandParentSubmenu = parentMenuItem.closest('.menu-sub');
        if (grandParentSubmenu) {
          const grandParentMenuItem = grandParentSubmenu.closest('.menu-item');
          if (grandParentMenuItem) {
            grandParentMenuItem.classList.add('active', 'open');
            grandParentSubmenu.style.display = 'block';
          }
        }
      }
    }
  }

  /**
   * Initialize menu toggle functionality
   */
  function initMenuToggle() {
    const menuToggles = document.querySelectorAll('.menu-toggle');

    menuToggles.forEach(toggle => {
      // Remove existing event listeners by cloning
      const newToggle = toggle.cloneNode(true);
      toggle.parentNode.replaceChild(newToggle, toggle);

      // Add click handler
      newToggle.addEventListener('click', handleMenuToggle);
    });

    // Initialize submenu display states
    document.querySelectorAll('.menu-sub').forEach(submenu => {
      const parentItem = submenu.closest('.menu-item');
      if (parentItem && parentItem.classList.contains('open')) {
        submenu.style.display = 'block';
      } else {
        submenu.style.display = 'none';
      }
    });
  }

  /**
   * Handle menu toggle click
   */
  function handleMenuToggle(e) {
    e.preventDefault();
    e.stopPropagation();

    const toggle = e.currentTarget;
    const menuItem = toggle.closest('.menu-item');
    const submenu = menuItem.querySelector(':scope > .menu-sub');

    if (!submenu) return;

    const isOpen = menuItem.classList.contains('open');

    // Close sibling menus at the same level (accordion behavior)
    const siblingItems = menuItem.parentElement.querySelectorAll(':scope > .menu-item');
    siblingItems.forEach(sibling => {
      if (sibling !== menuItem && sibling.classList.contains('open')) {
        sibling.classList.remove('open');
        const siblingSubmenu = sibling.querySelector(':scope > .menu-sub');
        if (siblingSubmenu) {
          slideUp(siblingSubmenu);
        }
      }
    });

    // Toggle current menu
    if (isOpen) {
      menuItem.classList.remove('open');
      slideUp(submenu);
    } else {
      menuItem.classList.add('open');
      slideDown(submenu);
    }

    return false;
  }

  /**
   * Slide down animation for submenu
   */
  function slideDown(element) {
    element.style.display = 'block';
    element.style.overflow = 'hidden';
    element.style.height = '0';

    const height = element.scrollHeight;

    element.style.transition = 'height 0.3s ease-in-out';
    element.style.height = height + 'px';

    setTimeout(() => {
      element.style.height = '';
      element.style.overflow = '';
      element.style.transition = '';
    }, 300);
  }

  /**
   * Slide up animation for submenu
   */
  function slideUp(element) {
    element.style.overflow = 'hidden';
    element.style.height = element.scrollHeight + 'px';

    element.style.transition = 'height 0.3s ease-in-out';

    requestAnimationFrame(() => {
      element.style.height = '0';
    });

    setTimeout(() => {
      element.style.display = 'none';
      element.style.height = '';
      element.style.overflow = '';
      element.style.transition = '';
    }, 300);
  }

  // Handle dynamic navigation (for SPA-like behavior)
  window.addEventListener('popstate', function () {
    clearAllStates();
    setActiveMenuFromURL();
  });

})();
