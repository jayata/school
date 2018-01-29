/**
 * Created by ayata on 28/01/18.
 */

$(function () {
    $('#matricula_submit').on('click', function (e) {

        var aluno = $("#matricula_aluno").val();
        var matricula_curso = $("#matricula_curso").val();
        var anoleitivo = $("#anoleitivo").val();
        var periodo = $("#periodo").val();

        if (aluno == "" || matricula_curso == "" || anoleitivo == ""  || periodo == "") {
            return;
        }
        e.preventDefault();
        var csrftoken = getCookie('csrftoken');
        var data = new Object();
        data.matricula_aluno = aluno;
        data.matricula_curso = matricula_curso;
        data.anoleitivo = anoleitivo;
        data.periodo = periodo;

        var promise = $.ajax({
            method: 'post',
            dataType: 'json',
            url: "/admin/matricular/check",
            data: data,
            headers: {
                'X-CSRFToken': csrftoken
            }
        });
        $.when(promise)
            .done(function (response) {
                if (!response.validator) {
                    $('#alert-anoleitivo').alert();
                    $('#alert-anoleitivo').show();
                    e.preventDefault();
                } else {
                    if (response.data) {
                        if(response.available){
                            $('#form_matricula_aluno').submit();
                        }else{
                            $('#modal_form_matricula_available').modal('show');
                        }
                    } else {
                        $('#modal_form_matricula').modal('show');
                    }
                }
            })
            .fail(function (reason) {
                console.log(reason)
            });
        // }
    });
    // $("#datepicker").datepicker( {
    //     format: " yyyy", // Notice the Extra space at the beginning
    //     viewMode: "years",
    //     minViewMode: "years"
    // });

    // $.when(promise)
    //     .done(function (response) {
    //         if (response.categorias) {
    //             $("#id_fk_categoria").html("");
    //             optionsAsString = '<option value="" selected="selected">---------</option>'
    //             for (var i = 0; i < response.categorias.length; i++) {
    //                 key = Object.keys(response.categorias[i])[0]
    //                 value = response.categorias[i][key]
    //
    //                 optionsAsString += "<option value='" + key + "'>" + value + "</option>";
    //             }
    //             $("#id_fk_categoria").html(optionsAsString);
    //             $('#id_fk_categoria').removeAttr("disabled");
    //             $.uniform.update();
    //             Metronic.init();
    //             Layout.init();
    //         }
    //         else {
    //             $("#id_fk_categoria").html("");
    //             toastr.error(response.error);
    //         }
    //     })
    //     .fail(function (reason) {
    //         console.log(reason)
    //     });


//construye la cokie para las peticiones post
    function getCookie(name) {
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
});
