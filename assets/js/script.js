// Function to sanitize input to protect against XSS attacks
function sanitizeInput(input) {
    const element = document.createElement('div');
    if (input) {
      element.innerText = input;  // Automatically escapes special characters
      return element.innerHTML;   // Return sanitized input
    }
    return '';  // Return empty string if input is undefined or null
  }
  
  // Function to validate email addresses (basic check)
  function validateEmail(email) {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
    return emailRegex.test(email);
  }
  
  // Function to validate non-empty inputs
  function validateNonEmpty(input) {
    return input.trim() !== '';
  }
  
  // Function to validate if a value is numeric
  function validateNumeric(input) {
    return !isNaN(input) && input > 0;
  }
  
  // Password strength check (at least 8 characters, one uppercase, one number, and one special character)
  function validatePasswordStrength(password) {
      if (typeof password !== 'string') return false;
      return password.length >= 8
          && /[A-Z]/.test(password)
          && /[0-9]/.test(password)
          && /[^A-Za-z0-9]/.test(password);
  }
 
  
  // Function to prevent SQL Injection attacks by escaping dangerous characters
  function preventSQLInjection(input) {
    return input.replace(/(['";])/g, '\\$1');  // Escape single quotes, double quotes, and semicolons
  }
  
  // Function to set maximum length restrictions for inputs
  function setInputLengthRestrictions() {
      { const el_ = document.getElementById("playerId"); if (el_) el_.setAttribute("maxlength", 16); }  // Limit Player ID to 30 characters
      { const el_ = document.getElementById("paymentDetails"); if (el_) el_.setAttribute("maxlength", 16); }  // Limit Remarks to 30 characters
      { const el_ = document.getElementById("userId"); if (el_) el_.setAttribute("maxlength", 16); }  // Limit Mobile Legends User ID to 30 characters
      { const el_ = document.getElementById("zoneId"); if (el_) el_.setAttribute("maxlength", 16); }  // Limit Zone ID to 30 characters
      { const el_ = document.getElementById("supercellEmail"); if (el_) el_.setAttribute("maxlength", 30); }  // Limit Supercell Email to 30 characters
      { const el_ = document.getElementById("konamiEmail"); if (el_) el_.setAttribute("maxlength", 30); }  // Limit Konami Email to 30 characters
      { const el_ = document.getElementById("password"); if (el_) el_.setAttribute("maxlength", 16); }  // Limit Password to 30 characters
  }
  
  // Function to enforce input length validation during input
  function enforceMaxLength(event) {
      const maxLength = event.target.getAttribute("maxlength");
      if (event.target.value.length > maxLength) {
          event.target.value = event.target.value.substring(0, maxLength);
      }
  }
  
  // CSRF token generation function (simplified for demo purposes)
  function generateCSRFToken() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
  }
  
  // Function to validate form before submission (includes sanitization and validation)
  function validateForm() {
      const playerId = document.getElementById("playerId").value;
      const paymentDetails = document.getElementById("paymentDetails").value;
      const userId = document.getElementById("userId").value;
      const zoneId = document.getElementById("zoneId").value;
      const supercellEmail = document.getElementById("supercellEmail").value;
      const konamiEmail = document.getElementById("konamiEmail").value;
      const password = document.getElementById("password").value;
  
      // Sanitize inputs
      const sanitizedPlayerId = sanitizeInput(playerId);
      const sanitizedPaymentDetails = sanitizeInput(paymentDetails);
      const sanitizedUserId = sanitizeInput(userId);
      const sanitizedZoneId = sanitizeInput(zoneId);
      const sanitizedSupercellEmail = sanitizeInput(supercellEmail);
      const sanitizedKonamiEmail = sanitizeInput(konamiEmail);
      const sanitizedPassword = sanitizeInput(password);
  
      // Prevent SQL Injection
      const safePlayerId = preventSQLInjection(sanitizedPlayerId);
      const safePaymentDetails = preventSQLInjection(sanitizedPaymentDetails);
      const safeUserId = preventSQLInjection(sanitizedUserId);
      const safeZoneId = preventSQLInjection(sanitizedZoneId);
      const safeSupercellEmail = preventSQLInjection(sanitizedSupercellEmail);
      const safeKonamiEmail = preventSQLInjection(sanitizedKonamiEmail);
      const safePassword = preventSQLInjection(sanitizedPassword);
  
      // Validation for empty fields
      if (!validateNonEmpty(safePlayerId) || !validateNonEmpty(safeUserId) || !validateNonEmpty(safePassword)) {
          alert("Please fill all required fields.");
          return false;
      }
  
      // Validate password strength
      if (!validatePasswordStrength(safePassword)) {
          alert("Password must be at least 8 characters long, contain at least one uppercase letter, one number, and one special character.");
          return false;
      }
  
      // Example: Validate email format
      if (!validateEmail(safeSupercellEmail)) {
          alert("Invalid Supercell email format.");
          return false;
      }
  
      // Validate CSRF token (assuming it's included in the form submission)
      const csrfToken = document.getElementById("csrfToken").value;
      if (csrfToken !== generateCSRFToken()) {
          alert("Invalid CSRF token.");
          return false;
      }
  
      // Additional logic for other fields can be added here...
  
      // If everything is valid
      return true;
  }
  
  // Attach the event listener to input fields for length enforcement
  function addInputListeners() {
      { const el_ = document.getElementById("playerId"); if (el_) el_.addEventListener('input', enforceMaxLength); }
      { const el_ = document.getElementById("paymentDetails"); if (el_) el_.addEventListener('input', enforceMaxLength); }
      { const el_ = document.getElementById("userId"); if (el_) el_.addEventListener('input', enforceMaxLength); }
      { const el_ = document.getElementById("zoneId"); if (el_) el_.addEventListener('input', enforceMaxLength); }
      { const el_ = document.getElementById("supercellEmail"); if (el_) el_.addEventListener('input', enforceMaxLength); }
      { const el_ = document.getElementById("konamiEmail"); if (el_) el_.addEventListener('input', enforceMaxLength); }
      { const el_ = document.getElementById("password"); if (el_) el_.addEventListener('input', enforceMaxLength); }
  }
  
  // Real-time feedback function (for immediate validation feedback)
  function realTimeValidation(event) {
      const targetId = event.target.id;
      if (targetId === "password" && !validatePasswordStrength(event.target.value)) {
          { const el_ = document.getElementById("passwordError"); if (el_) el_.innerText = "Password is too weak!"; }
      } else if (targetId === "supercellEmail" && !validateEmail(event.target.value)) {
          { const el_ = document.getElementById("emailError"); if (el_) el_.innerText = "Invalid email format!"; }
      } else {
          { const el_ = document.getElementById("passwordError"); if (el_) el_.innerText = ""; }
          { const el_ = document.getElementById("emailError"); if (el_) el_.innerText = ""; }
      }
  }
  
  // Initialize the input length restrictions and listeners
  function initializeFormProtection() {
      setInputLengthRestrictions();
      addInputListeners();
      { const el_ = document.getElementById("password"); if (el_) el_.addEventListener("input", realTimeValidation); }
      { const el_ = document.getElementById("supercellEmail"); if (el_) el_.addEventListener("input", realTimeValidation); }
  }
  
  // Call the initialization function on page load
  window.onload = initializeFormProtection;
 

 