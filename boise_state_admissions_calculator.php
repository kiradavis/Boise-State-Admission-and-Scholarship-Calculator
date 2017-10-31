<?php
/*
*	Plugin Name: Boise State Admission & Scholarship Calculators
*	Description: Plugin to handle the admission and scholarship calculators for admissions.boisestate.edu. [calc type=""] & [calc type="scholarship"]
*	Version: 0.3
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
	
	$lowDefault = "The GPA and test score combination you've provided is below the minimum requirement
	for admission to Boise State. There are, however, other avenues for attending Boise State in the future.
	You may choose to attend Boise State as a non-degree seeking student until you have satisfactorily completed
	at least 14 college-level credits, or you may attend another institution and re-apply to Boise State once
	you have completed 14 college-level transferable semester credits with a 2.25 GPA or better. If you would
	like to further discuss your options for attending Boise State, please contact the Admissions Office.";
	
	$midDefault = "Based on the information you provided, you are a potential candidate for admission. You 
	are encouraged to submit recommendations from faculty at your school or other adults who have worked
	with you in a classroom setting, and provide a personal statement describing your ability to be successful
	in a university setting. Your application will be reviewed by an Admissions Committee.";
	
	$highDefault = "Congratulations! Based on the information you provided, you will make an excellent addition
	to the Bronco Family! We look forward to seeing you on campus soon!";
	
	$oneDefault = "The GPA or ACT/SAT score you’ve provided is below the minimum requirement for the 
	<a href='http://financialaid.boisestate.edu/scholarships/non-resident-tuition-assistance-programs/' 
	target='_blank' title='Western Exchange Scholarship Program'>Western Exchange Scholarship Program</a>
	and/or <a href='http://financialaid.boisestate.edu/scholarships/non-resident-tuition-assistance-programs/' 
	target='_blank' title='GEM nonresident tuition scholarship'>GEM nonresident tuition scholarship</a>.
	Scholarship recipients must have a minimum 3.20 cumulative unweighted high school GPA and ACT composite
	21 or SAT critical reading and math combined 980 to qualify for consideration. If your GPA and/or test
	scores improve, you may resubmit them before the December 15th Non-Resident Priority Date.&nbsp;Be sure
	to review Boise State’s <a href='http://financialaid.boisestate.edu/scholarships/how-to-apply/' target='_blank'
	title='scholarship checklist'>scholarship checklist</a> to view all <strong>scholarship options</strong>.";
	
	$twoDefault = "Congratulations! Based on the information you provided, you will be awarded a&nbsp;
	<a href='http://financialaid.boisestate.edu/scholarships/non-resident-tuition-assistance-programs/' 
	target='_blank' title='Western Exchange Scholarship Program'>Western Undergraduate Exchange (WUE)
	Scholarship</a>! All admission materials must be received in Boise State Admissions by the December
	15th&nbsp;Non-Resident Priority Date to qualify for consideration.&nbsp;<strong>Be sure to review Boise
	State’s&nbsp;<a href='http://financialaid.boisestate.edu/scholarships/how-to-apply/' target='_blank' 
	title='scholarship checklist'>scholarship checklist</a>&nbsp;to view all scholarship options.</strong>";
	
	$threeDefault = "Congratulations! Based on the information you provided, you will
	be awarded a <a href='http://financialaid.boisestate.edu/scholarships/non-resident-tuition-assistance-programs/'
	target='_blank' title='GEM nonresident scholarship'>GEM nonresident scholarship</a>! All admission materials
	must be received in Boise State Admissions by the December 15th&nbsp; Non-Resident Priority Date to qualify
	for the award.&nbsp;<strong>Be sure to review Boise State’s&nbsp;
	<a href='http://financialaid.boisestate.edu/scholarships/how-to-apply/' target='_blank' 
	title='scholarship checklist'>scholarship checklist</a> to view all scholarship options.</strong>";
	
	$fourDefault = "The GPA or ACT/SAT score you’ve provided is below the minimum requirement for the&nbsp;
	<a href='http://financialaid.boisestate.edu/scholarships/non-resident-tuition-assistance-programs/' 
	target='_blank' title='GEM nonresident tuition scholarship'>GEM Scholarship</a> for residents. Scholarship
	recipients must have a minimum 3.60 cumulative, unweighted high school GPA and ACT composite 26 or SAT
	critical reading and math combined 1170 to qualify for consideration. If your GPA and/or test scores improve,
	you may resubmit them before the December 15th Non-Resident Priority Date.&nbsp;<strong>Be sure to review Boise
	State’s&nbsp;<a href='http://financialaid.boisestate.edu/scholarships/how-to-apply/' target='_blank' 
	title='scholarship checklist'>scholarship checklist</a>&nbsp;to view all scholarship options.</strong>";
	
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
				$str .= '<form legend="Admissions Calculator" class="one_half_last" id="slider-options">';
					$str .= 'Please use the scales on the left to select your GPA and the highest of either your SAT or ACT score.<br><br>';
					$str .= '<label class="field-title" id="gpa">GPA:</label><br>';
					$str .= '<input aria-labelledby="gpa" ng-model="sliderGPA.value" step="0.01"/><br>';
  					$str .= '<label class="field-title" id="sat-or-act">SAT or ACT equivalent Score:</label><br>';
					$str .= '<input type="number" aria-labelledby="sat-or-act" ng-model="sliderScore.value" max="36" min="11" /><br>';
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
						$str .= '<input type="submit" value="Calculate" ng-click="nonresidentCalculate()"><br>';
						$str .= '<em>';
						$str .= '<span ng-show="I">' . get_option( 'message_I', $oneDefault ) . '</span>' 
                          		. '<span ng-show="II">' . get_option( 'message_II', $twoDefault ) . '</span>'
                          		. '<span ng-show="III">' . get_option( 'message_III', $threeDefault ) . '</span>' 
                          		. '<span ng-show="IV">' . get_option( 'message_IV', $fourDefault ) . '</span>';
						$str .= '</em>';
					} else { 
						$str .= '<br><input type="submit" value="Calculate" ng-click="calculate()"><br>';
						//$str .= '<em>{{msg}}</em>';
						$str .= '<span ng-show="low">' . get_option( 'low_score', $lowDefault ) . '</span>';
						$str .= '<span ng-show="mid">' . get_option( 'mid_score', $midDefault ) . '</span>';
						$str .= '<span ng-show="high">' . get_option( 'high_score', $highDefault ) . '</span>';
					}
				$str .= '</form>'; // end one half
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
