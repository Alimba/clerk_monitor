 $(document).ready(function(){
      $('#myInput').click(function(){
          $('#n_keypad').fadeToggle('fast');
      });
      $('.done').click(function(){
          $('#n_keypad').hide('fast');
      });
      $('.numero').click(function(){
        if (!isNaN($('#myInput').val())) {
           if (parseInt($('#myInput').val()) == 0) {
             $('#myInput').val($(this).text());
           } else {
             $('#myInput').val($('#myInput').val() + $(this).text());
           }
        }
      });
      $('.neg').click(function(){
          if (!isNaN($('#myInput').val()) && $('#myInput').val().length > 0) {
            if (parseInt($('#myInput').val()) > 0) {
              $('#myInput').val(parseInt($('#myInput').val()) - 1);
            }
          }
      });
      $('.pos').click(function(){
          if (!isNaN($('#myInput').val()) && $('#myInput').val().length > 0) {
            $('#myInput').val(parseInt($('#myInput').val()) + 1);
          }
      });
      $('.del').click(function(){
          $('#myInput').val($('#myInput').val().substring(0,$('#myInput').val().length - 1));
      });
      $('.clear').click(function(){
          $('#myInput').val('');
      });
      $('.zero').click(function(){
        if (!isNaN($('#myInput').val())) {
          if (parseInt($('#myInput').val()) != 0) {
            $('#myInput').val($('#myInput').val() + $(this).text());
          }
        }
      });
    });