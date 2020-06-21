using System;
using System.Collections.Generic;
using System.Diagnostics;
using System.IO;
using System.Linq;
using System.Reflection;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Hosting.Server.Features;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.ResponseCaching;
using Microsoft.AspNetCore.Rewrite;
using Microsoft.Extensions.Caching.Memory;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.FileProviders;
using Microsoft.Extensions.Options;
using Pchp.Core;
using Peachpie.AspNetCore.Web;
using ResponsiveFileManager;

namespace Microsoft.AspNetCore.Builder
{
    /// <summary>
    /// <see cref="IApplicationBuilder"/> extension for enabling WordPress.
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

            //
            return app;
        }
    }
}
