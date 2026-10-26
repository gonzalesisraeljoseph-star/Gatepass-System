// auth.js - login handler with debugging and safe fallbacks

console.log("auth.js loaded | jQuery:", typeof $, "| Swal:", typeof Swal);

// Show a message with SweetAlert2 if available, otherwise fall back to alert()
function notify(options) {
  if (typeof Swal !== "undefined") {
    return Swal.fire(options);
  }
  alert((options.title || "") + "\n" + (options.text || ""));
}

$(function () {
  const LOGIN_URL = $("#formAuthentication").data("login-url");
  const DASHBOARD_URL = $("#formAuthentication").data("dashboard-url");

  const form = $("#formAuthentication");
  const loginBtn = $(".login-btn");

  if (form.length === 0) {
    console.error("auth.js: #formAuthentication not found on this page");
    return;
  }

  const defaultBtnHtml = `
    <span class="btn-content">
      <i class="fa-solid fa-right-to-bracket me-2"></i>
      Login to System
    </span>
  `;

  function setLoading() {
    loginBtn.prop("disabled", true).addClass("loading");
    loginBtn.html(`<span class="btn-content">Logging In...</span>`);
  }

  function resetButton() {
    loginBtn.prop("disabled", false).removeClass("loading");
    loginBtn.html(defaultBtnHtml);
  }

  form.on("submit", function (e) {
    e.preventDefault();
    console.log("Login form submitted");

    const username = $("#username").val();
    const password = $("#password").val();
    const csrf_token_1 = $("#csrf_token_1").val();

    setLoading();

    $.ajax({
      type: "POST",
      url: LOGIN_URL,
      dataType: "json",
      data: {
        username: username,
        password: password,
        csrf_token: csrf_token_1,
      },

      success: function (obj) {
        console.log("Login response:", obj);

        if (obj && obj.status === true) {
          localStorage.setItem("token", obj.token);

          notify({
            icon: "success",
            title: "Login Success!",
            text: "Welcome!",
            timer: 1500,
            showConfirmButton: false,
          });

          setTimeout(function () {
            window.location.href = DASHBOARD_URL;
          }, 1500);
        } else {
          notify({
            icon: "error",
            title: "Login Failed",
            text: (obj && obj.message) || "Invalid credentials",
          });
          resetButton();
        }
      },

      error: function (xhr, textStatus, errorThrown) {
        console.error("Login request failed:", xhr.status, textStatus, errorThrown);
        console.error("Response body:", xhr.responseText);

        // 400 / 401 come back as JSON with a message from the controller
        let message = "Please try again later";
        if (xhr.responseJSON && xhr.responseJSON.message) {
          message = xhr.responseJSON.message;
        } else if (xhr.status) {
          message = "Error " + xhr.status + ": " + (xhr.statusText || textStatus);
        }

        notify({
          icon: "error",
          title: xhr.status === 401 || xhr.status === 400 ? "Login Failed" : "Server Error",
          text: message,
        });
        resetButton();
      },
    });
  });
});