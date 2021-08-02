(function ($, Drupal, drupalSettings){
  'use strict';
  Drupal.behaviors.myBehavior = {
    attach: function (context, settings) {
      $('#edit-adopted-pet', context).once('myBehavior').each(function (){
        $(this).change(function (){
          let  pet_id = $(this).children("option:selected").val();
          $.ajax({
            url: '/take_a_pet/change_photo',
            type: 'POST',
            data: ({pet_id: pet_id}),
            dataType: 'JSON',
            success: function (data){
              $('#pet_image').attr('src', data['path']);
              $('#pet_name').text("Name: " + data['pet_name']);
              $('#pet_age').text("Name: " + data['pet_age']);
              $('#foundation_name').text("Foundation Name: " + data['foundation_name']);
              $('#foundation_email').text("Foundation Email: " + data['foundation_email']);
            }
          });
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings)
