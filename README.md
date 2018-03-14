![Peachpie Responsive File Manager](https://github.com/gordon-matt/peachpie-responsive-file-manager/raw/master/Misc/logo.png)

# peachpie-responsive-file-manager
Responsive File Manager running on .NET Core with Peachpie

Getting Started

1. Run `dotnet restore`

2. Right-click the `Website` project and choose **Reload Project**

3. Change the `current_path` and `thumbs_base_path` paths in `Website/filemanager/config/config.php` for your local file system. This is supposed to work with relative file paths, but Peachpie doesn't seem to allow that yet.

4. Set the WebApplication project as the default, if it isn't already.

5. Run and test one of the 3 demo pages

**NOTE**: At this time, this project is not working properly. This is partly due to the fact that some PHP functions are not yet  implemented - for example: there are some missing functions in the `Peachpie.Library.Graphics` project. Further details can be found here: [Peachpie Issue 185](https://github.com/peachpiecompiler/peachpie/issues/185)
