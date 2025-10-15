<?php

http_response_code(503);
header('Retry-After: 3600');

global $mosConfig_live_site;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Maintenance - Bloomex</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .maintenance-container {
            background: white;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 90%;
            text-align: center;
            position: relative;
        }
        .logo {
            max-width: 200px;
            margin-bottom: 0px;
        }
        .maintenance-card {
            background: white;
            padding: 10px;
            border-radius: 15px;
            margin: 10px 0;
            border: 2px solid #e0e0e0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        /*.maintenance-card:hover {*/
        /*    transform: translateY(-5px);*/
        /*    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);*/
        /*}*/
        .maintenance-symbol {
            max-width: 100px;
            margin: 0 auto 10px;
            display: block;
        }
        .apology-message {
            color: #3498db;
            font-size: 1.3em;
            margin-bottom: 15px;
            font-weight: 600;
        }
        h1 {
            color: #2c3e50;
            margin-bottom: 20px;
            font-size: 2.5em;
        }
        .status-message {
            color: #7f8c8d;
            margin-bottom: 30px;
            font-size: 1.3em;
            line-height: 1.6;
        }
        .contact-info {
            margin: 25px 0;
        }
        .contact-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }
        .contact-card h2 {
            color: #3498db;
            font-size: 1.4em;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .contact-content p {
            color: #7f8c8d;
            margin-bottom: 15px;
            font-size: 1.1em;
            line-height: 1.5;
        }
        .contact-options {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .contact-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: white;
            border-radius: 6px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        .contact-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .contact-icon {
            font-size: 1.4em;
            color: #3498db;
        }
        .contact-text {
            color: #2c3e50;
            font-weight: 500;
        }
        .phone-number {
            font-size: 1.6em;
            color: #3498db;
            margin: 15px 0;
            font-weight: 600;
        }
        .support-number {
            font-size: 1.2em;
            color: #2ecc71;
            margin: 15px 0;
            font-weight: 500;
        }
        .contact-form {
            margin-top: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            position: relative;
            overflow: hidden;
        }
        .form-header {
            cursor: pointer;
            padding: 15px;
            background: white;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        .form-header:hover {
            background: #f8f9fa;
            transform: translateY(-2px);
        }
        .form-header h2 {
            color: #3498db;
            font-size: 1.2em;
            margin: 0;
            padding: 0;
        }
        .form-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.5s ease;
        }
        .form-content.expanded {
            max-height: 500px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #2c3e50;
            font-weight: 500;
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1em;
            transition: border-color 0.3s ease;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        .submit-btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.1em;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .submit-btn:hover {
            background-color: #2980b9;
        }
        .chat-icon {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .chat-icon:hover {
            transform: scale(1.1);
        }
        .contact-link {
            color: inherit;
            text-decoration: none;
        }
        @media (max-width: 600px) {
            .maintenance-container {
                padding: 30px;
            }
            h1 {
                font-size: 2em;
            }
            .maintenance-symbol {
                max-width: 200px;
            }
            .chat-icon {
                width: 50px;
                height: 50px;
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const formHeader = document.querySelector('.form-header');
            const formContent = document.querySelector('.form-content');

            formHeader.addEventListener('click', function() {
                if (formContent.classList.contains('expanded')) {
                    formContent.style.maxHeight = '0';
                    setTimeout(() => {
                        formContent.classList.remove('expanded');
                    }, 500);
                } else {
                    formContent.classList.add('expanded');
                    formContent.style.maxHeight = '500px';
                }
            });
        });

        (function(d) {
            var cm = d.createElement('scr' + 'ipt'); cm.type = 'text/javascript'; cm.async = true;
            cm.src = 'https://kcsafexvff.chat.digital.ringcentral.com/chat/f6b2a033e9ef4884a58cf253/loader.js';
            var s = d.getElementsByTagName('scr' + 'ipt')[0]; s.parentNode.insertBefore(cm, s);
        }(document));

        function openchat() {
            if (document.querySelector('.dimelo-chat-bubble')) {
                document.querySelector('.dimelo-chat-bubble').click();
            }
        }
    </script>
</head>
<body>
<div class="maintenance-container">
    <img src="/templates/bloomex_adaptive/images/bloomexlogo.svg" alt="Bloomex Logo" class="logo">
    <div class="maintenance-card">
        <img src="<?= $mosConfig_live_site ?>/components/com_under_maintenance/15569750.png" alt="Maintenance Symbol" class="maintenance-symbol">
        <p class="apology-message">We apologize for the inconvenience</p>
        <h1>System Under Maintenance</h1>
        <div class="status-message">
            Our system is currently undergoing maintenance to improve your experience.
        </div>
    </div>

    <div class="contact-info">
        <div class="contact-card">
            <h2>Need Assistance?</h2>
            <div class="contact-content">
                <p>If you need assistance placing a new order or support with an existing one, please contact us at:</p>
                <div class="contact-options">
                    <div class="contact-option">
                        <a href="tel:+1800905147" class="contact-link">
                            <span class="contact-icon">📞</span>
                            <span class="contact-text">1 (800) 905-147</span>
                        </a>
                    </div>
                    <div class="contact-option" onclick="openchat();">
                        <span class="contact-icon">💬</span>
                        <span class="contact-text">24/7 Live Chat</span>
                    </div>
                    <div class="contact-option">
                        <span class="contact-icon"><img style="width: 26px;" src="<?= $mosConfig_live_site ?>/components/com_under_maintenance/email.png"></span>
                        <a style="text-decoration: none" class="link-blue" href="mailto:care@bloomex.com.au"><span class="contact-text">care@bloomex.com.au</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>