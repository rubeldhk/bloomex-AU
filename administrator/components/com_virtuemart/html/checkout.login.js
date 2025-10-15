document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("login");
    const usernameInput = document.getElementById("username");
    const passwdInput = document.getElementById("passwd");
    const loginButton = document.querySelector(".btn-login");

    if (!loginForm || !loginButton) {
        return;
    }
    usernameInput.addEventListener('keydown',function (){
        loginButton.disabled = false;
    })
    passwdInput.addEventListener('keydown',function (){
        loginButton.disabled = false;
    })
    loginForm.addEventListener("submit", function (event) {
        loginButton.disabled = true;
        loginButton.textContent = "Login";
    });
});
