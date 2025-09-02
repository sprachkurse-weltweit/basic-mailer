# Basic Mailer

An implementation of a general form mailer for *Sprachkurse Weltweit* utilizing PHPMailer

## Prerequisites
- PHP-8.x
- [PHPMailer](https://github.com/PHPMailer/PHPMailer)-6.x 
- Needs cURL extension!
- Needs an `env.php` file (see *Environmental Variables*)

## Environmental Variables
Users (different environmental settings) can be created by placing a folder with
the name of the user inside the `./ENV/` folder containing an `env.php` file.

e.g. `./ENV/Tom/env.php`

This user can then be referenced in `mailer.php`:

```php
$user = "Tom";
```

Check out `./ENV/Example/env.php` for an example.

## Project structure
```text
public_html/backend/
├─ PHPMailer6/
└─ Basic-Mailer/
```
