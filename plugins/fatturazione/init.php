<?php

try {
    $fattura = new Plugins\Fatturazione\FatturaElettronica($id_record);
} catch (UnexpectedValueException $e) {
}

$upload_dir = DOCROOT.'/'.Uploads::getDirectory($id_module, $id_plugin);
