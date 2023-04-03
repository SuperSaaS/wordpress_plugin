function isValidURL(str) {
  var pattern = new RegExp('^(https?:\\/\\/)?' + // protocol
    '((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|' + // domain name
    '((\\d{1,3}\\.){3}\\d{1,3}))' + // OR ip (v4) address
    '(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*' + // port and path
    '(\\?[;&a-z\\d%_.~+=-]*)?' + // query string
    '(\\#[-a-z\\d_]*)?$', 'i'); // fragment locator
  return !!pattern.test(str);
}

jQuery(function ($) {
  // Toggle widget script setting depending on display choice
  $("input[name=ss_display_choice]").on("click", function (e) {
    switch (e.target.value) {
      case("regular_btn"):
        $("#ss_widget_script").addClass("hidden");
        $("#ss_button_settings").removeClass("hidden");
        $("#ss_domain").removeClass("hidden");
        break;
      case("popup_btn"):
        $("#ss_widget_script").removeClass("hidden");
        $("#ss_button_settings").addClass("hidden");
        $("#ss_domain").addClass("hidden");
        break;
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

  // Validations:
  $("#supersaas-options-form").on("submit", function (e) {
    if(this.valid) { return; }

    e.preventDefault();
    var errors = 0;
    // Refresh all error messages;
    $("span.error-msg").addClass("hidden");
    $("input, textarea").css( "border-color", "" );

    var accountName = $("input[name=ss_account_name]")
    if(accountName[0].value.trim().length === 0) {
      accountName.css( "border-color", "red" );
      accountName.nextAll(".error-msg-1.hidden").removeClass("hidden");
      errors++;
    }

    if(accountName[0].value.includes("@")) {
      accountName.css( "border-color", "red" );
      accountName.nextAll(".error-msg-2.hidden").removeClass("hidden");
      errors++;
    }

    if($("input[name=ss_autologin_enabled]")[0].checked) {
      var apiKey = $("input[name=ss_password]")
      if(apiKey[0].value.trim().length === 0) {
        apiKey.css( "border-color", "red" );
        apiKey.nextAll(".error-msg.hidden").removeClass("hidden");
        errors++;
      }
    }

    if ($("input[name=ss_display_choice][value=popup_btn]")[0].checked) {
      var widgetScript = $("textarea[name=ss_widget_script]");
      if( !/https:\/\/cdn.supersaas.net\/widget.js/i.test(widgetScript[0].value) ) {
        widgetScript.css( "border-color", "red" );
        widgetScript.nextAll(".error-msg.hidden").removeClass("hidden");
        errors++;
      }
    }

    if ($("input[name=ss_display_choice][value=regular_btn]")[0].checked) {
      var buttonImage = $("input[name=ss_button_image]");
      if(!isValidURL(buttonImage[0].value)) {
        buttonImage.css( "border-color", "red" );
        buttonImage.nextAll(".error-msg.hidden").removeClass("hidden");
        errors++;
      }
    }

    if (errors === 0) {
      this.valid = true;
      $(this).submit()
    }
  })

});