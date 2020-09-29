using System.IO;
using System.Reflection;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.FileProviders;
using Microsoft.Extensions.Options;
using Pchp.Core;
using ResponsiveFileManager;

namespace Microsoft.AspNetCore.Builder
{
    /// <summary>
    /// <see cref="IApplicationBuilder"/> extension for enabling Responsive File Manager.
    /// </summary>
    public static class RequestDelegateExtension
    {
        /// <summary>
        /// Installs ResponsiveFileManager middleware.
        /// </summary>
        /// <param name="app">The application builder.</param>
        public static IApplicationBuilder UseResponsiveFileManager(this IApplicationBuilder app)
        {
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
                ctx.Globals["rfm_options"] = PhpValue.FromClass(options);
            });

            return app;
        }
    }
}