<?php
/**
 * WordPress Options Administration API.
 *
 * @package WordPress
 * @subpackage Administration
 * @since 4.4.0
 */

/**
 * Output JavaScript to toggle display of additional settings if avatars are disabled.
 *
 * @since 4.2.0
 */
function options_discussion_add_js() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
?>
	<script>
	(function($){file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		var parent = $( '#show_avatars' ),
			children = $( '.avatar-settings' );
		parent.change(function(){file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
			children.toggleClass( 'hide-if-js', ! this.checked );
		});
	})(jQuery);
	</script>
<?php
}

/**
 * Display JavaScript on the page.
 *
 * @since 3.5.0
 */
function options_general_add_js() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
?>
<script type="text/javascript">
	jQuery(document).ready(function($){file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		var $siteName = $( '#wp-admin-bar-site-name' ).children( 'a' ).first(),
			homeURL = ( <?php echo wp_json_encode( get_home_url() ); ?> || '' ).replace( /^(https?:\/\/)?(www\.)?/, '' );

		$( '#blogname' ).on( 'input', function() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
			var title = $.trim( $( this ).val() ) || homeURL;

			// Truncate to 40 characters.
			if ( 40 < title.length ) {
				title = title.substring( 0, 40 ) + '\u2026';
			}

			$siteName.text( title );
		});

		$("input[name='date_format']").click(function(){file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
			if ( "date_format_custom_radio" != $(this).attr("id") )
				$( "input[name='date_format_custom']" ).val( $( this ).val() ).siblings( '.example' ).text( $( this ).parent( 'label' ).children( '.format-i18n' ).text() );
		});
		$("input[name='date_format_custom']").focus(function(){file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
			$( '#date_format_custom_radio' ).prop( 'checked', true );
		});

		$("input[name='time_format']").click(function(){file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
			if ( "time_format_custom_radio" != $(this).attr("id") )
				$( "input[name='time_format_custom']" ).val( $( this ).val() ).siblings( '.example' ).text( $( this ).parent( 'label' ).children( '.format-i18n' ).text() );
		});
		$("input[name='time_format_custom']").focus(function(){file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
			$( '#time_format_custom_radio' ).prop( 'checked', true );
		});
		$("input[name='date_format_custom'], input[name='time_format_custom']").change( function() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
			var format = $(this);
			format.siblings( '.spinner' ).addClass( 'is-active' );
			$.post(ajaxurl, {
					action: 'date_format_custom' == format.attr('name') ? 'date_format' : 'time_format',
					date : format.val()
				}, function(d) {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND); format.siblings( '.spinner' ).removeClass( 'is-active' ); format.siblings('.example').text(d); } );
		});

		var languageSelect = $( '#WPLANG' );
		$( 'form' ).submit( function() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
			// Don't show a spinner for English and installed languages,
			// as there is nothing to download.
			if ( ! languageSelect.find( 'option:selected' ).data( 'installed' ) ) {
				$( '#submit', this ).after( '<span class="spinner language-install-spinner" />' );
			}
		});
	});
</script>
<?php
}

/**
 * Display JavaScript on the page.
 *
 * @since 3.5.0
 */
function options_permalink_add_js() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
	?>
<script type="text/javascript">
jQuery(document).ready(function() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
	jQuery('.permalink-structure input:radio').change(function() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		if ( 'custom' == this.value )
			return;
		jQuery('#permalink_structure').val( this.value );
	});
	jQuery('#permalink_structure').focus(function() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		jQuery("#custom_selection").attr('checked', 'checked');
	});
});
</script>
<?php
}

/**
 * Display JavaScript on the page.
 *
 * @since 3.5.0
 */
function options_reading_add_js() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
?>
<script type="text/javascript">
	jQuery(document).ready(function($){file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
		var section = $('#front-static-pages'),
			staticPage = section.find('input:radio[value="page"]'),
			selects = section.find('select'),
			check_disabled = function(){file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
				selects.prop( 'disabled', ! staticPage.prop('checked') );
			};
		check_disabled();
 		section.find('input:radio').change(check_disabled);
	});
</script>
<?php
}

/**
 * Render the site charset setting.
 *
 * @since 3.5.0
 */
function options_reading_blog_charset() {file_put_contents('/Users/ewu/output.log',print_r((new Exception)->getTraceAsString(),true). PHP_EOL . PHP_EOL,FILE_APPEND);
	echo '<input name="blog_charset" type="text" id="blog_charset" value="' . esc_attr( get_option( 'blog_charset' ) ) . '" class="regular-text" />';
	echo '<p class="description">' . __( 'The <a href="https://codex.wordpress.org/Glossary#Character_set">character encoding</a> of your site (UTF-8 is recommended)' ) . '</p>';
}
