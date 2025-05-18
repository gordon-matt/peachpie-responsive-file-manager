using System.ComponentModel.DataAnnotations;
using Pchp.Core;

namespace ResponsiveFileManager;

/// <summary>
/// Configuration options for Responsive File Manager.
/// </summary>
public class ResponsiveFileManagerOptions
{
    /// <summary>
    /// Path from base_url to base of upload folder. Use start and final /
    /// </summary>
    [Required]
    public string UploadDirectory { get; set; } = "/files/";

    /// <summary>
    /// Relative path from filemanager folder to upload folder. Use final /
    /// </summary>
    [Required]
    public string CurrentPath { get; set; } = "../files/";

    /// <summary>
    /// Relative path from filemanager folder to thumbs folder. Use final / and DO NOT put inside upload folder.
    /// </summary>
    [Required]
    public string ThumbsBasePath { get; set; } = "../thumbs/";

    /// <summary>
    /// Path from base_url to base of thumbs folder. Use final / and DO NOT put inside upload folder.
    /// </summary>
    public string ThumbsUploadDir { get; set; } = "/thumbs/";

    /// <summary>
    /// MIME file control to define files extensions.
    /// </summary>
    public bool MimeExtensionRename { get; set; } = true;

    /// <summary>
    /// FTP host.
    /// </summary>
    public string? FtpHost { get; set; }

    /// <summary>
    /// FTP user.
    /// </summary>
    public string FtpUser { get; set; } = "user";

    /// <summary>
    /// FTP password.
    /// </summary>
    public string FtpPass { get; set; } = "pass";

    /// <summary>
    /// FTP base folder.
    /// </summary>
    public string FtpBaseFolder { get; set; } = "base_folder";

    /// <summary>
    /// FTP base URL.
    /// </summary>
    public string FtpBaseUrl { get; set; } = "http://site to ftp root";

    /// <summary>
    /// FTP temp folder.
    /// </summary>
    public string FtpTempFolder { get; set; } = "../temp/";

    /// <summary>
    /// FTP thumbs directory.
    /// </summary>
    public string FtpThumbsDir { get; set; } = "/thumbs/";

    /// <summary>
    /// FTP SSL.
    /// </summary>
    public bool FtpSsl { get; set; } = false;

    /// <summary>
    /// FTP port.
    /// </summary>
    public int FtpPort { get; set; } = 21;

    /// <summary>
    /// Multiple files selection.
    /// </summary>
    public bool MultipleSelection { get; set; } = true;

    /// <summary>
    /// Multiple selection action button.
    /// </summary>
    public bool MultipleSelectionActionButton { get; set; } = true;

    /// <summary>
    /// Access keys.
    /// </summary>
    public PhpArray AccessKeys { get; set; } = [];

    /// <summary>
    /// Maximum size of all files in source folder in Megabytes.
    /// </summary>
    public int? MaxSizeTotal { get; set; }

    /// <summary>
    /// Maximum upload size in Megabytes.
    /// </summary>
    public int MaxSizeUpload { get; set; } = 10;

    /// <summary>
    /// File permission (octal).
    /// </summary>
    public int FilePermission { get; set; } = 0755;

    /// <summary>
    /// Folder permission (octal).
    /// </summary>
    public int FolderPermission { get; set; } = 0777;

    /// <summary>
    /// Default language file name.
    /// </summary>
    public string DefaultLanguage { get; set; } = "en_EN";

    /// <summary>
    /// Icon theme. Default available: ico and ico_dark.
    /// </summary>
    public string IconTheme { get; set; } = "ico";

    /// <summary>
    /// Show or not total size in filemanager.
    /// </summary>
    public bool ShowTotalSize { get; set; } = false;

    /// <summary>
    /// Show or not show folder size in list view feature in filemanager.
    /// </summary>
    public bool ShowFolderSize { get; set; } = false;

    /// <summary>
    /// Show or not show sorting feature in filemanager.
    /// </summary>
    public bool ShowSortingBar { get; set; } = true;

    /// <summary>
    /// Show or not show filters button in filemanager.
    /// </summary>
    public bool ShowFilterButtons { get; set; } = true;

    /// <summary>
    /// Show or not language selection feature in filemanager.
    /// </summary>
    public bool ShowLanguageSelection { get; set; } = true;

    /// <summary>
    /// Active or deactive the transliteration (mean convert all strange characters in A..Za..z0..9 characters).
    /// </summary>
    public bool Transliteration { get; set; } = false;

    /// <summary>
    /// Convert all spaces on files name and folders name with ReplaceWith variable.
    /// </summary>
    public bool ConvertSpaces { get; set; } = false;

    /// <summary>
    /// Convert all spaces on files name and folders name this value.
    /// </summary>
    public string ReplaceWith { get; set; } = "_";

    /// <summary>
    /// Convert to lowercase the files and folders name.
    /// </summary>
    public bool LowerCase { get; set; } = false;

    /// <summary>
    /// Add time value to returned images to prevent cache.
    /// </summary>
    public bool AddTimeToImg { get; set; } = false;

    /// <summary>
    /// Maximum pixel width for all images.
    /// </summary>
    public int ImageMaxWidth { get; set; } = 0;

    /// <summary>
    /// Maximum pixel height for all images.
    /// </summary>
    public int ImageMaxHeight { get; set; } = 0;

    /// <summary>
    /// Maximum image mode.
    /// </summary>
    public string ImageMaxMode { get; set; } = "auto";

    /// <summary>
    /// Automatic resizing.
    /// </summary>
    public bool ImageResizing { get; set; } = false;

    /// <summary>
    /// Image resizing width.
    /// </summary>
    public int ImageResizingWidth { get; set; } = 0;

    /// <summary>
    /// Image resizing height.
    /// </summary>
    public int ImageResizingHeight { get; set; } = 0;

    /// <summary>
    /// Image resizing mode.
    /// </summary>
    public string ImageResizingMode { get; set; } = "auto";

    /// <summary>
    /// Image resizing override.
    /// </summary>
    public bool ImageResizingOverride { get; set; } = false;

    /// <summary>
    /// Watermark path or false.
    /// </summary>
    public string? ImageWatermark { get; set; }

    /// <summary>
    /// Watermark position.
    /// </summary>
    public string ImageWatermarkPosition { get; set; } = "br";

    /// <summary>
    /// Watermark padding.
    /// </summary>
    public int ImageWatermarkPadding { get; set; } = 10;

    /// <summary>
    /// Default view.
    /// </summary>
    public int DefaultView { get; set; } = 0;

    /// <summary>
    /// Ellipsis title after first row.
    /// </summary>
    public bool EllipsisTitleAfterFirstRow { get; set; } = true;

    /// <summary>
    /// Allow delete files.
    /// </summary>
    public bool DeleteFiles { get; set; } = true;

    /// <summary>
    /// Allow create folders.
    /// </summary>
    public bool CreateFolders { get; set; } = true;

    /// <summary>
    /// Allow delete folders.
    /// </summary>
    public bool DeleteFolders { get; set; } = true;

    /// <summary>
    /// Allow upload files.
    /// </summary>
    public bool UploadFiles { get; set; } = true;

    /// <summary>
    /// Allow rename files.
    /// </summary>
    public bool RenameFiles { get; set; } = true;

    /// <summary>
    /// Allow rename folders.
    /// </summary>
    public bool RenameFolders { get; set; } = true;

    /// <summary>
    /// Allow duplicate files.
    /// </summary>
    public bool DuplicateFiles { get; set; } = true;

    /// <summary>
    /// Allow extract files.
    /// </summary>
    public bool ExtractFiles { get; set; } = true;

    /// <summary>
    /// Allow copy/cut files.
    /// </summary>
    public bool CopyCutFiles { get; set; } = true;

    /// <summary>
    /// Allow copy/cut directories.
    /// </summary>
    public bool CopyCutDirs { get; set; } = true;

    /// <summary>
    /// Allow change file permissions.
    /// </summary>
    public bool ChmodFiles { get; set; } = true;

    /// <summary>
    /// Allow change folder permissions.
    /// </summary>
    public bool ChmodDirs { get; set; } = true;

    /// <summary>
    /// Allow preview text files.
    /// </summary>
    public bool PreviewTextFiles { get; set; } = true;

    /// <summary>
    /// Allow edit text files.
    /// </summary>
    public bool EditTextFiles { get; set; } = true;

    /// <summary>
    /// Allow create text files.
    /// </summary>
    public bool CreateTextFiles { get; set; } = true;

    /// <summary>
    /// Allow download files or just preview.
    /// </summary>
    public bool DownloadFiles { get; set; } = true;

    /// <summary>
    /// Previewable text file extensions.
    /// </summary>
    public PhpArray PreviewableTextFileExts { get; set; } = new PhpArray(["bsh", "c", "css", "cc", "cpp", "cs", "csh", "cyc", "cv", "htm", "html", "java", "js", "m", "mxml", "perl", "pl", "pm", "py", "rb", "sh", "xhtml", "xml", "xsl", "txt", "log", ""]);

    /// <summary>
    /// Editable text file extensions.
    /// </summary>
    public PhpArray EditableTextFileExts { get; set; } = new PhpArray(["txt", "log", "xml", "html", "css", "htm", "js", ""]);

    /// <summary>
    /// JPlayer extensions.
    /// </summary>
    public PhpArray JPlayerExts { get; set; } = new PhpArray(["mp4", "flv", "webmv", "webma", "webm", "m4a", "m4v", "ogv", "oga", "mp3", "midi", "mid", "ogg", "wav"]);

    /// <summary>
    /// CAD extensions.
    /// </summary>
    public PhpArray CadExts { get; set; } = new PhpArray(["dwg", "dxf", "hpgl", "plt", "spl", "step", "stp", "iges", "igs", "sat", "cgm", "svg"]);

    /// <summary>
    /// Enable Google Docs preview.
    /// </summary>
    public bool GoogledocEnabled { get; set; } = true;

    /// <summary>
    /// Google Docs file extensions.
    /// </summary>
    public PhpArray GoogledocFileExts { get; set; } = new PhpArray(["doc", "docx", "xls", "xlsx", "ppt", "pptx", "pdf", "odt", "odp", "ods"]);

    /// <summary>
    /// Defines size limit for paste in MB / operation.
    /// </summary>
    public int? CopyCutMaxSize { get; set; } = 100;

    /// <summary>
    /// Defines file count limit for paste / operation.
    /// </summary>
    public int? CopyCutMaxCount { get; set; } = 200;

    /// <summary>
    /// Allowed image extensions.
    /// </summary>
    public PhpArray ExtImg { get; set; } = new PhpArray(["jpg", "jpeg", "png", "gif", "bmp", "svg", "ico", "webp"]);

    /// <summary>
    /// Allowed file extensions.
    /// </summary>
    public PhpArray ExtFile { get; set; } = new PhpArray(["doc", "docx", "rtf", "pdf", "xls", "xlsx", "txt", "csv", "html", "xhtml", "psd", "sql", "log", "fla", "xml", "ade", "adp", "mdb", "accdb", "ppt", "pptx", "odt", "ots", "ott", "odb", "odg", "otp", "otg", "odf", "ods", "odp", "css", "ai", "kmz", "dwg", "dxf", "hpgl", "plt", "spl", "step", "stp", "iges", "igs", "sat", "cgm", "tiff", ""]);

    /// <summary>
    /// Allowed video extensions.
    /// </summary>
    public PhpArray ExtVideo { get; set; } = new PhpArray(["mov", "mpeg", "m4v", "mp4", "avi", "mpg", "wma", "flv", "webm"]);

    /// <summary>
    /// Allowed audio extensions.
    /// </summary>
    public PhpArray ExtMusic { get; set; } = new PhpArray(["mp3", "mpga", "m4a", "ac3", "aiff", "mid", "ogg", "wav"]);

    /// <summary>
    /// Allowed archive extensions.
    /// </summary>
    public PhpArray ExtMisc { get; set; } = new PhpArray(["zip", "rar", "gz", "tar", "iso", "dmg"]);

    /// <summary>
    /// Extensions blacklist.
    /// </summary>
    public PhpArray? ExtBlacklist { get; set; }

    /// <summary>
    /// Empty filename permits like .htaccess, .env, ...
    /// </summary>
    public bool EmptyFilename { get; set; } = false;

    /// <summary>
    /// Accept files without extension.
    /// </summary>
    public bool FilesWithoutExtension { get; set; } = false;

    /// <summary>
    /// TUI active.
    /// </summary>
    public bool TuiActive { get; set; } = true;

    /// <summary>
    /// TUI position.
    /// </summary>
    public string TuiPosition { get; set; } = "bottom";

    /// <summary>
    /// Common background image.
    /// </summary>
    public string CommonBackgroundImage { get; set; } = "none";

    /// <summary>
    /// Common background color.
    /// </summary>
    public string CommonBackgroundColor { get; set; } = "#ececec";

    /// <summary>
    /// Common border.
    /// </summary>
    public string CommonBorder { get; set; } = "1px solid #E6E7E8";

    /// <summary>
    /// Header background image.
    /// </summary>
    public string HeaderBackgroundImage { get; set; } = "none";

    /// <summary>
    /// Header background color.
    /// </summary>
    public string HeaderBackgroundColor { get; set; } = "#ececec";

    /// <summary>
    /// Header border.
    /// </summary>
    public string HeaderBorder { get; set; } = "0px";

    /// <summary>
    /// Menu normal icon path.
    /// </summary>
    public string MenuNormalIconPath { get; set; } = "svg/icon-d.svg";

    /// <summary>
    /// Menu normal icon name.
    /// </summary>
    public string MenuNormalIconName { get; set; } = "icon-d";

    /// <summary>
    /// Menu active icon path.
    /// </summary>
    public string MenuActiveIconPath { get; set; } = "svg/icon-b.svg";

    /// <summary>
    /// Menu active icon name.
    /// </summary>
    public string MenuActiveIconName { get; set; } = "icon-b";

    /// <summary>
    /// Menu disabled icon path.
    /// </summary>
    public string MenuDisabledIconPath { get; set; } = "svg/icon-a.svg";

    /// <summary>
    /// Menu disabled icon name.
    /// </summary>
    public string MenuDisabledIconName { get; set; } = "icon-a";

    /// <summary>
    /// Menu hover icon path.
    /// </summary>
    public string MenuHoverIconPath { get; set; } = "svg/icon-c.svg";

    /// <summary>
    /// Menu hover icon name.
    /// </summary>
    public string MenuHoverIconName { get; set; } = "icon-c";

    /// <summary>
    /// Menu icon size width.
    /// </summary>
    public string MenuIconSizeWidth { get; set; } = "24px";

    /// <summary>
    /// Menu icon size height.
    /// </summary>
    public string MenuIconSizeHeight { get; set; } = "24px";

    /// <summary>
    /// Submenu background color.
    /// </summary>
    public string SubmenuBackgroundColor { get; set; } = "#ececec";

    /// <summary>
    /// Submenu partition color.
    /// </summary>
    public string SubmenuPartitionColor { get; set; } = "#000000";

    /// <summary>
    /// Submenu normal icon path.
    /// </summary>
    public string SubmenuNormalIconPath { get; set; } = "svg/icon-d.svg";

    /// <summary>
    /// Submenu normal icon name.
    /// </summary>
    public string SubmenuNormalIconName { get; set; } = "icon-d";

    /// <summary>
    /// Submenu active icon path.
    /// </summary>
    public string SubmenuActiveIconPath { get; set; } = "svg/icon-b.svg";

    /// <summary>
    /// Submenu active icon name.
    /// </summary>
    public string SubmenuActiveIconName { get; set; } = "icon-b";

    /// <summary>
    /// Submenu icon size width.
    /// </summary>
    public string SubmenuIconSizeWidth { get; set; } = "32px";

    /// <summary>
    /// Submenu icon size height.
    /// </summary>
    public string SubmenuIconSizeHeight { get; set; } = "32px";

    /// <summary>
    /// Submenu normal label color.
    /// </summary>
    public string SubmenuNormalLabelColor { get; set; } = "#000";

    /// <summary>
    /// Submenu normal label font weight.
    /// </summary>
    public string SubmenuNormalLabelFontWeight { get; set; } = "normal";

    /// <summary>
    /// Submenu active label color.
    /// </summary>
    public string SubmenuActiveLabelColor { get; set; } = "#000";

    /// <summary>
    /// Submenu active label font weight.
    /// </summary>
    public string SubmenuActiveLabelFontWeight { get; set; } = "normal";

    /// <summary>
    /// Checkbox border.
    /// </summary>
    public string CheckboxBorder { get; set; } = "1px solid #E6E7E8";

    /// <summary>
    /// Checkbox background color.
    /// </summary>
    public string CheckboxBackgroundColor { get; set; } = "#000";

    /// <summary>
    /// Range pointer color.
    /// </summary>
    public string RangePointerColor { get; set; } = "#333";

    /// <summary>
    /// Range bar color.
    /// </summary>
    public string RangeBarColor { get; set; } = "#ccc";

    /// <summary>
    /// Range subbar color.
    /// </summary>
    public string RangeSubbarColor { get; set; } = "#606060";

    /// <summary>
    /// Range disabled pointer color.
    /// </summary>
    public string RangeDisabledPointerColor { get; set; } = "#d3d3d3";

    /// <summary>
    /// Range disabled bar color.
    /// </summary>
    public string RangeDisabledBarColor { get; set; } = "rgba(85,85,85,0.06)";

    /// <summary>
    /// Range disabled subbar color.
    /// </summary>
    public string RangeDisabledSubbarColor { get; set; } = "rgba(51,51,51,0.2)";

    /// <summary>
    /// Range value color.
    /// </summary>
    public string RangeValueColor { get; set; } = "#000";

    /// <summary>
    /// Range value font weight.
    /// </summary>
    public string RangeValueFontWeight { get; set; } = "normal";

    /// <summary>
    /// Range value font size.
    /// </summary>
    public string RangeValueFontSize { get; set; } = "11px";

    /// <summary>
    /// Range value border.
    /// </summary>
    public string RangeValueBorder { get; set; } = "0";

    /// <summary>
    /// Range value background color.
    /// </summary>
    public string RangeValueBackgroundColor { get; set; } = "#f5f5f5";

    /// <summary>
    /// Range title color.
    /// </summary>
    public string RangeTitleColor { get; set; } = "#000";

    /// <summary>
    /// Range title font weight.
    /// </summary>
    public string RangeTitleFontWeight { get; set; } = "lighter";

    /// <summary>
    /// Colorpicker button border.
    /// </summary>
    public string ColorpickerButtonBorder { get; set; } = "0px";

    /// <summary>
    /// Colorpicker title color.
    /// </summary>
    public string ColorpickerTitleColor { get; set; } = "#000";

    /// <summary>
    /// File number limit JS.
    /// </summary>
    public int FileNumberLimitJs { get; set; } = 500;

    /// <summary>
    /// Hidden folders.
    /// </summary>
    public PhpArray HiddenFolders { get; set; } = [];

    /// <summary>
    /// Hidden files.
    /// </summary>
    public PhpArray HiddenFiles { get; set; } = new PhpArray(["config.php"]);

    /// <summary>
    /// URL upload.
    /// </summary>
    public bool UrlUpload { get; set; } = true;

    /// <summary>
    /// Fixed image creation.
    /// </summary>
    public bool FixedImageCreation { get; set; } = false;

    /// <summary>
    /// Fixed path from filemanager.
    /// </summary>
    public PhpArray FixedPathFromFilemanager { get; set; } = new PhpArray(["../test/", "../test1/"]);

    /// <summary>
    /// Fixed image creation name to prepend.
    /// </summary>
    public PhpArray FixedImageCreationNameToPrepend { get; set; } = new PhpArray(["", "test_"]);

    /// <summary>
    /// Fixed image creation to append.
    /// </summary>
    public PhpArray FixedImageCreationToAppend { get; set; } = new PhpArray(["_test", ""]);

    /// <summary>
    /// Fixed image creation width.
    /// </summary>
    public PhpArray FixedImageCreationWidth { get; set; } = new PhpArray([300, 400]);

    /// <summary>
    /// Fixed image creation height.
    /// </summary>
    public PhpArray FixedImageCreationHeight { get; set; } = new PhpArray([200, 300]);

    /// <summary>
    /// Fixed image creation option.
    /// </summary>
    public PhpArray FixedImageCreationOption { get; set; } = new PhpArray(["crop", "auto"]);

    /// <summary>
    /// Relative image creation.
    /// </summary>
    public bool RelativeImageCreation { get; set; } = false;

    /// <summary>
    /// Relative path from current position.
    /// </summary>
    public PhpArray RelativePathFromCurrentPos { get; set; } = new PhpArray(["./", "./"]);

    /// <summary>
    /// Relative image creation name to prepend.
    /// </summary>
    public PhpArray RelativeImageCreationNameToPrepend { get; set; } = new PhpArray(["", ""]);

    /// <summary>
    /// Relative image creation name to append.
    /// </summary>
    public PhpArray RelativeImageCreationNameToAppend { get; set; } = new PhpArray(["_thumb", "_thumb1"]);

    /// <summary>
    /// Relative image creation width.
    /// </summary>
    public PhpArray RelativeImageCreationWidth { get; set; } = new PhpArray([300, 400]);

    /// <summary>
    /// Relative image creation height.
    /// </summary>
    public PhpArray RelativeImageCreationHeight { get; set; } = new PhpArray([200, 300]);

    /// <summary>
    /// Relative image creation option.
    /// </summary>
    public PhpArray RelativeImageCreationOption { get; set; } = new PhpArray(["crop", "crop"]);

    /// <summary>
    /// Remember text filter.
    /// </summary>
    public bool RememberTextFilter { get; set; } = false;
}