[![Donate](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=gordon_matt%40live%2ecom&lc=AU&currency_code=AUD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted)

![Peachpie Responsive File Manager](https://github.com/gordon-matt/peachpie-responsive-file-manager/raw/master/Misc/logo.png)

# peachpie-responsive-file-manager
Responsive File Manager running on .NET Core with Peachpie

## Getting Started

**If you are not already using Peachpie for anything, then including Responsive File Manager is very easy:**

1. Get the ResponsiveFileManager.AspNetCore NuGet package from: https://www.nuget.org/packages/ResponsiveFileManager.AspNetCore/

2. Add the following to your **appsettings.json**:

```json
"ResponsiveFileManagerOptions": {
    // Path from base_url to base of upload folder. Use start and final /
    "UploadDirectory": "/Media/Uploads/",

    // Relative path from filemanager folder to upload folder. Use final /
    "CurrentPath": "../Media/Uploads/",

    // Relative path from filemanager folder to thumbs folder. Use final / and DO NOT put inside upload folder.
    "ThumbsBasePath": "../Media/Thumbs/",
	
    "MaxSizeUpload":  10
}
```

3. Add ResponsiveFileManager as middleware within the `Configure` method:

```csharp
app.UseResponsiveFileManager();
```

4. Optionally configure ResponsiveFileManager settings in the `ConfigureServices` method.

```csharp
services.AddResponsiveFileManager(options =>
{
	
});
```

**If you are wanting to use Peachpie for more than just ResponsiveFileManager, then it is recommended you ignore the ResponsiveFileManager.AspNetCore package, only acquire the base ResponsiveFileManager package and then manually configure the settings as follows:**

1. Get the ResponsiveFileManager.AspNetCore NuGet package from: https://www.nuget.org/packages/ResponsiveFileManager.AspNetCore/

2. Add the following to your **appsettings.json**:

```json
"ResponsiveFileManagerOptions": {
    // Path from base_url to base of upload folder. Use start and final /
    "UploadDirectory": "/Media/Uploads/",

    // Relative path from filemanager folder to upload folder. Use final /
    "CurrentPath": "../Media/Uploads/",

    // Relative path from filemanager folder to thumbs folder. Use final / and DO NOT put inside upload folder.
    "ThumbsBasePath": "../Media/Thumbs/",
	
    "MaxSizeUpload":  10
}
```

There are more options available. Look at the demo app for an example.

3. Open your `Program.cs` and ensure it looks something like this:

```csharp
var builder = WebApplication.CreateBuilder(args);

builder.Services.AddDistributedMemoryCache();
builder.Services.AddSession(options =>
{
    options.IdleTimeout = TimeSpan.FromMinutes(30);
    options.Cookie.HttpOnly = true;
});

builder.Services.AddControllersWithViews();
builder.Services.AddRazorPages();
builder.Services.AddResponsiveFileManager(options => options.MaxSizeUpload = 32);

var app = builder.Build();

app.UseSession();
app.UseDefaultFiles();
app.UseStaticFiles();
app.UseResponsiveFileManager(); // Simple version. See advanced example below

app.UseRouting();

app.UseAuthentication();
app.UseAuthorization();

app.MapControllerRoute(
    name: "default",
    pattern: "{controller=Home}/{action=Index}/{id?}");
app.MapRazorPages();

app.Run();
```

Instead of calling `UseResponsiveFileManager` method, you can also handle the configuration yourself like so:
```csharp
const string filemanagerPath = "/filemanager";

app.UseStaticFiles(new StaticFileOptions
{
    RequestPath = filemanagerPath,
    FileProvider = new PhysicalFileProvider(Path.GetFullPath(Path.Combine(Assembly.GetEntryAssembly().Location, ".." + filemanagerPath))),
});

app.UsePhp(filemanagerPath, (Context ctx) =>
{
    // construct the options
    var options = new ResponsiveFileManagerOptions();
    ctx.GetService<IConfiguration>().GetSection("ResponsiveFileManagerOptions").Bind(options);
    ctx.GetService<IConfigureOptions<ResponsiveFileManagerOptions>>()?.Configure(options);

    // pass the options object to PHP globals
    ctx.Globals["rfm_options"] = PhpValue.FromClass(options); // this is how config in appsettings.json is passed to PHP
});
```

You can use the source code in this repo, as follows:

1. Open the solution in Visual Studio or newer.
2. Set the **WebApplication** project as the default, if it isn't already.
3. restore libs in WebApplication folder:
  - `dotnet tool install -g Microsoft.Web.LibraryManager.Cli`
  - `libman restore`
4. Run and test one of the demo pages
5. Look at the `Program.cs` file for configuration to copy to your own project to use with the NuGet package.

## Donate
If you find this project helpful, consider buying me a cup of coffee.  :-)

#### PayPal:

[![paypal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=gordon_matt%40live%2ecom&lc=AU&currency_code=AUD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted)

#### Crypto:
- **Bitcoin**: 1EeDfbcqoEaz6bbcWsymwPbYv4uyEaZ3Lp
- **Ethereum**: 0x277552efd6ea9ca9052a249e781abf1719ea9414
- **Litecoin**: LRUP8hukWGXRrcPK6Tm7iUp9vPvnNNt3uz
