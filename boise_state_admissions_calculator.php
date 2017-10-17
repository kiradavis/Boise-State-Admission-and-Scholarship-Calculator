<?php
/*
*	Plugin Name: Boise State Admission & Scholarship Calculators
*	Description: Plugin to handle the admission and scholarship calculators for admissions.boisestate.edu. [calc type=""] & [calc type="scholarship"]
*	Version: 0.2
*	Author: Kira Davis & David Lentz
*	Author URI: https://webguide.boisestate.edu/
*	Based on the impressive work at https://jsfiddle.net/ValentinH/954eve2L/
*	See https://github.com/angular-slider/angularjs-slider/blob/master/README.md for useful info on how to work with rz-slider 
*/

function my_assets() {
	// register AngularJS
	wp_register_script( 'angular-core', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.6.5/angular.min.js', array(), null, false );
	
	// register CSS
	wp_register_style( 'calc-css', plugins_url('style.css',__FILE__ ) );
	wp_register_style( 'slider-css', 'https://rawgit.com/rzajac/angularjs-slider/master/dist/rzslider.css' );
	
	// register scripts
	wp_register_script( 'calc-js', plugins_url('script.js',__FILE__ ) );
	wp_register_script( 'slider-js', 'https://rawgit.com/rzajac/angularjs-slider/master/dist/rzslider.js' );
	wp_register_script( 'bootstrap-js', 'https://cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.14.3/ui-bootstrap-tpls.js' );
	
	// enqueue everything
	wp_enqueue_script( 'angular-core' );
	wp_enqueue_style( 'calc-css' );
	wp_enqueue_style( 'slider-css' );
	wp_enqueue_script( 'calc-js' );
	wp_enqueue_script( 'slider-js' );
  	wp_enqueue_script( 'bootstrap-js' );
}

add_action( 'wp_enqueue_scripts', 'my_assets' );

function calculator_html($atts = array(), $content = null, $tag) {
    shortcode_atts(array(
        'type' => 'scholarship'
    ), $atts);
	
	$str .= '<div ng-app="calculatorApp" id="slider-body">';
		$str .= '<div ng-controller="calculatorCtrl" class="wrapper">';
			$str .= '<div class="row all-options">';
      			$str .= '<div class="one_fourth sliders" id="slider-gpa" style="height: 600px !important">';
					$str .= '<small>GPA</small><br>';	
					$str .= '<rzslider rz-slider-model="sliderGPA.value" rz-slider-options="sliderGPA.options"></rzslider>';
				$str .= '</div>';
				$str .= '<div class="one_fourth last sliders" id="slider1" style="height: 600px !important">';	
					$str .= '<small>SAT - ACT</small><br>';
					$str .= '<rzslider rz-slider-model="sliderScore.value" rz-slider-options="sliderScore.options"></rzslider><br>';
				$str .= '</div>';
				$str .= '<div class="one_half" id="slider-options">';
					$str .= 'Please use the scales on the left to select your GPA and the highest of either your SAT or ACT score.<br><br>';
					$str .= '<label class="field-title">GPA:</label><br>';
					$str .= '<input type="number" ng-model="sliderGPA.value" max="4" min="2" step="0.01"/><br>';
  					$str .= '<label class="field-title">SAT or ACT equivalent Score:</label><br>';
					$str .= '<input type="number" ng-model="sliderScore.value" max="36" min="11" /><br>';
					if ($atts['type'] == 'scholarship') {
						$str .= '<label class="field-title">Participating States:';
						$str .= '<select name="state" ng-model="state" >';
						$str .= '<option value="AK" selected="selected">Alaska</option>';
						$str .= '<option value="AZ">Arizona</option>';
						$str .= '<option value="CA">California</option>';
						$str .= '<option value="CO">Colorado</option>';
						$str .= '<option value="HI">Hawaii</option>';
						$str .= '<option value="MT">Montana</option>';
						$str .= '<option value="NV">Nevada</option>';
						$str .= '<option value="NM">New Mexico</option>';
						$str .= '<option value="ND">North Dakota</option>';
						$str .= '<option value="OR">Oregon</option>';
						$str .= '<option value="SD">South Dakota</option>';
						$str .= '<option value="UT">Utah</option>';
						$str .= '<option value="WA">Washington</option>';
						$str .= '<option value="WY">Wyoming</option>';
						$str .= '<option value="PT">US Pacific Territories and Freely Associated States</option>';
						$str .= '<option value="NA">My state is not listed</option>';
						$str .= '</select></label><br><br>';
						$str .= '<label>Are you a US Citizen or Permanent Resident?';
						$str .= '<input type="checkbox" ng-model="chkselect" ng-click="checkValidation()" checked="true">';
						$str .= '</label>';
						$str .= '<span ng-show="validationmsg">International Students are considered for the GEM nonresident scholarship program. You must submit all admission materials by the December 15th scholarship deadline to qualify for consideration.<br></span><br>';
						$str .= 'Press calculate after setting sliders to check your eligibility<br><br>';
						$str .= '<input type="button" value="Calculate" ng-click="nonresidentCalculate()"><br>';
						$str .= '<em>';
						$str .= '<span ng-show="I">' . get_option( 'message_I' ) . '</span>' 
                          		. '<span ng-show="II">' . get_option( 'message_II' ) . '</span>'
                          		. '<span ng-show="III">' . get_option( 'message_III' ) . '</span>' 
                          		. '<span ng-show="IV">' . get_option( 'message_IV' ) . '</span>';
						$str .= '</em>';
					} else { 
						$str .= '<br><input type="button" value="Calculate" ng-click="calculate()"><br>';
						//$str .= '<em>{{msg}}</em>';
						$str .= '<span ng-show="low">' . get_option( 'low_score' ) . '</span>';
						$str .= '<span ng-show="mid">' . get_option( 'mid_score' ) . '</span>';
						$str .= '<span ng-show="high">' . get_option( 'high_score' ) . '</span>';
					}
				$str .= '</div>'; // end one half
			$str .= '</div>'; // end options 
		$str .= '</div>'; // end controller
	$str .= '</div>'; // end app

	return $str;
}

add_shortcode('calc', 'calculator_html');

//-----------------------------------------------------
// Admin Page Settings
//-----------------------------------------------------
 
add_action('admin_menu', 'boise_state_ac_admin_settings');
add_action('admin_menu', 'boise_state_sc_admin_settings');

function boise_state_ac_admin_settings() {
	$parent_slug = 'options-general.php';
    $page_title = 'Boise State Admission Calculator Settings';
    $menu_title = 'Admission Calculator';
    $capability = 'edit_posts';
    $menu_slug = 'boise_state_ac_options';
    $function = 'boise_state_ac_admin_settings_page_display';
    // $icon_url = '';
    // $position = 80;

    //add_submenu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}

function boise_state_sc_admin_settings() {
	$parent_slug = 'options-general.php';
    $page_title = 'Boise State Scholarship Calculator Settings';
    $menu_title = 'Scholarship Calculator';
    $capability = 'edit_posts';
    $menu_slug = 'boise_state_sc_options';
    $function = 'boise_state_sc_admin_settings_page_display';

    //add_submenu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
	add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
}

//-----------------------------------------------------
// Settings Fields
//-----------------------------------------------------
/* Admission */
function low_score_field() { 
	$low_score = get_option( 'low_score' );
	echo wp_editor( $low_score, 'low_score', array('textarea_content' => 'low_score') );
}
function mid_score_field() { 
	$mid_score = get_option( 'mid_score' );
	echo wp_editor( $mid_score, 'mid_score', array('textarea_content' => 'mid_score') );
}
function high_score_field() { 
	$high_score = get_option( 'high_score' );
	echo wp_editor( $high_score, 'high_score', array('textarea_content' => 'high_score') );
}
/* Scholarship */
function message_I_field() { 
	$message_I = get_option( 'message_I' );
	echo wp_editor( $message_I, 'message_I', array('textarea_content' => 'message_I') );
}
function message_II_field() { 
	$message_II = get_option( 'message_II' );
	echo wp_editor( $message_II, 'message_II', array('textarea_content' => 'message_II') );
}
function  message_III_field() { 
	$message_III = get_option( 'message_III' );
	echo wp_editor( $message_III, 'message_III', array('textarea_content' => 'message_III') );
}
function  message_IV_field() { 
	$message_IV = get_option( 'message_IV' );
	echo wp_editor( $message_IV, 'message_IV', array('textarea_content' => 'message_IV') );
}

function boise_state_ac_display_theme_panel_fields() {
	add_settings_section("ac-section", "Admissions Calculator", null, "ac-options");
	add_settings_section("sc-section", "Scholarship Calculator", null, "sc-options");

	/* Admissions Settings */
	add_settings_field("low_score", "Low score message:", "low_score_field", "ac-options", "ac-section");
	add_settings_field("mid_score", "Middle score message:", "mid_score_field", "ac-options", "ac-section");
	add_settings_field("high_score", "High score message:", "high_score_field", "ac-options", "ac-section");
	
	/* Scholarship Settings */
	add_settings_field("message_I", "Message I (gpa or act too low: no scholarship)", "message_I_field", "sc-options", "sc-section");
	add_settings_field("message_II", "Message II (gpa and act in middle)", "message_II_field", "sc-options", "sc-section");
	add_settings_field("message_III", "Message III (gpa and act high: gem scholarship)", "message_III_field", "sc-options", "sc-section");
	add_settings_field("message_IV", "Message IV (non-wue: too low for gem)", "message_IV_field", "sc-options", "sc-section");

	/* Register All Settings */
	register_setting("ac-section", "low_score");
	register_setting("ac-section", "mid_score");
	register_setting("ac-section", "high_score");
	register_setting("sc-section", "message_I");
	register_setting("sc-section", "message_II");
	register_setting("sc-section", "message_III");
	register_setting("sc-section", "message_IV");
}

add_action("admin_init", "boise_state_ac_display_theme_panel_fields");

//-----------------------------------------------------
// Admin Page Display
//-----------------------------------------------------

function boise_state_ac_admin_settings_page_display() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die('You do not have sufficient permissions to access this page.');
	}
	?>
	<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	    <form method="post" action="options.php" enctype="multipart/form-data">
	        <?php
				settings_fields("ac-section");
				do_settings_sections("ac-options"); 			
				submit_button(); 
	        ?>          
		</form>
	</div>
	<?php
}

function boise_state_sc_admin_settings_page_display() {
	// check user capabilities
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die('You do not have sufficient permissions to access this page.');
	}
	?>
	<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
	    <form method="post" action="options.php" enctype="multipart/form-data">
	        <?php
				settings_fields("sc-section");
				do_settings_sections("sc-options"); 				
				submit_button(); 
	        ?>          
		</form>
	</div>
	<?php
}
