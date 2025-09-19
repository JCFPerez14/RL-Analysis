// Allowed image types
const allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
// Max file size (2MB)
const maxSize = 2 * 1024 * 1024;

function previewImage(event) {
  const file = event.target.files[0];
  const preview = document.getElementById("preview");

  if (!file) {
    preview.src = "uploads/default.png";
    return;
  }

  // ✅ Validate file type
  if (!allowedTypes.includes(file.type)) {
    alert("Only JPG and PNG images are allowed.");
    event.target.value = ""; // reset input
    preview.src = "uploads/default.png";
    return;
  }

  // ✅ Validate file size
  if (file.size > maxSize) {
    alert("File size must not exceed 2MB.");
    event.target.value = ""; // reset input
    preview.src = "uploads/default.png";
    return;
  }

  // ✅ Show preview
  const reader = new FileReader();
  reader.onload = function () {
    preview.src = reader.result;
  };
  reader.readAsDataURL(file);
}
