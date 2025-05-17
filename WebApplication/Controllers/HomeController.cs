using Microsoft.AspNetCore.Mvc;

namespace WebApplication.Controllers;

public class HomeController : Controller
{
    [Route("")]
    public IActionResult Index() => View();

    [Route("ckeditor")]
    public IActionResult CKEditor() => View();

    [Route("tiny-mce-4")]
    public IActionResult TinyMCE4() => View();

    [Route("tiny-mce-5")]
    public IActionResult TinyMCE5() => View();

    [Route("tiny-mce-6")]
    public IActionResult TinyMCE6() => View();

    [Route("tiny-mce-7")]
    public IActionResult TinyMCE7() => View();

    [Route("iframe")]
    public IActionResult IFrame() => View();

    [Route("standalone")]
    public IActionResult Standalone() => View();
}