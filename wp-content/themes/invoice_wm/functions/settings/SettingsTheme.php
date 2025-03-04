<?php

namespace Invoice\settings;

class SettingsTheme {
	public function enable_menus( $menus ): void {
		add_action( 'after_setup_theme', function () use ( $menus ) {
			register_nav_menus( $menus );
		} );
	}

	public function enable_thumbnails(): void {
		add_action( 'after_setup_theme', function () {
			add_theme_support( 'post-thumbnails' );
		} );
	}

	public function disable_content_editor( $template_files = [] ): void {
		add_action( 'admin_init', function () use ( $template_files ) {
			$post_id = $_GET['post'] ?? ( $_POST['post_ID'] ?? '' );
			if ( ! $post_id ) {
				return;
			}

			$template_file = get_post_meta( $post_id, '_wp_page_template', true );
			if ( in_array( $template_file, $template_files, true ) ) {
				remove_post_type_support( 'page', 'editor' );
			}
		} );
	}

	public function add_custom_admin_css(): void {
		add_action( 'admin_footer', function () {
			echo '<style>
                .cf-complex__groups { z-index: 0 !important; }
                ul.cf-complex__inserter-menu { min-width: 250px; max-height: 50vh; overflow-y: auto; }
                .cf-complex__inserter-item { padding: 5px 12px; }
            </style>';
		} );
	}

	public function get_attach_by_id_js(): void {
		add_action( 'admin_footer', function () {
			$admin_ajax = esc_js( admin_url( 'admin-ajax.php' ) );
			echo "<script>
                var _adminAjax = '{$admin_ajax}';
                jQuery(document).ready(function () {
                    setTimeout(function () {
                        jQuery('.cf-file__inner').each(function () {
                            var t = jQuery(this);
                            var id = t.find('input[type=\"hidden\"]').val();
                            if (id) {
                                jQuery.post(_adminAjax, {
                                    action: 'get_attach_by_id',
                                    id: id
                                }).done(function (response) {
                                    t.find('.cf-file__image').attr('src', response);
                                });
                            }
                        });
                    }, 500);
                });
            </script>";
		} );
	}

	public function hide_admin_bar(): void {
		add_filter( 'show_admin_bar', '__return_false' );
	}

	public function archive_title_hook(): void {
		add_filter( 'get_the_archive_title', function ( $title ) {
			return preg_replace( '~^[^:]+: ~', '', $title );
		} );
	}

	public function association_field_title(): void {
		add_filter( 'carbon_fields_association_field_title', function ( $title, $name, $id, $type, $subtype ) {
			$post_id = $_GET['post'] ?? 0;
			if ( (int) $post_id === (int) $id ) {
				$title .= ' - [CURRENT POST]';
			}

			return $title;
		}, 10, 5 );
	}

	public function add_menu_bubble(): void {
		add_action( 'admin_menu', [ $this, 'add_user_menu_bubble' ] );
	}

	public function add_user_menu_bubble(): void {
		global $menu;
		$count1 = wp_count_posts( 'reviews' )->pending;
		$count2 = wp_count_posts( 'orders' )->pending;
		if ( $count1 ) {
			foreach ( $menu as $key => $value ) {
				if ( $menu[ $key ][2] == 'edit.php?post_type=reviews' ) {
					$menu[ $key ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $count1 . '</span></span>';
					break;
				}
			}
		}
		if ( $count2 ) {
			foreach ( $menu as $key => $value ) {
				if ( $menu[ $key ][2] == 'edit.php?post_type=orders' ) {
					$menu[ $key ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . $count2 . '</span></span>';
					break;
				}
			}
		}
	}

	public static function redirect_to_art(): void {
		if ( get_current_user_id() ) {
			return;
		}
		wp_redirect( 'https://web-mosaica.art/' );
		exit();
	}

	public function views_hook(): void {
		add_action( 'init', [ $this, 'start_session' ] );
		add_action( 'wp', function () {
			if ( is_single() ) {
				$this->track_unique_views( get_the_ID() );
			}
		} );
	}

	public function start_session(): void {
		if ( ! session_id() ) {
			session_start();
		}
	}

	private function track_unique_views( bool|int $post_id ): void {
		if ( is_bot() ) {
			return;
		}
		if ( ! is_single() || empty( $post_id ) || ! is_singular( 'bill' ) ) {
			return;
		}
		if ( get_current_user_id() ) {
			return;
		}

		$meta_key = 'unique_views_count';
		if ( ! isset( $_SESSION['viewed_posts'] ) || ! in_array( $post_id, $_SESSION['viewed_posts'] ) ) {
			$views = get_post_meta( $post_id, $meta_key, true );
			$views = $views ? intval( $views ) : 0;
			update_post_meta( $post_id, $meta_key, $views + 1 );
			$_SESSION['viewed_posts'][] = $post_id;
			$bill_views                 = carbon_get_post_meta( $post_id, 'bill_views' ) ?: [];
			$city                       = get_client_city_by_ip( get_client_ip() );
			$temp                       = [
				'ip'      => get_client_ip(),
				'client'  => get_user_agent(),
				'time'    => current_time( 'd-m-Y H:i' ),
				'ip_city' => $city,
			];
			array_unshift( $bill_views, $temp );
			carbon_set_post_meta( $post_id, 'bill_views', $bill_views );
		}
	}
}

$settings = new SettingsTheme();
$settings->enable_thumbnails();
$settings->disable_content_editor( [
	'login-page.php',
	'index.php',
] );
$settings->add_custom_admin_css();
$settings->archive_title_hook();
$settings->association_field_title();
$settings->get_attach_by_id_js();
$settings->hide_admin_bar();
$settings->views_hook();
