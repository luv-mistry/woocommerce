<?php
/*
 * Plugin Name: Product Discount
 * Description: Discount for Product
 * Version: 1.0
 */

if(!defined('ABSPATH')){
    die("");
}

class SingleProductDiscount {
    function __construct(){
        add_action('woocommerce_product_options_general_product_data', array($this,'woocommerce_simple_product_discount'));
        add_action('woocommerce_process_product_meta', array($this,'woocommerce_simple_product_discount_save'));
        add_action( 'woocommerce_single_product_summary', array($this,'woocommerce_simple_product_discount_display'), 15 );
        add_action( 'woocommerce_new_product', array($this,'woocommerce_simple_product_discount_apply_on_price'), 10, 1 );
        add_action( 'woocommerce_update_product', array($this,'woocommerce_simple_product_discount_apply_on_price'), 10, 1 );



        add_action( 'woocommerce_variation_options_pricing', array($this,'woocommerce_variation_product_discount'), 10, 3 ); 
        add_action( 'woocommerce_save_product_variation', array($this,'woocommerce_variation_product_discount_save'), 10, 2 );
        add_action( 'woocommerce_new_product', array($this,'woocommerce_variation_product_discount_apply_on_price'), 10, 1 );
        add_action( 'woocommerce_update_product', array($this,'woocommerce_variation_product_discount_apply_on_price'), 10, 1 );
        add_filter( 'woocommerce_available_variation', array($this,'add_variation_custom_field_to_variable_form'));
        //add_action( 'woocommerce_product_additional_information', array($this,'add_html_container_to_display_selected_variation_custom_field') );
        //add_action( 'woocommerce_after_variations_form',array($this, 'display_selected_variation_custom_field_js') );




        add_action('admin_enqueue_scripts', array($this,'enqueue_custom_js'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_custom_validation_script'));






       

    }
    
    function activation(){
        //
    }
    function deactivation(){
        //
    }

    function woocommerce_simple_product_discount(){
        
        global $woocommerce, $post;
        echo '<div class="product_custom_field">';
        //
        woocommerce_wp_text_input(
            array(
                'id' => '_product_discount',
                'placeholder' => '',
                'label' => __('Discount (%)', 'woocommerce'),
                'type' => 'number',
                'custom_attributes' => array(
                    'step' => 'any', 
                    'min' => '0'
                )
                
                )
        );
        echo '</div>';//<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>';
      /*  ?>
        <script>
            $(document).ready(function(){
            $("#_product_discount").keyup(function(){
                var discount =  $("#_product_discount").val();
                consle.log(jQuery.type( discount ));
                if (discount < 0  ){
                    //document.getElementById("_product_discount_error_message").innerHTML = "Minimum persentage is 0% ";
                    alert("Minimum persentage is 0%");
                }
                else if (discount > 100 ){
                    //document.getElementById("_product_discount_error_messagev").innerHTML = "Maximum persentage is 100% ";
                    alert("Maximum persentage is 100%")
                }
                else {
                    //document.getElementById("_product_discount_error_messagev").innerHTML = "";
                }
                
            });
            
            });
        </script>
        <?php*/

        
    }

    function woocommerce_simple_product_discount_save($post_id)    {
    // Custom Product Number Field
        $woocommerce_product_discount = $_POST['_product_discount'];
        $product = wc_get_product($post_id);
        if (!empty($woocommerce_product_discount)){
            //update_post_meta($post_id, '_Woocommerce_discount', esc_attr($woocommerce_product_discount));
            $product->update_meta_data('_product_discount', sanitize_text_field($woocommerce_product_discount));
            $product->save();           

        }

    
    }

    function woocommerce_simple_product_discount_display(){
        $product = wc_get_product(get_the_ID());
        if ($product->get_type() == "simple"){
            $regularPrice = $product->get_regular_price();
            $discount = $product->get_meta('_product_discount');
            if (!empty($discount) && !empty($regularPrice) ) {
                echo "<p>Discount : ".$discount."%</p>";
        }
    }
        
    
    }
    function woocommerce_simple_product_discount_apply_on_price($product_id){
        $product = wc_get_product( $product_id );
        $regular_price =  $product->get_regular_price();
        $discount = $product->get_meta('_product_discount');
        if (!empty($regular_price) && !empty($discount)){
            $price = (float)$regular_price - ((float)$regular_price*(float)$discount / 100);
            $price = strval($price);
            //$product->update_meta_data('cc',sanitize_text_field($price));
            //$product->save();
            update_post_meta($product_id, '_price', esc_attr($price));

        }
    }


    function woocommerce_variation_product_discount( $loop, $variation_data, $variation ){

        echo '<div class="options_group form-row form-row-full">';

    
        woocommerce_wp_text_input(
            array(
                'id'          => '_product_discount[' . $loop . ']',
                'label'       => __( 'Discount (%)', 'woocommerce' ),
                'placeholder' => '',
                'desc_tip'    => true,
                'type' => 'number',
                'value' => get_post_meta( $variation->ID, '_product_discount', true )
            )
 	);
	echo '</div>';


    }


    function woocommerce_variation_product_discount_save( $variation_id, $loop ){

        // Text Field
        $woocommerce_product_discount = $_POST['_product_discount'][ $loop ];
        if (!empty($woocommerce_product_discount)){
            update_post_meta( $variation_id, '_product_discount', esc_attr( $woocommerce_product_discount ) );
        }

    }



    function woocommerce_variation_product_discount_apply_on_price ($product_id){
        $product = wc_get_product( $product_id );
        $variations = $product->get_children();
        foreach($variations as $variation){
            $product_variation = wc_get_product($variation); 
            $regular_price =  $product_variation->get_regular_price();
            $discount = $product_variation->get_meta('_product_discount');
            if (!empty($regular_price) && !empty($discount)){
                $price = (float)$regular_price - ((float)$regular_price*(float)$discount / 100);
                $price = strval($price);
                
                update_post_meta($product_variation->get_id(), '_price', esc_attr($price));
                update_post_meta($product_variation->get_id(), '_sale_price', esc_attr($price));
                

            }
        }  
        
    }

    function add_variation_custom_field_to_variable_form( $variation_data ) {
        $variation_data['product_discount'] = '<div class="woocommerce_product_discount"> Discount (%) : <span>'.get_post_meta( $variation_data['variation_id'], '_product_discount',true ).'</span></div>';
    
        return $variation_data;
    }



    function enqueue_custom_js() {
        // Replace 'your-custom-script' with a unique handle for your script
        wp_enqueue_script('your-custom-script', plugin_dir_url( __FILE__ ). 'custom-script.js', array('jquery'), '1.0', true);
    }

    function enqueue_custom_validation_script() {
        // Replace 'your-custom-validation-script' with a unique handle for your script
        wp_enqueue_script('your-custom-validation-script',plugin_dir_url( __FILE__ ). 'custom-variation-validation.js', array('jquery'), '1.0', true);
      }
     // add_action('admin_enqueue_scripts', 'enqueue_custom_validation_script');




    

    


    

    

    
}

if (class_exists('SingleProductDiscount')){
    $discount = new SingleProductDiscount();
}


register_activation_hook(
	__FILE__,
	array($discount,'activation')
);

register_deactivation_hook(
	__FILE__,
	array($discount,'activation')
);


