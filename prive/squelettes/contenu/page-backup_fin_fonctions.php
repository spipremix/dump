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

function dump_lire_status($status_file) {
	$status_file = _DIR_TMP.basename($status_file).".php";
	if (!lire_fichier($status_file, $status)
		OR !$status = unserialize($status))
		return '';

	return $status;
}

function dump_verifie_sauvegarde_finie($status_file) {
	if (!$status=dump_lire_status($status_file)
	 OR $status['etape']!=='fini')
	 return '';
	return ' ';
}

function dump_nom_sauvegarde($status_file) {
	if (!$status=dump_lire_status($status_file)
	  OR !file_exists($f=$status['archive'].".sqlite"))
		return '';

	return $f;
}

function dump_taille_sauvegarde($status_file) {
	if (!$f=dump_nom_sauvegarde($status_file)
		OR !$s = filesize($f))
		return '';

	return $s;
}

function dump_date_sauvegarde($status_file) {
	if (!$f=dump_nom_sauvegarde($status_file)
		OR !$d = filemtime($f))
		return '';

	return date('Y-m-d',$d);
}

function dump_afficher_tables_sauvegardees($status_file) {
	$status = dump_lire_status($status_file);
	$tables = array_keys($status['tables_copiees']);
	$n = floor(count($tables)/2);
	$corps .= "<div style='width:49%;float:left;'><ul class='spip'><li class='spip'>" . join("</li><li class='spip'>", array_slice($tables,0,$n)) . "</li></ul></div>"
		. "<div style='width:49%;float:left;'><ul class='spip'><li>" . join("</li><li class='spip'>", array_slice($tables,$n)) . "</li></ul></div>"
		. "<div class='nettoyeur'></div>";
	return $corps;
}

?>