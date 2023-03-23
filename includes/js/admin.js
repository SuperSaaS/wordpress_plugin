jQuery(function ($) {
  console.log($("input[name=ss_display_choice]"));
  $("input[name=ss_display_choice]").on("click", function (e) {
      if(e.target.value === "regular_btn") {
          console.log("clicked on regular btn")
          $("textarea[name=ss_widget_script]").addClass("hidden")
      }

      if(e.target.value === "popup_btn") {
          console.log("clicked on popup btn")
          $("textarea[name=ss_widget_script]").removeClass("hidden")
      }
  })
});