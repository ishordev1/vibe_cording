# QR Code Implementation for Certificates & Offer Letters

## Overview
All certificates and offer letters now include a scannable QR code that redirects users to a verification page displaying the certificate/offer letter details.

## Features Implemented

### 1. QR Code Generation
- **Library Used**: QR Server API (https://api.qrserver.com)
- **Size**: 150x150 pixels
- **Data**: Encoded URL pointing to verification page with certificate/offer letter ID

### 2. Verification Page
- **Location**: `/pages/verify-certificate.php`
- **URL Format**: `http://localhost/ai/DigitalTarai/pages/verify-certificate.php?cert={CERTIFICATE_ID}`
- **Features**:
  - Displays student name and email
  - Shows internship title and duration
  - Certificate/Offer Letter ID
  - Verification code (unique hash)
  - Generation date
  - Download button
  - Authentic verification badge

### 3. Certificate Changes
- **File**: `/admin/sections/certificates.php`
- **Changes**:
  - Creates database record first (to get certificate ID)
  - Generates unique QR code URL with certificate ID
  - Embeds QR code in the certificate footer
  - Updates database record with actual filename after generation
  - QR code positioned in the center-bottom of certificate

### 4. Offer Letter Changes
- **File**: `/admin/sections/offer-letters.php`
- **Changes**:
  - Creates database record first (to get offer letter ID)
  - Generates unique QR code URL with offer letter ID
  - Embeds QR code in the top-right corner of letter
  - Updates database record with actual filename after generation
  - Professional placement next to company header

## How It Works

### Generation Flow:
1. Admin clicks "Generate" button
2. System inserts placeholder record in database (gets ID)
3. QR code URL generated: `verify-certificate.php?cert={ID}`
4. QR code embedded in certificate/letter HTML
5. File saved to `/public/certificates/` or `/public/offer-letters/`
6. Database record updated with filename
7. User redirected to view certificate

### Verification Flow:
1. User scans QR code with phone camera
2. Browser opens verification page with certificate ID
3. Page queries database for certificate details
4. All information displayed with verification badge
5. User can download the certificate

## Database Changes
- Certificate ID stored in QR code URL
- Enables secure verification linking
- Each certificate/letter has unique ID and hash

## Files Modified

✅ `/admin/sections/certificates.php`
- Added QR code generation
- Changed record insertion flow
- Added file path update logic

✅ `/admin/sections/offer-letters.php`
- Added QR code generation
- Added QR code positioning in header
- Changed record insertion flow
- Added file path update logic

✅ `/pages/verify-certificate.php` (NEW)
- Certificate verification page
- Displays full certificate details
- Shows verification status
- Download link
- Professional styling

## Security Features

✅ **Unique Certificate IDs**: Each certificate gets a unique database ID
✅ **Hash-based Verification Code**: 8-character verification code from certificate hash
✅ **Database Verification**: Always queries database for authentic records
✅ **Tamper-proof**: QR code links to server-side verification

## Usage

### For Students:
1. Scan QR code on certificate with phone
2. View full certificate details online
3. Download certificate if needed
4. Share verification link with employers

### For Admins:
1. Generate certificates as normal
2. QR codes automatically embedded
3. All verification data stored in database
4. No additional steps needed

## Testing

### Test Steps:
1. Go to Admin → Certificates
2. Click "Generate" on pending certificate
3. Certificate downloads with embedded QR code
4. Open certificate in browser and scan QR code
5. Should redirect to verification page
6. Page displays student and internship info

### QR Code Test:
- Use phone camera or QR code scanner app
- Scan the code on generated certificate
- Should open verification page with certificate details

## Future Enhancements

✅ **Student Portal Display**: Show all student certificates
✅ **Email Certificates**: Send via email with QR code
✅ **Multiple Formats**: PDF with embedded QR code
✅ **Statistics**: Track certificate verification scans
✅ **Digital Badge**: Shareable digital credential
✅ **Blockchain**: Optional blockchain verification

## Technical Details

### QR Code URL:
```
https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={ENCODED_URL}
```

### Verification Page URL:
```
http://localhost/ai/DigitalTarai/pages/verify-certificate.php?cert={CERTIFICATE_ID}
```

### Database Query:
```sql
SELECT c.*, u.name, u.email, i.title, i.duration, i.skills, i.fees
FROM certificates c
JOIN users u ON c.user_id = u.id
JOIN internships i ON c.internship_id = i.id
WHERE c.id = {CERTIFICATE_ID}
```

## Troubleshooting

**QR Code not displaying**
- Check internet connection (needs to fetch from QR Server API)
- Ensure certificate is saved correctly
- Check browser console for errors

**Verification page not loading**
- Verify certificate ID in URL
- Check database has certificate record
- Ensure `/pages/verify-certificate.php` exists

**QR code linking to wrong page**
- Check SITE_URL constant is correct
- Verify certificate ID matches database
- Clear browser cache

