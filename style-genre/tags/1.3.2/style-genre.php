<?php

/**
 * Style Genre
 *
 * @package           StyleGenre
 * @author            janiko
 * @copyright         2021-2024
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Style Genre
 * Plugin URI:        https://profiles.wordpress.org/janiko/#content-plugins
 * Description:       This extension helps you to modify some parts of the translations.
 * Version:           1.3.2
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            janiko
 * Author URI:        https://geba.fr
 * Text Domain:       style-genre
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

/**
 * Security checks (https://developer.wordpress.org/plugins/security/)
 *
 *   - Check user capabilitites: none required
 *   - Data validation: done
 *   - Sanitazing input: plugin's parameters to checkd
 *   - Sanitazing output: escaping output (from plugin's parameters)
 *   - Nonces: if needed, in the request handler
 */


// Sécurité : empêcher un accès direct au fichier.
if (!defined('ABSPATH')) {
    exit;
}

// Charger les fichiers de traduction pour le plugin
function style_genre_load_textdomain() {
    load_plugin_textdomain('style-genre', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}
add_action('plugins_loaded', 'style_genre_load_textdomain');

// Créer une page d'administration pour configurer les paramètres
function style_genre_menu() {
    add_menu_page(
        esc_attr__('Style Genre', 'style-genre'),
        esc_attr__('Style Genre', 'style-genre'),
        'manage_options',
        'style-genre-settings',
        'style_genre_settings_page',
        'dashicons-translation',
        90
    );
}
add_action('admin_menu', 'style_genre_menu');

// Affichage de la page d'administration pour configurer les substitutions
function style_genre_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['save_substitution_rules'])) {
        check_admin_referer('style_genre_save_settings');
		if (isset($_POST['substitution_rules'])) {
			$substitution_rules = sanitize_text_field(wp_unslash($_POST['substitution_rules']));
			update_option('style_genre_substitution_rules', $substitution_rules);
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_attr__('Substitution rules saved successfully!', 'style-genre') . '</p></div>';
		}
	}
		
    $substitution_rules = get_option('style_genre_substitution_rules', 'autrice|auteure, Autrice|Autrice');
    ?>
    <div class="wrap">
        <h1><?php esc_attr_e('Style Genre Settings', 'style-genre'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('style_genre_save_settings'); ?>
            <label for="substitution_rules"><?php esc_attr_e('Substitution Rules (e.g., "autrice|auteure"):', 'style-genre'); ?></label>
            <textarea id="substitution_rules" name="substitution_rules" rows="5" class="large-text"><?php echo esc_textarea($substitution_rules); ?></textarea>
            <p><?php esc_attr_e('Enter each substitution as "original_text|replacement_text" separated by commas.', 'style-genre'); ?></p>
            <input type="submit" name="save_substitution_rules" class="button-primary" value="<?php esc_attr_e('Save Changes', 'style-genre'); ?>">
        </form>

        <h2><?php esc_attr_e('Manual Substitution', 'style-genre'); ?></h2>
        <form method="post">
            <?php wp_nonce_field('execute_substitution_nonce'); ?>
            <input type="submit" name="execute_substitution" class="button-primary" value="<?php esc_attr_e('Run Substitution Now', 'style-genre'); ?>">
        </form>
    </div>
    <?php
}

// Fonction pour appliquer les substitutions sur les fichiers de traduction
function apply_style_genre_substitution() {
    // Initialiser le système de fichiers WordPress
    if (!function_exists('WP_Filesystem')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
    }

    // Prépare l'accès au WP_Filesystem global
    global $wp_filesystem;
    WP_Filesystem();

    // Récupérer les règles de substitution
    $substitution_rules = get_option('style_genre_substitution_rules', '');

    if (!empty($substitution_rules)) {
        $rules = array_map('trim', explode(',', $substitution_rules));

        // Récupérer les fichiers à modifier (.po, .mo, .php)
        $translation_files = glob(ABSPATH . 'wp-content/languages/*.{po,mo,php}', GLOB_BRACE);
        $theme_translation_files = glob(ABSPATH . 'wp-content/languages/themes/*.{po,mo,php}', GLOB_BRACE);

        // Appliquer les substitutions dans chaque fichier
        foreach (array_merge($translation_files, $theme_translation_files) as $file) {
            // Lire le contenu du fichier avec WP_Filesystem
            $content = $wp_filesystem->get_contents($file);
            if ($content === false) {
                continue; // Si la lecture échoue, passer au fichier suivant
            }

            // Appliquer les substitutions
            foreach ($rules as $rule) {
                list($search, $replace) = array_map('trim', explode('|', $rule));
                $content = str_replace($search, $replace, $content);
            }

            // Enregistrer les modifications avec WP_Filesystem
            $wp_filesystem->put_contents($file, $content);
        }
        return true;
    }
    return false;
}


// Ajouter un bouton pour exécuter la substitution manuellement
function add_manual_style_genre_button() {
    if (isset($_POST['execute_substitution'])) {
        check_admin_referer('execute_substitution_nonce');

        // Ajouter un message de débogage pour voir si la soumission est capturée
        if (apply_style_genre_substitution()) {
            echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Substitutions applied successfully!', 'style-genre') . '</p></div>';
        } else {
            echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('No substitutions applied. Please check your rules.', 'style-genre') . '</p></div>';
        }
    }
}
add_action('admin_notices', 'add_manual_style_genre_button');

// Hook pour déclencher la substitution après une mise à jour des traductions
function run_style_genre_substitution_after_translation_update($upgrader, $hook_extra) {
    if (isset($hook_extra['type']) && $hook_extra['type'] === 'translation') {
        apply_style_genre_substitution();
    }
}
add_action('upgrader_process_complete', 'run_style_genre_substitution_after_translation_update', 10, 2);
