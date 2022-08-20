Email Validation
======

[![Latest Stable Version](http://poser.pugx.org/yswprog/mail-validation/v)](https://packagist.org/packages/yswprog/mail-validation)
[![Total Downloads](http://poser.pugx.org/yswprog/mail-validation/downloads)](https://packagist.org/packages/yswprog/mail-validation)
[![License](http://poser.pugx.org/yswprog/mail-validation/license)](https://packagist.org/packages/yswprog/mail-validation)

### Install with composer

To install with [Composer](https://getcomposer.org/), simply require the
latest version of this package.

```bash
composer require yswprog/mail-validation
```

## Method of use
- PHP native.
    ```php
    <?php
    
    include('vendor/autoload.php');
    
    use YProg\MailValidation\EmailVerify;
  
    // Load .env file if with custom env
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
  
    // Check email is valid or not
    $verify = EmailVerify::setEmail('yswprog@gmail.com');
    $verify->check();
  
    // Show message
    echo $verify->getMessage(); 
    ```
- Laravel
    ```php
    use App\Http\Controllers\Controller;
    use YProg\MailValidation\EmailVerify;
    
    class CustomController extends Controller
    {
        public function testing() 
        {
            // Check email is valid or not
            $verify = EmailVerify::setEmail('yswprog@gmail.com');
            $verify->check();
  
            // Show message
            return $verify->getMessage(); 
        }
    }
    ```