<?php
namespace JWP\WCD;

class Plugin {
    private static $_instance = null;

    private $settings = null;

    private $visited_discount = false;

    private $leaving_discount = false;

    private $timeout = 3600; // время в секундах, после которого считается что пользователь покинул сайт.

    protected function __clone() {
        _doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'content-copy-finder' ), '1.0.0' );
    }

    protected function __wakeup() {
        _doing_it_wrong( __FUNCTION__, __( 'Something went wrong.', 'content-copy-finder' ), '1.0.0' );
    }

    static public function instance() {
        if( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {
        $this->settings = new Settings;
        add_action( 'init', array( $this, 'init' ) );
    }

    public function init() {
        $this->hooks();
        $this->compute_discount();
    }

    private function hooks() {
        add_filter( 'woocommerce_product_get_price', array( $this, 'custom_price' ), 99, 2 );
        add_filter( 'woocommerce_product_variation_get_price', array( $this, 'custom_price' ), 99, 2 );

        if ( ! is_user_logged_in() ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        }

        add_filter( 'wcd/reg_discount', array( $this, 'price_filter' ), 10, 2 );
        add_filter( 'wcd/visited_discount', array( $this, 'price_filter' ), 10, 2 );
        add_filter( 'wcd/leaving_discount', array( $this, 'price_filter' ), 10, 2 );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'wcd-main', WCD_PLUGIN_URL . 'assets/js/wcd.js', array( 'jquery' ) );
        wp_localize_script( 'wcd-main', 'wcd', array(
            'visited_discount' => floatval( $this->settings->get( 'visited_discount' ) ),
            'leaving_discount' => floatval( $this->settings->get( 'leaving_discount' ) )
        ) );
    }

    public function custom_price( $price, $product ) {
        if ( is_user_logged_in() ) {
            $price = apply_filters( 'wcd/reg_discount', $price, 'reg' );
        } elseif( $this->has_visited_discount() ) {
            $price = apply_filters( 'wcd/visited_discount', $price, 'visited' );
        } elseif ( $this->has_leaving_discount() ) {
            $price = apply_filters( 'wcd/leaving_discount', $price, 'leaving' );
        }
        return (float) $price;
    }

    public function has_visited_discount() {
        return $this->visited_discount ? true : false;
    }

    public function has_leaving_discount() {
        return $this->leaving_discount ? true : false;
    }

    protected function compute_discount() {
        if ( isset( $_COOKIE['wcd_leaving_discount'] ) ) {
            $this->leaving_discount = true;
        }
        if ( isset( $_COOKIE['wcd_visited_discount'] ) ) {
            $this->visited_discount = true;
            return;
        }
        if ( isset( $_COOKIE['wcd_last_visited'] ) ) {
            $last_visited = intval( $_COOKIE['wcd_last_visited'] );
            if ( ( $last_visited + $this->timeout ) > date( 'U' ) ) {
                return;
            }
            $current_day = new \DateTime();
            $current_day->setTime( 0, 0, 0 );
            $start_day_timestamp = $current_day->format( 'U' );
            if ( $last_visited > $start_day_timestamp ) {
                setcookie( 'wcd_show_message', 1, date('U' ) + MONTH_IN_SECONDS, '/' );
                setcookie( 'wcd_visited_discount', 1, date('U' ) + MONTH_IN_SECONDS, '/' );
                $this->visited_discount = true;
            }
        }
    }

    public function price_filter( $price = 0, $type = '' ) {
        if ( ! $type ) {
            return $price;
        }
        if ( 'reg' == $type ) {
            $settings_field = 'reg_discount';
        } elseif ( 'leaving' == $type ) {
            $settings_field = 'leaving_discount';
        } elseif ( 'visited' == $type ) {
            $settings_field = 'visited_discount';
        }
        $discount_percent = floatval( $this->settings->get( $settings_field ) );
        $ratio = $price / 100;
        $discount = $ratio * $discount_percent;
        $price = $price - $discount;
        return $price;
    }

}