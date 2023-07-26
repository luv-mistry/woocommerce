jQuery(document).ready(function($) {
    // Replace 'your-custom-field' with the ID or class of your custom field
    $('#_product_discount').on('keyup', function() {
        var value = $(this).val();

        console.log(value);
        // Add your custom JavaScript functionality based on the field's value
        // For example, update other elements on the page, show/hide sections, etc.
        if (value > 100) {
            alert ('Maximum Discount is 100%');

        }else if (value < 0 ) {
            alert ('Minimum Discount is  0%');
        }

    });
});