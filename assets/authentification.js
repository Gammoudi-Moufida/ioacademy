const bootstrap = require('bootstrap');

function ValidateEmail(mailtxt) {
  if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(mailtxt)) {
    return (true)
  }
  return (false)
}

var $formlostpassword = $('form[name="ForgotPasswordModal"]');
$formlostpassword.on("submit", function (e) {
  e.preventDefault();
  $('#result').html('');
  email = $('#email_lost_pass').val();
  isvalidmail = ValidateEmail(email);
  if (email.length == 0) {
    $('#result').html('<p class="alert alert-danger login_error_class m-0">Champ email obligatoire.</p>');
  }
  else if (!isvalidmail) {
    $('#result').html('<p class="alert alert-danger login_error_class m-0">Vérifiez la format du champ email.</p>');
  }
  else {
    $.ajax({
      url: $formlostpassword.attr('action'),
      method: 'POST',
      data: $formlostpassword.serialize()
    })
      .done(function (data) {
        if (data == "noexist") {
          $('#result').html('<p class="alert alert-danger login_error_class m-0">Cet email ne correspond à aucun compte utilisateur !</p>');
        }
        else if (data == "err_token") {
          $('#result').html('<p class="alert alert-danger login_error_class m-0">Demandes abusives bloqué, réessayer plus tard !</p>');
        }
        else if (data == "email_sent") {
          $('#result').html('<p class="alert alert-success login_error_class m-0">Consultez votre boite mail pour continuer les processus avant le découlement d\'une heure.</p>');
        }
      })
      .fail(function (err) {
      });
  }
});

var closem=$('#closemodal');
closem.on('click', function(e){
  window.location.reload();
});
var close=$('#close');
close.on('click', function(e){
  window.location.reload();
});
var $form1 = $('form[name="loginForm"]');
$form1.on("submit", function (e) {
  e.preventDefault();
  $('.login_error_class').html('');
  $('#registration_result').html('');
  let check = true;
  email = $('#login_email').val();
  pass = $('#login_password').val();
  isvalidmail = ValidateEmail(email);
  if (email.length == 0 || pass.length == 0 || !isvalidmail) {
    check = false;
    if (email.length == 0) {
      $('#login_email').after('<p class="text-danger login_error_class m-0">Champ email obligatoire</p>');
    }
    else {
      if (!isvalidmail) {
        $('#login_email').after('<p class="text-danger login_error_class m-0">Format d\'adresse email invalide</p>');
      }
    }
    if (pass.length == 0) {
      $('#login_password').after('<p class="text-danger login_error_class m-0">Champ mot de passe obligatoire</p>');
    }
  }
  if (check) {
    $.ajax({
      url: $form1.attr('action'),
      method: 'POST',
      data: $form1.serialize()
    })
      .done(function (data) {
        $('#registration_result').html('');
        if (data == "Error") {
          $('#registration_result').html('<p class="alert alert-danger text-center m-2">E-mail et / ou mot de passe incorrect !</p>');
        }
        else if (data == "InverifiedAccount") {
          $('#registration_result').html('<p class="alert alert-danger text-center m-2">Vous n\'avez pas encore confirmé votre inscription, cliquez sur le lien ci-dessous pour <a class="fw-bold text-decoration-none" role="button" id="resendlink" data-target="/revalidatemail/' + email + '">renvoyer un nouvel email</a> !</p>');
        }
        else if (data == "BlockedAccount") {
          $('#registration_result').html('<p class="alert alert-danger text-center m-2">Votre compte est désactivé par l\'administrateur !</p>');
        }
        else {
          $('#registration_result').html('<p class="alert alert-success text-center m-2">Authentification effectuée.</p>');
          // window.location = '/user/profile';
          window.location.reload();
        }
      })
      .fail(function (err) {
        $('#registration_result').html('<p class="alert alert-danger text-center m-2">Vérifiez votre connexion réseau !</p>');
      });
  }
});

$(document).on('click', '#resendlink', function (e) {
  e.preventDefault();
  var data = $(this).attr("data-target");
  $.ajax({
    url: data,
    method: 'POST',
  })
    .done(function (data) {
      if (data == 'EmailSent') {
        $('#registration_result').html('<p class="alert alert-success text-center m-2">Vous avez recu un nouveau email de confirmation d\'inscription.</p>');
      }
      else {
        $('#registration_result').html('<p class="alert alert-danger text-center m-2">Vérifiez votre connexion réseau !</p>');
      }
    });
});
$('#CompleteSignUpModal').on('hidden.bs.modal', function () {
  alert('hello');
});

var $form = $('form[name="registration_form"]');
$form.on('submit', function (e) {
  e.preventDefault();
  $(".registration_error_class").remove();
  $('#registration_result').html("");
  $.ajax({
      url: $form.attr('action'),
      method: 'POST',
      data: $form.serialize()
    })
      .done(function (data) {
        if (data == 'Inserted') {
          var modalRegistration = bootstrap.Modal.getOrCreateInstance('#SignUpModal')
          modalRegistration.hide()
          var completemodalLogin = bootstrap.Modal.getOrCreateInstance('#CompleteSignUpModal',{show: false,backdrop: 'static'})
          completemodalLogin.show()
          $('#registration_results').html('<p class="alert alert-success text-center m-2">Inscription effectuée.</p>');
        }
        else {
          let errs = JSON.stringify(data);
          errs = JSON.parse(errs);
          $.each(errs, function (index, value) {
              $('#registration_form_' + index).after('<p class="text-danger registration_error_class m-0">' + value + '</p>');
          });
        }
      })
      .fail(function (error) {
        $('#registration_result').html('<p class="alert alert-danger text-center m-2">Erreur lors de l\'inscription !</p>');
      });
});

var $formcomplete = $('form[name="complete_registration_form"]');
$formcomplete.on('submit', function (e) {
  e.preventDefault();
  $(".registration_error_class").remove();
  $('#registration_results').html("");
  $.ajax({
      url: $formcomplete.attr('action'),
      method: 'POST',
      data: $formcomplete.serialize()
    })
      .done(function (data) {
        if (data == 'Inserted') {
          $('#registration_results').html('<p class="alert alert-success text-center m-2">Information enregistrée.</p>');
          window.location.reload();
        }
        else {
          let errs = JSON.stringify(data);
          errs = JSON.parse(errs);
          $.each(errs, function (index, value) {
              $('#registration_form_' + index).after('<p class="text-danger registration_error_class m-0">' + value + '</p>');
          });
        }
      })
      .fail(function (error) {
        $('#registration_result').html('<p class="alert alert-danger text-center m-2">Erreur lors de l\'inscription !</p>');
      });
});