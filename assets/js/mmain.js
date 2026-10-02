/**
* Template Name: Bootslander
* Template URL: https://bootstrapmade.com/bootslander-free-bootstrap-landing-page-template/
* Updated: Aug 07 2024 with Bootstrap v5.3.3
* Author: BootstrapMade.com
* License: https://bootstrapmade.com/license/
*/

(function() {
  "use strict";

  /**
   * Apply .scrolled class to the body as the page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

 
 const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');
 
 function mobileNavToogle() {
   document.querySelector('body').classList.toggle('mobile-nav-active');
   mobileNavToggleBtn.classList.toggle('bi-list');
   mobileNavToggleBtn.classList.toggle('bi-x');
 }
 mobileNavToggleBtn.addEventListener('click', mobileNavToogle);
 
 /**
  * Hide mobile nav on same-page/hash links
  */
 document.querySelectorAll('#navmenu a').forEach(navmenu => {
   navmenu.addEventListener('click', () => {
     if (document.querySelector('body').classList.contains('mobile-nav-active')) {
       mobileNavToogle();
     }
   });
 });
 
 /**
  * Toggle mobile nav dropdowns
  */
 document.querySelectorAll('.navmenu .toggle-dropdown').forEach(navmenu => {
   navmenu.addEventListener('click', function(e) {
     e.preventDefault();
     this.parentNode.classList.toggle('active');
     this.parentNode.nextElementSibling.classList.toggle('dropdown-active');
     e.stopImmediatePropagation();
   });
 });
 
  
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      preloader.remove();
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');

  function toggleScrollTop() {
    if (scrollTop) {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    }
  }
  scrollTop.addEventListener('click', (e) => {
    e.preventDefault();
    window.scrollTo({
      top: 0,
      behavior: 'smooth'
    });
  });

  window.addEventListener('load', toggleScrollTop);
  document.addEventListener('scroll', toggleScrollTop);

  /**
   * Animation on scroll function and init
   */
  function aosInit() {
    AOS.init({
      duration: 600,
      easing: 'ease-in-out',
      once: true,
      mirror: false
    });
  }
  window.addEventListener('load', aosInit);

  /**
   * Initiate glightbox
   */
  const glightbox = GLightbox({
    selector: '.glightbox'
  });

  /**
   * Initiate Pure Counter
   */
  new PureCounter();

  /**
   * Init swiper sliders
   */
  function initSwiper() {
    document.querySelectorAll(".init-swiper").forEach(function(swiperElement) {
      let config = JSON.parse(
        swiperElement.querySelector(".swiper-config").innerHTML.trim()
      );

      if (swiperElement.classList.contains("swiper-tab")) {
        initSwiperWithCustomPagination(swiperElement, config);
      } else {
        new Swiper(swiperElement, config);
      }
    });
  }

  window.addEventListener("load", initSwiper);

  /**
   * Frequently Asked Questions Toggle
   */
  document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((faqItem) => {
    faqItem.addEventListener('click', () => {
      faqItem.parentNode.classList.toggle('faq-active');
    });
  });

  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function(e) {
    if (window.location.hash) {
      if (document.querySelector(window.location.hash)) {
        setTimeout(() => {
          let section = document.querySelector(window.location.hash);
          let scrollMarginTop = getComputedStyle(section).scrollMarginTop;
          window.scrollTo({
            top: section.offsetTop - parseInt(scrollMarginTop),
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');

  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    })
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);

})();
 

 


document.body.style.userSelect = 'none';  // Disable text selection on the whole page

// Optional: To allow text selection only for specific elements
document.querySelector('.allow-selection').style.userSelect = 'text';
document.addEventListener('keydown', function(e) {
  if (e.key === 'PrintScreen') {
      alert('Screenshot action blocked!');
      e.preventDefault();
  }
});
document.addEventListener('keydown', function(e) {
  // Block DevTools shortcuts
  if (e.key === 'F12' || (e.ctrlKey && e.shiftKey && e.key === 'I')) {
      alert("Developer tools are disabled!");
      e.preventDefault();
  }
});



  // Function to open the modal
  // Function to open the modal
  function openModal(game) {
    var modal = document.getElementById(game);
    modal.style.display = "block";
  }
  
  // Function to close the modal
  function closeModal(game) {
    var modal = document.getElementById(game);
    modal.style.display = "none";
  }
  
  // Event listener for closing modal when clicking outside of the modal content
  window.onclick = function(event) {
    // Check if the clicked area is outside of any modal content
    var modals = document.querySelectorAll('.modal'); // Get all modals
    modals.forEach(function(modal) {
      var modalContent = modal.querySelector('.modal-content');
      
      // Close the modal if clicked outside modal-content
      if (event.target === modal) {
        modal.style.display = "none"; // Close the modal
      }
    });
  }
  
  
  document.addEventListener("DOMContentLoaded", function () {
    // Modal open/close functionality
    window.onclick = function(event) {
      const modals = document.querySelectorAll('.modal');
      modals.forEach(function(modal) {
        if (event.target === modal) {
          modal.style.display = "none";
        }
      });
    };
  
    // Show More button for features
    document.getElementById('show-more-btn').addEventListener('click', function () {
      const hiddenFeatures = document.getElementById('hidden-features');
      if (hiddenFeatures.style.display === 'none') {
        hiddenFeatures.style.display = 'flex';
        this.textContent = 'Show Less';
      } else {
        hiddenFeatures.style.display = 'none';
        this.textContent = 'More';
      }
    });
  
    // Show More button for details
    document.getElementById('details-more-btn').addEventListener('click', function () {
      const hiddenDetails = document.getElementById('hidden-details');
      if (hiddenDetails.style.display === 'none') {
        hiddenDetails.style.display = 'block';
        this.textContent = 'Show Less';
      } else {
        hiddenDetails.style.display = 'none';
        this.textContent = 'More';
      }
    });
  });
  const lightbox = GLightbox({
      touchNavigation: true,
      loop: true,
      zoomable: true,
      autoplayVideos: false
    });
  



    function redirectToForm(gameType) {
      console.log('Game selected:', gameType);  // Debug log to see which game is selected
  
      // Store the selected game in localStorage
      localStorage.setItem("selectedGame", gameType);
  
      // Check if the game is stored correctly
      console.log('Selected game stored in localStorage:', localStorage.getItem("selectedGame"));
      
      // Redirect to secondary.html
      window.location.href = "secondary.php";
  }
  
     // Check if the flag exists in sessionStorage
     window.onload = function() {
              // Check if user came back after clicking 'Buy' button
              if (sessionStorage.getItem("buyClicked") === "true") {
                  // Show the alert message
                  alert("🎉 Thank you for your order! 🎮\n\nYour order has been successfully placed on WhatsApp. 📱💬 Please head over to WhatsApp to confirm your purchase. We'll be in touch with you shortly for confirmation. 🙌\n\nWe appreciate your business and look forward to serving you! 🌟");
  
                  // Remove the flag so the alert doesn't show again after page reload
                  sessionStorage.removeItem("buyClicked");
              }
          };
  
          // Function to set the flag when the 'Buy' button is clicked
          function handleBuyClick() {
              // Set a flag in sessionStorage that the user clicked 'Buy'
              sessionStorage.setItem("buyClicked", "true");
          }
          function showMore() {
      const hiddenItems = document.querySelectorAll('.pricing-item[style*="display: none"]');
      hiddenItems.forEach((item) => {
        item.style.display = "block";
      });
      document.getElementById('showMoreBtn').style.display = "none"; // Hide the button after showing all cards
    }



    
    function toggleDiamondList() {
    const hiddenItems = document.querySelectorAll('#diamondList .hidden-item');
    const toggleButton = document.getElementById('toggleListBtn');
  
    // Check current state of the list and toggle visibility
    const isHidden = hiddenItems[0].style.display === 'none' || !hiddenItems[0].style.display;
    
    hiddenItems.forEach(item => {
      item.style.display = isHidden ? 'list-item' : 'none';
    });
  
    // Update button text
    toggleButton.textContent = isHidden ? 'Show Less' : 'Show More';
  }
  



  // Initialize list state (optional but ensures hidden items are initially hidden)
  document.addEventListener('DOMContentLoaded', () => {
    const hiddenItems = document.querySelectorAll('#diamondList .hidden-item');
    hiddenItems.forEach(item => {
      item.style.display = 'none';
    });
  });
  




