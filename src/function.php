<?php

use EDTF\DateTime as EdtfDateTime;

/**
 * @param string $data
 * @return EdtfDateTime
 */
function edtf_datetime(string $data){
    return EdtfDateTime::from($data);
}