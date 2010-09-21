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

function dump_afficher_tables_restaurees_erreurs($status_file) {
	$status = dump_lire_status($status_file);
	$tables = $status['tables_copiees'];

	$corps = "";
	$erreurs = array();

	if (!$tables)
		return "<p>"._L("Aucune donnee restauree")."</p>";

	// lister les tables copiees aller verifier dans la base
	// qu'on a le bon nombre de donnees
	foreach($tables as $t=>$n) {
		if (!sql_showtable($t) OR $n===0)
			$erreurs[$t] = _L("Table <strong>$t</strong> absente");
		else {
			$n = abs(intval($n));
			$n_dump = intval(sql_countsel($t));
			if ($n_dump<$n)
				$erreurs[$t] = _L("Table <strong>$t</strong>, donnees manquantes");
		}
	}

	if (count($erreurs))
		$corps = "<ul class='spip'><li>".implode("</li><li class='spip'>",$erreurs)."</li></ul>";
	return $corps;
}
?>