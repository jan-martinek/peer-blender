## Setup

You need a server environment with PHP 5.4+ & MySQL 5.6+.

1. Clone repo
2. Get dependencies via composer and bower
3. Rename `app/config/config.blank.neon` to `app/config/config.neon` and fill in database info
4. Import database from `misc/db_dump.sql`
5. Set documentroot to `path/to/app/www/`
6. Make `path/to/app/www/uploads` writable
7. Open app in your browser, it should work

These instructions are very brief. If you get lost or something is broken, feel free to reach me at [honza.martinek@gmail.com](mailto:honza.martinek@gmail.com).
