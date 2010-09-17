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
	return $nom;
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


?>