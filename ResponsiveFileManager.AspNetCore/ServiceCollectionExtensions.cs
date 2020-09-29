using System;
using Microsoft.Extensions.DependencyInjection;
using ResponsiveFileManager;

namespace Microsoft.AspNetCore.Builder
{
    /// <summary>
    /// Configuration extension methods.
    /// </summary>
    public static class ServiceCollectionExtensions
    {
        /// <summary>
        /// Adds optional Responsive File Manager services and configuration callback.
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
                //
            });

            return services;
        }
    }
}