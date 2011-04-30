<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucune_donnee' => 'vide', # NEW
	'avis_probleme_ecriture_fichier' => 'Problème d\'écriture du fichier @fichier@', # NEW

	// B
	'bouton_restaurer_base' => 'إسترجاع القاعدة',

	// C
	'confirmer_ecraser_base' => 'Oui, je veux écraser ma base avec cette sauvegarde', # NEW
	'confirmer_ecraser_tables_selection' => 'Oui, je veux écraser les tables sélectionnées avec cette sauvegarde', # NEW

	// D
	'details_sauvegarde' => 'Détails de la sauvegarde :', # NEW

	// E
	'erreur_aucune_donnee_restauree' => 'Aucune donnée restaurée', # NEW
	'erreur_connect_dump' => 'Un serveur nommé « @dump@ » existe déjà. Renommez-le.', # NEW
	'erreur_creation_base_sqlite' => 'Impossible de créer une base SQLite pour la sauvegarde', # NEW
	'erreur_nom_fichier' => 'Ce nom de fichier n\'est pas autorisé', # NEW
	'erreur_restaurer_verifiez' => 'Corrigez l\'erreur pour pouvoir restaurer.', # NEW
	'erreur_sauvegarde_deja_en_cours' => 'Vous avez déjà une sauvegarde en cours', # NEW
	'erreur_sqlite_indisponible' => 'Impossible de faire une sauvegarde SQLite sur votre hébergement', # NEW
	'erreur_table_absente' => 'Table @table@ absente', # NEW
	'erreur_table_donnees_manquantes' => 'Table @table@, données manquantes', # NEW
	'erreur_taille_sauvegarde' => 'La sauvegarde semble avoir échoué. Le fichier @fichier@ est vide ou absent.', # NEW

	// I
	'info_restauration_finie' => 'C\'est fini !. La sauvegarde @archive@ a été restaurée dans votre site. Vous pouvez', # NEW
	'info_restauration_sauvegarde' => 'إسترجاع النسخة الاحتياطية @archive@',
	'info_sauvegarde' => 'نسخة إحتياطية',
	'info_sauvegarde_reussi_02' => 'تم حفظ قاعدة البيانات في @archive@. يمكنك',
	'info_sauvegarde_reussi_03' => 'العودة الى إدارة',
	'info_sauvegarde_reussi_04' => 'موقعك.',
	'info_selection_sauvegarde' => 'Vous avez choisi de restaurer la sauvegarde @fichier@. Cette opération est irréversible.', # NEW

	// L
	'label_nom_fichier_restaurer' => 'Ou indiquez le nom du fichier à restaurer', # NEW
	'label_nom_fichier_sauvegarde' => 'Nom du fichier pour la sauvegarde', # NEW
	'label_selectionnez_fichier' => 'Sélectionnez un fichier dans la liste', # NEW

	// R
	'restauration_en_cours' => 'Restauration en cours', # NEW

	// S
	'sauvegarde_en_cours' => 'Sauvegarde en cours', # NEW
	'sauvegardes_existantes' => 'Sauvegardes existantes', # NEW
	'selectionnez_table_a_restaurer' => 'Sélectionnez les tables à restaurer', # NEW

	// T
	'texte_admin_tech_01' => 'يتيح لك هذا الخيار حفظمحتوى قاعدة البيانات في ملف يتم تخزينه في دليل @dossier@. لا تنسى أيضاً أن تسترد كامل دليل @img@، الذي يحتوي على الصور والمستندات المستخدمة في المقالات والأقسام.',
	'texte_admin_tech_02' => 'تحذير: لا يمكن إسترجاع هذه النسخة الاحتياطية إلا من خلال موقع تم تثبيته بالإصدار نفسه من SPIP . بالاخص لا يجب «تفريغ القاعدة» مع الامل بإعادة تثبيتها بعد التحديث... لمزيد من المعلومات راجع <a href="@spipnet@">دليل SPIP </a> .', # MODIF
	'texte_restaurer_base' => 'إسترجاع محتوى النسخة الاحتياطية من القاعدة',
	'texte_restaurer_sauvegarde' => 'يتيح لك هذا الخيار إسترجاع نسخة إحتياطية
سابقة من القاعدة. لتنفيذ ذلك، يجب على ملف النسخة الاحتياطية
ان يكون قد حفظ في دليل @dossier@.
توخى الحذر مع هذه الوظيفة: <b>اي تعديل أو فقدان في البيانات نهائي ولا يمكن
التراجع عنه</b>.',
	'texte_sauvegarde' => 'نسخ إحتياطي لمحتوى القاعدة',
	'texte_sauvegarde_base' => 'إنشاء نسخة إحتياطية من القاعدة',
	'tout_restaurer' => 'Restaurer toutes les tables', # NEW
	'tout_sauvegarder' => 'Sauvegarder toutes les tables' # NEW
);

?>
