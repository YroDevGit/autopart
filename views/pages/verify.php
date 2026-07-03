<?php

use Classes\Ctrx;
use Tables\Verification;

$code = get("code");
if(! $code){
    Ctrx::forbidden_page();
}

$result = Verification::findOne(["code"=>$code, "active"=>1]);
if(! $result){
    Ctrx::forbidden_page();
}

$email = $result['email'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up · Create account</title>
    <!-- Font Awesome for icons (optional but adds a nice touch) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Roboto, system-ui, -apple-system, sans-serif;
            background: linear-gradient(145deg, #f6f9fc 0%, #e9f1f8 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        .signup-card {
            max-width: 680px;
            width: 100%;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            background: #ffffff;
            border-radius: 2.5rem;
            padding: 2.5rem 2.8rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        @media (max-width: 480px) {
            .signup-card {
                padding: 1.8rem 1.2rem;
                border-radius: 2rem;
            }
        }

        .signup-header {
            margin-bottom: 2.2rem;
            text-align: left;
        }

        .signup-header h2 {
            font-size: 2.2rem;
            font-weight: 600;
            color: #0b1b2f;
            letter-spacing: -0.02em;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .signup-header h2 i {
            color: #2a7de1;
            font-size: 2rem;
        }

        .signup-header p {
            color: #4a5b6e;
            margin-top: 0.4rem;
            font-weight: 400;
            font-size: 1rem;
            border-left: 3px solid #2a7de1;
            padding-left: 0.9rem;
        }

        .form-grid {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1.2rem;
        }

        .form-row .field-group {
            flex: 1 1 calc(50% - 0.6rem);
            min-width: 180px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .field-group.full-width {
            flex: 1 1 100%;
        }

        label {
            font-size: 0.85rem;
            font-weight: 500;
            color: #1e2f41;
            letter-spacing: 0.3px;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        label i {
            color: #2a7de1;
            font-size: 0.9rem;
            width: 1.1rem;
        }

        .field-group input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid #dce3ec;
            border-radius: 1.2rem;
            font-size: 0.95rem;
            background: #fafcff;
            transition: all 0.15s ease;
            color: #0b1b2f;
            font-weight: 450;
        }

        .field-group input:focus {
            outline: none;
            border-color: #2a7de1;
            box-shadow: 0 0 0 4px rgba(42, 125, 225, 0.15);
            background: #ffffff;
        }

        .field-group input::placeholder {
            color: #9aadc2;
            font-weight: 300;
            font-size: 0.9rem;
        }

        /* password hint / extra */
        .hint-text {
            font-size: 0.7rem;
            color: #6f84a0;
            margin-top: 0.1rem;
            padding-left: 0.3rem;
        }
        .error_text{
            font-size: 13px;
        }

        .btn-submit {
            margin-top: 1.8rem;
            width: 100%;
            padding: 0.9rem 1.5rem;
            background: #0b1b2f;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            border: none;
            border-radius: 3rem;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.7rem;
            letter-spacing: 0.3px;
            box-shadow: 0 8px 20px -8px rgba(11, 27, 47, 0.3);
        }

        .btn-submit i {
            font-size: 1rem;
        }

        .btn-submit:hover {
            background: #1a3350;
            transform: scale(1.01);
            box-shadow: 0 12px 28px -10px #0b1b2f55;
        }

        .btn-submit:active {
            transform: scale(0.97);
        }

        .footer-note {
            margin-top: 1.8rem;
            text-align: center;
            font-size: 0.85rem;
            color: #4f677f;
            border-top: 1px solid #e2eaf3;
            padding-top: 1.5rem;
        }

        .footer-note i {
            color: #2a7de1;
        }

        /* small screens */
        @media (max-width: 550px) {
            .form-row .field-group {
                flex: 1 1 100%;
            }
        }

        /* inline validation style (optional) */
        .field-group input:invalid:not(:placeholder-shown) {
            border-color: #e15a5a;
        }
        .field-group input:invalid:not(:placeholder-shown) + .hint-text {
            color: #c0392b;
        }

        /* show required star */
        .required-star {
            color: #c0392b;
            margin-left: 2px;
            font-weight: 600;
        }
    </style>
    <script>
        let gEmail = "<?=$email?>";
        let gCode = "<?=$code?>";
    </script>
</head>
<body>
    <div class="signup-card">
        <div class="signup-header">
            <h2>
                <i class="fas fa-user-plus"></i> Sign up
            </h2>
            <p>Create your account — all fields are required</p>
        </div>

        <form id="signupForm">
            <div class="form-grid">

                <!-- Fullname + Contact (row) -->
                <div class="form-row">
                    <div class="field-group">
                        <label for="fullname">
                            <i class="fas fa-user"></i> Full name <span class="required-star">*</span>
                        </label>
                        <input type="text" id="fullname" name="fullname" placeholder="e.g. Maria Santos">
                        <?=error_text("fullname")?>
                    </div>
                    <div class="field-group">
                        <label for="contact">
                            <i class="fas fa-phone-alt"></i> Contact <span class="required-star">*</span>
                        </label>
                        <input type="tel" id="contact" name="contact" placeholder="+63 912 345 6789">
                        <?=error_text("contact")?>
                    </div>
                </div>

                <!-- Username + Email (row) -->
                <div class="form-row">
                    <div class="field-group">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email <span class="required-star">*</span>
                        </label>
                        <input type="email" id="email" name="email" placeholder="you@example.com" readonly value="<?=$email?>">
                    </div>
                </div>

                <!-- Password (full width) -->
                <div class="field-group full-width">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password <span class="required-star">*</span>
                    </label>
                    <input type="password" id="password" name="password" placeholder="Min 8 characters" minlength="8">
                    <?=error_text("password")?>
                    <span class="hint-text"><i class="fas fa-info-circle"></i> At least 8 characters</span>
                </div>

                <!-- submit button -->
                <button type="submit" class="btn-submit">
                    <i class="fas fa-pen-to-square"></i> Create account
                </button>
            </div>
        </form>

        <div class="footer-note">
            <i class="fas fa-shield-alt"></i> Your data is safe with us
        </div>
    </div>

    <script>
       
    </script>

</body>
</html>

<?=js()?>