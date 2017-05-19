<?php
defined('BASEPATH') OR exit('No direct script access allowed');


if(! isset($_GET['name']) || ! isset($_GET['file']))
{
    exit();
}

$file = FCPATH . $_GET['file'];
$name = $_GET['name'];

header("Content-Type: application/force-download");
header("Content-Disposition: attachment; filename={$name}");
readfile($file);
exit();


