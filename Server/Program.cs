using System.IO;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Server;

namespace ResponsiveFileManager.Server
{
    internal class Program
    {
        private static void Main(string[] args)
        {
            var root = Path.GetDirectoryName(Directory.GetCurrentDirectory()) + "/Website";

            var host = new WebHostBuilder()
                .UseKestrel()
                .UseWebRoot(root)
                .UseContentRoot(root)
                .UseUrls("http://*:5004/")
                .UseStartup<Startup>()
                .Build();

            host.Run();
        }
    }
}