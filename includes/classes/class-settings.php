<?php
namespace JWP\WCD;

class Settings {

    public $menu_slug   = 'wcd-settings';

    public $option_name = 'wcd_settings';

    protected $option = null;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_settings_page' ) );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
    }

    public function admin_init() {
        register_setting(
            $this->option_name,
            $this->option_name,
            array( $this, 'sanitize_fields' )
        );
        add_settings_section(
            'wcd_settings_main',
            'Основные настройки',
            '',
            $this->option_name
        );
        add_settings_field(
            'reg_discount',
            'Скидка для зарегистророванного юзера',
            array( $this, 'render_input_field' ),
            $this->option_name,
            'wcd_settings_main',
            array( 'key' => 'reg_discount' )
        );
        add_settings_field(
            'visited_discount',
            'Скидка для посетившего сегодня',
            array( $this, 'render_input_field' ),
            $this->option_name,
            'wcd_settings_main',
            array( 'key' => 'visited_discount' )
        );
        add_settings_field(
            'leaving_discount',
            'Скидка для покидающего сайт',
            array( $this, 'render_input_field' ),
            $this->option_name,
            'wcd_settings_main',
            array( 'key' => 'leaving_discount' )
        );
    }

    public function register_settings_page() {
        add_options_page(
            'Настройки Woo Custom Discount',
            'Настройки WCD',
            'manage_options',
            $this->menu_slug,
            array( $this, 'render_settings_page' )
        );
    }

    public function render_settings_page() {
        View::render( 'settings-page', array(
            'option_name' => $this->option_name
        ) );
    }

    public function render_input_field( $params = array() ) {
        $key = isset( $params['key'] ) ? $params['key'] : '';
        $val = $this->get( $key );
        View::render( 'input-field', array(
            'key' => $key,
            'val' => $val
        ) );
    }

    public function sanitize_fields( $options ) {
        foreach ( $options as &$option ) {
            $option = sanitize_text_field( $option );
        }
        return $options;
    }

    public function get( $option_key = '' ) {
        if ( is_null( $this->option ) ) {
            $this->option = get_option( $this->option_name );
        }
        if ( isset( $this->option[ $option_key ] ) ) {
            return $this->option[ $option_key ];
        }

        return '';
    }

}
