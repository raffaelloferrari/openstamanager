<?php

// INTERVENTI ESEGUITI SU QUESTO IMPIANTO
echo '
<div class="card card-outline card-primary">
    <div class="card-header">
        <h3 class="card-title">'.tr('Interventi eseguiti su questo impianto').'</h3>
    </div>
    <div class="card-body">';

$results = $dbo->fetchArray('SELECT in_interventi.id, in_interventi.codice, descrizione, (SELECT MIN(orario_inizio) FROM in_interventi_tecnici WHERE idintervento=my_impianti_interventi.idintervento) AS data FROM my_impianti_interventi INNER JOIN in_interventi ON my_impianti_interventi.idintervento=in_interventi.id WHERE idimpianto='.prepare($id_record).' ORDER BY data DESC');
$totale_interventi = 0;

if (!empty($results)) {
    echo '
        <table class="table table-striped table-hover">
            <tr>
                <th width="350">'.tr('Intervento').'</th>
                <th>'.tr('Descrizione').'</th>
                <th width="150" class="text-right">'.tr('Costo totale').'</th>
            </tr>';

    foreach ($results as $result) {
        $intervento = \Modules\Interventi\Intervento::find($result['id']);
        $totale_interventi += $intervento->totale;

        echo '
            <tr>
                <td>
                    '.module('Interventi')->link($result['id'], tr('Intervento num. _NUM_ del _DATE_', [
                        '_NUM_' => $result['codice'],
                        '_DATE_' => dateFormat($result['data']),
                    ])).'
                </td>
                <td>'.nl2br($result['descrizione']).'</td>
                <td class="text-right">'.moneyFormat($costi_intervento['totale']).'</td>
            </tr>';
    }

    echo '  <tr>';
    echo '      <td colspan="2" class="text-right">';
    echo '          <b>'.tr('Totale').':</b>';
    echo '      </td>';
    echo '      <td class="text-right">';
    echo            '<b>'.moneyFormat($totale_interventi).'</b>';
    echo '      </td>';
    echo '  </tr>';

    echo '
        </table>';
} else {
    echo '
<div class=\'alert alert-info\' ><i class=\'fa fa-info-circle\'></i> '.tr('Nessun intervento su questo impianto').'.</div>';
}

echo '
    </div>
</div>';
