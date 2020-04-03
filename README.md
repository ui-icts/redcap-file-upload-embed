# File Upload Embed

### Description
This module allows File Upload fields to display the uploaded file as an inline embed on a data entry form.

### Setup
No system-level configuration is required, but you may want to customize the whitelisted file extensions. By default, the only file types that will display as embedded elements are PDF, JPG, JPEG, and PNG. This module has only been tested extensively with PDFs.

Once enabled on a project, you need to define which field(s) can be embedded via the project-level configuration. The selected fields must be the "File Upload" type.

### Usage
If a file with a whitelisted extension is uploaded to a valid File Upload field, it will appear below the File Upload field after saving and reloading the data entry form. The File Upload field can be hidden using an action tag, but the embed will still display (helpful for restricting the ability for a user to re-upload or delete files).