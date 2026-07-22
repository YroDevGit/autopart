import Ctr from "../code/src/mods/ctr.js";
import FormValidator from "../code/src/mods/formValidator.js";
import Popmodal from "../code/src/mods/modals/popmodal.js";
import TModal from "../code/src/mods/modals/tmodal.js";
import CImagePicker from "../code/src/mods/picker/imagepicker.js";
import Toast from "../code/src/mods/toast.js";
import { Twal } from "../code/src/mods/twal.js";
import Validator from "../code/src/mods/validator.js";
import { Tyrax } from "../code/src/tyrux/main.js";


(function () {
    const modex = TModal.init({
        title: "Register here",
        id: "modex", 
        form_id: "regForm",
        form: {
            email: {type: "text", label: "Enter email here:", validation:{required: true, email:true, maxChar: 50, label: "Email"}}
        }
    });

    CImagePicker.init({
        id: "#imagep"
    });
    
    Ctr.click("#signupclick", ()=>{
        modex.show();
    });


    modex.form_submit(function(data,form){
        Tyrax.post({
            url: "customer/reg",
            data: data,
            loading: {id: modex.form_id, size: 50},
            res: (send, code, message, data, errors)=>{
                if(code == 200){
                    Twal.ok("Verification sent to your email", true);
                }else{
                    Twal.err(message);
                }
            }
        })
    });

    const emailInput = document.getElementById('loginEmail');
    const passwordInput = document.getElementById('loginPassword');
    const loginForm = document.getElementById('loginForm');
    const loginBtn = document.getElementById('loginBtn');
    const emailErrorDiv = document.getElementById('_email');
    const passwordErrorDiv = document.getElementById('_password');
    const togglePasswordBtn = document.getElementById('togglePasswordBtn');
    const toggleIcon = document.getElementById('toggleIcon');

    // For modals actions (demo simulation)
    const sendResetBtn = document.getElementById('sendResetBtn');
    const simulateSignupBtn = document.getElementById('simulateSignupBtn');

    // set dynamic year
    const yearSpan = document.getElementById('year');
    if (yearSpan) yearSpan.innerText = new Date().getFullYear();

    // Helper: remove invalid feedback and add
    function setInvalid(element, errorDiv, isInvalid, customMessage = '') {
        return;
        if (isInvalid) {
            element.classList.add('is-invalid');
            errorDiv.classList.remove('d-none');
         
        } else {
            element.classList.remove('is-invalid');
            errorDiv.classList.add('d-none');
        }
    }

    // Validate email format
    function isValidEmail(email) {
        const re = /^[^\s@]+@([^\s@.,]+\.)+[^\s@.,]{2,}$/i;
        return re.test(email);
    }

    // Real-time validation: improve UX
    emailInput.addEventListener('input', function () {
        const val = this.value.trim();
        if (val === '') {
            setInvalid(emailInput, emailErrorDiv, true, 'Email address cannot be empty.');
        } else if (!isValidEmail(val)) {
            setInvalid(emailInput, emailErrorDiv, true, 'Enter a valid email (e.g., name@domain.com).');
        } else {
            setInvalid(emailInput, emailErrorDiv, false);
        }
    });

    passwordInput.addEventListener('input', function () {
        const val = this.value.trim();
        if (val === '') {
            setInvalid(passwordInput, passwordErrorDiv, true, 'Password cannot be blank.');
        } else {
            setInvalid(passwordInput, passwordErrorDiv, false);
        }
    });

    // Toggle password visibility (amazing usability)
    togglePasswordBtn.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        if (type === 'text') {
            toggleIcon.classList.remove('bi-eye-slash-fill');
            toggleIcon.classList.add('bi-eye-fill');
        } else {
            toggleIcon.classList.remove('bi-eye-fill');
            toggleIcon.classList.add('bi-eye-slash-fill');
        }
    });

    // Show success alert / simulation (bootstrap toasts not needed; use sweet boot alert)
    function showFloatingMessage(message, isError = false) {
        // create temporary floating alert (pure BS style, but dynamic)
        const toastContainer = document.createElement('div');
        toastContainer.className = 'position-fixed top-0 start-50 translate-middle-x mt-3 p-3';
        toastContainer.style.zIndex = '9999';
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${isError ? 'danger' : 'success'} alert-dismissible fade show shadow-lg border-0 rounded-pill px-4`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
        <i class="bi ${isError ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill'} me-2"></i> 
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      `;
        toastContainer.appendChild(alertDiv);
        document.body.appendChild(toastContainer);
        setTimeout(() => {
            if (toastContainer && toastContainer.parentNode) toastContainer.remove();
        }, 3000);
    }

    Ctr.submit("#loginForm", (data, formData)=>{
        if(formData.user_role == "customer"){
            //Ctr.log(formData);return;
            Tyrax.findOne({
                table: "customer",
                loading: {id: "#loginForm", size: 35}, 
                where: {username: formData.email, password: formData.password},
                res: (send, code, message,data)=>{
                    if(code == 200){
                        if(send.empty){
                            Twal.err("Account is invalid");
                        }else{
                            Ctr.storage_set("userid", data.id);
                            Twal.ok("Login success", "/order");
                        }
                    }else{
                        Twal.err(message);
                    }
                }
            });
            return;
        }

        Tyrax.post({
            url: "user/login",
            request: data,
            loading: {id: "#loginForm", size: 35},
            response: (send)=>{
                if(send.code == 402){
                    Twal.err(send.message);
                    let errs = send.errors;
                    FormValidator.displayErrors(errs, "#loginForm");
                }else if(send.code == 404){
                    Twal.err(send.message);
                }else{
                    if(! send.userid) {Twal.err("User Error, please contact admin"); return;}
                    localStorage.setItem("userid", send.userid);
                    if(send.role == 1 || send.role == 2){
                        Ctr.redirect("cashier/orders");
                    }else if(send.role == 3){
                        Ctr.redirect("rider/welcome");
                    }
                    
                }
            }
        })
    });

    // Forgot password modal send reset simulation
    if (sendResetBtn) {
        sendResetBtn.addEventListener('click', function () {
            const resetEmailField = document.getElementById('resetEmail');
            const resetEmailVal = resetEmailField ? resetEmailField.value.trim() : '';
            if (resetEmailVal === '' || !isValidEmail(resetEmailVal)) {
                showFloatingMessage('Please provide a valid registered email address for password recovery.', true);
            } else {
                showFloatingMessage(`🔧 A password reset link has been sent to ${resetEmailVal} (demo mode).`, false);
                const modalEl = document.getElementById('forgotModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) modal.hide();
                if (resetEmailField) resetEmailField.value = '';
            }
        });
    }

    // Signup modal simulation
    if (simulateSignupBtn) {
        simulateSignupBtn.addEventListener('click', function () {
            showFloatingMessage('🚗 Registration demo: Thank you for joining AutoParts Elite! (Full experience in production)', false);
            const signModal = document.getElementById('signupModal');
            const modal = bootstrap.Modal.getInstance(signModal);
            if (modal) modal.hide();
        });
    }

    const initDemo = () => {
        // already pre-filled demo@autoparts.com and autoparts2025, trigger validations
        emailInput.dispatchEvent(new Event('input'));
        passwordInput.dispatchEvent(new Event('input'));
    };
    initDemo();

    // extra mechanical visual: add dynamic gear rotate? small fun, does not break rules (just adds tiny inline style)
    const gearIcons = document.querySelectorAll('.gear-icon i');
    if (gearIcons.length) {
        setInterval(() => {
            gearIcons.forEach(icon => {
                icon.style.transition = 'transform 0.2s linear';
                icon.style.transform = 'rotate(3deg)';
                setTimeout(() => { if (icon) icon.style.transform = 'rotate(-3deg)'; }, 150);
            });
        }, 2000);
    }
})();