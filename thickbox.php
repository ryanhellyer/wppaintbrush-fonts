<!DOCTYPE html>
<html dir="ltr" lang="en-US">
<head>
	<meta charset="UTF-8" />
	<title>Pressabl theme font test page</title>
<?php
/**
 * Inject font here
 */
foreach ( wppb_embeddable_fonts() as $font => $details ) {
	$details['url'] = str_replace( 'PRESSABL_INTERNAL_FONT_', get_template_directory_uri() . '/fonts', $details['url'] );
	if ( $_GET['font_test'] == $font ) {
		echo "	<link rel='stylesheet' href='" . $details['url'] . "' type='text/css' />\n";
		$current_font = $details['family'];
	}
}
?>
	<style type="text/css">
		span,p {
			font-family: '<?php echo $current_font; ?>';
			margin: 10px 0;
			display: block;
			line-height: 36px;
		}
	</style>
</head>
<body>

<span style="font-size:36px;line-height:36px;"><?php echo $_GET['font_test']; ?> font at 30px</span>
<span style="font-size:24px;line-height:24px;"><?php echo $_GET['font_test']; ?> font at 24px <em>Italics</em>, <strong>bold</strong></span>
<span style="font-size:20px;line-height:20px;"><?php echo $_GET['font_test']; ?> font at 20px <em>Italics</em>, <strong>bold</strong></span>
<span style="font-size:18px;line-height:18px;"><?php echo $_GET['font_test']; ?> font at 18px <em>Italics</em>, <strong>bold</strong></span>
<span style="font-size:16px;line-height:16px;"><?php echo $_GET['font_test']; ?> font at 16px <em>Italics</em>, <strong>bold</strong></span>
<span style="font-size:14px;line-height:14px;"><?php echo $_GET['font_test']; ?> font at 14px <em>Italics</em>, <strong>bold</strong></span>
<span style="font-size:12px;line-height:12px;"><?php echo $_GET['font_test']; ?> font at 12px <em>Italics</em>, <strong>bold</strong></span>
<span style="font-size:10px;line-height:10px;"><?php echo $_GET['font_test']; ?> font at 10px <em>Italics</em>, <strong>bold</strong></span>
<br />
<span style="font-size:14px;line-height:20px;">
	The font-family can use a specific named font, but it is important to always name a fallback generic font family in 
	case the named font does not exist. Generic fonts include serif, sans-serif, monospace, cursive and fantasy.
	To ensure that all web users had a basic set of fonts, Microsoft released fonts includeing Arial, Georgia, 
	Times New Roman, and Verdana under an EULA which made them freely distributable.
</span>

</body>
</html>
<?php die; ?>