
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

        // Charger les données des devises depuis le fichier JSON
        /* async function loadCurrencyData() {
            try {
                const response = await fetch('assets/frontend/ultimate/js/currencies.json');
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Erreur lors du chargement des données de devises:', error);
                // Fallback en cas d'erreur de chargement du JSON
                return {
                   countryToCurrency: {
                'US': 'USD', 'GB': 'GBP', 'JP': 'JPY', 'FR': 'EUR', 'DE': 'EUR',
                'CA': 'CAD', 'AU': 'AUD', 'MA': 'MAD', 'BR': 'BRL', 'IN': 'INR',
                'CN': 'CNY', 'RU': 'RUB', 'MX': 'MXN', 'ZA': 'ZAR', 'KR': 'KRW',
                'IT': 'EUR', 'ES': 'EUR', 'NL': 'EUR', 'SE': 'SEK', 'CH': 'CHF',
                'SG': 'SGD', 'NZ': 'NZD', 'AR': 'ARS', 'CL': 'CLP', 'CO': 'COP',
                'EG': 'EGP', 'NG': 'NGN', 'SA': 'SAR', 'AE': 'AED', 'TR': 'TRY',
                'PL': 'PLN', 'ID': 'IDR', 'TH': 'THB', 'MY': 'MYR', 'PH': 'PHP',
                'VN': 'VND', 'PK': 'PKR', 'BD': 'BDT', 'HK': 'HKD', 'TW': 'TWD',
                'KW': 'KWD', 'QA': 'QAR', 'IL': 'ILS', 'UA': 'UAH', 'CZ': 'CZK',
                'NO': 'NOK', 'DK': 'DKK', 'KE': 'KES', 'GH': 'GHS', 'SN': 'XOF',
                'CM': 'XAF', 'PE': 'PEN', 'VE': 'VES', 'LK': 'LKR', 'KZ': 'KZT'
            },
            currencySymbols: {
                'EUR': '€', 'USD': '$', 'GBP': '£', 'JPY': '¥', 'CAD': 'C$',
                'AUD': 'A$', 'MAD': 'MAD', 'BRL': 'R$', 'INR': '₹', 'CNY': '¥',
                'RUB': '₽', 'MXN': '$', 'ZAR': 'R', 'KRW': '₩', 'SEK': 'kr',
                'CHF': 'CHF', 'SGD': 'S$', 'NZD': 'NZ$', 'ARS': '$', 'CLP': '$',
                'COP': '$', 'EGP': '£', 'NGN': '₦', 'SAR': '﷼', 'AED': 'AED',
                'TRY': '₺', 'PLN': 'zł', 'IDR': 'Rp', 'THB': '฿', 'MYR': 'RM',
                'PHP': '₱', 'VND': '₫', 'PKR': '₨', 'BDT': '৳', 'HKD': 'HK$',
                'TWD': 'NT$', 'KWD': 'KD', 'QAR': 'QR', 'ILS': '₪', 'UAH': '₴',
                'CZK': 'Kč', 'NOK': 'kr', 'DKK': 'kr', 'KES': 'KSh', 'GHS': '₵',
                'XOF': 'CFA', 'XAF': 'CFA', 'PEN': 'S/', 'VES': 'Bs', 'LKR': 'Rs',
                'KZT': '₸'
            }
                };
            }
        }

        // Récupérer la devise à partir de l'IP avec IPinfo Lite
        async function getCurrencyFromIP(currencyData) {
            try {
                const response = await fetch('https://ipinfo.io/json?token=6767edf58cb8da');
                const data = await response.json();
                return currencyData.countryToCurrency[data.country] || 'EUR'; // Devise par défaut : EUR
            } catch (error) {
                console.error('Erreur lors de la détection IP:', error);
                return 'EUR'; // Devise par défaut en cas d'erreur
            }
        }
 */
        // Récupérer les taux de change avec Currency-API
        /* async function getExchangeRates() {
            try {
                const response = await fetch('https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/eur.min.json');
                const data = await response.json();
                return data.eur; // Taux de change par rapport à l'EUR
            } catch (error) {
                console.error('Erreur lors de la récupération des taux de change:', error);
                return { 
                    usd: 1.1, gbp: 0.85, jpy: 150, cad: 1.5, aud: 1.6, mad: 10.5, 
                    brl: 5.5, inr: 90, cny: 7.1, rub: 100, mxn: 20, zar: 18, 
                    krw: 1400, sek: 11, chf: 0.95, sgd: 1.35, nzd: 1.65, 
                    ars: 1000, clp: 950, cop: 4500, egp: 50, ngn: 1600, 
                    sar: 4.1, aed: 4.0, try: 34, pln: 4.3, idr: 16000, 
                    thb: 35, myr: 4.8, php: 60, vnd: 27000, pkr: 300, 
                    bdt: 120, hkd: 8.5, twd: 33 
                }; // Taux de secours
            }
        } */

        // Mettre à jour les prix affichés
            /* async function updatePrices() {
                const currencyData = await loadCurrencyData();
                const selectedCurrency = await getCurrencyFromIP(currencyData);
                const rates = await getExchangeRates();
                const priceCells = document.querySelectorAll('.price-row td[data-price]');

                priceCells.forEach(cell => {
                    const basePrice = parseFloat(cell.getAttribute('data-price'));
                    const convertedPrice = (basePrice * rates[selectedCurrency.toLowerCase()]).toFixed(2);
                    cell.textContent = `${currencyData.currencySymbols[selectedCurrency]} ${convertedPrice}/${window.translations.month}`;
                    if (basePrice === 0) {
                        cell.textContent = `${currencyData.currencySymbols[selectedCurrency]}0`;
                    }
                });
            } */

           // Mettre à jour les prix affichés
  async function updatePrices() {
    try {
      // Détection du pays de l’utilisateur
      const response = await fetch('https://api.country.is/');
      const data = await response.json();
      const country = data.country;

      // Définition des prix fixes
      const prices = {
        'MA': {
          value: 790,
          currency: 'DH'
        },
        'AE': {
          value: 299, 
          currency: 'AED'
        }
      };

      // Fallback par défaut (Maroc)
      const priceData = prices[country] || prices['MA'];

      // Mise à jour des éléments prix
      const priceElements = document.querySelectorAll('.plan-price-split');

      priceElements.forEach(element => {
        const priceValue = element.querySelector('.price-value');
        const priceCurrency = element.querySelector('.price-currency');

        if (priceValue) priceValue.textContent = priceData.value;
        if (priceCurrency) priceCurrency.textContent = priceData.currency;
      });

    } catch (error) {
      console.error('Error updating prices:', error);
    }
  }
          // Récupérer les taux de change avec Currency-API
          async function getExchangeRate(from, to) {
              try {
                  // Using free exchangerate-api.com
                  const response = await fetch(`https://api.exchangerate-api.com/v4/latest/${from}`);
                  const data = await response.json();
                  return data.rates[to] || 0.4; // Fallback rate if API fails
              } catch (error) {
                  console.error('Error fetching exchange rate:', error);
                  return 0.4; // Fallback: 1 MAD ≈ 0.4 AED
              }
          }
        // Initialiser les prix au chargement de la page
        document.addEventListener('DOMContentLoaded', updatePrices);
