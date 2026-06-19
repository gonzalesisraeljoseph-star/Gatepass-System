$("#formAuthentication").submit(function (e) {
  e.preventDefault();

  const loginBtn = $(".login-btn");

  // Disable button + loading state
  loginBtn.prop("disabled", true);
  loginBtn.addClass("loading");

  loginBtn.html(`
    <span class="btn-content">
      Logging In...
    </span>
  `);

  $("#toastr-login").trigger("click");
  $("#content").hide();

  var username = $("#username").val();
  var password = $("#password").val();
  var csrf_token_1 = $("#csrf_token_1").val();

  setTimeout(function () {
    $.ajax({
      type: "POST",
      url: "authentication",
      dataType: "json",
      data: {
        username: username,
        password: password,
        csrf_token: csrf_token_1,
      },

      success: function (obj) {
        if (obj.status === true) {
          localStorage.setItem("token", obj.token);

          Swal.fire({
            icon: "success",
            title: "Login Success!",
            text: "Welcome!",
            timer: 1500,
            showConfirmButton: false,
          });

          setTimeout(function () {
            window.location.href = "dashboard";
          }, 1500);
        } else {
          Swal.fire({
            icon: "error",
            title: "Login Failed",
            text: obj.message || "Invalid credentials",
          });

          // Restore button
          loginBtn.prop("disabled", false);
          loginBtn.removeClass("loading");

          loginBtn.html(`
            <span class="btn-content">
              <i class="fa-solid fa-right-to-bracket me-2"></i>
              Login to System
            </span>
          `);
        }
      },

      error: function () {
        Swal.fire({
          icon: "error",
          title: "Server Error",
          text: "Please try again later",
        });

        // Restore button
        loginBtn.prop("disabled", false);
        loginBtn.removeClass("loading");

        loginBtn.html(`
          <span class="btn-content">
            <i class="fa-solid fa-right-to-bracket me-2"></i>
            Login to System
          </span>
        `);
      },
    });
  }, 3000);
});
