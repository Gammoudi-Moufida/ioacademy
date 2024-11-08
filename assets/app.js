import './styles/app.css';
const $ = require('jquery');
require('bootstrap');
import 'bootstrap/dist/css/bootstrap.min.css';
require('webpack-jquery-ui/autocomplete');
require('webpack-jquery-ui/css');

let btn_to_top = document.getElementById("btn-back-to-top");
btn_to_top.style.display = "none";
window.onscroll = function () {
  scrollFunction();
};

function scrollFunction() {
  if (
    document.body.scrollTop > 20 ||
    document.documentElement.scrollTop > 20
  ) {
    btn_to_top.style.display = "block";
  } else {
    btn_to_top.style.display = "none";
  }
}
btn_to_top.addEventListener("click", backToTop);

function backToTop() {
  document.body.scrollTop = 0;
  document.documentElement.scrollTop = 0;
}

let items = document.querySelectorAll('.carousel .carousel-item')
items.forEach((el) => {
  const minPerSlide = 6
  let next = el.nextElementSibling
  for (var i = 1; i < minPerSlide; i++) {
    if (!next) {
      next = items[0]
    }
    let cloneChild = next.cloneNode(true)
    el.appendChild(cloneChild.children[0])
    next = next.nextElementSibling
  }
})
$('#edit_profile_form_picture').on('change', function () {
  if (this.files && this.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#img_preview').attr('src', e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  }
});
$('#formation_form_image').on('change', function () {
  if (this.files && this.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#img_preview').attr('src', e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  }
});

/* admin edit photo formation*/
var btnadmineditformation=$('#formation_form_admin_edit_image');
btnadmineditformation.hide();
$('#uploadimgAdminEditformation').on('click',function(){
  btnadmineditformation.trigger('click');
});
btnadmineditformation.on('change', function () {
  if (this.files && this.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#img_preview').attr('src', e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  }
});

/* teacher edit photo formation*/
var btn=$('#formation_form_edit_image');
btn.hide();
$('#uploadimgEditformation').on('click',function(){
  btn.trigger('click');
});
btn.on('change', function () {
  if (this.files && this.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#img_preview').attr('src', e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  }
});


/* teacher add photo formation */

var btnphoto=$('#formation_form_image');
btnphoto.hide();
$('#img_preview_new').hide();
$('#uploadimgformation').on('click',function(){
  btnphoto.trigger('click');
});
btnphoto.on('change', function () {
  if (this.files && this.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#img_preview_new').attr('src', e.target.result);
      $('#img_preview_new').show();
    };
    reader.readAsDataURL(this.files[0]);
  }
});

/* admin change document */
var btneditdoc=$('#formation_form_admin_edit_document');
btneditdoc.hide();
$('#uploaddocAdminEditformation').on('click',function(){
  btneditdoc.trigger('click');
});
btneditdoc.on('change', function () {
  if (this.files && this.files[0]) {
    var fsize=this.files[0].size;
    fsize=fsize / 1024;
    $('#selectedfile').html('<svg width="20" height="20" fill="#00c9a7" class="bi bi-check2 mb-1" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>'
    +'Vous avez selectionnez la fichier <b>' + this.files[0].name + ' </b>de taille '+ Math.round(fsize) +'Ko');
  }
});

/* teacher change document */
var btnteachereditdoc=$('#formation_form_edit_document');
btnteachereditdoc.hide();
$('#uploaddocEditformation').on('click',function(){
  btnteachereditdoc.trigger('click');
});
btnteachereditdoc.on('change', function () {
  if (this.files && this.files[0]) {
    var fsize=this.files[0].size;
    fsize=fsize / 1024;
    $('#selectedfile').html('<svg width="20" height="20" fill="#00c9a7" class="bi bi-check2 mb-1" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>'
    +'Vous avez selectionnez la fichier <b>' + this.files[0].name + ' </b>de taille '+ Math.round(fsize) +'Ko');
  }
});

/* teacher upload new document */
var btnteacheradddoc=$('#formation_form_document');
btnteacheradddoc.hide();
$('#uploaddocNewformation').on('click',function(){
  btnteacheradddoc.trigger('click');
});
btnteacheradddoc.on('change', function () {
  if (this.files && this.files[0]) {
    var fsize=this.files[0].size;
    fsize=fsize / 1024;
    $('#selectedfile').html('<svg width="20" height="20" fill="#00c9a7" class="bi bi-check2 mb-1" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>'
    +'Vous avez selectionnez la fichier <b>' + this.files[0].name + ' </b>de taille '+ Math.round(fsize) +'Ko');
  }
});

$('#user_form_picture').on('change', function () {
  if (this.files && this.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#img_preview').attr('src', e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  }
});

$('#edit_user_form_picture').on('change', function () {
  if (this.files && this.files[0]) {
    var reader = new FileReader();
    reader.onload = function (e) {
      $('#img_preview').attr('src', e.target.result);
    };
    reader.readAsDataURL(this.files[0]);
  }
});

$('.formation-autocomplete').each(function () {
  $(this).autocomplete({
    minLength: 2,
    search  : function(){$(this).addClass('loading-autocomplete');},
    open    : function(){$(this).removeClass('loading-autocomplete');},
    source: function (request, cb) {
      $.ajax({
        url: '/search/formation/' + request.term
      }).then(function (data) {
        if(!data.length){
          var result = [
              {
                  label: 'Aucune resultat trouvée.', 
                  value: 'Aucune resultat trouvée.'
              }
          ];
          cb(result);
      }
      else{
        cb(data);
      }
      });
    },
    displayKey: 'label',
    select: function (event, ui) {
      if(ui.item.label!="Aucune resultat trouvée."){
        this.value=ui.item.label;
        window.location = '/description/'+ui.item.value;
      }
      else{
        event.preventDefault();
      }
      return false;
      },
      focus: function (event, ui) {
        event.preventDefault();
        this.value = ui.item.label;
    },
  }).data("ui-autocomplete")._renderItem = function( ul, item ) {
    let txt = String(item.label).replace(new RegExp(this.term, "gi"),"<span class='fw-bold'>$&</span>");
    return $("<li></li>")
        .data("ui-autocomplete-item", item)
        .append("<div>"+txt+"</div>")
        .appendTo(ul);
};
});

$('#onClick').on('click', function(){
  if ($('.clickable').hasClass("d-none")) {
    $(".clickable").removeClass("d-none");
    $(".clickable").addClass("d-bloc");
  }
  else {
    $(".clickable").removeClass("d-bloc");
    $(".clickable").addClass("d-none");
  }
});

$('#onClick_skills').on('click', function(){
  if ($('.clickable_skills').hasClass("d-none")) {
    $(".clickable_skills").removeClass("d-none");
    $(".clickable_skills").addClass("d-bloc");
  }
  else {
    $(".clickable_skills").removeClass("d-bloc");
    $(".clickable_skills").addClass("d-none");
  }
});