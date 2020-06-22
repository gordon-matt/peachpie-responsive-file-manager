using Microsoft.Extensions.DependencyInjection;
using System;
using System.Collections.Generic;
using System.Text;
using ResponsiveFileManager;
using Pchp.Core;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.Options;

namespace Microsoft.AspNetCore.Builder
{
    /// <summary>
    /// Configuration extension methods.
    /// </summary>
    public static class ServiceCollectionExtensions
    {
        /// <summary>
        /// Adds optional WordPress services and configuration callback.
        /// </summary>
        public static IServiceCollection AddResponsiveFileManager(this IServiceCollection services, Action<ResponsiveFileManagerOptions> configure = null)
        {
            if (services == null)
            {
                throw new ArgumentNullException(nameof(services));
            }

            services.Configure(configure);

            services.AddPhp(options =>
            {
                options.RequestStart += (Context ctx) =>
                {
                    // construct the options
                    var options = new ResponsiveFileManagerOptions();
                    ctx.GetService<IConfiguration>().GetSection("ResponsiveFileManagerOptions").Bind(options);
                    ctx.GetService<IConfigureOptions<ResponsiveFileManagerOptions>>()?.Configure(options);

                    // pass the options object to PHP globals
                    ctx.Globals["rfm_options"] = PhpValue.FromClass(options);
                };
            });

            //
            return services;
        }
    }
}
