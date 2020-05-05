using System;
using System.IO;
using System.Reflection;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.FileProviders;
using Microsoft.Extensions.Hosting;
using Pchp.Core;
using WebApplication.Data;
using WebApplication.Models;
using WebApplication.Options;
using WebApplication.Services;

namespace WebApplication
{
    public class Startup
    {
        public Startup(IConfiguration configuration, IWebHostEnvironment environment)
        {
            Configuration = configuration;
            HostEnvironment = environment;
        }

        public IConfiguration Configuration { get; }

        public IWebHostEnvironment HostEnvironment { get; }

        // This method gets called by the runtime. Use this method to add services to the container.
        public void ConfigureServices(IServiceCollection services)
        {
            services.AddDbContext<ApplicationDbContext>(options =>
                options.UseSqlServer(Configuration.GetConnectionString("DefaultConnection")));

            services.AddIdentity<ApplicationUser, IdentityRole>()
                .AddEntityFrameworkStores<ApplicationDbContext>()
                .AddDefaultTokenProviders();

            // Add application services.
            services.AddTransient<IEmailSender, EmailSender>();

            // Adds a default in-memory implementation of IDistributedCache.
            services.AddDistributedMemoryCache();

            services.AddSession(options =>
            {
                options.IdleTimeout = TimeSpan.FromMinutes(30);
                options.Cookie.HttpOnly = true;
            });

            services.AddControllersWithViews();
            services.AddRazorPages();
        }

        // This method gets called by the runtime. Use this method to configure the HTTP request pipeline.
        public void Configure(IApplicationBuilder app, IHostEnvironment env)
        {
            if (env.IsDevelopment())
            {
                //app.UseBrowserLink();
                app.UseDeveloperExceptionPage();
                app.UseDatabaseErrorPage();
            }
            else
            {
                app.UseExceptionHandler("/error");
            }

            app.UseSession();

            var rfmOptions = new ResponsiveFileManagerOptions();
            Configuration.GetSection("ResponsiveFileManagerOptions").Bind(rfmOptions);

            app.UseDefaultFiles();
            app.UseStaticFiles(); // shortcut for HostEnvironment.WebRootFileProvider
            app.UseStaticFiles(new StaticFileOptions
            {
                RequestPath = new PathString("/filemanager"),
                FileProvider = new PhysicalFileProvider(Path.GetFullPath(Path.Combine(Assembly.GetEntryAssembly().Location, "../filemanager"))),
            });

            app.UsePhp(new PhpRequestOptions(scriptAssemblyName: "ResponsiveFileManager")
            {
                BeforeRequest = (Context ctx) =>
                {
                    ctx.Globals["appsettings"] = new PhpArray
                    {
                        { "upload_dir", rfmOptions.UploadDirectory },
                        { "current_path", rfmOptions.CurrentPath },
                        { "thumbs_base_path", rfmOptions.ThumbsBasePath },
                        { "MaxSizeUpload", rfmOptions.MaxSizeUpload } //TODO: Test this new option
                    };
                }
            });

            app.UseRouting();

            app.UseAuthentication();
            app.UseAuthorization();

            app.UseEndpoints(endpoints =>
            {
                endpoints.MapControllerRoute(
                    name: "default",
                    pattern: "{controller=Home}/{action=Index}/{id?}");
                endpoints.MapRazorPages();
            });
        }
    }
}