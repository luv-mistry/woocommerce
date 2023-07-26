jQuery(document).ready(function($) {
    // Function to validate the custom field for variations on keyup
    function validateVariationCustomFieldOnKeyup() {
      // Replace 'your_custom_field_name' with the actual name of your custom field
      var $customFieldInputs = $('input[name^="_product_discount"]');
  
      // Loop through each variation row and check the custom field value
      $customFieldInputs.each(function() {
        var $customFieldInput = $(this);
        var fieldValue = $customFieldInput.val().trim();
        //console.log(fieldValue);
  
        // Perform your custom validation here
        if (Number(fieldValue ) > 100) {
          // Display an error alert message
          alert('Maximum Discount is 100%');
        }
        else if (Number(fieldValue ) < 0){
            alert('Minimum Discount is  0%')
        }
      });
    }
  
    // Hook into the keyup event of the custom field input for variations
    $(document).on('keyup', 'input[name^="_product_discount"]', function() {
      validateVariationCustomFieldOnKeyup();
    });
  });