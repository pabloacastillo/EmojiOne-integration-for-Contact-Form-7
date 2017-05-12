<?php
/*
Plugin Name: EmojiOne integration for Contact Form 7
Description: <a href="https://www.emojione.com/emoji/v3" target="_blank">EmojiOne</a> integration for Contact Form 7. The best way to get better answers, is to ask better questions.
Author: Pablo Castillo
Author URI: http://pabloacastillo.me
Version: 1.0
*/


/**
 .d8888b. 88888888888888888888
d88P  Y88b888             d88P
888    888888            d88P
888       8888888       d88P
888       888        88888888
888    888888         d88P
Y88b  d88P888        d88P
 "Y8888P" 888       d88P


8888888888888b     d888 .d88888b.8888888888888 .d88888b. 888b    8888888888888
888       8888b   d8888d88P" "Y88b "88b  888  d88P" "Y88b8888b   888888
888       88888b.d88888888     888  888  888  888     88888888b  888888
8888888   888Y88888P888888     888  888  888  888     888888Y88b 8888888888
888       888 Y888P 888888     888  888  888  888     888888 Y88b888888
888       888  Y8P  888888     888  888  888  888     888888  Y88888888
888       888   "   888Y88b. .d88P  88P  888  Y88b. .d88P888   Y8888888
8888888888888       888 "Y88888P"   8888888888 "Y88888P" 888    Y8888888888888
                                  .d88P
                                .d88P"
                               888P"
*/

add_action('plugins_loaded', 'cf7_add_emojione_fields', 11);


function cf7_add_emojione_fields() {
	global $pagenow;
	if( class_exists('WPCF7_Shortcode') ) {
		wpcf7_add_shortcode( array( 'cf7emojione'), 'wpcf7_emojione_shortcode_handler', true );
	} else {
		//if($pagenow != 'plugins.php') { return; }
		add_action('admin_notices', 'cf7emojionefieldserror');

		function cf7emojionefieldserror() {
			$out = '<div class="error" id="messages"><p>';
			if(file_exists(WP_PLUGIN_DIR.'/contact-form-7/wp-contact-form-7.php')) {
				$out .= 'The Contact Form 7 is installed, but <strong>you must activate Contact Form 7</strong> below for the Location picker to work.';
			} else {
				$out .= 'The Contact Form 7 plugin must be installed for the EmojiOne Field to work. <a href="'.admin_url('plugin-install.php?tab=plugin-information&plugin=contact-form-7&from=plugins&TB_iframe=true&width=600&height=550').'" class="thickbox" title="Contact Form 7">Install Now.</a>';
			}
			$out .= '</p></div>';
			echo $out;
		}
	}
}


function wpcf7_emojione_shortcode_handler( $tag){

	include_once 'emojionearray.php';
	$tag = new WPCF7_FormTag( $tag );

	if ( empty( $tag->name ) ) {
		return '';
	}

	$values = (array) $tag->values;
	$labels = (array) $tag->labels;


 	$html = '';
 	foreach ($values as $k => $v) {
 		$img='//cdn.jsdelivr.net/emojione/assets/svg/'.$cf7emojioneshortcode_replace[$v][2].'.svg';
 		$html.= 	'<label class="cf7emojione">
 						<input type="radio" name="'.$tag->name.'" value="'.$v.'">
 						<img src="'.$img.'">
			 		</label>';
 	}

 	return $html;

}


if ( is_admin() ) {
	add_action( 'admin_init', 'wpcf7_add_tag_generator_emojione', 30 );
}

function wpcf7_add_tag_generator_emojione() {

	if( class_exists('WPCF7_TagGenerator') ) {

		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'cf7emojione',  'EmojiOne', 'wpcf7_tg_pane_cf7emojione' );
	}
}

function wpcf7_tg_pane_cf7emojione( $contact_form, $args = '' ) {

	$args = wp_parse_args( $args, array() );

	$description = __( "Generate a form tag for the emoji. For more details, see %s.", 'contact-form-7' );
	$desc_link = wpcf7_link( __( 'https://wordpress.org/plugins/contact-form-7-map/', 'contact-form-7' ), __( 'the plugin page on WordPress.org', 'contact-form-7' ), array('target' => '_blank' ) );
	?>
	<div class="control-box">
		<fieldset>
			<legend><?php printf( esc_html( $description ), $desc_link ); ?></legend>

			<table class="form-table">
				<tbody>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
						<td>
							<input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" />

							<fieldset>
								<legend class="screen-reader-text"><?php echo esc_html( __( 'Options', 'contact-form-7' ) ); ?></legend>
								<textarea name="values" class="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea>
								<label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><span class="description"><?php echo esc_html( __( "One option per line.", 'contact-form-7' ) ); ?></span></label><br />
								<label><input type="checkbox" name="label_first" class="option" /> <?php echo esc_html( __( 'Put a label first, a checkbox last', 'contact-form-7' ) ); ?></label><br />
								<label><input type="checkbox" name="use_label_element" class="option" /> <?php echo esc_html( __( 'Wrap each item with label element', 'contact-form-7' ) ); ?></label>
							</fieldset>

						</td>
					</tr>

				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="insert-box">
		<input type="text" name="cf7emojione" class="tag code" readonly="readonly" onfocus="this.select()" />

		<div class="submitbox">
			<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
		</div>

		<br class="clear" />

		<p class="description mail-tag"><label for="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>"><?php echo sprintf( esc_html( __( "To use the value input through this field in a mail field, you need to insert the corresponding mail-tag (%s) into the field on the Mail tab.", 'contact-form-7' ) ), '<strong><span class="mail-tag"></span></strong>' ); ?><input type="text" class="mail-tag code hidden" readonly="readonly" id="<?php echo esc_attr( $args['content'] . '-mailtag' ); ?>" /></label></p>
	</div>
	<?php
}




function cf7_emojione_theme_scripts() {

	// wp_enqueue_script( 'jquery' );
	// wp_enqueue_script('emojionejs', 'https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js',null,false,true);
	// wp_enqueue_script('emojionejshelper', plugin_dir_url(__FILE__).'helpers.js',null,false,true);

	wp_enqueue_style('emojionecss','https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css',null);
	wp_enqueue_style( 'cf7emojionecss', plugin_dir_url(__FILE__).'style.css', false, false );
}

add_action( 'wp_enqueue_scripts', 'cf7_emojione_theme_scripts' );



function cf7_emojione_admin_scripts(){
	wp_register_style( 'emojionecss', 'https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/assets/css/emojione.min.css?t='.(time()), false, false );
	wp_enqueue_style( 'emojionecss' );

	wp_register_style( 'cf7emojionecss', plugin_dir_url(__FILE__).'style.css', false, false );

	wp_enqueue_script('jquery_textcomplete', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.textcomplete/1.8.0/jquery.textcomplete.min.js', false );
	wp_enqueue_script('emojionejs', 'https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js', false );
	wp_enqueue_script('emojionejshelper', plugin_dir_url(__FILE__).'helpers.js?t='.time(), false );

}

add_action('admin_enqueue_scripts', 'cf7_emojione_admin_scripts');










































