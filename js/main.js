(function ($) {
    "use strict";

    var $window = $(window);
    var $document = $(document);
    var $htmlBody = $("html, body");
    var $body = $("body");
    var $header = $("#header");
    var $menuToggle = $("#toggle_mobile_menu");
    var $menuLinks = $("#mainmenu a, .cc-footer-nav a, .cc-hero-actions a");
    var $headerProgressBar = $(".cc-header-progress-bar");
    var reducedMotionQuery = window.matchMedia ? window.matchMedia("(prefers-reduced-motion: reduce)") : null;
    var scrollRaf = null;

    function prefersReducedMotion() {
        return !!(reducedMotionQuery && reducedMotionQuery.matches);
    }

    function syncHeaderHeight() {
        var headerHeight;

        if (!$header.length) {
            return;
        }

        headerHeight = Math.ceil($header.outerHeight() || 0);
        if (headerHeight > 0) {
            document.documentElement.style.setProperty("--cc-header-height", headerHeight + "px");
        }
    }

    function getHeaderOffset() {
        return $header.length ? ($header.outerHeight() || 0) : 0;
    }

    function setHeaderCompactState() {
        if (!$header.length) {
            return;
        }

        if (($window.scrollTop() || 0) > 24) {
            $header.addClass("is-compact");
        } else {
            $header.removeClass("is-compact");
        }
    }

    function updateScrollProgress() {
        if (!$headerProgressBar.length) {
            return;
        }

        var scrollTop = $window.scrollTop() || 0;
        var documentHeight = Math.max($document.height() || 0, 0);
        var viewportHeight = Math.max($window.height() || 0, 1);
        var maxScrollable = Math.max(documentHeight - viewportHeight, 1);
        var progress = Math.min(Math.max(scrollTop / maxScrollable, 0), 1);

        $headerProgressBar.css("transform", "scaleX(" + progress.toFixed(4) + ")");
    }

    function runScrollBoundUpdates() {
        scrollRaf = null;
        setHeaderCompactState();
        updateScrollProgress();
    }

    function scheduleScrollBoundUpdates() {
        if (scrollRaf !== null) {
            return;
        }

        if (window.requestAnimationFrame) {
            scrollRaf = window.requestAnimationFrame(runScrollBoundUpdates);
        } else {
            scrollRaf = window.setTimeout(runScrollBoundUpdates, 16);
        }
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

        if (prefersReducedMotion()) {
            $htmlBody.stop(true).scrollTop(top);
        } else {
            $htmlBody.stop(true).animate({scrollTop: top}, 540);
        }

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

        $window.on("resize", function () {
            if (($window.width() || 0) > 991) {
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
            $navLinks.removeAttr("aria-current");

            if (currentHash) {
                $navLinks.filter("[href='" + currentHash + "']").attr("aria-current", "page").parent("li").addClass("active");
            }
        }

        $window.on("scroll resize", markActiveSection);
        markActiveSection();
    }

    function setupServicesAccordion() {
        $("[data-accordion-group]").each(function () {
            var $group = $(this);
            var $items = $group.find("[data-accordion-item]");

            if (!$items.length) {
                return;
            }

            $items.each(function () {
                var $item = $(this);
                var $summary = $item.children("summary");
                $summary.attr("aria-expanded", $item.prop("open") ? "true" : "false");
            });

            $items.on("toggle", function () {
                var $current = $(this);

                if ($current.prop("open")) {
                    $items.not($current).prop("open", false).children("summary").attr("aria-expanded", "false");
                }

                $current.children("summary").attr("aria-expanded", $current.prop("open") ? "true" : "false");
            });
        });
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

    function setupGalleryExpansion() {
        $(".cc-gallery-panel").each(function () {
            var $panel = $(this);
            var $toggle = $panel.find("[data-gallery-toggle]");
            var $extraItems = $panel.find(".cc-gallery-item.is-extra");

            if (!$toggle.length || !$extraItems.length) {
                if ($toggle.length) {
                    $toggle.attr("hidden", true);
                }
                return;
            }

            $toggle.on("click", function () {
                var expanded = $toggle.attr("aria-expanded") === "true";

                if (expanded) {
                    $extraItems.attr("hidden", true);
                    $toggle.attr("aria-expanded", "false").text("Показать все фото");
                } else {
                    $extraItems.removeAttr("hidden");
                    $toggle.attr("aria-expanded", "true").text("Скрыть лишние фото");
                }
            });
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
            opener: null,
            touchStartX: 0,
            touchEndX: 0
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

        function trapLightboxTab(event) {
            if (!$lightbox.hasClass("is-open") || event.key !== "Tab") {
                return;
            }

            var focusable = $lightbox.find("button:visible").toArray();
            if (!focusable.length) {
                return;
            }

            var first = focusable[0];
            var last = focusable[focusable.length - 1];

            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }

        function open(item) {
            var $item = $(item);
            var group = $item.data("lightbox-group");
            state.items = collectGroup(group);
            state.index = Number($item.data("lightbox-index")) || 0;
            state.opener = item;

            render();
            $lightbox.addClass("is-open").attr("aria-hidden", "false");
            $body.addClass("cc-no-scroll");
            $close.focus();
        }

        function close() {
            $lightbox.removeClass("is-open").attr("aria-hidden", "true");
            $body.removeClass("cc-no-scroll");
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

        $lightbox.on("touchstart", function (event) {
            if (!event.originalEvent.touches || !event.originalEvent.touches.length) {
                return;
            }
            state.touchStartX = event.originalEvent.touches[0].clientX;
            state.touchEndX = state.touchStartX;
        });

        $lightbox.on("touchmove", function (event) {
            if (!event.originalEvent.touches || !event.originalEvent.touches.length) {
                return;
            }
            state.touchEndX = event.originalEvent.touches[0].clientX;
        });

        $lightbox.on("touchend", function () {
            var delta = state.touchEndX - state.touchStartX;
            if (Math.abs(delta) < 42) {
                return;
            }
            if (delta < 0) {
                showNext();
            } else {
                showPrev();
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
            trapLightboxTab(event);
        });
    }

    function setupBootstrapModal() {
        var $modal = $("#contactForm_modal");

        if (!$modal.length) {
            return;
        }

        $modal.on("shown.bs.modal", function () {
            $body.addClass("cc-no-scroll");
            syncHeaderHeight();

            var $firstField = $modal.find("#name");
            if ($firstField.length) {
                $firstField.focus();
            }

            fitRecaptcha();
        });

        $modal.on("hidden.bs.modal", function () {
            $body.removeClass("cc-no-scroll");
        });
    }

    function fitRecaptcha() {
        var $widgets = $("#contactForm .g-recaptcha");
        var baseWidth = 304;
        var baseHeight = 78;

        if (!$widgets.length) {
            return;
        }

        $widgets.each(function () {
            var $widget = $(this);
            var $container = $widget.closest(".form-group");
            var containerWidth = Math.floor($container.width() || $widget.parent().width() || baseWidth);
            var scale = Math.min(1, containerWidth / baseWidth);

            if (!isFinite(scale) || scale <= 0) {
                scale = 1;
            }

            $widget.css({
                width: baseWidth + "px",
                transformOrigin: "left top",
                transform: "scale(" + scale.toFixed(3) + ")",
                minHeight: Math.ceil(baseHeight * scale) + "px"
            });
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

        var $button = $("#button");
        var $answer = $("#answer");
        var $loader = $("#loader");
        var buttonDefaultText = $.trim($button.text()) || "Отправить";
        var buttonLoadingText = $.trim(String($button.data("loading-text") || "")) || "Отправляем...";

        function trackFormEvent(eventName) {
            if (
                typeof yaCounter38639640 !== "undefined" &&
                yaCounter38639640 &&
                typeof yaCounter38639640.reachGoal === "function"
            ) {
                yaCounter38639640.reachGoal(eventName);
            }

            if (typeof window.ga === "function") {
                window.ga("send", "event", "contact_form", eventName);
            }
        }

        function setLoadingState(isLoading) {
            if (isLoading) {
                $button.prop("disabled", true).attr("aria-disabled", "true").text(buttonLoadingText);
                $form.attr("aria-busy", "true");
                $loader.stop(true, true).fadeIn(160);
                return;
            }

            $button.prop("disabled", false).attr("aria-disabled", "false").text(buttonDefaultText);
            $form.removeAttr("aria-busy");
            $loader.stop(true, true).fadeOut(120);
        }

        function getRecaptchaResponseToken() {
            var $tokenField = $form.find("textarea[name='g-recaptcha-response'], input[name='g-recaptcha-response']").first();
            return $.trim(String(($tokenField.val() || "")));
        }

        $form.on("submit", function (event) {
            if (event.isDefaultPrevented()) {
                return;
            }

            event.preventDefault();

            if ($button.prop("disabled")) {
                return;
            }

            if ($form.find(".g-recaptcha").length && getRecaptchaResponseToken() === "") {
                $answer.html("<div class='answer'><h4>Подтвердите, что вы не робот.</h4></div>");
                trackFormEvent("form_captcha_missing");
                return;
            }

            trackFormEvent("form_attempt");

            $.ajax({
                url: "handler.php",
                type: "POST",
                data: $form.serialize(),
                timeout: 20000,
                beforeSend: function () {
                    $answer.empty();
                    setLoadingState(true);
                },
                success: function (result) {
                    var isSuccess = /Ваше сообщение отправлено/i.test(String(result));

                    $loader.stop(true, true).fadeOut(180, function () {
                        $answer.html(result);
                    });

                    if (isSuccess) {
                        $form
                            .find("input[name='name'], input[name='company'], input[name='phone'], input[name='email'], textarea[name='message']")
                            .val("");
                        trackFormEvent("form_success");
                    } else {
                        trackFormEvent("form_validation_error");
                    }

                    if (isSuccess && typeof grecaptcha !== "undefined" && typeof grecaptcha.reset === "function") {
                        grecaptcha.reset();
                    }
                },
                error: function (xhr, textStatus) {
                    var fallbackHtml = "<div class='answer'><h4>Произошла ошибка! Попробуйте позже.</h4></div>";

                    if (xhr && xhr.status === 429) {
                        fallbackHtml = "<div class='answer'><h4>Слишком много запросов. Повторите попытку через несколько минут.</h4></div>";
                    }

                    $loader.stop(true, true).fadeOut(180, function () {
                        if (xhr && typeof xhr.responseText === "string" && xhr.responseText.indexOf("class=\"answer\"") !== -1) {
                            $answer.html(xhr.responseText);
                            return;
                        }
                        $answer.html(fallbackHtml);
                    });

                    if (textStatus === "timeout") {
                        trackFormEvent("form_timeout");
                    } else {
                        trackFormEvent("form_error");
                    }
                },
                complete: function () {
                    setLoadingState(false);
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

        if (prefersReducedMotion() || !("IntersectionObserver" in window)) {
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

    $(function () {
        syncHeaderHeight();
        setHeaderCompactState();
        updateScrollProgress();

        $window.on("resize orientationchange load", function () {
            syncHeaderHeight();
            scheduleScrollBoundUpdates();
            fitRecaptcha();
        });

        $window.on("scroll", scheduleScrollBoundUpdates);

        setupHeaderMenu();
        setupAnchors();
        setupScrollSpy();
        setupServicesAccordion();
        setupGalleryTabs();
        setupGalleryExpansion();
        setupGalleryLightbox();
        setupBootstrapModal();
        setupContactForm();
        setupRevealAnimations();
        fitRecaptcha();

        setTimeout(function () {
            syncHeaderHeight();
            scheduleScrollBoundUpdates();
        }, 160);
    });
})(jQuery);
