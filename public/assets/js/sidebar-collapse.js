/**
 * Sequence Sidebar Collapse/Expand Functionality
 * Handles sidebar toggle with smooth animations
 */

(function() {
  'use strict';

  // Initialize on DOM ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebar);
  } else {
    initSidebar();
  }

  function initSidebar() {
    const sidebar = document.querySelector('#layout-menu');
    const toggleBtn = document.querySelector('.layout-menu-toggle');
    const layoutPage = document.querySelector('.layout-page');
    
    if (!sidebar || !toggleBtn) {
      console.warn('Sidebar or toggle button not found');
      return;
    }

    // IMPORTANT: Default sidebar is EXPANDED, not collapsed
    // Load saved state from localStorage
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true') {
      sidebar.classList.add('collapsed');
      if (layoutPage) {
        layoutPage.style.paddingLeft = '80px';
      }
    } else {
      // Ensure sidebar is expanded by default
      sidebar.classList.remove('collapsed');
      if (layoutPage) {
        layoutPage.style.paddingLeft = '260px';
      }
    }

    // Add data-label attributes for tooltips
    addTooltipLabels();

    // Toggle sidebar on button click
    toggleBtn.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      toggleSidebar();
    });

    // Optional: Toggle with keyboard shortcut (Ctrl + B)
    document.addEventListener('keydown', function(e) {
      if (e.ctrlKey && e.key === 'b') {
        e.preventDefault();
        toggleSidebar();
      }
    });
  }

  function toggleSidebar() {
    const sidebar = document.querySelector('#layout-menu');
    const layoutPage = document.querySelector('.layout-page');
    const isCollapsed = sidebar.classList.contains('collapsed');

    if (isCollapsed) {
      // Expand
      sidebar.classList.remove('collapsed');
      if (layoutPage) {
        layoutPage.style.paddingLeft = '260px';
      }
      localStorage.setItem('sidebarCollapsed', 'false');
    } else {
      // Collapse
      sidebar.classList.add('collapsed');
      if (layoutPage) {
        layoutPage.style.paddingLeft = '80px';
      }
      localStorage.setItem('sidebarCollapsed', 'true');
      
      // Close all open submenus when collapsing
      const openMenus = sidebar.querySelectorAll('.menu-item.open');
      openMenus.forEach(menu => menu.classList.remove('open'));
    }
  }

  function addTooltipLabels() {
    const menuItems = document.querySelectorAll('.menu-item');
    
    menuItems.forEach(item => {
      const link = item.querySelector('.menu-link');
      if (link) {
        const textDiv = link.querySelector('div');
        if (textDiv && textDiv.textContent) {
          const label = textDiv.textContent.trim();
          item.setAttribute('data-label', label);
          
          // Also set title for native tooltip fallback
          item.setAttribute('title', '');
          link.setAttribute('title', '');
        }
      }
    });
  }

  // Note: Submenu toggle is handled by sequence-sidebar.js
  // This script only handles sidebar collapse/expand

  // Prevent default tooltip when sidebar is collapsed
  document.addEventListener('mouseenter', function(e) {
    const sidebar = document.querySelector('#layout-menu');
    if (!sidebar) return;

    if (sidebar.classList.contains('collapsed')) {
      const menuItem = e.target.closest('.menu-item');
      if (menuItem) {
        // Remove native title to use CSS tooltip
        menuItem.removeAttribute('title');
        const link = menuItem.querySelector('.menu-link');
        if (link) {
          link.removeAttribute('title');
        }
      }
    }
  }, true);

  // Smooth scroll for menu
  const menuInner = document.querySelector('.menu-inner');
  if (menuInner) {
    menuInner.style.scrollBehavior = 'smooth';
  }

})();
