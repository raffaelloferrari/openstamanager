<?php

// TODO: aggiornare con la funzione months()
$mesi = [
    tr('Gennaio'),
    tr('Febbraio'),
    tr('Marzo'),
    tr('Aprile'),
    tr('Maggio'),
    tr('Giugno'),
    tr('Luglio'),
    tr('Agosto'),
    tr('Settembre'),
    tr('Ottobre'),
    tr('Novembre'),
    tr('Dicembre'),
];

// Righe inserite
$qp = "SELECT *, DATE_FORMAT( data_richiesta, '%m-%Y') AS mese, (SELECT descrizione FROM in_tipiintervento WHERE id_tipo_intervento = in_interventi.id_tipo_intervento ) AS tipointervento, idanagrafica AS idcliente, (SELECT ragione_sociale  FROM an_anagrafiche WHERE idanagrafica = in_interventi.idanagrafica) AS ragione_sociale FROM in_interventi WHERE id NOT IN (SELECT idintervento FROM in_interventi_tecnici) AND id_stato IN (SELECT id FROM in_statiintervento WHERE completato = 0) ORDER BY DATE_FORMAT( data_richiesta, '%Y-%m') ASC, ragione_sociale ASC";
$rsp = $dbo->fetchArray($qp);
$n = $dbo->fetchNum($qp);

if (!empty($n)) {
    // Elenco interventi da pianificare
    foreach ($rsp as $i => $r) {
        // Se cambia il mese ricreo l'intestazione della tabella
        if (!isset($rsp[$i - 1]) || $r['mese'] != $rsp[$i - 1]['mese']) {
            if ($i == 0) {
                $attr = '';
                $class = 'fa-minus-circle';
            } else {
                $attr = 'style="display:none;"';
                $class = 'fa-plus-circle';
            }

            echo "
<h4>
    <a class='clickable' onclick=\"if( $('#t1_".$i."').css('display') == 'none' ){ $(this).children('i').removeClass('fa-plus-circle'); $(this).children('i').addClass('fa-minus-circle'); }else{ $(this).children('i').addClass('fa-plus-circle'); $(this).children('i').removeClass('fa-minus-circle'); } $('#t1_".$i."').slideToggle();\">
        <i class='fa ".$class."'></i> ".$mesi[intval(date('m', strtotime($r['data_richiesta']))) - 1].' '.date('Y', strtotime($r['data_richiesta'])).'
    </a>
</h4>';

            echo '
<div id="t1_'.$i.'" '.$attr.'>
    <table class="table table-hover table-striped">
        <thead>
            <tr>
				 <th width="70">'.tr('Codice').'</th>
                <th width="120">'.tr('Cliente').'</th>
                <th width="70"><small>'.tr('Data richiesta').'</small></th>
                <th width="200">'.tr('Tipo intervento').'</th>
                <th>'.tr('Descrizione').'</th>
                <th width="100">'.tr('Sede').'</th>
            </tr>
        </thead>

        <tbody>';
        }

        echo '
            <tr id="int_'.$r['id'].'">
				<td><a target="_blank" >'.module('Interventi')->link($r['id'], $r['codice']).'</a></td>
                <td>'.$r['ragione_sociale'].'</td>
                <td>'.dateFormat($r['data_richiesta']).'</td>
                <td>'.$r['tipointervento'].'</td>
                <td>'.nl2br($r['richiesta']).'</td>
				';

        echo '
                <td>';
        // Sede
        if ($r['idsede'] == '-1') {
            echo '- '.('Nessuna').' -';
        } elseif (empty($r['idsede'])) {
            echo tr('Sede legale');
        } else {
            $rsp2 = $dbo->fetchArray("SELECT id, CONCAT( CONCAT_WS( ' (', CONCAT_WS(', ', nomesede, citta), indirizzo ), ')') AS descrizione FROM an_sedi WHERE id=".prepare($r['idsede']));

            echo $rsp2[0]['descrizione'];
        }
        echo '
                </td>';

        echo '
            </tr>';

        if (!isset($rsp[$i + 1]) || $r['mese'] != $rsp[$i + 1]['mese']) {
            echo '
        </tbody>
    </table>
</div>';
        }
    }
} else {
    echo '
<p>'.tr('Non ci sono interventi da pianificare').'.</p>';
}
