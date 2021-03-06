<?php
/*
Plugin Name: WP Paintbrush Fonts
Plugin URI: http://wppaintbrush.com/
Description: Adds ability to use non web-safe fonts to the WP Paintbrush theme
Author: Ryan Hellyer / WP Paintbrush
Version: 1.0.1
Author URI: http://wppaintbrush.com/
*/


/**
 * Do not continue processing if WP Paintbrush not already loaded
 * @since 0.1
 */
if ( !defined( 'WPPB_SETTINGS' ) )
	return;

/**
 * Define some constants
 * @since 0.1
 */
define( 'WPPB_FONTS_DIR', dirname( __FILE__ ) . '/' ); // Plugin folder DIR
define( 'WPPB_FONTS_URL', WP_PLUGIN_URL . '/' . basename( WPPB_FONTS_DIR )  . '/' ); // Plugin folder URL

/**
 * List of free fonts
 * @since 0.1
 */
function wppb_free_fonts() {
	$fonts = array(
		'Architects Daughter',
		'Astloch',
		'Cousine',
		'Crafty Girls',
		'Indie Flower',
		'Kenia',
		'Lobster',
		'Miltonian',
		'Radley',
		'Six Caps',
		'Sniglet',
		'Special Elite',
		'UnifrakturMaguntia',
		'VT323',
	);
	return $fonts;
}

/**
 * Load up the menu pages
 * @since 0.1
 */
function wppb_fonts_options_add_page() {

	// Edit template admin page
	$page = add_theme_page(
		__( 'Fonts', 'wppb_theme_editor' ), 
		__( 'Fonts', 'wppb_theme_editor' ), 
		'edit_theme_options', 
		'fonts',
		'wppb_fonts_do_page' 
	);
	add_action( 'admin_print_scripts-' . $page, 'wppb_fonts_thickbox_scripts' ); // Add thickbox scripts
	add_action( 'admin_print_styles-' . $page, 'wppb_fonts_thickbox_styles' ); // Add thickbox styles
	add_action( 'admin_print_styles-' . $page, 'wppb_settings_admin_styles' ); // Add styles (only for this admin page)
}
add_action( 'admin_menu', 'wppb_fonts_options_add_page' ); // Creat admin page

/**
 * Init options to white list our options
 * @since 0.1
 */
function wppb_fonts_settings_options_init(){

	// Register settings
	register_setting( 'wppb_fonts_options', 'wppb_fonts', 'wppb_fonts_options_validate' );

}
add_action( 'admin_init', 'wppb_fonts_settings_options_init' );

/**
 * Validate options before submission to database
 * @since 0.1
 */
function wppb_fonts_options_validate( $input ) {

	// Process each option individually
	foreach( $input as $font=>$option ) {

		// Only process free fonts
		if ( !function_exists( 'wppb_fonts_pro' ) ) {
			foreach( wppb_free_fonts() as $free_font ) {
				echo $input[$font] . ' ' . $font . ' = ' . $free_font . '<br>';
				if ( $font == 'fontembed_' . $free_font ) {
					$output[$font] = $input[$font];
				}
			}
		}

		// Process all fonts (Pro members)
		else
			$output = $input;

		// Explicitly checking for possible options
		if ( $option != 'on' || $option != 'off' || $option != '' )
			$output[$option] = '';
	}

	return $output;
}

/**
 * Create the options page
 * @since 0.1
 */
function wppb_fonts_do_page() {
	?>
	<div class="wrap">
		<?php
			// Create screen icon by heading
			screen_icon( 'wppb-icon' ); echo '<h2>' . __( 'Embed fonts', 'wppb_theme_editor' ) . '</h2>';

			// "Options Saved" message as displayed at top of page on clicking "Save"
			if ( isset( $_REQUEST['updated'] ) )
				echo '<div class="updated fade"><p><strong>' . __( 'Options saved', 'wppb_theme_editor' ) . '</strong></p></div>';
		?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'wppb_fonts_options' ); // Create various hidden fields (includes security nonces etc.)
			?>

			<h3><?php _e( 'Embed fonts', 'wppb_theme_editor' ); ?></h3>
			<p><?php _e( 'You may embed extra fonts here. Fonts can be selected from the list of available fonts in the design editor or used manually via CSS.', 'wppb_theme_editor' ); ?></p>
			<p><?php _e( 'Note: It is best to only embed 2-3 fonts maximum, as the more variants you use, the slower your page loads will be.', 'wppb_theme_editor' ); ?></p>
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
			</p>
			<table class="widefat" style="width: auto;">
				<thead>
					<tr>
						<th style="width: 160px;">
							<label><?php _e( 'Font name', 'wppb_theme_editor' ); ?></label>
						</th>
						<th style="width: 70px;">
							<label><?php _e( 'Embed?', 'wppb_theme_editor' ); ?></label>
						</th>
						<th style="width: 500px;">
							<label><?php _e( 'Description', 'wppb_theme_editor' ); ?></label>
						</th>
					</tr>
				</head>
				<tbody class="plugins"> 
					<?php
					foreach ( wppb_embeddable_fonts() as $font => $details ) {
						if ( !function_exists( 'wppb_fonts_pro' ) ) :
						?>
						<style type="text/css">
							tr td {color: #ccc;}
							tr td a {color: #ccc;}
							tr.free td {color: #333;}
							tr.free td a {color: #21759B;}
							.buy-now {font-weight: bold; color: #21759B;}
						</style>
						<?php
						$pro_class = 'pro';
						endif;
						foreach( wppb_free_fonts() as $free_font ) {
							if ( $free_font == $font )
								$pro_class = 'free';
						}

						if ( !isset( $pro_class ) )
							$pro_class = '';
						if ( !isset( $fonts_list ) )
							$fonts_list = '';
						if ( !isset( $fonts_list[$pro_class] ) )
							$fonts_list[$pro_class] = '';
						$fonts_list[$pro_class] .= '
					<tr class="' . $pro_class . '">
						<td>
							<label>
								<a href="' . get_home_url() . '/?wppb_font_test=' . $font . '" class="thickbox">
									' . $font . '
								</a>
							</label>
						</td>
						<td>';

							if ( 'pro' == $pro_class )
								$fonts_list[$pro_class] .= '<a class="buy-now" href="http://wppaintbrush.com/fonts_will_be_for_sale_from_this_url_or_a_similar_one_in_the_future">Buy now</a>';
							else {
								$fonts_list[$pro_class] .= '<input type="checkbox" name="wppb_fonts[fontembed_' . $font . ']" value="on" ';
								if ( 'on' == get_wppb_font_option( 'fontembed_' . $font ) ) {$fonts_list[$pro_class] .= 'checked="checked" ';} 
								$fonts_list[$pro_class] .= '/>';
							}
						$fonts_list[$pro_class] .= '
						</td>
						<td class="hard-crop">
							' . $details['description'] . '
						</td>
					</tr>';

				// End load thumbnail settings
				}

				if ( !isset( $fonts_list['free'] ) )
					$fonts_list['free'] = '';
				if ( !isset( $fonts_list['pro'] ) )
					$fonts_list['pro'] = '';
				echo $fonts_list['free']; // List free fonts
				echo $fonts_list['pro']; // List pro fonts
				?>
				</tbody>
			</table>

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e( 'Save Options' ); ?>" />
			</p>
		</form>
	</div>

	<?php
}

/**
 * Load font test page
 * Used in admin panel to demo embeddable fonts
 * @since 0.1
 */
if ( isset( $_GET['wppb_font_test'] ) )
	require( 'thickbox.php' );

/**
 * List of fonts which may be embedded
 * @since 0.1
 */
function wppb_embeddable_fonts() {
	$fonts = array(
		/* Internally hosted fonts 
		'ChunkFive'                  => array(
			'description'     => 'Chunk is an ultra-bold slab serif typeface that is reminiscent of old American Western woodcuts, broadsides, and newspaper headlines. Used mainly for display, the fat block lettering is unreserved yet refined for contemporary use.',
			'url'             => 'WPPB_INTERNAL_FONT_/ChunkFive/stylesheet.css',
			'family'          => 'ChunkFiveRegular',
		),
		*/
		/* Google fonts */
		'Allan'	=> array(
			'description'     => 'Once Allan was a sign painter in Berlin. (Gray paneling work in subway, bad materials, city split in two). Now things have changed. His (character) palette of activities have expanded tremendously: he happily spends time traveling, experimenting in gastronomic field, all sorts of festivities are not alien to him. He always comes with alternate features, and hints. Typeface suited for bigger size display use. Truly a type that you like to see more!',
			'url'             => 'http://fonts.googleapis.com/css?family=Allan:bold',
			'family'          => 'Allan',
		),
		'Anonymous Pro'	=> array(
			'description'     => 'Anonymous Pro is a family of four fixed-width fonts designed especially with coding in mind. Characters that could be mistaken for one another (O, 0, I, l, 1, etc.) have distinct shapes to make them easier to tell apart in the context of source code. Anonymous Pro also features an international, Unicode-based character set, with support for most Western and European Latin-based languages, Greek, and Cyrillic. It also includes special "box drawing" characters for those who need them.',
			'url'             => 'http://fonts.googleapis.com/css?family=Anonymous+Pro:regular,italic,bold,bolditalic',
			'family'          => 'Anonymous Pro',
		),
		'Annie Use Your Telescope'	=> array(
			'description'     => 'This is a favorite of mine. A talented photography student I know was writing something down and I squealed and ran over to beg her for a sample of her writing. It was worth the effort, as I adore her style and feel it translated perfectly into a cute font. She named the font as a nod to one of her favorite bands, Jack’s Mannequin.',
			'url'             => 'http://fonts.googleapis.com/css?family=Annie+Use+Your+Telescop',
			'family'          => 'Annie Use Your Telescope',
		),
		'Architects Daughter'	=> array(
			'description'     => 'This font incorporates the graphic, squared look of architectural writing, combined with the natural feel of daily handwriting.',
			'url'             => 'http://fonts.googleapis.com/css?family=Architects+Daughter',
			'family'          => 'Architects Daughter',
		),
		'Arimo'                      => array(
			'description'     => 'Arimo was designed by Steve Matteson as an innovative, refreshing sans serif design',
			'url'             => 'http://fonts.googleapis.com/css?family=Arimo:regular,italic,bold,bolditalic&subset=latin',
			'family'          => 'Arimo',
		),
		'Arvo'                       => array(
			'description'     => 'Arvo is a geometric slab-serif typeface family suited for screen and print',
			'url'             => 'http://fonts.googleapis.com/css?family=Arvo:regular,italic,bold,bolditalic&subset=latin',
			'family'          => 'Arvo',
		),
		'Astloch'	=> array(
			'description'     => 'Astloch is a set of monolinear display faces — one delicate, one sturdy — based on the mix of sharp angles and florid curves found in fraktur lettering.',
			'url'             => 'http://fonts.googleapis.com/css?family=Astloch:regular,bold',
			'family'          => 'Astloch',
		),
		'Bangers'	=> array(
			'description'     => 'Bangers is a comicbook style font which packs a punch! It was designed in the style of mid-20th century superhero comics cover lettering in mind.',
			'url'             => 'http://fonts.googleapis.com/css?family=Bangers',
			'family'          => 'Bangers',
		),
		'Bevan'	=> array(
			'description'     => "Bevan is a reworking of a traditional slab serif display typeface created by Heinrich Jost in the 1930s. In Bevan, Jost's earlier letter forms have been digitised and then reshaped for use as a webfont, the counters have been opened up a little and the stems optimised for use as bold display font in modern web browsers.",
			'url'             => 'http://fonts.googleapis.com/css?family=Bevan',
			'family'          => 'Bevan',
		),
		'Buda'	=> array(
			'description'     => 'Against the typographical grey, Buda is a black and white typeface. The letters are shared by two contrasting weights, which clash and balance each other out. This typeface is inspired by Budapest, a paradoxal town, beautiful and ugly, fragile and exuberant. Like this town, the typeface Buda is an assemblage of heterogeneous elements which form an harmony.',
			'url'             => 'http://fonts.googleapis.com/css?family=Buda:light',
			'family'          => 'Buda',
		),
		'Cabin'	=> array(
			'description'     => 'The Cabin font family is a humanist sans with 4 weights and true italics, inspired by Edward Johnston’s and Eric Gill’s typefaces, with a touch of modernism. Cabin incorporates modern proportions, optical adjustments, and some elements of the geometric sans.',
			'url'             => 'http://fonts.googleapis.com/css?family=Cabin:regular,regularitalic,bold,bolditalic',
			'family'          => 'Cabin',
		),
		'Cabin Sketch'	=> array(
			'description'     => 'The Cabin Font is a humanist sans inspired by Edward Johnston’s and Eric Gill’s typefaces, with a touch of modernism. Cabin incorporates modern proportions, optical adjustments, and some elements of the geometric sans. This is the Sketch version, with the texture of a teenage doodle.',
			'url'             => 'http://fonts.googleapis.com/css?family=Cabin+Sketch:bold',
			'family'          => 'Cabin Sketch',
		),
		'Cantarell'                  => array(
			'description'     => 'The Cantarell typeface was designed as a contemporary Humanist sans serif',
			'url'             => 'http://fonts.googleapis.com/css?family=Cantarell:regular,italic,bold,bolditalic&subset=latin',
			'family'          => 'Cantarell',
		),
		'Cardo'                      => array(
			'description'     => 'Cardo is a large Unicode font specifically designed for the needs of classicists, Biblical scholars, medievalists, and linguists',
			'url'             => 'http://fonts.googleapis.com/css?family=Cardo&subset=latin',
			'family'          => 'Cardo',
		),
		'Cousine'                    => array(
			'description'     => 'Cousine was designed by Steve Matteson as an innovative, refreshing sans serif design that is metrically compatible with Courier New™',
			'url'             => 'http://fonts.googleapis.com/css?family=Cousine:regular,italic,bold,bolditalic&subset=latin',
			'family'          => 'Cousine',
		),
		'Crafty Girls'	=> array(
			'description'     => "Inspired by crochet hooks, yarn, grandma's button box, thread, glitter and crafty girls hands everywhere! This delightfully playful casual handwriting script font was hand drawn by the Tart Workshop's own resident lettering artist and crafty guru Crystal Kluge and makes the perfect compliment to all your projects! Use it in your very best creative projects, then blog about them in the same font!",
			'url'             => 'http://fonts.googleapis.com/css?family=Crafty+Girls',
			'family'          => 'Crafty Girls',
		),
		'Crimson Text'               => array(
			'description'     => 'Crimson Text was designed as a very traditional, garalde-ish book font',
			'url'             => 'http://fonts.googleapis.com/css?family=Crimson+Text&subset=latin',
			'family'          => 'Crimson Text',
		),
		'Cuprum'                     => array(
			'description'     => 'Cuprum is a versatile, narrow grotesque font',
			'url'             => 'http://fonts.googleapis.com/css?family=Cuprum&subset=latin',
			'family'          => 'Cuprum',
		),
		'Dawning of a New Day'	=> array(
			'description'     => 'Dawning of a New Day is based on the handwriting of a friend. The title was chosen by my friend, but also had great meaning to me. I like to think of each new day as a chance to start fresh, free from past mistakes. It is my desire that the light, fluid motions of this script mimic that hopeful feeling.',
			'url'             => 'http://fonts.googleapis.com/css?family=Dawning+of+a+New+Day',
			'family'          => 'Dawning of a New Day',
		),
		'Droid Sans'                 => array(
			'description'     => 'Droid Sans is a humanist sans serif typeface designed by Steve Matteson, Type Director of Ascender Corp. Droid Sans was designed with an upright stress, open forms and a neutral, yet friendly appearance.',
			'url'             => 'http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold&subset=latin',
			'family'          => '',
		),
		'Droid Sans Mono'            => array(
			'description'     => 'Droid Sans Mono is a fixed width version of Droid Sans. The Droid Sans Mono typefaces were designed by Steve Matteson of Ascender Corp. The Droid Sans Mono fonts feature non-proportional spacing for displaying text in a tabular setting and other uses where a monospaced font is desired.',
			'url'             => 'http://fonts.googleapis.com/css?family=Droid+Sans+Mono&subset=latin',
			'family'          => 'Droid Sans Mono',
		),
		'Droid Serif'                => array(
			'description'     => 'The Droid Serif font family features a contemporary appearance and was designed for comfortable reading on screen. Designed by Steve Matteson, Type Director of Ascender Corp, Droid Serif features slightly condensed letterforms to maximize the amount of text displayed on small screens.',
			'url'             => 'http://fonts.googleapis.com/css?family=Droid+Serif:regular,italic,bold,bolditalic&subset=latin',
			'family'          => 'Droid Serif',
		),
		'Expletus Sans'	=> array(
			'description'     => "Expletus Sans is a display typeface, which means that it is not recommended for long pieces of text. However, it's very effective for setting headers and other large sized text, due to it's way of pulling in the reader. It comes in 4 weights and will include italics from May 2011. It's character set supports most European languages, but if you do wish to have several extra glyphs made to support your language, please don't hesitate to send an email to jasper@designtown.nl",
			'url'             => 'http://fonts.googleapis.com/css?family=Expletus+Sans:regular,500,600,bol',
			'family'          => 'Expletus Sans',
		),
		'Geo'	=> array(
			'description'     => 'Geo expresses both the directness of some of the 1920s faces and the rather disingenuous consumerist thrust of the 1980s and 1990s descendants of theirs.',
			'url'             => 'http://fonts.googleapis.com/css?family=Geo',
			'family'          => 'Geo',
		),
		'GFS Didot'	=> array(
			'description'     => 'DESCRIPTION COMING SOON!',
			'url'             => 'WPPB_INTERNAL_FONT',
			'family'          => 'GFS Didot',
		),
		'GFS Neohellenic'            => array(
			'description'     => 'DESCRIPTION COMING SOON!',
			'url'             => 'WPPB_INTERNAL_FONT',
			'family'          => 'GFS Neohellenic',
		),
		'Hanuman'                    => array(
			'description'     => 'DESCRIPTION COMING SOON!',
			'url'             => 'WPPB_INTERNAL_FONT',
			'family'          => 'Hanuman',
		),
		'Inconsolata'                => array(
			'description'     => 'Inconsolata is a monospace font, designed for printed code listings and the like.',
			'url'             => 'http://fonts.googleapis.com/css?family=Inconsolata&subset=latin',
			'family'          => 'Inconsolata',
		),
		'Indie Flower'	=> array(
			'description'     => 'This handwriting font feels carefree and open to me with the bubbly, rounded edges. It is easy to read and a bit bolder than some of my other fonts.',
			'url'             => 'http://fonts.googleapis.com/css?family=Indie+Flower',
			'family'          => 'Indie Flower',
		),
		'Kenia'	=> array(
			'description'     => 'With its text appearance in small point sizes resembling an old German gothic sort of font, modern-feel Kenia works for headlines, introductory paragraphs, and in small text setting. Its playful and friendly character makes it suitable for happy typography in magazines, blogs, online games and other on-screen and print-based text.',
			'url'             => 'http://fonts.googleapis.com/css?family=Kenia',
			'family'          => 'Kenia',
		),
		'Just Me Again Down Here'	=> array(
			'description'     => 'This is a “messy” handwriting with mixed capitals and irregularities.',
			'url'             => 'http://fonts.googleapis.com/css?family=Just+Me+Again+Down+Here',
			'family'          => 'Just Me Again Down Here',
		),
		'Kristi'	=> array(
			'description'     => 'Kristi is a calligraphy font inspired by old chancery typefaces and it is made with a basic felt-pen by using bold and quick moves while writing. The most distinctive characteristics of this type are tall ascenders and descenders, slim vertical lines and little twists like the letter "g" in the text. It is advised to use Kristi for a title or text at quite large sizefor example as a logotype or heading.',
			'url'             => 'http://fonts.googleapis.com/css?family=Kristi',
			'family'          => 'Kristi',
		),
		'League Script'	=> array(
			'description'     => 'This ain’t no Lucida. League Script #1 is a modern, coquettish script font that sits somewhere between your high school girlfriend’s love notes and handwritten letters from the ’20s. Designed for the League of Moveable Type, it includes ligatures and will act as the framework for future script designs.',
			'url'             => 'http://fonts.googleapis.com/css?family=League+Script',
			'family'          => 'League Script',
		),
		'Lekton'	=> array(
			'description'     => 'Lekton has been designed at ISIA Urbino, Italy, and is inspired by some of the typefaces used on the Olivetti typewriters.',
			'url'             => 'http://fonts.googleapis.com/css?family=Lekton:regular,italic,bold',
			'family'          => 'Lekton',
		),
		'Lobster'                    => array(
			'description'     => 'Instead of compromising the design of our letters to force connections, the Lobster font lets lettering artists do what they do.',
			'url'             => 'http://fonts.googleapis.com/css?family=Lobster&subset=latin',
			'family'          => 'Lobster',
		),
		'Maiden Orange'	=> array(
			'description'     => "Maiden Orange is a light and festive slab serif font inspired by custom hand lettered 1950's advertisements. Clean and legible, while also being offbeat and friendly, this font lends itself to a wide variety of uses. From children's stories to the retro inspired, take Maiden Orange for a spin and let her liven up both your website and your design work.",
			'url'             => 'http://fonts.googleapis.com/css?family=Maiden+Orange',
			'family'          => 'Maiden Orange',
		),
		'MedievalSharp'	=> array(
			'description'     => 'This font was used to make inscriptions on stone.',
			'url'             => 'http://fonts.googleapis.com/css?family=MedievalSharp',
			'family'          => 'MedievalSharp',
		),
		'Michroma'	=> array(
			'description'     => 'Michroma is a reworking and remodelling of the rounded-square sans genre that is closely associated with a 1960s feeling of the future. This is due to the popularity of Microgramma, designed by Aldo Novarese and Alessandro Buttiin in 1952, which pioneered the style; and the most famous typeface family of the genre that came 10 years later in Novarese’s Eurostile.',
			'url'             => 'http://fonts.googleapis.com/css?family=Michroma',
			'family'          => 'Michroma',
		),
		'Miltonian'	=> array(
			'description'     => "Miltonian is a fun 'tattoo' font; the Tattoo variant has filled forms. They can be combined or filled for nice effects. Also included are a few dingbats that can be used for decoration.",
			'url'             => 'http://fonts.googleapis.com/css?family=Miltonian',
			'family'          => 'Miltonian',
		),
		'Miltonian Tattoo'	=> array(
			'description'     => "Miltonian is a fun 'tattoo' font; the Tattoo variant has filled forms. They can be combined or filled for nice effects. Also included are a few dingbats that can be used for decoration.",
			'url'             => 'http://fonts.googleapis.com/css?family=Miltonian+Tattoo',
			'family'          => 'Miltonian Tattoo',
		),
		'Molengo'                    => array(
			'description'     => 'Molengo is a Latin typeface for documents. It is multilingual and has some features required by many minority languages such as non-spacing mark placement.',
			'url'             => 'http://fonts.googleapis.com/css?family=Molengo&subset=latin',
			'family'          => 'Molengo',
		),
		'Neucha'                     => array(
			'description'     => 'An eccentric, yet occasionally useful font.',
			'url'             => 'http://fonts.googleapis.com/css?family=Neucha&subset=latin',
			'family'          => 'Neucha',
		),
		'Neuton'                     => array(
			'description'     => 'Neuton is a dark, somewhat Dutch-inspired serif font.',
			'url'             => 'http://fonts.googleapis.com/css?family=Neuton&subset=latin',
			'family'          => 'Neuton',
		),
		'Nobile'                     => array(
			'description'     => '"Nobile" is designed to work with the technologies of digital screens and handheld devices without losing the distinctive look more usually found in fonts designed for printing.',
			'url'             => 'http://fonts.googleapis.com/css?family=Nobile:regular,italic,bold,bolditalic&subset=latin',
			'family'          => 'Nobile',
		),
		'OFL Sorts Mill Goudy TT'    => array(
			'description'     => 'A ‘revival’ of Goudy Oldstyle and Italic, with features among which are small capitals (in the roman only), oldstyle and lining figures, superscripts and subscripts, fractions, ligatures, class-based kerning, case-sensitive forms and capital spacing.',
			'url'             => 'http://fonts.googleapis.com/css?family=OFL+Sorts+Mill+Goudy+TT:regular,italic&subset=latin',
			'family'          => 'OFL Sorts Mill Goudy TT',
		),
		'Old Standard TT'            => array(
			'description'     => 'Old Standard reproduces a specific type of Modern (classicist) style of serif typefaces, very commonly used in various editions of the late 19th and early 20th century, but almost completely abandoned later.',
			'url'             => 'http://fonts.googleapis.com/css?family=Old+Standard+TT:regular,italic,bold&subset=latin',
			'family'          => 'Old Standard TT',
		),
		'Oswald'	=> array(
			'description'     => "Oswald is a reworking of the classic style historically represented by the 'Alternate Gothic' sans serif typefaces. The characters of Oswald have been re-drawn and reformed to better fit the pixel grid of standard digital screens.",
			'url'             => 'http://fonts.googleapis.com/css?family=Oswald',
			'family'          => 'Oswald',
		),
		'Philosopher'                => array(
			'description'     => 'This font is universal, it can be used in the logos, you can make headlines, but you can gain massive amounts of texts.',
			'url'             => 'http://fonts.googleapis.com/css?family=Philosopher&subset=latin',
			'family'          => 'Philosopher',
		),
		/*
		'IM Fell'                    => array(
			'description'     => 'DESCRIPTION COMING SOON!',
			'url'             => 'coming_soon',
			'family'          => 'IM Fell',
		),
		*/
		/*
		'Josefin Sans'               => array(
			'description'     => 'DESCRIPTION COMING SOON!',
			'url'             => 'coming_soon',
			'family'          => 'Josefin Sans',
		),
		'Josefin Slab'               => array(
			'description'     => 'DESCRIPTION COMING SOON!',
			'url'             => 'coming_soon',
			'family'          => 'Josefin Slab',
		),
		*/
		'PT Sans'                    => array(
			'description'     => 'PT Sans is a simple, yet elegant sans-serif font.',
			'url'             => 'http://fonts.googleapis.com/css?family=PT+Sans:regular,italic,bold,bolditalic&subset=latin',
			'family'          => 'PT Sans',
		),
		'Quattrocento'	=> array(
			'description'     => 'The Quattrocento Roman typeface is a Classic, Elegant, Sober and Strong typeface. Their wide and open letterforms, and the great x-height, make it very legible for body text at small sizes. And their tiny details that only shows up at bigger sizes make it also great for display use.',
			'url'             => 'http://fonts.googleapis.com/css?family=Quattrocento',
			'family'          => 'Quattrocento',
		),
		'Quattrocento Sans'	=> array(
			'description'     => 'The Quattrocento Sans typeface is a Classic, Elegant, Sober and Strong typeface. Their wide and open letterforms, and the great x-height, make it very legible for body text at small sizes. And their tiny details that only shows up at bigger sizes make it also great for display use.',
			'url'             => 'http://fonts.googleapis.com/css?family=Quattrocento+Sans',
			'family'          => 'Quattrocento Sans',
		),
		'Radley'	=> array(
			'description'     => 'Radley is a display font, designed from woodcarved lettering. It is a practical face, based on letterforms used by hand carvers, where lettering needs to be cut quickly, efficiently, but with style.',
			'url'             => 'http://fonts.googleapis.com/css?family=Radley',
			'family'          => 'Radley',
		),
		'Reenie Beanie'	=> array(
			'description'     => 'Reene Beanie is a fun font based on basic ball-point pen handwriting. It has a playful and loose look, which lends itself to casual and informal messages.',
			'url'             => 'http://fonts.googleapis.com/css?family=Reenie+Beanie&subset=latin',
			'family'          => 'Reenie Beanie',
		),
		'Six Caps'	=> array(
			'description'     => "Six Caps is a highly condensed and tight display font. It is a stripped down and 'normalised' version of the classic grotesk display forms.",
			'url'             => 'http://fonts.googleapis.com/css?family=Six+Caps',
			'family'          => 'Six Caps',
		),
		'Smythe'	=> array(
			'description'     => "Smythe is a reworking and mashing together of various typefaces from the late nineteenth and early twentieth centuries that can be best described as 'Arts and Crafts', or, 'Art Deco'. Smythe also has a touch of Batfink too!",
			'url'             => 'http://fonts.googleapis.com/css?family=Smythe',
			'family'          => 'Smythe',
		),
		'Sniglet'	=> array(
			'description'     => 'A rounded display face that’s great for headlines.',
			'url'             => 'http://fonts.googleapis.com/css?family=Sniglet:800	',
			'family'          => 'Sniglet',
		),
		'Special Elite'	=> array(
			'description'     => 'Special Elite was created to mimic the Smith Corona Special Elite Type No NR6 and Remington Noiseless typewriter models. A little bit of inked up grunge and a little old school analog flavor work together to give you a vintage typewriter typeface for your website and designs.',
			'url'             => 'http://fonts.googleapis.com/css?family=Special+Elite',
			'family'          => 'Special Elite',
		),
		'Sue Ellen Francisco'	=> array(
			'description'     => 'Sue Ellen Francisco is a tall, skinny font based on my own handwriting. It was created in Adobe Illustrator, using a Wacom tablet. It is named after a nickname I used for a friend. It is one of my earlier fonts and is imperfect, but I still enjoy the tall, slender lines.',
			'url'             => 'http://fonts.googleapis.com/css?family=Sue+Ellen+Francisco',
			'family'          => 'Sue Ellen Francisco',
		),
		'Sunshiney'	=> array(
			'description'     => 'Is your website a little weary? Is your blog a bit boggy? Brighten them up with Sunshiney, a little a ray of hand-crafted goodness by Squid! This happy little font is sure to lighten up the dreariest of domains or the frumpiest of facebook pages. Happy, happy!',
			'url'             => 'http://fonts.googleapis.com/css?family=Sunshiney',
			'family'          => 'Sunshiney',
		),
		'Tangerine'                  => array(
			'description'     => 'Tangerine is a calligraphy font inspired by many italic chancery hands from the 16-17th century. Its tall ascender, the most distinct characteristic of this type, takes a wide line space between lines and gives it a graceful texture.',
			'url'             => 'http://fonts.googleapis.com/css?family=Tangerine:regular,bold&subset=latin',
			'family'          => 'Tangerine',
		),
		'Terminal Dosis Light'	=> array(
			'description'     => "Terminal Dosis Light is a really really light, almost hairline, sans-serif rounded font. In fact, it's so light she only wants to be used at 36pt or up. She also includes many alternative characters, all designed by Edgar Tolentino and Pablo Impallari.",
			'url'             => 'http://fonts.googleapis.com/css?family=Terminal+Dosis+Light',
			'family'          => 'Terminal Dosis Light',
		),
		'The Girl Next Door'	=> array(
			'description'     => "'the girl next door' is based on the handwriting of a middle school geography teacher. From her personality to her handwriting, she is the typical girl next door - she puts you at ease in every situation and is perfectly comfortable and confident in who she is. Her handwriting reflects that and is readable, neat, and yet comfortable and welcoming.",
			'url'             => 'http://fonts.googleapis.com/css?family=The+Girl+Next+Door',
			'family'          => 'The Girl Next Door',
		),
		'Tinos'                      => array(
			'description'     => 'Tinos was designed by Steve Matteson as an innovative, refreshing sans serif design that is metrically compatible with Times New Roman™',
			'url'             => 'http://fonts.googleapis.com/css?family=Tinos:regular,italic,bold,bolditalic&subset=latin',
			'family'          => 'Tinos',
		),
		'UnifrakturMaguntia'	=> array(
			'description'     => 'UnifrakturMaguntia is based on Peter Wiegel’s font Berthold Mainzer Fraktur which is in turn based on a 1901 typeface by Carl Albert Fahrenwaldt. While the glyph design of Peter Wiegel’s font has hardly been changed at all, UnifrakturMaguntia uses smart font technologies for displaying the font’s ligatures (OpenType, Apple Advanced Typography and SIL Graphite). An experimental feature is the distinction of good blackletter typography between required ligatures ‹ch, ck, ſt, tz› that must be kept when letterspacing is increased, and regular ligatures (for instance, ‹fi, fl›) that are broken up when letterspacing is increased.',
			'url'             => 'http://fonts.googleapis.com/css?family=UnifrakturMaguntia',
			'family'          => 'UnifrakturMaguntia',
		),
		'Vollkorn'                   => array(
			'description'     => 'Vollkorn intends to be a quiet, modest and well working text face for bread and butter use. Unlike its examples in the book faces from the renaissance until today, it has dark and meaty serifs and a bouncing and healthy look',
			'url'             => 'http://fonts.googleapis.com/css?family=Vollkorn:regular,italic,bold,bolditalic&subset=latin',
			'family'          => 'Vollkorn',
		),
		'VT323'	=> array(
			'description'     => 'This font was created from the glyphs of the DEC VT320 text terminal.',
			'url'             => 'http://fonts.googleapis.com/css?family=VT323',
			'family'          => 'VT323',
		),
		'Waiting for the Sunrise'	=> array(
			'description'     => 'Waiting for the Sunrise is based on the handwriting of a high school student. The title comes from the song Lift Me Up by The Afters. Although this font was created during a time of darkness in my life, it is a cheery, perky font. It is a reminder to me of the joy that comes after mourning.',
			'url'             => 'http://fonts.googleapis.com/css?family=Waiting+for+the+Sunrise',
			'family'          => 'Waiting for the Sunrise',
		),
		'Yanone Kaffeesatz'          => array(
			'description'     => ' Its Bold is reminiscent of 1920s coffee house typography, while the rather thin fonts bridge the gap to present times.',
			'url'             => 'http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:extralight,light,regular,bold&subset=latin',
			'family'          => 'Yanone Kaffeesatz',
		),
	);
	return $fonts;
}

/**
 * Load Thickbox scripts
 * @since 0.1
 */
function wppb_fonts_thickbox_scripts() {
	wp_enqueue_script( 'theme-preview' );
}

/**
 * Load Thickbox styles
 * @since 0.1
 */
function wppb_fonts_thickbox_styles() {
	wp_register_style( 'thickbox', site_url() . '/wp-includes/js/thickbox/thickbox.css', '', 1.0 );
	wp_enqueue_style( 'thickbox' );
}

/**
 * Get option
 * @since 0.1
 */
function get_wppb_font_option( $option ) {
	$options = get_option( 'wppb_fonts' );
	if ( isset( $options[$option] ) )
		return $options[$option];
}

/**
 * Embedding fonts
 * @since 0.1
 */
function wppb_load_fonts() {
	foreach ( wppb_embeddable_fonts() as $font => $details ) {
		if ( 'on' == get_wppb_font_option( 'fontembed_' . $font ) )
			wp_enqueue_style( 'pressabl-' . $font, $details['url'], false, '', 'screen' );
	}

}
add_action( 'wp_print_styles', 'wppb_load_fonts' );
