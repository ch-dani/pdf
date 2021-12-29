(function () {
    $(".dropdown-toggle-btn").on("click", function (e) {
        e.preventDefault();

        $(".dropdown-menu-upload").fadeToggle(200);
    });

    $(".tools-menu-item").on("click", function (e) {

        e.preventDefault();
        var th = $(this);
        var currentMenu = th.next();

        $(".tools-menu-item").parent().removeClass("active");
        th.parent().addClass("active");


        if (currentMenu.hasClass("active")) {
            $(".tools-dropdown-menu").removeClass("active");
            $(".tools-menu-item").parent().removeClass("active");
        } else {
            $(".tools-dropdown-menu").removeClass("active");
            currentMenu.addClass("active");
        }

    });


    $(document).on("click", ".editable-btn", function (e) {

        e.preventDefault();
        var th = $(this);
        var currentBtn = th.next();

        $(".editable-btn").parent().removeClass("active");
        th.parent().addClass("active");


        if (currentBtn.hasClass("active")) {
            $(".tools-dropdown-menu").removeClass("active");
            $(".editable-btn").parent().removeClass("active");
        } else {
            $(".tools-dropdown-menu").removeClass("active");
            currentBtn.addClass("active");
        }

    });


    $(".font-size-range").on("input", function () {
        var rangeVal = $(this).val();
        var numberVal = $(".font-size-number");
        numberVal.val(rangeVal);
        numberVal.change();
    });


    $(".chank").on("click", function (e) {
        e.preventDefault();
        $(".text-editable-menu").show();
    });


    $(document).on("mouseup", function (event) {
        var target = event.target;
        var container = $(".text-editable-menu");
        var uploadBtn = $(".upload-btn-wrap");
        var toolsDropMenu = $(".tools-dropdown-menu");

//		if(!container.is(target) && container.has(target).length === 0){
//			container.hide();
//		}

        if (!uploadBtn.is(target) && uploadBtn.has(target).length === 0) {
            $(".dropdown-menu-upload").hide();
        }

        if (toolsDropMenu.hasClass("active")) {

            if (!toolsDropMenu.is(target) && toolsDropMenu.has(target).length === 0 && !$(".tools-menu-item").is(target) && $(".tools-menu-item").has(target).length === 0 && !$(".editable-btn").is(target) && $(".editable-btn").has(target).length === 0) {
                toolsDropMenu.removeClass("active");
                toolsDropMenu.parent().removeClass("active");
            }
        }


    });

    $(".options-btn-transparent").click(function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.toggleClass("active");

        if ($this.hasClass("active")) {
            $this.text("Fewer options");
        } else {
            $this.text("More options");
        }

        $(".fixed-task-form .more-options-box:not(.skip-fade)").fadeToggle(200);
    });


    $(".btn-dropdown-toggle").click(function (e) {
        e.preventDefault();
        $(".dropdown-menu-right").fadeToggle(200);
    });

    $(".dropdown-menu-item-customize").click(function (e) {
        e.preventDefault();
        $(".dropdown-menu-right").hide();
        var dataValue = $(this).data("value"),
            hfCustomizer = $(".hf-format-customize");


        $(document).trigger("change_pdf_header_type", [$(this).data("value")]);

        hfCustomizer.each(function () {
            var dataId = $(this).data("id");
            if (dataValue === dataId) {
                $(".hf-format-customize").hide();
                $(this).fadeIn();
            }
        });

    });

    $(".bates-select-option").click(function (e) {
        e.preventDefault();
        var dataValue = $(this).val(),
            batesCustomizer = $(".bates-format-customize");

        batesCustomizer.each(function () {
            var dataId = $(this).data("id");
            if (dataValue === dataId) {
                $(".bates-format-customize").hide();
                $(this).fadeIn();
            }
        });

    });


    if ($(".minicolors-input").length) {
        $(".minicolors-input").minicolors({
            defaultValue: "#000"
        });
    }


    if ($(".contact-btn-popup").length) {
        $(document).on("click", ".contact-btn-popup", function (e) {
            e.preventDefault();
        });

//		$(".contact-btn-popup").fancybox({
//			baseClass: "fancybox-contact-form-modal",
//		});
    }


})(jQuery);
