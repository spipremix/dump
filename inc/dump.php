<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Repertoire de sauvegarde
 *
 * @return string
 */
function dump_repertoire() {
	$repertoire = _DIR_DUMP;
	if (!@file_exists($repertoire)
		AND !$repertoire = sous_repertoire(_DIR_DUMP,'',false,true)
	) {
		$repertoire = preg_replace(','._DIR_TMP.',', '', _DIR_DUMP);
		$repertoire = sous_repertoire(_DIR_TMP, $repertoire);
	}
	return $repertoire;
}


/**
 * Nom du fichier de sauvegarde
 * la fourniture de l'extension permet de verifier que le nom n'existe pas deja
 *
 * @param string $dir
 * @param string $extension
 * @return string
 */
function dump_nom_fichier($dir,$extension='sqlite'){
	$site = isset($GLOBALS['meta']['nom_site'])
	  ? preg_replace(array(",\W,is",",_(?=_),",",_$,"),array("_","",""), couper(translitteration(trim($GLOBALS['meta']['nom_site'])),30,""))
	  : 'spip';

	$site .= '_' . date('Ymd');

	$nom = $site;
	$cpt=0;
	while (file_exists($dir. $nom . ".$extension")) {
		$nom = $site . sprintf('_%03d', ++$cpt);
	}
	return $nom.".$extension";
}

/**
 * Determine le type de serveur de sauvegarde
 * sqlite2 ou sqlite3
 * 
 * @return string
 */
function dump_type_serveur() {

	// chercher si sqlite2 ou 3 est disponible
	include_spip('req/sqlite3');
	if (spip_versions_sqlite3())
		return 'sqlite3';

	include_spip('req/sqlite2');
	if (spip_versions_sqlite2())
		return 'sqlite2';

	return '';
}

/**
 * Conteneur pour les arguments de la connexion
 * si on passe $args, les arguments de la connexion sont memorises
 * renvoie toujours les derniers arguments memorises
 *
 * @staticvar array $connect_args
 * @param array $connect
 * @return array
 */
function dump_serveur($args=null) {
	static $connect_args = null;
	if ($args)
		$connect_args = $args;

	return $connect_args;
}

/**
 * Lister toutes les tables a copier depuis un serveur
 * en excluant eventuellement une liste fournie
 * 
 * @param string $serveur
 * @param array $exclude
 * @return array 
 */
function dump_lister_toutes_tables($serveur='', $exclude = array()) {
	list($tables,) = base_liste_table_for_dump($exclude);

	$connexion = $GLOBALS['connexions'][$serveur ? $serveur : 0];
	$prefixe = $connexion['prefixe'];

	$p = '/^' . $prefixe . '/';
	$res = $tables;
	foreach(sql_alltable(null,$serveur) as $t) {
		$t = preg_replace($p, 'spip', $t);
		if (!in_array($t, $tables) AND !in_array($t, $exclude)) $res[]= $t;
	}
	sort($res);
	return $res;
}

/**
 * Initialiser un dump
 * @param string $status_file
 * @param string $archive
 * @param array $tables
 * @param array $where
 * @return bool/string
 */
function dump_init($status_file, $archive, $tables=null, $where=array()){
	$status_file = _DIR_TMP.basename($status_file).".txt";

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

/**
 * Ecrire le js pour relancer la procedure de dump
 * @param string $redirect
 * @return string
 */
function dump_relance($redirect){
	// si Javascript est dispo, anticiper le Time-out
	return "<script language=\"JavaScript\" type=\"text/javascript\">window.setTimeout('location.href=\"$redirect\";',300);</script>\n";
}


/**
 * Marquer la procedure de dump comme finie
 * @param string $status_file
 * @return <type>
 */
function dump_end($status_file){
	$status_file = _DIR_TMP.basename($status_file).".txt";
	if (!lire_fichier($status_file, $status)
		OR !$status = unserialize($status))
		return;
	$status['etape'] = 'fini';
	ecrire_fichier($status_file, serialize($status));
}

?>