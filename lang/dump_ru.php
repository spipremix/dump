<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/dump?lang_cible=ru
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucune_donnee' => 'пусто',
	'avis_probleme_ecriture_fichier' => 'Не удалось сохранить файл @fichier@',

	// B
	'bouton_restaurer_base' => 'Восстановление базы данных',

	// C
	'confirmer_ecraser_base' => 'Да, я хочу перезаписать всю текущую информацию данными из резервной копии',
	'confirmer_ecraser_tables_selection' => 'Да, я хочу заменить информацию в выбранных таблицах данными из резервной копии.',

	// D
	'details_sauvegarde' => 'Информация о резервной копии :',

	// E
	'erreur_aucune_donnee_restauree' => 'Не удалось восстановить информацию',
	'erreur_connect_dump' => 'Сервер « @dump@ » уже существует. Переименуйте его.',
	'erreur_creation_base_sqlite' => 'Не удалось создать SQLite базу для бэкапа',
	'erreur_nom_fichier' => 'Данное называние файла не разрешено',
	'erreur_restaurer_verifiez' => 'Исправьте ошибки для того, что бы продолжить восстановление.',
	'erreur_sauvegarde_deja_en_cours' => 'Резервная копия уже создается',
	'erreur_sqlite_indisponible' => 'Не получается сделать резервную копию SQLite на вашем хостинге',
	'erreur_table_absente' => 'Не хватает таблицы @table@ ',
	'erreur_table_donnees_manquantes' => 'В таблице @table@ нет информации',
	'erreur_taille_sauvegarde' => 'La sauvegarde semble avoir échoué. Le fichier @fichier@ est vide ou absent.', # NEW

	// I
	'info_aucune_sauvegarde_trouvee' => 'Aucune sauvegarde trouvée', # NEW
	'info_restauration_finie' => 'C\'est fini !. La sauvegarde @archive@ a été restaurée dans votre site. Vous pouvez', # NEW
	'info_restauration_sauvegarde' => 'Восстановление резервной копии  @archive@',
	'info_sauvegarde' => 'Резервная копия',
	'info_sauvegarde_reussi_02' => 'База данных была сохранена в @archive@. Вы можете',
	'info_sauvegarde_reussi_03' => 'вернуться к управлению',
	'info_sauvegarde_reussi_04' => 'из Вашего сайта.',
	'info_selection_sauvegarde' => 'Vous avez choisi de restaurer la sauvegarde @fichier@. Cette opération est irréversible.', # NEW

	// L
	'label_nom_fichier_restaurer' => 'Или укажите название файла резервной копии',
	'label_nom_fichier_sauvegarde' => 'Название файла резервной копии',
	'label_selectionnez_fichier' => 'Выбрать файл из списка',

	// N
	'nb_donnees' => '@nb@ записей',

	// R
	'restauration_en_cours' => 'Идет восстановление',

	// S
	'sauvegarde_en_cours' => 'Идет сохранение данных',
	'sauvegardes_existantes' => 'Резервные копии',
	'selectionnez_table_a_restaurer' => 'Выбрать таблицы для восстановления',

	// T
	'texte_admin_tech_01' => 'Вы можете сделать резервную копию базы данных.Файл будет сохранен в каталоге @dossier@.<br />Также не забудьте сделать копию папки @img@, которая содержит изображения и документы, используемые в статьях и разделах.',
	'texte_admin_tech_02' => 'Внимание: вы можете востаносить резервную копию только в той же версии SPIP, в какой вы ее создали. Подробнее в <a href="@spipnet@">документации</a>.',
	'texte_restaurer_base' => 'Восстановление из резервной копии',
	'texte_restaurer_sauvegarde' => 'Вы можете восстановить сайт из резервной копии. Для этого поместите файл с копией в папку @dossier@.


<b>Внимание:</b>  вся текущая информация будет заменена информацией с резервной копии. Эта операция необратима, если вы не уверены в том, что вы делаете - сделайте резервную копию перед началом восстановления. ',
	'texte_sauvegarde' => 'Резервное копирование базы данных',
	'texte_sauvegarde_base' => 'Резервное копирование базы данных',
	'tout_restaurer' => 'Восстановить все таблицы',
	'tout_sauvegarder' => 'Сохранить все таблицы',

	// U
	'une_donnee' => '1 запись'
);

?>
