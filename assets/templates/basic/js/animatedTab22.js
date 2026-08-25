(function ($) {
  $.fn.animatedTab22 = function (options) {
    var $element = this;

    const settings = $.extend(
      {
        timeout: 3,
        transition: 0.5,
      },
      options
    );

    let isPaused = false;
    let intervalId;

    $element.find(".left-menu-item-bar").css("--time", `${settings.timeout}s`);

    let base = $element.find(".left-menu-item.active");
    let rightBase = $element.find(".right-tab-content .tab-item.active");
    let leftParent = base.parent();
    let rightParent = rightBase.parent();

    function animate() {
      intervalId = setInterval(() => {
        if (isPaused) {
          return;
        }

        base.removeClass("active");

        rightBase.fadeOut(settings.transition * 1000);
        // rightBase.removeClass('active');

        if (rightBase.is(":last-child")) {
          rightBase = rightParent.children().first();
        } else {
          rightBase = rightBase.next();
        }

        if (base.is(":last-child")) {
          base = leftParent.children().first();
        } else {
          base = base.next();
        }

        base.addClass("active");

        rightBase.fadeIn(settings.transition * 1000);

        // rightBase.addClass('active').css('visibility', 'visible').fadeIn(500);
      }, settings.timeout * 1000);
    }

    animate();

    $element.find(".left-menu-item").on("click", function () {
      clearInterval(intervalId);

      $element.find(".tab-item").fadeOut(settings.transition * 1000);
      $element.find(".left-menu-item").removeClass("active");

      $(this).addClass("active");

      const index = $(this).index();

      base = leftParent.find(".left-menu-item").eq(index);
      base.addClass("active");

      rightBase = rightParent.find(".tab-item").eq(index);
      rightBase.fadeIn(settings.transition * 1000);

      animate();
    });

    $element.find(".tab-item").on("mouseenter", function () {
      base.find(".barline").css("animation-play-state", "paused");
      isPaused = true;
    });

    $element.find(".tab-item").on("mouseleave", function () {
      base.find(".barline").css("animation-play-state", "running");
      isPaused = false;
    });

    return $element;
  };
})(jQuery);
