<?php

declare(strict_types=1);

/**
 * Converts an URL to an anchor link.
 *
 * Prefix the last segment of a URL with a # to use it as an anchor link.
 *
 * @since 1.0.0
 * @example
 * ```php
 * // Output: https://www.mind.ch/onepager#subsection
 * echo make_anchor_link( 'https://www.mind.ch/onepager/subsection' );
 * ```
 *
 * @param string $url URL to turn into an anchor link.
 * @return string The updated URL.
 */
function make_anchor_link($url)
{
    return untrailingslashit(preg_replace('/[^\/]+(?=\/$|$)/i', '#$0', $url));
}

/**
 * Checks if a URL is external or internal.
 *
 * @since 1.0.0
 * @example
 * ```php
 * if ( is_external_url( 'https://example.org' ) ) {
 *     // Do something
 * }
 * ```
 *
 * ```twig
 * {% if is_external_url('https://www.example.org') %}
 *     {# Do something #}
 * {% endif %}
 * ```
 *
 * @param string $url URL to be parsed.
 * @return bool Whether the URL is external.
 */
function is_external_url($url)
{
    $link_url = wp_parse_url($url);
    $home_url = wp_parse_url(home_url());

    return $link_url['host'] !== $home_url['host'];
}

/**
 * Converts an URL to just the domain name together with the TLD.
 *
 * @since 1.0.0
 * @example
 * ```php
 * <?php $domain = 'https://www.mind.ch/post/blablabla?param=wow'; ?>
 * <a href="<?php echo $url; ?>"><?php echo url_to_domain( $domain ); ?></a>
 * ```
 *
 * ```twig
 * {# url = 'https://www.mind.ch/post/blablabla?param=wow' #}
 * <a href="{{ url }}">{{ url_to_domain(url) }}</a>
 * ```
 *
 * @param string   $url       The URL to convert.
 * @param bool     $strip_www Optional. Whether to strip the "www" part of the domain. Default false.
 * @param int|bool $limit     Optional. Limit to a certain amount of characters. Default false.
 *
 * @return string The domain part of the URL.
 */
function url_to_domain($url, $strip_www = false, $limit = false)
{
    $host = wp_parse_url($url, PHP_URL_HOST);

    /**
     * If the URL can't be parsed, use the original URL.
     * Change to "return false" if you don't want that.
     */
    if (! $host) {
        $host = $url;
    }

    /**
     * The "www." prefix isn't really needed if you're just using
     * this to display the domain to the user.
     */
    if ('www.' === substr($host, 0, 4) && $strip_www) {
        $host = substr($host, 4);
    }

    // You might also want to limit the length if screen space is limited
    if ($limit && strlen($host) > $limit) {
        $host = substr($host, 0, $limit - 3) . '&hellip;';
    }

    return $host;
}

/**
 * Gets href attribute for a link tag with proper target and rel attributes.
 *
 * Checks if the URL is internal or external. Adds a `target="_blank"` for external urls. Inspired by
 * <http://stackoverflow.com/a/25090564/1059980>. To catch a security vulnerability, the attribute
 * `rel="noopener noreferrer"` is added, see <https://mathiasbynens.github.io/rel-noopener/> for more info.
 *
 * @since 1.0.0
 * @example
 * ```php
 * <a <?php echo get_link_attributes( 'https://www.mind.ch/blog' ); ?>>MIND Blog</a>
 * ```
 *
 * ```twig
 * <a {{ get_link_attributes(url) }}>MIND Blog</a>
 * ```
 *
 * @param string $url URL to be parsed.
 *
 * @return string|bool An href attribute with target and rel attributes, when necessary. Returns `false` if input
 *                     is empty.
 */
function get_link_attributes($url)
{
    // Bail out if parameter URL is empty
    if (empty($url)) {
        return false;
    }

    if (is_external_url($url)) {
        return 'href="' . $url . '" target="_blank" rel="noopener noreferrer"';
    } else {
        return 'href="' . $url . '"';
    }
}
