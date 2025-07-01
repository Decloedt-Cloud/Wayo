
$(document).ready(function () {
  $(".owl-carousel").owlCarousel({
    center: true,
    loop: true,       
    margin: 50,       
    autoplay: true,   
    nav: true,
    autoWidth: true,    
    items: 3,              
    responsive: {
      0: {
        items: 1,          
      },
      600: {
        items: 2,         
      },
      1000: {
        items: 3, 
        margin: 50
      }
    }
  });

const featureTabs = Array.from(document.querySelectorAll(".feature-nav li"));
const featurePanes = Array.from(document.querySelectorAll(".feature-pane"));
const progressBar = document.querySelector(".feature-progress-bar");

if (featureTabs.length && featurePanes.length) {
  let featureIndex = 0;
  const featureCount = featureTabs.length;
  const AUTO_SCROLL_INTERVAL = 5000; // 5 secondes

  function goToFeature(index) {
    featureTabs[featureIndex].classList.remove("active");
    featurePanes[featureIndex].classList.remove("active");

    featureIndex = index;

    featureTabs[featureIndex].classList.add("active");
    featurePanes[featureIndex].classList.add("active");

    // Mettre à jour la largeur de la barre de progression
    updateProgressBar();
  }

  function goToNextFeature() {
    const nextIndex = (featureIndex + 1) % featureCount;
    goToFeature(nextIndex);
  }

  function updateProgressBar() {
    // Détecter si le mode est RTL en vérifiant feature-nav-section
    const isRTL = document.querySelector('.feature-nav-section')?.getAttribute('dir') === 'rtl';

    // Calculer la largeur en pourcentage : (index actuel + 1) / nombre total de slides * 100
    const progressWidth = ((featureIndex + 1) / featureCount) * 100;
    progressBar.style.width = `${progressWidth}%`; // Progression normale de 0% à 100%

    // Supprimer et réappliquer la transition pour une animation fluide
    progressBar.style.transition = 'width 0.3s ease';
  }

  featureTabs.forEach((tab, idx) => {
    tab.addEventListener("click", () => {
      goToFeature(idx);
      clearInterval(autoScrollTimer);
      autoScrollTimer = setInterval(goToNextFeature, AUTO_SCROLL_INTERVAL);
    });
  });

  // Initialiser la barre de progression
  updateProgressBar();
  let autoScrollTimer = setInterval(goToNextFeature, AUTO_SCROLL_INTERVAL);
}


    // carousel phares
const track = document.querySelector(".carousel-track");
const slides = Array.from(track?.children || []);
const prevBtn = document.querySelector(".carousel-btn.prev");
const nextBtn = document.querySelector(".carousel-btn.next");
const carousel = document.querySelector(".courses-carousel");

if (slides.length) {
  // Calculer le nombre de slides visibles à la fois (basé sur la largeur de l'écran)
  const getSlidesPerView = () => {
    if (window.innerWidth >= 1024) return 4; // 4 slides sur desktop
    if (window.innerWidth >= 768) return 3; // 3 slides sur tablette
    return 1; // 1 slide sur mobile
  };

  // Cloner les slides pour la boucle infinie
  const slidesPerView = getSlidesPerView();
  const clonesBefore = slides.slice(-slidesPerView).map(slide => slide.cloneNode(true));
  const clonesAfter = slides.slice(0, slidesPerView).map(slide => slide.cloneNode(true));
  clonesBefore.forEach(clone => track.insertBefore(clone, slides[0]));
  clonesAfter.forEach(clone => track.appendChild(clone));

  // Mettre à jour la liste des slides après clonage
  const allSlides = Array.from(track.children);
  const totalSlides = allSlides.length;

  const updateSlideWidth = () => {
    const slideWidth = allSlides[0].getBoundingClientRect().width + 20; // Inclut le gap
    allSlides.forEach((slide, idx) => {
      slide.style.left = slideWidth * idx + "px";
    });
    return slideWidth;
  };

  let slideWidth = updateSlideWidth();
  // Démarrer à l'index correspondant au premier slide original
  let currentIndex = slidesPerView;
  let autoSlideInterval;

  // Positionner initialement sur le premier slide original
  track.style.transform = `translateX(-${slideWidth * currentIndex}px)`;

  const moveTo = (idx, animate = true) => {
    track.style.transition = animate ? "transform 0.5s ease" : "none";
    track.style.transform = `translateX(-${slideWidth * idx}px)`;
    currentIndex = idx;

    // Réinitialiser pour la boucle infinie
    if (currentIndex >= totalSlides - slidesPerView) {
      setTimeout(() => {
        track.style.transition = "none";
        currentIndex = slidesPerView;
        track.style.transform = `translateX(-${slideWidth * currentIndex}px)`;
      }, 500); // Correspond à la durée de la transition
    }
    if (currentIndex < slidesPerView) {
      setTimeout(() => {
        track.style.transition = "none";
        currentIndex = totalSlides - slidesPerView * 2;
        track.style.transform = `translateX(-${slideWidth * currentIndex}px)`;
      }, 500);
    }
  };

  const startAutoSlide = () => {
    clearInterval(autoSlideInterval);
    autoSlideInterval = setInterval(() => {
      moveTo(currentIndex + 1);
    }, 3000);
  };

  const stopAutoSlide = () => {
    clearInterval(autoSlideInterval);
  };

  prevBtn.addEventListener("click", () => {
    stopAutoSlide();
    moveTo(currentIndex - 1);
    startAutoSlide();
  });

  nextBtn.addEventListener("click", () => {
    stopAutoSlide();
    moveTo(currentIndex + 1);
    startAutoSlide();
  });

  carousel.addEventListener("mouseenter", stopAutoSlide);
  carousel.addEventListener("mouseleave", startAutoSlide);

  // Swipe support for mobile
  let touchStartX = 0;
  let touchEndX = 0;
  carousel.addEventListener("touchstart", (e) => {
    touchStartX = e.changedTouches[0].screenX;
  });

  carousel.addEventListener("touchend", (e) => {
    touchEndX = e.changedTouches[0].screenX;
    const swipeDistance = touchEndX - touchStartX;
    const minSwipeDistance = 50;
    stopAutoSlide();
    if (swipeDistance > minSwipeDistance && currentIndex >= slidesPerView) {
      moveTo(currentIndex - 1);
    } else if (swipeDistance < -minSwipeDistance && currentIndex < totalSlides - slidesPerView) {
      moveTo(currentIndex + 1);
    }
    startAutoSlide();
  });

  window.addEventListener("resize", () => {
    const newSlidesPerView = getSlidesPerView();
    if (newSlidesPerView !== slidesPerView) {
      // Re-cloner les slides si le nombre de slides visibles change
      while (track.firstChild) track.removeChild(track.firstChild);
      slides.forEach(slide => track.appendChild(slide));
      const clonesBefore = slides.slice(-newSlidesPerView).map(slide => slide.cloneNode(true));
      const clonesAfter = slides.slice(0, newSlidesPerView).map(slide => slide.cloneNode(true));
      clonesBefore.forEach(clone => track.insertBefore(clone, slides[0]));
      clonesAfter.forEach(clone => track.appendChild(clone));
    }
    slideWidth = updateSlideWidth();
    currentIndex = newSlidesPerView || slidesPerView;
    moveTo(currentIndex, false);
  });

  startAutoSlide();
}

    //SUCCESS STORIES
    const storiesTrack = document.querySelector(".stories-track");
const storySlides = Array.from(storiesTrack?.children || []);
const storiesPrev = document.querySelector(".stories-btn.prev");
const storiesNext = document.querySelector(".stories-btn.next");
const storiesCarousel = document.querySelector(".stories-carousel");

if (storySlides.length) {
  // Calculer le nombre de slides visibles à la fois
  const getSlidesPerView = () => {
    if (window.innerWidth >= 1024) return 3; // 3 slides sur desktop
    if (window.innerWidth >= 768) return 2; // 2 slides sur tablette
    return 1; // 1 slide sur mobile
  };

  let slidesPerView = getSlidesPerView();

  // Cloner les slides pour la boucle infinie
  const clonesBefore = storySlides.slice(-slidesPerView).map(slide => slide.cloneNode(true));
  const clonesAfter = storySlides.slice(0, slidesPerView).map(slide => slide.cloneNode(true));
  clonesBefore.forEach(clone => storiesTrack.insertBefore(clone, storySlides[0]));
  clonesAfter.forEach(clone => storiesTrack.appendChild(clone));

  // Mettre à jour la liste des slides avec les clones
  const allSlides = Array.from(storiesTrack.children);
  const totalSlides = allSlides.length;

  const updateSlideWidth = () => {
    const storyWidth = storySlides[0].getBoundingClientRect().width + 20;
    allSlides.forEach((slide, idx) => {
      slide.style.left = storyWidth * idx + "px";
    });
    return storyWidth;
  };

  let storyWidth = updateSlideWidth();
  let storyIndex = slidesPerView; // Commencer au premier slide réel
  let autoSlideInterval;
  let touchStartX = 0;
  let touchEndX = 0;

  // Positionner le carrousel au premier slide réel au démarrage
  storiesTrack.style.transform = `translateX(-${storyWidth * storyIndex}px)`;

  const moveStory = (idx, withTransition = true) => {
    storiesTrack.style.transition = withTransition ? "transform 0.5s ease" : "none";
    storiesTrack.style.transform = `translateX(-${storyWidth * idx}px)`;
    storiesTrack.offsetHeight; // Forcer un reflow
    storyIndex = idx;

    // Réinitialiser pour la boucle infinie
    if (storyIndex >= totalSlides - slidesPerView) {
      setTimeout(() => {
        storiesTrack.style.transition = "none";
        storyIndex = slidesPerView;
        storiesTrack.style.transform = `translateX(-${storyWidth * storyIndex}px)`;
      }, 500); // Correspond à la durée de la transition
    }
    if (storyIndex < slidesPerView) {
      setTimeout(() => {
        storiesTrack.style.transition = "none";
        storyIndex = totalSlides - slidesPerView * 2;
        storiesTrack.style.transform = `translateX(-${storyWidth * storyIndex}px)`;
      }, 500);
    }
  };

  const startAutoSlide = () => {
    clearInterval(autoSlideInterval);
    autoSlideInterval = setInterval(() => {
      moveStory(storyIndex + 1);
    }, 3000);
  };

  const stopAutoSlide = () => {
    clearInterval(autoSlideInterval);
  };

  // Gestion des boutons
  storiesPrev.addEventListener("click", () => {
    stopAutoSlide();
    moveStory(storyIndex - 1);
    startAutoSlide();
  });

  storiesNext.addEventListener("click", () => {
    stopAutoSlide();
    moveStory(storyIndex + 1);
    startAutoSlide();
  });

  // Gestion du survol
  storiesCarousel.addEventListener("mouseenter", stopAutoSlide);
  storiesCarousel.addEventListener("mouseleave", startAutoSlide);

  // Gestion des swipes
  storiesCarousel.addEventListener("touchstart", (e) => {
    touchStartX = e.changedTouches[0].screenX;
  });

  storiesCarousel.addEventListener("touchend", (e) => {
    touchEndX = e.changedTouches[0].screenX;
    const swipeDistance = touchEndX - touchStartX;
    const minSwipeDistance = 50;
    stopAutoSlide();
    if (swipeDistance > minSwipeDistance && storyIndex >= slidesPerView) {
      moveStory(storyIndex - 1);
    } else if (swipeDistance < -minSwipeDistance) {
      moveStory(storyIndex + 1);
    }
    startAutoSlide();
  });

  // Gestion du redimensionnement
  const debounce = (func, wait) => {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), wait);
    };
  };

  window.addEventListener("resize", debounce(() => {
    const newSlidesPerView = getSlidesPerView();
    if (newSlidesPerView !== slidesPerView) {
      // Re-cloner les slides si le nombre de slides visibles change
      while (storiesTrack.firstChild) storiesTrack.removeChild(storiesTrack.firstChild);
      storySlides.forEach(slide => storiesTrack.appendChild(slide));
      slidesPerView = newSlidesPerView;
      const clonesBefore = storySlides.slice(-slidesPerView).map(slide => slide.cloneNode(true));
      const clonesAfter = storySlides.slice(0, slidesPerView).map(slide => slide.cloneNode(true));
      clonesBefore.forEach(clone => storiesTrack.insertBefore(clone, storySlides[0]));
      clonesAfter.forEach(clone => storiesTrack.appendChild(clone));
      allSlides.length = 0; // Vider le tableau
      allSlides.push(...Array.from(storiesTrack.children));
      totalSlides = allSlides.length;
    }
    storyWidth = updateSlideWidth();
    storyIndex = slidesPerView;
    moveStory(storyIndex, false);
  }, 100));

  // Optimisation des vidéos
  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach(entry => {
        const video = entry.target;
        if (entry.isIntersecting) {
          video.play().catch(error => {
            console.log(`Erreur de lecture pour ${video.id} : `, error);
          });
        } else {
          video.pause();
          video.currentTime = 0; // Réinitialiser pour éviter les décalages
        }
      });
    },
    { threshold: 0.3 }
  );

  allSlides.forEach(slide => {
    const video = slide.querySelector(".video-player");
    if (video) observer.observe(video);
    const img = slide.querySelector("img");
    if (img) img.loading = "eager";
  });

  startAutoSlide();
}
});

document.addEventListener("DOMContentLoaded", function () {
  const links = document.querySelectorAll("a[href^='#']");
  links.forEach(link => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const targetId = this.getAttribute("href").substring(1);
      const targetElement = document.getElementById(targetId);
      if (targetElement) {
        targetElement.scrollIntoView({ behavior: "smooth" });
      }
    });
  });
});