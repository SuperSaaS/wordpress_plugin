jQuery(function ($) {
  // Toggle widget script setting depending on display choice
  $("input[name=ss_display_choice]").on("click", function (e) {
    switch (e.target.value) {
      case("regular_btn"):
        $("#ss_widget_script").addClass("hidden"); break;
      case("popup_btn"):
        $("#ss_widget_script").removeClass("hidden"); break;
    }
  })

  // Toggle SuperSaaS api key setting depending on autologin choice
  $("input[name=ss_autologin_enabled]").on("click", function (e) {
    if (e.target.checked) {
      $("#ss_password").removeClass("hidden")
    } else {
      $("#ss_password").addClass("hidden")
    }
  })
});