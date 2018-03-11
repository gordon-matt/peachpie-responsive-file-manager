using System.IO;
using Microsoft.AspNetCore;
using Microsoft.AspNetCore.Hosting;

namespace WebApplication
{
    public class Program
    {
        public static void Main(string[] args)
        {
            // TODO: How can we set root for PHP as different to root for MVC?
            // Meaning: wwwroot as usual for MVC and ../Website for PHP....
            var root = Path.GetDirectoryName(Directory.GetCurrentDirectory()) + "/Website";
            BuildWebHost(root, args).Run();
        }

        public static IWebHost BuildWebHost(string root, string[] args) =>
            WebHost.CreateDefaultBuilder(args)
                .UseStartup<Startup>()
                //.UseWebRoot(root) // Doing this fixes PHP, but breaks MVC
                //.UseContentRoot(root) // Doing this fixes PHP, but breaks MVC
                .Build();
    }
}