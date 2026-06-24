<?php
/**
 * Plugin Name: HandAIMan Follow & Subscribe
 * Description: Clean podcast subscription and social follow links for TheHandAIMan. Use [handaiman_follow].
 * Version: 0.1.2
 * Author: HandAIMan / ChatGPT
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) { exit; }

class HandAIMan_Follow_Subscribe_Plugin {
    const VERSION = '0.1.2';
    const OPTION_KEY = 'ha_follow_options';
    const MENU_SLUG = 'handaiman-follow';

    public static function init() {
        register_activation_hook(__FILE__, array(__CLASS__, 'activate'));
        add_action('admin_menu', array(__CLASS__, 'admin_menu'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));

        add_shortcode('handaiman_follow', array(__CLASS__, 'shortcode'));
        add_shortcode('ha_follow', array(__CLASS__, 'shortcode'));
        add_shortcode('handaiman_podcast_links', array(__CLASS__, 'podcast_shortcode'));
        add_shortcode('handaiman_social_links', array(__CLASS__, 'social_shortcode'));

        add_filter('the_content', array(__CLASS__, 'maybe_auto_append'), 10);
    }

    public static function activate() {
        self::options();
    }

    private static function defaults() {
        return array(
            'global' => array(
                'heading' => 'Subscribe / Follow',
                'intro' => 'Get new episodes, posts, and bad decisions before the algorithm buries them.',
                'collapsed_summary' => 'Subscribe / Follow',
                'layout' => 'full',
                'show_descriptions' => 1,
                'open_new_tab' => 1,
                'auto_append_posts' => 0,
                'auto_append_podcasts' => 0,
                'auto_append_collapsed' => 1,
                'auto_append_open' => 0,
                'show_podcast' => 1,
                'show_social' => 1,
                'podcast_heading' => 'Podcast',
                'social_heading' => 'Social',
            ),
            'podcast' => array(
                'apple' => array(
                    'enabled' => 0,
                    'order' => 10,
                    'label' => 'Apple Podcasts',
                    'url' => '',
                    'description' => 'Subscribe on Apple Podcasts.',
                ),
                'spotify' => array(
                    'enabled' => 0,
                    'order' => 20,
                    'label' => 'Spotify',
                    'url' => '',
                    'description' => 'Follow the show on Spotify.',
                ),
                'youtube_podcast' => array(
                    'enabled' => 0,
                    'order' => 30,
                    'label' => 'YouTube',
                    'url' => '',
                    'description' => 'Watch or subscribe on YouTube.',
                ),
                'rss' => array(
                    'enabled' => 1,
                    'order' => 40,
                    'label' => 'Podcast RSS',
                    'url' => home_url('/feed/podcast'),
                    'description' => 'Direct podcast RSS feed for podcast apps.',
                ),
                'episodes' => array(
                    'enabled' => 1,
                    'order' => 50,
                    'label' => 'Episode List',
                    'url' => home_url('/ssp-podcast-archive/'),
                    'description' => 'Browse the full episode archive.',
                ),
                'amazon' => array(
                    'enabled' => 0,
                    'order' => 60,
                    'label' => 'Amazon Music',
                    'url' => '',
                    'description' => 'Listen on Amazon Music.',
                ),
                'pocketcasts' => array(
                    'enabled' => 0,
                    'order' => 70,
                    'label' => 'Pocket Casts',
                    'url' => '',
                    'description' => 'Subscribe in Pocket Casts.',
                ),
                'overcast' => array(
                    'enabled' => 0,
                    'order' => 80,
                    'label' => 'Overcast',
                    'url' => '',
                    'description' => 'Subscribe in Overcast.',
                ),
                'podcastaddict' => array(
                    'enabled' => 0,
                    'order' => 90,
                    'label' => 'Podcast Addict',
                    'url' => '',
                    'description' => 'Subscribe in Podcast Addict.',
                ),
                'iheart' => array(
                    'enabled' => 0,
                    'order' => 100,
                    'label' => 'iHeart',
                    'url' => '',
                    'description' => 'Listen on iHeart.',
                ),
                'custom_podcast_1' => array(
                    'enabled' => 0,
                    'order' => 190,
                    'label' => 'Custom Podcast Link 1',
                    'url' => '',
                    'description' => '',
                ),
                'custom_podcast_2' => array(
                    'enabled' => 0,
                    'order' => 200,
                    'label' => 'Custom Podcast Link 2',
                    'url' => '',
                    'description' => '',
                ),
            ),
            'social' => array(
                'youtube' => array(
                    'enabled' => 0,
                    'order' => 10,
                    'label' => 'YouTube',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Subscribe on YouTube.',
                ),
                'x' => array(
                    'enabled' => 0,
                    'order' => 20,
                    'label' => 'X / Twitter',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on X.',
                ),
                'bluesky' => array(
                    'enabled' => 0,
                    'order' => 30,
                    'label' => 'Bluesky',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on Bluesky.',
                ),
                'rumble' => array(
                    'enabled' => 0,
                    'order' => 40,
                    'label' => 'Rumble',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on Rumble.',
                ),
                'odysee' => array(
                    'enabled' => 0,
                    'order' => 50,
                    'label' => 'Odysee',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on Odysee.',
                ),
                'facebook' => array(
                    'enabled' => 0,
                    'order' => 60,
                    'label' => 'Facebook',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on Facebook.',
                ),
                'instagram' => array(
                    'enabled' => 0,
                    'order' => 70,
                    'label' => 'Instagram',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on Instagram.',
                ),
                'tiktok' => array(
                    'enabled' => 0,
                    'order' => 80,
                    'label' => 'TikTok',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on TikTok.',
                ),
                'telegram' => array(
                    'enabled' => 0,
                    'order' => 90,
                    'label' => 'Telegram',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Join or follow on Telegram.',
                ),
                'substack' => array(
                    'enabled' => 0,
                    'order' => 100,
                    'label' => 'Substack',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on Substack.',
                ),
                'nostr' => array(
                    'enabled' => 0,
                    'order' => 110,
                    'label' => 'Nostr',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on Nostr.',
                ),
                'github' => array(
                    'enabled' => 0,
                    'order' => 120,
                    'label' => 'GitHub',
                    'handle' => '',
                    'url' => '',
                    'description' => 'Follow on GitHub.',
                ),
                'custom_social_1' => array(
                    'enabled' => 0,
                    'order' => 190,
                    'label' => 'Custom Social Link 1',
                    'handle' => '',
                    'url' => '',
                    'description' => '',
                ),
                'custom_social_2' => array(
                    'enabled' => 0,
                    'order' => 200,
                    'label' => 'Custom Social Link 2',
                    'handle' => '',
                    'url' => '',
                    'description' => '',
                ),
                'custom_social_3' => array(
                    'enabled' => 0,
                    'order' => 210,
                    'label' => 'Custom Social Link 3',
                    'handle' => '',
                    'url' => '',
                    'description' => '',
                ),
            ),
        );
    }

    private static function recursive_merge($defaults, $saved) {
        if (!is_array($saved)) { return $defaults; }
        foreach ($defaults as $key => $value) {
            if (is_array($value)) {
                $saved[$key] = self::recursive_merge($value, isset($saved[$key]) ? $saved[$key] : array());
            } elseif (!array_key_exists($key, $saved)) {
                $saved[$key] = $value;
            }
        }
        return $saved;
    }

    private static function options() {
        $defaults = self::defaults();
        $saved = get_option(self::OPTION_KEY, array());
        if (!is_array($saved)) { $saved = array(); }
        $merged = self::recursive_merge($defaults, $saved);
        if ($merged !== $saved) {
            update_option(self::OPTION_KEY, $merged);
        }
        return $merged;
    }

    public static function register_settings() {
        register_setting('ha_follow_settings', self::OPTION_KEY, array(__CLASS__, 'sanitize_options'));
    }

    public static function sanitize_options($input) {
        $defaults = self::defaults();
        if (!is_array($input)) { $input = array(); }
        $out = $defaults;

        $global = isset($input['global']) && is_array($input['global']) ? $input['global'] : array();
        $global_text = array('heading', 'collapsed_summary', 'layout', 'podcast_heading', 'social_heading');
        foreach ($global_text as $field) {
            if (isset($global[$field])) {
                $out['global'][$field] = sanitize_text_field(wp_unslash($global[$field]));
            }
        }
        if (!in_array($out['global']['layout'], array('full', 'compact', 'buttons'), true)) {
            $out['global']['layout'] = 'full';
        }
        $out['global']['intro'] = isset($global['intro']) ? sanitize_textarea_field(wp_unslash($global['intro'])) : $defaults['global']['intro'];
        foreach (array('show_descriptions', 'open_new_tab', 'auto_append_posts', 'auto_append_podcasts', 'auto_append_collapsed', 'auto_append_open', 'show_podcast', 'show_social') as $checkbox) {
            $out['global'][$checkbox] = empty($global[$checkbox]) ? 0 : 1;
        }

        foreach (array('podcast', 'social') as $section) {
            $rows = isset($input[$section]) && is_array($input[$section]) ? $input[$section] : array();
            foreach ($defaults[$section] as $key => $row_defaults) {
                $row = isset($rows[$key]) && is_array($rows[$key]) ? $rows[$key] : array();
                $out[$section][$key]['enabled'] = empty($row['enabled']) ? 0 : 1;
                $out[$section][$key]['order'] = isset($row['order']) ? intval($row['order']) : intval($row_defaults['order']);
                $out[$section][$key]['label'] = isset($row['label']) ? sanitize_text_field(wp_unslash($row['label'])) : $row_defaults['label'];
                $out[$section][$key]['url'] = isset($row['url']) ? esc_url_raw(trim(wp_unslash($row['url']))) : $row_defaults['url'];
                $out[$section][$key]['description'] = isset($row['description']) ? sanitize_textarea_field(wp_unslash($row['description'])) : $row_defaults['description'];
                if ($section === 'social') {
                    $out[$section][$key]['handle'] = isset($row['handle']) ? sanitize_text_field(wp_unslash($row['handle'])) : $row_defaults['handle'];
                }
            }
        }

        return $out;
    }

    public static function admin_menu() {
        if (function_exists('handaistack_parent_slug')) {
            add_submenu_page(
                handaistack_parent_slug(),
                'HandAIMan Follow & Subscribe',
                'Follow',
                'manage_options',
                'handaiman-follow',
                array(__CLASS__, 'admin_settings_page')
            );
        } else {
            add_menu_page(
                'HandAIMan Follow & Subscribe',
                'HandAIMan Follow',
                'manage_options',
                'handaiman-follow',
                array(__CLASS__, 'admin_settings_page'),
                'dashicons-admin-generic',
                58
            );
        }
    }

    public static function admin_settings_page() {
        if (!current_user_can('manage_options')) { return; }
        $opts = self::options();
        ?>
        <div class="wrap">
            <h1>HandAIMan Follow &amp; Subscribe</h1>
            <p>Use <code>[handaiman_follow]</code> or <code>[ha_follow]</code>. Podcast-only: <code>[handaiman_podcast_links]</code>. Social-only: <code>[handaiman_social_links]</code>.</p>
            <form method="post" action="options.php">
                <?php settings_fields('ha_follow_settings'); ?>
                <?php submit_button('Save Follow Settings', 'primary', 'submit', false); ?>

                <h2>General Display</h2>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="ha_follow_heading">Heading</label></th>
                        <td><input id="ha_follow_heading" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][heading]" type="text" class="regular-text" value="<?php echo esc_attr($opts['global']['heading']); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ha_follow_intro">Intro text</label></th>
                        <td><textarea id="ha_follow_intro" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][intro]" rows="3" class="large-text"><?php echo esc_textarea($opts['global']['intro']); ?></textarea></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ha_follow_collapsed_summary">Collapsed summary</label></th>
                        <td><input id="ha_follow_collapsed_summary" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][collapsed_summary]" type="text" class="regular-text" value="<?php echo esc_attr($opts['global']['collapsed_summary']); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ha_follow_layout">Default layout</label></th>
                        <td>
                            <select id="ha_follow_layout" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][layout]">
                                <?php foreach (array('full' => 'Full', 'compact' => 'Compact', 'buttons' => 'Buttons only') as $value => $label) : ?>
                                    <option value="<?php echo esc_attr($value); ?>" <?php selected($opts['global']['layout'], $value); ?>><?php echo esc_html($label); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Display options</th>
                        <td>
                            <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][show_podcast]" value="1" <?php checked(!empty($opts['global']['show_podcast'])); ?>> Show podcast section</label><br>
                            <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][show_social]" value="1" <?php checked(!empty($opts['global']['show_social'])); ?>> Show social section</label><br>
                            <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][show_descriptions]" value="1" <?php checked(!empty($opts['global']['show_descriptions'])); ?>> Show descriptions</label><br>
                            <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][open_new_tab]" value="1" <?php checked(!empty($opts['global']['open_new_tab'])); ?>> Open links in a new tab</label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Auto-append</th>
                        <td>
                            <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][auto_append_posts]" value="1" <?php checked(!empty($opts['global']['auto_append_posts'])); ?>> Auto-append to blog posts</label><br>
                            <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][auto_append_podcasts]" value="1" <?php checked(!empty($opts['global']['auto_append_podcasts'])); ?>> Auto-append to podcast episodes</label><br>
                            <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][auto_append_collapsed]" value="1" <?php checked(!empty($opts['global']['auto_append_collapsed'])); ?>> Render auto-appended box collapsed by default</label><br>
                            <label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][auto_append_open]" value="1" <?php checked(!empty($opts['global']['auto_append_open'])); ?>> Open auto-appended collapsed box by default</label>
                            <p class="description">Auto-append runs before the Support and Contact plugin appenders, producing the desired order: Subscribe/Follow, Support, Contact.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ha_follow_podcast_heading">Podcast section heading</label></th>
                        <td><input id="ha_follow_podcast_heading" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][podcast_heading]" type="text" class="regular-text" value="<?php echo esc_attr($opts['global']['podcast_heading']); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ha_follow_social_heading">Social section heading</label></th>
                        <td><input id="ha_follow_social_heading" name="<?php echo esc_attr(self::OPTION_KEY); ?>[global][social_heading]" type="text" class="regular-text" value="<?php echo esc_attr($opts['global']['social_heading']); ?>"></td>
                    </tr>
                </table>

                <?php self::render_admin_rows('podcast', 'Podcast Subscription Links', $opts['podcast']); ?>
                <?php self::render_admin_rows('social', 'Social Follow Links', $opts['social']); ?>

                <?php submit_button('Save Follow Settings'); ?>
            </form>
        </div>
        <?php
    }

    private static function render_admin_rows($section, $title, $rows) {
        ?>
        <h2><?php echo esc_html($title); ?></h2>
        <p>Enable the links you want displayed. Use the order number to rearrange them.</p>
        <table class="widefat striped" style="max-width:1200px;">
            <thead>
                <tr>
                    <th style="width:70px;">Enabled</th>
                    <th style="width:80px;">Order</th>
                    <th style="width:190px;">Label</th>
                    <?php if ($section === 'social') : ?><th style="width:150px;">Handle</th><?php endif; ?>
                    <th>URL</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($rows as $key => $row) : ?>
                <tr>
                    <td><input type="checkbox" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>][enabled]" value="1" <?php checked(!empty($row['enabled'])); ?>></td>
                    <td><input type="number" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>][order]" value="<?php echo esc_attr(intval($row['order'])); ?>" style="width:80px;"></td>
                    <td><input type="text" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>][label]" value="<?php echo esc_attr($row['label']); ?>" class="regular-text"></td>
                    <?php if ($section === 'social') : ?>
                        <td><input type="text" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>][handle]" value="<?php echo esc_attr(isset($row['handle']) ? $row['handle'] : ''); ?>" placeholder="@handle" style="width:140px;"></td>
                    <?php endif; ?>
                    <td><input type="url" name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>][url]" value="<?php echo esc_url($row['url']); ?>" class="large-text" placeholder="https://..."></td>
                    <td><textarea name="<?php echo esc_attr(self::OPTION_KEY); ?>[<?php echo esc_attr($section); ?>][<?php echo esc_attr($key); ?>][description]" rows="2" class="large-text"><?php echo esc_textarea($row['description']); ?></textarea></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

    public static function podcast_shortcode($atts = array()) {
        $atts = shortcode_atts(array(
            'layout' => '',
            'collapsed' => 'no',
            'open' => 'no',
            'summary' => '',
            'platforms' => '',
        ), $atts, 'handaiman_podcast_links');
        $atts['podcast'] = 'yes';
        $atts['social'] = 'no';
        return self::shortcode($atts);
    }

    public static function social_shortcode($atts = array()) {
        $atts = shortcode_atts(array(
            'layout' => '',
            'collapsed' => 'no',
            'open' => 'no',
            'summary' => '',
            'platforms' => '',
        ), $atts, 'handaiman_social_links');
        $atts['podcast'] = 'no';
        $atts['social'] = 'yes';
        return self::shortcode($atts);
    }

    public static function shortcode($atts = array()) {
        $opts = self::options();
        $atts = shortcode_atts(array(
            'layout' => '',
            'collapsed' => 'no',
            'open' => 'no',
            'summary' => '',
            'podcast' => '',
            'social' => '',
            'platforms' => '',
            'show_descriptions' => '',
        ), $atts, 'handaiman_follow');

        $layout = $atts['layout'] ? sanitize_key($atts['layout']) : $opts['global']['layout'];
        if (!in_array($layout, array('full', 'compact', 'buttons'), true)) { $layout = 'full'; }
        $show_podcast = $atts['podcast'] === '' ? !empty($opts['global']['show_podcast']) : self::truthy($atts['podcast']);
        $show_social = $atts['social'] === '' ? !empty($opts['global']['show_social']) : self::truthy($atts['social']);
        $show_descriptions = $atts['show_descriptions'] === '' ? !empty($opts['global']['show_descriptions']) : self::truthy($atts['show_descriptions']);
        if ($layout === 'buttons') { $show_descriptions = false; }
        $allowed = self::parse_csv_keys($atts['platforms']);

        $inner = self::render_follow_inner($opts, array(
            'layout' => $layout,
            'show_podcast' => $show_podcast,
            'show_social' => $show_social,
            'show_descriptions' => $show_descriptions,
            'allowed' => $allowed,
        ));
        if ($inner === '') { return ''; }

        $collapsed = self::truthy($atts['collapsed']);
        if (!$collapsed) { return $inner; }

        $summary = $atts['summary'] !== '' ? sanitize_text_field($atts['summary']) : $opts['global']['collapsed_summary'];
        $open_attr = self::truthy($atts['open']) ? ' open' : '';
        return '<details class="ha-follow-details"' . $open_attr . ' style="border:1px solid #dcdcde; padding:16px; border-radius:8px; max-width:760px; margin:1em 0; background:#fff; box-sizing:border-box;"><summary style="cursor:pointer; font-weight:600; font-size:1.05em;">' . esc_html($summary) . '</summary><div class="ha-follow-details-inner" style="margin-top:14px;">' . $inner . '</div></details>' . self::inline_css();
    }

    private static function render_follow_inner($opts, $args) {
        $sections = array();
        if (!empty($args['show_podcast'])) {
            $podcast = self::render_link_section($opts['podcast'], $opts['global']['podcast_heading'], $args);
            if ($podcast) { $sections[] = $podcast; }
        }
        if (!empty($args['show_social'])) {
            $social = self::render_link_section($opts['social'], $opts['global']['social_heading'], $args);
            if ($social) { $sections[] = $social; }
        }
        if (!$sections) { return ''; }

        $classes = 'ha-follow-box ha-follow-layout-' . sanitize_html_class($args['layout']);
        ob_start();
        ?>
        <div class="<?php echo esc_attr($classes); ?>" style="border:1px solid #dcdcde; padding:16px; border-radius:8px; max-width:760px; margin:1em 0; background:#fff; box-sizing:border-box;">
            <?php if ($args['layout'] !== 'buttons') : ?>
                <h3 class="ha-follow-heading" style="margin-top:0;"><?php echo esc_html($opts['global']['heading']); ?></h3>
                <?php if (trim($opts['global']['intro']) !== '') : ?>
                    <p class="ha-follow-intro" style="margin-bottom:1em;"><?php echo esc_html($opts['global']['intro']); ?></p>
                <?php endif; ?>
            <?php endif; ?>
            <?php echo implode("\n", $sections); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
        </div>
        <?php
        return ob_get_clean() . self::inline_css();
    }

    private static function render_link_section($rows, $heading, $args) {
        $links = self::enabled_sorted_links($rows, $args['allowed']);
        if (!$links) { return ''; }
        ob_start();
        ?>
        <div class="ha-follow-section" style="margin-top:1em;">
            <?php if ($args['layout'] === 'full' && trim($heading) !== '') : ?>
                <h4 class="ha-follow-section-heading"><?php echo esc_html($heading); ?></h4>
            <?php endif; ?>
            <div class="ha-follow-buttons" style="display:flex; flex-wrap:wrap; gap:8px;">
                <?php foreach ($links as $key => $row) : ?>
                    <?php echo self::render_link_button($row, $args['show_descriptions']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php endforeach; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private static function render_link_button($row, $show_description) {
        $url = isset($row['url']) ? trim($row['url']) : '';
        if ($url === '') { return ''; }
        $opts = self::options();
        $target = !empty($opts['global']['open_new_tab']) ? ' target="_blank" rel="noopener noreferrer"' : '';
        $label = isset($row['label']) && $row['label'] !== '' ? $row['label'] : 'Follow';
        $description = isset($row['description']) ? trim($row['description']) : '';
        $handle = isset($row['handle']) ? trim($row['handle']) : '';
        ob_start();
        ?>
        <a class="ha-follow-button" href="<?php echo esc_url($url); ?>"<?php echo $target; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> style="display:inline-flex; flex-direction:column; justify-content:center; border:1px solid #dcdcde; border-radius:6px; padding:10px 12px; min-width:130px; text-decoration:none!important; background:#f6f7f7; color:inherit; box-sizing:border-box;">
            <span class="ha-follow-button-label" style="font-weight:600;"><?php echo esc_html($label); ?></span>
            <?php if ($handle !== '') : ?><span class="ha-follow-handle" style="font-size:0.88em; opacity:0.82;"><?php echo esc_html($handle); ?></span><?php endif; ?>
            <?php if ($show_description && $description !== '') : ?><span class="ha-follow-description" style="font-size:0.88em; opacity:0.82;"><?php echo esc_html($description); ?></span><?php endif; ?>
        </a>
        <?php
        return ob_get_clean();
    }

    private static function enabled_sorted_links($rows, $allowed = array()) {
        $links = array();
        foreach ($rows as $key => $row) {
            if (!empty($allowed) && !in_array($key, $allowed, true)) { continue; }
            if (empty($row['enabled'])) { continue; }
            if (empty($row['url'])) { continue; }
            $links[$key] = $row;
        }
        uasort($links, function($a, $b) {
            $ao = isset($a['order']) ? intval($a['order']) : 0;
            $bo = isset($b['order']) ? intval($b['order']) : 0;
            if ($ao === $bo) { return strcmp((string) $a['label'], (string) $b['label']); }
            return $ao <=> $bo;
        });
        return $links;
    }

    public static function maybe_auto_append($content) {
        if (is_admin() || !is_singular() || !in_the_loop() || !is_main_query()) { return $content; }
        $opts = self::options();
        $post_type = get_post_type();
        $should = false;
        if ($post_type === 'post' && !empty($opts['global']['auto_append_posts'])) { $should = true; }
        if ($post_type === 'podcast' && !empty($opts['global']['auto_append_podcasts'])) { $should = true; }
        if (!$should) { return $content; }

        $shortcode = '[handaiman_follow layout="compact"';
        if (!empty($opts['global']['auto_append_collapsed'])) {
            $shortcode .= ' collapsed="yes"';
            if (!empty($opts['global']['auto_append_open'])) { $shortcode .= ' open="yes"'; }
        }
        $shortcode .= ']';

        return $content . "\n\n" . do_shortcode($shortcode);
    }

    private static function parse_csv_keys($value) {
        $value = trim((string) $value);
        if ($value === '') { return array(); }
        $parts = array_map('sanitize_key', array_map('trim', explode(',', $value)));
        return array_values(array_filter($parts));
    }

    private static function truthy($value) {
        if (is_bool($value)) { return $value; }
        $value = strtolower(trim((string) $value));
        return in_array($value, array('1', 'yes', 'true', 'on', 'open'), true);
    }

    private static function inline_css() {
        static $done = false;
        if ($done) { return ''; }
        $done = true;
        ob_start();
        ?>
        <style>
        .ha-follow-box,
        .ha-follow-details {
            border: 1px solid #dcdcde;
            padding: 16px;
            border-radius: 8px;
            max-width: 760px;
            margin: 1em 0;
            background: #fff;
            box-sizing: border-box;
        }
        .ha-follow-details > summary {
            cursor: pointer;
            font-weight: 600;
            font-size: 1.05em;
        }
        .ha-follow-details-inner {
            margin-top: 14px;
        }
        .ha-follow-heading {
            margin-top: 0;
        }
        .ha-follow-intro {
            margin-bottom: 1em;
        }
        .ha-follow-section {
            margin-top: 1em;
        }
        .ha-follow-section:first-of-type {
            margin-top: 0.5em;
        }
        .ha-follow-section-heading {
            margin: 0 0 0.5em;
        }
        .ha-follow-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .ha-follow-button {
            display: inline-flex;
            flex-direction: column;
            justify-content: center;
            border: 1px solid #dcdcde;
            border-radius: 6px;
            padding: 10px 12px;
            min-width: 130px;
            text-decoration: none !important;
            background: #f6f7f7;
            color: inherit;
            box-sizing: border-box;
        }
        .ha-follow-button:hover,
        .ha-follow-button:focus {
            background: #fff;
            border-color: currentColor;
        }
        .ha-follow-button-label {
            font-weight: 600;
        }
        .ha-follow-handle,
        .ha-follow-description {
            font-size: 0.85em;
            opacity: 0.78;
            margin-top: 2px;
        }
        .ha-follow-layout-compact .ha-follow-description {
            display: none;
        }
        .ha-follow-layout-buttons {
            padding: 0;
            border: 0;
            background: transparent;
        }
        @media (max-width: 600px) {
            .ha-follow-buttons { display: block; }
            .ha-follow-button { display: flex; width: 100%; margin-top: 8px; }
        }
        </style>
        <?php
        return ob_get_clean();
    }
}

HandAIMan_Follow_Subscribe_Plugin::init();
