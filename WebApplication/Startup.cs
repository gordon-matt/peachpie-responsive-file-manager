using System;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.Extensions.FileProviders;
using Microsoft.Extensions.Hosting;
using Pchp.Core;
using Peachpie.AspNetCore.Web;
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

            services.AddMvc(opt =>
            {
                opt.EnableEndpointRouting = false; // TODO: this is pre-aspnetcore 3.0 MVC, for app.UseMvc() below
            });
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

            app.UsePhp(new PhpRequestOptions(scriptAssemblyName: "ResponsiveFileManager")
            {
                //RootPath = Path.GetDirectoryName(Directory.GetCurrentDirectory()) + "\\Website",
                BeforeRequest = (Context ctx) =>
                {
                    // Since the config.php file is compiled, we cannot modify it once deployed... everything is hard coded there.
                    //  TODO: Place these values in appsettings.json and pass them in here to override the ones from config.php

                    ctx.Globals["appsettings"] = (PhpValue)new PhpArray()
                    {
                        { "upload_dir", rfmOptions.UploadDirectory },
                        { "current_path", rfmOptions.CurrentPath },
                        { "thumbs_base_path", rfmOptions.ThumbsBasePath }
                    };
                }
            });

            app.UseDefaultFiles();
            app.UseStaticFiles();
            app.UseStaticFiles(new StaticFileOptions()
            {
                FileProvider = HostEnvironment.WebRootFileProvider,
            });

            app.UseAuthentication();

            app.UseMvc(routes =>
            {
                routes.MapRoute(
                    name: "default",
                    template: "{controller=Home}/{action=Index}/{id?}");
            });
        }
    }
}
