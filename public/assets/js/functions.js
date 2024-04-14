const validateEmail = (email) => {
    return email.match(
      /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    );
  };
  
  const validate = () => {
    const $result = $('#result');
    const email = $('#email').val();
    const submit = $('#submit');
    $result.text('');
  
    if(validateEmail(email)){
      $result.text(email + ' is valid.');
      $result.css('color', 'green');
      submit.prop('disabled', false);
      submit.removeClass("disabled");
    } else{
      $result.text(email + ' is invalid.');
      $result.css('color', 'red');
      submit.prop('disabled', true);
      submit.addClass("disabled");

    }
    return false;
  }
  
  $('#email').on('input', validate);