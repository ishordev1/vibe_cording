# Vendor Libraries Setup

This project requires two PHP libraries for certificate generation:

## 1. FPDF (PDF Generation)

Download from: http://www.fpdf.org/

1. Download FPDF (latest version)
2. Extract and place `fpdf.php` in `vendor/fpdf/fpdf.php`

Directory structure:
```
vendor/
  fpdf/
    fpdf.php
```

## 2. PHP QR Code (QR Code Generation)

Download from: https://sourceforge.net/projects/phpqrcode/

1. Download phpqrcode library
2. Extract and place `qrlib.php` in `vendor/phpqrcode/qrlib.php`

Directory structure:
```
vendor/
  phpqrcode/
    qrlib.php
    (other library files)
```

## Quick Setup:

After downloading both libraries, your vendor folder should look like:

```
vendor/
├── fpdf/
│   └── fpdf.php
└── phpqrcode/
    └── qrlib.php
```

Once these are in place, the certificate generation in `admin/generate_certificate.php` will work.

## Alternative: Manual Setup

If you prefer not to use these libraries, you can modify `admin/generate_certificate.php` to use other PDF/QR libraries or a different approach.
