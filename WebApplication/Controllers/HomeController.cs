using System.Diagnostics;
using Microsoft.AspNetCore.Mvc;
using WebApplication.Models;

namespace WebApplication.Controllers
{
    public class HomeController : Controller
    {
        [Route("")]
        public ActionResult Index()
        {
            return View();
        }

        [Route("ckeditor")]
        public ActionResult CKEditor()
        {
            return View();
        }

        [Route("tiny-mce-4")]
        public ActionResult TinyMCE4()
        {
            return View();
        }

        [Route("tiny-mce-5")]
        public ActionResult TinyMCE5()
        {
            return View();
        }

        [Route("iframe")]
        public ActionResult IFrame()
        {
            return View();
        }

        [Route("standalone")]
        public ActionResult Standalone()
        {
            return View();
        }

        [Route("error")]
        public IActionResult Error()
        {
            return View(new ErrorViewModel { RequestId = Activity.Current?.Id ?? HttpContext.TraceIdentifier });
        }
    }
}