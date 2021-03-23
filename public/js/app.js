/** Select Color - Questões Semafóricas */
$("select[data-option-color]").each(function() {
    $(this).addClass("select-color");

    var colors = $(this)
        .data("option-color")
        .split(",");
    $(this)
        .find("> option:gt(0)")
        .each(function(k) {
            $(this).addClass("color-" + colors[k]);
            $(this).attr("color", colors[k]);
        });

    $(this)
        .change(function() {
            if (!$(this).val()) {
                $(this).attr("color", null);
            } else {
                var color = $(this)
                    .find("option:selected")
                    .attr("color");
                $(this).attr("color", color);
            }
        })
        .change();
});

/**
 * Datatable Warning no console
 */
$.fn.dataTable.ext.errMode = "none";
$("#table").on("error.dt", function(e, settings, techNote, message) {
    console.log("An error has been reported by DataTables: ", message);
});

/**
 * Datatable Expand Button
 *
 * Wrapper Bootstrap Collapse
 *
 * Bootstrap Collapse (data-toggle) não functiona com o core-ui (testei atualizar o bootstrap/coreui/retirar outros .js)
 */
$("body").on("click", ".btn-collapse-ater", function() {
    var elem = $(this)
        .parent()
        .find(".collapse");

    $(this).toggleClass("show");

    if (elem.hasClass("show")) {
        elem.collapse("hide");
    } else {
        elem.collapse("show");
    }
});

/**
 * select / show/hidden / value == 1 (show), value == 0 (hide)
 */
function selectAutoYesNo(idSelect, idCard) {
    $(idSelect)
        .change(function() {
            if ($(this).attr("type") == "checkbox") {
                if ($(this).prop("checked")) {
                    $(idCard).removeClass("d-none");
                } else {
                    $(idCard).addClass("d-none");
                }
            } else {
                if ($(this).val() == 0) {
                    $(idCard).addClass("d-none");
                } else {
                    $(idCard).removeClass("d-none");
                }
            }
        })
        .change();
}

function selectAutoYesNoNone(idSelect, idCard) {
    $(idSelect)
        .change(function() {
            if ($(this).attr("type") == "checkbox") {
                if ($(this).prop("checked")) {
                    $(idCard).addClass("d-none");
                } else {
                    $(idCard).removeClass("d-none");
                }
            } else {
                if ($(this).val() == 0) {
                    $(idCard).removeClass("d-none");
                } else {
                    $(idCard).addClass("d-none");
                }
            }
        })
        .change();
}

function selectAutoComboSim(idSelect, idCard) {
    $(idSelect)
        .change(function() {
            var v = $(this).val();

            if (v == "sim") {
                $(idCard).removeClass("d-none");
            } else {
                $(idCard).addClass("d-none");
            }
        })
        .change();
}

/**
 * AutoLink - Ajax Refresh
 */
var _externalAutoLinks = [];

function initAutoLink(container) {
    for (var i in this._externalAutoLinks) {
        this._externalAutoLinks[i](container);
    }
}

function addAutoLink(func) {
    window._externalAutoLinks.push(func);
    func($(document));
}

/**
 * Order functions (Datatable)
 */
function initDatatableForms() {
    $(".datatable-form").each(function(index) {
        $(this).append(function() {
            if (!$(this).find("form").length > 0) {
                return (
                    "<iframe class='iframe-datatable' style='display:none' name='teste" +
                    index +
                    "'></iframe>" +
                    "\n<form target='teste" +
                    index +
                    "' action='" +
                    $(this).attr("href") +
                    "' method='POST' style='display:none'>\n" +
                    "<input type='hidden' name='_method' value='" +
                    $(this).attr("data-method") +
                    "'>\n" +
                    "<input type='hidden' name='_token' value='" +
                    $('meta[name="csrf-token"]').attr("content") +
                    "'>\n" +
                    "</form>\n"
                );
            } else {
                return "";
            }
        });

        $(this)
            .find(".iframe-datatable")
            .on("load", function() {
                var iframeUrl = $(this)
                    .contents()
                    .get(0).location.href;

                if (iframeUrl && iframeUrl != "about:blank") {
                    $("table")
                        .DataTable()
                        .ajax.reload();
                }
            });
        $(this)
            .attr("href", "#")
            .attr("style", "cursor:pointer;")
            .attr("onclick", '$(this).find("form").submit(); return false;');
    });
}

/**
 * Delete function
 */

function initDeleteForms() {
    /**
     * Add the data-method="delete" forms to all delete links
     */
    function addDeleteForms() {
        $("[data-method='delete'], [data-method='post']")
            .append(function() {
                if (!$(this).find("form").length > 0) {
                    return (
                        "\n<form action='" +
                        $(this).attr("href") +
                        "' method='POST' name='delete_item' style='display:none'>\n" +
                        "<input type='hidden' name='_method' value='" +
                        $(this).attr("data-method") +
                        "'>\n" +
                        "<input type='hidden' name='_token' value='" +
                        $('meta[name="csrf-token"]').attr("content") +
                        "'>\n" +
                        "</form>\n"
                    );
                } else {
                    return "";
                }
            })
            // .removeAttr("href")
            .attr("href", "#")
            .attr("style", "cursor:pointer;")
            .attr("onclick", '$(this).find("form").submit(); return false;');
    }

    /**
     * Disable all submit buttons once clicked
     */
    $("form").submit(function() {
        $(this)
            .find('input[type="submit"]')
            .attr("disabled", true);
        $(this)
            .find('button[type="submit"]')
            .attr("disabled", true);
        return true;
    });

    /**
     * Generic confirm form delete using Sweet Alert
     */
    $("body")
        .on("submit", "form[name=delete_item]", function(e) {
            e.preventDefault();
            var form = this;
            var link = $(this).parent(); // $('a[data-method="delete"]');
            var cancel = link.attr("data-trans-button-cancel")
                ? link.attr("data-trans-button-cancel")
                : "Cancel";
            var confirm = link.attr("data-trans-button-confirm")
                ? link.attr("data-trans-button-confirm")
                : "Yes, delete";
            var title = link.attr("data-trans-title")
                ? link.attr("data-trans-title")
                : "Are you sure you want to delete this item?";

            //Força o reposicionamento do scroll do "parent" p/ o usuário conseguir ver o modal que foi aberto
            //Isso é utilizado em sessões que tem iframes com listagem, ao clicar em excluir ou outra ação que abre o modal genérico (Swal).
            var idIframe = null;
            if (window.parent && window.frameElement) {
                var idIframe = window.frameElement.getAttribute("id");
                if (idIframe) {
                    var parentIframe = $(window.parent.document).find(
                        "#" + idIframe
                    );

                    $(window.parent.document).scrollTop(
                        parentIframe.offset().top - 150
                    );
                }
            }

            Swal.fire({
                title: title,
                showCancelButton: true,
                confirmButtonText: confirm,
                cancelButtonText: cancel,
                type: "warning",
                position: idIframe ? "top" : "center"
            }).then(function(result) {
                result.value && form.submit();
            });
        })
        .on("click", "a[name=confirm_item]", function(e) {
            /**
             * Generic 'are you sure' confirm box
             */
            e.preventDefault();
            var link = $(this);
            var title = link.attr("data-trans-title")
                ? link.attr("data-trans-title")
                : "Are you sure you want to do this?";
            var cancel = link.attr("data-trans-button-cancel")
                ? link.attr("data-trans-button-cancel")
                : "Cancel";
            var confirm = link.attr("data-trans-button-confirm")
                ? link.attr("data-trans-button-confirm")
                : "Continue";
            Swal.fire({
                title: title,
                showCancelButton: true,
                confirmButtonText: confirm,
                cancelButtonText: cancel,
                type: "info"
            }).then(function(result) {
                result.value && window.location.assign(link.attr("href"));
            });
        });
    $('[data-toggle="tooltip"]').tooltip();

    addDeleteForms();
}

/**
 * FIX CoreUi File input
 */
$(document).ready(function() {
    $(".custom-file-input").on("change", function() {
        var fileName = $(this)
            .val()
            .split("\\")
            .pop();

        $(this)
            .parent()
            .parent()
            .find(".custom-file-label")
            .html(fileName);
    });

    $(".custom-file").each(function(v) {
        // $(this).parent().prepend(
        //      "<label>" +
        //          $(this)
        //              .find("label")
        //              .html() +
        //          "</label>"
        // );

        var value = $(this)
            .find("input")
            .attr("value");

        $(this)
            .find(".custom-file-label")
            .html(value);

        if (value) {
            var filepath = storage_url + value;

            var filepathTest = (storage_url + value).toLowerCase();
            var isImage =
                filepathTest.indexOf(".jpg") > -1 ||
                filepathTest.indexOf(".png") > -1 ||
                filepathTest.indexOf(".jpeg") > -1 ||
                filepathTest.indexOf(".gif") > -1;

            var labelBtnInfo = isImage ? "Visualizar" : "Download";

            var label = $(this).append(
                "<div class='col-md-1 btn-view'><a class='btn btn-info' href='" +
                    filepath +
                    "' target='_blank'>" +
                    labelBtnInfo +
                    "</a>"
            );

            if (isImage) {
                var popover = $(this)
                    .find(".btn-view")
                    .popover({
                        html: true,
                        trigger: "hover",
                        content: function() {
                            return (
                                '<img class="popover-image-100" src="' +
                                filepath +
                                '" />'
                            );
                        }
                    });
            }
        }
    });
});

/**
 * Auto Link
 */
$(document).ready(function() {
    addAutoLink(function(parent) {
        //Aplica mascara nos inputs
        parent.find("input:text").setMask();

        //Desabilita inputs "readonly"
        parent
            .find("select[readonly='readonly']")
            .find("option:not(:selected)")
            .attr("disabled", "disabled");

        //Label pesquisar no Datatable
        $(".dataTables_filter")
            .find("input")
            .attr("placeholder", "Pesquisar");

        //Cliente pediu para remover funcionalidade
        // $(".alert")
        //     .fadeTo(4000, 500)
        //     .slideUp(500, function() {
        //         $(".alert").slideUp(500);
        //     });

        initDatatableForms();

        initDeleteForms();
    });

    addAutoLink(function(parent) {
        parent.find(".card-ater .btn-save-and-redirect").click(function(event) {
            event.preventDefault();
            event.stopPropagation();

            $("input[name='custom-redirect'").val($(this).attr("data"));

            if (
                $(".card-footer-ater").find('button[type="submit"]').length > 0
            ) {
                $(".card-footer-ater")
                    .find('button[type="submit"]')
                    .first()
                    .click();
            } else if (
                //Tratamento especifico para botões de submit que abrem modal. Ex: Checklist Base
                $(".card-footer-ater .col-submit .btn-primary").length > 0
            ) {
                $(".card-footer-ater .col-submit .btn-primary")
                    .first()
                    .click();
            }
        });
    });
});

$(document).ready(function() {
    // $(window).on("load", function() {
    var hash = location.hash.replace("#", "");

    if (hash != "") {
        setTimeout(function() {
            $("html, body").animate(
                { scrollTop: $("#" + hash).offset().top - 150 }, //offset do header
                600
            );
        }, 500);
    }
    // });
});

/**
 * DataTable
 */
function datatableRemoveItem(evt) {
    evt.preventDefault();
    evt.stopPropagation();

    var v = confirm("Você deseja remover o registro?");
    if (v) {
        $(evt.target)
            .parent("form")
            .submit();
    }
}

function htmlDecode(data) {
    var txt = document.createElement("textarea");
    txt.innerHTML = data;
    return txt.value;
}

/**
 * Form Validation
 */
(function() {
    "use strict";
    window.addEventListener(
        "load",
        function() {
            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.getElementsByClassName("needs-validation");
            // Loop over them and prevent submission
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener(
                    "submit",
                    function(event) {
                        event.preventDefault();
                        event.stopPropagation();

                        var firstInvalidField = null;

                        //Custom Validation
                        $(form.elements).each(function(v) {
                            var field = form.elements[v];

                            if (field.classList.contains("req-cpf")) {
                                field.setCustomValidity(
                                    isValidCPF(field.value)
                                        ? ""
                                        : "CPF Inválido"
                                );
                            } else if (field.classList.contains("req-cnpj")) {
                                field.setCustomValidity(
                                    isValidCNPJ(field.value)
                                        ? ""
                                        : "CNPJ Inválido"
                                );
                            } else if (
                                field.files &&
                                field.files.length > 0 &&
                                field.getAttribute("maxlength")
                            ) {
                                var isValidFileMessage = isValidFile(
                                    field.files,
                                    field.getAttribute("maxlength")
                                );

                                //Não funciona, por isso foi adicionado a validação por "firstInvalidField"
                                field.setCustomValidity(isValidFileMessage);

                                //Força o firstInvalidField
                                if (isValidFileMessage.length > 0) {
                                    firstInvalidField = field;
                                    toastr.error(isValidFileMessage);
                                }
                            }

                            if (!field.validity.valid && !firstInvalidField) {
                                firstInvalidField = field;
                            }
                        });

                        if (form.checkValidity() === false) {
                            toastr.error("Verifique os campos obrigatórios.");

                            $("html, body").animate(
                                {
                                    scrollTop:
                                        $(firstInvalidField).offset().top - 150
                                },
                                500
                            );
                        }

                        form.classList.add("was-validated");

                        //Força o submit
                        if (!firstInvalidField) {
                            var self = this;

                            //Debounce
                            if (self.timeoutBlocked) {
                                clearTimeout(self.timeoutBlocked);
                            }
                            self.timeoutBlocked = setTimeout(function() {
                                self.blocked = false;
                            }, 2000);

                            if (self.blocked) {
                                return;
                            }
                            self.blocked = true;

                            this.submit();
                        }
                    },
                    false
                );
            });
        },
        false
    );
})();

function isValidFile(files, size) {
    for (var i = 0; i <= files.length - 1; i++) {
        const fsize = files.item(i).size;

        if (Math.round(fsize / 1024) >= size) {
            return "Tamanho máximo do arquivo: " + size / 1024 + "mb";
        }
    }

    return "";
}

function isValidCNPJ(cnpj) {
    if (cnpj.length == 0) return true;

    //formata o cnpj
    cnpj = cnpj.replace(".", "");
    cnpj = cnpj.replace(".", "");
    cnpj = cnpj.replace(".", "");
    cnpj = cnpj.replace("-", "");
    cnpj = cnpj.replace("/", "");

    if (cnpj.length != 14) return false;

    var firstLetter = cnpj.charAt(0);
    var testEqualsCnpj = new RegExp("^[" + firstLetter + "]{13}$").test(cnpj);
    if (testEqualsCnpj) return false;

    var multiplicador = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    var multiplicador2 = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    var digito1 = cnpj.charAt(12);
    var digito2 = cnpj.charAt(13);
    var r = 0;

    // Calculo do primeiro digito
    for (var i = 0; i < multiplicador.length; i++) {
        r += cnpj.charAt(i) * multiplicador[i];
    }
    r %= 11;
    r = r < 2 ? 0 : 11 - r;
    if (r != digito1) return false;

    // Calculo do segundo digito
    r = 0;
    for (i = 0; i < multiplicador2.length; i++) {
        r += cnpj.charAt(i) * multiplicador2[i];
    }
    r %= 11;
    r = r < 2 ? 0 : 11 - r;
    if (r == digito2) return true;
    else return false;
}

function isValidCPF(cpf) {
    if (cpf.length == 0) return true;

    //reformata o cpf caso esteja formtado =)
    cpf = cpf.replace(".", "");
    cpf = cpf.replace(".", "");
    cpf = cpf.replace("-", "");

    if (cpf.length != 11) return false;

    //Anula Cpf`s com mesmo numero 11111111111
    var firstLetter = cpf.charAt(0);
    ///g  o group do regexp
    var testEqualsCpf = new RegExp("^[" + firstLetter + "]{11}$").test(cpf);
    if (testEqualsCpf) return false;

    var multiplicador = [10, 9, 8, 7, 6, 5, 4, 3, 2];
    var digito1 = cpf.charAt(9);
    var digito2 = cpf.charAt(10);
    var r = 0;

    // Calculo do primeiro digito
    for (var i = 0; i < multiplicador.length; i++) {
        r += cpf.charAt(i) * multiplicador[i];
        multiplicador[i] += 1;
    }
    r %= 11;
    r = r < 2 ? 0 : 11 - r;
    if (r != digito1) return false;

    // Calculo do segundo digito
    multiplicador.push(2);
    r = 0;
    for (i = 0; i < multiplicador.length; i++) {
        r += cpf.charAt(i) * multiplicador[i];
    }
    r %= 11;
    r = r < 2 ? 0 : 11 - r;
    if (r == digito2) return true;
    else return false;
}

/**
 * Iframe AutoSize
 */

function autosizeIframe(strElem) {
    var elem = $(strElem);

    var contents = elem.contents();

    if (contents.length > 0) {
        var firstChildren = contents
            .find("body")
            .children()
            .not(".pace")
            .eq(0);

        if (firstChildren.length > 0) {
            contents.find("body").css("overflow", "hidden");

            var h = firstChildren.outerHeight();

            if (h > 5500 || !h || h === 0) {
                return;
            }

            const newH = h + 100;
            if (elem.height() >= h && elem.height() <= newH) {
                contents.find("body").css("overflow", "auto");
                return;
            }

            elem.height(h);
            contents.find("body").css("overflow", "auto");
        }
    }
}

/**
 * Debounce DataTable
 */
function debounceSearch(tableId) {
    var $searchBox = $(tableId + "_filter input[type='search']");
    $searchBox.off();

    var searchDebouncedFn = _.debounce(function() {
        $(tableId)
            .DataTable()
            .search($searchBox.val())
            .draw();
    }, 1000);

    $searchBox.on("keyup", searchDebouncedFn);
    $searchBox.on("click", function() {
        var self = $(this);

        setTimeout(function() {
            if (self.val() == "") {
                searchDebouncedFn();
            }
        }, 100);
    });
}

/**
 * Input Table
 */
function inputTabela(self) {
    var inputValue = $(self);
    var container = inputValue.parent();

    if (!inputValue.data("colunas")) {
        return;
    }

    var colunas = inputValue.data("colunas").split(",");

    var linhas = inputValue.data("linhas");
    if (!linhas) {
        linhas = [];
    } else {
        linhas = linhas.split(",");
        if (linhas.length == 1 && linhas[0] == "") {
            linhas = [];
        }
    }

    var tabela = createTable(colunas);

    function init() {
        inputValue.attr("type", "hidden");

        container.addClass("table-input");
    }

    function createTable(colunas) {
        var table = $("<table/>").attr(
            "class",
            "table table-bordered table-input-tabela"
        );

        var rowHeader = $("<tr/>");

        if (linhas.length > 0) {
            rowHeader.append($("<th/>").text(linhas[0]));
        }

        $.each(colunas, function(k, v) {
            rowHeader.append($("<th/>").text(v));
        });

        table.append(rowHeader);

        return table;
    }

    function addInputs(rowValues, index) {
        var defaultInput = $("<textarea/>")
            .attr("type", "text")
            .attr("rows", 2)
            .attr("style", "resize:none")
            .attr("class", "form-control");

        var rowInputs = $("<tr/>");

        if (linhas.length > 0) {
            rowInputs.append($("<th/>").text(linhas[index + 1]));
        }

        $.each(colunas, function(k, v) {
            var customInput = defaultInput.clone();
            customInput.attr("name", "tabela-input-" + k + "[]");
            customInput.on("keyup blur", changeValues);

            if (rowValues && rowValues.length > 0) {
                customInput.val(rowValues[k]);
            }

            rowInputs.append($("<td/>").html(customInput));
        });

        tabela.append(rowInputs);
    }

    function loadData(values) {
        var obvalues;

        try {
            obvalues = $.parseJSON(values);
        } catch (e) {
            obvalues = [];
        }

        var list = [];
        colunas.map(function(k) {
            list.push(obvalues[k]);
        });

        transpose(list).map(function(v, k) {
            //Previne adicionar mais dados do que linhas (Cadastro: Sem linhas -> Com Linha -> Sem linha)
            if (linhas.length > 0 && k >= linhas.length - 1) {
                return;
            }

            addInputs(v, k);
        });

        //Força adição de novas linhas quando tiver linhas com nome
        if (linhas.length > 0) {
            const length = transpose(list).length;

            for (var i = length; i < linhas.length - 1; i++) {
                if (linhas.length > 0 && i >= linhas.length - 1) {
                    return;
                }

                addInputs(null, i);
            }
        }
    }

    function transpose(matrix) {
        if (!matrix[0]) return [];

        return matrix[0].map((col, i) =>
            matrix.map(row => (row && row[i] ? row[i] : null))
        );
    }

    function changeValues() {
        var data = {};

        colunas.map(function(v, k) {
            var values = tabela
                .find('textarea[name="tabela-input-' + k + '[]"]')
                .map(function(v) {
                    return $(this).val();
                })
                .get();

            data[v] = values;
        });

        inputValue.val(JSON.stringify(data));
    }

    init();

    container.append(tabela);

    if (!linhas || linhas.length == 0) {
        var button = $("<div/>")
            .attr("class", "btn btn-primary float-right")
            .text("Nova Linha");
        button.click(addInputs);
        container.append(button);
    }

    loadData(inputValue.val());

    if (linhas.length == 0) {
        addInputs();
    }
}

$(document).ready(function() {
    $(".input-tabela").each(function() {
        inputTabela(this);
    });
});

// a11y fix ( auto complete )

setTimeout(() => {
    $(".select2-search__field").attr("aria-label", function() {
        return $(this)
            .closest(".form-group")
            .children("label")
            .text();
    });
}, 500);

document.addEventListener("DOMContentLoaded", function() {
    var es = document.getElementsByClassName("fix-anchor");
    for (var i = 0; i < es.length; i++) {
        es[i].addEventListener("click", function(e) {
            e.preventDefault();
            if (e.target.classList.contains("open-menu-on-click")) {
                document
                    .getElementById("sidebar")
                    .classList.add("c-sidebar-lg-show");
            }
            document.location.hash = e.target.getAttribute("href");
        });
    }
});

function setScrollPositionToIframe() {
    var idIframe = null;
    if (window.parent && window.frameElement) {
        var idIframe = window.frameElement.getAttribute("id");
        if (idIframe) {
            var parentIframe = $(window.parent.document).find("#" + idIframe);

            $(window.parent.document).scrollTop(
                parentIframe.offset().top - 150
            );
        }
    }
}
