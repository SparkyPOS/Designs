"use strict";
(function ($) {
  const config = {
    init() {
      this.preLoader();
      this.scrollToTop();
      this.hideNavbar();
      this.backgroundImage();
      this.activeMenuClass($("ul.sidebar-menu-list"));
      this.togglePassword();
      this.sidebarMenu();
      this.showSidebarActive();
      this.dbProfileDropdown();
      this.activeSelect2();
      this.activeRangeSlider();
      this.testimonialSlider();
      this.activeOdometer();
      this.animatedTab();
      this.categorySlider();
      this.earningCalculate();
      this.sidebarMenuHide();
      this.activeSpectrum();
      this.addSizeVariation();
      this.addPrintArea();
      this.initPrintAreaUpload();
      this.removePrintArea();
      this.initProductImageUpload();
      this.addColorVariation();
      this.toggleNotification();
      this.activePieChart();
      this.activeTooltip();
      this.activeCustomSelection();
      this.productThumbsSlider();
      this.productSoldTogether();
      this.activeXzoom();
      this.partnerSlider();
      this.reswponsiveTableLable();
      this.showVideo();
    },

    scrollToTop() {
      const btn = $(".scroll-top");
      $(window).on("scroll", function () {
        if ($(window).scrollTop() >= 300) {
          $(".header").addClass("fixed-header");
          btn.addClass('show');
        } else {
          $(".header").removeClass("fixed-header");
          btn.removeClass('show');
        }
      });

      btn.on("click", function (e) {
        e.preventDefault();
        $("html, body").animate({ scrollTop: 0 }, "300");
      });
    },

    hideNavbar() {
      function hideNavbarCollapse() {
        new bootstrap.Collapse($(".navbar-collapse")[0]).hide();
        $(".navbar-collapse").trigger("hide.bs.collapse");
      }

      $(".navbar-collapse").on({
        "show.bs.collapse": function () {
          $("body").addClass("scroll-hide");
          $(".body-overlay").addClass("show").on("click", hideNavbarCollapse);
        },
        "hide.bs.collapse": function () {
          $("body").removeClass("scroll-hide");
          $(".body-overlay")
            .removeClass("show")
            .unbind("click", hideNavbarCollapse);
        },
      });
    },

    backgroundImage() {
      $(".bg-img").css("background-image", function () {
        return `url(${$(this).data("background-image")})`;
      });
    },

    activeMenuClass(selector) {
      if (!$(selector).length) return;

      let fileName = window.location.pathname.split("/").reverse()[0];
      selector.find("li").each(function () {
        let anchor = $(this).find("a");
        if ($(anchor).attr("href") == fileName) {
          $(this).addClass("active");
        }
      });
      // if any li has active element add class
      selector.children("li").each(function () {
        if ($(this).find(".active").length) {
          $(this).addClass("active");
        }
      });
      // if no file name return
      if ("" == fileName) {
        selector.find("li").eq(0).addClass("active");
      }
    },

    togglePassword() {
      $(".toggle-password").on("click", function () {
        $(this).toggleClass("fa-eye");
        var input = $($(this).attr("id"));
        if (input.attr("type") == "password") {
          input.attr("type", "text");
        } else {
          input.attr("type", "password");
        }
      });
    },

    sidebarMenu() {
      if ($(document).innerWidth() > 992) {
        $(document).on("click", function (e) {
          if (
            !$(e.target).closest(".mega-menu__content").length &&
            !$(e.target).closest('[data-bs-toggle="collapse"]').length
          ) {
            $(".mega-menu__content").collapse("hide");
          }
        });
        return;
      }

      $(".mega-menu__list-item-menu").slideUp(0);
      $(".mega-menu__list-item-title").on("click", function () {
        const parent = $(this).closest(".category-dropdown");
        const submenu = parent.find(".mega-menu__list-item-menu");
        $(".mega-menu__list-item-menu").slideUp(200);

        if ($(this).hasClass("active")) {
          $(this).removeClass("active");
          $(this).find(".mega-menu__list-item-title").removeClass("active");
        } else {
          submenu.slideDown(200);
          $(".mega-menu__list-item-title").removeClass("active");
          $(this).addClass("active");
        }
      });
    },

    showSidebarActive() {
      $(".navigation-bar").on("click", function () {
        $(".sidebar-menu").addClass("show-sidebar");
        $(".sidebar-overlay").addClass("show");
      });
      $(".sidebar-menu__close, .sidebar-overlay").on("click", function () {
        $(".sidebar-menu").removeClass("show-sidebar");
        $(".sidebar-overlay").removeClass("show");
      });
    },

    dbProfileDropdown() {
      $(".user-info__button").on("click", function () {
        $(".user-info-dropdown").toggleClass("show");
      });
      $(".user-info__button").attr("tabindex", -1).focus();

      $(".user-info__button").on("focusout", function () {
        $(".user-info-dropdown").removeClass("show");
      });
    },

    activeSelect2() {
      $(".select2").each((index, select) => {
        $(select)
          .wrap('<div class="select2-wrapper"></div>')
          .select2({
            dropdownParent: $(select).closest(".select2-wrapper"),
          });
      });

      $(".select2-tag").each((index, select) => {
        $(select)
          .wrap('<div class="select2-wrapper"></div>')
          .select2({
            dropdownParent: $(select).closest(".select2-wrapper"),
          });
      });
    },

    activeRangeSlider() {
      if ($('.rangeslider').length) {
        $('.rangeslider').each(function () {
          $(this).rangeslider({
            polyfill: false,

            onSlide: function (position, value) {
              $(this.$element)
                .siblings(".price-value")
                .find(".text")
                .text(value);
              $(this.$element).siblings('[type="hidden"]').val(value);
              $("#earning-amount").text(value * 20 * 30);
            },
          });
        });
      }

      // Update earning amount when range slider changes
      $('.rangeslider').on("change", calculateEarning);

      function calculateEarning() {
        const sellPrice = $('input[name="sell-price"]').val();
        const dailySales = $('input[name="daily-sales"]').val();
        const earningAmount = sellPrice * dailySales * 30;
        $("#earning-amount").text(earningAmount);
      }
    },

    testimonialSlider() {
      if (!$(".testimonails-card__list").length) return;
      const sliderConfig = {
        slidesToScroll: 1,
        autoplay: false,
        speed: 500,
        dots: true,
        infinite: false,
        pauseOnHover: true,
        arrows: true,
        fade: true,
        prevArrow:
          '<button type="button" class="testimonial-prev"><i class="fa-solid fa-arrow-left "></i></button>',
        nextArrow:
          '<button type="button" class="testimonial-next"><i class="fa-solid fa-arrow-right"></i></button>',
      };

      const $slider = $(".testimonails-card__list").slick({
        ...sliderConfig,
        slidesToShow: 1,
      });

      const $testimonialThumbs = $(".testimonial-item__thumb-list img");
      if ($testimonialThumbs.length) {
        $(".testimonails-card__list").css("--count", $testimonialThumbs.length);
      }
      $testimonialThumbs.each(function () {
        const img = new Image();
        img.src = $(this).attr("src");
      });

      $testimonialThumbs.hide().eq(0).show();

      $slider.on(
        "beforeChange",
        function (event, slick, currentSlide, nextSlide) {
          // Fade out the current image
          $testimonialThumbs.eq(currentSlide).fadeOut(0);
        }
      );

      $slider.on("afterChange", function (event, slick, currentSlide) {
        // Fade in the new image
        $testimonialThumbs.eq(currentSlide).fadeIn(0);
      });
    },

    activeOdometer() {
      if (!$(".counterup-item").length) return;
      $(".counterup-item").each(function () {
        $(this).isInViewport(function (status) {
          if (status === "entered") {
            for (
              var i = 0;
              i < document.querySelectorAll(".odometer").length;
              i++
            ) {
              var el = document.querySelectorAll(".odometer")[i];
              el.innerHTML = el.getAttribute("data-odometer-final");
            }
          }
        });
      });
    },

    animatedTab() {
      if ($(".animated-thing-3").hasClass("animated-thing-3")) {
        $(".animated-thing-3").animatedTab22();
      }
    },

    categorySlider() {
      if (!$(".category-list").length) return;
      const sliderConfig = {
        slidesToScroll: 1,
        autoplay: false,
        dots: false,
        infinite: false,
        pauseOnHover: true,
        arrows: true,
        prevArrow:
          '<button type="button" class="slick-prev"><i class="fa-solid fa-arrow-left"></i></button>',
        nextArrow:
          '<button type="button" class="slick-next"><i class="fa-solid fa-arrow-right"></i></button>',
      };

      $(".category-list").slick({
        ...sliderConfig,
        slidesToShow: 7,
        responsive: [
          {
            breakpoint: 1399,
            settings: {
              slidesToShow: 6,
              arrows: false,
            },
          },
          {
            breakpoint: 1199,
            settings: {
              slidesToShow: 5,
              arrows: false,
            },
          },
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 4,
              arrows: false,
            },
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 3,
              arrows: false,
            },
          },
          {
            breakpoint: 560,
            settings: {
              slidesToShow: 2,
              arrows: false,
            },
          },
        ],
      });
    },

    earningCalculate() {
      if (!$(".earning-calculate__category").length) return;
      $(".earning-calculate__category").slick({
        variableWidth: true,
        infinite: false,
        prevArrow:
          '<button type="button" class="slick-prev"><i class="fa-solid fa-arrow-left"></i></button>',
        nextArrow:
          '<button type="button" class="slick-next"><i class="fa-solid fa-arrow-right"></i></button>',
      });
    },

    preLoader() {
      $(window).on("load", () => $(".preloader").fadeOut());
    },

    sidebarMenuHide() {
      if ($(window).innerWidth() > 992) {
        const isCollapsed = localStorage.getItem("sidebar-collapsed");

        if (isCollapsed === "collapsed") {
          $(".dashboard").addClass("collapsed");
        }
        $(".sidebar-collapse").on("click", function () {
          $(".dashboard").toggleClass("collapsed");
          localStorage.setItem(
            "sidebar-collapsed",
            isCollapsed === "collapsed" ? "" : "collapsed"
          );
        });
      }
    },

    addColorVariation() {
      let newId = 99;
      $("#add-color-variation").on("click", function () {
        newId++;
        $(this).before(
          `
        <div class="row color-variation-row" data-id="${newId}">
          <div class="col-lg-3">
            <div class="form-group">
              <label for="product-color-${newId}" class="form--label">Color HEX Code</label>
              <div class="input-group input--group">
                <input type="text" class="form--control form-control product-color-input" id="product-color-${newId}" placeholder="hex code" value="#e1e7ef">
                <span class="input-group-text colorInput pe-5" id="colorInput-${newId}" style="background-color: #e1e7ef">
                </span>
              </div>
            </div>
          </div>

          <div class="col-lg-3">
            <div class="form-group">
              <label for="color-name-${newId}" class="form--label">Color Name</label>
              <input type="text" class="form--control" id="color-name-${newId}" placeholder="Enter Color Name">
            </div>
          </div>

          <div class="col-lg-3">
            <div class="form-group">
              <label class="form--label">Product Image</label>
              <div class="product-image">
                <div class="product-image__preview">
                  <img src="../../assets/images/thumbs/product-img.png" alt="upload-thumb">
                </div>
                <div class="product-image__upload">
                  <input type="file" class="opacity-0 product-image__input" accept="image/*" id="product-image-${newId}" placeholder="Enter your retail price">

                  <label for="product-image-${newId}">
                    <img src="../../assets/images/thumbs/upload-thumb.png" alt="upload-thumb">
                    Upload Image
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>`
        );

        // Initialize Spectrum for the newly added color input
        const $colorInput = $(`#product-color-${newId}`);
        const $spectrumBtn = $(`#colorInput-${newId}`);

        $spectrumBtn.spectrum({
          color: $colorInput.val(),
          change: function (color) {
            const hex = color.toHexString();
            $colorInput.val(hex);
            $(this).css("background-color", hex);
          },
          move: function (color) {
            const hex = color.toHexString();
            $(this).css("background-color", hex);
          },
        });

        // Update Spectrum when input changes
        $colorInput.on("input change", function () {
          const hex = $(this).val();
          if (/^#([0-9A-F]{3}){1,2}$/i.test(hex)) {
            // Basic hex color validation
            $spectrumBtn.spectrum("set", hex).css("background-color", hex);
          }
        });
      });
    },

    activeSpectrum() {
      $(".color-variation-row").each(function () {
        const id = $(this).data("id");
        const $colorInput = $(`#product-color-${id}`);
        const $spectrumBtn = $(`#colorInput-${id}`);

        if ($spectrumBtn.data("spectrum")) return; // Skip if already initialized

        $spectrumBtn.spectrum({
          color: $colorInput.val(),
          change: function (color) {
            const hex = color.toHexString();
            $colorInput.val(hex.replace(/^#/, ""));

            $(this).css("background-color", hex);
          },
          move: function (color) {
            const hex = color.toHexString();
            $(this).css("background-color", hex);
          },
        });

        $colorInput.on("input change", function () {
          const hex = $(this).val();
          if (/^#([0-9A-F]{3}){1,2}$/i.test(hex)) {
            $spectrumBtn.spectrum("set", hex).css("background-color", hex);
          }
        });
      });
    },

    addSizeVariation() {
      $("#add-size-variation").on("click", function () {
        const parent = $(this).parent("li");
        parent.before(
          `
          <li>
            <input class="size-variation__item text-center" type="text" id="size-variation-4xl"/>
          </li>
          `
        );
      });
    },

    addPrintArea() {
      let newId = 99;
      $("#add-print-area").on("click", function () {
        newId++;
        const parent = $(this).parent("li");
        parent.before(
          `
          <li class="position-relative">
            <button class="close-print-area position-absolute top-0 end-0">
              <i class="fa-solid fa-xmark"></i>
            </button>
            <label for="print-area-area-${newId}" class="print-area__item">
              <input type="file" id="print-area-area-${newId}" class="print-area__item-input" accept="image/*">
              <div class="print-area__item-image">
                <img src="../../assets/images/thumbs/new-upload-print.png" alt="empty-thumb">
              </div>
              <input type="text" class="print-area__item-name text-start" placeholder="Print Area">
            </label>
          </li>
          `
        );
      });
    },

    initPrintAreaUpload() {
      $(document).on("change", ".print-area__item-input", function () {
        const input = this;
        if (input.files && input.files[0]) {
          const reader = new FileReader();
          const image = $(this).siblings(".print-area__item-image").find("img");

          reader.onload = function (e) {
            image.attr("src", e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
        }
      });
    },

    removePrintArea() {
      $(document).on("click", ".close-print-area", function () {
        const parent = $(this).parent("li");
        parent.remove();
      });
    },

    initProductImageUpload() {
      $(document).on("change", ".product-image__input", function () {
        const input = this;

        if (input.files && input.files[0]) {
          const reader = new FileReader();
          const image = $(this)
            .parent(".product-image__upload")
            .siblings(".product-image__preview")
            .find("img");

          image.parent().addClass("view-thumb");
          reader.onload = function (e) {
            image.attr("src", e.target.result);
          };

          reader.readAsDataURL(input.files[0]);
        }
      });
    },

    toggleNotification() {
      $("#db-notification").fadeOut();
      $(".notification-btn").on("click", function () {
        $(this).toggleClass("show");
        hidePanel();
      });

      $(document).on("click", function (e) {
        const $notification = $("#db-notification");
        const $btn = $(".notification-btn");

        // Check if the click is inside #db-notification
        if (
          $btn.is(e.target) ||
          $btn.has(e.target).length > 0 ||
          $notification.is(e.target) ||
          $notification.has(e.target).length > 0
        ) {
          return; // Do nothing if clicked inside the notification
        }

        // If the button has class 'show', remove it
        if ($btn.hasClass("show")) {
          $btn.removeClass("show");
          hidePanel();
        }
      });

      function hidePanel() {
        if ($(".notification-btn").hasClass("show")) {
          $("#db-notification").fadeIn();
        } else {
          $("#db-notification").fadeOut();
        }
      }
    },

    activePieChart() {
      if (!$("#pie-chart").length) return;

      // Calculate total for percentage
      const series = [44, 55, 13, 43, 22];
      const total = series.reduce((a, b) => a + b, 0);

      // Calculate percentages dynamically
      const percentages = series.map((value) =>
        ((value / total) * 100).toFixed(2)
      );

      // Generate random previous month percentages between -15% and +15%
      const prevMonthPercents = series.map(() => {
        const percent = (Math.random() * 30 - 15).toFixed(2);
        return {
          value: percent,
          class: percent >= 0 ? "text--success" : "text--danger",
        };
      });

      var options = {
        series: series,
        chart: {
          width: "100%", // Allow CSS to control it
          height: "320px", // Allow CSS to control it
          type: "pie",
        },
        dataLabels: {
          enabled: false,
        },
        stroke: {
          width: 0,
          show: false,
        },
        colors: [
          "hsl(var(--base-two-l-200))",
          "hsl(var(--base-two-d-200))",
          "hsl(var(--base-two-l-600))",
          "hsl(var(--base-two-d-100))",
          "hsl(var(--base-two-l-300))",
          "hsl(var(--base-two))",
          "hsl(var(--base-two-d-200))",
          "hsl(var(--base-two-l-500))",
          "hsl(var(--base-two-d-300))",
          "hsl(var(--base-two-l-400))",
          "hsl(var(--base-two-d-400))",
          "hsl(var(--base-two-l-700))",
          "hsl(var(--base-two-d-500))",
        ],
        labels: [
          `<div class='chart-label'><span class='title'>T-shirt</span>  <span class='percentage'>${percentages[0]}%</span> <span class='pre-month ${prevMonthPercents[0].class}'>(${prevMonthPercents[0].value}%)</span> </div>`,
          `<div class='chart-label'><span class='title'>Hoodie</span>  <span class='percentage'>${percentages[1]}%</span> <span class='pre-month ${prevMonthPercents[1].class}'>(${prevMonthPercents[1].value}%)</span> </div>`,
          `<div class='chart-label'><span class='title'>Tote Bag</span>  <span class='percentage'>${percentages[2]}%</span> <span class='pre-month ${prevMonthPercents[2].class}'>(${prevMonthPercents[2].value}%)</span> </div>`,
          `<div class='chart-label'><span class='title'>Mug</span>  <span class='percentage'>${percentages[3]}%</span> <span class='pre-month ${prevMonthPercents[3].class}'>(${prevMonthPercents[3].value}%)</span> </div>`,
          `<div class='chart-label'><span class='title'>Sticker</span>  <span class='percentage'>${percentages[4]}%</span> <span class='pre-month ${prevMonthPercents[4].class}'>(${prevMonthPercents[4].value}%)</span> </div>`,
        ],
        responsive: [
          {
            breakpoint: 768,
            options: {
              chart: {
                height: 400,
              },
              legend: {
                position: "bottom",
              },
            },
          },
        ],
      };
      var chart = new ApexCharts(document.querySelector("#pie-chart"), options);
      chart.render();
    },

    activeTooltip() {
      // const tooltipTriggerList = document.querySelectorAll(
      //   '[data-bs-toggle="tooltip"]'
      // );
      // [...tooltipTriggerList].map(
      //   (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl)
      // );

      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
      });

    },

    activeCustomSelection() {
      const select = $("#selection-active");
      select.wrap('<div class="custom-selection-wrapper"></div>').select2({
        dropdownParent: select.closest(".custom-selection-wrapper"),
        minimumResultsForSearch: Infinity,
      });
    },

    productThumbsSlider() {
      const id = $(document).find("#details-thumb-list");
      if (!id?.length) return;

      const slide = id.slick({
        slidesToShow: 6,
        autoplay: false,
        speed: 500,
        dots: false,
        infinite: false,
        pauseOnHover: true,
        arrows: true,
        fade: false,
        prevArrow:
          '<button type="button" class="thumb-prev"><i class="fa-solid fa-arrow-left "></i></button>',
        nextArrow:
          '<button type="button" class="thumb-next"><i class="fa-solid fa-arrow-right"></i></button>',
        responsive: [
          {
            breakpoint: 1200,
            settings: {
              slidesToShow: 4,
            },
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 4,
            },
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 3,
            },
          },
        ],
      });

      function setActive($this) {
        $this.find(".slick-slide").removeClass("first-active last-active");

        const activeList = $this.find(".slick-slide.slick-active");
        activeList.first().addClass("first-active");
        activeList.last().addClass("last-active");
      }
      setActive(id);

      // Trigger Slick to fire `init` manually if needed
      slide.slick("setPosition"); // Ensures positioning is correct on init
    },
    productSoldTogether() {
      const id = $(document).find(".sold-together__list");
      if (!id?.length) return;

      id.slick({
        slidesToShow: 4,
        autoplay: false,
        speed: 500,
        dots: false,
        infinite: false,
        pauseOnHover: true,
        arrows: true,
        fade: false,
        prevArrow:
          '<button type="button" class="thumb-prev"><i class="fa-solid fa-arrow-left "></i></button>',
        nextArrow:
          '<button type="button" class="thumb-next"><i class="fa-solid fa-arrow-right"></i></button>',
        responsive: [
          {
            breakpoint: 1400,
            settings: {
              slidesToShow: 3,
            },
          },
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 2,
            },
          },
          {
            breakpoint: 575,
            settings: {
              slidesToShow: 1,
            },
          },
        ],
      });
    },
    activeXzoom() {
      if ($(".xzoom5, .xzoom-gallery5").length) {
        initializeXZOOM();
      }
    },
    partnerSlider() {
      if (!$(".partners__list").length) return;
      $(".partners__list").slick({
        slidesToShow: 8, // Default, will be overridden in responsive
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 0, // Set to 0 for continuous
        speed: 4000, // Adjust speed as needed
        cssEase: "linear", // Makes it scroll smoothly like a marquee
        pauseOnHover: true,
        arrows: false,
        dots: false,
        draggable: false, // Disables drag/swipe
        swipe: false,
        touchMove: false,
        infinite: true,
        responsive: [
          {
            breakpoint: 1200,
            settings: {
              slidesToShow: 6,
            },
          },
          {
            breakpoint: 992,
            settings: {
              slidesToShow: 5,
            },
          },
          {
            breakpoint: 768,
            settings: {
              slidesToShow: 4,
            },
          },
          {
            breakpoint: 480,
            settings: {
              slidesToShow: 3,
            },
          },
        ],
      });
    },
    reswponsiveTableLable() {
      // ========================== Table Data Label Js Start =====================
      Array.from(document.querySelectorAll("table")).forEach((table) => {
        let heading = table.querySelectorAll("thead tr th");
        Array.from(table.querySelectorAll("tbody tr")).forEach((row) => {
          let columArray = Array.from(row.querySelectorAll("td"));
          if (columArray.length <= 1) return;
          columArray.forEach((colum, i) => {
            if (heading[i] != undefined) {
              colum.setAttribute("data-label", heading[i].innerText);
            }
          });
        });
      });
      // ========================== Table Data Label Js End =====================
    },
    showVideo() {
      var magPhoto = $(".show-video");
      if (magPhoto.length) {
        magPhoto.magnificPopup({
          disableOn: 700,
          type: "iframe",
          mainClass: "mfp-fade",
          removalDelay: 160,
          preloader: false,
          fixedContentPos: false,
          disableOn: 300,
        });
      }
    }
  };
  config.init();
})(jQuery);

$(document).ready(function () {
  const $slider = $("#print-preview");
  const $thumbs = $(".print-preview-item");

  // Initialize slider on modal open
  $("#printModal").on("shown.bs.modal", function () {
    if (!$slider.hasClass("slick-initialized")) {
      $slider.slick({
        slidesToShow: 1,
        arrows: true,
        dots: false,
        infinite: false,
        adaptiveHeight: true,
        prevArrow:
          '<button type="button" class="thumb-prev"><i class="fa-solid fa-arrow-left "></i></button>',
        nextArrow:
          '<button type="button" class="thumb-next"><i class="fa-solid fa-arrow-right"></i></button>',
      });
    } else {
      $slider.slick("setPosition");
    }

    // Set default active thumb
    $thumbs.removeClass("active").eq(0).addClass("active");
  });

  // Click thumbnail -> go to slide
  $thumbs.on("click", function () {
    const index = $(this).index();
    $slider.slick("slickGoTo", index);
  });

  // When slide changes, highlight corresponding thumbnail
  $slider.on("afterChange", function (event, slick, currentSlide) {
    $thumbs.removeClass("active").eq(currentSlide).addClass("active");
  });
});

// image upload
function proPicURL(input) {
  if (input.files && input.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      var preview = $(input).closest('.image-upload-wrapper').find('.image-upload-preview');
      $(preview).css('background-image', 'url(' + e.target.result + ')');
      $(preview).addClass('has-image');
      $(preview).hide();
      $(preview).fadeIn(650);
    }
    reader.readAsDataURL(input.files[0]);
  }
}
$(".image-upload-input").on('change', function () {
  proPicURL(this);
});
$(".remove-image").on('click', function () {
  $(this).parents(".image-upload-preview").css('background-image', 'none');
  $(this).parents(".image-upload-preview").removeClass('has-image');
  $(this).parents(".image-upload-wrapper").find('input[type=file]').val('');
});
$("form").on("change", ".file-upload-field", function () {
  $(this).parent(".file-upload-wrapper").attr("data-text", $(this).val().replace(/.*(\/|\\)/, ''));
});

$('.table-responsive').on('click', '[data-bs-toggle="dropdown"]', function (e) {
  const { top, left } = $(this).next(".dropdown-menu")[0].getBoundingClientRect();
  $(this).next(".dropdown-menu").css({
    position: "fixed",
    inset: "unset",
    transform: "unset",
    top: top + "px",
    left: left + "px",
  });
});

if ($('.table-responsive').length) {
  $(window).on('scroll', function (e) {
    $('.table-responsive .dropdown-menu').removeClass('show');
    $('.table-responsive [data-bs-toggle="dropdown"]').removeClass('show');
  });
}