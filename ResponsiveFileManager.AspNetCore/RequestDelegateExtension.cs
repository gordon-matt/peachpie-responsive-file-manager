using System.IO;
using System.Reflection;
using Microsoft.AspNetCore.Http;
using Microsoft.Extensions.FileProviders;

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
            app.UseStaticFiles(new StaticFileOptions
            {
                RequestPath = new PathString("/filemanager"),
                FileProvider = new PhysicalFileProvider(Path.GetFullPath(Path.Combine(Assembly.GetEntryAssembly().Location, "../filemanager"))),
            });

            app.UsePhp();

            return app;
        }
    }
}