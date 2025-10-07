// Function to get query params from URL
function getQueryParams() {
  const params = {};
  const queryString = window.location.search.substring(1);
  const pairs = queryString.split("&");
  for (let pair of pairs) {
    const [key, value] = pair.split("=");
    params[key] = decodeURIComponent(value || "");
  }
  return params;
}

document.addEventListener('DOMContentLoaded', () => {
  const params = getQueryParams();
  if (params.email) document.getElementById('email').value = params.email;

  const form = document.getElementById('loginForm');
  const password = document.getElementById('password');
  const formMessage = document.getElementById('formMessage');

  form.addEventListener('submit', (e) => {
    formMessage.textContent = '';
    let errors = [];

    const passwordValue = password.value.trim();
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,20}$/;

    if (!passwordRegex.test(passwordValue)) {
      errors.push("Password must be 6–20 characters and include letters & numbers.");
    }

    if (errors.length > 0) {
      e.preventDefault();
      formMessage.className = "text-danger";
      formMessage.innerHTML = errors.join("<br>");
    }
  });
});
