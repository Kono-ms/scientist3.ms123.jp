$(document).ready(function() {
  show_authentication_mask();
  show_authentication_form();
});

function show_authentication_mask() {
  var html = ''
    + '<style>'
    + '.authentication_mask {'
    + '  position: fixed;'
    + '  left: 0px;'
    + '  top: -8rem;'
    + '  width: 200%;'
    + '  height: 200%;'
    + '  background: #808080;'
    + '  opacity: 0.75;'
    + '  z-index: 1000;'
    + '}'
    + '</style>'
    + '<div class="authentication_mask"></div>'
    + '';
  $('body').append(html);

}

function show_authentication_form() {

  var html = ''
    + '<style>'
    + '.authentication_form {'
    + '  position: fixed;'
    + '  left: calc(50% - 300px);'
    + '  top: calc(50% - 200px);'
    + '  width: 600px;'
    + '  height: 400px;'
    + '  z-index: 1001;'
    + '}'
    + '.formset__ttl {'
    + '  width: 100px;'
    + '}'
    + '</style>'
    + '          <div class="authentication_form formColumn__inner formColumn__inner-67">'
    + '              <div class="formset">'
    + '                <div class="">Administrators only</div>'
    + '                <div class="formset__item">'
    + '                  <div class="formset__ttl"><span>'
    + '                      <label for="login_email">ID</label>'
    + '                    </span></div>'
    + '                  <div class="formset__input"><input type="text" name="authentication_id" placeholder="" required />'
    + '                  </div>'
    + '                </div>'
    + '                <div class="formset__item">'
    + '                  <div class="formset__ttl"><span>'
    + '                      <label for="login_password">Password</label>'
    + '                    </span></div>'
    + '                  <div class="formset__input"><input type="password" name="authentication_pass" placeholder="" required />'
    + '                  </div>'
    + '                </div>'
    + '                <div class="formset__btn formset__btn--supplier">'
    + '                  <button type="button" onclick="check_authentication();">Login</button>'
    + '                </div>'
    + '              </div>'
    + '          </div>'
    +'';
  $('body').append(html);
}

function hide_authentication() {
  //$('.authentication_mask').hide();
  //$('.authentication_form').hide();
  location.reload();
}

function check_authentication() {
  var id = $('[name=authentication_id]').val();
  var pass = $('[name=authentication_pass]').val();
  console.log(id);
  console.log(pass);
  $.ajax({
    'url': '/ajax_authentication.php',
    'type': 'post',
    'dataType': 'text',
    'data': {
      'id': id,
      'pass': pass,
    },
  }).done(function(res) {
    console.log(res);
    if(res == 'ok') {
      hide_authentication();
    }
    else if(res == 'ng') {
      alert('管理者IDまたはパスワードが正しくありません')
    }
    else if(res == 'lock') {
      alert('ロックされました。しばらくたってから再度お試しください。')
    }
    else if(res == 'lock2') {
      alert('ロックされています。しばらくたってから再度お試しください。')
    }
  });

}