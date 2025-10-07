/**
 * validatePasswordField
 * @param {HTMLFormElement} form - the form element
 * @param {string} passwordSelector - selector for password input
 * @param {string} errorDivId - id of div to show error message
 */
function validatePasswordField(form, passwordSelector, errorDivId){
    const passwordInput = form.querySelector(passwordSelector);
    const errorDiv = document.getElementById(errorDivId);

    if(!passwordInput || !errorDiv) return;

    // Real-time validation while typing
    passwordInput.addEventListener('input', function(){
        errorDiv.innerText = (this.value.length < 6) 
            ? "Password must be at least 6 characters long." 
            : "";
    });

    // Form submit validation
    form.addEventListener('submit', function(e){
        if(passwordInput.value.length < 6){
            e.preventDefault();
            errorDiv.innerText = "Password must be at least 6 characters long.";
        }
    });
}

// Apply the function for register and login forms
const registerForm = document.getElementById('registerForm');
if(registerForm){
    validatePasswordField(registerForm, 'input[name="password"]', 'registerPasswordError');
}

const loginForm = document.getElementById('loginForm');
if(loginForm){
    validatePasswordField(loginForm, 'input[name="password"]', 'loginPasswordError');
}
