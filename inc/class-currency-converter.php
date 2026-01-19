<?php
/**
 * Currency Converter System
 *
 * A built-in multi-currency system for WooCommerce.
 * Base currency is ISK (Icelandic Króna).
 *
 * @package Kadence_Child
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Currency configuration - exchange rates from ISK (Icelandic Króna)
 * Update these rates periodically or integrate with an API
 *
 * @return array Currency configuration array
 */
function jj_get_currency_config() {
    return array(
        'ISK' => array(
            'rate'     => 1,           // Base currency
            'symbol'   => 'kr',
            'position' => 'right_space', // Symbol on right with space: "100 kr"
            'decimals' => 0,
            'name'     => 'IS',
        ),
        'USD' => array(
            'rate'     => 0.0071,      // 1 ISK = 0.0071 USD (approx)
            'symbol'   => '$',
            'position' => 'left',      // Symbol on left: "$10"
            'decimals' => 2,
            'name'     => 'USA',
        ),
        'EUR' => array(
            'rate'     => 0.0067,      // 1 ISK = 0.0067 EUR (approx)
            'symbol'   => '€',
            'position' => 'left',
            'decimals' => 2,
            'name'     => 'EU',
        ),
        'GBP' => array(
            'rate'     => 0.0057,      // 1 ISK = 0.0057 GBP (approx)
            'symbol'   => '£',
            'position' => 'left',
            'decimals' => 2,
            'name'     => 'UK',
        ),
        'NOK' => array(
            'rate'     => 0.079,       // 1 ISK = 0.079 NOK (approx)
            'symbol'   => 'kr',
            'position' => 'right_space',
            'decimals' => 0,
            'name'     => 'NO',
        ),
        'DKK' => array(
            'rate'     => 0.050,       // 1 ISK = 0.050 DKK (approx)
            'symbol'   => 'kr',
            'position' => 'right_space',
            'decimals' => 0,
            'name'     => 'DK',
        ),
        'SEK' => array(
            'rate'     => 0.076,       // 1 ISK = 0.076 SEK (approx)
            'symbol'   => 'kr',
            'position' => 'right_space',
            'decimals' => 0,
            'name'     => 'SE',
        ),
    );
}

/**
 * Get current currency from cookie or default to ISK
 *
 * @return string Currency code
 */
function jj_get_current_currency() {
    if (isset($_COOKIE['jj_currency']) && !empty($_COOKIE['jj_currency'])) {
        $currency = sanitize_text_field($_COOKIE['jj_currency']);
        $currencies = jj_get_currency_config();
        if (isset($currencies[$currency])) {
            return $currency;
        }
    }
    return 'ISK'; // Default currency
}

/**
 * Set currency via AJAX
 */
function jj_set_currency_ajax() {
    $currency = isset($_POST['currency']) ? sanitize_text_field($_POST['currency']) : 'SEK';
    $currencies = jj_get_currency_config();

    if (isset($currencies[$currency])) {
        setcookie('jj_currency', $currency, time() + (86400 * 30), '/'); // 30 days
        wp_send_json_success(array('currency' => $currency));
    } else {
        wp_send_json_error(array('message' => 'Invalid currency'));
    }
    wp_die();
}
add_action('wp_ajax_jj_set_currency', 'jj_set_currency_ajax');
add_action('wp_ajax_nopriv_jj_set_currency', 'jj_set_currency_ajax');

/**
 * Convert price from ISK to selected currency
 *
 * @param float $price_in_isk Price in ISK
 * @return float Converted price
 */
function jj_convert_price($price_in_isk) {
    $currency = jj_get_current_currency();
    $currencies = jj_get_currency_config();

    if (!isset($currencies[$currency])) {
        return $price_in_isk;
    }

    $config = $currencies[$currency];
    return $price_in_isk * $config['rate'];
}

/**
 * Format price with correct currency symbol and position
 *
 * @param float $price Price to format
 * @param string|null $currency_code Optional currency code
 * @return string Formatted price
 */
function jj_format_price($price, $currency_code = null) {
    if ($currency_code === null) {
        $currency_code = jj_get_current_currency();
    }

    $currencies = jj_get_currency_config();

    if (!isset($currencies[$currency_code])) {
        return wc_price($price);
    }

    $config = $currencies[$currency_code];
    $formatted_number = number_format($price, $config['decimals'], '.', ',');

    switch ($config['position']) {
        case 'left':
            return $config['symbol'] . $formatted_number;
        case 'left_space':
            return $config['symbol'] . ' ' . $formatted_number;
        case 'right':
            return $formatted_number . $config['symbol'];
        case 'right_space':
            return $formatted_number . ' ' . $config['symbol'];
        default:
            return $config['symbol'] . $formatted_number;
    }
}

/**
 * Filter WooCommerce price HTML
 *
 * @param string $price_html Original price HTML
 * @param WC_Product $product Product object
 * @return string Modified price HTML
 */
function jj_filter_woocommerce_price($price_html, $product) {
    $currency = jj_get_current_currency();

    // If ISK (base currency), return original
    if ($currency === 'ISK') {
        return $price_html;
    }

    // Get original price(s) in ISK
    if ($product->is_type('variable')) {
        $min_price = $product->get_variation_price('min', true);
        $max_price = $product->get_variation_price('max', true);

        $min_converted = jj_convert_price($min_price);
        $max_converted = jj_convert_price($max_price);

        if ($min_price === $max_price) {
            return jj_format_price($min_converted);
        } else {
            return jj_format_price($min_converted) . ' – ' . jj_format_price($max_converted);
        }
    } else {
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();

        if ($sale_price && $sale_price < $regular_price) {
            $regular_converted = jj_convert_price($regular_price);
            $sale_converted = jj_convert_price($sale_price);
            return '<del>' . jj_format_price($regular_converted) . '</del> <ins>' . jj_format_price($sale_converted) . '</ins>';
        } else {
            $price = $product->get_price();
            $converted = jj_convert_price($price);
            return jj_format_price($converted);
        }
    }
}
add_filter('woocommerce_get_price_html', 'jj_filter_woocommerce_price', 100, 2);

/**
 * Filter cart item price
 *
 * @param string $price_html Original price HTML
 * @param array $cart_item Cart item data
 * @param string $cart_item_key Cart item key
 * @return string Modified price HTML
 */
function jj_filter_cart_item_price($price_html, $cart_item, $cart_item_key) {
    $currency = jj_get_current_currency();

    if ($currency === 'ISK') {
        return $price_html;
    }

    $product = $cart_item['data'];
    $price = $product->get_price();
    $converted = jj_convert_price($price);

    return jj_format_price($converted);
}
add_filter('woocommerce_cart_item_price', 'jj_filter_cart_item_price', 100, 3);

/**
 * Filter cart subtotal
 *
 * @param string $subtotal Original subtotal HTML
 * @param bool $compound Whether compound
 * @param WC_Cart $cart Cart object
 * @return string Modified subtotal HTML
 */
function jj_filter_cart_subtotal($subtotal, $compound, $cart) {
    $currency = jj_get_current_currency();

    if ($currency === 'ISK') {
        return $subtotal;
    }

    $total = $cart->get_subtotal();
    $converted = jj_convert_price($total);

    return jj_format_price($converted);
}
add_filter('woocommerce_cart_subtotal', 'jj_filter_cart_subtotal', 100, 3);

/**
 * Filter cart total
 *
 * @param string $total Original total
 * @return string Modified total
 */
function jj_filter_cart_total($total) {
    $currency = jj_get_current_currency();

    if ($currency === 'ISK') {
        return $total;
    }

    // Remove currency symbol and convert
    $numeric_total = floatval(preg_replace('/[^0-9.]/', '', $total));
    $converted = jj_convert_price($numeric_total);

    return jj_format_price($converted);
}
add_filter('woocommerce_cart_total', 'jj_filter_cart_total', 100);
