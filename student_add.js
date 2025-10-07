const form = document.getElementById("form");
const submitBtn = document.getElementById("submitBtn");
let selectedRow = null;

// Validation rules
const validators = {
  firstname: v => v.trim() !== "" || "First name required",
  lastname: v => v.trim() !== "" || "Last name required",
  age: v => (!isNaN(v) && v > 0 && v <= 120) || "Enter valid age",
  phonenumber: v => (/^(?:\+91)?[6-9]\d{9}$/.test(v)) || "Phone must be valid 10 digits",
  email: v => (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) || "Invalid email format",
  degree: v => v !== "" || "Select degree"
};

// Show error under input
function showError(input, message) {
  let errorSpan = input.parentNode.querySelector(".error-msg");
  if (!errorSpan) {
    errorSpan = document.createElement("small");
    errorSpan.classList.add("error-msg", "text-danger");
    input.parentNode.appendChild(errorSpan);
  }
  errorSpan.innerText = message || "";
}

// Validate a single input
function validateInput(input) {
  const rule = validators[input.name];
  if (rule) {
    const result = rule(input.value);
    if (result !== true) {
      showError(input, result);
      return false;
    } else {
      showError(input, "");
      return true;
    }
  }
  return true;
}

// Gender validation
function validateGender() {
  const genderInputs = form.querySelectorAll("input[name='gender']");
  const selected = Array.from(genderInputs).some(r => r.checked);
  document.getElementById("gender-error").innerText = selected ? "" : "Select gender";
  return selected;
}

// Languages validation
function validateLanguages() {
  const langs = form.querySelectorAll("input[name='langs[]']");
  const selected = Array.from(langs).some(cb => cb.checked);
  document.getElementById("langs-error").innerText = selected ? "" : "Select at least one language";
  return selected;
}

// File validation
function validateFile() {
  const fileInput = document.getElementById("fileupload");
  if (submitBtn.value === "Submit" && !fileInput.files[0]) {
    showError(fileInput, "Upload a photo");
    return false;
  }
  showError(fileInput, "");
  return true;
}

// Attach dynamic validation to inputs
Object.keys(validators).forEach(name => {
  const input = form.querySelector(`[name="${name}"]`);
  if (input) {
    input.addEventListener("input", () => validateInput(input));
    input.addEventListener("blur", () => validateInput(input));
  }
});
form.querySelectorAll("input[name='gender']").forEach(r => r.addEventListener("change", validateGender));
form.querySelectorAll("input[name='langs[]']").forEach(cb => cb.addEventListener("change", validateLanguages));
document.getElementById("fileupload").addEventListener("change", validateFile);

// Fetch and render students
async function fetchStudents() {
  try {
    const res = await fetch("student_add.php?action=fetch");
    const text = await res.text();
    let data = [];
    try {
      data = text ? JSON.parse(text) : [];
    } catch (e) {
      console.error("Invalid JSON:", text);
      return;
    }

    const tbody = document.querySelector("#data-table tbody");
    tbody.innerHTML = "";

    data.forEach(student => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${student.id}</td>
        <td>${student.firstname}</td>
        <td>${student.lastname}</td>
        <td>${student.age}</td>
        <td>${student.phonenumber}</td>
        <td>${student.email}</td>
        <td>${student.gender}</td>
        <td>${student.languages}</td>
        <td>${student.degree}</td>
        <td>${student.photo ? '<img src="data:image/jpeg;base64,'+student.photo+'" width="50">' : 'No Image'}</td>
        <td>
  <button class="edit-btn btn btn-sm btn-warning" style="margin-right:5px;">Edit</button>
  <button class="delete-btn btn btn-sm btn-danger" style="margin-right:5px;">Delete</button>
  <a href="student_login_create.html?email=${encodeURIComponent(student.email)}" 
   class="btn btn-sm btn-info" 
   style="margin-top: 2px;">
   Create Login
</a>

</td>
`;

      tbody.appendChild(tr);

      // Edit
      tr.querySelector(".edit-btn").addEventListener("click", () => {
        selectedRow = tr;
        form.id.value = student.id;
        form.firstname.value = student.firstname;
        form.lastname.value = student.lastname;
        form.age.value = student.age;
        form.phonenumber.value = student.phonenumber;
        form.email.value = student.email;
        Array.from(form.querySelectorAll("input[name='gender']")).forEach(g => g.checked = g.value === student.gender);
        Array.from(form.querySelectorAll("input[name='langs[]']")).forEach(cb => cb.checked = student.languages.split(",").includes(cb.value));
        form.degree.value = student.degree;
        submitBtn.value = "Update";
      });

      // Delete
      tr.querySelector(".delete-btn").addEventListener("click", async () => {
        console.log("Delete clicked for ID:", student.id);
        if (confirm("Delete student?")) {
          try {
            const delRes = await fetch("student_add.php?action=delete", {
              method: "POST",
              headers: { "Content-Type": "application/x-www-form-urlencoded" },
              body: `id=${encodeURIComponent(student.id)}`
            });
            const txt = await delRes.text();
            console.log("Delete response:", txt);
            if (txt.trim() === "success") {
              fetchStudents();
              form.reset();
              submitBtn.value = "Submit";
            } else {
              alert("Delete failed: " + txt);
            }
          } catch (err) {
            console.error("Delete error:", err);
          }
        }
      });
    });
  } catch (err) {
    console.error("fetchStudents error:", err);
  }
}

// Final submit
form.addEventListener("submit", async e => {
  e.preventDefault();
  let isValid = true;

  Object.keys(validators).forEach(name => {
    const input = form.querySelector(`[name="${name}"]`);
    if (input && !validateInput(input)) isValid = false;
  });
  if (!validateGender()) isValid = false;
  if (!validateLanguages()) isValid = false;
  if (!validateFile()) isValid = false;

  if (!isValid) return;

  const formData = new FormData(form);
  const res = await fetch("student_add.php", { method: "POST", body: formData });
  const txt = await res.text();

  if (txt === "already exist") {
    alert("Phone or email already exists");
    return;
  }

  form.reset();
  submitBtn.value = "Submit";
  fetchStudents();
});

// Init
window.addEventListener("DOMContentLoaded", fetchStudents);
