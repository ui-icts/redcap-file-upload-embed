<?php
namespace UIOWA\FileUploadEmbed;

class FileUploadEmbed extends \ExternalModules\AbstractExternalModule {

    function redcap_module_system_enable($version) {
        $key = 'allowed-file-extension';

        // if no file types already exist, set default
        if (!$this->getSystemSetting($key)) {
            $this->setSystemSetting($key, array(
                'pdf',
                'jpg',
                'jpeg',
                'png'
            ));
        }
    }

    function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance)
    {
        // only embed if record has been saved
        if (isset($record)) {
            $enabledFields = $this->getProjectSetting('allowed-upload-field');
            $supportedFiletypes = $this->getSystemSetting('allowed-file-extension');

            // get doc_id(s)
            $data = \REDCap::getData(array(
                'records' => $record,
                'fields' => $enabledFields,
                'events' => $event_id,
                'groups' => $group_id,
                'return_format' => 'json'
            ));

            $data = json_decode($data, true);

            // get specific instance if repeatable
            if (isset($repeat_instance)) {
                $data = $data[$repeat_instance - 1]; // todo better way to get instance id?
            }

            foreach($data as $field => $doc_id) {
                // remove any fields that are not File Upload type
                if (\REDCap::getFieldType($field) !== 'file') {
                    unset($data[$field]);
                }
                else {
                    // Returns array of "mime_type" (string), "doc_name" (string), and "contents" (string) or FALSE if failed
                    $docInfo = \Files::getEdocContentsAttributes($doc_id);
                    $uploadedFileType = \Files::get_file_extension_by_mime_type($docInfo[0]);

                    // exit if file type isn't whitelisted in module config or couldn't lookup mime type
                    if (!in_array($uploadedFileType, $supportedFiletypes) || !$uploadedFileType) {
                        $this->exitAfterHook();
                    }

                    // generate verification hash
                    $hash = \Files::docIdHash($doc_id);

                    // get embed url
                    $data[$field] = $this->getUrl("index.php?id=" . $doc_id . '&doc_id_hash=' . $hash);
                }
            }

            // exit if no valid fields found
            if (!count($data)) {
                $this->exitAfterHook();
            }
            ?>
            <script>
                $(document).ready(function() {
                    $.each(<?= json_encode($data) ?>, function(field, url) {
                        const splitUrl = url.split("&")
                        const getIdIndex = splitUrl.findIndex(function(item){
                            return item.indexOf("id=")!==-1;
                        });
                 
                        const getId = splitUrl[getIdIndex].split("=")[1]
        
                        if(getId !== "") {
                     
                            let $uploadTr = $("[sq_id='" + field + "']");

                            // if field appears on page, add embed
                            if ($uploadTr.length) {
                                let embedId = 'fileUploadEmbed_' + field;

                                $uploadTr.after("<tr id='" + embedId + "'></tr>");

                                $('#' + embedId).html(
                                    "<td colspan='2'>" +
                                    "<object id='embeddedFile_" + field + "' data='" + url + "#toolbar=0&navpanes=0&scrollbar=0" + "' style='width:100%;height:800px'></object>" +
                                    "</td>"
                                );
                            }
                        }
                        
                    });
                });
            </script>
            <?php
        }
    }
}
?>