const translations = {
  en: {
    // Header
    logoName: "Kusumba Grampanchayat",
    language: "EN",
    login: "Login",

    // Navigation
    home: "Home",
    aboutVillage: "About Village",
    aboutUs: "About Us",
    gallery: "Gallery",
    notices: "Notices",
    schemes: "Schemes",
    meetings: "Meetings",
    tax: "Tax",
    homeTax: "Home Tax",
    waterTax: "Water Tax",
    sanitationTax: "Sanitation Tax",
    certificates: "Certificates",
    birthCertificate: "Birth Certificate",
    deathCertificate: "Death Certificate",
    marriageCertificate: "Marriage Certificate",

    // About Village Section
    aboutVillageTitle: "About Our Village",
    historyTitle: "Rich History",
    historyContent:
      "Discover the fascinating history of Kusumba village, its cultural heritage, and the stories that shape our community.",
    cultureTitle: "Culture & Tradition",
    cultureContent:
      "Experience the vibrant culture and time-honored traditions that make Kusumba unique.",
    developmentTitle: "Panjara River",
    developmentContent:
      "Panjara River is a major river in the Kusumba village, located in the Kusumba village in Dhule District, Maharashtra.",

    // Statistics Section
    population: "Population",
    households: "Households",
    literacy: "Literacy Rate",
    landArea: "Land Area (Hectares)",

    // Map Section
    locationTitle: "Our Location",
    viewMap: "View Kusumbe Village Map",
  },
  mr: {
    // Header
    logoName: "कुसुंबा ग्रामपंचायत",
    language: "मराठी",
    login: "लॉगिन",

    // Navigation
    home: "मुख्यपृष्ठ",
    aboutVillage: "गावाविषयी",
    aboutUs: "आमच्याबद्दल",
    gallery: "गॅलरी",
    notices: "सूचना",
    schemes: "योजना",
    meetings: "सभा",
    tax: "कर",
    homeTax: "घरपट्टी",
    waterTax: "पाणीपट्टी",
    sanitationTax: "स्वच्छता कर",
    certificates: "प्रमाणपत्रे",
    birthCertificate: "जन्म प्रमाणपत्र",
    deathCertificate: "मृत्यू प्रमाणपत्र",
    marriageCertificate: "विवाह प्रमाणपत्र",

    // About Village Section
    aboutVillageTitle: "आमच्या गावाबद्दल",
    historyTitle: "समृद्ध इतिहास",
    historyContent:
      "कुसुंबा गावाचा मनोरंजक इतिहास, त्याचे सांस्कृतिक वारसा आणि आमच्या समुदायाला आकार देणाऱ्या कथा जाणून घ्या.",
    cultureTitle: "संस्कृती आणि परंपरा",
    cultureContent:
      "कुसुंबाला अनन्य बनवणारी जीवंत संस्कृती आणि प्राचीन परंपरा अनुभवा.",
    developmentTitle: "पंजरा नदी",
    developmentContent:
      "पंजरा नदी ही महाराष्ट्रातील धुळे जिल्ह्यातील कुसुंबा गावातील प्रमुख नदी आहे.",

    // Statistics Section
    population: "लोकसंख्या",
    households: "कुटुंबे",
    literacy: "साक्षरता दर",
    landArea: "जमीन क्षेत्र (हेक्टर)",
    // Village Leaders Section
    villageLeaders: "गाव नेते",
    sarpanchTitle: "गाव सरपंच",
    gramSevakTitle: "ग्रामसेवक",

    // Map Section
    locationTitle: "आमचे स्थान",
    viewMap: "कुसुंबे गाव नकाशा पहा",
  },
};

// Set default language
let currentLanguage = "en";

// Load language preference from localStorage if available
document.addEventListener("DOMContentLoaded", function () {
  const savedLanguage = localStorage.getItem("language");
  if (savedLanguage) {
    currentLanguage = savedLanguage;
    updateContent(currentLanguage);
    updateLanguageButton(currentLanguage);
  }
});

// Toggle between languages
function toggleLanguage() {
  // Cycle through languages: en -> mr -> hi -> en
  if (currentLanguage === "en") {
    currentLanguage = "mr";
  } else {
    currentLanguage = "en";
  }

  // Save language preference
  localStorage.setItem("language", currentLanguage);

  // Update content and language button
  updateContent(currentLanguage);
  updateLanguageButton(currentLanguage);
}

function updateContent(lang) {
  const elements = document.querySelectorAll("[data-translate]");

  elements.forEach((element) => {
    const key = element.getAttribute("data-translate");

    if (translations[lang] && translations[lang][key]) {
      // Handle different element types
      if (element.tagName === "INPUT" || element.tagName === "TEXTAREA") {
        element.placeholder = translations[lang][key];
      } else if (element.tagName === "IMG") {
        element.alt = translations[lang][key];
      } else {
        element.textContent = translations[lang][key];
      }
    }
  });

  // Update document title if it has a translation key
  const titleElement = document.querySelector("title");
  if (titleElement && titleElement.getAttribute("data-translate")) {
    const titleKey = titleElement.getAttribute("data-translate");
    if (translations[lang] && translations[lang][titleKey]) {
      document.title = translations[lang][titleKey];
    }
  }

  // Also update navigation links that don't have data-translate attribute
  const navLinks = document.querySelectorAll(
    ".nav-links > li > a:not([data-translate])"
  );
  navLinks.forEach((link) => {
    const href = link.getAttribute("href");
    // Get text content and find corresponding key in translations
    const textContent = link.textContent.trim();

    // Map common navigation items to translation keys
    const navMap = {
      Home: "home",
      "About Village": "aboutVillage",
      "About Us": "aboutUs",
      Gallery: "Gallery",
      Notices: "notices",
      Tax: "tax",
      Certificates: "certificates",
    };

    if (navMap[textContent] && translations[lang][navMap[textContent]]) {
      link.textContent = translations[lang][navMap[textContent]];
    }
  });

  // Update dropdown menu items
  const dropdownLinks = document.querySelectorAll(
    ".dropdown-menu li a:not([data-translate])"
  );
  dropdownLinks.forEach((link) => {
    const textContent = link.textContent.trim();

    // Map dropdown items to translation keys
    const dropdownMap = {
      Schemes: "schemes",
      Meetings: "meetings",
      "Home Tax": "homeTax",
      "Water Tax": "waterTax",
      "Sanitation Tax": "sanitationTax",
      "Birth Certificate": "birthCertificate",
      "Death Certificate": "deathCertificate",
      "Marriage Certificate": "marriageCertificate",
    };

    if (
      dropdownMap[textContent] &&
      translations[lang][dropdownMap[textContent]]
    ) {
      link.textContent = translations[lang][dropdownMap[textContent]];
    }
  });
}

// Update the language button text
function updateLanguageButton(lang) {
  const langBtn = document.querySelector(".lang-btn");
  if (langBtn) {
    langBtn.textContent = translations[lang]["language"];
  }
}

// Add this function to handle login (placeholder for now)
function handleLogin() {
  window.location.href = "../login.html";
}

// Mobile menu functionality
document.addEventListener("DOMContentLoaded", function () {
  const mobileMenuToggle = document.querySelector(".mobile-menu-toggle");
  const navLinks = document.querySelector(".nav-links");
  const mobileMenuOverlay = document.querySelector(".mobile-menu-overlay");

  if (mobileMenuToggle && navLinks && mobileMenuOverlay) {
    mobileMenuToggle.addEventListener("click", function () {
      mobileMenuToggle.classList.toggle("active");
      navLinks.classList.toggle("active");
      mobileMenuOverlay.classList.toggle("active");
      document.body.style.overflow = navLinks.classList.contains("active")
        ? "hidden"
        : "";
    });

    mobileMenuOverlay.addEventListener("click", function () {
      mobileMenuToggle.classList.remove("active");
      navLinks.classList.remove("active");
      mobileMenuOverlay.classList.remove("active");
      document.body.style.overflow = "";
    });
  }
});

function handleLogin() {
  window.location.href = "../Login/login.php";
}
// Profile management
function showProfileDetails() {
  // Fetch user details via AJAX
  fetch("../login/get_user_details.php")
    .then((response) => {
      if (!response.ok) {
        throw new Error("Not logged in");
      }
      return response.json();
    })
    .then((user) => {
      // Create a modal or popup with user details
      const profileModal = document.createElement("div");
      profileModal.innerHTML = `
                    <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 1000;">
                        <div style="background: white; padding: 2rem; border-radius: 10px; max-width: 500px; width: 90%; text-align: center;">
                            <img src="../image/user (2).png" alt="Profile" style="width: 100px; height: 100px; border-radius: 50%; margin-bottom: 1rem;">
                            <h2>Profile Details</h2>
                            <p><strong>Name:</strong> ${user.name}</p>
                            <p><strong>Email:</strong> ${user.email}</p>
                            <p><strong>Mobile:</strong> ${user.mobile}</p>
                            <p><strong>Address:</strong> ${user.address}</p>
                            <button onclick="this.parentElement.parentElement.remove()" style="background: #dc3545; color: white; border: none; padding: 0.5rem 1rem; border-radius: 5px; margin-top: 1rem;">Close</button>
                        </div>
                    </div>
                `;
      document.body.appendChild(profileModal);
    })
    .catch((error) => {
      console.error("Error fetching user details:", error);
      alert("Unable to fetch profile details. Please log in again.");
      // Clear login state and redirect to login
      sessionStorage.removeItem("isLoggedIn");
      window.location.href = "../login/login.php";
    });
}
// Check login status when page loads
document.addEventListener("DOMContentLoaded", function () {
  checkLoginStatus();
});

// Function to check if user is logged in
function checkLoginStatus() {
  // Check if session storage indicates user is logged in
  const isLoggedIn = sessionStorage.getItem("isLoggedIn") === "true";

  // Alternative: Check server-side login status
  fetch("../login/session_check.php")
    .then((response) => response.json())
    .then((data) => {
      updateLoginUI(data.isLoggedIn);
      // Also update session storage
      sessionStorage.setItem("isLoggedIn", data.isLoggedIn);
    })
    .catch((error) => {
      // Fallback to client-side check if server check fails
      console.error("Error checking login status:", error);
      updateLoginUI(isLoggedIn);
    });
}

// Update UI based on login status
function updateLoginUI(isLoggedIn) {
  const loginContainer = document.querySelector(".login-container");
  const profileContainer = document.querySelector(".profile-container");

  if (isLoggedIn) {
    // User is logged in - show profile, hide login
    loginContainer.style.display = "none";
    profileContainer.style.display = "block";
  } else {
    // User is not logged in - show login, hide profile
    loginContainer.style.display = "block";
    profileContainer.style.display = "none";
  }
}
