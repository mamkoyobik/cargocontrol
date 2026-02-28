(function ($) {
    "use strict";

    var $window = $(window);
    var $document = $(document);
    var $htmlBody = $("html, body");
    var $header = $("#header");
    var $menuToggle = $("#toggle_mobile_menu");
    var $menuLinks = $("#mainmenu a, .cc-footer-nav a");

    function getHeaderOffset() {
        return $header.length ? ($header.outerHeight() || 0) : 0;
    }

    function closeMobileMenu() {
        $header.removeClass("mobile-active");
        $menuToggle.attr("aria-expanded", "false");
    }

    function openMobileMenu() {
        $header.addClass("mobile-active");
        $menuToggle.attr("aria-expanded", "true");
    }

    function toggleMobileMenu() {
        if ($header.hasClass("mobile-active")) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    }

    function smoothScrollTo(hash) {
        if (!hash || hash.charAt(0) !== "#") {
            return;
        }

        var $target = $(hash);
        if (!$target.length) {
            return;
        }

        var offset = getHeaderOffset();
        var top = Math.max($target.offset().top - offset + 1, 0);

        $htmlBody.stop(true).animate({scrollTop: top}, 620);

        if (window.history && window.history.replaceState) {
            window.history.replaceState(null, "", hash);
        }
    }

    function setupAnchors() {
        $menuLinks.on("click", function (event) {
            var href = $(this).attr("href");
            if (!href || href.charAt(0) !== "#") {
                return;
            }
            event.preventDefault();
            smoothScrollTo(href);
            closeMobileMenu();
        });
    }

    function setupHeaderMenu() {
        if (!$menuToggle.length) {
            return;
        }

        $menuToggle.on("click", function (event) {
            event.preventDefault();
            event.stopPropagation();
            toggleMobileMenu();
        });

        $document.on("click", function (event) {
            if (!$(event.target).closest("#header").length) {
                closeMobileMenu();
            }
        });

        $document.on("keydown", function (event) {
            if (event.key === "Escape") {
                closeMobileMenu();
            }
        });
    }

    function setupScrollSpy() {
        var $navLinks = $("#mainmenu a[href^='#']");

        if (!$navLinks.length) {
            return;
        }

        function markActiveSection() {
            var marker = ($window.scrollTop() || 0) + getHeaderOffset() + 46;
            var currentHash = null;

            $navLinks.each(function () {
                var hash = $(this).attr("href");
                var $section = $(hash);
                if ($section.length && $section.offset().top <= marker) {
                    currentHash = hash;
                }
            });

            $navLinks.parent("li").removeClass("active");
            if (currentHash) {
                $navLinks.filter("[href='" + currentHash + "']").parent("li").addClass("active");
            }
        }

        $window.on("scroll resize", markActiveSection);
        markActiveSection();
    }

    function setupGalleryTabs() {
        var $tabs = $(".cc-gallery-tab");

        if (!$tabs.length) {
            return;
        }

        function activateTab($tab) {
            var targetId = $tab.data("gallery-target");
            var $panel = $("#" + targetId);

            if (!$panel.length) {
                return;
            }

            $tabs
                .removeClass("is-active")
                .attr("aria-selected", "false")
                .attr("tabindex", "-1");

            $tab
                .addClass("is-active")
                .attr("aria-selected", "true")
                .attr("tabindex", "0");

            $(".cc-gallery-panel")
                .attr("hidden", true)
                .attr("aria-hidden", "true")
                .removeClass("is-active");

            $panel
                .removeAttr("hidden")
                .attr("aria-hidden", "false")
                .addClass("is-active");
        }

        var $initial = $tabs.filter(".is-active").first();
        if (!$initial.length) {
            $initial = $tabs.first();
        }
        activateTab($initial);

        $tabs.on("click", function () {
            activateTab($(this));
        });

        $tabs.on("keydown", function (event) {
            var key = event.key;
            if (key !== "ArrowRight" && key !== "ArrowLeft" && key !== "Home" && key !== "End") {
                return;
            }

            event.preventDefault();
            var index = $tabs.index(this);
            var next = index;

            if (key === "ArrowRight") {
                next = index + 1;
            } else if (key === "ArrowLeft") {
                next = index - 1;
            } else if (key === "Home") {
                next = 0;
            } else if (key === "End") {
                next = $tabs.length - 1;
            }

            if (next < 0) {
                next = $tabs.length - 1;
            }
            if (next >= $tabs.length) {
                next = 0;
            }

            var $nextTab = $tabs.eq(next);
            $nextTab.focus();
            activateTab($nextTab);
        });
    }

    function setupGalleryLightbox() {
        var $lightbox = $("#ccLightbox");
        var $img = $lightbox.find(".cc-lightbox-image");
        var $close = $lightbox.find(".cc-lightbox-close");
        var $prev = $lightbox.find(".cc-lightbox-prev");
        var $next = $lightbox.find(".cc-lightbox-next");
        var emptyImageSrc = "data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==";

        if (!$lightbox.length) {
            return;
        }

        var state = {
            items: [],
            index: 0,
            opener: null
        };

        function collectGroup(group) {
            return $(".cc-gallery-item[data-lightbox-group='" + group + "']").toArray();
        }

        function render() {
            if (!state.items.length) {
                return;
            }

            if (state.index < 0) {
                state.index = state.items.length - 1;
            }
            if (state.index >= state.items.length) {
                state.index = 0;
            }

            var item = state.items[state.index];
            var $item = $(item);
            var src = $item.attr("href") || "";
            var alt = $item.find("img").attr("alt") || "";

            $img.attr("src", src).attr("alt", alt);
        }

        function open(item) {
            var $item = $(item);
            var group = $item.data("lightbox-group");
            state.items = collectGroup(group);
            state.index = Number($item.data("lightbox-index")) || 0;
            state.opener = item;

            render();
            $lightbox.addClass("is-open").attr("aria-hidden", "false");
            $("body").addClass("cc-no-scroll");
        }

        function close() {
            $lightbox.removeClass("is-open").attr("aria-hidden", "true");
            $("body").removeClass("cc-no-scroll");
            $img.attr("src", emptyImageSrc).attr("alt", "");

            if (state.opener) {
                $(state.opener).focus();
            }
        }

        function showNext() {
            state.index += 1;
            render();
        }

        function showPrev() {
            state.index -= 1;
            render();
        }

        $document.on("click", ".cc-gallery-item", function (event) {
            event.preventDefault();
            open(this);
        });

        $close.on("click", function () {
            close();
        });

        $next.on("click", function () {
            showNext();
        });

        $prev.on("click", function () {
            showPrev();
        });

        $lightbox.on("click", function (event) {
            if ($(event.target).is("#ccLightbox")) {
                close();
            }
        });

        $document.on("keydown", function (event) {
            if (!$lightbox.hasClass("is-open")) {
                return;
            }

            if (event.key === "Escape") {
                close();
            }
            if (event.key === "ArrowRight") {
                showNext();
            }
            if (event.key === "ArrowLeft") {
                showPrev();
            }
        });
    }

    function setupContactForm() {
        var $form = $("#contactForm");

        if (!$form.length) {
            return;
        }

        if ($.fn.validator) {
            $form.validator();
        }

        $form.on("submit", function (event) {
            if (event.isDefaultPrevented()) {
                return;
            }

            event.preventDefault();

            var $button = $("#button");
            var $answer = $("#answer");
            var $loader = $("#loader");

            $.ajax({
                url: "handler.php",
                type: "POST",
                data: $form.serialize(),
                beforeSend: function () {
                    $answer.empty();
                    $button.prop("disabled", true).css("margin-bottom", "20px");
                    $loader.fadeIn(160);
                },
                success: function (result) {
                    $loader.fadeOut(180, function () {
                        $answer.html(result);
                    });

                    $form.find("input[type='text'], input[type='email'], textarea").val("");

                    if (typeof grecaptcha !== "undefined") {
                        grecaptcha.reset();
                    }

                    $button.prop("disabled", false);
                },
                error: function () {
                    $loader.fadeOut(180, function () {
                        $answer.html("<div class='answer'><h4>Произошла ошибка! Попробуйте позже.</h4></div>");
                    });
                    $button.prop("disabled", false);
                }
            });
        });
    }

    function setupRevealAnimations() {
        var selectors = [
            ".cc-story-item",
            ".cc-services-card",
            ".cc-gallery-panel",
            ".cc-contact-card",
            ".cc-about-card",
            ".cc-footer-col"
        ];
        var nodes = document.querySelectorAll(selectors.join(","));

        if (!nodes.length) {
            return;
        }

        for (var i = 0; i < nodes.length; i += 1) {
            nodes[i].classList.add("cc-reveal");
            nodes[i].style.setProperty("--cc-reveal-delay", Math.min(i, 6) * 35 + "ms");
        }

        if (!("IntersectionObserver" in window)) {
            for (var k = 0; k < nodes.length; k += 1) {
                nodes[k].classList.add("is-visible");
            }
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            for (var j = 0; j < entries.length; j += 1) {
                if (entries[j].isIntersecting) {
                    entries[j].target.classList.add("is-visible");
                    observer.unobserve(entries[j].target);
                }
            }
        }, {
            root: null,
            threshold: 0.12,
            rootMargin: "0px 0px -6% 0px"
        });

        for (var p = 0; p < nodes.length; p += 1) {
            observer.observe(nodes[p]);
        }
    }

    window.verifyRecaptchaCallback = function () {
        return true;
    };

    window.expiredRecaptchaCallback = function () {
        return true;
    };

    $(function () {
        setupHeaderMenu();
        setupAnchors();
        setupScrollSpy();
        setupGalleryTabs();
        setupGalleryLightbox();
        setupContactForm();
        setupRevealAnimations();
    });
})(jQuery);
