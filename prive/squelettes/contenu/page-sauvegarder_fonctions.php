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

include_spip('inc/dump');

function dump_lire_status($status_file) {
	$status_file = _DIR_TMP.basename($status_file).".txt";
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
	$tables = $status['tables_copiees'];

	// lister les tables sauvegardees et aller verifier dans le dump
	// qu'on a le bon nombre de donnees
	dump_serveur($status['connect']);
	spip_connect('dump');

	foreach($tables as $t=>$n) {
		$n = abs(intval($n));
		$n_dump = intval(sql_countsel($t,'','','','dump'));
		$res = "$t ";
		if ($n_dump==0 AND $n==0)
			$res.="("._T('dump:aucune_donnee').")";
		else
			$res .= "($n_dump/$n)";
		if ($n!==$n_dump)
			$res= "<strong>$res</strong>";
		$tables[$t] = $res;
	}

	$n = floor(count($tables)/2);
	$corps .= "<div style='width:49%;float:left;'><ul class='spip'><li class='spip'>" . join("</li><li class='spip'>", array_slice($tables,0,$n)) . "</li></ul></div>"
		. "<div style='width:49%;float:left;'><ul class='spip'><li>" . join("</li><li class='spip'>", array_slice($tables,$n)) . "</li></ul></div>"
		. "<div class='nettoyeur'></div>";
	return $corps;
}

?>