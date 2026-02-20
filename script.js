// Fonction pour ouvrir/fermer le menu
function toggleMenu() {
  const navMenu = document.getElementById("navMenu");
  const overlay = document.getElementById("overlay");

  navMenu.classList.toggle("active");
  overlay.classList.toggle("active");
}

// Fermer le menu avec la touche Escape
document.addEventListener("keydown", function (e) {
  if (e.key === "Escape") {
    const navMenu = document.getElementById("navMenu");
    const overlay = document.getElementById("overlay");

    if (navMenu.classList.contains("active")) {
      navMenu.classList.remove("active");
      overlay.classList.remove("active");
    }

    // Fermer aussi le modal de la galerie si ouvert
    const modal = document.getElementById("galleryModal");
    if (modal && modal.classList.contains("active")) {
      closeModal();
    }
  }
});

// CARROUSEL
let currentSlide = 0;
const slides = document.querySelectorAll(".carousel-slide");
const indicators = document.querySelectorAll(".indicator");

function showSlide(index) {
  if (slides.length === 0) return;

  // Boucler l'index
  if (index >= slides.length) currentSlide = 0;
  else if (index < 0) currentSlide = slides.length - 1;
  else currentSlide = index;

  // Masquer toutes les slides
  slides.forEach((slide) => slide.classList.remove("active"));

  // Désactiver tous les indicateurs
  if (indicators.length > 0) {
    indicators.forEach((ind) => ind.classList.remove("active"));
  }

  // Afficher la slide courante
  slides[currentSlide].classList.add("active");

  // Activer l'indicateur correspondant
  if (indicators.length > 0) {
    indicators[currentSlide].classList.add("active");
  }
}

function changeSlide(direction) {
  showSlide(currentSlide + direction);
}

function goToSlide(index) {
  showSlide(index);
}

// Auto-play du carrousel (toutes les 5 secondes)
if (slides.length > 0) {
  setInterval(() => {
    changeSlide(1);
  }, 5000);
}

// GALERIE
let currentImageIndex = 0;
const galleryImages = [
  "images/photo1.jpg",
  "images/photo2.jpg",
  "images/photo3.jpg",
  "images/photo4.jpg",
  "images/photo5.jpg",
  "images/photo6.jpg",
  "images/photo7.jpg",
  "images/photo8.jpg",
  "images/photo9.jpg",
];

function openModal(index) {
  const modal = document.getElementById("galleryModal");
  const modalImg = document.getElementById("modalImage");

  if (modal && modalImg) {
    currentImageIndex = index;
    modal.classList.add("active");
    modalImg.src = galleryImages[index];
    document.body.style.overflow = "hidden";
  }
}

function closeModal() {
  const modal = document.getElementById("galleryModal");
  if (modal) {
    modal.classList.remove("active");
    document.body.style.overflow = "";
  }
}

function modalNextImage() {
  currentImageIndex = (currentImageIndex + 1) % galleryImages.length;
  document.getElementById("modalImage").src = galleryImages[currentImageIndex];
}

function modalPrevImage() {
  currentImageIndex =
    (currentImageIndex - 1 + galleryImages.length) % galleryImages.length;
  document.getElementById("modalImage").src = galleryImages[currentImageIndex];
}

// Fermer le modal en cliquant à l'extérieur de l'image
document.addEventListener("click", function (e) {
  const modal = document.getElementById("galleryModal");
  if (modal && e.target === modal) {
    closeModal();
  }
});

// Navigation clavier dans la galerie
document.addEventListener("keydown", function (e) {
  const modal = document.getElementById("galleryModal");
  if (modal && modal.classList.contains("active")) {
    if (e.key === "ArrowRight") modalNextImage();
    if (e.key === "ArrowLeft") modalPrevImage();
  }
});

// FORMULAIRE AVIS
document.addEventListener("DOMContentLoaded", function () {
  const reviewForm = document.getElementById("reviewForm");

  if (reviewForm) {
    reviewForm.addEventListener("submit", function (e) {
      e.preventDefault();

      // Validation basique
      const name = document.getElementById("name").value;
      const email = document.getElementById("email").value;
      const sessionType = document.getElementById("session-type").value;
      const rating = document.querySelector('input[name="rating"]:checked');
      const review = document.getElementById("review").value;
      const consent = document.querySelector('input[name="consent"]').checked;

      if (!name || !email || !sessionType || !rating || !review || !consent) {
        alert("Veuillez remplir tous les champs obligatoires");
        return;
      }

      if (review.length < 50) {
        alert("Votre témoignage doit contenir au moins 50 caractères");
        return;
      }

      // Afficher le message de succès
      reviewForm.style.display = "none";
      document.getElementById("successMessage").style.display = "block";

      // Scroll vers le message de succès
      document
        .getElementById("successMessage")
        .scrollIntoView({ behavior: "smooth" });
    });
  }

  // Animation au scroll
  const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -100px 0px",
  };

  const observer = new IntersectionObserver(function (entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = "1";
        entry.target.style.transform = "translateY(0)";
      }
    });
  }, observerOptions);

  // Observer tous les éléments animables
  const animatedElements = document.querySelectorAll(
    ".content-block, .pricing-card, .contact-card, .step, .service-card, .quality-card, .gallery-item, .faq-item",
  );
  animatedElements.forEach((element) => {
    element.style.opacity = "0";
    element.style.transform = "translateY(30px)";
    element.style.transition = "opacity 0.6s ease, transform 0.6s ease";
    observer.observe(element);
  });

  // Ajouter la classe active au lien de navigation correspondant
  const currentPage = window.location.pathname.split("/").pop() || "index.html";
  const navLinks = document.querySelectorAll(".nav-menu a");

  navLinks.forEach((link) => {
    const linkPage = link.getAttribute("href");
    if (
      linkPage === currentPage ||
      (currentPage === "" && linkPage === "index.html")
    ) {
      link.classList.add("active");
    }
  });
});

// Smooth scroll pour les liens internes (si utilisés)
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
  anchor.addEventListener("click", function (e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute("href"));
    if (target) {
      target.scrollIntoView({
        behavior: "smooth",
        block: "start",
      });
    }
  });
});
