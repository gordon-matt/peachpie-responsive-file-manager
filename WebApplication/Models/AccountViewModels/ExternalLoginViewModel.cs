using System.ComponentModel.DataAnnotations;

namespace WebApplication.Models.AccountViewModels;

public class ExternalLoginViewModel
{
    [Required]
    [EmailAddress]
    public string Email { get; set; }
}