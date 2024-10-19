=== Style Genre ===
Contributors: janiko
Tags: translation, gettext, inclusion
Requires at least: 4.7
Requires PHP: 7.0
Tested up to: 6.6.2
Stable tag: 1.3.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

This plugin helps you to modify/override some parts of the translations. 

== Description ==

Cette extension vous permet de modifier certaines parties des traductions pour les mettre à votre goût. Vous n'aimez pas l'écriture inclusive ? Le mot 'autrice' vous vrille les tympas ? Cette extension est faite pour vous !

Malheureusement, WordPress utilise de plus en plus de JavaScript, et cette extension n'agit que sur ce qui est généré par PHP. De nombreuses occurrences ne seront donc pas transformées (celles qui sont issues des parties en JS). 

This plugin helps you to modify/override some parts of the translations. Unfortunately, WordPress uses more JS than before, and the plugin affects only the PHP-generated parts. 

== Installation ==

Obtenez l'extension via le menu d'administration, et activez-le. Dans la partie administration, vous pourrez choisir quelle partie de la traduction (msgid) vous souhaitez modifier (vous pouvez en mettre plusieurs). 

La substitution se fera soit par activation manuelle, soit à chaque mise-à-jour des fichiers de traduction.

Get the plugin and activate it. In the admin section, you can choose which translation (msgid) you want to override, and the remplacement string to use. It's an array, so you can put more than one string.

The substitution will occur every time the translations are updated, or you can force it manually (see the button in the screenshot).

== Screenshots ==

1. Exemple de paramétrage (settings example)

== Frequently Asked Questions ==

Let me know if you have some. I will add them here!

= Does the plugin handles text domains?
No. The option panel would be too complex. And generally, the same text should be translated in the same way in every domain.

= Are the CR/LF supported? =
Well, not for now. Please avoid using msgid with CR/LF, it would really complexify the plugin.

== Changelog ==

= 1.3.2 = 
* Complete rebuild

= 1.2.0 = 
* Rebuild and shell script added (cron)

= 1.1.1 =
* Some text added

= 1.0.1 =
* Fixed bug: option panel

= 1.0.0 =
* First minimal release
* Minimalist settings and features but fully functionnal (I hope)