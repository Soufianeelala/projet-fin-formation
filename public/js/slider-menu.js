// public/js/slider-menu.js
    // ===== MENU BURGER =====
document.addEventListener("DOMContentLoaded", () => {
  const burger  = document.getElementById("burger");
  const menu    = document.getElementById("menu");
  const overlay = document.getElementById("overlay");
  const body    = document.body;

  function toggleMenu() {
    menu.classList.toggle("active");
    burger.classList.toggle("active");
    overlay.classList.toggle("active");
    body.classList.toggle("no-scroll");
  }

  function closeMenu() {
    menu.classList.remove("active");
    burger.classList.remove("active");
    overlay.classList.remove("active");
    body.classList.remove("no-scroll");
  }

  if (burger && menu && overlay) {
    burger.addEventListener("click", e => {
      e.stopPropagation(); // Important : empêche la fermeture immédiate
      toggleMenu();
    });

    overlay.addEventListener("click", closeMenu);

    document.addEventListener("click", e => {
      const clickedOutsideMenu = !menu.contains(e.target);
      const clickedOutsideBurger = !burger.contains(e.target);
      const isMenuActive = menu.classList.contains("active");

      if (isMenuActive && clickedOutsideMenu && clickedOutsideBurger) {
        closeMenu();
      }
    });

    document.addEventListener("keydown", e => {
      if (e.key === "Escape") {
        closeMenu();
      }
    });
  }


  
    // ===== SLIDER =====
    let currentIndex = 0;
    const slides = document.querySelectorAll(".slide");
    const dots   = document.querySelectorAll(".dot");
    let autoSlide;
  
    function showSlide(i) {
      slides.forEach(s => s.classList.remove("active"));
      dots.forEach(d => d.classList.remove("active"));
      slides[i].classList.add("active");
      dots[i].classList.add("active");
      currentIndex = i;
    }
  
    function nextSlide() { showSlide((currentIndex + 1) % slides.length); }
    function prevSlide() { showSlide((currentIndex + slides.length - 1) % slides.length); }
    function startAuto() { autoSlide = setInterval(nextSlide, 3000); }
    function stopAuto()  { clearInterval(autoSlide); }
  
    document.querySelector(".next")?.addEventListener("click", nextSlide);
    document.querySelector(".prev")?.addEventListener("click", prevSlide);
    document.querySelector(".slider-container")?.addEventListener("mouseenter", stopAuto);
    document.querySelector(".slider-container")?.addEventListener("mouseleave", startAuto);
  
    showSlide(0);
    startAuto();
  });
  