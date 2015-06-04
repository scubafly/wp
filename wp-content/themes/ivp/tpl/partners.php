<?php

echo $before_widget;
if ( !empty( $instance ) ) {	
	if ( $imageurl ) {
		echo "<img src=\"{$imageurl}\" style=\"";
		if ( !empty( $width ) && is_numeric( $width ) ) {
			echo "max-width: {$width}px;";
		}
		if ( !empty( $height ) && is_numeric( $height ) ) {
			echo "max-height: {$height}px;";
		}
		echo "\"";
		if ( !empty( $align ) && $align != 'none' ) {
			echo " class=\"align{$align}\"";
		}
		if ( !empty( $alt ) ) {
			echo " alt=\"{$alt}\"";
		} else {
			echo " alt=\"{$title}\"";					
		}
		echo " />";
	}
}
echo $after_widget;
?>