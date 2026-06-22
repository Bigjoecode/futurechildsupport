# Future Child Support: InfinityFree Upload Notes

This copy is already prepared for InfinityFree.

## Database settings already applied

- Host: `sql110.infinityfree.com`
- Port: `3306`
- Database: `if0_42238983_future`
- Username: `if0_42238983`

These values are stored in [secured/config.php](C:/xamppnew/htdocs/testing/deploy/futurechildsupport-infinityfree/secured/config.php:1).

## Before upload

1. Keep the folder structure exactly as it is.
2. Upload all files inside this prepared package to your InfinityFree site root.
3. Make sure the `secured/uploads/` folder remains writable after upload if donation proof files need to be saved.

## After upload

1. Open the homepage.
2. Test the contact form.
3. Test one donation submission.
4. Open `/admin/login.php` and confirm new submissions appear in the dashboard.

## Config files

- Main runtime config: [secured/config.php](C:/xamppnew/htdocs/testing/deploy/futurechildsupport-infinityfree/secured/config.php:1)
- Example template: [secured/config.example.php](C:/xamppnew/htdocs/testing/deploy/futurechildsupport-infinityfree/secured/config.example.php:1)

## Admin login

- URL: `/admin/login.php`
- Default username: `admin`
- Default password: `ChangeMe123!`

Change the admin password in `secured/config.php` after deployment.
