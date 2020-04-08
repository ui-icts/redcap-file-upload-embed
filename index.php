<?php
/** @var FileUploadEmbed $module */

use UIOWA\FileUploadEmbed\FileUploadEmbed;

// exit if hash doesn't match doc_id
if (!isset($_GET['doc_id_hash']) || (isset($_GET['doc_id_hash']) && $_GET['doc_id_hash'] != \Files::docIdHash($_GET['id']))) {
    exit("{$lang['global_01']}!");
}

// Returns array of "mime_type" (string), "doc_name" (string), and "contents" (string) or FALSE if failed
$docInfo = \Files::getEdocContentsAttributes($_GET['id']);

// set mime type and file contents
header('Content-type: ' . $docInfo[0]);
echo $docInfo[2];

?>