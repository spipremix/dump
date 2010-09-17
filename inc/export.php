<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/dump');

/**
 * Initialiser un export
 * @param string $status_file
 * @param string $archive
 * @param array $tables
 * @param array $where
 * @return bool/string
 */
function export_init($status_file, $archive, $tables=null, $where=array()){	
	$status_file = _DIR_TMP.basename($status_file).".php";

	if (lire_fichier($status_file, $status)
		AND $status = unserialize($status)
		AND $status['etape']!=='fini'
		AND filemtime($status_file)>=time()-120) // si le fichier status est trop vieux c'est un abandon
		return _T('dump:erreur_sauvegarde_deja_en_cours');

	if (!$type_serveur = dump_type_serveur())
		return _T('erreur_sqlite_indisponible');

	if (!$tables)
		list($tables,) = base_liste_table_for_dump(lister_tables_noexport());
	$status = array('tables'=>$tables,'where'=>$where,'archive'=>$archive);

	$status['connect'] = array(dirname($archive), '', '', '', basename($archive), $type_serveur, 'spip');
	dump_serveur($status['connect']);
	if (!spip_connect('dump'))
		return _T('dump:erreur_creation_base_sqlite');

	// la constante sert a verifier qu'on utilise bien le connect/dump du plugin,
	// et pas une base externe homonyme
	if (!defined('_DUMP_SERVEUR_OK'))
		return _T('erreur_connect_dump', array('dump' => 'dump'));

	$status['etape'] = 'init';
	
	if (!ecrire_fichier($status_file, serialize($status)))
		return _T('dump:avis_probleme_ecriture_fichier',array('fichier'=>$status_file));

	return true;
}

function export_relance($redirect){
	// si Javascript est dispo, anticiper le Time-out
	return "<script language=\"JavaScript\" type=\"text/javascript\">window.setTimeout('location.href=\"$redirect\";',0);</script>\n";
}

/**
 * Marquer la sauvegarde comme finie
 * @param string $status_file
 * @return <type>
 */
function export_end($status_file){
	$status_file = _DIR_TMP.basename($status_file).".php";
	if (!lire_fichier($status_file, $status)
		OR !$status = unserialize($status))
		return;
	$status['etape'] = 'fini';
	ecrire_fichier($status_file, serialize($status));
}

/**
 * Afficher l'avancement de la copie
 * @staticvar int $etape
 * @param <type> $courant
 * @param <type> $total
 * @param <type> $table
 */
function export_afficher_progres($courant,$total,$table) {
	static $etape = 1;
	if (unique($table)) {
		if ($total<0)
			echo "<br /><strong>".$etape. '. '."</strong>$table (". (-$total).")";
		else
			echo "<br /><strong>".$etape. '. '."$table</strong> ";
		$etape++;
	}
	echo ". ";
	flush();
}

// http://doc.spip.org/@exec_export_all_args
function inc_export_dist($status_file, $redirect='') {
	$status_file = _DIR_TMP.basename($status_file).".php";
	if (!lire_fichier($status_file, $status)
		OR !$status = unserialize($status)) {
	}
	else {		
		$timeout = ini_get('max_execution_time');
		// valeur conservatrice si on a pas reussi a lire le max_execution_time
		if (!$timeout) $timeout=30; // parions sur une valeur tellement courante ...
		$max_time = time()+$timeout/2;
		
		include_spip('inc/minipres');
		@ini_set("zlib.output_compression","0"); // pour permettre l'affichage au fur et a mesure

		echo ( install_debut_html(_T('info_sauvegarde') . " (".count($status['tables']).")"));
		// script de rechargement auto sur timeout
		echo http_script("window.setTimeout('location.href=\"".$redirect."\";',".($timeout*1000).")");
		echo "<div style='text-align: left'>\n";

		dump_serveur($status['connect']);
		spip_connect('dump');

		$res = base_copier_tables($status_file, $status['tables'], '', 'dump', 'export_afficher_progres', $max_time, false, lister_tables_noerase(),$status['where']?$status['where']:array());
		echo ( "</div>\n");

		if (!$res AND $redirect)
			echo export_relance($redirect);
		echo (install_fin_html());
		ob_end_flush();
		flush();

		return $res;
	}
}


?>
