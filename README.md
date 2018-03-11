![Peachpie Responsive File Manager](https://github.com/gordon-matt/peachpie-responsive-file-manager/raw/master/Misc/logo.png)

# peachpie-responsive-file-manager
Responsive File Manager running on .NET Core with Peachpie

Getting Started

1. Change the `current_path` and `thumbs_base_path` paths in `Website/filemanager/config/config.php` for your local file system. This is supposed to work with relative file paths, but Peachpie doesn't seem to allow that yet.

2. Browse to http://localhost:5004/filemanager/dialog.php

**NOTE**: At this time, this project is not working properly. The reason has yet to be determined, but it could be due to some parts of Peachpie not being implemented yet.
