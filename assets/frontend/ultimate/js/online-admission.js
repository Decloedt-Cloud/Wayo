
var studentForm = document.getElementById("studentform");

var schoolForm = document.getElementById("schoolform");

var studentFormLine = document.getElementById("left-line");

var schoolFormLine = document.getElementById("right-line");

var studentFormSelector = document.getElementById("studentFormSelector");

var schoolFormSelector = document.getElementById("schoolFormSelector");

// Listen for file input change for educational qualifications
// Guard: only add listeners if elements exist
if (studentFormSelector) {
  studentFormSelector.addEventListener("click", function () {
    var studentformEl = document.getElementById("studentform");
    var schoolformEl = document.getElementById("schoolform");
    if (studentformEl) studentformEl.style.display = "block";
    if (schoolformEl) schoolformEl.style.display = "none";
    if (studentFormLine) studentFormLine.classList.remove("underline-left");
    if (schoolFormLine) schoolFormLine.classList.add("underline-right");
    studentFormSelector.classList.add("active-form");
    schoolFormSelector.classList.remove("active-form");
    var sideLineLeft = document.querySelector(".side-line-left");
    var sideLineRight = document.querySelector(".side-line-right");
    if (sideLineLeft) sideLineLeft.setAttribute("data-selected", "true");
    if (sideLineRight) sideLineRight.setAttribute("data-selected", "false");
  });
}

if (schoolFormSelector) {
  schoolFormSelector.addEventListener("click", function () {
    var studentformEl = document.getElementById("studentform");
    var schoolformEl = document.getElementById("schoolform");
    if (schoolformEl) schoolformEl.style.display = "block";
    if (studentformEl) studentformEl.style.display = "none";
    if (schoolFormLine) schoolFormLine.classList.remove("underline-right");
    if (studentFormLine) studentFormLine.classList.add("underline-left");
    schoolFormSelector.classList.add("active-form");
    studentFormSelector.classList.remove("active-form");
    document
      .querySelector(".side-line-left")
      .setAttribute("data-selected", "false");
    document
      .querySelector(".side-line-right")
      .setAttribute("data-selected", "true");
  });
}


var schoolImageInput = document.getElementById("school_image");
if (schoolImageInput) {
  schoolImageInput.addEventListener("change", function () {
    var fileName = this.value.split("\\").pop(); // Gets the file name
    if (fileName.length > 10) {
      fileName = fileName.substring(0, 10) + "... ." + getFileExtension(fileName); // Truncate the file name if it's too long
    }

    var fileNameEl = document.querySelector(".file-name-school-image");
    if (fileNameEl) fileNameEl.textContent = fileName;
  });
}

//Check for Form selector to display the correct form

//-------------------------------------------------------------------------------

var schoolImageInput2 = document.querySelector("#school_image");
var schoolPreview = document.querySelector(".school-image-preview");
var schoolPreviewBtn = document.querySelector(".school-image-preview-btn");
var schoolModal = document.querySelector(".school-image-modal");
var schoolCloseBtn = document.querySelector(".close-school-image");

if (schoolImageInput2 && schoolPreview && schoolPreviewBtn) {
  schoolImageInput2.addEventListener("change", function () {
    var file = this.files[0];
    var fileUrl = URL.createObjectURL(file);
    console.log(fileUrl);
    schoolPreview.src = fileUrl;
    schoolPreviewBtn.classList.remove("disabled");
  });
}

if (schoolPreviewBtn && schoolModal) {
  schoolPreviewBtn.addEventListener("click", function () {
    schoolModal.classList.toggle("display-none");
  });
}

if (schoolCloseBtn && schoolModal) {
  schoolCloseBtn.addEventListener("click", function () {
    schoolModal.classList.toggle("display-none");
  });
}




var rpp = document.getElementById("repeat-password");
var p = document.getElementById("password");

if (p && rpp) {
  p.addEventListener("input", function() {

      if (p.value !== rpp.value) {
          // Show error message
          document.getElementById("errorMessage").style.display = "block";
      }

      if (rpp.value === this.value || rpp.value === "") {
          // Hide error message
          document.getElementById("errorMessage").style.display = "none";
      }

  });


  rpp.addEventListener("input", function() {
      var password = document.getElementById("password").value;

      if (password !== this.value) {
          // Show error message
          document.getElementById("errorMessage").style.display = "block";
      }

      if (password === this.value || this.value === "") {
          // Hide error message
          document.getElementById("errorMessage").style.display = "none";
      }

  });
}



var schoolFormEl = document.getElementById("schoolform");
if (schoolFormEl) {
  schoolFormEl.addEventListener("submit", function(event) {
      var passwordEl = document.getElementById("password");
      var confirmPasswordEl = document.getElementById("repeat-password");
      if (!passwordEl || !confirmPasswordEl) return;
      
      var password = passwordEl.value;
      var confirmPassword = confirmPasswordEl.value;

      if (password !== confirmPassword) {
          // Prevent form submission
          event.preventDefault();
          // Show error message
          var errorMessageEl = document.getElementById("errorMessage");
          if (errorMessageEl) errorMessageEl.style.display = "block";
      }
  });
}








var rpps = document.getElementById("repeat-password-student");
var ps = document.getElementById("password-student");

if (ps && rpps) {
  ps.addEventListener("input", function() {

      if (ps.value !== rpps.value) {
          // Show error message
          document.getElementById("errorMessage").style.display = "block";
      }

      if (rpps.value === this.value || rpps.value === "") {
          // Hide error message
          document.getElementById("errorMessage").style.display = "none";
      }

  });


  rpps.addEventListener("input", function() {
      var passwordstudent = document.getElementById("password-student").value;

      if (passwordstudent !== this.value) {
          // Show error message
          document.getElementById("errorMessage").style.display = "block";
      }

      if (passwordstudent === this.value || this.value === "") {
          // Hide error message
          document.getElementById("errorMessage").style.display = "none";
      }

  });
}
